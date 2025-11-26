<?php

namespace App\Traits;

use App\Models\Solicitacoes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UtmfyTrait
{
    /**
     * @param 'pix'|'credit_card' | 'boleto' $method
     * @param 'waiting_payment' | 'paid' | 'refunded'
     * @param Solicitacoes $data
     * @param string $apiToken
     * @param string $ip
     * @param string $description
     */
    public static function gerarUTM($method, $status, $data, $apiToken, $ip, $description)
    {
        try {
            Log::info('=== UTMFY INTEGRATION START ===', [
                'order_id' => $data['idTransaction'],
                'method' => $method,
                'status' => $status,
                'amount' => $data['amount'],
                'client_email' => $data['client_email'],
                'ip' => $ip
            ]);

            $dataAtual = Carbon::now()->format('Y-m-d H:i:s');
            
            $payload = [
                'orderId' => $data['idTransaction'],
                'platform' => env('APP_NAME'),
                'paymentMethod' => $method,
                'status' => $status,
                'createdAt' => $dataAtual,
                'approvedDate' => null,
                'refundedAt' => null,
                'customer' => [
                    'name' => $data['client_name'],
                    'email' => $data['client_email'],
                    'phone' => $data['client_telefone'],
                    'document' => $data['client_document'],
                    'country' => 'BR',
                    'ip' => $ip,
                ],
                'products' => [
                    [
                        'id' => uniqid(),
                        'name' =>  $description,
                        'planId' => null,
                        'planName' => null,
                        'quantity' => 1,
                        'priceInCents' => (int) $data['amount'] * 100,
                    ],
                ],
                'trackingParameters' => [
                    'src' => null,
                    'sck' => null,
                    'utm_source' => null,
                    'utm_campaign' => null,
                    'utm_medium' => null,
                    'utm_content' => null,
                    'utm_term' => null,
                ],
                'commission' => [
                    'totalPriceInCents' => (int) $data['amount'] * 100,
                    'gatewayFeeInCents' => 0,
                    'userCommissionInCents' => 0,
                ],
                'isTest' => false,
            ];

            Log::info('UTMFY - Payload preparado:', $payload);

            $response = Http::timeout(30)
                ->withHeaders([
                    'x-api-token' => $apiToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post('https://api.utmify.com.br/api-credentials/orders', $payload);

            Log::info('UTMFY - Response Status:', [
                'status_code' => $response->status(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                Log::info('UTMFY - Sucesso na integração:', [
                    'order_id' => $data['idTransaction'],
                    'response_body' => $response->body()
                ]);
            } else {
                Log::error('UTMFY - Erro na integração:', [
                    'order_id' => $data['idTransaction'],
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                    'payload' => $payload
                ]);
            }

            Log::info('=== UTMFY INTEGRATION END ===');

        } catch (\Exception $e) {
            Log::error('UTMFY - Exceção capturada:', [
                'order_id' => $data['idTransaction'] ?? 'N/A',
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
