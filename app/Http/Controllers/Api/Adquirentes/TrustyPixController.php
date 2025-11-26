<?php

namespace App\Http\Controllers\Api\Adquirentes;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Solicitacoes;
use App\Models\SolicitacoesCashOut;
use App\Models\User;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrustyPixController extends Controller
{
    private $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Callback para depósitos (Cash-in) - TrustyPix
     */
    public function callbackDeposit(Request $request)
    {
        try {
            $data = $request->all();
            Log::debug('[+] Callback TrustyPix Deposit: ' . json_encode($data));

            // Verifica se é um evento de pagamento PIX
            $status = null;
            $transactionId = null;
            $typeTransaction = 'PIX';
            
            // Formato TrustyPix - Versão 1 (com type e data)
            if (isset($data['type']) && $data['type'] === 'PAYMENT') {
                $status = $data['data']['status'] ?? null;
                $transactionId = $data['data']['id'] ?? null;
                $typeTransaction = 'PIX';
                Log::info("Callback TrustyPix: Formato v1 detectado - Status: $status, TransactionId: $transactionId");
            }
            // Formato TrustyPix - Versão 2 (formato direto)
            else if (isset($data['id']) && isset($data['status'])) {
                $status = $data['status'];
                $transactionId = $data['id'];
                $typeTransaction = 'PIX';
                Log::info("Callback TrustyPix: Formato v2 detectado - Status: $status, TransactionId: $transactionId");
            }
            // Formato TrustyPix - Versão 3 (com type CHARGE e data)
            else if (isset($data['type']) && $data['type'] === 'CHARGE') {
                $status = $data['data']['status'] ?? null;
                $transactionId = $data['data']['external_id'] ?? null; // Usar external_id em vez de id
                $typeTransaction = 'PIX';
                Log::info("Callback TrustyPix: Formato v3 detectado - Status: $status, TransactionId (external_id): $transactionId");
            }
            
            if (!$status || !$transactionId) {
                Log::warning('Callback TrustyPix Deposit: Dados obrigatórios não encontrados');
                return response()->json(['status' => false, 'message' => 'Dados obrigatórios não encontrados']);
            }

            // Busca a solicitação pelo idTransaction (primeiro em depósitos, depois em saques)
            $cashin = Solicitacoes::where('idTransaction', $transactionId)
                ->where('adquirente_ref', 'trustypix')
                ->first();
            $cashout = null;
            $isWithdraw = false;

            if (!$cashin) {
                // Se não encontrou em depósitos, busca em saques
                $cashout = SolicitacoesCashOut::where('idTransaction', $transactionId)
                    ->where('executor_ordem', 'trustypix')
                    ->first();
                if ($cashout) {
                    $isWithdraw = true;
                    Log::info("Callback TrustyPix: Transação identificada como SAQUE - ID: $transactionId");
                }
            } else {
                Log::info("Callback TrustyPix: Transação identificada como DEPÓSITO - ID: $transactionId");
            }

            if (!$cashin && !$cashout) {
                Log::warning("Callback TrustyPix: Solicitação não encontrada para idTransaction: $transactionId");
                return response()->json(['status' => false, 'message' => 'Solicitação não encontrada']);
            }

            // Verifica se já foi processada
            if ($isWithdraw) {
                if ($cashout->status === 'PAID_OUT') {
                    Log::info("Callback TrustyPix Withdraw: Transação já processada: $transactionId");
                    return response()->json(['status' => true, 'message' => 'Transação já processada']);
                }
            } else {
                if ($cashin->status === 'PAID_OUT') {
                    Log::info("Callback TrustyPix Deposit: Transação já processada: $transactionId");
                    return response()->json(['status' => true, 'message' => 'Transação já processada']);
                }
            }

            // Processa apenas status de pagamento aprovado
            if ($status === 'PAID' || $status === 'APPROVED' || $status === 'CONFIRMED') {
                Log::info("Callback TrustyPix Deposit: Processando pagamento aprovado - Status: $status, TransactionId: $transactionId");

                if ($isWithdraw) {
                    // Processar saque
                    $cashout->update([
                        'status' => 'PAID_OUT',
                        'updated_at' => Carbon::now()
                    ]);

                    Log::info("Callback TrustyPix Withdraw: Saque processado com sucesso - ID: $transactionId");

                    // Envia callback personalizado se configurado
                    if ($cashout->callback && $cashout->callback !== 'web') {
                        $payload = [
                            "status" => "paid",
                            "idTransaction" => $cashout->idTransaction,
                            "typeTransaction" => $typeTransaction,
                            "amount" => $cashout->amount,
                            "beneficiary_name" => $cashout->beneficiaryname,
                            "beneficiary_document" => $cashout->beneficiarydocument,
                            "pix_key" => $cashout->pix,
                            "created_at" => $cashout->created_at,
                            "paid_at" => $cashout->updated_at
                        ];

                        Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'accept' => 'application/json'
                        ])->post($cashout->callback, $payload);

                        Log::info("Callback TrustyPix Withdraw: Callback personalizado enviado para: {$cashout->callback}");
                    }

                    return response()->json(['status' => true, 'message' => 'Saque processado com sucesso']);
                } else {
                    // Processar depósito
                    $cashin->update([
                        'status' => 'PAID_OUT',
                        'updated_at' => Carbon::now()
                    ]);

                    Log::info("Callback TrustyPix Deposit: Depósito processado com sucesso - ID: $transactionId");

                    // Buscar usuário
                    $user = User::where('user_id', $cashin->user_id)->first();
                    
                    if ($user) {
                        // Incrementa o saldo do usuário
                        Helper::incrementAmount($user, $cashin->deposito_liquido, 'saldo');
                        Helper::calculaSaldoLiquido($user->user_id);

                        // Log específico para depósito
                        \App\Helpers\BalanceLogHelper::logDepositOperation(
                            'DEPOSIT_CREDIT',
                            $user,
                            $cashin->deposito_liquido,
                            [
                                'adquirente' => 'TRUSTYPIX',
                                'cashin_id' => $cashin->id,
                                'transaction_id' => $transactionId,
                                'valor_bruto' => $cashin->amount,
                                'valor_liquido' => $cashin->deposito_liquido,
                                'operacao' => 'trustypix_callback_deposit'
                            ]
                        );

                        Log::info("Callback TrustyPix Deposit: Saldo incrementado para usuário {$user->user_id} - Valor: {$cashin->deposito_liquido}");

                        // Notificação será enviada automaticamente pelo Observer (SolicitacoesObserver)

                        // Processa splits se configurados
                        if (($cashin->split_email || $cashin->split_username) && $cashin->split_percentage) {
                            $splitResults = \App\Traits\SplitTrait::processSplits($cashin, $user);
                            Log::info("Callback TrustyPix Deposit: Splits processados", [
                                'solicitacao_id' => $cashin->id,
                                'split_results' => $splitResults
                            ]);
                        }

                        // Processa comissão do gerente se existir
                        if (isset($user->gerente_id) && !is_null($user->gerente_id)) {
                            $gerente = User::where('user_id', $user->gerente_id)->first();
                            if ($gerente) {
                                $comissaoGerente = ($cashin->deposito_liquido * $user->gerente_percentage) / 100;
                                Helper::incrementAmount($gerente, $comissaoGerente, 'saldo');
                                Helper::calculaSaldoLiquido($gerente->user_id);
                                
                                Log::info("Callback TrustyPix Deposit: Comissão do gerente processada", [
                                    'gerente_id' => $gerente->user_id,
                                    'comissao' => $comissaoGerente,
                                    'percentage' => $user->gerente_percentage
                                ]);
                            }
                        }

                        // Processa comissão do afiliado se existir
                        if (isset($user->affiliate_id) && !is_null($user->affiliate_id)) {
                            $afiliado = User::where('user_id', $user->affiliate_id)->first();
                            if ($afiliado) {
                                $comissaoAfiliado = ($cashin->deposito_liquido * $user->affiliate_percentage) / 100;
                                Helper::incrementAmount($afiliado, $comissaoAfiliado, 'saldo');
                                Helper::calculaSaldoLiquido($afiliado->user_id);
                                
                                Log::info("Callback TrustyPix Deposit: Comissão do afiliado processada", [
                                    'afiliado_id' => $afiliado->user_id,
                                    'comissao' => $comissaoAfiliado,
                                    'percentage' => $user->affiliate_percentage
                                ]);
                            }
                        }
                    }

                    // Envia callback personalizado se configurado
                    if ($cashin->callback && $cashin->callback !== 'web') {
                        $payload = [
                            "status" => "paid",
                            "idTransaction" => $cashin->idTransaction,
                            "typeTransaction" => $typeTransaction,
                            "amount" => $cashin->amount,
                            "debtor_name" => $cashin->client_name,
                            "email" => $cashin->client_email,
                            "debtor_document_number" => $cashin->client_document,
                            "phone" => $cashin->client_telefone,
                            "created_at" => $cashin->created_at,
                            "paid_at" => $cashin->updated_at,
                            "split_processed" => ($cashin->split_email || $cashin->split_username) && $cashin->split_percentage ? true : false,
                            "split_amount" => ($cashin->split_email || $cashin->split_username) && $cashin->split_percentage ? (float) (($cashin->amount * $cashin->split_percentage) / 100) : 0,
                            "split_recipient" => $cashin->split_email ?: $cashin->split_username
                        ];

                        Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'accept' => 'application/json'
                        ])->post($cashin->callback, $payload);

                        Log::info("Callback TrustyPix Deposit: Callback personalizado enviado para: {$cashin->callback}");
                    }

                    return response()->json(['status' => true, 'message' => 'Pagamento processado com sucesso']);
                }

            } else {
                Log::info("Callback TrustyPix Deposit: Status não processado: $status para transação: $transactionId");
                return response()->json(['status' => false, 'message' => 'Status não processado']);
            }

        } catch (\Exception $e) {
            Log::error('Callback TrustyPix Deposit - Erro: ' . $e->getMessage());
            Log::error('Callback TrustyPix Deposit - Stack trace: ' . $e->getTraceAsString());
            return response()->json(['status' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Callback para saques (Cash-out) - TrustyPix
     */
    public function callbackWithdraw(Request $request)
    {
        try {
            $data = $request->all();
            Log::debug('[+] Callback TrustyPix Withdraw: ' . json_encode($data));

            // Verifica se é um evento de transferência PIX
            if (!isset($data['type']) || $data['type'] !== 'PAYOUT') {
                Log::warning('Callback TrustyPix Withdraw: Tipo de evento não é PAYOUT');
                return response()->json(['status' => false, 'message' => 'Tipo de evento inválido']);
            }

            $status = $data['data']['status'] ?? null;
            $transactionId = $data['data']['external_id'] ?? $data['data']['id'] ?? null; // Usar external_id primeiro, depois id
            $typeTransaction = 'PIX';
            
            Log::info("Callback TrustyPix Withdraw: Status: $status, TransactionId: $transactionId (external_id: " . ($data['data']['external_id'] ?? 'não fornecido') . ", id: " . ($data['data']['id'] ?? 'não fornecido') . ")");

            if (!$status || !$transactionId) {
                Log::warning('Callback TrustyPix Withdraw: Dados obrigatórios não encontrados');
                return response()->json(['status' => false, 'message' => 'Dados obrigatórios não encontrados']);
            }

            // Busca a solicitação pelo idTransaction
            $cashout = SolicitacoesCashOut::where('idTransaction', $transactionId)
                ->where('descricao_transacao', '!=', 'WEB')
                ->first();

            if (!$cashout) {
                Log::warning("Callback TrustyPix Withdraw: Solicitação não encontrada para idTransaction: $transactionId");
                return response()->json(['status' => false, 'message' => 'Solicitação não encontrada']);
            }

            // Verifica se já foi processada
            if ($cashout->status === 'PAID_OUT') {
                Log::info("Callback TrustyPix Withdraw: Transação já processada: $transactionId");
                return response()->json(['status' => true, 'message' => 'Transação já processada']);
            }

            // Processa apenas status de pagamento aprovado
            if ($status === 'PAID' || $status === 'APPROVED' || $status === 'CONFIRMED') {
                Log::info("Callback TrustyPix Withdraw: Processando saque aprovado - Status: $status, TransactionId: $transactionId");

                $cashout->update([
                    'status' => 'PAID_OUT',
                    'updated_at' => Carbon::now()
                ]);

                Log::info("Callback TrustyPix Withdraw: Saque processado com sucesso - ID: $transactionId");

                // Envia callback personalizado se configurado
                if ($cashout->callback && $cashout->callback !== 'web') {
                    $payload = [
                        "status" => "paid",
                        "idTransaction" => $cashout->idTransaction,
                        "typeTransaction" => $typeTransaction,
                        "amount" => $cashout->amount,
                        "beneficiary_name" => $cashout->beneficiaryname,
                        "beneficiary_document" => $cashout->beneficiarydocument,
                        "pix_key" => $cashout->pix,
                        "created_at" => $cashout->created_at,
                        "paid_at" => $cashout->updated_at
                    ];

                    Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'accept' => 'application/json'
                    ])->post($cashout->callback, $payload);

                    Log::info("Callback TrustyPix Withdraw: Callback personalizado enviado para: {$cashout->callback}");
                }

                return response()->json(['status' => true, 'message' => 'Saque processado com sucesso']);

            } else if ($status === 'FAILED' || $status === 'REJECTED' || $status === 'CANCELLED') {
                Log::info("Callback TrustyPix Withdraw: Processando saque rejeitado - Status: $status, TransactionId: $transactionId");

                // Buscar usuário para reverter saldo
                $user = User::where('user_id', $cashout->user_id)->first();
                
                if ($user) {
                    // Reverter desconto do saldo
                    Helper::incrementAmount($user, $cashout->amount, 'saldo');
                    Helper::decrementAmount($user, $cashout->amount, 'valor_sacado');
                    Helper::calculaSaldoLiquido($user->user_id);

                    Log::info("Callback TrustyPix Withdraw: Saldo revertido para usuário {$user->user_id} - Valor: {$cashout->amount}");
                }

                $cashout->update([
                    'status' => 'REJECTED',
                    'updated_at' => Carbon::now()
                ]);

                Log::info("Callback TrustyPix Withdraw: Saque rejeitado processado - ID: $transactionId");

                return response()->json(['status' => true, 'message' => 'Saque rejeitado processado']);

            } else {
                Log::info("Callback TrustyPix Withdraw: Status não processado: $status para transação: $transactionId");
                return response()->json(['status' => false, 'message' => 'Status não processado']);
            }

        } catch (\Exception $e) {
            Log::error('Callback TrustyPix Withdraw - Erro: ' . $e->getMessage());
            Log::error('Callback TrustyPix Withdraw - Stack trace: ' . $e->getTraceAsString());
            return response()->json(['status' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Teste de callback para TrustyPix
     */
    public function testCallback(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('=== TRUSTYPIX TEST CALLBACK ===');
            Log::info('TrustyPixController::testCallback - Dados recebidos:', $data);
            
            return response()->json([
                'status' => true,
                'message' => 'Callback TrustyPix recebido com sucesso',
                'data' => $data,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('TrustyPixController::testCallback - Erro: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erro interno do servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}