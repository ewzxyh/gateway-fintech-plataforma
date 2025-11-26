<?php

namespace App\Http\Controllers\Api\Adquirentes;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Solicitacoes;
use App\Models\User;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    private $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Webhook do Stripe para processar eventos de pagamento
     */
    public function webhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');
            
            Log::info('🌐 WEBHOOK STRIPE RECEBIDO');
            Log::info('Timestamp: ' . now()->format('Y-m-d H:i:s'));
            Log::info('Headers: ' . json_encode($request->headers->all()));
            Log::info('Payload: ' . $payload);

            // Verificar assinatura do webhook
            $stripe = \App\Models\Stripe::first();
            if (!$stripe || !$stripe->webhook_secret) {
                Log::error('❌ Webhook secret não configurado');
                return response()->json(['status' => false, 'message' => 'Webhook secret not configured'], 400);
            }

            // Verificar assinatura (simplificado para desenvolvimento)
            // Em produção, implementar verificação completa da assinatura
            
            $event = json_decode($payload, true);
            $eventType = $event['type'] ?? 'unknown';
            
            Log::info("🎪 TIPO DE EVENTO STRIPE DETECTADO: $eventType");

            switch ($eventType) {
                case 'payment_intent.succeeded':
                case 'charge.succeeded':
                    return $this->processSuccessfulPayment($event);
                    
                case 'payment_intent.payment_failed':
                case 'charge.failed':
                    return $this->processFailedPayment($event);
                    
                case 'payment_intent.canceled':
                case 'charge.dispute.created':
                case 'dispute.created':
                case 'dispute.updated':
                case 'dispute.closed':
                    return $this->processDisputedPayment($event);
                    
                case 'charge.refunded':
                case 'refund.created':
                case 'refund.updated':
                    return $this->processRefund($event);
                    
                default:
                    Log::info("Webhook Stripe: Evento não processado - Tipo: $eventType");
                    return response()->json(['status' => true, 'message' => 'Evento não processado']);
            }

        } catch (\Exception $e) {
            Log::error('Webhook Stripe - Erro: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Processa pagamento bem-sucedido
     */
    private function processSuccessfulPayment($event)
    {
        try {
            $data = $event['data']['object'];
            $transactionId = $data['id'];
            $amount = $data['amount'] / 100; // Converter de centavos
            $currency = $data['currency'];
            
            Log::info('💳 WEBHOOK STRIPE - PAGAMENTO APROVADO');
            Log::info("   Transaction ID: $transactionId");
            Log::info("   Amount: $amount $currency");
            Log::info("   Status: " . ($data['status'] ?? 'unknown'));

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

            // Verificar se já foi processado
            if ($order->status === 'pago') {
                Log::info('✅ Pagamento já foi processado anteriormente');
                return response()->json(['status' => true, 'message' => 'Pagamento já processado']);
            }

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
                'externalreference' => 'STRIPE_' . $transactionId,
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
                'adquirente_ref' => 'Stripe',
                'taxa_cash_in' => $taxa_cash_in,
                'taxa_pix_cash_in_adquirente' => 0,
                'taxa_pix_cash_in_valor_fixo' => 0,
                'client_telefone' => $order->telefone,
                'executor_ordem' => 'Stripe Card',
                'descricao_transacao' => 'Pagamento com Cartão - ' . ($checkout->produto_name ?? 'Produto'),
            ]);

            // Creditar o saldo do usuário
            \App\Helpers\Helper::incrementAmount($user, $deposito_liquido, 'saldo');
            \App\Helpers\Helper::calculaSaldoLiquido($user->user_id);

            Log::info('✅ SALDO CREDITADO COM SUCESSO:');
            Log::info("   Valor Líquido: R$ " . number_format($deposito_liquido, 2, ',', '.'));
            Log::info("   Taxa: R$ " . number_format($taxa_cash_in, 2, ',', '.'));

            // Atualizar status do pedido
            $order->update(['status' => 'pago']);

            Log::info("✅ STATUS ATUALIZADO: pago");

            // Disparar webhook se configurado
            if (!is_null($user->webhook_url) && in_array('gerado', (array) $user->webhook_endpoint)) {
                Http::withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                    ->post($user->webhook_url, [
                        'nome' => $order->name,
                        'cpf' => preg_replace('/\D/', '', $order->cpf),
                        'telefone' => preg_replace('/\D/', '', $order->telefone),
                        'email' => $order->email,
                        'status' => 'pago',
                        'transaction_id' => $transactionId
                    ]);
            }

            return response()->json(['status' => true, 'message' => 'Webhook processado com sucesso']);

        } catch (\Exception $e) {
            Log::error('Webhook Stripe - Erro no processamento: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Processa pagamento falhado
     */
    private function processFailedPayment($event)
    {
        try {
            $data = $event['data']['object'];
            $transactionId = $data['id'];
            
            Log::info('❌ WEBHOOK STRIPE - PAGAMENTO FALHADO');
            Log::info("   Transaction ID: $transactionId");
            Log::info("   Failure Code: " . ($data['failure_code'] ?? 'unknown'));
            Log::info("   Failure Message: " . ($data['failure_message'] ?? 'unknown'));

            // Buscar o pedido pelo ID da transação
            $order = \App\Models\CheckoutOrders::where('idTransaction', $transactionId)->first();

            if ($order) {
                $order->update(['status' => 'cancelado']);
                Log::info("✅ Status do pedido atualizado para: cancelado");
            }

            return response()->json(['status' => true, 'message' => 'Pagamento falhado processado']);

        } catch (\Exception $e) {
            Log::error('Webhook Stripe - Erro no processamento de falha: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Processa pagamento disputado/cancelado/chargeback
     */
    private function processDisputedPayment($event)
    {
        try {
            $data = $event['data']['object'];
            $eventType = $event['type'];
            
            Log::info('⚠️ WEBHOOK STRIPE - DISPUTA/CHARGEBACK PROCESSADO');
            Log::info("   Event Type: $eventType");
            Log::info("   Object ID: " . ($data['id'] ?? 'unknown'));
            Log::info("   Status: " . ($data['status'] ?? 'unknown'));
            Log::info("   Reason: " . ($data['reason'] ?? 'unknown'));

            // Determinar o transaction ID baseado no tipo de evento
            $transactionId = null;
            
            if (in_array($eventType, ['charge.dispute.created', 'dispute.created', 'dispute.updated', 'dispute.closed'])) {
                $transactionId = $data['charge'] ?? null; // Charge ID dentro da disputa
            } else {
                $transactionId = $data['id'] ?? null; // Charge ID direto
            }

            if (!$transactionId) {
                Log::error('❌ Transaction ID não encontrado no evento de disputa');
                return response()->json(['status' => false, 'message' => 'Transaction ID não encontrado']);
            }

            // Buscar o pedido pelo ID da transação
            $order = \App\Models\CheckoutOrders::where('idTransaction', $transactionId)->first();

            if (!$order) {
                Log::error("❌ PEDIDO NÃO ENCONTRADO PARA DISPUTA:");
                Log::error("   Transaction ID: $transactionId");
                return response()->json(['status' => false, 'message' => 'Pedido não encontrado']);
            }

            Log::info("✅ PEDIDO ENCONTRADO PARA DISPUTA:");
            Log::info("   Order ID: " . $order->id);
            Log::info("   Status Atual: " . $order->status);
            Log::info("   Valor: R$ " . number_format($order->valor_total, 2, ',', '.'));

            // Determinar o novo status baseado no tipo de evento e dados
            $newStatus = $this->determineDisputeStatus($eventType, $data);

            // Atualizar status do pedido
            $order->update(['status' => $newStatus]);

            Log::info("✅ STATUS ATUALIZADO: $newStatus");

            // Buscar checkout e usuário para webhook
            $checkout = \App\Models\CheckoutBuild::find($order->checkout_id);
            $user = $checkout ? \App\Models\User::find($checkout->user_id) : null;

            if ($user) {
                // Disparar webhook se configurado
                if (!is_null($user->webhook_url) && in_array('gerado', (array) $user->webhook_endpoint)) {
                    Http::withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                        ->post($user->webhook_url, [
                            'nome' => $order->name,
                            'cpf' => preg_replace('/\D/', '', $order->cpf),
                            'telefone' => preg_replace('/\D/', '', $order->telefone),
                            'email' => $order->email,
                            'status' => $newStatus,
                            'transaction_id' => $transactionId,
                            'dispute_id' => $data['id'] ?? null,
                            'dispute_reason' => $data['reason'] ?? null,
                            'dispute_amount' => ($data['amount'] ?? 0) / 100,
                            'event_type' => $eventType
                        ]);
                    
                    Log::info("✅ Webhook de disputa enviado para: " . $user->webhook_url);
                }
            }

            return response()->json(['status' => true, 'message' => 'Disputa processada com sucesso']);

        } catch (\Exception $e) {
            Log::error('Webhook Stripe - Erro no processamento de disputa: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Determina o status baseado no tipo de evento e dados da disputa
     */
    private function determineDisputeStatus($eventType, $data)
    {
        $disputeStatus = $data['status'] ?? null;
        $reason = $data['reason'] ?? null;

        // Para eventos de criação de disputa
        if (in_array($eventType, ['charge.dispute.created', 'dispute.created'])) {
            // Verificar se é fraudulento baseado na razão
            if ($reason === 'fraudulent') {
                return 'fraudulento';
            }
            
            // Verificar se é chargeback baseado no status
            if (in_array($disputeStatus, ['needs_response', 'warning_needs_response'])) {
                return 'chargeback';
            }
            
            // Default para disputa geral
            return 'disputado';
        }

        // Para eventos de atualização/fechamento de disputa
        if (in_array($eventType, ['dispute.updated', 'dispute.closed'])) {
            switch ($disputeStatus) {
                case 'won':
                    return 'disputa_ganha';
                case 'lost':
                    return 'disputa_perdida';
                case 'charge_refunded':
                    return 'reembolsado';
                case 'needs_response':
                case 'warning_needs_response':
                    return 'chargeback';
                case 'under_review':
                case 'warning_under_review':
                    return 'disputado';
                default:
                    return 'disputado';
            }
        }

        // Para cancelamentos de PaymentIntent
        if ($eventType === 'payment_intent.canceled') {
            return 'cancelado';
        }

        // Default
        return 'disputado';
    }

    /**
     * Processa reembolso
     */
    private function processRefund($event)
    {
        try {
            $data = $event['data']['object'];
            $eventType = $event['type'];
            
            Log::info('💰 WEBHOOK STRIPE - REEMBOLSO PROCESSADO');
            Log::info("   Event Type: $eventType");
            Log::info("   Refund ID: " . ($data['id'] ?? 'unknown'));
            Log::info("   Charge ID: " . ($data['charge'] ?? 'unknown'));
            Log::info("   Amount: " . ($data['amount'] ?? 0) / 100);
            Log::info("   Status: " . ($data['status'] ?? 'unknown'));
            Log::info("   Reason: " . ($data['reason'] ?? 'unknown'));

            // Para charge.refunded, o transactionId é o charge ID
            // Para refund.created/updated, o transactionId é o charge ID dentro do objeto
            $transactionId = null;
            
            if ($eventType === 'charge.refunded') {
                $transactionId = $data['id']; // Charge ID
            } else {
                $transactionId = $data['charge'] ?? null; // Charge ID dentro do refund
            }

            if (!$transactionId) {
                Log::error('❌ Transaction ID não encontrado no evento de reembolso');
                return response()->json(['status' => false, 'message' => 'Transaction ID não encontrado']);
            }

            // Buscar o pedido pelo ID da transação (charge ID)
            $order = \App\Models\CheckoutOrders::where('idTransaction', $transactionId)->first();

            if (!$order) {
                Log::error("❌ PEDIDO NÃO ENCONTRADO PARA REEMBOLSO:");
                Log::error("   Transaction ID: $transactionId");
                return response()->json(['status' => false, 'message' => 'Pedido não encontrado']);
            }

            Log::info("✅ PEDIDO ENCONTRADO PARA REEMBOLSO:");
            Log::info("   Order ID: " . $order->id);
            Log::info("   Status Atual: " . $order->status);
            Log::info("   Valor: R$ " . number_format($order->valor_total, 2, ',', '.'));

            // Atualizar status do pedido para reembolsado
            $order->update(['status' => 'reembolsado']);

            Log::info("✅ STATUS ATUALIZADO: reembolsado");

            // Buscar checkout e usuário para webhook
            $checkout = \App\Models\CheckoutBuild::find($order->checkout_id);
            $user = $checkout ? \App\Models\User::find($checkout->user_id) : null;

            if ($user) {
                // Disparar webhook se configurado
                if (!is_null($user->webhook_url) && in_array('gerado', (array) $user->webhook_endpoint)) {
                    Http::withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                        ->post($user->webhook_url, [
                            'nome' => $order->name,
                            'cpf' => preg_replace('/\D/', '', $order->cpf),
                            'telefone' => preg_replace('/\D/', '', $order->telefone),
                            'email' => $order->email,
                            'status' => 'reembolsado',
                            'transaction_id' => $transactionId,
                            'refund_id' => $data['id'] ?? null,
                            'refund_amount' => ($data['amount'] ?? 0) / 100,
                            'refund_reason' => $data['reason'] ?? null
                        ]);
                    
                    Log::info("✅ Webhook de reembolso enviado para: " . $user->webhook_url);
                }
            }

            return response()->json(['status' => true, 'message' => 'Reembolso processado com sucesso']);

        } catch (\Exception $e) {
            Log::error('Webhook Stripe - Erro no processamento de reembolso: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }
}

