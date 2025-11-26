<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\IPManagementTrait;

class CheckAllowedIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar se o usuário está autenticado via token (definido pelo CheckTokenAndSecret)
        $user = $request->input('user_auth') ?: $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // Verificar se é uma requisição do mobile app
        $isMobileApp = $this->isMobileAppRequest($request);

        // Debug: Log dos dados da requisição
        Log::info('[IP_CHECK] Debug da requisição no middleware', [
            'user_id' => $user->user_id,
            'all_input' => $request->all(),
            'baasPostbackUrl' => $request->input('baasPostbackUrl'),
            'method' => $request->method(),
            'url' => $request->url(),
            'is_mobile_app' => $isMobileApp,
            'user_agent' => $request->userAgent()
        ]);

        // Se for mobile app, pular verificação de IP
        if ($isMobileApp) {
            Log::info('[IP_CHECK] Requisição do mobile app - pulando verificação de IP', [
                'user_id' => $user->user_id,
                'user_agent' => $request->userAgent()
            ]);
            return $next($request);
        }

        // Determinar se é saque via interface web ou API
        $isInterfaceWeb = $request->input('baasPostbackUrl') === 'web';
        $realClientIP = $this->getClientIP($request);

        if ($isInterfaceWeb) {
            // Para requisições da interface web, verificar se realmente veio do navegador
            $isRealWebRequest = $this->isRealWebRequest($request);

            if (!$isRealWebRequest) {
                Log::warning('[IP_CHECK] Tentativa de burlar validação usando baasPostbackUrl=web via API externa', [
                    'user_id' => $user->user_id,
                    'real_client_ip' => $realClientIP,
                    'user_agent' => $request->userAgent(),
                    'referer' => $request->header('referer'),
                    'origin' => $request->header('origin'),
                    'baasPostbackUrl' => $request->input('baasPostbackUrl')
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado: requisição não autorizada',
                    'client_ip' => $realClientIP
                ], 403);
            }

            // Para requisições da interface web, usar IP do servidor (mesmo comportamento do getIPForAcquirer)
            $clientIP = IPManagementTrait::getServerIPFromConfig();
            Log::info('[IP_CHECK] Saque via interface web real - usando IP do servidor', [
                'user_id' => $user->user_id,
                'real_client_ip' => $realClientIP,
                'server_ip' => $clientIP,
                'baasPostbackUrl' => $request->input('baasPostbackUrl')
            ]);
        } else {
            // Para requisições de API direta, usar IP real do cliente
            $clientIP = $realClientIP;
            Log::info('[IP_CHECK] Saque via API direta - usando IP do cliente', [
                'user_id' => $user->user_id,
                'client_ip' => $clientIP,
                'baasPostbackUrl' => $request->input('baasPostbackUrl')
            ]);
        }

        // Usar IPManagementTrait para verificação de IPs (inclui IPs globais)
        if (!IPManagementTrait::isIPAllowed($clientIP, $user)) {
            Log::warning('[IP_CHECK] IP não autorizado para saque', [
                'user_id' => $user->user_id,
                'client_ip' => $clientIP,
                'is_interface_web' => $isInterfaceWeb
            ]);

            return response()->json([
                'success' => false,
                'message' => 'IP não autorizado para realizar saques',
                'client_ip' => $clientIP
            ], 403);
        }

        Log::info('[IP_CHECK] IP autorizado para saque', [
            'user_id' => $user->user_id,
            'client_ip' => $clientIP,
            'is_interface_web' => $isInterfaceWeb
        ]);

        return $next($request);
    }

    /**
     * Obtém o IP real do cliente (usando IPManagementTrait)
     */
    private function getClientIP(Request $request): string
    {
        return IPManagementTrait::getClientIP();
    }

    /**
     * Verifica se a requisição vem do mobile app
     */
    private function isMobileAppRequest(Request $request): bool
    {
        $userAgent = $request->userAgent();

        // Verificar User-Agent do React Native/Expo
        if (
            strpos($userAgent, 'Expo') !== false ||
            strpos($userAgent, 'ReactNative') !== false ||
            strpos($userAgent, 'GatewayApp') !== false
        ) {
            return true;
        }

        // Verificar se tem header específico do mobile app
        if (
            $request->header('X-Mobile-App') === 'GatewayApp' ||
            $request->header('X-App-Type') === 'mobile'
        ) {
            return true;
        }

        // Verificar se tem parâmetro específico do mobile app
        if (
            $request->input('mobile_app') === 'true' ||
            $request->input('app_type') === 'mobile'
        ) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se a requisição realmente veio do navegador web
     */
    private function isRealWebRequest(Request $request): bool
    {
        // Verificar se tem cookies de sessão válidos
        $hasValidSession = $request->hasCookie('closedpay_session') &&
            $request->hasCookie('XSRF-TOKEN');

        // Verificar se o referer é do próprio site
        $referer = $request->header('referer');
        $currentDomain = $request->getHost();
        $isValidReferer = $referer && str_contains($referer, $currentDomain);

        // Verificar se o origin é do próprio site
        $origin = $request->header('origin');
        $isValidOrigin = $origin && str_contains($origin, $currentDomain);

        // Verificar se tem headers típicos de navegador
        $userAgent = $request->userAgent();
        $hasBrowserHeaders = $userAgent && (
            str_contains($userAgent, 'Mozilla') ||
            str_contains($userAgent, 'Chrome') ||
            str_contains($userAgent, 'Safari') ||
            str_contains($userAgent, 'Firefox') ||
            str_contains($userAgent, 'Edge')
        );

        // Verificar se tem headers de segurança do navegador
        $hasSecurityHeaders = $request->header('sec-fetch-site') === 'same-origin' &&
            $request->header('sec-fetch-mode') === 'cors' &&
            $request->header('sec-fetch-dest') === 'empty';

        // Requisição é válida se tem pelo menos 3 dessas verificações
        $validations = [
            $hasValidSession,
            $isValidReferer,
            $isValidOrigin,
            $hasBrowserHeaders,
            $hasSecurityHeaders
        ];

        $validCount = array_sum($validations);

        Log::info('[IP_CHECK] Verificação de requisição web real', [
            'has_valid_session' => $hasValidSession,
            'is_valid_referer' => $isValidReferer,
            'is_valid_origin' => $isValidOrigin,
            'has_browser_headers' => $hasBrowserHeaders,
            'has_security_headers' => $hasSecurityHeaders,
            'valid_count' => $validCount,
            'current_domain' => $currentDomain,
            'user_agent' => $userAgent,
            'referer' => $referer,
            'origin' => $origin
        ]);

        return $validCount >= 3;
    }
}
