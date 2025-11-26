<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\App;
use Illuminate\Http\JsonResponse;

class LogoController extends Controller
{
    /**
     * Retorna as URLs das logos baseadas no tema
     * 
     * @return JsonResponse
     */
    public function getLogos(): JsonResponse
    {
        try {
            $app = App::first();

            if (!$app) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configurações não encontradas',
                    'logos' => [
                        'light' => asset('assets/images/default-logo.png'),
                        'dark' => asset('assets/images/default-logo.png'),
                    ]
                ], 404);
            }

            $logos = [
                'light' => $app->gateway_logo ? asset($app->gateway_logo) : asset('assets/images/default-logo.png'),
                'dark' => $app->gateway_logo_dark ? asset($app->gateway_logo_dark) : ($app->gateway_logo ? asset($app->gateway_logo) : asset('assets/images/default-logo.png')),
            ];

            return response()->json([
                'success' => true,
                'logos' => $logos,
                'gateway_name' => $app->gateway_name ?? 'Gateway',
                'gateway_color' => $app->gateway_color ?? '#00d4aa',
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar logos: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'logos' => [
                    'light' => asset('assets/images/default-logo.png'),
                    'dark' => asset('assets/images/default-logo.png'),
                ]
            ], 500);
        }
    }

    /**
     * Retorna apenas a logo baseada no tema especificado
     * 
     * @param string $theme
     * @return JsonResponse
     */
    public function getLogoByTheme(string $theme): JsonResponse
    {
        try {
            $app = App::first();

            if (!$app) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configurações não encontradas',
                    'logo' => asset('assets/images/default-logo.png')
                ], 404);
            }

            $logo = null;

            if ($theme === 'dark' && $app->gateway_logo_dark) {
                $logo = asset($app->gateway_logo_dark);
            } else {
                $logo = $app->gateway_logo ? asset($app->gateway_logo) : asset('assets/images/default-logo.png');
            }

            return response()->json([
                'success' => true,
                'logo' => $logo,
                'theme' => $theme,
                'gateway_name' => $app->gateway_name ?? 'Gateway',
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar logo por tema: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'logo' => asset('assets/images/default-logo.png')
            ], 500);
        }
    }
}
