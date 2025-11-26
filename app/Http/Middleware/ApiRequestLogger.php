<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiRequestLogger
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        
        // Log da requisição
        Log::channel('app_requests')->info('🔍 API REQUEST INICIADA', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $request->headers->all(),
            'query_params' => $request->query(),
            'body' => $request->all(),
            'timestamp' => now()->toISOString()
        ]);

        $response = $next($request);
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2); // em milissegundos
        
        // Log da resposta
        Log::channel('app_requests')->info('📤 API RESPONSE FINALIZADA', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'response_size' => strlen($response->getContent()),
            'timestamp' => now()->toISOString()
        ]);

        return $response;
    }
}
