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

class RedeController extends Controller
{
    private $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Callback para depósitos (Cash-in) - Rede e.Rede
     */
    public function callbackDeposit(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('=== WEBHOOK REDE RECEBIDO ===');
            Log::info('Timestamp: ' . now()->format('Y-m-d H:i:s'));
            Log::info('IP: ' . $request->ip());
            Log::info('User-Agent: ' . $request->userAgent());
            Log::info('Headers: ' . json_encode($request->headers->all()));
            Log::info('Dados recebidos: ' . json_encode($data, JSON_PRETTY_PRINT));
            Log::info('=====================================');

            // Verifica se é um evento de pagamento PIX
            $status = null;
            $transactionId = null;
            $typeTransaction = 'PIX';
            
            // Formato Rede - verificar primeiro se tem data.status
            if (isset($data['data']['status']) && isset($data['data']['id'])) {
                $status = $data['data']['status'];
                $transactionId = $data['data']['id'];
                $typeTransaction = $data['data']['paymentMethod'] ?? 'PIX';
                Log::info("🔍 FORMATO REDE DETECTADO (data.status):");
                Log::info("   Status: $status");
                Log::info("   Transaction ID: $transactionId");
                Log::info("   Tipo: $typeTransaction");
                Log::info("   Valor: R$ " . number_format($data['data']['amount'] / 100, 2, ',', '.'));
            }
            // Formato Rede - nível raiz
            elseif (isset($data['status']) && (isset($data['transaction_id']) || isset($data['externalId']))) {
                $status = $data['status'];
                $transactionId = $data['transaction_id'] ?? $data['externalId'];
                $typeTransaction = $data['type'] ?? 'PIX';
                Log::info("🔍 FORMATO REDE DETECTADO (raiz):");
                Log::info("   Status: $status");
                Log::info("   Transaction ID: $transactionId");
                Log::info("   Tipo: $typeTransaction");
            }
            // Formato alternativo
            elseif (isset($data['event']) && isset($data['data'])) {
                $eventData = $data['data'];
                $status = $eventData['status'] ?? null;
                $transactionId = $eventData['id'] ?? null;
                $typeTransaction = $eventData['type'] ?? 'PIX';
                Log::info("🔍 FORMATO ALTERNATIVO DETECTADO:");
                Log::info("   Event: " . ($data['event'] ?? 'N/A'));
                Log::info("   Status: $status");
                Log::info("   Transaction ID: $transactionId");
                Log::info("   Tipo: $typeTransaction");
            }

            if (!$transactionId || !$status) {
                Log::error('❌ DADOS INSUFICIENTES NO WEBHOOK:');
                Log::error('   Transaction ID: ' . ($transactionId ?? 'NÃO ENCONTRADO'));
                Log::error('   Status: ' . ($status ?? 'NÃO ENCONTRADO'));
                Log::error('   Dados completos: ' . json_encode($data, JSON_PRETTY_PRINT));
                return response()->json(['status' => false, 'message' => 'Dados insuficientes']);
            }

            // Processar apenas pagamentos aprovados
            if ($status === 'PAID' || $status === 'paid' || $status === 'APPROVED' || $status === 'COMPLETED') {
                Log::info("✅ STATUS DE PAGAMENTO APROVADO DETECTADO: $status");
                Log::info("🔍 Buscando solicitação com Transaction ID: $transactionId");
                
                // Buscar pela transação usando o ID da Rede
                $cashin = Solicitacoes::where('rede_id', $transactionId)->first();
                
                if (!$cashin) {
                    Log::error("❌ SOLICITAÇÃO NÃO ENCONTRADA:");
                    Log::error("   Transaction ID: $transactionId");
                    Log::error("   Verificando se existe na tabela Solicitacoes...");
                    return response()->json(['status' => false, 'message' => 'Solicitação não encontrada']);
                }
                
                Log::info("✅ SOLICITAÇÃO ENCONTRADA:");
                Log::info("   ID: " . $cashin->id);
                Log::info("   User ID: " . $cashin->user_id);
                Log::info("   Valor: R$ " . number_format($cashin->amount, 2, ',', '.'));
                Log::info("   Valor Líquido: R$ " . number_format($cashin->deposito_liquido, 2, ',', '.'));
                Log::info("   Status Atual: " . $cashin->status);
                Log::info("   Callback URL: " . ($cashin->callback ?? 'NÃO CONFIGURADO'));

                if ($cashin->status !== "WAITING_FOR_APPROVAL") {
                    Log::warning("⚠️ SOLICITAÇÃO JÁ PROCESSADA:");
                    Log::warning("   Status atual: {$cashin->status}");
                    Log::warning("   Não será processada novamente");
                    return response()->json(['status' => true, 'message' => 'Solicitação já processada']);
                }

                Log::info("🔄 PROCESSANDO PAGAMENTO:");
                Log::info("   Atualizando status de WAITING_FOR_APPROVAL para PAID_OUT");
                
                $updated_at = Carbon::now();
                $cashin->update(['status' => 'PAID_OUT', 'updated_at' => $updated_at]);
                
                Log::info("✅ STATUS ATUALIZADO COM SUCESSO");

                $user = User::where('user_id', $cashin->user_id)->first();
                if ($user) {
                    Log::info("👤 USUÁRIO ENCONTRADO:");
                    Log::info("   Username: {$user->username}");
                    Log::info("   User ID: {$user->user_id}");
                    Log::info("   Valor a creditar: R$ " . number_format($cashin->deposito_liquido, 2, ',', '.'));
                    
                    Helper::incrementAmount($user, $cashin->deposito_liquido, 'saldo');
                    Helper::calculaSaldoLiquido($user->user_id);
                    
                    Log::info("💰 SALDO CREDITADO COM SUCESSO");

                    // Notificar gerente se existir
                    if (isset($user->gerente_id) && !is_null($user->gerente_id)) {
                        Log::info("👨‍💼 GERENTE DETECTADO:");
                        Log::info("   Gerente ID: {$user->gerente_id}");
                        Log::info("   Taxa a creditar: R$ " . number_format($cashin->taxa_cash_in, 2, ',', '.'));
                        
                        $gerente = User::where('user_id', $user->gerente_id)->first();
                        if ($gerente) {
                            Helper::incrementAmount($gerente, $cashin->taxa_cash_in, 'saldo');
                            Helper::calculaSaldoLiquido($gerente->user_id);
                            Log::info("✅ TAXA CREDITADA NO GERENTE: {$gerente->username}");
                            
                            // Enviar notificação de comissão ao gerente
                            $this->pushService->sendCommissionNotification(
                                $gerente->user_id,
                                $cashin->taxa_cash_in,
                                "Comissão de gerente"
                            );
                        }
                    } else {
                        Log::info("ℹ️ Nenhum gerente configurado para este usuário");
                    }

                    Log::info("🎉 DEPÓSITO PROCESSADO COM SUCESSO!");
                } else {
                    Log::error("❌ USUÁRIO NÃO ENCONTRADO:");
                    Log::error("   User ID: {$cashin->user_id}");
                }

                // ENVIAR WEBHOOK PARA O CASSINO (POSTBACK)
                if ($cashin->callback && $cashin->callback != 'web') {
                    $payload = [
                        "status" => "paid",
                        "idTransaction" => $cashin->idTransaction,
                        "typeTransaction" => "RECEIVEPIX",
                        "amount" => $cashin->amount,
                        "date" => $cashin->updated_at->format('Y-m-d H:i:s')
                    ];

                    Log::info('📤 ENVIANDO WEBHOOK PARA O CASSINO:');
                    Log::info('   URL: ' . $cashin->callback);
                    Log::info('   Payload: ' . json_encode($payload, JSON_PRETTY_PRINT));

                    try {
                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'accept' => 'application/json'
                        ])->timeout(30)->post($cashin->callback, $payload);

                        Log::info('✅ WEBHOOK ENVIADO COM SUCESSO:');
                        Log::info('   Status Code: ' . $response->status());
                        Log::info('   Resposta: ' . $response->body());
                    } catch (\Exception $e) {
                        Log::error('❌ ERRO AO ENVIAR WEBHOOK:');
                        Log::error('   URL: ' . $cashin->callback);
                        Log::error('   Erro: ' . $e->getMessage());
                    }
                } else {
                    Log::warning('⚠️ WEBHOOK NÃO ENVIADO:');
                    Log::warning('   Motivo: ' . ($cashin->callback ? 'Callback é "web"' : 'Callback não configurado'));
                    Log::warning('   Callback: ' . ($cashin->callback ?? 'NULL'));
                }

