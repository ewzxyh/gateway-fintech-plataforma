<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class NotificationLogger
{
    /**
     * Log de notificação enviada
     */
    public static function logNotificationSent($userId, $title, $body, $data = [], $success = true)
    {
        Log::channel('notifications')->info('📱 NOTIFICAÇÃO ENVIADA', [
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'success' => $success,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log de erro ao enviar notificação
     */
    public static function logNotificationError($userId, $title, $body, $error, $data = [])
    {
        Log::channel('notifications')->error('❌ ERRO AO ENVIAR NOTIFICAÇÃO', [
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'error' => $error,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log de token de notificação registrado
     */
    public static function logTokenRegistered($userId, $token, $platform = 'unknown')
    {
        Log::channel('notifications')->info('🔑 TOKEN REGISTRADO', [
            'user_id' => $userId,
            'token' => substr($token, 0, 20) . '...',
            'platform' => $platform,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log de token de notificação desativado
     */
    public static function logTokenDeactivated($userId, $token)
    {
        Log::channel('notifications')->info('🚫 TOKEN DESATIVADO', [
            'user_id' => $userId,
            'token' => substr($token, 0, 20) . '...',
            'timestamp' => now()->toISOString()
        ]);
    }
}

class WithdrawLogger
{
    /**
     * Log de início de saque
     */
    public static function logWithdrawStarted($userId, $amount, $pixKey, $data = [])
    {
        Log::channel('withdraw_flow')->info('💰 SAQUE INICIADO', [
            'user_id' => $userId,
            'amount' => $amount,
            'pix_key' => substr($pixKey, 0, 10) . '...',
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log de saque processado
     */
    public static function logWithdrawProcessed($userId, $amount, $status, $transactionId, $data = [])
    {
        Log::channel('withdraw_flow')->info('✅ SAQUE PROCESSADO', [
            'user_id' => $userId,
            'amount' => $amount,
            'status' => $status,
            'transaction_id' => $transactionId,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log de erro no saque
     */
    public static function logWithdrawError($userId, $amount, $error, $data = [])
    {
        Log::channel('withdraw_flow')->error('❌ ERRO NO SAQUE', [
            'user_id' => $userId,
            'amount' => $amount,
            'error' => $error,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log de notificação de saque enviada
     */
    public static function logWithdrawNotification($userId, $amount, $status, $notificationSent = true)
    {
        Log::channel('withdraw_flow')->info('📱 NOTIFICAÇÃO DE SAQUE', [
            'user_id' => $userId,
            'amount' => $amount,
            'status' => $status,
            'notification_sent' => $notificationSent,
            'timestamp' => now()->toISOString()
        ]);
    }
}

