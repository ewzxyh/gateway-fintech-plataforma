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

class PagarMeController extends Controller
{
    private $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Callback para depósitos (Cash-in) - Pagar.me
     */
    public function callbackDeposit(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('=== WEBHOOK PAGAR.ME DEPOSIT RECEBIDO ===');
            Log::info('Timestamp: ' . now()->format('Y-m-d H:i:s'));
            Log::info('IP: ' . $request->ip());
            Log::info('User-Agent: ' . $request->userAgent());
            Log::info('Headers: ' . json_encode($request->headers->all()));
            Log::info('Dados recebidos: ' . json_encode($data, JSON_PRETTY_PRINT));
            Log::info('=====================================');

            // Verifica se é um evento de pagamento
            $status = null;
            $transactionId = null;
            $typeTransaction = 'PIX';
            
            // Formato Pagar.me - verificar estrutura do webhook
            if (isset($data['type']) && isset($data['data'])) {
                $eventType = $data['type'];
                $transactionData = $data['data'];
                
                Log::info("🎪 TIPO DE EVENTO PAGAR.ME: $eventType");
                
                // Verificar se é evento de ordem paga
                if ($eventType === 'order.paid' && isset($transactionData['charges'])) {
                    $charge = $transactionData['charges'][0] ?? null;
                    if ($charge && isset($charge['last_transaction'])) {
                        $lastTransaction = $charge['last_transaction'];
                        $status = $lastTransaction['status'] ?? null;
                        $transactionId = $transactionData['id'] ?? null;
                        
                        Log::info("💰 PAGAR.ME DEPOSIT - Status: $status, TransactionId: $transactionId");
                    }
                }
            }

            if (!$status || !$transactionId) {
                Log::warning('⚠️ Webhook Pagar.me Deposit: Status ou TransactionId não encontrado');
                return response()->json(['status' => false, 'message' => 'Dados incompletos'], 400);
            }

            // Buscar solicitação de depósito
            $cashin = Solicitacoes::where('idTransaction', $transactionId)->first();
            
            if (!$cashin) {
                Log::warning('⚠️ Webhook Pagar.me Deposit: Solicitação não encontrada', ['transaction_id' => $transactionId]);
                return response()->json(['status' => false, 'message' => 'Solicitação não encontrada'], 404);
            }

            Log::info('📦 Solicitação encontrada:', [
                'cashin_id' => $cashin->id,
                'user_id' => $cashin->user_id,
                'current_status' => $cashin->status,
                'amount' => $cashin->amount,
                'new_status' => $status
            ]);

            // Processar status do pagamento
            Log::info("🔄 PROCESSANDO STATUS: $status");
            switch ($status) {
                case 'paid':
                case 'approved':
                case 'completed':
                    if ($cashin->status !== "PAID_OUT") {
                        $cashin->update(['status' => 'PAID_OUT', 'updated_at' => Carbon::now()]);
                        
                        // Creditar saldo do usuário
                        $user = User::where('user_id', $cashin->user_id)->first();
                        if ($user) {
                            Helper::incrementAmount($user, $cashin->deposito_liquido, 'saldo');
                            Helper::calculaSaldoLiquido($user->user_id);
                            Log::info("✅ Webhook Pagar.me Deposit: Saldo creditado - User: {$user->username}, Valor: {$cashin->deposito_liquido}");
                        }
                        
                        Log::info("✅ Webhook Pagar.me Deposit: Depósito processado - TransactionId: $transactionId");
                    } else {
                        Log::info("ℹ️ Webhook Pagar.me Deposit: Depósito já estava processado - TransactionId: $transactionId");
                    }
                    break;

                case 'failed':
                case 'rejected':
                case 'cancelled':
                    if ($cashin->status !== "FAILED") {
                        $cashin->update(['status' => 'FAILED', 'updated_at' => Carbon::now()]);
                        Log::info("❌ Webhook Pagar.me Deposit: Depósito falhado - TransactionId: $transactionId");
                    } else {
                        Log::info("ℹ️ Webhook Pagar.me Deposit: Depósito já estava falhado - TransactionId: $transactionId");
                    }
                    break;

                case 'pending':
                case 'processing':
                    Log::info("⏳ Webhook Pagar.me Deposit: Depósito pendente - TransactionId: $transactionId");
                    break;

                default:
                    Log::info("❓ Webhook Pagar.me Deposit: Status não mapeado - Status: $status, TransactionId: $transactionId");
                    break;
            }

            return response()->json(['status' => true, 'message' => 'Webhook processado com sucesso']);

        } catch (\Exception $e) {
            Log::error('Webhook Pagar.me Deposit - Erro: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Callback para saques (Cash-out) - Pagar.me
     */
    public function callbackWithdraw(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('=== WEBHOOK PAGAR.ME WITHDRAW RECEBIDO ===');
            Log::info('Timestamp: ' . now()->format('Y-m-d H:i:s'));
            Log::info('IP: ' . $request->ip());
            Log::info('User-Agent: ' . $request->userAgent());
            Log::info('Headers: ' . json_encode($request->headers->all()));
            Log::info('Dados recebidos: ' . json_encode($data, JSON_PRETTY_PRINT));
            Log::info('=====================================');

            // Verifica se é um evento de saque
            $status = null;
            $transactionId = null;
            
            // Formato Pagar.me - verificar estrutura do webhook
            if (isset($data['type']) && isset($data['data'])) {
                $eventType = $data['type'];
                $transactionData = $data['data'];
                
                Log::info("🎪 TIPO DE EVENTO PAGAR.ME WITHDRAW: $eventType");
                
                // Verificar se é evento de transação processada
                if ($eventType === 'transaction.paid' || $eventType === 'transaction.failed') {
                    $status = $transactionData['status'] ?? null;
                    $transactionId = $transactionData['id'] ?? null;
                    
                    Log::info("💰 PAGAR.ME WITHDRAW - Status: $status, TransactionId: $transactionId");
                }
            }

            if (!$status || !$transactionId) {
                Log::warning('⚠️ Webhook Pagar.me Withdraw: Status ou TransactionId não encontrado');
                return response()->json(['status' => false, 'message' => 'Dados incompletos'], 400);
            }

            // Buscar solicitação de saque
            $cashout = SolicitacoesCashOut::where('idTransaction', $transactionId)->first();
            
            if (!$cashout) {
                Log::warning('⚠️ Webhook Pagar.me Withdraw: Solicitação não encontrada', ['transaction_id' => $transactionId]);
                return response()->json(['status' => false, 'message' => 'Solicitação não encontrada'], 404);
            }

            Log::info('📦 Solicitação de saque encontrada:', [
                'cashout_id' => $cashout->id,
                'user_id' => $cashout->user_id,
                'current_status' => $cashout->status,
                'amount' => $cashout->amount,
                'new_status' => $status
            ]);

            // Processar status do saque
            Log::info("🔄 PROCESSANDO STATUS: $status");
            switch ($status) {
                case 'paid':
                case 'approved':
                case 'completed':
                    if ($cashout->status !== "COMPLETED") {
                        $cashout->update(['status' => 'COMPLETED', 'updated_at' => Carbon::now()]);
                        Log::info("✅ Webhook Pagar.me Withdraw: Saque completado - TransactionId: $transactionId");
                    } else {
                        Log::info("ℹ️ Webhook Pagar.me Withdraw: Saque já estava completado - TransactionId: $transactionId");
                    }
                    break;

                case 'failed':
                case 'rejected':
                case 'cancelled':
                    if ($cashout->status !== "FAILED") {
                        $cashout->update(['status' => 'FAILED', 'updated_at' => Carbon::now()]);
                        
                        // Devolver saldo ao usuário
                        $user = User::where('user_id', $cashout->user_id)->first();
                        if ($user) {
                            Helper::incrementAmount($user, $cashout->cash_out_liquido, 'saldo');
                            Helper::calculaSaldoLiquido($user->user_id);
                            Log::info("✅ Webhook Pagar.me Withdraw: Saldo devolvido - User: {$user->username}, Valor: {$cashout->cash_out_liquido}");
                        }
                    } else {
                        Log::info("ℹ️ Webhook Pagar.me Withdraw: Saque já estava falhado - TransactionId: $transactionId");
                    }
                    break;

                case 'pending':
                case 'processing':
                    Log::info("⏳ Webhook Pagar.me Withdraw: Saque pendente - TransactionId: $transactionId");
                    break;

                default:
                    Log::info("❓ Webhook Pagar.me Withdraw: Status não mapeado - Status: $status, TransactionId: $transactionId");
                    break;
            }

            return response()->json(['status' => true, 'message' => 'Webhook processado com sucesso']);

        } catch (\Exception $e) {
            Log::error('Webhook Pagar.me Withdraw - Erro: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Webhook unificado para Pagar.me (compatibilidade)
     */
    public function callbackUnified(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('🌐 WEBHOOK UNIFICADO PAGAR.ME RECEBIDO');
            Log::info('Timestamp: ' . now()->format('Y-m-d H:i:s'));
            Log::info('Dados: ' . json_encode($data, JSON_PRETTY_PRINT));

            // Determinar o tipo de evento
            $eventType = $data['type'] ?? 'unknown';
            Log::info("🎪 TIPO DE EVENTO DETECTADO: $eventType");
            
            switch ($eventType) {
                case 'order.paid':
                    return $this->callbackDeposit($request);
                    
                case 'transaction.paid':
                case 'transaction.failed':
                    return $this->callbackWithdraw($request);
                    
                default:
                    Log::info("Webhook Pagar.me: Evento não processado - Tipo: $eventType");
                    return response()->json(['status' => true, 'message' => 'Evento não processado']);
            }

        } catch (\Exception $e) {
            Log::error('Webhook Pagar.me - Erro: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }

    /**
     * Webhook geral para Pagar.me (compatibilidade com sistema antigo)
     */
    public function webhook(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('🌐 WEBHOOK GERAL PAGAR.ME RECEBIDO');
            Log::info('Timestamp: ' . now()->format('Y-m-d H:i:s'));
            Log::info('Dados: ' . json_encode($data, JSON_PRETTY_PRINT));

            // Determinar o tipo de evento
            $eventType = $data['type'] ?? $data['event'] ?? 'unknown';
            Log::info("🎪 TIPO DE EVENTO DETECTADO: $eventType");
            
            switch ($eventType) {
                case 'order.paid':
                case 'payment.received':
                case 'deposit.completed':
                    return $this->callbackDeposit($request);
                    
                case 'transaction.paid':
                case 'transaction.failed':
                case 'payment.sent':
                case 'withdraw.completed':
                case 'withdraw.failed':
                    return $this->callbackWithdraw($request);
                    
                default:
                    Log::info("Webhook Pagar.me: Evento não processado - Tipo: $eventType");
                    return response()->json(['status' => true, 'message' => 'Evento não processado']);
            }

        } catch (\Exception $e) {
            Log::error('Webhook Pagar.me - Erro: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Erro interno'], 500);
        }
    }
}
