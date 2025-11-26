<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Helpers\NotificationLogger;
use App\Helpers\WithdrawLogger;
use App\Helpers\AppRequestLogger;

class UserController extends Controller
{
    /**
     * Obter saldo do usuário
     */
    public function getBalance(Request $request)
    {
        try {
            // Obter usuário do middleware check.token.secret
            $user = $this->getUserFromRequest($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401)->header('Access-Control-Allow-Origin', '*');
            }

            // Calcular totais de transações (entradas) - apenas COMPLETED e PAID_OUT
            $totalInflows = \App\Models\Solicitacoes::where('user_id', $user->user_id)
                ->whereIn('status', ['PAID_OUT', 'COMPLETED'])
                ->sum('amount');

            // Calcular totais de saques (saídas) - apenas COMPLETED e PAID_OUT
            $totalOutflows = \App\Models\SolicitacoesCashOut::where('user_id', $user->user_id)
                ->whereIn('status', ['PAID_OUT', 'COMPLETED'])
                ->sum('amount');

            // Log para debug
            \Log::info('Saldo calculado', [
                'user_id' => $user->user_id,
                'saldo_atual' => $user->saldo ?? 0,
                'total_inflows' => $totalInflows,
                'total_outflows' => $totalOutflows,
                'filtro_status' => ['PAID_OUT', 'COMPLETED']
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'current' => $user->saldo ?? 0,
                    'totalInflows' => $totalInflows,
                    'totalOutflows' => $totalOutflows,
                ]
            ])->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            Log::error('Erro ao obter saldo', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    /**
     * Obter transações do usuário
     */
    public function getTransactions(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401)->header('Access-Control-Allow-Origin', '*');
            }

            $page = $request->get('page', 1);
            $limit = min($request->get('limit', 20), 100);

