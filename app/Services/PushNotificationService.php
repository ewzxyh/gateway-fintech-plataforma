<?php

namespace App\Services;

use App\Models\PushToken;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PushNotificationService
{
    private $expoApiUrl = 'https://exp.host/--/api/v2/push/send';

    /**
     * Registrar token de push de um usuário
     */
    public function registerToken($userId, $token, $platform = 'expo', $deviceId = null)
    {
        try {
            // Verificar se o token já existe
            $existingToken = PushToken::where('token', $token)->first();

            if ($existingToken) {
                // Atualizar dados do token existente
                $existingToken->update([
                    'user_id' => $userId,
                    'platform' => $platform,
                    'device_id' => $deviceId,
                    'is_active' => true,
                    'last_used_at' => now()
                ]);
                return $existingToken;
            }

            // Criar novo token
            return PushToken::create([
                'user_id' => $userId,
                'token' => $token,
                'platform' => $platform,
                'device_id' => $deviceId,
                'is_active' => true,
                'last_used_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao registrar token de push', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Enviar notificação push para um usuário
     */
    public function sendToUser($userId, $title, $body, $data = [], $type = 'transaction')
    {
        try {
            Log::info('[PUSH] sendToUser chamado', [
                'user_id' => $userId,
                'title' => $title,
                'body' => $body,
                'type' => $type
            ]);

            // Buscar tokens ativos do usuário
            $tokens = PushToken::getActiveTokensForUser($userId);

            Log::info('[PUSH] Tokens encontrados', [
                'user_id' => $userId,
                'tokens_count' => $tokens->count(),
                'tokens' => $tokens->pluck('token')->map(function ($token) {
                    return substr($token, 0, 30) . '...';
                })->toArray()
            ]);

            if ($tokens->isEmpty()) {
                Log::warning('[PUSH] Usuário não possui tokens de push ativos', ['user_id' => $userId]);
                return false;
            }

            // Criar registro de notificação
            $notification = Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'push_sent' => false,
                'local_sent' => false
            ]);

            // Preparar dados da notificação
            $notificationData = [
                'type' => $type,
                'notification_id' => $notification->id,
                'timestamp' => now()->toISOString(),
                ...$data
            ];

            // Enviar para cada token
            $successCount = 0;
            foreach ($tokens as $token) {
                if ($this->sendToToken($token->token, $title, $body, $notificationData)) {
                    $successCount++;
                    $token->markAsUsed();
                }
            }

            // Atualizar status da notificação
            $notification->update([
                'push_sent' => $successCount > 0,
                'sent_at' => $successCount > 0 ? now() : null
            ]);

            Log::info('Notificação push enviada', [
                'user_id' => $userId,
                'tokens_count' => $tokens->count(),
                'success_count' => $successCount,
                'notification_id' => $notification->id
            ]);

            return $successCount > 0;
        } catch (\Exception $e) {
            Log::error('Erro ao enviar notificação push', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Enviar notificação para um token específico
     */
    private function sendToToken($token, $title, $body, $data = [])
    {
        try {
            $payload = [
                'to' => $token,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'sound' => 'default',
                'badge' => 1,
                'channelId' => 'gateway-notifications',
                'priority' => 'high'
            ];

            Log::info('[PUSH] Enviando notificação para Expo', [
                'token' => substr($token, 0, 30) . '...',
                'title' => $title,
                'body' => $body
            ]);

            $response = Http::timeout(10)->post($this->expoApiUrl, $payload);

            Log::info('[PUSH] Resposta da API Expo', [
                'token' => substr($token, 0, 30) . '...',
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Verificar se houve erro no envio (formato: {data: {status: "error"}})
                if (isset($responseData['data']['status']) && $responseData['data']['status'] === 'error') {
                    Log::error('[PUSH] ❌ Erro retornado pelo Expo', [
                        'token' => substr($token, 0, 20) . '...',
                        'error' => $responseData['data']['message'] ?? 'Erro desconhecido',
                        'details' => $responseData['data']['details'] ?? [],
                        'response' => $responseData
                    ]);
                    return false;
                }

                // Verificar formato array (formato: {data: [{status: "error"}]})
                if (isset($responseData['data'][0]['status']) && $responseData['data'][0]['status'] === 'error') {
                    Log::error('[PUSH] ❌ Erro retornado pelo Expo (array)', [
                        'token' => substr($token, 0, 20) . '...',
                        'error' => $responseData['data'][0]['message'] ?? 'Erro desconhecido',
                        'response' => $responseData
                    ]);
                    return false;
                }

                Log::info('[PUSH] ✅ Notificação enviada com sucesso!', [
                    'token' => substr($token, 0, 30) . '...'
                ]);
                return true;
            }

            Log::warning('Falha no envio de notificação', [
                'token' => substr($token, 0, 20) . '...',
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exceção ao enviar notificação', [
                'token' => substr($token, 0, 20) . '...',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Enviar notificação de depósito
     */
    public function sendDepositNotification($userId, $amount, $transactionId = null)
    {
        Log::info('[PUSH] Iniciando envio de notificação de depósito', [
            'user_id' => $userId,
            'amount' => $amount,
            'transaction_id' => $transactionId
        ]);

        $formattedAmount = 'R$ ' . number_format($amount, 2, ',', '.');

        $result = $this->sendToUser(
            $userId,
            'Depósito Realizado ✅',
            "Depósito de {$formattedAmount} foi solicitado com sucesso",
            [
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'action' => 'view_transaction'
            ],
            'deposit'
        );

        Log::info('[PUSH] Resultado do envio de notificação de depósito', [
            'user_id' => $userId,
            'success' => $result
        ]);

        return $result;
    }

    /**
     * Enviar notificação de saque
     */
    public function sendWithdrawNotification($userId, $amount, $transactionId = null)
    {
        $formattedAmount = 'R$ ' . number_format($amount, 2, ',', '.');

        return $this->sendToUser(
            $userId,
            'Saque Realizado ✅',
            "Saque de {$formattedAmount} foi solicitado com sucesso",
            [
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'action' => 'view_transaction'
            ],
            'withdraw'
        );
    }

    /**
     * Enviar notificação de comissão
     */
    public function sendCommissionNotification($userId, $amount, $description = '')
    {
        $formattedAmount = 'R$ ' . number_format($amount, 2, ',', '.');

        return $this->sendToUser(
            $userId,
            'Nova Comissão Confirmada 💰',
            "Você recebeu o valor de {$formattedAmount}",
            [
                'amount' => $amount,
                'description' => $description,
                'action' => 'view_commission'
            ],
            'commission'
        );
    }

    /**
     * Enviar notificação de transferência
     */
    public function sendTransferNotification($userId, $amount, $type = 'received', $fromUser = null)
    {
        $formattedAmount = 'R$ ' . number_format($amount, 2, ',', '.');

        if ($type === 'received') {
            $title = 'Transferência recebida';
            $body = "Você recebeu {$formattedAmount}";
        } else {
            $title = 'Transferência enviada';
            $body = "Transferência de {$formattedAmount} realizada";
        }

        return $this->sendToUser(
            $userId,
            $title,
            $body,
            [
                'amount' => $amount,
                'type' => $type,
                'from_user' => $fromUser,
                'action' => 'view_transfer'
            ],
            'transfer'
        );
    }

    /**
     * Desativar token
     */
    public function deactivateToken($token)
    {
        $pushToken = PushToken::findByToken($token);
        if ($pushToken) {
            $pushToken->deactivate();
            return true;
        }
        return false;
    }

    /**
     * Obter estatísticas de notificações
     */
    public function getNotificationStats($userId = null)
    {
        $query = Notification::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'total' => $query->count(),
            'sent' => $query->where('push_sent', true)->count(),
            'unread' => $query->whereNull('read_at')->count(),
            'today' => $query->whereDate('created_at', today())->count()
        ];
    }
}
