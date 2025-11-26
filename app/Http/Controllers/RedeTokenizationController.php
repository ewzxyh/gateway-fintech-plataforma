<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\RedeService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RedeTokenizationController extends Controller
{
    protected $redeService;

    public function __construct(RedeService $redeService)
    {
        $this->redeService = $redeService;
    }

    /**
     * Cria uma solicitação de tokenização
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'cardNumber' => 'required|string|min:13|max:19',
            'expirationMonth' => 'required|string|min:1|max:2',
            'expirationYear' => 'required|string|min:4|max:4',
            'cardholderName' => 'nullable|string|max:255',
            'securityCode' => 'nullable|string|min:3|max:4',
            'storageCard' => 'required|integer|in:0,1,2',
            'kind' => 'nullable|string|in:credit,debit',
            'embeddedZeroDollar' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $data = $request->all();
            
            // Adicionar valores padrão se não fornecidos
            $data['kind'] = $data['kind'] ?? 'credit';
            $data['embeddedZeroDollar'] = $data['embeddedZeroDollar'] ?? false;

            $result = $this->redeService->createTokenization($data);

            if ($result && isset($result['tokenizationId'])) {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar tokenização',
                    'error' => $result
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao criar tokenização: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Consulta uma solicitação de tokenização
     */
    public function show(string $tokenizationId): JsonResponse
    {
        try {
            $result = $this->redeService->getTokenization($tokenizationId);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tokenização não encontrada'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao consultar tokenização: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Atualiza o status de uma tokenização
     */
    public function updateStatus(Request $request, string $tokenizationId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tokenizationStatus' => 'required|string|in:suspend,resume,delete',
            'reason' => 'required|integer|min:1|max:99'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $data = $request->only(['tokenizationStatus', 'reason']);
            $result = $this->redeService->updateTokenizationStatus($tokenizationId, $data);

            if ($result && isset($result['returnCode']) && $result['returnCode'] === '00') {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar status da tokenização',
                    'error' => $result
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status da tokenização: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Gera cryptogram para uma tokenização
     */
    public function generateCryptogram(Request $request, string $tokenizationId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subscription' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $data = $request->only(['subscription']);
            $result = $this->redeService->generateCryptogram($tokenizationId, $data);

            if ($result && isset($result['returnCode']) && $result['returnCode'] === '00') {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar cryptogram',
                    'error' => $result
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao gerar cryptogram: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Configura callback para tokenização
     */
    public function setCallback(string $tokenizationId): JsonResponse
    {
        try {
            $result = $this->redeService->setTokenizationCallback($tokenizationId);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao configurar callback'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao configurar callback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Configura URL de callback para tokenização
     */
    public function setCallbackUrl(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:255',
            'authorization.type' => 'required|string|in:basic,oauth',
            'authorization.token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $data = $request->all();
            $result = $this->redeService->setTokenizationCallbackUrl($data);

            if ($result && isset($result['returnCode']) && $result['returnCode'] === '00') {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao configurar URL de callback',
                    'error' => $result
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao configurar URL de callback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}



