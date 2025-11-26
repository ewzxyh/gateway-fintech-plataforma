<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\TrustyPix;

class TrustyPixService
{
    public string $baseUrl;
    public ?string $clientId;
    public ?string $clientSecret;
    public ?string $token = null;

    public function __construct()
    {
        $trustypixConfig = TrustyPix::first();
        if ($trustypixConfig) {
            $this->baseUrl = $trustypixConfig->getApiUrl();
            $this->clientId = $trustypixConfig->client_id;
            $this->clientSecret = $trustypixConfig->client_secret;
        } else {
            // Fallback para .env se não houver configuração no banco
            $this->baseUrl = env('TRUSTYPIX_BASE_URL', 'https://api.trustypix.com');
            $this->clientId = env('TRUSTYPIX_CLIENT_ID');
            $this->clientSecret = env('TRUSTYPIX_CLIENT_SECRET');
        }
    }

    /**
     * Gera token de acesso para autenticação na API
     */
    public function generateToken()
    {
        try {
            Log::info('=== TRUSTYPIX GERAÇÃO DE TOKEN ===');
            Log::info('TrustyPixService::generateToken - URL:', ['url' => $this->baseUrl . '/api/auth/login']);
            Log::info('TrustyPixService::generateToken - Username:', ['username' => $this->clientId]);
            
            // Verificar conectividade antes de fazer a requisição
            if (!$this->checkConnectivity()) {
                Log::error('TrustyPixService::generateToken - Sem conectividade com a API');
                return false;
            }
            
            $response = Http::timeout(30)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT => 30
                ]
            ])->post($this->baseUrl . '/api/auth/login', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            ]);

            Log::info('TrustyPixService::generateToken - Response Status:', ['status' => $response->status()]);
            Log::info('TrustyPixService::generateToken - Response Body:', ['body' => $response->body()]);
            Log::info('=== FIM TRUSTYPIX GERAÇÃO DE TOKEN ===');

            if ($response->successful()) {
                $data = $response->json();
                $this->token = $data['access_token'] ?? $data['token'];
                Log::info('TrustyPixService::generateToken - Token gerado com sucesso:', ['token_length' => strlen($this->token)]);
                return $this->token;
            }

            Log::error('Erro ao gerar token TrustyPix: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('Exceção ao gerar token TrustyPix: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica conectividade com a API TrustyPix
     */
    private function checkConnectivity()
    {
        try {
            $parsedUrl = parse_url($this->baseUrl);
            $host = $parsedUrl['host'] ?? 'api.trustypix.com';
            
            // Tentar resolver o DNS
            $ip = gethostbyname($host);
            
            if ($ip === $host) {
                Log::warning('TrustyPixService::checkConnectivity - DNS não resolveu', ['host' => $host]);
                return false;
            }
            
            Log::info('TrustyPixService::checkConnectivity - DNS resolvido', ['host' => $host, 'ip' => $ip]);
            return true;
            
        } catch (\Exception $e) {
            Log::error('TrustyPixService::checkConnectivity - Erro na verificação: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cria uma cobrança PIX (Cash-in)
     */
    public function createCharge($data)
    {
        try {
            // Verificar conectividade antes de prosseguir
            if (!$this->checkConnectivity()) {
                Log::error('TrustyPixService::createCharge - Sem conectividade com a API');
                return [
                    'success' => false,
                    'status_code' => 503,
                    'data' => [
                        'message' => 'Serviço TrustyPix temporariamente indisponível'
                    ]
                ];
            }

            if (!$this->token) {
                $tokenGenerated = $this->generateToken();
                if (!$tokenGenerated) {
                    Log::error('TrustyPixService::createCharge - Falha ao gerar token');
                    return [
                        'success' => false,
                        'status_code' => 503,
                        'data' => [
                            'message' => 'Falha na autenticação com TrustyPix'
                        ]
                    ];
                }
            }

            $payload = [
                'amount' => (string) $data['amount'],
                'webhook' => $data['postback_url'],
                'external_id' => $data['external_id'],
                'description' => $data['description'] ?? 'Pagamento via PIX',
                'customer' => [
                    'name' => $data['customer']['name'] ?? 'Cliente',
                    'document' => $data['customer']['document'] ?? '67207393792',
                    'email' => $data['customer']['email'] ?? 'redacted@example.invalid',
                    'phone' => $data['customer']['phone'] ?? '11999999999'
                ]
            ];

            Log::info('TrustyPix - Payload enviado: ' . json_encode($payload));
            Log::info('TrustyPix - URL: ' . $this->baseUrl . '/api/charges');
            Log::info('TrustyPix - Token: ' . $this->token);

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT => 30
                ]
            ])->post($this->baseUrl . '/api/charges', $payload);

            Log::info('TrustyPix - Status da resposta: ' . $response->status());
            Log::info('TrustyPix - Corpo da resposta: ' . $response->body());

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'success' => true,
                    'status_code' => $response->status(),
                    'data' => [
                        'brcode' => $responseData['brcode'] ?? $responseData['qr_code'] ?? null,
                        'transaction_id' => $responseData['id'] ?? $responseData['transaction_id'] ?? null,
                        'status' => $responseData['status'] ?? 'pending',
                        'expires_at' => $responseData['expires_at'] ?? null,
                        'amount' => $responseData['amount'] ?? $data['amount']
                    ]
                ];
            }

            $errorData = $response->json();
            return [
                'success' => false,
                'status_code' => $response->status(),
                'data' => [
                    'message' => $errorData['message'] ?? $errorData['error'] ?? 'Erro ao criar cobrança',
                    'errors' => $errorData['errors'] ?? null
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Exceção ao criar cobrança TrustyPix: ' . $e->getMessage());
            return [
                'success' => false,
                'status_code' => 500,
                'data' => [
                    'message' => 'Erro interno do servidor'
                ]
            ];
        }
    }

    /**
     * Realiza pagamento PIX (Cash-out)
     */
    public function makePayment($data)
    {
        try {
            if (!$this->token) {
                $this->generateToken();
            }

            // Validar e formatar chave PIX
            $pixKey = $this->formatPixKey($data['pix_key'], $data['pix_key_type'] ?? 'CPF');
            $pixKeyType = $this->validatePixKeyType($data['pix_key_type'] ?? 'CPF');

            $payload = [
                'amount' => (float) $data['amount'],
                'webhook' => $data['postback_url'],
                'document' => $data['beneficiary_document'] ?? $pixKey,
                'pix_key' => $pixKey,
                'pix_key_type' => strtoupper($pixKeyType),
                'external_id' => $data['external_id'],
                'beneficiary_name' => $data['beneficiary_name'] ?? 'Beneficiário',
                'description' => $data['description'] ?? 'Saque via PIX'
            ];

            Log::info('=== TRUSTYPIX PAYLOAD ENVIADO ===');
            Log::info('TrustyPixService::makePayment - URL:', ['url' => $this->baseUrl . '/api/payments']);
            Log::info('TrustyPixService::makePayment - Headers:', [
                'Authorization' => 'Bearer ' . substr($this->token, 0, 20) . '...',
                'Content-Type' => 'application/json'
            ]);
            Log::info('TrustyPixService::makePayment - Payload Completo:', [
                'amount' => $payload['amount'],
                'webhook' => $payload['webhook'],
                'document' => $payload['document'],
                'pix_key' => $payload['pix_key'],
                'pix_key_type' => $payload['pix_key_type'],
                'external_id' => $payload['external_id'],
                'beneficiary_name' => $payload['beneficiary_name']
            ]);
            Log::info('TrustyPixService::makePayment - Payload JSON Raw:', ['payload' => json_encode($payload, JSON_PRETTY_PRINT)]);
            Log::info('=== FIM TRUSTYPIX PAYLOAD ===');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json'
            ])->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
                ]
            ])->post($this->baseUrl . '/api/payments', $payload);

            Log::info('TrustyPixService::makePayment - Response Status:', ['status' => $response->status()]);
            Log::info('TrustyPixService::makePayment - Response Body:', ['body' => $response->body()]);

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'success' => true,
                    'status_code' => $response->status(),
                    'data' => [
                        'transaction_id' => $responseData['id'] ?? $responseData['transaction_id'] ?? null,
                        'status' => $responseData['status'] ?? 'pending',
                        'amount' => $responseData['amount'] ?? $data['amount']
                    ]
                ];
            }

            $errorData = $response->json();
            return [
                'success' => false,
                'status_code' => $response->status(),
                'data' => [
                    'message' => $errorData['message'] ?? $errorData['error'] ?? 'Erro ao processar pagamento',
                    'errors' => $errorData['errors'] ?? null
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Exceção ao processar pagamento TrustyPix: ' . $e->getMessage());
            return [
                'success' => false,
                'status_code' => 500,
                'data' => [
                    'message' => 'Erro interno do servidor'
                ]
            ];
        }
    }

    /**
     * Formata chave PIX para o formato correto
     */
    private function formatPixKey($pixKey, $pixKeyType)
    {
        $pixKeyType = strtoupper($pixKeyType);
        
        switch ($pixKeyType) {
            case 'CPF':
            case 'CNPJ':
                return preg_replace('/\D/', '', $pixKey);
            case 'PHONE':
            case 'TELEFONE':
                return preg_replace('/\D/', '', $pixKey);
            case 'EMAIL':
                return strtolower(trim($pixKey));
            case 'RANDOM':
            case 'EVP':
            case 'CRYPTO':
                return $pixKey;
            default:
                return $pixKey;
        }
    }

    /**
     * Valida tipo de chave PIX
     */
    private function validatePixKeyType($pixKeyType)
    {
        $validTypes = ['CPF', 'CNPJ', 'EMAIL', 'PHONE', 'RANDOM'];
        $normalizedType = strtoupper(trim($pixKeyType));
        
        if (in_array($normalizedType, $validTypes)) {
            return $normalizedType;
        }
        
        // Mapear tipos comuns para os tipos aceitos pela TrustyPix
        $typeMapping = [
            'CPF' => 'CPF',
            'CNPJ' => 'CNPJ',
            'EMAIL' => 'EMAIL',
            'PHONE' => 'PHONE',
            'TELEFONE' => 'PHONE',
            'RANDOM' => 'RANDOM',
            'ALEATORIA' => 'RANDOM',
            'CRYPTO' => 'RANDOM',
            'CRIPTO' => 'RANDOM',
            'EVP' => 'RANDOM'
        ];
        
        return $typeMapping[$normalizedType] ?? 'CPF';
    }

    /**
     * Verifica status de uma transação
     */
    public function checkTransactionStatus($transactionId)
    {
        try {
            if (!$this->token) {
                $this->generateToken();
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json'
            ])->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
                ]
            ])->get($this->baseUrl . '/api/transactions/' . $transactionId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Erro ao verificar status da transação TrustyPix: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('Exceção ao verificar status da transação TrustyPix: ' . $e->getMessage());
            return false;
        }
    }
}
