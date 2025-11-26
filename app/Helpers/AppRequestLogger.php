<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class AppRequestLogger
{
    /**
     * Log de requisição específica do app
     */
    public static function logAppRequest($method, $endpoint, $userId, $data = [], $response = null)
    {
        Log::channel('app_requests')->info('📱 APP REQUEST', [
            'method' => $method,
            'endpoint' => $endpoint,
            'user_id' => $userId,
            'request_data' => $data,
            'response_status' => $response ? $response->getStatusCode() : null,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log de erro na requisição do app
     */
    public static function logAppRequestError($method, $endpoint, $userId, $error, $data = [])
    {
        Log::channel('app_requests')->error('❌ APP REQUEST ERROR', [
            'method' => $method,
            'endpoint' => $endpoint,
            'user_id' => $userId,
            'error' => $error,
            'request_data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }
}