                Log::info('🏁 PROCESSAMENTO FINALIZADO COM SUCESSO!');
                return response()->json(['status' => true, 'message' => 'Depósito processado com sucesso']);
            }

            Log::warning("⚠️ STATUS NÃO PROCESSADO:");
            Log::warning("   Status recebido: $status");
            Log::warning("   Status esperados: PAID, paid, APPROVED, COMPLETED");
            Log::warning("   Este status não será processado");
            return response()->json(['status' => true, 'message' => 'Status não processado']);

        } catch (\Exception $e) {
            Log::error('💥 ERRO CRÍTICO NO PROCESSAMENTO:');
            Log::error('   Mensagem: ' . $e->getMessage());
            Log::error('   Arquivo: ' . $e->getFile());
            Log::error('   Linha: ' . $e->getLine());
            Log::error('   Stack Trace: ' . $e->getTraceAsString());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Callback para saques (Cash-out) - Rede e.Rede
     */
    public function callbackWithdraw(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('=== WEBHOOK REDE WITHDRAW RECEBIDO ===');
            Log::info('Timestamp: ' . now()->format('Y-m-d H:i:s'));
            Log::info('IP: ' . $request->ip());
            Log::info('User-Agent: ' . $request->userAgent());
            Log::info('Headers: ' . json_encode($request->headers->all()));
            Log::info('Dados recebidos: ' . json_encode($data, JSON_PRETTY_PRINT));
            Log::info('===========================================');

            // Verifica se é um evento de saque
            $status = null;
            $transactionId = null;
            
            // Formato Rede - verificar primeiro se tem data.status
            if (isset($data['data']['status']) && isset($data['data']['id'])) {
                $status = $data['data']['status'];
                $transactionId = $data['data']['id'];
                Log::info("🔍 FORMATO REDE WITHDRAW DETECTADO (data.status):");
                Log::info("   Status: $status");
                Log::info("   Transaction ID: $transactionId");
                Log::info("   Valor: R$ " . number_format($data['data']['amount'] / 100, 2, ',', '.'));
            }
            // Formato Rede - nível raiz
            elseif (isset($data['status']) && isset($data['transaction_id'])) {
                $status = $data['status'];
                $transactionId = $data['transaction_id'];
                Log::info("🔍 FORMATO REDE WITHDRAW DETECTADO (nível raiz):");
                Log::info("   Status: $status");
                Log::info("   Transaction ID: $transactionId");
            }
            // Formato alternativo
            elseif (isset($data['event']) && isset($data['data'])) {
                $eventData = $data['data'];
                $status = $eventData['status'] ?? null;
                $transactionId = $eventData['id'] ?? null;
                Log::info("🔍 FORMATO REDE WITHDRAW DETECTADO (event.data):");
                Log::info("   Status: $status");
                Log::info("   Transaction ID: $transactionId");
            }

            if (!$transactionId || !$status) {
                Log::warning('❌ Callback Rede Withdraw: Dados insuficientes no callback', $data);
                return response()->json(['status' => false, 'message' => 'Dados insuficientes']);
            }

            Log::info("🔍 Buscando saque com Rede ID: $transactionId");
            $cashout = SolicitacoesCashOut::where('rede_id', $transactionId)->first();
            
            if (!$cashout) {
                Log::error("❌ Callback Rede Withdraw: Saque não encontrado para Rede ID: $transactionId");
                return response()->json(['status' => false, 'message' => 'Saque não encontrado']);
            }

            Log::info("✅ SAQUE ENCONTRADO:");
            Log::info("   ID: " . $cashout->id);
            Log::info("   User ID: " . $cashout->user_id);
            Log::info("   Valor: R$ " . number_format($cashout->amount, 2, ',', '.'));
            Log::info("   Valor Líquido: R$ " . number_format($cashout->cash_out_liquido, 2, ',', '.'));
            Log::info("   Status Atual: " . $cashout->status);
            Log::info("   Rede ID: " . $cashout->rede_id);

            // Processar diferentes status
            Log::info("🔄 PROCESSANDO STATUS: $status");
            switch ($status) {
                case 'PAID':
                case 'APPROVED':
                case 'COMPLETED':
                    if ($cashout->status !== "COMPLETED") {
                        $cashout->update(['status' => 'COMPLETED', 'updated_at' => Carbon::now()]);
                        Log::info("✅ Callback Rede Withdraw: Saque completado - TransactionId: $transactionId");
                    } else {
                        Log::info("ℹ️ Callback Rede Withdraw: Saque já estava completado - TransactionId: $transactionId");
                    }
                    break;

                case 'FAILED':
                case 'REJECTED':
                case 'CANCELLED':
                    if ($cashout->status !== "FAILED") {
                        $cashout->update(['status' => 'FAILED', 'updated_at' => Carbon::now()]);
                        
                        // Devolver saldo ao usuário
                        $user = User::where('user_id', $cashout->user_id)->first();
                        if ($user) {
                            Helper::incrementAmount($user, $cashout->cash_out_liquido, 'saldo');
                            Helper::calculaSaldoLiquido($user->user_id);
                            Log::info("✅ Callback Rede Withdraw: Saldo devolvido - User: {$user->username}, Valor: {$cashout->cash_out_liquido}");
                        }
                    } else {
                        Log::info("ℹ️ Callback Rede Withdraw: Saque já estava falhado - TransactionId: $transactionId");
                    }
                    break;

                case 'PENDING':
                case 'PROCESSING':
                    if ($cashout->status !== "PROCESSING") {
                        $cashout->update(['status' => 'PROCESSING', 'updated_at' => Carbon::now()]);
                        Log::info("✅ Callback Rede Withdraw: Saque em processamento - TransactionId: $transactionId");
                    } else {
                        Log::info("ℹ️ Callback Rede Withdraw: Saque já estava em processamento - TransactionId: $transactionId");
                    }
                    break;

                default:
                    Log::warning("⚠️ Callback Rede Withdraw: Status não processado - Status: $status");
                    break;
            }

            return response()->json(['status' => true, 'message' => 'Callback processado com sucesso']);

        } catch (\Exception $e) {
            Log::error('Callback Rede Withdraw - Erro: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Callback unificado para Rede - detecta automaticamente se é depósito ou saque
     */
    public function callbackUnified(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('🔄 CALLBACK UNIFICADO REDE RECEBIDO');
            Log::info('Timestamp: ' . now()->format('Y-m-d H:i:s'));
            Log::info('Dados: ' . json_encode($data, JSON_PRETTY_PRINT));

            // Detectar tipo de transação baseado no conteúdo
            $transactionType = $this->detectTransactionType($data);
            
            Log::info("🎯 TIPO DE TRANSAÇÃO DETECTADO: $transactionType");

            switch ($transactionType) {
                case 'deposit':
                    return $this->callbackDeposit($request);
                    
                case 'withdraw':
                    return $this->callbackWithdraw($request);
                    
                default:
                    Log::warning("Callback Unificado Rede: Tipo não identificado", $data);
                    return response()->json(['status' => false, 'message' => 'Tipo de transação não identificado'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Callback Unificado Rede - Erro: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Detecta o tipo de transação baseado no conteúdo do callback
     */
    private function detectTransactionType($data)
    {
        Log::info("🔍 DETECTANDO TIPO DE TRANSAÇÃO:");
        Log::info("   Dados recebidos: " . json_encode($data, JSON_PRETTY_PRINT));

        // PRIMEIRO: Verificar se tem data.status (formato Rede)
        if (isset($data['data']['status'])) {
            $status = strtolower($data['data']['status']);
            $transactionId = $data['data']['id'] ?? null;
            
            Log::info("   Status encontrado em data.status: $status");
            Log::info("   Transaction ID: $transactionId");
            
            // Se tem transaction_id, verificar no banco de dados
            if ($transactionId) {
                // Verificar se é uma solicitação de depósito - buscar pelo ID da Rede
                $deposit = \App\Models\Solicitacoes::where('rede_id', $transactionId)->first();
                if ($deposit) {
                    Log::info("   ✅ TRANSAÇÃO DETECTADA COMO DEPÓSITO (encontrada na tabela Solicitacoes pelo rede_id)");
                    return 'deposit';
                }
                
                // Verificar se é uma solicitação de saque - buscar pelo ID da Rede
                $withdraw = \App\Models\SolicitacoesCashOut::where('rede_id', $transactionId)->first();
                if ($withdraw) {
                    Log::info("   ✅ TRANSAÇÃO DETECTADA COMO SAQUE (encontrada na tabela SolicitacoesCashOut pelo rede_id)");
                    return 'withdraw';
                }
            }
            
            // Se não encontrou no banco, usar lógica baseada no status
            if (in_array($status, ['waiting_payment', 'paid', 'completed', 'approved'])) {
                Log::info("   ✅ TRANSAÇÃO DETECTADA COMO DEPÓSITO (baseado no status: $status)");
                return 'deposit';
            }
        }

        // SEGUNDO: Verificar campos específicos que indicam tipo de transação
        if (isset($data['transaction_type'])) {
            $type = strtolower($data['transaction_type']);
            Log::info("   Tipo encontrado em transaction_type: $type");
            return $type === 'deposit' ? 'deposit' : 'withdraw';
        }

        if (isset($data['type'])) {
            $type = strtolower($data['type']);
            Log::info("   Tipo encontrado em type: $type");
            return $type === 'deposit' ? 'deposit' : 'withdraw';
        }

        if (isset($data['event'])) {
            $event = strtolower($data['event']);
            Log::info("   Evento encontrado: $event");
            if (strpos($event, 'deposit') !== false || strpos($event, 'received') !== false) {
                return 'deposit';
            }
            if (strpos($event, 'withdraw') !== false || strpos($event, 'sent') !== false) {
                return 'withdraw';
            }
        }

        // TERCEIRO: Verificar por campos de status que podem indicar tipo
        if (isset($data['status'])) {
            $status = strtolower($data['status']);
            Log::info("   Status encontrado em status: $status");
            // Status que geralmente indicam depósito
            if (in_array($status, ['received', 'credited', 'deposited', 'waiting_payment', 'paid', 'completed', 'approved'])) {
                return 'deposit';
            }
            // Status que geralmente indicam saque
            if (in_array($status, ['sent', 'withdrawn', 'debited'])) {
                return 'withdraw';
            }
        }

        // QUARTO: Verificar por campos de valor (positivo = depósito, negativo = saque)
        if (isset($data['amount'])) {
            $amount = (float) $data['amount'];
            Log::info("   Valor encontrado: $amount");
            if ($amount > 0) {
                return 'deposit';
            } elseif ($amount < 0) {
                return 'withdraw';
            }
        }

        // QUINTO: Verificar por campos de direção
        if (isset($data['direction'])) {
            $direction = strtolower($data['direction']);
            Log::info("   Direção encontrada: $direction");
            return $direction === 'in' ? 'deposit' : 'withdraw';
        }

        if (isset($data['flow'])) {
            $flow = strtolower($data['flow']);
            Log::info("   Fluxo encontrado: $flow");
            return $flow === 'in' ? 'deposit' : 'withdraw';
        }

        // SEXTO: Se não conseguir detectar, tentar verificar se existe transaction_id
        // e consultar no banco de dados para determinar o tipo
        if (isset($data['transaction_id']) || isset($data['id'])) {
            $transactionId = $data['transaction_id'] ?? $data['id'];
            Log::info("   Tentando detectar por transaction_id: $transactionId");
            
            // Verificar se é uma solicitação de depósito - buscar pelo ID da Rede
            $deposit = \App\Models\Solicitacoes::where('rede_id', $transactionId)->first();
            if ($deposit) {
                Log::info("   ✅ TRANSAÇÃO DETECTADA COMO DEPÓSITO (encontrada na tabela Solicitacoes pelo rede_id)");
                return 'deposit';
            }
            
            // Verificar se é uma solicitação de saque - buscar pelo ID da Rede
            $withdraw = \App\Models\SolicitacoesCashOut::where('rede_id', $transactionId)->first();
            if ($withdraw) {
                Log::info("   ✅ TRANSAÇÃO DETECTADA COMO SAQUE (encontrada na tabela SolicitacoesCashOut pelo rede_id)");
                return 'withdraw';
            }
        }

        // Se não conseguir detectar, retornar null para indicar erro
        Log::warning("   ❌ NÃO FOI POSSÍVEL DETECTAR O TIPO DE TRANSAÇÃO");
        return null;
    }

    /**
     * Webhook geral para todos os eventos da Rede (mantido para compatibilidade)
     */
    public function webhook(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('🌐 WEBHOOK GERAL REDE RECEBIDO');
            Log::info('Timestamp: ' . now()->format('Y-m-d H:i:s'));
            Log::info('Dados: ' . json_encode($data, JSON_PRETTY_PRINT));

            // Verificar se é um webhook de cartão de crédito
            if ($this->isCardPaymentWebhook($data)) {
                return $this->processCardPaymentWebhook($request);
            }

            // Determinar o tipo de evento
            $eventType = $data['event'] ?? $data['type'] ?? 'unknown';
            Log::info("🎪 TIPO DE EVENTO DETECTADO: $eventType");
            
            switch ($eventType) {
                case 'payment.received':
                case 'deposit.completed':
                    return $this->callbackDeposit($request);
                    
                case 'payment.sent':
                case 'withdraw.completed':
                case 'withdraw.failed':
                    return $this->callbackWithdraw($request);
                    
                default:
                    Log::info("Webhook Rede: Evento não processado - Tipo: $eventType");
                    return response()->json(['status' => true, 'message' => 'Evento não processado']);
            }

        } catch (\Exception $e) {
            Log::error('Webhook Rede - Erro: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Verifica se é um webhook de pagamento com cartão
     */
    private function isCardPaymentWebhook($data)
    {
        // Verificar se tem os campos específicos de cartão da Rede
        if (isset($data['tid']) && isset($data['returnCode'])) {
            return true;
        }
        
        // Verificar se tem os campos de transação de cartão
        if (isset($data['transaction_id']) && isset($data['status']) && isset($data['amount'])) {
            return true;
        }
        
        return false;
    }

    /**
     * Processa webhook de pagamento com cartão
     */
    private function processCardPaymentWebhook(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('💳 WEBHOOK CARTÃO REDE RECEBIDO');
            Log::info('Timestamp: ' . now()->format('Y-m-d H:i:s'));
            Log::info('Dados: ' . json_encode($data, JSON_PRETTY_PRINT));

            // Extrair dados da transação
            $transactionId = $data['tid'] ?? $data['transaction_id'] ?? null;
            $status = $data['returnCode'] ?? $data['status'] ?? null;
            $amount = $data['amount'] ?? null;

            if (!$transactionId || !$status) {
                Log::error('❌ DADOS INSUFICIENTES NO WEBHOOK CARTÃO:');
                Log::error('   Transaction ID: ' . ($transactionId ?? 'NÃO ENCONTRADO'));
                Log::error('   Status: ' . ($status ?? 'NÃO ENCONTRADO'));
                return response()->json(['status' => false, 'message' => 'Dados insuficientes']);
            }

            // Analisar o código de retorno
            $errorInfo = $this->getRedeErrorInfo($status);
            
            Log::info("🔍 PROCESSANDO WEBHOOK CARTÃO:");
            Log::info("   Transaction ID: $transactionId");
            Log::info("   Return Code: $status");
            Log::info("   Error Type: " . $errorInfo['type']);
            Log::info("   Description: " . $errorInfo['description']);
            Log::info("   Is Success: " . ($errorInfo['is_success'] ? 'Sim' : 'Não'));
            Log::info("   Amount: " . ($amount ? number_format($amount / 100, 2, ',', '.') : 'N/A'));

            // Buscar o pedido pelo ID da transação
            $order = \App\Models\CheckoutOrders::where('idTransaction', $transactionId)->first();

            if (!$order) {
                Log::error("❌ PEDIDO NÃO ENCONTRADO:");
                Log::error("   Transaction ID: $transactionId");
                return response()->json(['status' => false, 'message' => 'Pedido não encontrado']);
            }

            Log::info("✅ PEDIDO ENCONTRADO:");
            Log::info("   Order ID: " . $order->id);
            Log::info("   Status Atual: " . $order->status);
            Log::info("   Valor: R$ " . number_format($order->valor_total, 2, ',', '.'));

            // Mapear status da Rede para status interno
            $newStatus = $this->mapRedeStatusToInternal($status);

            if ($newStatus === 'pago' && $order->status !== 'pago') {
                Log::info('💰 PROCESSANDO PAGAMENTO APROVADO...');

                // Buscar checkout e usuário
                $checkout = \App\Models\CheckoutBuild::find($order->checkout_id);
                $user = $checkout ? \App\Models\User::find($checkout->user_id) : null;

                if (!$user) {
                    Log::error('❌ Usuário não encontrado');
                    return response()->json(['status' => false, 'message' => 'Usuário não encontrado']);
                }

                // Calcular taxa de depósito
                $setting = \App\Models\App::first();
                $taxaCalculada = \App\Helpers\TaxaFlexivelHelper::calcularTaxaDeposito($order->valor_total, $setting, $user);
                $deposito_liquido = $taxaCalculada['deposito_liquido'];
                $taxa_cash_in = $taxaCalculada['taxa_cash_in'];

                // Criar registro na tabela de solicitações
                $solicitacao = \App\Models\Solicitacoes::create([
                    'user_id' => $user->user_id,
                    'externalreference' => 'REDE_CARD_' . $transactionId,
                    'amount' => $order->valor_total,
                    'client_name' => $order->name,
                    'client_document' => preg_replace('/\D/', '', $order->cpf),
                    'client_email' => $order->email,
                    'date' => now(),
                    'status' => 'PAID_OUT',
                    'idTransaction' => $transactionId,
                    'deposito_liquido' => $deposito_liquido,
                    'qrcode_pix' => '',
                    'paymentcode' => '',
                    'paymentCodeBase64' => '',
                    'adquirente_ref' => 'Rede',
                    'taxa_cash_in' => $taxa_cash_in,
                    'taxa_pix_cash_in_adquirente' => 0,
                    'taxa_pix_cash_in_valor_fixo' => 0,
                    'client_telefone' => $order->telefone,
                    'executor_ordem' => 'Rede Card',
                    'descricao_transacao' => 'Pagamento com Cartão - ' . ($checkout->produto_name ?? 'Produto'),
                ]);

                // Creditar o saldo do usuário
                \App\Helpers\Helper::incrementAmount($user, $deposito_liquido, 'saldo');
                \App\Helpers\Helper::calculaSaldoLiquido($user->user_id);

                Log::info('✅ SALDO CREDITADO COM SUCESSO:');
                Log::info("   Valor Líquido: R$ " . number_format($deposito_liquido, 2, ',', '.'));
                Log::info("   Taxa: R$ " . number_format($taxa_cash_in, 2, ',', '.'));
            }

            // Atualizar status do pedido
            $order->update(['status' => $newStatus]);

            Log::info("✅ STATUS ATUALIZADO: $newStatus");
            
            // Enviar email de confirmação se o status for 'pago'
            if ($newStatus === 'pago') {
                \App\Http\Controllers\ObrigadoController::enviarEmailConfirmacao($order->id);
            }

            return response()->json(['status' => true, 'message' => 'Webhook processado com sucesso']);

        } catch (\Exception $e) {
            Log::error('Webhook Cartão Rede - Erro: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Mapeia status da Rede para status interno baseado na documentação oficial
     */
    private function mapRedeStatusToInternal($redeStatus)
    {
        // Usar o mesmo mapeamento do RedeService
        $redeService = new \App\Services\RedeService();
        $errorInfo = $this->getRedeErrorInfo($redeStatus);
        
        // Mapear tipos para status internos
        switch ($errorInfo['type']) {
            case 'success':
                return 'pago';
            case 'pending':
                return 'pendente';
            case 'card_error':
            case 'fraud':
            case 'validation_error':
            case 'config_error':
            case 'limit_error':
            case 'processing_error':
            case 'cancelled':
            case 'unknown_error':
                return 'cancelado';
            default:
                return 'pendente';
        }
    }

    /**
     * Mapeia códigos de retorno da Rede e.Rede (mesmo do RedeService)
     */
    private function getRedeErrorInfo($returnCode)
    {
        $errorMap = [
            // Códigos de Sucesso
            '00' => ['type' => 'success', 'is_success' => true, 'description' => 'Transação aprovada'],
            
            // Códigos de Erro do Cartão
            '01' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão não autorizado'],
            '02' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão bloqueado'],
            '03' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão vencido'],
            '04' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão inválido'],
            '05' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão não suportado'],
            '06' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão com restrições'],
            '07' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão com limite insuficiente'],
            '08' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão com problema de segurança'],
            '09' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão com problema de segurança'],
            '14' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão inválido'],
            '15' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão não autorizado'],
            '41' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão perdido'],
            '43' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão roubado'],
            '51' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Saldo insuficiente'],
            '54' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão vencido'],
            '55' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Senha inválida'],
            '57' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Transação não permitida'],
            '58' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Transação não autorizada - Entre em contato com o banco'],
            '61' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Limite de saque excedido'],
            '62' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão restrito'],
            '65' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Limite de saque excedido'],
            '75' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Senha bloqueada'],
            '78' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Cartão bloqueado'],
            '89' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Erro de comunicação'],
            '91' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Banco indisponível'],
            '96' => ['type' => 'card_error', 'is_success' => false, 'description' => 'Sistema indisponível'],
            
            // Códigos de Fraude
            '10' => ['type' => 'fraud', 'is_success' => false, 'description' => 'Transação suspeita - Fraude detectada'],
            '11' => ['type' => 'fraud', 'is_success' => false, 'description' => 'Transação bloqueada por política de fraude'],
            '12' => ['type' => 'fraud', 'is_success' => false, 'description' => 'Transação rejeitada por análise de risco'],
            '13' => ['type' => 'fraud', 'is_success' => false, 'description' => 'Transação com comportamento suspeito'],
            
            // Códigos de Erro de Processamento
            '20' => ['type' => 'processing_error', 'is_success' => false, 'description' => 'Erro interno do processador'],
            '21' => ['type' => 'processing_error', 'is_success' => false, 'description' => 'Timeout na comunicação'],
            '22' => ['type' => 'processing_error', 'is_success' => false, 'description' => 'Erro de conectividade'],
            '23' => ['type' => 'processing_error', 'is_success' => false, 'description' => 'Serviço temporariamente indisponível'],
            
            // Códigos de Validação
            '30' => ['type' => 'validation_error', 'is_success' => false, 'description' => 'Dados inválidos'],
            '31' => ['type' => 'validation_error', 'is_success' => false, 'description' => 'Valor inválido'],
            '32' => ['type' => 'validation_error', 'is_success' => false, 'description' => 'Moeda não suportada'],
            '33' => ['type' => 'validation_error', 'is_success' => false, 'description' => 'Parcelas inválidas'],
            '34' => ['type' => 'validation_error', 'is_success' => false, 'description' => 'CVV inválido'],
            '35' => ['type' => 'validation_error', 'is_success' => false, 'description' => 'Data de validade inválida'],
            '36' => ['type' => 'validation_error', 'is_success' => false, 'description' => 'Nome do portador inválido'],
            '37' => ['type' => 'validation_error', 'is_success' => false, 'description' => 'CPF inválido'],
            '38' => ['type' => 'validation_error', 'is_success' => false, 'description' => 'Email inválido'],
            '39' => ['type' => 'validation_error', 'is_success' => false, 'description' => 'Telefone inválido'],
            '40' => ['type' => 'validation_error', 'is_success' => false, 'description' => 'Referência inválida'],
            
            // Códigos de Configuração
            '50' => ['type' => 'config_error', 'is_success' => false, 'description' => 'Configuração inválida'],
            '51' => ['type' => 'config_error', 'is_success' => false, 'description' => 'PV não configurado'],
            '52' => ['type' => 'config_error', 'is_success' => false, 'description' => 'Token inválido'],
            '53' => ['type' => 'config_error', 'is_success' => false, 'description' => 'Ambiente não configurado'],
            
            // Códigos de Limite
            '60' => ['type' => 'limit_error', 'is_success' => false, 'description' => 'Limite de transações excedido'],
            '61' => ['type' => 'limit_error', 'is_success' => false, 'description' => 'Limite de valor excedido'],
            '62' => ['type' => 'limit_error', 'is_success' => false, 'description' => 'Limite diário excedido'],
            '63' => ['type' => 'limit_error', 'is_success' => false, 'description' => 'Limite mensal excedido'],
            
            // Códigos de Cancelamento
            '70' => ['type' => 'cancelled', 'is_success' => false, 'description' => 'Transação cancelada pelo portador'],
            '71' => ['type' => 'cancelled', 'is_success' => false, 'description' => 'Transação cancelada pelo estabelecimento'],
            '72' => ['type' => 'cancelled', 'is_success' => false, 'description' => 'Transação cancelada pelo banco'],
            
            // Códigos de Pendência
            '80' => ['type' => 'pending', 'is_success' => true, 'description' => 'Transação pendente de confirmação'],
            '81' => ['type' => 'pending', 'is_success' => true, 'description' => 'Transação em análise'],
            '82' => ['type' => 'pending', 'is_success' => true, 'description' => 'Transação aguardando captura'],
            
            // Códigos de Erro Desconhecido
            '99' => ['type' => 'unknown_error', 'is_success' => false, 'description' => 'Erro não identificado']
        ];

        return $errorMap[$returnCode] ?? [
            'type' => 'unknown_error',
            'is_success' => false,
            'description' => "Código de retorno não mapeado: $returnCode"
        ];
    }
}