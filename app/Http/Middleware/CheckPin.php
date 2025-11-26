<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\PinManagementTrait;

class CheckPin
{
    use PinManagementTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar se o usuário está autenticado via token (adicionado pelo CheckTokenAndSecret)
        $user = $request->input('user_auth') ?: $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // Verificar se o usuário tem PIN ativo
        if (!$this->hasActivePin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Para realizar saques via web, você precisa cadastrar um PIN de segurança primeiro. Acesse seu perfil para configurar o PIN.',
                'requires_pin_setup' => true,
                'pin_status' => 'not_configured'
            ], 403);
        }

        // Verificar se o PIN foi fornecido
        $pin = $request->input('pin');
        if (!$pin) {
            return response()->json([
                'success' => false,
                'message' => 'PIN é obrigatório para esta operação',
                'requires_pin' => true
            ], 400);
        }

        // Verificar se o PIN está correto
        if (!$this->verifyPinInternal($user, $pin)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN incorreto'
            ], 403);
        }

        return $next($request);
    }
}