            // Buscar transações de entrada (depósitos)
            $depositos = \App\Models\Solicitacoes::where('user_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Buscar transações de saída (saques)
            $saques = \App\Models\SolicitacoesCashOut::where('user_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Combinar e ordenar todas as transações
            $allTransactions = collect();
            
            // Adicionar depósitos
            foreach ($depositos as $deposito) {
                $allTransactions->push([
                    'id' => $deposito->id,
                    'transaction_id' => $deposito->idTransaction ?? $deposito->externalreference,
                    'tipo' => 'deposit',
                    'amount' => $deposito->amount,
                    'valor_liquido' => $deposito->deposito_liquido,
                    'taxa' => $deposito->taxa_cash_in,
                    'status' => $deposito->status,
                    'status_legivel' => $this->mapStatus($deposito->status),
                    'data' => $deposito->date,
                    'created_at' => $deposito->created_at,
                    'nome' => $deposito->client_name ?? 'Cliente',
                    'documento' => $deposito->client_document ?? '08355037120',
                    'adquirente' => $deposito->adquirente ?? 'Sistema',
                    'description' => $deposito->description ?? 'Depósito via PIX'
                ]);
            }
            
            // Adicionar saques
            foreach ($saques as $saque) {
                $allTransactions->push([
                    'id' => $saque->id,
                    'transaction_id' => $saque->idTransaction ?? $saque->externalreference,
                    'tipo' => 'withdraw',
                    'amount' => $saque->amount,
                    'valor_liquido' => $saque->cash_out_liquido,
                    'taxa' => $saque->taxa_cash_out,
                    'status' => $saque->status,
                    'status_legivel' => $this->mapStatus($saque->status),
                    'data' => $saque->date,
                    'created_at' => $saque->created_at,
                    'nome' => $saque->beneficiaryname ?? 'Cliente',
                    'documento' => $saque->beneficiarydocument ?? '08355037120',
                    'pix_key' => $saque->pix ?? '',
                    'pix_key_type' => $saque->pixkey ?? '',
                    'adquirente' => $saque->adquirente ?? 'Sistema',
                    'description' => $saque->description ?? 'Saque via PIX'
                ]);
            }

            // Ordenar por data de criação (mais recente primeiro)
            $allTransactions = $allTransactions->sortByDesc('created_at')->values();
            
            // Aplicar paginação manual
            $total = $allTransactions->count();
            $offset = ($page - 1) * $limit;
            $paginatedTransactions = $allTransactions->slice($offset, $limit)->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $paginatedTransactions,
                    'current_page' => $page,
                    'last_page' => ceil($total / $limit),
                    'per_page' => $limit,
                    'total' => $total
                ]
            ])->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            Log::error('Erro ao obter transações', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    /**
     * Obter transação específica por ID
     */
    public function getTransactionById(Request $request, $id)
    {
        try {
            $user = $this->getUserFromRequest($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401)->header('Access-Control-Allow-Origin', '*');
            }

            Log::info('Buscando transação por ID', [
                'transaction_id' => $id,
                'user_id' => $user->user_id
            ]);

            // Primeiro, procurar nas solicitações de depósito (entradas)
            $deposito = \App\Models\Solicitacoes::where('user_id', $user->user_id)
                ->where(function($query) use ($id) {
                    $query->where('id', $id)
                          ->orWhere('idTransaction', $id)
                          ->orWhere('externalreference', $id);
                })
                ->first();

            if ($deposito) {
                Log::info('Transação encontrada em depósitos', [
                    'id' => $deposito->id,
                    'status' => $deposito->status,
                    'amount' => $deposito->amount
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $deposito->id,
                        'transaction_id' => $deposito->idTransaction ?? $deposito->externalreference,
                        'tipo' => 'deposit',
                        'amount' => $deposito->amount,
                        'valor_liquido' => $deposito->deposito_liquido,
                        'taxa' => $deposito->taxa_cash_in,
                        'status' => $deposito->status,
                        'status_legivel' => $this->mapStatus($deposito->status),
                        'data' => $deposito->date,
                        'created_at' => $deposito->created_at,
                        'nome' => $deposito->client_name ?? 'Cliente',
                        'documento' => $deposito->client_document ?? '08355037120',
                        'adquirente' => $deposito->adquirente ?? 'Sistema',
                        'description' => $deposito->description ?? 'Depósito via PIX'
                    ]
                ])->header('Access-Control-Allow-Origin', '*');
            }

            // Se não encontrou em depósitos, procurar em saques (saídas)
            $saque = \App\Models\SolicitacoesCashOut::where('user_id', $user->user_id)
                ->where(function($query) use ($id) {
                    $query->where('id', $id)
                          ->orWhere('idTransaction', $id)
                          ->orWhere('externalreference', $id);
                })
                ->first();

            if ($saque) {
                Log::info('Transação encontrada em saques', [
                    'id' => $saque->id,
                    'status' => $saque->status,
                    'amount' => $saque->amount
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $saque->id,
                        'transaction_id' => $saque->idTransaction ?? $saque->externalreference,
                        'tipo' => 'withdraw',
                        'amount' => $saque->amount,
                        'valor_liquido' => $saque->cash_out_liquido,
                        'taxa' => $saque->taxa_cash_out,
                        'status' => $saque->status,
                        'status_legivel' => $this->mapStatus($saque->status),
                        'data' => $saque->date,
                        'created_at' => $saque->created_at,
                        'nome' => $saque->beneficiaryname ?? 'Cliente',
                        'documento' => $saque->beneficiarydocument ?? '08355037120',
                        'pix_key' => $saque->pix ?? '',
                        'pix_key_type' => $saque->pixkey ?? '',
                        'adquirente' => $saque->adquirente ?? 'Sistema',
                        'description' => $saque->description ?? 'Saque via PIX'
                    ]
                ])->header('Access-Control-Allow-Origin', '*');
            }

            // Não encontrou a transação
            Log::info('Transação não encontrada', [
                'transaction_id' => $id,
                'user_id' => $user->user_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Transação não encontrada'
            ], 404)->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            Log::error('Erro ao obter transação por ID', [
                'error' => $e->getMessage(),
                'transaction_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }


    /**
     * Gerar QR Code para recebimento PIX
     */
    public function generatePixQR(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401)->header('Access-Control-Allow-Origin', '*');
            }

            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 400)->header('Access-Control-Allow-Origin', '*');
            }

            $amount = $request->input('amount');
            $description = $request->input('description', 'Depósito via PIX');

            // Usar o sistema real de geração de QR Code através dos Traits
            $adquirenteDefault = \App\Helpers\Helper::adquirenteDefault($user->user_id);
            
            Log::info('Gerando QR Code PIX via API', [
                'user_id' => $user->username,
                'amount' => $amount,
                'adquirente' => $adquirenteDefault
            ]);

            // Preparar dados para o trait
            $requestData = new \Illuminate\Http\Request();
            $requestData->merge([
                'amount' => $amount,
                'description' => $description,
                'debtor_name' => $user->name ?? 'Cliente',
                'debtor_document_number' => $user->cpf ?? '08355037120',
                'email' => $user->email ?? 'redacted@example.invalid',
                'phone' => $user->telefone ?? '11999999999',
                'postback' => env('APP_URL') . '/api/callback'
            ]);
            
            // Simular usuário autenticado no request
            $requestData->setUserResolver(function () use ($user) {
                return $user;
            });

            // Escolher o trait baseado no adquirente padrão
            $response = null;
            switch ($adquirenteDefault) {
                case 'woovi':
                    $response = \App\Traits\WooviTrait::requestPaymentWoovi($requestData);
                    break;
                case 'bspay':
                    $response = \App\Traits\BSPayTrait::requestDepositBSPay($requestData);
                    break;
                case 'pixup':
                    $response = \App\Traits\PixupTrait::requestDepositPixup($requestData);
                    break;
                case 'xdpag':
                    $response = \App\Traits\XDPagTrait::requestDepositXDPag($requestData);
                    break;
                case 'trustypix':
                    $response = \App\Traits\TrustyPixTrait::requestDepositTrustyPix($requestData);
                    break;
                case 'primepay7':
                    $response = \App\Traits\PrimePay7Trait::requestDepositPrimePay7($requestData);
                    break;
                case 'rede':
                    $response = \App\Traits\RedeTrait::generateQrCodeRede($requestData);
                    break;
                case 'asaas':
                    $response = \App\Traits\AsaasTrait::requestDepositAsaas($requestData);
                    break;
                default:
                    // Fallback para Woovi se não encontrar o adquirente
                    $response = \App\Traits\WooviTrait::requestPaymentWoovi($requestData);
                    break;
            }

            if (!$response || $response['status'] !== 200) {
                Log::error('Erro ao gerar QR Code via adquirente', [
                    'adquirente' => $adquirenteDefault,
                    'response' => $response,
                    'user_id' => $user->username
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar QR Code PIX'
                ], 500)->header('Access-Control-Allow-Origin', '*');
            }

            // Formatar resposta para o formato esperado pelo app
            $qrData = $response['data'];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'amount' => $amount,
                    'description' => $description,
                    'user_id' => $user->username,
                    'qr_code' => $qrData['qrcode'] ?? $qrData['charge']['brCode'] ?? null,
                    'qr_code_image_url' => $qrData['qr_code_image_url'] ?? $qrData['charge']['qrCode'] ?? null,
                    'transaction_id' => $qrData['idTransaction'] ?? $qrData['charge']['id'] ?? null,
                    'expires_at' => now()->addHours(24)->toISOString(),
                    'adquirente' => $adquirenteDefault
                ]
            ])->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            Log::error('Erro ao gerar QR Code PIX', [
                'error' => $e->getMessage(),
                'user_id' => $user->username ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    /**
     * Realizar saque PIX
     */
    public function makePixWithdraw(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            
            if (!$user) {
                AppRequestLogger::logAppRequestError('POST', 'pix/withdraw', 'unknown', 'Usuário não autenticado', $request->all());
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401)->header('Access-Control-Allow-Origin', '*');
            }

            AppRequestLogger::logAppRequest('POST', 'pix/withdraw', $user->user_id, $request->all());

            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0.01',
                'pix_key' => 'required|string',
                'pixKeyType' => 'nullable|string|in:cpf,cnpj,email,phone,telefone,random',
                'idTransaction' => 'nullable|string',
                'pin' => 'required|string|size:6',
                'description' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                AppRequestLogger::logAppRequestError('POST', 'pix/withdraw', $user->user_id, 'Dados inválidos: ' . $firstError, $request->all());
                
                // Mensagens mais específicas para o usuário
                $userMessage = 'Dados inválidos';
                if (str_contains($firstError, 'amount')) {
                    $userMessage = 'Valor inválido. Insira um valor maior que R$ 0,01';
                } elseif (str_contains($firstError, 'pix_key')) {
                    $userMessage = 'Chave PIX inválida';
                } elseif (str_contains($firstError, 'pixKeyType')) {
                    $userMessage = 'Tipo de chave PIX inválido';
                } elseif (str_contains($firstError, 'idTransaction')) {
                    $userMessage = 'ID de transação inválido';
                } elseif (str_contains($firstError, 'pin')) {
                    $userMessage = 'PIN deve ter exatamente 6 dígitos';
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $userMessage,
                    'errors' => $validator->errors()
                ], 400)->header('Access-Control-Allow-Origin', '*');
            }

            $amount = $request->input('amount');
            $pixKey = $request->input('pix_key');
            $pixKeyType = $request->input('pixKeyType');
            $idTransaction = $request->input('idTransaction');
            
            // Se pixKeyType não foi fornecido, detectar automaticamente
            if (empty($pixKeyType)) {
                $pixKeyType = $this->detectPixKeyType($pixKey);
                Log::info('Tipo de chave PIX detectado automaticamente', [
                    'pix_key' => substr($pixKey, 0, 10) . '...',
                    'detected_type' => $pixKeyType
                ]);
            }
            
            // Se idTransaction não foi fornecido, gerar automaticamente
            if (empty($idTransaction)) {
                $idTransaction = 'withdraw_' . time() . '_' . $user->user_id;
                Log::info('ID de transação gerado automaticamente', [
                    'generated_id' => $idTransaction
                ]);
            }

            // Log do início do saque
            WithdrawLogger::logWithdrawStarted($user->user_id, $request->amount, $request->pix_key, [
                'pix_key_type' => $pixKeyType,
                'transaction_id' => $idTransaction,
                'description' => $request->description
            ]);
            $pin = $request->input('pin');
            $description = $request->input('description', 'Saque via PIX');

            // Verificar se o usuário tem saldo suficiente
            if ($user->saldo < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo insuficiente'
                ], 400)->header('Access-Control-Allow-Origin', '*');
            }

            // Verificar se o usuário tem PIN ativo
            if (!$user->pin_active || empty($user->pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Para realizar saques via app, você precisa cadastrar um PIN de segurança primeiro.',
                    'requires_pin_setup' => true
                ], 403)->header('Access-Control-Allow-Origin', '*');
            }

            // Verificar PIN usando o trait
            $pinController = new \App\Http\Controllers\ProfileController();
            if (!$pinController->verifyPinInternal($user, $pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'PIN incorreto'
                ], 403)->header('Access-Control-Allow-Origin', '*');
            }

            // Usar o sistema real de saque através dos Traits
            $adquirenteDefault = \App\Helpers\Helper::adquirenteDefault($user->user_id);
            
            Log::info('Realizando saque PIX via API', [
                'user_id' => $user->username,
                'amount' => $amount,
                'pix_key' => $pixKey,
                'pixKeyType' => $pixKeyType,
                'idTransaction' => $idTransaction,
                'adquirente' => $adquirenteDefault
            ]);

            // Preparar dados para o trait
            $requestData = new \Illuminate\Http\Request();
            $requestData->merge([
                'amount' => $amount,
                'pixKey' => $pixKey,
                'pixKeyType' => $pixKeyType,
                'idTransaction' => $idTransaction,
                'baasPostbackUrl' => env('APP_URL') . '/api/callback',
                'saque_automatico' => true
            ]);
            
            // Simular usuário autenticado no request
            $requestData->setUserResolver(function () use ($user) {
                return $user;
            });

            // Escolher o trait baseado no adquirente padrão
            $response = null;
            switch ($adquirenteDefault) {
                case 'woovi':
                    $response = \App\Traits\WooviTrait::requestSaqueWoovi($requestData);
                    break;
                case 'bspay':
                    $response = \App\Traits\BSPayTrait::requestPaymentBSPay($requestData);
                    break;
                case 'pixup':
                    $response = \App\Traits\PixupTrait::requestPaymentPixup($requestData);
                    break;
                case 'xdpag':
                    $response = \App\Traits\XDPagTrait::requestPaymentXDPag($requestData);
                    break;
                case 'trustypix':
                    $response = \App\Traits\TrustyPixTrait::requestPaymentTrustyPix($requestData);
                    break;
                case 'primepay7':
                    $response = \App\Traits\PrimePay7Trait::requestPaymentPrimePay7($requestData);
                    break;
                case 'rede':
                    $response = \App\Traits\RedeTrait::requestPaymentRede($requestData);
                    break;
                case 'asaas':
                    $response = \App\Traits\AsaasTrait::requestPaymentAsaas($requestData);
                    break;
                default:
                    // Fallback para Woovi se não encontrar o adquirente
                    $response = \App\Traits\WooviTrait::requestSaqueWoovi($requestData);
                    break;
            }

            if (!$response || $response['status'] !== 200) {
                Log::error('Erro ao realizar saque via adquirente', [
                    'adquirente' => $adquirenteDefault,
                    'response' => $response,
                    'user_id' => $user->username
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao processar saque PIX'
                ], 500)->header('Access-Control-Allow-Origin', '*');
            }

            // Formatar resposta para o formato esperado pelo app
            $withdrawData = $response['data'];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $withdrawData['id'] ?? $idTransaction,
                    'amount' => $amount,
                    'pix_key' => $pixKey,
                    'pixKeyType' => $pixKeyType,
                    'description' => $description,
                    'status' => $withdrawData['withdrawStatusId'] ?? 'PROCESSING',
                    'estimated_time' => '5-10 minutos',
                    'created_at' => $withdrawData['createdAt'] ?? now()->toISOString()
                ]
            ])->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            Log::error('Erro ao realizar saque PIX', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    /**
     * Obter extrato combinado (entradas e saídas)
     */
    public function getStatement(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401)->header('Access-Control-Allow-Origin', '*');
            }

            // Fazer requisições para obter entradas e saídas do período atual
            $inflowsResponse = Http::get(url('/relatorio/entradas?periodo=hoje&user_id=' . $user->user_id));
            $outflowsResponse = Http::get(url('/relatorio/saidas?periodo=hoje&user_id=' . $user->user_id));

            $inflows = $inflowsResponse->successful() ? $inflowsResponse->json() : [];
            $outflows = $outflowsResponse->successful() ? $outflowsResponse->json() : [];

            return response()->json([
                'success' => true,
                'data' => [
                    'inflows' => [
                        'label' => 'Entradas de Hoje',
                        'data' => $inflows
                    ],
                    'outflows' => [
                        'label' => 'Saídas de Hoje',
                        'data' => $outflows
                    ],
                    'period' => 'hoje'
                ]
            ])->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            Log::error('Erro ao obter extrato', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    /**
     * Obter perfil completo do usuário
     */
    public function getProfile(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401)->header('Access-Control-Allow-Origin', '*');
            }

            // Fazer requisição para obter dados completos do perfil
            $profileResponse = Http::get(url('/my-profile?user_id=' . $user->user_id));

            if ($profileResponse->successful()) {
                $profileData = $profileResponse->json();
                
                return response()->json([
                    'success' => true,
                    'data' => array_merge([
                        'id' => $user->username,
                        'username' => $user->username,
                        'email' => $user->email ?? '',
                        'name' => $user->name ?? $user->username,
                        'phone' => $user->telefone ?? '',
                        'cnpj' => $user->cpf_cnpj ?? '',
                        'status' => $user->status == 1 ? 'active' : 'inactive',
                        'balance' => $user->saldo ?? 0,
                    ], $profileData ?? [])
                ])->header('Access-Control-Allow-Origin', '*');
            }

            // Fallback para dados básicos se a requisição falhar
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->username,
                    'username' => $user->username,
                    'email' => $user->email ?? '',
                    'name' => $user->name ?? $user->username,
                    'phone' => $user->telefone ?? '',
                    'cnpj' => $user->cnpj ?? '',
                    'status' => $user->status == 1 ? 'active' : 'inactive',
                    'balance' => $user->saldo ?? 0,
                ]
            ])->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            Log::error('Erro ao obter perfil', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    /**
     * Obter dados reais de faturamento e transações
     */
    public function getRealData(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401)->header('Access-Control-Allow-Origin', '*');
            }

            $periodo = $request->input('periodo', 'hoje'); // hoje, 7dias, mes, personalizado
            $dataInicio = $request->input('data_inicio');
            $dataFim = $request->input('data_fim');

            // Calcular datas baseado no período
            $dates = $this->calculateDateRange($periodo, $dataInicio, $dataFim);
            
            // Log para debug
            \Log::info('Filtro de data aplicado', [
                'periodo' => $periodo,
                'inicio' => $dates['inicio']->format('Y-m-d H:i:s'),
                'fim' => $dates['fim']->format('Y-m-d H:i:s'),
                'user_id' => $user->user_id
            ]);

            // Buscar dados de entradas (depósitos) - apenas COMPLETED e PAID_OUT
            $entradasQuery = \App\Models\Solicitacoes::where('user_id', $user->user_id)
                ->whereBetween('date', [$dates['inicio'], $dates['fim']])
                ->whereIn('status', ['PAID_OUT', 'COMPLETED']);

            $totalEntradas = $entradasQuery->sum('amount');
            $totalEntradasLiquidas = $entradasQuery->sum('deposito_liquido');
            $totalTaxasEntradas = $entradasQuery->sum('taxa_cash_in');

            // Buscar dados de saídas (saques) - apenas COMPLETED e PAID_OUT
            $saidasQuery = \App\Models\SolicitacoesCashOut::where('user_id', $user->user_id)
                ->whereBetween('date', [$dates['inicio'], $dates['fim']])
                ->whereIn('status', ['PAID_OUT', 'COMPLETED']);

            $totalSaidas = $saidasQuery->sum('amount');
            $totalSaidasLiquidas = $saidasQuery->sum('cash_out_liquido');
            $totalTaxasSaidas = $saidasQuery->sum('taxa_cash_out');

            // Buscar transações para o extrato
            $transacoesEntradas = $entradasQuery->orderBy('date', 'desc')->get();
            $transacoesSaidas = $saidasQuery->orderBy('date', 'desc')->get();
            
            // Log para debug - verificar quantas transações foram encontradas
            \Log::info('Transações aprovadas encontradas', [
                'periodo' => $periodo,
                'user_id' => $user->user_id,
                'saldo_atual' => $user->saldo ?? 0,
                'entradas_count' => $transacoesEntradas->count(),
                'saidas_count' => $transacoesSaidas->count(),
                'total_entradas' => $totalEntradas,
                'total_saidas' => $totalSaidas,
                'total_entradas_liquidas' => $totalEntradasLiquidas,
                'total_saidas_liquidas' => $totalSaidasLiquidas,
                'primeira_entrada_date' => $transacoesEntradas->first() ? $transacoesEntradas->first()->date : null,
                'ultima_entrada_date' => $transacoesEntradas->last() ? $transacoesEntradas->last()->date : null,
                'filtro_status' => ['PAID_OUT', 'COMPLETED']
            ]);

            // Combinar e ordenar transações
            $extrato = collect();
            
            // Adicionar entradas com tipo 'deposit'
            foreach ($transacoesEntradas as $entrada) {
                $extrato->push([
                    'id' => $entrada->id,
                    'tipo' => 'deposit',
                    'transaction_id' => $entrada->idTransaction ?? $entrada->externalreference,
                    'valor' => $entrada->amount,
                    'valor_liquido' => $entrada->deposito_liquido,
                    'taxa' => $entrada->taxa_cash_in,
                    'status' => $this->mapStatus($entrada->status),
                    'data' => $entrada->date,
                    'nome' => $entrada->client_name ?? 'Cliente',
                    'documento' => $entrada->client_document ?? '08355037120',
                    'adquirente' => $entrada->adquirente ?? 'Sistema'
                ]);
            }

            // Adicionar saídas com tipo 'withdraw'
            foreach ($transacoesSaidas as $saida) {
                $extrato->push([
                    'id' => $saida->id,
                    'tipo' => 'withdraw',
                    'transaction_id' => $saida->idTransaction ?? $saida->externalreference,
                    'valor' => $saida->amount,
                    'valor_liquido' => $saida->cash_out_liquido,
                    'taxa' => $saida->taxa_cash_out,
                    'status' => $this->mapStatus($saida->status),
                    'data' => $saida->date,
                    'nome' => $saida->beneficiaryname ?? 'Cliente',
                    'documento' => $saida->beneficiarydocument ?? '08355037120',
                    'pix_key' => $saida->pix ?? '',
                    'pix_key_type' => $saida->pixkey ?? '',
                    'adquirente' => $saida->adquirente ?? 'Sistema'
                ]);
            }

            // Ordenar por data (mais recente primeiro)
            $extrato = $extrato->sortByDesc('data')->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'periodo' => $periodo,
                    'data_inicio' => $dates['inicio'],
                    'data_fim' => $dates['fim'],
                    'resumo' => [
                        'total_entradas' => $totalEntradas,
                        'total_entradas_liquidas' => $totalEntradasLiquidas,
                        'total_taxas_entradas' => $totalTaxasEntradas,
                        'total_saidas' => $totalSaidas,
                        'total_saidas_liquidas' => $totalSaidasLiquidas,
                        'total_taxas_saidas' => $totalTaxasSaidas,
                        'saldo_atual' => $user->saldo ?? 0,
                        'saldo_periodo' => $user->saldo ?? 0
                    ],
                    'extrato' => $extrato,
                    'contadores' => [
                        'total_entradas_count' => $transacoesEntradas->count(),
                        'total_saidas_count' => $transacoesSaidas->count()
                    ]
                ]
            ])->header('Access-Control-Allow-Origin', '*');

        } catch (\Exception $e) {
            Log::error('Erro ao obter dados reais', [
                'error' => $e->getMessage(),
                'user_id' => $user->username ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    /**
     * Calcular intervalo de datas baseado no período
     */
    private function calculateDateRange($periodo, $dataInicio = null, $dataFim = null)
    {
        // Usar timezone do Brasil para garantir consistência
        $now = \Carbon\Carbon::now('America/Sao_Paulo');
        
        switch ($periodo) {
            case 'hoje':
                return [
                    'inicio' => $now->copy()->startOfDay(),
                    'fim' => $now->copy()->endOfDay()
                ];
                
            case '7dias':
                return [
                    'inicio' => $now->copy()->subDays(7)->startOfDay(),
                    'fim' => $now->copy()->endOfDay()
                ];
                
            case 'mes':
                return [
                    'inicio' => $now->copy()->startOfMonth(),
                    'fim' => $now->copy()->endOfMonth()
                ];
                
            case 'personalizado':
                if ($dataInicio && $dataFim) {
                    return [
                        'inicio' => \Carbon\Carbon::parse($dataInicio, 'America/Sao_Paulo')->startOfDay(),
                        'fim' => \Carbon\Carbon::parse($dataFim, 'America/Sao_Paulo')->endOfDay()
                    ];
                }
                // Fallback para hoje se não tiver datas
                return [
                    'inicio' => $now->copy()->startOfDay(),
                    'fim' => $now->copy()->endOfDay()
                ];
                
            case 'tudo':
                return [
                    'inicio' => \Carbon\Carbon::parse('2020-01-01', 'America/Sao_Paulo'),
                    'fim' => $now->copy()->endOfDay()
                ];
                
            default:
                return [
                    'inicio' => $now->copy()->startOfDay(),
                    'fim' => $now->copy()->endOfDay()
                ];
        }
    }

    /**
     * Mapear status para formato legível
     */
    private function mapStatus($status)
    {
        $statusMap = [
            'WAITING_FOR_APPROVAL' => 'Pendente',
            'PENDING' => 'Pendente',
            'PENDING_APPROVAL' => 'Pendente',
            'PAID_OUT' => 'Aprovado',
            'COMPLETED' => 'Aprovado',
            'APPROVED' => 'Aprovado',
            'CANCELLED' => 'Cancelado',
            'FAILED' => 'Falhou',
            'REJECTED' => 'Rejeitado'
        ];

        return $statusMap[$status] ?? $status;
    }
    private function detectPixKeyType($pixKey)
    {
        // Remover caracteres especiais para análise
        $cleanKey = preg_replace('/[^0-9a-zA-Z@.]/', '', $pixKey);
        
        // CPF (11 dígitos) - tem prioridade sobre telefone
        if (preg_match('/^\d{11}$/', $cleanKey)) {
            return 'cpf';
        }
        
        // CNPJ (14 dígitos)
        if (preg_match('/^\d{14}$/', $cleanKey)) {
            return 'cnpj';
        }
        
        // Email
        if (filter_var($cleanKey, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        
        // Telefone (10 dígitos) - apenas 10 dígitos para evitar conflito com CPF
        if (preg_match('/^\d{10}$/', $cleanKey)) {
            return 'phone';
        }
        
        // Chave aleatória (UUID ou similar)
        if (preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $cleanKey)) {
            return 'random';
        }
        
        // Chave aleatória (formato hexadecimal de 36 caracteres)
        if (preg_match('/^[a-f0-9]{36}$/i', $cleanKey)) {
            return 'random';
        }
        
        // Default para CPF se não conseguir detectar
        return 'cpf';
    }

    /**
     * Extrair usuário do request (usando middleware check.token.secret)
     */
    private function getUserFromRequest(Request $request)
    {
        try {
            // O middleware check.token.secret já validou o token e secret
            // e adicionou o usuário ao request
            $token = $request->input('token');
            $secret = $request->input('secret');
            
            if (!$token || !$secret) {
                return null;
            }

            // Buscar as chaves do usuário
            $userKeys = \App\Models\UsersKey::where('token', $token)
                ->where('secret', $secret)
                ->first();
            
            if (!$userKeys) {
                return null;
            }

            // Buscar o usuário
            return User::where('username', $userKeys->user_id)->first();
            
        } catch (\Exception $e) {
            Log::error('Erro ao obter usuário do request', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Extrair usuário do token (método antigo - mantido para compatibilidade)
     */
    private function getUserFromToken(Request $request)
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                return null;
            }

            $decoded = json_decode(base64_decode($token), true);
            
            if (!$decoded || !isset($decoded['expires_at']) || $decoded['expires_at'] < now()->timestamp) {
                return null;
            }

            return User::where('username', $decoded['user_id'])->first();
            
        } catch (\Exception $e) {
            return null;
        }
    }
}
