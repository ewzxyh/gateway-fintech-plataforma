<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeService
{
    private $secretKey;
    private $publishableKey;
    private $webhookSecret;
    private $environment;
    private $currency;
    private $country;

    public function __construct()
    {
        $stripe = \App\Models\Stripe::first();

        if ($stripe) {
            $this->secretKey = $stripe->secret_key;
            $this->publishableKey = $stripe->publishable_key;
            $this->webhookSecret = $stripe->webhook_secret;
            $this->environment = $stripe->environment ?? 'sandbox';
            $this->currency = $stripe->currency ?? 'usd';
            $this->country = $stripe->country ?? 'US';
        }
    }

    /**
     * Cria uma venda com cartão de crédito usando PaymentIntent
     */
    public function createCardSale($data)
    {
        try {
            Log::info('StripeService::createCardSale - Iniciando venda com cartão:', $data);

            // Primeiro, criar o PaymentIntent
            $paymentIntentResult = $this->createPaymentIntent($data);

            if (!$paymentIntentResult['success']) {
                return $paymentIntentResult;
            }

            $paymentIntentId = $paymentIntentResult['payment_intent_id'];
            Log::info('StripeService::createCardSale - PaymentIntent criado:', ['id' => $paymentIntentId]);

            // Agora, criar o PaymentMethod com os dados do cartão
            $paymentMethodResult = $this->createPaymentMethod($data['card']);

            if (!$paymentMethodResult['success']) {
                Log::error('StripeService::createCardSale - Erro ao criar PaymentMethod:', $paymentMethodResult);
                return [
                    'success' => false,
                    'error' => $paymentMethodResult['error'],
                    'message' => 'Erro ao processar dados do cartão'
                ];
            }

            $paymentMethodId = $paymentMethodResult['payment_method_id'];
            Log::info('StripeService::createCardSale - PaymentMethod criado:', ['id' => $paymentMethodId]);

            // Anexar o PaymentMethod ao PaymentIntent
            $attachResult = $this->attachPaymentMethodToIntent($paymentIntentId, $paymentMethodId);

            if (!$attachResult['success']) {
                Log::error('StripeService::createCardSale - Erro ao anexar PaymentMethod:', $attachResult);
                return [
                    'success' => false,
                    'error' => $attachResult['error'],
                    'message' => 'Erro ao processar pagamento'
                ];
            }

            // Confirmar o PaymentIntent
            $confirmResult = $this->confirmPaymentIntent($paymentIntentId);

            if (!$confirmResult['success']) {
                Log::error('StripeService::createCardSale - Erro ao confirmar PaymentIntent:', $confirmResult);
                return [
                    'success' => false,
                    'error' => $confirmResult['error'],
                    'message' => 'Erro ao confirmar pagamento'
                ];
            }

            Log::info('StripeService::createCardSale - Pagamento processado com sucesso:', [
                'payment_intent_id' => $paymentIntentId,
                'payment_method_id' => $paymentMethodId,
                'status' => $confirmResult['status']
            ]);

            return [
                'success' => true,
                'transaction_id' => $paymentIntentId,
                'payment_intent_id' => $paymentIntentId,
                'payment_method_id' => $paymentMethodId,
                'amount' => $data['amount'],
                'currency' => $this->currency,
                'status' => $confirmResult['status'],
                'data' => $confirmResult['data']
            ];
        } catch (\Exception $e) {
            Log::error('StripeService::createCardSale - Erro na execução:', [
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
     * Cria um PaymentIntent para processar pagamentos
     */
    public function createPaymentIntent($data)
    {
        try {
            Log::info('StripeService::createPaymentIntent - Criando PaymentIntent:', $data);

            $payload = [
                'amount' => (int)($data['amount'] * 100),
                'currency' => $this->currency,
                'description' => $data['description'] ?? 'Pagamento via EXEMPLO',
                'metadata' => [
                    'customer_name' => $data['customer']['name'] ?? '',
                    'customer_email' => $data['customer']['email'] ?? '',
                    'customer_document' => $data['customer']['document']['number'] ?? '',
                    'customer_phone' => $data['customer']['phone'] ?? '',
                    'checkout_id' => $data['externalId'] ?? '',
                    'installments' => $data['installments'] ?? 1,
                    'customer_ip' => $data['customer_ip'] ?? ''
                ],
                'receipt_email' => $data['customer']['email'] ?? null
            ];

            Log::info('StripeService::createPaymentIntent - Payload final:', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
                'User-Agent' => 'EXEMPLO-Integration/1.0'
            ])->timeout(30)->asForm()->post('https://api.stripe.com/v1/payment_intents', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('StripeService::createPaymentIntent - PaymentIntent criado:', $result);

                return [
                    'success' => true,
                    'transaction_id' => $result['id'],
                    'payment_intent_id' => $result['id'],
                    'amount' => $result['amount'],
                    'currency' => $result['currency'],
                    'status' => $result['status'],
                    'client_secret' => $result['client_secret'],
                    'data' => $result
                ];
            } else {
                $errorResponse = $response->json();
                Log::error('StripeService::createPaymentIntent - Erro na resposta:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload
                ]);

                return [
                    'success' => false,
                    'error' => $errorResponse,
                    'status_code' => $response->status(),
                    'message' => $this->getStripeErrorMessage($errorResponse['error']['code'] ?? 'unknown')
                ];
            }
        } catch (\Exception $e) {
            Log::error('StripeService::createPaymentIntent - Erro na execução:', [
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
     * Cria um PaymentMethod usando token de teste (para desenvolvimento)
     */
    public function createPaymentMethod($cardData)
    {
        try {
            Log::info('StripeService::createPaymentMethod - Criando PaymentMethod com token de teste:', [
                'number' => substr($cardData['number'], 0, 4) . '****',
                'exp_month' => $cardData['expirationMonth'],
                'exp_year' => $cardData['expirationYear']
            ]);

            // Para desenvolvimento, usar token de teste baseado no número do cartão
            $testToken = $this->getTestTokenForCard($cardData['number']);

            if (!$testToken) {
                Log::error('StripeService::createPaymentMethod - Token de teste não encontrado para cartão:', [
                    'number' => substr($cardData['number'], 0, 4) . '****'
                ]);
                return [
                    'success' => false,
                    'error' => 'Cartão de teste não suportado',
                    'message' => 'Use um cartão de teste válido do Stripe'
                ];
            }

            $payload = [
                'type' => 'card',
                'card[token]' => $testToken,
                'billing_details[name]' => $cardData['holderName'] ?? '',
                'billing_details[phone]' => $cardData['customer_phone'] ?? '',
                'metadata' => [
                    'customer_ip' => $cardData['customer_ip'] ?? '',
                    'customer_phone' => $cardData['customer_phone'] ?? ''
                ]
            ];

            Log::info('StripeService::createPaymentMethod - Payload com token:', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
                'User-Agent' => 'EXEMPLO-Integration/1.0'
            ])->timeout(30)->asForm()->post('https://api.stripe.com/v1/payment_methods', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('StripeService::createPaymentMethod - PaymentMethod criado com sucesso');

                return [
                    'success' => true,
                    'payment_method_id' => $result['id'],
                    'data' => $result
                ];
            } else {
                $errorResponse = $response->json();
                Log::error('StripeService::createPaymentMethod - Erro na resposta:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload
                ]);

                return [
                    'success' => false,
                    'error' => $errorResponse,
                    'message' => $this->getStripeErrorMessage($errorResponse['error']['code'] ?? 'unknown')
                ];
            }
        } catch (\Exception $e) {
            Log::error('StripeService::createPaymentMethod - Erro na execução:', [
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
     * Retorna token de teste baseado no número do cartão
     */
    private function getTestTokenForCard($cardNumber)
    {
        // Mapear números de cartão de teste para tokens de teste do Stripe
        $testTokens = [
            '4242424242424242' => 'tok_visa', // Visa
            '4000000000000002' => 'tok_visa_debit', // Visa Debit
            '4000000000009995' => 'tok_visa_debit', // Visa Debit
            '5555555555554444' => 'tok_mastercard', // Mastercard
            '2223003122003222' => 'tok_mastercard', // Mastercard
            '378282246310005' => 'tok_amex', // American Express
            '371449635398431' => 'tok_amex', // American Express
            '6011111111111117' => 'tok_discover', // Discover
            '6011000990139424' => 'tok_discover', // Discover
            '30569309025904' => 'tok_diners', // Diners Club
            '38520000023237' => 'tok_diners', // Diners Club
            '4895370010000005' => 'tok_visa', // Cartão do usuário - Visa
        ];

        return $testTokens[$cardNumber] ?? null;
    }

    /**
     * Anexa um PaymentMethod a um PaymentIntent
     */
    public function attachPaymentMethodToIntent($paymentIntentId, $paymentMethodId)
    {
        try {
            Log::info('StripeService::attachPaymentMethodToIntent - Anexando PaymentMethod:', [
                'payment_intent_id' => $paymentIntentId,
                'payment_method_id' => $paymentMethodId
            ]);

            $payload = [
                'payment_method' => $paymentMethodId
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
                'User-Agent' => 'EXEMPLO-Integration/1.0'
            ])->timeout(30)->asForm()->post("https://api.stripe.com/v1/payment_intents/{$paymentIntentId}", $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('StripeService::attachPaymentMethodToIntent - PaymentMethod anexado com sucesso');

                return [
                    'success' => true,
                    'data' => $result
                ];
            } else {
                $errorResponse = $response->json();
                Log::error('StripeService::attachPaymentMethodToIntent - Erro na resposta:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => $errorResponse,
                    'message' => $this->getStripeErrorMessage($errorResponse['error']['code'] ?? 'unknown')
                ];
            }
        } catch (\Exception $e) {
            Log::error('StripeService::attachPaymentMethodToIntent - Erro na execução:', [
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
     * Confirma um PaymentIntent
     */
    public function confirmPaymentIntent($paymentIntentId)
    {
        try {
            Log::info('StripeService::confirmPaymentIntent - Confirmando PaymentIntent:', ['id' => $paymentIntentId]);

            $payload = [
                'return_url' => 'https://demo.gateway.shop/obrigado'
            ];

            // Para confirmar um PaymentIntent, usar POST para /confirm
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
                'User-Agent' => 'EXEMPLO-Integration/1.0'
            ])->timeout(30)->asForm()->post("https://api.stripe.com/v1/payment_intents/{$paymentIntentId}/confirm", $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('StripeService::confirmPaymentIntent - PaymentIntent confirmado:', [
                    'id' => $paymentIntentId,
                    'status' => $result['status']
                ]);

                return [
                    'success' => true,
                    'status' => $result['status'],
                    'data' => $result
                ];
            } else {
                $errorResponse = $response->json();
                Log::error('StripeService::confirmPaymentIntent - Erro na resposta:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => $errorResponse,
                    'message' => $this->getStripeErrorMessage($errorResponse['error']['code'] ?? 'unknown')
                ];
            }
        } catch (\Exception $e) {
            Log::error('StripeService::confirmPaymentIntent - Erro na execução:', [
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
     * Tokeniza um cartão de crédito
     */
    public function createToken($cardData)
    {
        try {
            Log::info('StripeService::createToken - Tokenizando cartão:', [
                'number' => substr($cardData['number'], 0, 4) . '****',
                'exp_month' => $cardData['exp_month'],
                'exp_year' => $cardData['exp_year']
            ]);

            $payload = [
                'card[number]' => $cardData['number'],
                'card[exp_month]' => $cardData['exp_month'],
                'card[exp_year]' => $cardData['exp_year'],
                'card[cvc]' => $cardData['cvc'],
                'card[name]' => $cardData['name'] ?? ''
            ];

            Log::info('StripeService::createToken - Payload:', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
                'User-Agent' => 'EXEMPLO-Integration/1.0'
            ])->timeout(30)->post('https://api.stripe.com/v1/tokens', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('StripeService::createToken - Token criado com sucesso');

                return [
                    'success' => true,
                    'token' => $result['id'],
                    'card' => $result['card'],
                    'data' => $result
                ];
            } else {
                $errorResponse = $response->json();
                Log::error('StripeService::createToken - Erro na resposta:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload
                ]);

                return [
                    'success' => false,
                    'error' => $errorResponse,
                    'message' => $this->getStripeErrorMessage($errorResponse['error']['code'] ?? 'unknown')
                ];
            }
        } catch (\Exception $e) {
            Log::error('StripeService::createToken - Erro na execução:', [
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
     * Consulta o status de um PaymentIntent
     */
    public function getPaymentIntentStatus($paymentIntentId)
    {
        try {
            Log::info('StripeService::getPaymentIntentStatus - Consultando status:', ['paymentIntentId' => $paymentIntentId]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Accept' => 'application/json',
                'User-Agent' => 'EXEMPLO-Integration/1.0'
            ])->timeout(30)->get("https://api.stripe.com/v1/payment_intents/{$paymentIntentId}");

            if ($response->successful()) {
                $result = $response->json();
                Log::info('StripeService::getPaymentIntentStatus - Status consultado:', $result);

                return [
                    'success' => true,
                    'payment_intent_id' => $result['id'],
                    'status' => $result['status'],
                    'amount' => $result['amount'],
                    'currency' => $result['currency'],
                    'data' => $result
                ];
            } else {
                $errorResponse = $response->json();
                Log::error('StripeService::getPaymentIntentStatus - Erro na resposta:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => $errorResponse,
                    'status_code' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('StripeService::getPaymentIntentStatus - Erro na execução:', [
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
     * Mapeia códigos de erro do Stripe para mensagens amigáveis
     */
    private function getStripeErrorMessage($errorCode)
    {
        $errorMap = [
            'card_declined' => 'Cartão recusado. Verifique os dados e tente novamente.',
            'expired_card' => 'Cartão expirado. Use outro cartão.',
            'incorrect_cvc' => 'Código de segurança inválido.',
            'insufficient_funds' => 'Saldo insuficiente no cartão.',
            'invalid_expiry_month' => 'Mês de expiração inválido.',
            'invalid_expiry_year' => 'Ano de expiração inválido.',
            'invalid_number' => 'Número do cartão inválido.',
            'processing_error' => 'Erro no processamento. Tente novamente.',
            'rate_limit' => 'Muitas tentativas. Aguarde um momento.',
            'authentication_required' => 'Autenticação adicional necessária.',
            'generic_decline' => 'Transação não autorizada.',
            'lost_card' => 'Cartão reportado como perdido.',
            'stolen_card' => 'Cartão reportado como roubado.',
            'withdrawal_count_limit_exceeded' => 'Limite de tentativas excedido.',
            'new_account_information_available' => 'Informações da conta atualizadas.',
            'restricted_card' => 'Cartão com restrições.',
            'security_violation' => 'Violação de segurança detectada.',
            'service_not_allowed' => 'Serviço não permitido.',
            'stop_payment_order' => 'Pagamento cancelado.',
            'testmode_decline' => 'Cartão de teste recusado.',
            'transaction_not_allowed' => 'Transação não permitida.',
            'try_again_later' => 'Tente novamente mais tarde.',
            'withdrawal_count_limit_exceeded' => 'Limite de tentativas excedido.'
        ];

        return $errorMap[$errorCode] ?? 'Erro no processamento. Tente novamente.';
    }

    /**
     * Verifica se o Stripe está configurado
     */
    public function isConfigured()
    {
        return !empty($this->secretKey) && !empty($this->publishableKey);
    }

    /**
     * Verifica se o Stripe está habilitado
     */
    public function isEnabled()
    {
        $stripe = \App\Models\Stripe::first();
        return $stripe ? $stripe->isEnabled() : false;
    }

    /**
     * Retorna a chave pública para o frontend
     */
    public function getPublishableKey()
    {
        return $this->publishableKey;
    }
}
