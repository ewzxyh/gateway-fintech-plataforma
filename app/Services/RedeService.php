<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Rede;

class RedeService
{
    public string $baseUrl;
    public string $tokenizationUrl;
    public string $pv;
    public string $token;
    public string $environment;

    public function __construct()
    {
        $redeConfig = Rede::first();
        if ($redeConfig) {
            $this->baseUrl = $redeConfig->getApiUrl();
            $this->tokenizationUrl = $redeConfig->getTokenizationUrl();
            $this->pv = $redeConfig->pv ?? '';
            $this->token = $redeConfig->token ?? '';
            $this->environment = $redeConfig->environment;
        } else {
            // Fallback para .env se não houver configuração no banco
            $this->baseUrl = env('REDE_API_URL', 'https://sandbox-erede.useredecloud.com.br');
            $this->tokenizationUrl = env('REDE_TOKENIZATION_URL', 'https://rl7-sandbox-api.useredecloud.com.br');
            $this->pv = env('REDE_PV');
            $this->token = env('REDE_TOKEN');
            $this->environment = env('REDE_ENVIRONMENT', 'sandbox');
        }
    }

    /**
     * Obtém o access_token usando OAuth 2.0
     */
    public function getAccessToken()
    {
        try {
            Log::info('RedeService::getAccessToken - Obtendo token OAuth 2.0');

            // Verificar se já temos um token válido
            $redeConfig = Rede::first();
            if ($redeConfig && $redeConfig->access_token && $redeConfig->token_expires_at && $redeConfig->token_expires_at->isFuture()) {
                Log::info('RedeService::getAccessToken - Token ainda válido');
                return $redeConfig->access_token;
            }

            // Gerar novo token
            $auth = base64_encode($this->clientId . ':' . $this->clientSecret);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->timeout(30)->post($this->tokenUrl, [
                'grant_type' => 'client_credentials'
            ]);

            Log::info('RedeService::getAccessToken - Resposta da API:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $accessToken = $result['access_token'] ?? null;
                $expiresIn = $result['expires_in'] ?? 3600;

                if ($accessToken && $redeConfig) {
                    // Salvar token no banco
                    $redeConfig->update([
                        'access_token' => $accessToken,
                        'token_expires_at' => now()->addSeconds($expiresIn - 60) // 1 minuto antes do vencimento
                    ]);

                    Log::info('RedeService::getAccessToken - Token salvo com sucesso');
                    return $accessToken;
                }
            } else {
                Log::error('RedeService::getAccessToken - Erro ao obter token:', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('RedeService::getAccessToken - Exceção: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cria um QR Code PIX para recebimento (Cash-in)
     */
    public function createPixQrCode($data)
    {
        try {
            Log::info('RedeService::createPixQrCode - Iniciando criação de QR Code PIX:', $data);

            // Preparar payload conforme documentação da Rede e.Rede
            $payload = [
                'amount' => (int)($data['amount'] * 100), // Valor em centavos
                'description' => $data['description'] ?? 'Pagamento via PIX',
                'externalId' => $data['external_id'],
                'callbackUrl' => $data['callback_url'],
                'expiresIn' => $data['expires_in'] ?? 3600, // 1 hora por padrão
                'customer' => [
                    'name' => 'Cliente Gateway',
                    'email' => 'redacted@example.invalid',
                    'document' => '08355037120'
                ]
            ];

            Log::info('RedeService::createPixQrCode - Payload:', $payload);

            // Criar autenticação conforme documentação da Rede e.Rede
            $auth = base64_encode($this->pv . ':' . $this->token);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'EXEMPLO-Integration/1.0'
            ])->timeout(30)->post($this->baseUrl . '/v1/transactions', $payload);

            Log::info('RedeService::createPixQrCode - Resposta da API:', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('RedeService::createPixQrCode - QR Code criado com sucesso:', $result);
                return $result;
            } else {
                Log::error('RedeService::createPixQrCode - Erro na resposta:', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'payload' => $payload
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('RedeService::createPixQrCode - Exceção: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Realiza um pagamento PIX (Cash-out)
     */
    public function makePayment($data)
    {
        try {
            Log::info('RedeService::makePayment - Iniciando pagamento PIX:', $data);

            // Validar e formatar chave PIX
            $pixKey = $this->formatPixKey($data['pix_key'], $data['pix_key_type'] ?? 'CPF');
            $pixKeyType = $this->validatePixKeyType($data['pix_key_type'] ?? 'CPF');

            $payload = [
                'amount' => (int)($data['amount'] * 100), // Valor em centavos
                'description' => $data['description'] ?? 'Saque via PIX',
                'externalId' => $data['external_id'],
                'callbackUrl' => $data['callback_url'],
                'pixKey' => $pixKey,
                'pixKeyType' => $pixKeyType,
                'beneficiaryName' => $data['beneficiary_name'] ?? 'Cliente Gateway',
                'beneficiaryDocument' => $data['beneficiary_document'] ?? $pixKey
            ];

            Log::info('RedeService::makePayment - Payload:', $payload);

            // Criar autenticação
            $auth = base64_encode($this->pv . ':' . $this->token);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->baseUrl . '/v1/transfers', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('RedeService::makePayment - Pagamento criado com sucesso:', $result);
                return $result;
            } else {
                Log::error('RedeService::makePayment - Erro ao criar pagamento:', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'payload' => $payload
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('RedeService::makePayment - Exceção: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cria uma venda com cartão de crédito
     */
    public function createCardSale($data)
    {
        try {
            Log::info('RedeService::createCardSale - Iniciando venda com cartão:', $data);

            // Preparar payload conforme documentação da Rede e.Rede (Postman)
            $payload = [
                'capture' => false,
                'kind' => 'credit',
                'reference' => 'EXEMPLO' . time(), // Formato correto para a Rede
                'amount' => (int)($data['amount'] * 100), // Valor em centavos
                'installments' => (int)($data['installments'] ?? 1),
                'cardholderName' => $data['card']['holderName'] ?? '',
                'cardNumber' => $data['card']['number'] ?? '',
                'expirationMonth' => (int)($data['card']['expirationMonth'] ?? 0),
                'expirationYear' => (int)($data['card']['expirationYear'] ?? 0),
                'securityCode' => $data['card']['cvv'] ?? '',
                'softDescriptor' => $data['softDescriptor'] ?? 'EXEMPLO',
                'subscription' => false,
                'origin' => 1,
                'distributorAffiliation' => 0,
                'brandTid' => $data['brandTid'] ?? null,
                'storageCard' => '0',
                'transactionCredentials' => [
                    'credentialId' => '01'
                ]
            ];

            Log::info('RedeService::createCardSale - Payload final:', $payload);

            // Criar autenticação Basic Auth
            $auth = base64_encode($this->pv . ':' . $this->token);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'EXEMPLO-Integration/1.0'
            ])->timeout(30)->post($this->baseUrl . '/v1/transactions', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('RedeService::createCardSale - Resposta recebida:', $result);

                // Processar resposta baseada no returnCode
                $returnCode = $result['returnCode'] ?? null;
                $returnMessage = $result['returnMessage'] ?? 'Sem mensagem';

                $errorInfo = $this->getRedeErrorInfo($returnCode);

                Log::info('RedeService::createCardSale - Análise do código:', [
                    'return_code' => $returnCode,
                    'return_message' => $returnMessage,
                    'error_type' => $errorInfo['type'],
                    'is_success' => $errorInfo['is_success'],
                    'description' => $errorInfo['description']
                ]);

                if ($errorInfo['is_success']) {
                    return [
                        'success' => true,
                        'transaction_id' => $result['tid'] ?? null,
                        'reference' => $result['reference'] ?? null,
                        'nsu' => $result['nsu'] ?? null,
                        'authorization_code' => $result['authorizationCode'] ?? null,
                        'amount' => $result['amount'] ?? null,
                        'return_code' => $returnCode,
                        'return_message' => $returnMessage,
                        'data' => $result
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => $result,
                        'error_type' => $errorInfo['type'],
                        'error_description' => $errorInfo['description'],
                        'return_code' => $returnCode,
                        'return_message' => $returnMessage,
                        'message' => $this->getUserFriendlyMessage($errorInfo['type'], $returnMessage)
                    ];
                }
            } else {
                $errorResponse = $response->json();
                Log::error('RedeService::createCardSale - Erro na resposta:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload
                ]);

                // Verificar se a resposta contém códigos válidos mesmo com status de erro
                if (isset($errorResponse['returnCode'])) {
                    $returnCode = $errorResponse['returnCode'];
                    $returnMessage = $errorResponse['returnMessage'] ?? 'Sem mensagem';

                    $errorInfo = $this->getRedeErrorInfo($returnCode);

                    Log::info('RedeService::createCardSale - Processando código de erro:', [
                        'return_code' => $returnCode,
                        'return_message' => $returnMessage,
                        'error_type' => $errorInfo['type'],
                        'description' => $errorInfo['description']
                    ]);

                    return [
                        'success' => false,
                        'error' => $errorResponse,
                        'error_type' => $errorInfo['type'],
                        'error_description' => $errorInfo['description'],
                        'return_code' => $returnCode,
                        'return_message' => $returnMessage,
                        'message' => $this->getUserFriendlyMessage($errorInfo['type'], $returnMessage),
                        'status_code' => $response->status()
                    ];
                }

                return [
                    'success' => false,
                    'error' => $errorResponse,
                    'status_code' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('RedeService::createCardSale - Erro na execução:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Mapeia códigos de retorno da Rede e.Rede baseado na documentação oficial
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

    /**
     * Retorna mensagem amigável para o usuário baseada no tipo de erro
     */
    private function getUserFriendlyMessage($errorType, $originalMessage)
    {
        $messages = [
            'success' => 'Pagamento processado com sucesso',
            'card_error' => 'Problema com o cartão de crédito. Verifique os dados e tente novamente.',
            'fraud' => 'Transação não autorizada por questões de segurança. Entre em contato com seu banco.',
            'processing_error' => 'Erro temporário no processamento. Tente novamente em alguns minutos.',
            'validation_error' => 'Dados inválidos. Verifique as informações e tente novamente.',
            'config_error' => 'Erro de configuração. Entre em contato com o suporte.',
            'limit_error' => 'Limite de transações excedido. Tente novamente mais tarde.',
            'cancelled' => 'Transação cancelada.',
            'pending' => 'Pagamento processado com sucesso',
            'unknown_error' => 'Erro no processamento. Tente novamente ou entre em contato com o suporte.'
        ];

        return $messages[$errorType] ?? 'Erro no processamento. Tente novamente.';
    }

    /**
     * Consulta o status de uma transação
     */
    public function getTransactionStatus($transactionId)
    {
        try {
            Log::info('RedeService::getTransactionStatus - Consultando status:', ['transactionId' => $transactionId]);

            $auth = base64_encode($this->pv . ':' . $this->token);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->get($this->baseUrl . '/v1/transactions/' . $transactionId);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('RedeService::getTransactionStatus - Status consultado com sucesso:', $result);
                return $result;
            } else {
                Log::error('RedeService::getTransactionStatus - Erro ao consultar status:', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('RedeService::getTransactionStatus - Exceção: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Formata a chave PIX removendo caracteres especiais
     */
    private function formatPixKey($pixKey, $pixKeyType)
    {
        switch (strtolower($pixKeyType)) {
            case 'cpf':
            case 'cnpj':
            case 'telefone':
            case 'phone':
                return preg_replace('/[^0-9]/', '', $pixKey);
            case 'email':
                return strtolower(trim($pixKey));
            case 'random':
                return $pixKey; // Chave aleatória já vem formatada
            default:
                return $pixKey;
        }
    }

    /**
     * Valida e mapeia o tipo de chave PIX
     */
    private function validatePixKeyType($pixKeyType)
    {
        $mapping = [
            'cpf' => 'CPF',
            'cnpj' => 'CNPJ',
            'telefone' => 'PHONE',
            'phone' => 'PHONE',
            'email' => 'EMAIL',
            'random' => 'RANDOM'
        ];

        return $mapping[strtolower($pixKeyType)] ?? 'CPF';
    }

    /**
     * Verifica se a API está funcionando
     */
    public function healthCheck()
    {
        try {
            $auth = base64_encode($this->pv . ':' . $this->token);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $auth,
                'Accept' => 'application/json'
            ])->get($this->baseUrl . '/v1/health');

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('RedeService::healthCheck - Exceção: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cria uma solicitação de tokenização
     */
    public function createTokenization(array $data)
    {
        try {
            Log::info('RedeService::createTokenization - Iniciando tokenização:', $data);

            $response = Http::withBasicAuth($this->pv, $this->token)
                ->post($this->tokenizationUrl . '/token-service/v2/tokenization/', $data);

            Log::info('RedeService::createTokenization - Resposta:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RedeService::createTokenization - Erro: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Consulta uma solicitação de tokenização
     */
    public function getTokenization(string $tokenizationId)
    {
        try {
            Log::info('RedeService::getTokenization - Consultando tokenização:', ['tokenizationId' => $tokenizationId]);

            $response = Http::withBasicAuth($this->pv, $this->token)
                ->get($this->tokenizationUrl . '/token-service/v2/tokenization/' . $tokenizationId);

            Log::info('RedeService::getTokenization - Resposta:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RedeService::getTokenization - Erro: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Atualiza o status de uma tokenização
     */
    public function updateTokenizationStatus(string $tokenizationId, array $data)
    {
        try {
            Log::info('RedeService::updateTokenizationStatus - Atualizando status:', [
                'tokenizationId' => $tokenizationId,
                'data' => $data
            ]);

            $response = Http::withBasicAuth($this->pv, $this->token)
                ->put($this->tokenizationUrl . '/token-service/v2/tokenization/' . $tokenizationId, $data);

            Log::info('RedeService::updateTokenizationStatus - Resposta:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RedeService::updateTokenizationStatus - Erro: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Gera cryptogram para uma tokenização
     */
    public function generateCryptogram(string $tokenizationId, array $data)
    {
        try {
            Log::info('RedeService::generateCryptogram - Gerando cryptogram:', [
                'tokenizationId' => $tokenizationId,
                'data' => $data
            ]);

            $response = Http::withBasicAuth($this->pv, $this->token)
                ->post($this->tokenizationUrl . '/token-service/v2/cryptogram/' . $tokenizationId, $data);

            Log::info('RedeService::generateCryptogram - Resposta:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RedeService::generateCryptogram - Erro: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Configura callback para tokenização
     */
    public function setTokenizationCallback(string $tokenizationId)
    {
        try {
            Log::info('RedeService::setTokenizationCallback - Configurando callback:', ['tokenizationId' => $tokenizationId]);

            $response = Http::put($this->tokenizationUrl . '/token-service/v2/tokenization/callback/' . $tokenizationId);

            Log::info('RedeService::setTokenizationCallback - Resposta:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RedeService::setTokenizationCallback - Erro: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Configura URL de callback para tokenização
     */
    public function setTokenizationCallbackUrl(array $data)
    {
        try {
            Log::info('RedeService::setTokenizationCallbackUrl - Configurando URL:', $data);

            $response = Http::withBasicAuth($this->pv, $this->token)
                ->post($this->tokenizationUrl . '/token-service/v1/tokenization/seturl', $data);

            Log::info('RedeService::setTokenizationCallbackUrl - Resposta:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RedeService::setTokenizationCallbackUrl - Erro: ' . $e->getMessage());
            return null;
        }
    }
}
