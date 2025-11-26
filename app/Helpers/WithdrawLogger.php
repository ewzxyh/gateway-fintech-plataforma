<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class WithdrawLogger
{
    /**
     * Log do início de um saque
     */
    public static function logWithdrawStarted($userId, $amount, $pixKey, $additionalData = [])
    {
        Log::info('[SAQUE] Saque iniciado', array_merge([
            'user_id' => $userId,
            'amount' => $amount,
            'pix_key' => substr($pixKey, 0, 10) . '...', // Mascarar chave por segurança
            'timestamp' => now()->toDateTimeString()
        ], $additionalData));
    }

    /**
     * Log de sucesso do saque
     */
    public static function logWithdrawSuccess($userId, $amount, $transactionId, $additionalData = [])
    {
        Log::info('[SAQUE] Saque realizado com sucesso', array_merge([
            'user_id' => $userId,
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'timestamp' => now()->toDateTimeString()
        ], $additionalData));
    }

    /**
     * Log de erro no saque
     */
    public static function logWithdrawError($userId, $amount, $error, $additionalData = [])
    {
        Log::error('[SAQUE] Erro no saque', array_merge([
            'user_id' => $userId,
            'amount' => $amount,
            'error' => $error,
            'timestamp' => now()->toDateTimeString()
        ], $additionalData));
    }

    /**
     * Log de validação de PIN
     */
    public static function logPinValidation($userId, $success, $additionalData = [])
    {
        Log::info('[SAQUE] Validação de PIN', array_merge([
            'user_id' => $userId,
            'pin_valid' => $success,
            'timestamp' => now()->toDateTimeString()
        ], $additionalData));
    }

    /**
     * Log de verificação de saldo
     */
    public static function logBalanceCheck($userId, $requestedAmount, $currentBalance, $sufficient, $additionalData = [])
    {
        Log::info('[SAQUE] Verificação de saldo', array_merge([
            'user_id' => $userId,
            'requested_amount' => $requestedAmount,
            'current_balance' => $currentBalance,
            'sufficient_balance' => $sufficient,
            'timestamp' => now()->toDateTimeString()
        ], $additionalData));
    }
}


