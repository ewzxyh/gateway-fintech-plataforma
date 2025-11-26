<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeStatusController extends Controller
{
    /**
     * Verifica o status de um PaymentIntent do Stripe
     */
    public function checkStatus(Request $request)
    {
        try {
            $paymentIntentId = $request->input('payment_intent_id');
            
            if (!$paymentIntentId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment Intent ID é obrigatório'
                ], 400);
            }

            Log::info('🔍 Verificando status do PaymentIntent:', ['id' => $paymentIntentId]);

            $stripeService = new StripeService();
            $result = $stripeService->getPaymentIntentStatus($paymentIntentId);

            if ($result['success']) {
                $status = $result['status'];
                
                Log::info('📊 Status do PaymentIntent:', [
                    'id' => $paymentIntentId,
                    'status' => $status
                ]);

                // Mapear status do Stripe para status do sistema
                $systemStatus = $this->mapStripeStatus($status);
                
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'payment_intent_id' => $paymentIntentId,
                        'stripe_status' => $status,
                        'system_status' => $systemStatus,
                        'is_final' => $this->isFinalStatus($status),
                        'message' => $this->getStatusMessage($systemStatus)
                    ]
                ]);
            } else {
                Log::error('❌ Erro ao verificar status:', $result);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erro ao verificar status do pagamento',
                    'error' => $result['error'] ?? 'Erro desconhecido'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('💥 Erro no checkStatus:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Mapeia o status do Stripe para o status do sistema
     */
    private function mapStripeStatus($stripeStatus)
    {
        switch ($stripeStatus) {
            case 'succeeded':
                return 'aprovado';
            case 'requires_payment_method':
            case 'requires_confirmation':
            case 'requires_action':
                return 'pendente';
            case 'canceled':
                return 'cancelado';
            case 'processing':
                return 'processando';
            default:
                return 'pendente';
        }
    }

    /**
     * Verifica se o status é final (não precisa mais polling)
     */
    private function isFinalStatus($stripeStatus)
    {
        return in_array($stripeStatus, ['succeeded', 'canceled']);
    }

    /**
     * Retorna mensagem amigável baseada no status
     */
    private function getStatusMessage($systemStatus)
    {
        switch ($systemStatus) {
            case 'aprovado':
                return 'Pagamento aprovado com sucesso!';
            case 'cancelado':
                return 'Pagamento foi cancelado.';
            case 'processando':
                return 'Pagamento está sendo processado...';
            case 'pendente':
                return 'Aguardando confirmação do pagamento...';
            default:
                return 'Verificando status do pagamento...';
        }
    }
}
