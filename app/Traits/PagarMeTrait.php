<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Solicitacoes;
use App\Models\SolicitacoesCashOut;
use App\Models\App;
use App\Models\User;
use App\Models\Pagarme;
use App\Traits\IPManagementTrait;
use Faker\Factory as FakerFactory;
use App\Helpers\Helper;

trait PagarMeTrait
{
    /**
     * Verifica se o IP está autorizado para operações de saque
     */
    public static function checkIPForWithdraw(User $user, $request = null): array
    {
        // Usar getIPForAcquirer se o request estiver disponível, senão usar getClientIP
        $clientIP = $request ? IPManagementTrait::getIPForAcquirer($request) : IPManagementTrait::getClientIP();

        if (!IPManagementTrait::isIPAllowed($clientIP, $user)) {
            return [
                'success' => false,
                'message' => 'IP não autorizado para realizar saques',
                'client_ip' => $clientIP
            ];
        }

        return [
            'success' => true,
            'client_ip' => $clientIP
        ];
    }
    protected static string $secret;
    protected static string $urlCashIn;
    protected static string $urlCashOut;
    protected static string $taxaCashIn;
    protected static string $taxaCashOut;

    protected static function generateCredentialsPagarme()
    {
        $setting = Pagarme::first();
        if (!$setting) {
            return false;
        }

        self::$secret = $setting->secret;
        self::$urlCashIn = $setting->url_cash_in;
        self::$urlCashOut = $setting->url_cash_out;
        // Taxas serão calculadas dinamicamente pelo sistema flexível
        self::$taxaCashIn = '0.00'; // Valor padrão, será sobrescrito pelo cálculo dinâmico
        self::$taxaCashOut = '0.00'; // Valor padrão, será sobrescrito pelo cálculo dinâmico

        return true;
    }

    /**
     * Gera QR Code PIX para depósito (Cash-in) - Pagar.me
     */
    public static function generateQrCodePagarme($request)
    {
        try {
            $data = $request->all();
            $user = User::where('id', $request->user()->id)->first();
            $setting = App::first();

            Log::info('=== PAGARMETRAIT GENERATE QR CODE INICIADO ===');
            Log::info('PagarMeTrait::generateQrCodePagarme - Dados da requisição:', [
                'user_id' => $user->id,
                'username' => $user->username,
                'amount' => $request->amount,
                'is_interface_web' => $request->input('baasPostbackUrl') === 'web'
            ]);

            // Determinar se é depósito via interface web ou API
            $isInterfaceWeb = $request->input('baasPostbackUrl') === 'web';

            // Calcula taxas de depósito (cash-in) usando sistema flexível
            $taxaCalculada = \App\Helpers\TaxaFlexivelHelper::calcularTaxaDeposito($request->amount, $setting, $user);
            $deposito_liquido = $taxaCalculada['deposito_liquido'];
            $taxa_cash_in = $taxaCalculada['taxa_cash_in'];
            $descricao = $taxaCalculada['descricao'];

            // Definir variáveis para compatibilidade com o código existente
            $taxa_total = $taxa_cash_in;
            $taxa_percentual_valor = $taxa_cash_in;
            $taxa_fixa_deposito = 0;

            Log::info('PagarMeTrait::generateQrCodePagarme - Cálculo de taxas:', [
                'amount_original' => $request->amount,
                'taxa_cash_in' => $taxa_cash_in,
                'deposito_liquido' => $deposito_liquido,
                'descricao' => $descricao,
                'is_interface_web' => $isInterfaceWeb
            ]);

            $date = Carbon::now();
            $externalId = Str::uuid()->toString();

            // Criar solicitação de depósito
            $solicitacao = Solicitacoes::create([
                'user_id' => $user->user_id,
                'idTransaction' => $externalId,
                'externalreference' => $externalId,
                'amount' => $request->amount,
                'deposito_liquido' => $deposito_liquido,
                'taxa_cash_in' => $taxa_total,
                'taxa_pix_cash_in_adquirente' => $taxa_percentual_valor,
                'taxa_pix_cash_in_valor_fixo' => $taxa_fixa_deposito,
                'status' => 'WAITING_FOR_APPROVAL',
                'adquirente_ref' => 'pagarme',
                'client_name' => $request->debtor_name ?? 'Cliente',
                'client_document' => $request->debtor_document_number ?? '08355037120',
                'client_email' => $request->email ?? 'redacted@example.invalid',
                'client_telefone' => $request->phone ?? '08355037120',
                'date' => $date,
                'executor_ordem' => 'pagarme',
                'descricao_transacao' => $descricao,
                'qrcode_pix' => '',
                'paymentcode' => '',
                'paymentCodeBase64' => '',
                'callback' => $request->postback_url ?? $user->webhook_url ?? $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
                'created_at' => $date,
                'updated_at' => $date
            ]);

            Log::info('PagarMeTrait::generateQrCodePagarme - Solicitação criada:', [
                'solicitacao_id' => $solicitacao->id,
                'external_id' => $externalId
            ]);

            // UTMfy integration
            if (!is_null($user->integracao_utmfy)) {
                $ip = $request->header('X-Forwarded-For') ?
                    $request->header('X-Forwarded-For') : ($request->header('CF-Connecting-IP') ?
                        $request->header('CF-Connecting-IP') :
                        $request->ip());

                $msg = "PIX Gerado " . env('APP_NAME');
                UtmfyTrait::gerarUTM('pix', 'waiting_payment', $solicitacao->toArray(), $user->integracao_utmfy, $ip, $msg);
            }

            // Gerar QR Code via Pagar.me
            if (self::generateCredentialsPagarme()) {
                $client_ip = $request->ip();
                $productid = uniqid();
                $document = $request->debtor_document ?? Helper::generateValidCpf();
                $access_secret = base64_encode(self::$secret);
                $gerarpessoa = self::gerarPessoa();
                $client_code = uniqid(strtoupper(str_replace(' ', '_', env('APP_NAME'))) . '_');

                $payload = [
                    "customer" => [
                        "phones" => [
                            "mobile_phone" => [
                                "country_code" => "55",
                                "area_code" => self::ajustePhone('' . $gerarpessoa['celular'])['ddd'],
                                "number" => self::ajustePhone('' . $gerarpessoa['celular'])['phone']
                            ]
                        ],
                        "name" => $gerarpessoa['nome'],
                        "document" => str_replace([".", "-"], "", $gerarpessoa['cpf']),
                        "email" => $gerarpessoa['email'],
                        "type" => "individual",
                        "document_type" => "CPF"
                    ],
                    "payments" => [
                        [
                            "Pix" => [
                                "expires_in" => 3600
                            ],
                            "payment_method" => "pix"
                        ]
                    ],
                    "items" => [
                        [
                            "amount" => intval($request['amount'] * 100),
                            "code" => $client_code,
                            "quantity" => 1,
                            "description" => "Pagamento $client_code"
                        ]
                    ]
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . $access_secret
                ])->post(self::$urlCashIn, $payload);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $transaction_id = $responseData['id'];
                    $qrcode = $responseData['charges'][0]['last_transaction']['qr_code'] ?? '';
                    $qrCodeImageUrl = $responseData['charges'][0]['last_transaction']['qr_code_url'] ?? '';

                    // Atualizar solicitação com dados do QR Code
                    $solicitacao->update([
                        'qrcode_pix' => $qrcode,
                        'paymentcode' => $qrCodeImageUrl,
                        'paymentCodeBase64' => $qrcode ? 'data:image/png;base64,' . base64_encode($qrcode) : '',
                        'externalreference' => $transaction_id,
                        'idTransaction' => $transaction_id
                    ]);

                    Log::info('PagarMeTrait::generateQrCodePagarme - QR Code gerado e salvo com sucesso');

                    return [
                        'status' => 200,
                        'data' => [
                            'idTransaction' => $transaction_id,
                            'qrcode' => $qrcode,
                            'qr_code_image_url' => $qrCodeImageUrl,
                            'charge' => [
                                'id' => $transaction_id,
                                'qrCode' => $qrCodeImageUrl,
                                'brCode' => $qrcode
                            ]
                        ]
                    ];
                } else {
                    Log::error('PagarMeTrait::generateQrCodePagarme - Erro ao gerar QR Code:', ['response' => $response->json()]);
                    $solicitacao->delete();
                    return [
                        'status' => 500,
                        'data' => [
                            'status' => 'error',
                            'message' => 'Erro ao gerar QR Code PIX'
                        ]
                    ];
                }
            } else {
                Log::error('PagarMeTrait::generateQrCodePagarme - Credenciais não configuradas');
                $solicitacao->delete();
                return [
                    'status' => 500,
                    'data' => [
                        'status' => 'error',
                        'message' => 'Credenciais não configuradas'
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error('PagarMeTrait::generateQrCodePagarme - Exceção: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => [
                    'status' => 'error',
                    'message' => 'Erro interno do servidor'
                ]
            ];
        }
    }

