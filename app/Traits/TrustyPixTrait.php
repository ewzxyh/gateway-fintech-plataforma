<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Solicitacoes;
use App\Models\SolicitacoesCashOut;
use App\Models\App;
use App\Services\TrustyPixService;
use App\Traits\IPManagementTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App as AppFacade;

trait TrustyPixTrait
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
     * Solicita depósito PIX via TrustyPix
     */
    public static function requestDepositTrustyPix($request)
    {
        \Log::info('🔍 TrustyPixTrait::requestDepositTrustyPix - INÍCIO', [
            'checkout_id' => $request->checkout_id ?? null,
            'metodo' => $request->metodo ?? null,
            'amount' => $request->amount,
            'all_data' => $request->all(),
            'has_checkout_id' => $request->has('checkout_id')
        ]);
        
        try {
            $trustypixConfig = \App\Models\TrustyPix::first();
            if (!$trustypixConfig || !$trustypixConfig->status) {
                return [
                    "status" => 500,
                    "data" => [
                        "status" => "error",
                        "message" => "TrustyPix não configurado ou inativo."
                    ]
                ];
            }

            // Usar o usuário já autenticado pelo middleware ou dados do checkout
            $user = $request->user();
            
            // Se não há usuário autenticado, buscar dados do checkout
            if (!$user && $request->has('checkout_id')) {
                $checkout = \App\Models\CheckoutBuild::where('id', $request->checkout_id)->first();
                if ($checkout) {
                    $user = \App\Models\User::where('id', $checkout->user_id)->first();
                    \Log::info('🔍 TrustyPixTrait: Usuário obtido via checkout', [
                        'checkout_id' => $request->checkout_id,
                        'user_id' => $user ? $user->id : 'não encontrado'
                    ]);
                }
            }
            
            if (!$user) {
                return [
                    "status" => 404,
                    "data" => [
                        "status" => "error",
                        "message" => "Usuário não encontrado."
                    ]
                ];
            }

            // Usar valor_total do checkout se amount não estiver disponível
            $valor = (float) ($request->amount ?? $request->valor_total);
            $setting = \App\Models\App::first();

            Log::info('=== TRUSTYPIXTRAIT REQUEST DEPOSIT INICIADO ===');
            Log::info('TrustyPixTrait::requestDepositTrustyPix - Dados da requisição:', [
                'user_id' => $user->id,
                'username' => $user->username,
                'amount' => $valor,
                'debtor_name' => $request->debtor_name ?? $request->name,
                'debtor_document_number' => $request->debtor_document_number ?? $request->cpf,
                'email' => $request->email,
                'phone' => $request->phone ?? $request->telefone,
                'checkout_id' => $request->checkout_id
            ]);

            // Calcula taxas usando o sistema flexível (com prioridade do usuário)
            $taxaCalculada = \App\Helpers\TaxaFlexivelHelper::calcularTaxaDeposito($valor, $setting, $user);
            $valor_liquido = $taxaCalculada['deposito_liquido'];
            $taxa_cash_in = $taxaCalculada['taxa_cash_in'];
            $descricao_taxa = $taxaCalculada['descricao'];

            Log::info('TrustyPixTrait::requestDepositTrustyPix - Cálculo de taxas:', [
                'amount_original' => $valor,
                'taxa_cash_in' => $taxa_cash_in,
                'deposito_liquido' => $valor_liquido,
                'descricao' => $descricao_taxa
            ]);

            $date = Carbon::now();
            $descricao = "Depósito PIX via TrustyPix - R$ " . number_format($valor, 2, ',', '.');

            $trustypix = new TrustyPixService();
            
            // Gerar ID externo único
            $externalId = 'trustypix_' . $user->user_id . '_' . time() . '_' . Str::random(8);
            
            // Validar CPF/CNPJ antes de enviar para o TrustyPix
            $documentNumber = $request->debtor_document_number ?? $request->cpf ?? $user->cpf_cnpj ?? null;
            
            // Se não houver documento, gerar um CPF válido para teste
            if (!$documentNumber || $documentNumber === '08355037120') {
                $documentNumber = \App\Helpers\Helper::generateValidCpf();
                Log::info('TrustyPixTrait: Gerando CPF válido para teste', ['cpf_gerado' => $documentNumber]);
            } else {
                $cleanDocument = preg_replace('/\D/', '', $documentNumber);
                if (strlen($cleanDocument) === 11) {
                    $documentNumber = $cleanDocument;
                } elseif (strlen($cleanDocument) === 14) {
                    $documentNumber = $cleanDocument;
                } else {
                    $documentNumber = \App\Helpers\Helper::generateValidCpf();
                    Log::info('TrustyPixTrait: Documento inválido, gerando CPF válido', ['documento_original' => $request->cpf]);
                }
            }

            // URL do callback
            $callbackUrl = env('APP_URL') . '/api/trustypix/callback/deposit';

            $chargeData = [
                'amount' => $valor,
                'external_id' => $externalId,
                'postback_url' => $callbackUrl,
                'description' => $descricao,
                'customer' => [
                    'name' => $request->debtor_name ?? $request->name ?? $user->name,
                    'document' => $documentNumber,
                    'email' => $request->email ?? $user->email,
                    'phone' => $request->phone ?? $request->telefone ?? $user->telefone ?? '11999999999'
                ]
            ];

            Log::info('TrustyPixTrait::requestDepositTrustyPix - Dados da cobrança:', $chargeData);

            $response = $trustypix->createCharge($chargeData);

            Log::info('TrustyPixTrait::requestDepositTrustyPix - Resposta completa da API:', [
                'status_code' => $response['status_code'] ?? 'N/A',
                'response_data' => $response['data'] ?? 'N/A',
                'success' => $response['success'] ?? false
            ]);

            if (!$response['success']) {
                Log::error('TrustyPixTrait::requestDepositTrustyPix - Erro na API:', [
                    'error' => $response['data']['message'] ?? 'Erro desconhecido',
                    'status_code' => $response['status_code'] ?? 'N/A'
                ]);
                
                // Mensagem específica para problemas de conectividade
                $errorMessage = $response['data']['message'] ?? "Erro ao processar pagamento.";
                if ($response['status_code'] == 503) {
                    $errorMessage = "Serviço de pagamento temporariamente indisponível. Tente novamente em alguns minutos.";
                }
                
                return [
                    "status" => $response['status_code'] ?? 500,
                    "data" => [
                        "status" => "error",
                        "message" => $errorMessage
                    ]
                ];
            }

            // Criar registro de solicitação
            $solicitacao = Solicitacoes::create([
                'user_id' => $user->user_id,
                'externalreference' => $externalId,
                'amount' => $valor,
                'deposito_liquido' => $valor_liquido,
                'taxa_cash_in' => $taxa_cash_in,
                'taxa_pix_cash_in_adquirente' => 0,
                'taxa_pix_cash_in_valor_fixo' => 0,
                'client_name' => $request->debtor_name ?? $request->name ?? $user->name,
                'client_document' => $documentNumber,
                'client_email' => $request->email ?? $user->email,
                'client_telefone' => $request->phone ?? $request->telefone ?? $user->telefone ?? '11999999999',
                'executor_ordem' => 'trustypix',
                'status' => 'WAITING_FOR_APPROVAL',
                'descricao_transacao' => $descricao_taxa,
                'idTransaction' => $externalId,
                'qrcode_pix' => $response['data']['brcode'] ?? null,
                'paymentcode' => $response['data']['brcode'] ?? null,
                'paymentCodeBase64' => $response['data']['brcode'] ?? null,
                'method' => 'PIX',
                'adquirente_ref' => 'trustypix',
                'callback' => $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
                'split_email' => $request->split_email ?? null,
                'split_username' => $request->split_username ?? null,
                'split_percentage' => $request->split_percentage ?? null,
                'date' => $date,
                'created_at' => $date,
                'updated_at' => $date
            ]);

            Log::info('TrustyPixTrait::requestDepositTrustyPix - Registro de solicitação criado:', [
                'solicitacao_id' => $solicitacao->id,
                'external_id' => $externalId,
                'status' => 'WAITING_FOR_APPROVAL'
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

            return [
                "status" => 200,
                "data" => [
                    "status" => "success",
                    "message" => "Cobrança PIX criada com sucesso.",
                    "data" => [
                        "id" => $externalId,
                        "brcode" => $response['data']['brcode'] ?? null,
                        "amount" => $valor,
                        "status" => "WAITING_FOR_APPROVAL",
                        "expires_at" => $response['data']['expires_at'] ?? null,
                        "created_at" => $date->toISOString()
                    ]
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Erro no TrustyPixTrait::requestDepositTrustyPix: ' . $e->getMessage());
            return [
                "status" => 500,
                "data" => [
                    "status" => "error",
                    "message" => "Erro interno do servidor."
                ]
            ];
        }
    }

    /**
     * Solicita pagamento PIX via TrustyPix
     */
    public static function requestPaymentTrustyPix($request)
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

            Log::info('=== TRUSTYPIXTRAIT REQUEST PAYMENT INICIADO ===');
            Log::info('TrustyPixTrait::requestPaymentTrustyPix - Dados da requisição:', [
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

            Log::info('TrustyPixTrait::requestPaymentTrustyPix - Cálculo de taxas:', [
                'amount_original' => $request->amount,
                'taxa_cash_out' => $taxa_cash_out,
                'cashout_liquido' => $cashout_liquido,
                'descricao' => $descricao,
                'user_saldo' => $user->saldo,
                'is_interface_web' => $isInterfaceWeb
            ]);

            $date = Carbon::now();
            $descricao = "Saque PIX via TrustyPix - R$ " . number_format($request->amount, 2, ',', '.');

            // Verificar saldo considerando taxa por fora
            $saldo_necessario = $valor_total_descontar;
            if ($user->saldo < $saldo_necessario) {
                Log::warning('TrustyPixTrait::requestPaymentTrustyPix - Saldo insuficiente:', [
                    'user_saldo' => $user->saldo,
                    'valor_necessario' => $saldo_necessario,
                    'diferenca' => $user->saldo - $saldo_necessario
                ]);
                
                return [
                    "status" => 400,
                    "data" => [
                        "status" => "error",
                        "message" => "Saldo insuficiente para realizar o saque."
                    ]
                ];
            }

            $trustypix = new TrustyPixService();
            
            // Gerar ID externo único
            $externalId = 'trustypix_saque_' . $user->user_id . '_' . time() . '_' . Str::random(8);

            // Normalizar dados da chave PIX
            $normalizedData = self::normalizePixKeyData(
                $request->pixKey,
                $request->pixKeyType,
                $request->beneficiaryDocument ?? null,
                $user->cpf ?? null
            );

            Log::info('TrustyPixTrait::requestPaymentTrustyPix - Dados normalizados:', $normalizedData);

            // Prepara dados do PIX
            $pixKey = $normalizedData['pix_key'];
            
            // Limpar caracteres especiais para CPF/CNPJ/Telefone
            if (in_array(strtolower($normalizedData['pix_key_type']), ['cpf', 'cnpj', 'telefone', 'phone'])) {
                $pixKey = preg_replace('/[^0-9]/', '', $pixKey);
            }

            // Mapear tipos de chave PIX para o formato da API TrustyPix
            $pixKeyTypeMapping = [
                'cpf' => 'CPF',
                'cnpj' => 'CNPJ',
                'email' => 'EMAIL',
                'telefone' => 'PHONE',
                'phone' => 'PHONE',
                'random' => 'RANDOM',
                'crypto' => 'CRYPTO'
            ];

            $pixKeyType = $pixKeyTypeMapping[strtolower($normalizedData['pix_key_type'])] ?? 'CPF';

            // URL do callback
            $callbackUrl = env('APP_URL') . '/api/trustypix/callback/withdraw';

            $paymentData = [
                'amount' => $cashout_liquido,
                'external_id' => $externalId,
                'postback_url' => $callbackUrl,
                'description' => $descricao,
                'pix_key' => $pixKey,
                'pix_key_type' => $pixKeyType,
                'beneficiary_name' => $request->beneficiaryName ?? $user->username,
                'beneficiary_document' => $normalizedData['document']
            ];

            Log::info('TrustyPixTrait::requestPaymentTrustyPix - Dados do pagamento:', $paymentData);

            $response = $trustypix->makePayment($paymentData);

            Log::info('TrustyPixTrait::requestPaymentTrustyPix - Resposta completa da API:', [
                'status_code' => $response['status_code'] ?? 'N/A',
                'response_data' => $response['data'] ?? 'N/A',
                'success' => $response['success'] ?? false
            ]);

            if (!$response['success']) {
                Log::error('TrustyPixTrait::requestPaymentTrustyPix - Erro na API:', [
                    'error' => $response['data']['message'] ?? 'Erro desconhecido',
                    'status_code' => $response['status_code'] ?? 'N/A'
                ]);
                
                return [
                    "status" => $response['status_code'] ?? 500,
                    "data" => [
                        "status" => "error",
                        "message" => $response['data']['message'] ?? "Erro ao processar saque."
                    ]
                ];
            }

            // Criar registro de saque
            $solicitacao = SolicitacoesCashOut::create([
                'user_id' => $user->user_id,
                'externalreference' => $externalId,
                'amount' => $request->amount,
                'beneficiaryname' => $request->beneficiaryName ?? $user->name ?? 'Usuário',
                'beneficiarydocument' => $normalizedData['document'],
                'pix' => $pixKey,
                'pixkey' => $pixKey,
                'date' => $date,
                'status' => 'PENDING',
                'type' => 'PIX',
                'idTransaction' => $externalId,
                'taxa_cash_out' => $taxa_cash_out,
                'cash_out_liquido' => $cashout_liquido,
                'descricao_transacao' => 'AUTOMATICO',
                'callback' => $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
                'created_at' => $date,
                'updated_at' => $date
            ]);

            // Atualizar saldo do usuário
            $valor_para_descontar = $taxaPorFora ? ($request->amount + $taxa_cash_out) : $request->amount;
            
            Log::info('=== TRUSTYPIXTRAIT::processarSaqueAutomatico - DESCONTO DE SALDO ===', [
                'user_id' => $user->user_id,
                'saldo_antes' => $user->saldo,
                'valor_saque' => $request->amount,
                'taxa_cash_out' => $taxa_cash_out,
                'taxa_por_fora' => $taxaPorFora,
                'valor_para_descontar' => $valor_para_descontar,
                'valor_sacado_antes' => $user->valor_sacado
            ]);
            
            \App\Helpers\Helper::decrementAmount($user, $valor_para_descontar, 'saldo');
            $user->increment('valor_sacado', $request->amount);

            Log::info('TrustyPixTrait::requestPaymentTrustyPix - Saque processado com sucesso:', [
                'solicitacao_id' => $solicitacao->id,
                'external_id' => $externalId,
                'status' => 'PENDING'
            ]);

            return [
                "status" => 200,
                "data" => [
                    "status" => "success",
                    "message" => "Saque PIX solicitado com sucesso.",
                    "data" => [
                        "id" => $externalId,
                        "amount" => $request->amount,
                        "status" => "PENDING",
                        "created_at" => $date->toISOString()
                    ]
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Erro no TrustyPixTrait::requestPaymentTrustyPix: ' . $e->getMessage());
            return [
                "status" => 500,
                "data" => [
                    "status" => "error",
                    "message" => "Erro interno do servidor."
                ]
            ];
        }
    }

    /**
     * Normaliza dados da chave PIX
     */
    private static function normalizePixKeyData($pixKey, $pixKeyType, $beneficiaryDocument = null, $userCpf = null)
    {
        $normalized = [
            'pix_key' => $pixKey,
            'pix_key_type' => strtolower($pixKeyType ?? 'cpf'),
            'document' => $beneficiaryDocument ?? $userCpf ?? '08355037120'
        ];

        // Limpar documento
        $normalized['document'] = preg_replace('/\D/', '', $normalized['document']);

        return $normalized;
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
            Log::warning('TrustyPixTrait::ensureUniqueTransactionId - ID duplicado encontrado (status: ' . $existing->status . '), gerando novo:', [
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
        Log::warning('TrustyPixTrait::ensureUniqueTransactionId - ID duplicado em uso ativo, gerando UUID novo:', [
            'id_original' => $requestedId,
            'id_novo' => $newId,
            'user_id' => $userId,
            'registro_existente_id' => $existing->id,
            'status_existente' => $existing->status
        ]);
        return $newId;
    }
}
