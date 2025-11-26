<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PrimePay7;

class PrimePay7Service
{
    public ?string $baseUrl = null;
    public ?string $privateKey = null;
    public ?string $publicKey = null;
    public ?string $withdrawalKey = null;

    public function __construct()
    {
        $primepay7Config = PrimePay7::first();
        if ($primepay7Config) {
            $this->baseUrl = $primepay7Config->getApiUrl();
            $this->privateKey = $primepay7Config->private_key ?? null;
            $this->publicKey = $primepay7Config->public_key ?? null;
            $this->withdrawalKey = $primepay7Config->withdrawal_key ?? null;
        } else {
            // Fallback para .env se não houver configuração no banco
            $this->baseUrl = env('PRIMEPAY7_BASE_URL', 'https://api.primepay7.com');
            $this->privateKey = env('PRIMEPAY7_PRIVATE_KEY');
            $this->publicKey = env('PRIMEPAY7_PUBLIC_KEY');
            $this->withdrawalKey = env('PRIMEPAY7_WITHDRAWAL_KEY');
        }

        // Validar se as chaves estão configuradas
        if (empty($this->privateKey) || empty($this->publicKey)) {
            Log::warning('PrimePay7Service::__construct - Chaves de API não configuradas', [
                'has_private_key' => !empty($this->privateKey),
                'has_public_key' => !empty($this->publicKey),
                'has_withdrawal_key' => !empty($this->withdrawalKey)
            ]);
        }
    }

    /**
     * Retorna a chave apropriada baseada no tipo de operação
     */
    private function getKeyForOperation($operationType)
    {
        switch ($operationType) {
            case 'deposit':
            case 'public':
                return $this->publicKey;
            case 'withdraw':
            case 'private':
                return $this->privateKey;
            case 'external_withdraw':
                return $this->withdrawalKey;
            default:
                return $this->publicKey;
        }
    }