    /**
     * Processa pagamento PIX (Cash-out) - Pagar.me
     */
    public static function requestPaymentPagarme($request)
    {
        try {
            $data = $request->all();
            $user = User::where('id', $request->user()->id)->first();
            $setting = App::first();

            // Verificar se o IP está autorizado para saques
            $ipCheck = self::checkIPForWithdraw($user, $request);
            if (!$ipCheck['success']) {
                return [
                    "status" => 403,
                    "data" => [
                        "status" => "error",
                        "message" => $ipCheck['message'],
                        "client_ip" => $ipCheck['client_ip']
                    ]
                ];
            }

            Log::info('=== PAGARMETRAIT REQUEST PAYMENT INICIADO ===');
            Log::info('PagarMeTrait::requestPaymentPagarme - Dados da requisição:', [
                'user_id' => $user->id,
                'username' => $user->username,
                'amount' => $request->amount,
                'pix_key' => $request->pixKey,
                'pix_key_type' => $request->pixKeyType,
                'baasPostbackUrl' => $request->baasPostbackUrl,
                'is_interface_web' => $request->input('baasPostbackUrl') === 'web'
            ]);

            // Determinar se é saque via interface web ou API
            $isInterfaceWeb = $request->input('baasPostbackUrl') === 'web';

            // Verificar se deve usar taxa por fora para saques via API
            $taxaPorFora = $setting->taxa_por_fora_api ?? true;

            // Calcula taxas de saque usando o helper centralizado
            $taxaCalculada = \App\Helpers\TaxaSaqueHelper::calcularTaxaSaque((float)$request->amount, $setting, $user, $isInterfaceWeb, $taxaPorFora);
            $cashout_liquido = $taxaCalculada['saque_liquido'];
            $taxa_cash_out = $taxaCalculada['taxa_cash_out'];
            $descricao = $taxaCalculada['descricao'];
            $valor_total_descontar = $taxaCalculada['valor_total_descontar'] ?? $request->amount;

            Log::info('PagarMeTrait::requestPaymentPagarme - Cálculo de taxas:', [
                'amount_original' => $request->amount,
                'taxa_cash_out' => $taxa_cash_out,
                'cashout_liquido' => $cashout_liquido,
                'descricao' => $descricao,
                'user_saldo' => $user->saldo,
                'is_interface_web' => $isInterfaceWeb
            ]);

            // Verificar saldo considerando taxa por fora
            $saldo_necessario = $valor_total_descontar;
            if ($user->saldo < $saldo_necessario) {
                return [
                    'status' => 401,
                    'data' => [
                        'status' => 'error',
                        'message' => "Saldo insuficiente. Necessário: R$ " . number_format($saldo_necessario, 2, ',', '.') . ", Disponível: R$ " . number_format($user->saldo, 2, ',', '.'),
                    ]
                ];
            }

            $date = Carbon::now();

            // Se for web, verificar se é saque automático
            if ($request->baasPostbackUrl === 'web') {
                Log::info('PagarMeTrait::requestPaymentPagarme - Interface web detectada:', [
                    'saque_automatico' => $request->has('saque_automatico') ? $request->saque_automatico : false,
                    'has_saque_automatico' => $request->has('saque_automatico')
                ]);

                if ($request->has('saque_automatico') && $request->saque_automatico) {
                    Log::info('PagarMeTrait::requestPaymentPagarme - Processando saque automático');
                    return self::processarSaqueAutomaticoPagarme($request, $taxa_cash_out, $cashout_liquido, $date, $descricao, $user);
                } else {
                    Log::info('PagarMeTrait::requestPaymentPagarme - Processando saque manual');
                    return self::generateTransactionPaymentManualPagarme($request, $taxa_cash_out, $cashout_liquido, $date, $descricao, $user);
                }
            }

            Log::info('PagarMeTrait::requestPaymentPagarme - Processando via API (não web)');

            if (self::generateCredentialsPagarme()) {
                $callback = url('/api/pagarme/callback/withdraw');
                $client_ip = \App\Traits\IPManagementTrait::getIPForAcquirer($request);

                $keytype = $request->keytype ?? Helper::verifyPixType($request->pixKey);
                $pixKey = $request->pixKey;

                switch ($keytype) {
                    case 'cpf':
                    case 'cnpj':
                    case 'phone':
                        $pixKey = preg_replace('/[^0-9]/', '', $pixKey);
                        break;
                }

                $payload = [
                    "amount" => intval($request->amount * 100),
                    "pixKey" => $pixKey,
                    "pixKeyType" => $keytype,
                    "baasPostbackUrl" => $callback
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-authorization-key' => self::$secret
                ])->post(self::$urlCashOut, $payload);

                Log::debug("Resposta solicitacao saque Body: " . json_encode($response->json()));

                if ($response->successful()) {
                    $responseData = $response->json();
                    $transaction_id = $responseData['id'] ?? Str::uuid()->toString();

                    $solicitacao = SolicitacoesCashOut::create([
                        'user_id' => $user->user_id,
                        'idTransaction' => $transaction_id,
                        'externalreference' => $transaction_id,
                        'amount' => $request->amount,
                        'beneficiaryname' => $request->beneficiary_name ?? $user->name ?? 'Cliente Gateway',
                        'beneficiarydocument' => $request->beneficiary_document ?? $request->pixKey,
                        'pix' => $request->pixKey,
                        'pixkey' => strtolower($keytype),
                        'date' => $date,
                        'status' => 'WAITING_FOR_APPROVAL',
                        'type' => 'PIX',
                        'taxa_cash_out' => $taxa_cash_out,
                        'cash_out_liquido' => $cashout_liquido,
                        'end_to_end' => $transaction_id,
                        'descricao_transacao' => $descricao,
                        'callback' => $request->baasPostbackUrl ?? $callback,
                    ]);

                    Log::info('PagarMeTrait::requestPaymentPagarme - Saque solicitado e salvo com sucesso.', ['idTransaction' => $transaction_id]);

                    // Debitar saldo do usuário imediatamente
                    if ($user) {
                        $valor_para_descontar = $taxaPorFora ? ($request->amount + $taxa_cash_out) : $request->amount;

                        Log::info('PagarMeTrait::requestPaymentPagarme - Descontando saldo:', [
                            'user_id' => $user->user_id,
                            'saldo_antes' => $user->saldo,
                            'valor_para_descontar' => $valor_para_descontar,
                            'taxa_por_fora' => $taxaPorFora
                        ]);

                        Helper::decrementAmount($user, $valor_para_descontar, 'saldo');
                        $user->increment('valor_sacado', $request->amount);

                        // Log específico para saque
                        \App\Helpers\BalanceLogHelper::logSaqueOperation(
                            'SAQUE_REQUEST',
                            $user,
                            $request->amount,
                            [
                                'adquirente' => 'PAGARME',
                                'valor_bruto' => $request->amount,
                                'valor_descontado' => $valor_para_descontar,
                                'taxa_cash_out' => $taxa_cash_out,
                                'taxa_por_fora' => $taxaPorFora,
                                'external_id' => $transaction_id,
                                'operacao' => 'requestPaymentPagarme'
                            ]
                        );

                        Log::info('PagarMeTrait::requestPaymentPagarme - Saldo debitado com sucesso:', [
                            'user_id' => $user->user_id,
                            'saldo_depois' => $user->fresh()->saldo,
                            'valor_debitado' => $valor_para_descontar
                        ]);
                    }

                    return [
                        'status' => 200,
                        'data' => [
                            'status' => 'WAITING_FOR_APPROVAL',
                            'idTransaction' => $transaction_id,
                            'message' => 'Saque solicitado com sucesso. Aguardando processamento.',
                            'amount' => $request->amount,
                            'cash_out_liquido' => $cashout_liquido,
                            'taxa_cash_out' => $taxa_cash_out,
                        ]
                    ];
                } else {
                    Log::error('PagarMeTrait::requestPaymentPagarme - Erro ao solicitar saque:', ['response' => $response->json()]);
                    return [
                        'status' => 400,
                        'data' => [
                            'status' => 'error',
                            'message' => 'Erro ao solicitar saque PIX',
                            'details' => $response->json()
                        ]
                    ];
                }
            } else {
                return [
                    'status' => 500,
                    'data' => [
                        'status' => 'error',
                        'message' => 'Credenciais não configuradas'
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error('PagarMeTrait::requestPaymentPagarme - Erro inesperado: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => [
                    'status' => 'error',
                    'message' => 'Erro interno ao solicitar saque PIX',
                    'details' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Processa saque automático via Pagar.me
     */
    private static function processarSaqueAutomaticoPagarme($request, $taxa_cash_out, $cashout_liquido, $date, $descricao, $user)
    {
        Log::info('PagarMeTrait::processarSaqueAutomaticoPagarme - Iniciando processamento automático.');

        if (self::generateCredentialsPagarme()) {
            $callback = url('/api/pagarme/callback/withdraw');
            $client_ip = \App\Traits\IPManagementTrait::getIPForAcquirer($request);

            $keytype = $request->keytype ?? Helper::verifyPixType($request->pixKey);
            $pixKey = $request->pixKey;

            switch ($keytype) {
                case 'cpf':
                case 'cnpj':
                case 'phone':
                    $pixKey = preg_replace('/[^0-9]/', '', $pixKey);
                    break;
            }

            $payload = [
                "amount" => intval($request->amount * 100),
                "pixKey" => $pixKey,
                "pixKeyType" => $keytype,
                "baasPostbackUrl" => $callback,
                "client_ip" => $client_ip
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'x-authorization-key' => self::$secret,
            ])->post(self::$urlCashOut, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                $transaction_id = $responseData['id'] ?? Str::uuid()->toString();

                $solicitacao = SolicitacoesCashOut::create([
                    'user_id' => $user->user_id,
                    'idTransaction' => $transaction_id,
                    'externalreference' => $transaction_id,
                    'amount' => $request->amount,
                    'beneficiaryname' => $request->beneficiary_name ?? $user->name ?? 'Cliente Gateway',
                    'beneficiarydocument' => $request->beneficiary_document ?? $request->pixKey,
                    'pix' => $request->pixKey,
                    'pixkey' => strtolower($keytype),
                    'date' => $date,
                    'status' => 'COMPLETED',
                    'type' => 'PIX',
                    'taxa_cash_out' => $taxa_cash_out,
                    'cash_out_liquido' => $cashout_liquido,
                    'end_to_end' => $transaction_id,
                    'descricao_transacao' => $descricao,
                    'callback' => $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
                ]);

                Log::info('PagarMeTrait::processarSaqueAutomaticoPagarme - Saque automático solicitado e salvo com sucesso.', ['idTransaction' => $transaction_id]);

                // Debitar saldo do usuário imediatamente
                if ($user) {
                    $taxaPorFora = \App\Models\App::first()->taxa_por_fora_api ?? true;
                    $valor_para_descontar = $taxaPorFora ? ($request->amount + $taxa_cash_out) : $request->amount;

                    Log::info('PagarMeTrait::processarSaqueAutomaticoPagarme - Descontando saldo:', [
                        'user_id' => $user->user_id,
                        'saldo_antes' => $user->saldo,
                        'valor_para_descontar' => $valor_para_descontar,
                        'taxa_por_fora' => $taxaPorFora
                    ]);

                    Helper::decrementAmount($user, $valor_para_descontar, 'saldo');
                    $user->increment('valor_sacado', $request->amount);

                    // Log específico para saque
                    \App\Helpers\BalanceLogHelper::logSaqueOperation(
                        'SAQUE_REQUEST',
                        $user,
                        $request->amount,
                        [
                            'adquirente' => 'PAGARME',
                            'valor_bruto' => $request->amount,
                            'valor_descontado' => $valor_para_descontar,
                            'taxa_cash_out' => $taxa_cash_out,
                            'taxa_por_fora' => $taxaPorFora,
                            'external_id' => $transaction_id,
                            'operacao' => 'processarSaqueAutomaticoPagarme'
                        ]
                    );

                    Log::info('PagarMeTrait::processarSaqueAutomaticoPagarme - Saldo debitado com sucesso:', [
                        'user_id' => $user->user_id,
                        'saldo_depois' => $user->fresh()->saldo,
                        'valor_debitado' => $valor_para_descontar
                    ]);
                }

                return [
                    'status' => 200,
                    'data' => [
                        'status' => 'COMPLETED',
                        'idTransaction' => $transaction_id,
                        'message' => 'Saque automático processado com sucesso.',
                        'amount' => $request->amount,
                        'cash_out_liquido' => $cashout_liquido,
                        'taxa_cash_out' => $taxa_cash_out,
                    ]
                ];
            } else {
                Log::error('PagarMeTrait::processarSaqueAutomaticoPagarme - Erro ao solicitar saque automático:', ['response' => $response->json()]);
                return [
                    'status' => 400,
                    'data' => [
                        'status' => 'error',
                        'message' => 'Erro ao solicitar saque automático',
                        'details' => $response->json()
                    ]
                ];
            }
        } else {
            return [
                'status' => 500,
                'data' => [
                    'status' => 'error',
                    'message' => 'Credenciais não configuradas'
                ]
            ];
        }
    }

    /**
     * Gera uma transação de pagamento manual (para aprovação) via Pagar.me
     */
    private static function generateTransactionPaymentManualPagarme($request, $taxa_cash_out, $cashout_liquido, $date, $descricao, $user)
    {
        Log::info('PagarMeTrait::generateTransactionPaymentManualPagarme - Gerando solicitação de saque manual.');
        $externalId = Str::uuid()->toString();

        SolicitacoesCashOut::create([
            'user_id' => $user->user_id,
            'idTransaction' => $externalId,
            'externalreference' => $externalId,
            'amount' => $request->amount,
            'beneficiaryname' => $request->beneficiary_name ?? $user->name ?? 'Cliente Gateway',
            'beneficiarydocument' => $request->beneficiary_document ?? $request->pixKey,
            'pix' => $request->pixKey,
            'pixkey' => strtolower($request->pixKeyType),
            'date' => $date,
            'status' => 'WAITING_FOR_APPROVAL',
            'type' => 'PIX',
            'taxa_cash_out' => $taxa_cash_out,
            'cash_out_liquido' => $cashout_liquido,
            'end_to_end' => $externalId,
            'descricao_transacao' => $descricao,
            'callback' => $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
        ]);

        Log::info('PagarMeTrait::generateTransactionPaymentManualPagarme - Solicitação de saque manual criada com sucesso.', ['idTransaction' => $externalId]);

        return [
            'status' => 200,
            'data' => [
                'status' => 'WAITING_FOR_APPROVAL',
                'idTransaction' => $externalId,
                'message' => 'Solicitação de saque criada com sucesso. Aguardando aprovação manual.',
                'amount' => $request->amount,
                'cash_out_liquido' => $cashout_liquido,
                'taxa_cash_out' => $taxa_cash_out,
            ]
        ];
    }

    /**
     * Processa pagamento com cartão de crédito - Pagar.me
     */
    public static function processCardPaymentPagarme($request)
    {
        try {
            $data = $request->all();
            $user = User::where('id', $request->user()->id)->first();
            $setting = App::first();

            Log::info('=== PAGARMETRAIT PROCESS CARD PAYMENT INICIADO ===');
            Log::info('PagarMeTrait::processCardPaymentPagarme - Dados da requisição:', [
                'user_id' => $user->id,
                'username' => $user->username,
                'amount' => $request->amount,
                'installments' => $request->installments ?? 1,
                'card_brand' => $request->card_brand ?? 'N/A'
            ]);

            // Determinar se é pagamento via interface web ou API
            $isInterfaceWeb = $request->input('baasPostbackUrl') === 'web';

            // Calcula taxas de depósito (cash-in) usando sistema flexível
            $taxaCalculada = \App\Helpers\TaxaFlexivelHelper::calcularTaxaDeposito($request->amount, $setting, $user);
            $deposito_liquido = $taxaCalculada['deposito_liquido'];
            $taxa_cash_in = $taxaCalculada['taxa_cash_in'];
            $descricao = $taxaCalculada['descricao'];

            Log::info('PagarMeTrait::processCardPaymentPagarme - Cálculo de taxas:', [
                'amount_original' => $request->amount,
                'taxa_cash_in' => $taxa_cash_in,
                'deposito_liquido' => $deposito_liquido,
                'descricao' => $descricao,
                'is_interface_web' => $isInterfaceWeb
            ]);

            $date = Carbon::now();
            $externalId = Str::uuid()->toString();

            // Criar solicitação de depósito
            $solicitacao = Solicitacoes::create([
                'user_id' => $user->user_id,
                'idTransaction' => $externalId,
                'externalreference' => $externalId,
                'amount' => $request->amount,
                'deposito_liquido' => $deposito_liquido,
                'taxa_cash_in' => $taxa_cash_in,
                'taxa_pix_cash_in_adquirente' => $taxa_cash_in,
                'taxa_pix_cash_in_valor_fixo' => 0,
                'status' => 'WAITING_FOR_APPROVAL',
                'adquirente_ref' => 'pagarme',
                'client_name' => $request->customer_name ?? $request->debtor_name ?? 'Cliente',
                'client_document' => $request->customer_document ?? $request->debtor_document_number ?? '08355037120',
                'client_email' => $request->customer_email ?? $request->email ?? 'redacted@example.invalid',
                'client_telefone' => $request->customer_phone ?? $request->phone ?? '08355037120',
                'date' => $date,
                'executor_ordem' => 'pagarme',
                'descricao_transacao' => $descricao,
                'qrcode_pix' => '',
                'paymentcode' => '',
                'paymentCodeBase64' => '',
                'callback' => $request->postback_url ?? $user->webhook_url ?? $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
                'created_at' => $date,
                'updated_at' => $date
            ]);

            Log::info('PagarMeTrait::processCardPaymentPagarme - Solicitação criada:', [
                'solicitacao_id' => $solicitacao->id,
                'external_id' => $externalId
            ]);

            // UTMfy integration
            if (!is_null($user->integracao_utmfy)) {
                $ip = $request->header('X-Forwarded-For') ?
                    $request->header('X-Forwarded-For') : ($request->header('CF-Connecting-IP') ?
                        $request->header('CF-Connecting-IP') :
                        $request->ip());

                $msg = "Cartão de Crédito Gerado " . env('APP_NAME');
                UtmfyTrait::gerarUTM('credit_card', 'waiting_payment', $solicitacao->toArray(), $user->integracao_utmfy, $ip, $msg);
            }

            // Processar pagamento com cartão via Pagar.me
            if (self::generateCredentialsPagarme()) {
                $client_ip = $request->ip();
                $access_secret = base64_encode(self::$secret);
                $gerarpessoa = self::gerarPessoa();
                $client_code = uniqid(strtoupper(str_replace(' ', '_', env('APP_NAME'))) . '_');

                // Preparar dados do cartão
                $cardData = [
                    'number' => $request->card_number ?? '',
                    'holder_name' => $request->card_holder_name ?? $gerarpessoa['nome'],
                    'exp_month' => $request->card_exp_month ?? '12',
                    'exp_year' => $request->card_exp_year ?? '2030',
                    'cvv' => $request->card_cvv ?? '123'
                ];

                $payload = [
                    "customer" => [
                        "phones" => [
                            "mobile_phone" => [
                                "country_code" => "55",
                                "area_code" => self::ajustePhone('' . $gerarpessoa['celular'])['ddd'],
                                "number" => self::ajustePhone('' . $gerarpessoa['celular'])['phone']
                            ]
                        ],
                        "name" => $gerarpessoa['nome'],
                        "document" => str_replace([".", "-"], "", $gerarpessoa['cpf']),
                        "email" => $gerarpessoa['email'],
                        "type" => "individual",
                        "document_type" => "CPF"
                    ],
                    "payments" => [
                        [
                            "credit_card" => [
                                "installments" => intval($request->installments ?? 1),
                                "statement_descriptor" => env('APP_NAME', 'EXEMPLO'),
                                "card" => $cardData
                            ],
                            "payment_method" => "credit_card"
                        ]
                    ],
                    "items" => [
                        [
                            "amount" => intval($request['amount'] * 100),
                            "code" => $client_code,
                            "quantity" => 1,
                            "description" => "Pagamento Cartão $client_code"
                        ]
                    ]
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . $access_secret
                ])->post(self::$urlCashIn, $payload);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $transaction_id = $responseData['id'];

                    // Atualizar solicitação com dados da transação
                    $solicitacao->update([
                        'externalreference' => $transaction_id,
                        'idTransaction' => $transaction_id,
                        'paymentcode' => $responseData['charges'][0]['last_transaction']['id'] ?? '',
                        'qrcode_pix' => '', // Cartão não tem QR Code
                        'paymentCodeBase64' => ''
                    ]);

                    Log::info('PagarMeTrait::processCardPaymentPagarme - Pagamento com cartão processado com sucesso');

                    return [
                        'status' => 200,
                        'data' => [
                            'idTransaction' => $transaction_id,
                            'status' => $responseData['status'] ?? 'pending',
                            'amount' => $request->amount,
                            'installments' => $request->installments ?? 1,
                            'payment_method' => 'credit_card',
                            'charge' => [
                                'id' => $transaction_id,
                                'status' => $responseData['status'] ?? 'pending'
                            ]
                        ]
                    ];
                } else {
                    Log::error('PagarMeTrait::processCardPaymentPagarme - Erro ao processar cartão:', ['response' => $response->json()]);
                    $solicitacao->delete();
                    return [
                        'status' => 500,
                        'data' => [
                            'status' => 'error',
                            'message' => 'Erro ao processar pagamento com cartão'
                        ]
                    ];
                }
            } else {
                Log::error('PagarMeTrait::processCardPaymentPagarme - Credenciais não configuradas');
                $solicitacao->delete();
                return [
                    'status' => 500,
                    'data' => [
                        'status' => 'error',
                        'message' => 'Credenciais não configuradas'
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error('PagarMeTrait::processCardPaymentPagarme - Exceção: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => [
                    'status' => 'error',
                    'message' => 'Erro interno do servidor'
                ]
            ];
        }
    }

    /**
     * Processa pagamento com boleto bancário - Pagar.me
     */
    public static function processBoletoPaymentPagarme($request)
    {
        try {
            $data = $request->all();
            $user = User::where('id', $request->user()->id)->first();
            $setting = App::first();

            Log::info('=== PAGARMETRAIT PROCESS BOLETO PAYMENT INICIADO ===');
            Log::info('PagarMeTrait::processBoletoPaymentPagarme - Dados da requisição:', [
                'user_id' => $user->id,
                'username' => $user->username,
                'amount' => $request->amount
            ]);

            // Determinar se é pagamento via interface web ou API
            $isInterfaceWeb = $request->input('baasPostbackUrl') === 'web';

            // Calcula taxas de depósito (cash-in) usando sistema flexível
            $taxaCalculada = \App\Helpers\TaxaFlexivelHelper::calcularTaxaDeposito($request->amount, $setting, $user);
            $deposito_liquido = $taxaCalculada['deposito_liquido'];
            $taxa_cash_in = $taxaCalculada['taxa_cash_in'];
            $descricao = $taxaCalculada['descricao'];

            Log::info('PagarMeTrait::processBoletoPaymentPagarme - Cálculo de taxas:', [
                'amount_original' => $request->amount,
                'taxa_cash_in' => $taxa_cash_in,
                'deposito_liquido' => $deposito_liquido,
                'descricao' => $descricao,
                'is_interface_web' => $isInterfaceWeb
            ]);

            $date = Carbon::now();
            $externalId = Str::uuid()->toString();

            // Criar solicitação de depósito
            $solicitacao = Solicitacoes::create([
                'user_id' => $user->user_id,
                'idTransaction' => $externalId,
                'externalreference' => $externalId,
                'amount' => $request->amount,
                'deposito_liquido' => $deposito_liquido,
                'taxa_cash_in' => $taxa_cash_in,
                'taxa_pix_cash_in_adquirente' => $taxa_cash_in,
                'taxa_pix_cash_in_valor_fixo' => 0,
                'status' => 'WAITING_FOR_APPROVAL',
                'adquirente_ref' => 'pagarme',
                'client_name' => $request->customer_name ?? $request->debtor_name ?? 'Cliente',
                'client_document' => $request->customer_document ?? $request->debtor_document_number ?? '08355037120',
                'client_email' => $request->customer_email ?? $request->email ?? 'redacted@example.invalid',
                'client_telefone' => $request->customer_phone ?? $request->phone ?? '08355037120',
                'date' => $date,
                'executor_ordem' => 'pagarme',
                'descricao_transacao' => $descricao,
                'qrcode_pix' => '',
                'paymentcode' => '',
                'paymentCodeBase64' => '',
                'callback' => $request->postback_url ?? $user->webhook_url ?? $request->baasPostbackUrl === 'web' ? null : $request->baasPostbackUrl,
                'created_at' => $date,
                'updated_at' => $date
            ]);

            Log::info('PagarMeTrait::processBoletoPaymentPagarme - Solicitação criada:', [
                'solicitacao_id' => $solicitacao->id,
                'external_id' => $externalId
            ]);

            // UTMfy integration
            if (!is_null($user->integracao_utmfy)) {
                $ip = $request->header('X-Forwarded-For') ?
                    $request->header('X-Forwarded-For') : ($request->header('CF-Connecting-IP') ?
                        $request->header('CF-Connecting-IP') :
                        $request->ip());

                $msg = "Boleto Gerado " . env('APP_NAME');
                UtmfyTrait::gerarUTM('boleto', 'waiting_payment', $solicitacao->toArray(), $user->integracao_utmfy, $ip, $msg);
            }

            // Processar pagamento com boleto via Pagar.me
            if (self::generateCredentialsPagarme()) {
                $client_ip = $request->ip();
                $access_secret = base64_encode(self::$secret);
                $gerarpessoa = self::gerarPessoa();
                $client_code = uniqid(strtoupper(str_replace(' ', '_', env('APP_NAME'))) . '_');

                $payload = [
                    "customer" => [
                        "phones" => [
                            "mobile_phone" => [
                                "country_code" => "55",
                                "area_code" => self::ajustePhone('' . $gerarpessoa['celular'])['ddd'],
                                "number" => self::ajustePhone('' . $gerarpessoa['celular'])['phone']
                            ]
                        ],
                        "name" => $gerarpessoa['nome'],
                        "document" => str_replace([".", "-"], "", $gerarpessoa['cpf']),
                        "email" => $gerarpessoa['email'],
                        "type" => "individual",
                        "document_type" => "CPF"
                    ],
                    "payments" => [
                        [
                            "boleto" => [
                                "instructions" => "Pagamento referente ao pedido $client_code",
                                "due_at" => Carbon::now()->addDays(3)->format('Y-m-d\TH:i:s\Z')
                            ],
                            "payment_method" => "boleto"
                        ]
                    ],
                    "items" => [
                        [
                            "amount" => intval($request['amount'] * 100),
                            "code" => $client_code,
                            "quantity" => 1,
                            "description" => "Pagamento Boleto $client_code"
                        ]
                    ]
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . $access_secret
                ])->post(self::$urlCashIn, $payload);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $transaction_id = $responseData['id'];
                    $boletoUrl = $responseData['charges'][0]['last_transaction']['url'] ?? '';
                    $boletoBarcode = $responseData['charges'][0]['last_transaction']['line'] ?? '';

                    // Atualizar solicitação com dados do boleto
                    $solicitacao->update([
                        'externalreference' => $transaction_id,
                        'idTransaction' => $transaction_id,
                        'paymentcode' => $boletoUrl,
                        'qrcode_pix' => $boletoBarcode,
                        'paymentCodeBase64' => $boletoUrl ? 'data:image/png;base64,' . base64_encode($boletoUrl) : ''
                    ]);

                    Log::info('PagarMeTrait::processBoletoPaymentPagarme - Boleto gerado com sucesso');

                    return [
                        'status' => 200,
                        'data' => [
                            'idTransaction' => $transaction_id,
                            'status' => $responseData['status'] ?? 'pending',
                            'amount' => $request->amount,
                            'payment_method' => 'boleto',
                            'boleto_url' => $boletoUrl,
                            'boleto_barcode' => $boletoBarcode,
                            'charge' => [
                                'id' => $transaction_id,
                                'status' => $responseData['status'] ?? 'pending',
                                'boletoUrl' => $boletoUrl,
                                'boletoBarcode' => $boletoBarcode
                            ]
                        ]
                    ];
                } else {
                    Log::error('PagarMeTrait::processBoletoPaymentPagarme - Erro ao gerar boleto:', ['response' => $response->json()]);
                    $solicitacao->delete();
                    return [
                        'status' => 500,
                        'data' => [
                            'status' => 'error',
                            'message' => 'Erro ao gerar boleto bancário'
                        ]
                    ];
                }
            } else {
                Log::error('PagarMeTrait::processBoletoPaymentPagarme - Credenciais não configuradas');
                $solicitacao->delete();
                return [
                    'status' => 500,
                    'data' => [
                        'status' => 'error',
                        'message' => 'Credenciais não configuradas'
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error('PagarMeTrait::processBoletoPaymentPagarme - Exceção: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => [
                    'status' => 'error',
                    'message' => 'Erro interno do servidor'
                ]
            ];
        }
    }

    /**
     * Libera saque manual (aprovação pelo admin) para Pagar.me
     */
    public static function liberarSaqueManualPagarme($id)
    {
        try {
            Log::info('PagarMeTrait::liberarSaqueManualPagarme - Iniciando liberação manual', ['id' => $id]);

            $cashout = SolicitacoesCashOut::where('id', $id)->first();
            if (!$cashout) {
                Log::warning('PagarMeTrait::liberarSaqueManualPagarme - Solicitação não encontrada', ['id' => $id]);
                return back()->with('error', 'Solicitação de saque não encontrada.');
            }

            if (self::generateCredentialsPagarme()) {
                $callback = url('/api/pagarme/callback/withdraw');

                $payload = [
                    "amount" => intval($cashout->amount * 100),
                    "pixKey" => $cashout->pix,
                    "pixKeyType" => $cashout->pixkey,
                    "baasPostbackUrl" => $callback
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-authorization-key' => self::$secret,
                ])->post(self::$urlCashOut, $payload);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $transaction_id = $responseData['id'] ?? Str::uuid()->toString();

                    $update = [
                        'status' => 'COMPLETED',
                        'descricao_transacao' => 'LIBERADOADMIN',
                        'externalreference' => $transaction_id,
                        'idTransaction' => $transaction_id,
                        'end_to_end' => $transaction_id,
                    ];

                    SolicitacoesCashOut::where('id', $id)->update($update);
                    Log::info('PagarMeTrait::liberarSaqueManualPagarme - Solicitação atualizada com sucesso', ['id' => $id, 'update' => $update]);
                    return back()->with('success', "Saque liberado e enviado com sucesso!");
                } else {
                    Log::error('PagarMeTrait::liberarSaqueManualPagarme - Erro ao enviar saque:', ['response' => $response->json()]);
                    return back()->with('error', 'Erro ao enviar saque para Pagar.me.');
                }
            } else {
                Log::error('PagarMeTrait::liberarSaqueManualPagarme - Credenciais não configuradas');
                return back()->with('error', 'Credenciais não configuradas.');
            }
        } catch (\Exception $e) {
            Log::error('PagarMeTrait::liberarSaqueManualPagarme - Exceção', ['message' => $e->getMessage()]);
            return back()->with('error', 'Erro ao liberar saque manual.');
        }
    }

    protected static function ajustePhone($string)
    {
        // Verifica se "55" aparece antes de "62"
        if (strpos($string, "55") === 0 && strpos($string, "62") === 2) {
            // Pega os dois primeiros números (62) como DDD
            $ddd = substr($string, 2, 2); // Pega "62"
            // Remove o "55" e pega o restante do número
            $phone = substr($string, 4); // Pega "981313984"
        } else {
            // Se não tiver o 55 antes do 62, apenas divide normalmente
            $ddd = substr($string, 0, 2); // Pega os dois primeiros números (DDD)
            $phone = substr($string, 2);   // Pega o restante do número
        }

        return [
            'ddd' => $ddd,
            'phone' => $phone
        ];
    }

    public static function gerarPessoa()
    {
        $url = "https://www.4devs.com.br/ferramentas_online.php";
        $request = "acao=gerar_pessoa&sexo=I&pontuacao=N&idade=0&cep_estado=&txt_qtde=1&cep_cidade=";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded",
                "Referer: https://www.4devs.com.br/gerador_de_pessoas",
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36 OPR/114.0.0.0",
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        if ($response) {
            $dados = json_decode($response, true);
            if (isset($dados[0]['nome']) && isset($dados[0]['cpf']) && isset($dados[0]['email'])) {
                return $dados[0]; // Retorna o primeiro registro do JSON
            }
        }

        return null; // Falha ao gerar os dados
    }

    /**
     * Garante que o idTransaction seja único, gerando um novo se necessário
     */
    private static function ensureUniqueTransactionId($requestedId, $userId)
    {
        if (empty($requestedId)) {
            return Str::uuid()->toString();
        }

        // Verificar se já existe um registro com esse ID
        $existing = SolicitacoesCashOut::where('idTransaction', $requestedId)->first();

        if (!$existing) {
            // ID não existe, pode usar
            return $requestedId;
        }

        // ID já existe - verificar status
        if (in_array($existing->status, ['CANCELLED', 'FAILED', 'REJECTED'])) {
            // Registro antigo cancelado/falho - gerar novo ID único
            $newId = $requestedId . '_' . time();
            Log::warning('PagarMeTrait::ensureUniqueTransactionId - ID duplicado encontrado (status: ' . $existing->status . '), gerando novo:', [
                'id_original' => $requestedId,
                'id_novo' => $newId,
                'user_id' => $userId,
                'registro_existente_id' => $existing->id,
                'status_existente' => $existing->status
            ]);
            return $newId;
        }

        // ID já existe e está ativo - gerar completamente novo
        $newId = Str::uuid()->toString();
        Log::warning('PagarMeTrait::ensureUniqueTransactionId - ID duplicado em uso ativo, gerando UUID novo:', [
            'id_original' => $requestedId,
            'id_novo' => $newId,
            'user_id' => $userId,
            'registro_existente_id' => $existing->id,
            'status_existente' => $existing->status
        ]);
        return $newId;
    }
}
