<?php

namespace App\Traits;

use App\Models\App;
use App\Models\Solicitacoes;
use App\Models\SolicitacoesCashOut;
use App\Models\User;
use App\Services\RedeService;
use App\Traits\IPManagementTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait RedeTrait
{
    /**
     * Verifica se o IP está autorizado para operações de saque
     */
    public static function checkIPForWithdraw(User $user, $request = null): array
    {
        // Usar getIPForAcquirer se o request estiver disponível, senão usar getClientIP
        $clientIP = $request ? IPManagementTrait::getIPForAcquirer($request) : IPManagementTrait::getClientIP();
        
        if (!IPManagementTrait::isIPAllowed($clientIP, $user)) {
            return [
                'success' => false,
                'message' => 'IP não autorizado para realizar saques',
                'client_ip' => $clientIP
            ];
        }
        
        return [
            'success' => true,
            'client_ip' => $clientIP
        ];
    }
    /**
     * Gera QR Code PIX para depósito (Cash-in)
     */
    public static function generateQrCodeRede($request)
    {
        try {
            $data = $request->all();
            $user = User::where('id', $request->user()->id)->first();
            $setting = App::first();

            Log::info('=== REDETRAIT GENERATE QR CODE INICIADO ===');
            Log::info('RedeTrait::generateQrCodeRede - Dados da requisição:', [
                'user_id' => $user->id,
                'username' => $user->username,
                'amount' => $request->amount,
                'is_interface_web' => $request->input('baasPostbackUrl') === 'web'
            ]);

            // Determinar se é depósito via interface web ou API
            $isInterfaceWeb = $request->input('baasPostbackUrl') === 'web';

            // Calcula taxas de depósito (cash-in) usando sistema flexível
            $taxaCalculada = \App\Helpers\TaxaFlexivelHelper::calcularTaxaDeposito($request->amount, $setting, $user);
            $deposito_liquido = $taxaCalculada['deposito_liquido'];
            $taxa_cash_in = $taxaCalculada['taxa_cash_in'];
            $descricao = $taxaCalculada['descricao'];
            
            // Definir variáveis para compatibilidade com o código existente
            $taxa_total = $taxa_cash_in;
            $taxa_percentual_valor = $taxa_cash_in;
            $taxa_fixa_deposito = 0;

            Log::info('RedeTrait::generateQrCodeRede - Cálculo de taxas:', [
                'amount_original' => $request->amount,
                'taxa_cash_in' => $taxa_cash_in,
                'deposito_liquido' => $deposito_liquido,
                'descricao' => $descricao,
                'is_interface_web' => $isInterfaceWeb
            ]);

            $date = Carbon::now();
            $externalId = Str::uuid()->toString();

            // Criar solicitação de depósito
            $solicitacao = Solicitacoes::create([
                'user_id' => $user->user_id,
                'idTransaction' => $externalId,
                'externalreference' => $externalId,
                'amount' => $request->amount,
                'deposito_liquido' => $deposito_liquido,
                'taxa_cash_in' => $taxa_total,
                'taxa_pix_cash_in_adquirente' => $taxa_percentual_valor,
                'taxa_pix_cash_in_valor_fixo' => $taxa_fixa_deposito,
                'status' => 'WAITING_FOR_APPROVAL',
                'adquirente_ref' => 'rede',
                'client_name' => $request->debtor_name ?? 'Cliente',
                'client_document' => $request->debtor_document_number ?? '08355037120',
                'client_email' => $request->email ?? 'redacted@example.invalid',
                'client_telefone' => $request->phone ?? '08355037120',
                'date' => $date,
                'executor_ordem' => 'rede',
                'descricao_transacao' => $descricao,
                'qrcode_pix' => '',
                'paymentcode' => '',
                'paymentCodeBase64' => '',
                'callback' => $request->postback_url ?? $user->webhook_url ?? $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
                'created_at' => $date,
                'updated_at' => $date
            ]);

            Log::info('RedeTrait::generateQrCodeRede - Solicitação criada:', [
                'solicitacao_id' => $solicitacao->id,
                'external_id' => $externalId
            ]);

            // UTMfy integration
            if (!is_null($user->integracao_utmfy)) {
                $ip = $request->header('X-Forwarded-For') ?
                    $request->header('X-Forwarded-For') : ($request->header('CF-Connecting-IP') ?
                        $request->header('CF-Connecting-IP') :
                        $request->ip());

                $msg = "PIX Gerado " . env('APP_NAME');
                UtmfyTrait::gerarUTM('pix', 'waiting_payment', $solicitacao->toArray(), $user->integracao_utmfy, $ip, $msg);
            }

            // Gerar QR Code via Rede e.Rede
            $rede = new RedeService();
            $callbackUrl = url('/api/rede/callback/deposit');
            
            $qrCodeData = $rede->createPixQrCode([
                'amount' => $request->amount,
                'description' => $descricao,
                'external_id' => $externalId,
                'callback_url' => $callbackUrl,
                'expires_in' => 3600 // 1 hora
            ]);

            if (!$qrCodeData || !is_array($qrCodeData)) {
                Log::error('RedeTrait::generateQrCodeRede - Erro ao gerar QR Code');
                $solicitacao->delete(); // Remove solicitação se falhou
                return [
                    'status' => 500,
                    'data' => [
                        'status' => 'error',
                        'message' => 'Erro ao gerar QR Code PIX'
                    ]
                ];
            }

            // Atualizar solicitação com dados do QR Code
            $qrcode = $qrCodeData['pix']['qrcode'] ?? '';
            $redeId = $qrCodeData['id'] ?? null;
            
            Log::info('RedeTrait::generateQrCodeRede - Salvando dados da Rede:', [
                'external_id' => $externalId,
                'rede_id' => $redeId,
                'qrCodeData_keys' => array_keys($qrCodeData),
                'qrCodeData_sample' => [
                    'id' => $redeId,
                    'status' => $qrCodeData['status'] ?? 'N/A',
                    'amount' => $qrCodeData['amount'] ?? 'N/A'
                ]
            ]);
            
            $solicitacao->update([
                'qrcode_pix' => $qrcode,
                'paymentcode' => $qrCodeData['secureUrl'] ?? '',
                'paymentCodeBase64' => $qrcode ? 'data:image/png;base64,' . base64_encode($qrcode) : '',
                'rede_id' => $redeId // Salvar o ID da Rede
            ]);

            Log::info('RedeTrait::generateQrCodeRede - QR Code gerado e salvo com sucesso');

            // Usar os campos corretos da resposta da Rede
            $qrcode = $qrCodeData['pix']['qrcode'] ?? null;
            $qrCodeImageUrl = $qrcode ? 'https://quickchart.io/qr?text=' . urlencode($qrcode) : '';
            
            return [
                'status' => 200,
                'data' => [
                    'idTransaction' => $externalId,
                    'qrcode' => $qrcode,
                    'qr_code_image_url' => $qrCodeImageUrl,
                    // Estrutura compatível com o frontend
                    'charge' => [
                        'id' => $externalId,
                        'qrCode' => $qrCodeImageUrl,
                        'brCode' => $qrcode
                    ]
                ]
            ];

        } catch (\Exception $e) {
            Log::error('RedeTrait::generateQrCodeRede - Exceção: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => [
                    'status' => 'error',
                    'message' => 'Erro interno do servidor'
                ]
            ];
        }
    }

    /**
     * Processa pagamento PIX (Cash-out)
     */
    public static function requestPaymentRede($request)
    {
        try {
            $data = $request->all();
            $user = User::where('id', $request->user()->id)->first();
            $setting = App::first();

            // Verificar se o IP está autorizado para saques
            $ipCheck = self::checkIPForWithdraw($user, $request);
            if (!$ipCheck['success']) {
                return [
                    "status" => 403,
                    "data" => [
                        "status" => "error",
                        "message" => $ipCheck['message'],
                        "client_ip" => $ipCheck['client_ip']
                    ]
                ];
            }

            Log::info('=== REDETRAIT REQUEST PAYMENT INICIADO ===');
            Log::info('RedeTrait::requestPaymentRede - Dados da requisição:', [
                'user_id' => $user->id,
                'username' => $user->username,
                'amount' => $request->amount,
                'pix_key' => $request->pixKey,
                'pix_key_type' => $request->pixKeyType,
                'baasPostbackUrl' => $request->baasPostbackUrl,
                'is_interface_web' => $request->input('baasPostbackUrl') === 'web'
            ]);

            // Determinar se é saque via interface web ou API
            $isInterfaceWeb = $request->input('baasPostbackUrl') === 'web';

            // Verificar se deve usar taxa por fora para saques via API
            $taxaPorFora = $setting->taxa_por_fora_api ?? true;

            // Calcula taxas de saque usando o helper centralizado
            $taxaCalculada = \App\Helpers\TaxaSaqueHelper::calcularTaxaSaque((float)$request->amount, $setting, $user, $isInterfaceWeb, $taxaPorFora);
            $cashout_liquido = $taxaCalculada['saque_liquido'];
            $taxa_cash_out = $taxaCalculada['taxa_cash_out'];
            $descricao = $taxaCalculada['descricao'];
            $valor_total_descontar = $taxaCalculada['valor_total_descontar'] ?? $request->amount;

            Log::info('RedeTrait::requestPaymentRede - Cálculo de taxas:', [
                'amount_original' => $request->amount,
                'taxa_cash_out' => $taxa_cash_out,
                'cashout_liquido' => $cashout_liquido,
                'descricao' => $descricao,
                'user_saldo' => $user->saldo,
                'is_interface_web' => $isInterfaceWeb
            ]);

            // Verificar saldo considerando taxa por fora
            $saldo_necessario = $valor_total_descontar;
            if ($user->saldo < $saldo_necessario) {
                return [
                    'status' => 401,
                    'data' => [
                        'status' => 'error',
                        'message' => "Saldo insuficiente. Necessário: R$ " . number_format($saldo_necessario, 2, ',', '.') . ", Disponível: R$ " . number_format($user->saldo, 2, ',', '.'),
                    ]
                ];
            }

            $date = Carbon::now();

            // Se for web, verificar se é saque automático
            if ($request->baasPostbackUrl === 'web') {
                Log::info('RedeTrait::requestPaymentRede - Interface web detectada:', [
                    'saque_automatico' => $request->has('saque_automatico') ? $request->saque_automatico : false,
                    'has_saque_automatico' => $request->has('saque_automatico')
                ]);
                
                if ($request->has('saque_automatico') && $request->saque_automatico) {
                    Log::info('RedeTrait::requestPaymentRede - Processando saque automático');
                    return self::processarSaqueAutomaticoRede($request, $taxa_cash_out, $cashout_liquido, $date, $descricao, $user);
                } else {
                    Log::info('RedeTrait::requestPaymentRede - Processando saque manual');
                    return self::generateTransactionPaymentManualRede($request, $taxa_cash_out, $cashout_liquido, $date, $descricao, $user);
                }
            }

            Log::info('RedeTrait::requestPaymentRede - Processando via API (não web)');
            
            $rede = new RedeService();
            $externalId = Str::uuid()->toString();

            // Prepara dados do PIX
            $pixKey = $request->pixKey;
            switch ($request->pixKeyType) {
                case 'cpf':
                case 'cnpj':
                case 'telefone':
                case 'phone':
                    $pixKey = preg_replace('/[^0-9]/', '', $pixKey);
                    break;
            }

            // Mapear tipos de chave PIX para o formato da API Rede
            $pixKeyTypeMapping = [
                'cpf' => 'CPF',
                'cnpj' => 'CNPJ',
                'email' => 'EMAIL',
                'telefone' => 'PHONE',
                'phone' => 'PHONE',
                'evp' => 'EVP',
            ];
            $redePixKeyType = $pixKeyTypeMapping[strtolower($request->pixKeyType)] ?? 'EVP';

            $payload = [
                'amount' => $cashout_liquido,
                'description' => $descricao,
                'external_id' => $externalId,
                'callback_url' => $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
                'pix_key' => $pixKey,
                'pix_key_type' => $redePixKeyType,
                'beneficiary_name' => $request->beneficiary_name ?? 'Cliente Gateway',
                'beneficiary_document' => $request->beneficiary_document ?? $pixKey
            ];

            $response = $rede->makePayment($payload);

            if ($response && is_array($response)) {
                $solicitacao = SolicitacoesCashOut::create([
                    'user_id' => $user->user_id,
                    'idTransaction' => $response['transaction_id'] ?? $externalId,
                    'externalreference' => $externalId,
                    'amount' => $request->amount,
                    'beneficiaryname' => $request->beneficiary_name ?? $user->name ?? 'Cliente Gateway',
                    'beneficiarydocument' => $request->beneficiary_document ?? $request->pixKey,
                    'pix' => $request->pixKey,
                    'pixkey' => strtolower($request->pixKeyType),
                    'date' => $date,
                    'status' => 'WAITING_FOR_APPROVAL',
                    'type' => 'PIX',
                    'taxa_cash_out' => $taxa_cash_out,
                    'cash_out_liquido' => $cashout_liquido,
                    'end_to_end' => $response['transaction_id'] ?? $externalId,
                    'descricao_transacao' => $descricao,
                    'callback' => $request->baasPostbackUrl ?? $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
                    'rede_id' => $response['transaction_id'] ?? $response['id'] ?? null,
                ]);

                Log::info('RedeTrait::requestPaymentRede - Saque solicitado e salvo com sucesso.', ['idTransaction' => $externalId]);

                // Debitar saldo do usuário imediatamente
                $user = User::where('id', $request->user()->id)->first();
                if ($user) {
                    $taxaPorFora = \App\Models\App::first()->taxa_por_fora_api ?? true;
                    $valor_para_descontar = $taxaPorFora ? ($request->amount + $taxa_cash_out) : $request->amount;
                    
                    Log::info('RedeTrait::requestPaymentRede - Descontando saldo:', [
                        'user_id' => $user->user_id,
                        'saldo_antes' => $user->saldo,
                        'valor_para_descontar' => $valor_para_descontar,
                        'taxa_por_fora' => $taxaPorFora
                    ]);
                    
                    \App\Helpers\Helper::decrementAmount($user, $valor_para_descontar, 'saldo');
                    $user->increment('valor_sacado', $request->amount);
                    
                    // Log específico para saque
                    \App\Helpers\BalanceLogHelper::logSaqueOperation(
                        'SAQUE_REQUEST',
                        $user,
                        $request->amount,
                        [
                            'adquirente' => 'REDE',
                            'valor_bruto' => $request->amount,
                            'valor_descontado' => $valor_para_descontar,
                            'taxa_cash_out' => $taxa_cash_out,
                            'taxa_por_fora' => $taxaPorFora,
                            'external_id' => $externalId,
                            'operacao' => 'requestPaymentRede'
                        ]
                    );
                    
                    Log::info('RedeTrait::requestPaymentRede - Saldo debitado com sucesso:', [
                        'user_id' => $user->user_id,
                        'saldo_depois' => $user->fresh()->saldo,
                        'valor_debitado' => $valor_para_descontar
                    ]);
                }

                return [
                    'status' => 200,
                    'data' => [
                        'status' => 'WAITING_FOR_APPROVAL',
                        'idTransaction' => $externalId,
                        'message' => 'Saque solicitado com sucesso. Aguardando processamento.',
                        'amount' => $request->amount,
                        'cash_out_liquido' => $cashout_liquido,
                        'taxa_cash_out' => $taxa_cash_out,
                    ]
                ];
            } else {
                Log::error('RedeTrait::requestPaymentRede - Erro ao solicitar saque:', ['response' => $response]);
                return [
                    'status' => 400,
                    'data' => [
                        'status' => 'error',
                        'message' => 'Erro ao solicitar saque PIX',
                        'details' => $response
                    ]
                ];
            }

        } catch (\Exception $e) {
            Log::error('RedeTrait::requestPaymentRede - Erro inesperado: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => [
                    'status' => 'error',
                    'message' => 'Erro interno ao solicitar saque PIX',
                    'details' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Processa saque automático via Rede (chamado internamente pelo trait)
     */
    private static function processarSaqueAutomaticoRede($request, $taxa_cash_out, $cashout_liquido, $date, $descricao, $user)
    {
        Log::info('RedeTrait::processarSaqueAutomaticoRede - Iniciando processamento automático.');
        $rede = new RedeService();
        $externalId = Str::uuid()->toString();

        $pixKey = $request->pixKey;
        switch ($request->pixKeyType) {
            case 'cpf':
            case 'cnpj':
            case 'telefone':
            case 'phone':
                $pixKey = preg_replace('/[^0-9]/', '', $pixKey);
                break;
        }

        $pixKeyTypeMapping = [
            'cpf' => 'CPF',
            'cnpj' => 'CNPJ',
            'email' => 'EMAIL',
            'telefone' => 'PHONE',
            'phone' => 'PHONE',
            'evp' => 'EVP',
        ];
        $redePixKeyType = $pixKeyTypeMapping[strtolower($request->pixKeyType)] ?? 'EVP';

        $payload = [
            'amount' => $cashout_liquido,
            'description' => $descricao,
            'external_id' => $externalId,
            'callback_url' => $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
            'pix_key' => $pixKey,
            'pix_key_type' => $redePixKeyType,
            'beneficiary_name' => $request->beneficiary_name ?? 'Cliente Gateway',
            'beneficiary_document' => $request->beneficiary_document ?? $pixKey
        ];

        $response = $rede->makePayment($payload);

        if ($response && is_array($response)) {
            $solicitacao = SolicitacoesCashOut::create([
                'user_id' => $user->user_id,
                'idTransaction' => $response['transaction_id'] ?? $externalId,
                'externalreference' => $externalId,
                'amount' => $request->amount,
                'beneficiaryname' => $request->beneficiary_name ?? $user->name ?? 'Cliente Gateway',
                'beneficiarydocument' => $request->beneficiary_document ?? $request->pixKey,
                'pix' => $request->pixKey,
                'pixkey' => strtolower($request->pixKeyType),
                'date' => $date,
                'status' => 'COMPLETED',
                'type' => 'PIX',
                'taxa_cash_out' => $taxa_cash_out,
                'cash_out_liquido' => $cashout_liquido,
                'end_to_end' => $response['transaction_id'] ?? $externalId,
                'descricao_transacao' => $descricao,
                'callback' => $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
                'rede_id' => $response['transaction_id'] ?? $response['id'] ?? null,
            ]);

            Log::info('RedeTrait::processarSaqueAutomaticoRede - Saque automático solicitado e salvo com sucesso.', ['idTransaction' => $externalId]);

            // Debitar saldo do usuário imediatamente
            $user = User::where('id', $request->user()->id)->first();
            if ($user) {
                $taxaPorFora = \App\Models\App::first()->taxa_por_fora_api ?? true;
                $valor_para_descontar = $taxaPorFora ? ($request->amount + $taxa_cash_out) : $request->amount;
                
                Log::info('RedeTrait::processarSaqueAutomaticoRede - Descontando saldo:', [
                    'user_id' => $user->user_id,
                    'saldo_antes' => $user->saldo,
                    'valor_para_descontar' => $valor_para_descontar,
                    'taxa_por_fora' => $taxaPorFora
                ]);
                
                \App\Helpers\Helper::decrementAmount($user, $valor_para_descontar, 'saldo');
                $user->increment('valor_sacado', $request->amount);
                
                // Log específico para saque
                \App\Helpers\BalanceLogHelper::logSaqueOperation(
                    'SAQUE_REQUEST',
                    $user,
                    $request->amount,
                    [
                        'adquirente' => 'REDE',
                        'valor_bruto' => $request->amount,
                        'valor_descontado' => $valor_para_descontar,
                        'taxa_cash_out' => $taxa_cash_out,
                        'taxa_por_fora' => $taxaPorFora,
                        'external_id' => $externalId,
                        'operacao' => 'processarSaqueAutomaticoRede'
                    ]
                );
                
                Log::info('RedeTrait::processarSaqueAutomaticoRede - Saldo debitado com sucesso:', [
                    'user_id' => $user->user_id,
                    'saldo_depois' => $user->fresh()->saldo,
                    'valor_debitado' => $valor_para_descontar
                ]);
            }

            return [
                'status' => 200,
                'data' => [
                    'status' => 'WAITING_FOR_APPROVAL',
                    'idTransaction' => $externalId,
                    'message' => 'Saque automático solicitado com sucesso. Aguardando processamento.',
                    'amount' => $request->amount,
                    'cash_out_liquido' => $cashout_liquido,
                    'taxa_cash_out' => $taxa_cash_out,
                ]
            ];
        } else {
            Log::error('RedeTrait::processarSaqueAutomaticoRede - Erro ao solicitar saque automático:', ['response' => $response]);
            return [
                'status' => 400,
                'data' => [
                    'status' => 'error',
                    'message' => 'Erro ao solicitar saque automático',
                    'details' => $response
                ]
            ];
        }
    }

    /**
     * Gera uma transação de pagamento manual (para aprovação) via Rede (chamado internamente pelo trait)
     */
    private static function generateTransactionPaymentManualRede($request, $taxa_cash_out, $cashout_liquido, $date, $descricao, $user)
    {
        Log::info('RedeTrait::generateTransactionPaymentManualRede - Gerando solicitação de saque manual.');
        $externalId = Str::uuid()->toString();

        SolicitacoesCashOut::create([
            'user_id' => $user->user_id,
            'idTransaction' => $externalId,
            'externalreference' => $externalId,
            'amount' => $request->amount,
            'beneficiaryname' => $request->beneficiary_name ?? $user->name ?? 'Cliente Gateway',
            'beneficiarydocument' => $request->beneficiary_document ?? $request->pixKey,
            'pix' => $request->pixKey,
            'pixkey' => strtolower($request->pixKeyType),
            'date' => $date,
            'status' => 'WAITING_FOR_APPROVAL',
            'type' => 'PIX',
            'taxa_cash_out' => $taxa_cash_out,
            'cash_out_liquido' => $cashout_liquido,
            'end_to_end' => $externalId,
            'descricao_transacao' => $descricao,
            'callback' => $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
            'rede_id' => null,
        ]);

        Log::info('RedeTrait::generateTransactionPaymentManualRede - Solicitação de saque manual criada com sucesso.', ['idTransaction' => $externalId]);

        return [
            'status' => 200,
            'data' => [
                'status' => 'WAITING_FOR_APPROVAL',
                'idTransaction' => $externalId,
                'message' => 'Solicitação de saque criada com sucesso. Aguardando aprovação manual.',
                'amount' => $request->amount,
                'cash_out_liquido' => $cashout_liquido,
                'taxa_cash_out' => $taxa_cash_out,
            ]
        ];
    }

    /**
     * Libera saque manual (aprovação pelo admin) para Rede
     */
    public static function liberarSaqueManual($id)
    {
        try {
            \Log::info('RedeTrait::liberarSaqueManual - Iniciando liberação manual', ['id' => $id]);

            $cashout = \App\Models\SolicitacoesCashOut::where('id', $id)->first();
            if (!$cashout) {
                \Log::warning('RedeTrait::liberarSaqueManual - Solicitação não encontrada', ['id' => $id]);
                return back()->with('error', 'Solicitação de saque não encontrada.');
            }

            $update = [
                'status' => 'COMPLETED',
                'descricao_transacao' => 'LIBERADOADMIN'
            ];

            \App\Models\SolicitacoesCashOut::where('id', $id)->update($update);
            \Log::info('RedeTrait::liberarSaqueManual - Solicitação atualizada com sucesso', ['id' => $id, 'update' => $update]);
            return back()->with('success', "Saque atualizado para 'PAGO' com sucesso!");
        } catch (\Exception $e) {
            \Log::error('RedeTrait::liberarSaqueManual - Exceção', ['message' => $e->getMessage()]);
            return back()->with('error', 'Erro ao liberar saque manual.');
        }
    }

    /**
     * Garante que o idTransaction seja único, gerando um novo se necessário
     */
    private static function ensureUniqueTransactionId($requestedId, $userId)
    {
        if (empty($requestedId)) {
            return Str::uuid()->toString();
        }

        // Verificar se já existe um registro com esse ID
        $existing = SolicitacoesCashOut::where('idTransaction', $requestedId)->first();
        
        if (!$existing) {
            // ID não existe, pode usar
            return $requestedId;
        }

        // ID já existe - verificar status
        if (in_array($existing->status, ['CANCELLED', 'FAILED', 'REJECTED'])) {
            // Registro antigo cancelado/falho - gerar novo ID único
            $newId = $requestedId . '_' . time();
            Log::warning('RedeTrait::ensureUniqueTransactionId - ID duplicado encontrado (status: ' . $existing->status . '), gerando novo:', [
                'id_original' => $requestedId,
                'id_novo' => $newId,
                'user_id' => $userId,
                'registro_existente_id' => $existing->id,
                'status_existente' => $existing->status
            ]);
            return $newId;
        }

        // ID já existe e está ativo - gerar completamente novo
        $newId = Str::uuid()->toString();
        Log::warning('RedeTrait::ensureUniqueTransactionId - ID duplicado em uso ativo, gerando UUID novo:', [
            'id_original' => $requestedId,
            'id_novo' => $newId,
            'user_id' => $userId,
            'registro_existente_id' => $existing->id,
            'status_existente' => $existing->status
        ]);
        return $newId;
    }
}