    /**
     * Cria um QR Code PIX para recebimento (Cash-in)
     */
    public function createPixQrCode($data)
    {
        try {
            // Usar Basic Auth com chave pública e chave privada (conforme documentação)
            $publicKey = $this->publicKey;
            $secretKey = $this->privateKey;

            $payload = [
                'amount' => (float) $data['amount'],
                'description' => $data['description'] ?? 'Pagamento via PIX',
                'external_id' => $data['external_id'],
                'callback_url' => $data['callback_url'],
                'expires_in' => $data['expires_in'] ?? 3600 // 1 hora por padrão
            ];

            Log::info('PrimePay7Service::createPixQrCode - Payload:', $payload);
            Log::info('PrimePay7Service::createPixQrCode - Usando Basic Auth:', [
                'public_key' => substr($publicKey, 0, 20) . '...',
                'secret_key' => substr($secretKey, 0, 20) . '...'
            ]);

            // Usar API real da PrimePay7 - tentar diferentes endpoints
            $endpoints = [
                '/api/v1/pix/qrcode',
                '/api/pix/qrcode',
                '/pix/qrcode',
                '/api/v1/qrcode',
                '/qrcode'
            ];

            // Usar API real da PrimePay7 com URL e formato corretos
            Log::info('PrimePay7Service::createPixQrCode - Chamando API real da PrimePay7');

            // Tentar diferentes endpoints possíveis para criar transação PIX
            $possibleEndpoints = [
                'https://api.primepay7.com/v1/transactions',
                'https://api.primepay7.com/v1/sales'
            ];

            $apiUrl = $possibleEndpoints[0]; // Começar com o primeiro endpoint

            // Payload correto conforme documentação da PrimePay7
            $primePayPayload = [
                'paymentMethod' => 'pix',
                'amount' => (int)($payload['amount'] * 100), // Valor em centavos
                'currency' => 'BRL',
                'description' => $payload['description'],
                'externalId' => $payload['external_id'],
                'callbackUrl' => $payload['callback_url'],
                'expiresIn' => $payload['expires_in'],
                'customer' => [
                    'name' => 'Cliente Gateway',
                    'email' => 'redacted@example.invalid',
                    'document' => [
                        'type' => 'cpf',
                        'number' => '08355037120'
                    ]
                ],
                'items' => [
                    [
                        'title' => $payload['description'],
                        'quantity' => 1,
                        'unitPrice' => (int)($payload['amount'] * 100),
                        'tangible' => false
                    ]
                ]
            ];

            Log::info('PrimePay7Service::createPixQrCode - Fazendo requisição para:', [
                'url' => $apiUrl,
                'payload' => $primePayPayload,
                'headers' => [
                    'Authorization' => 'Basic ' . substr(base64_encode($publicKey . ':' . $secretKey), 0, 20) . '...',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            // Tentar diferentes endpoints até encontrar um que funcione
            foreach ($possibleEndpoints as $endpoint) {
                Log::info('PrimePay7Service::createPixQrCode - Tentando endpoint: ' . $endpoint);

                // Criar Basic Auth conforme documentação da PrimePay7
                $auth = 'Basic ' . base64_encode($publicKey . ':' . $secretKey);

                $response = Http::withHeaders([
                    'Authorization' => $auth,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'EXEMPLO-Integration/1.0'
                ])->timeout(30)->post($endpoint, $primePayPayload);

                Log::info('PrimePay7Service::createPixQrCode - Resposta da API:', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'headers' => $response->headers(),
                    'body' => $response->body()
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    Log::info('PrimePay7Service::createPixQrCode - QR Code criado com sucesso via API real no endpoint: ' . $endpoint);
                    Log::info('PrimePay7Service::createPixQrCode - Resposta completa:', $result);

                    // Garantir que o ID da transação esteja disponível no formato esperado
                    if (isset($result['id'])) {
                        Log::info('PrimePay7Service::createPixQrCode - ID da transação PrimePay7: ' . $result['id']);
                    } else {
                        Log::warning('PrimePay7Service::createPixQrCode - ID da transação não encontrado na resposta');
                        Log::warning('PrimePay7Service::createPixQrCode - Chaves disponíveis: ' . implode(', ', array_keys($result)));
                    }

                    return $result;
                } else {
                    Log::warning('PrimePay7Service::createPixQrCode - Endpoint falhou: ' . $endpoint . ' - Status: ' . $response->status());
                    Log::warning('PrimePay7Service::createPixQrCode - Resposta de erro: ' . $response->body());
                }
            }

            // Se nenhum endpoint funcionou
            Log::error('PrimePay7Service::createPixQrCode - Todos os endpoints falharam', [
                'endpoints_tested' => $possibleEndpoints,
                'payload' => $primePayPayload
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('PrimePay7Service::createPixQrCode - Exceção: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Realiza um pagamento PIX (Cash-out)
     */
    public function makePayment($data)
    {
        try {
            // Verificar se as chaves estão configuradas
            if (empty($this->privateKey) || empty($this->publicKey) || empty($this->withdrawalKey)) {
                Log::error('PrimePay7Service::makePayment - Chaves de API não configuradas');
                return null;
            }

            // Usar chave de saque externo conforme documentação oficial
            $withdrawKey = $this->withdrawalKey;

            // Validar e formatar chave PIX
            $pixKey = $this->formatPixKey($data['pix_key'], $data['pix_key_type'] ?? 'CPF');
            $pixKeyType = $this->validatePixKeyType($data['pix_key_type'] ?? 'CPF');

            // Converter valor para centavos conforme documentação
            $amountInCents = (int) ($data['amount'] * 100);

            $payload = [
                'method' => 'fiat',
                'amount' => $amountInCents,
                'netPayout' => true, // Taxa será descontada do valor solicitado
                'pixKey' => $pixKey,
                'pixKeyType' => $pixKeyType,
                'postbackUrl' => $data['callback_url']
            ];

            Log::info('PrimePay7Service::makePayment - Payload:', $payload);
            Log::info('PrimePay7Service::makePayment - Usando withdrawal key: ' . substr($withdrawKey, 0, 20) . '...');

            // Criar autenticação Basic com public_key:private_key
            $basicAuth = base64_encode($this->publicKey . ':' . $this->privateKey);

            $response = Http::withHeaders([
                'x-withdraw-key' => $withdrawKey,
                'Authorization' => 'Basic ' . $basicAuth,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://api.primepay7.com/v1/transfers', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('PrimePay7Service::makePayment - Pagamento criado com sucesso', $result);
                return $result;
            } else {
                Log::error('PrimePay7Service::makePayment - Erro ao criar pagamento', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'payload' => $payload
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('PrimePay7Service::makePayment - Exceção: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Consulta o status de uma transação
     */
    public function getTransactionStatus($transactionId)
    {
        try {
            // Usar chave pública para consultas
            $key = $this->publicKey;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $key,
                'Content-Type' => 'application/json'
            ])->get($this->baseUrl . '/transactions/' . $transactionId);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('PrimePay7Service::getTransactionStatus - Status consultado com sucesso');
                return $result;
            } else {
                Log::error('PrimePay7Service::getTransactionStatus - Erro ao consultar status', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('PrimePay7Service::getTransactionStatus - Exceção: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Formata a chave PIX removendo caracteres especiais e validando formato
     */
    private function formatPixKey($pixKey, $pixKeyType)
    {
        $pixKey = trim($pixKey);
        $type = strtolower($pixKeyType);

        switch ($type) {
            case 'cpf':
                // Remove tudo exceto números
                $pixKey = preg_replace('/[^0-9]/', '', $pixKey);
                // Valida se tem exatamente 11 dígitos
                if (strlen($pixKey) !== 11 || !preg_match('/^\d{11}$/', $pixKey)) {
                    Log::warning('PrimePay7Service::formatPixKey - CPF inválido', [
                        'pix_key' => $pixKey,
                        'length' => strlen($pixKey)
                    ]);
                }
                return $pixKey;

            case 'cnpj':
                // Remove tudo exceto números
                $pixKey = preg_replace('/[^0-9]/', '', $pixKey);
                // Valida se tem exatamente 14 dígitos
                if (strlen($pixKey) !== 14 || !preg_match('/^\d{14}$/', $pixKey)) {
                    Log::warning('PrimePay7Service::formatPixKey - CNPJ inválido', [
                        'pix_key' => $pixKey,
                        'length' => strlen($pixKey)
                    ]);
                }
                return $pixKey;

            case 'telefone':
            case 'phone':
                // Remove tudo exceto números
                $pixKey = preg_replace('/[^0-9]/', '', $pixKey);
                // Valida se tem 10 ou 11 dígitos (DDD + número)
                // Formato: (XX) 9XXXX-XXXX ou (XX) XXXX-XXXX
                if (!preg_match('/^\d{10,11}$/', $pixKey)) {
                    Log::warning('PrimePay7Service::formatPixKey - Telefone inválido', [
                        'pix_key' => $pixKey,
                        'length' => strlen($pixKey)
                    ]);
                }
                return $pixKey;

            case 'email':
                $pixKey = strtolower(trim($pixKey));
                // Valida formato de email
                if (!filter_var($pixKey, FILTER_VALIDATE_EMAIL)) {
                    Log::warning('PrimePay7Service::formatPixKey - Email inválido', [
                        'pix_key' => $pixKey
                    ]);
                }
                return $pixKey;

            case 'random':
            case 'aleatoria':
            case 'evp':
                // Remove espaços e converte para minúsculo
                $pixKey = strtolower(str_replace(' ', '', $pixKey));
                // Valida formato UUID (com ou sem hífens)
                // Formato: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
                if (!preg_match('/^[a-f0-9]{8}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{12}$/i', $pixKey)) {
                    Log::warning('PrimePay7Service::formatPixKey - Chave aleatória com formato inválido', [
                        'pix_key' => $pixKey
                    ]);
                }
                return $pixKey;

            default:
                Log::warning('PrimePay7Service::formatPixKey - Tipo de chave desconhecido', [
                    'pix_key_type' => $pixKeyType,
                    'pix_key' => $pixKey
                ]);
                return $pixKey;
        }
    }

    /**
     * Valida e mapeia o tipo de chave PIX
     */
    private function validatePixKeyType($pixKeyType)
    {
        // Mapeamento para os tipos aceitos pela API PrimePay7
        // Retorna em minúsculo pois a linha 215 converte com strtolower()
        $mapping = [
            'cpf' => 'cpf',
            'cnpj' => 'cnpj',
            'telefone' => 'phone',
            'phone' => 'phone',
            'email' => 'email',
            'random' => 'evp',      // Chave aleatória
            'aleatoria' => 'evp',   // Chave aleatória (PT-BR)
            'evp' => 'evp'          // Chave aleatória (padrão BACEN)
        ];

        return $mapping[strtolower($pixKeyType)] ?? 'cpf';
    }

    /**
     * Detecta automaticamente o tipo de chave PIX baseado no formato
     * Útil para validação ou quando o tipo não é informado corretamente
     */
    private function detectPixKeyType($pixKey): string
    {
        $pixKey = trim($pixKey);

        // Testar EMAIL primeiro (mais específico)
        if (filter_var($pixKey, FILTER_VALIDATE_EMAIL)) {
            return 'EMAIL';
        }

        // Remover caracteres especiais para testar números
        $numbersOnly = preg_replace('/[^0-9]/', '', $pixKey);

        // Testar CPF (11 dígitos)
        if (strlen($numbersOnly) === 11 && preg_match('/^\d{11}$/', $numbersOnly)) {
            return 'CPF';
        }

        // Testar CNPJ (14 dígitos)
        if (strlen($numbersOnly) === 14 && preg_match('/^\d{14}$/', $numbersOnly)) {
            return 'CNPJ';
        }

        // Testar TELEFONE (10 ou 11 dígitos)
        if ((strlen($numbersOnly) === 10 || strlen($numbersOnly) === 11) && preg_match('/^\d{10,11}$/', $numbersOnly)) {
            return 'PHONE';
        }

        // Testar CHAVE ALEATÓRIA (formato UUID)
        $cleanKey = strtolower(str_replace(' ', '', $pixKey));
        if (preg_match('/^[a-f0-9]{8}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{4}-?[a-f0-9]{12}$/i', $cleanKey)) {
            return 'RANDOM';
        }

        // Se não detectou nenhum formato válido
        Log::warning('PrimePay7Service::detectPixKeyType - Não foi possível detectar o tipo da chave PIX', [
            'pix_key' => $pixKey,
            'numbers_only' => $numbersOnly,
            'length' => strlen($pixKey)
        ]);

        return 'UNKNOWN';
    }

    /**
     * Realiza um saque externo usando a chave de saque específica
     */
    public function makeExternalWithdrawal($data)
    {
        try {
            // Usar chave de saque externo
            $key = $this->withdrawalKey;

            // Validar e formatar chave PIX
            $pixKey = $this->formatPixKey($data['pix_key'], $data['pix_key_type'] ?? 'CPF');
            $pixKeyType = $this->validatePixKeyType($data['pix_key_type'] ?? 'CPF');

            $payload = [
                'amount' => (float) $data['amount'],
                'description' => $data['description'] ?? 'Saque externo via PIX',
                'external_id' => $data['external_id'],
                'callback_url' => $data['callback_url'],
                'pix_key' => $pixKey,
                'pix_key_type' => $pixKeyType,
                'beneficiary_name' => $data['beneficiary_name'] ?? '',
                'beneficiary_document' => $data['beneficiary_document'] ?? $pixKey,
                'external_withdrawal' => true
            ];

            Log::info('PrimePay7Service::makeExternalWithdrawal - Payload:', $payload);
            Log::info('PrimePay7Service::makeExternalWithdrawal - Usando chave de saque: ' . substr($key, 0, 20) . '...');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $key,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/pix/external-withdrawal', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('PrimePay7Service::makeExternalWithdrawal - Saque externo criado com sucesso');
                return $result;
            } else {
                Log::error('PrimePay7Service::makeExternalWithdrawal - Erro ao criar saque externo', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('PrimePay7Service::makeExternalWithdrawal - Exceção: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verifica se a API está funcionando
     */
    public function healthCheck()
    {
        try {
            $response = Http::get($this->baseUrl . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('PrimePay7Service::healthCheck - Exceção: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cria uma venda com cartão de crédito (Card Sales)
     * Documentação: https://bank.primepay7.com/docs/sales/create-sale
     */
    public function createCardSale($data)
    {
        try {
            // Usar chave pública para vendas com cartão
            $publicKey = $this->publicKey;

            Log::info('PrimePay7Service::createCardSale - Iniciando venda com cartão:', $data);

            // Preparar payload conforme documentação da PrimePay7
            $payload = [
                'paymentMethod' => 'credit_card', // Método de pagamento
                'amount' => (int) $data['amount'], // Valor em centavos
                'installments' => (int) ($data['installments'] ?? 1), // Número de parcelas

                // Items (produtos)
                'items' => $data['items'] ?? [],

                // Cliente
                'customer' => $data['customer'] ?? [],
            ];

            // Dados do cartão - enviar hash OU dados completos
            if (!empty($data['card']['hash'])) {
                // Se tiver hash, enviar apenas ele
                $payload['card'] = [
                    'hash' => $data['card']['hash']
                ];
            } else {
                // Caso contrário, enviar dados completos
                $payload['card'] = [
                    'number' => $data['card']['number'] ?? null,
                    'holderName' => $data['card']['holderName'] ?? null,
                    'expirationMonth' => (int) ($data['card']['expirationMonth'] ?? 0),
                    'expirationYear' => (int) ($data['card']['expirationYear'] ?? 0),
                    'cvv' => $data['card']['cvv'] ?? null,
                ];
            }

            // Adicionar dados 3DS se fornecidos
            if (isset($data['threeDS']) && !empty($data['threeDS'])) {
                $payload['threeDS'] = $data['threeDS'];
            }

            // Adicionar returnURL se fornecido (para 3DS REDIRECT)
            if (isset($data['returnURL'])) {
                $payload['returnURL'] = $data['returnURL'];
            }

            Log::info('PrimePay7Service::createCardSale - Payload final:', $payload);

            // Criar autenticação Basic com public_key:private_key
            $basicAuth = base64_encode($publicKey . ':' . $this->privateKey);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $basicAuth,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://api.primepay7.com/v1/transactions', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('PrimePay7Service::createCardSale - Venda criada com sucesso:', $result);
                return $result;
            } else {
                $errorResponse = $response->json();
                Log::error('PrimePay7Service::createCardSale - Erro na resposta:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload
                ]);
                return [
                    'success' => false,
                    'error' => $errorResponse,
                    'status_code' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('PrimePay7Service::createCardSale - Erro na execução:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
