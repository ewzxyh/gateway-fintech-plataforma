<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Adquirente;
use App\Models\CheckoutBuild;
use App\Models\CheckoutDepoimento;
use App\Models\CheckoutOrders;
use App\Models\SolicitacoesCashOut;
use App\Models\Solicitacoes;
use App\Models\UsersKey;
use App\Models\User;
use App\Traits\ApiTrait;
use App\Helpers\Helper;
use App\Traits\{PagarMeTrait, EfiTrait, MercadoPagoTrait, CashtimeTrait, XgateTrait, WitetecTrait, PixupTrait, WooviTrait, PrimePay7Trait, NxPayTrait};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckoutControlller extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $query = CheckoutBuild::where("user_id", auth()->id());

        if (!is_null($buscar)) {
            $query->where('produto_name', 'LIKE', "%$buscar%");
        }

        $checkouts = $query->get();

        return view("profile.checkout.index", compact("checkouts"));
    }

    public function indexEdit($id, Request $request)
    {
        $checkout = CheckoutBuild::where('id_unico', $id)->firstOrFail();
        return view("profile.checkout.edit", compact('checkout'));
    }

    public function detalhesPedido($id, Request $request)
    {
        try {
            $order = CheckoutOrders::with('checkout')->findOrFail($id);

            // Verificar se o pedido pertence ao usuário logado
            if ($order->checkout->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            // Buscar eventos/histórico do pedido (simulado por enquanto)
            $eventos = [
                [
                    'status' => $order->status,
                    'data' => $order->created_at->format('d/m/Y H:i:s')
                ]
            ];

            // Adicionar eventos baseados no status
            if ($order->status === 'pago') {
                $eventos[] = [
                    'status' => 'gerado',
                    'data' => $order->created_at->format('d/m/Y H:i:s')
                ];
            }

            // Detectar método de pagamento correto baseado no ID da transação
            $metodoCorreto = $order->metodo;

            // Padrões de IDs para pagamentos via CARTÃO
            $padroesCartao = [
                // Stripe
                'pi_',
                'ch_',
                'pm_',
                'src_',
                // Rede
                'rede_',
                'rd_',
                'rde_',
                // PrimePay
                'pp_',
                'prime_',
                'primepay_',
                // PagarMe
                'pg_',
                'pagar_',
                'pagarme_',
                // Cielo
                'cielo_',
                'cl_',
                // Stone
                'stone_',
                'st_',
                // GetNet
                'getnet_',
                'gn_',
                // Mercado Pago
                'mp_',
                'mercadopago_',
                // Outros padrões comuns de cartão
                'card_',
                'credit_',
                'debit_',
                'visa_',
                'master_',
                'amex_'
            ];

            // Padrões de IDs para pagamentos via PIX
            $padroesPix = [
                // Padrões comuns de PIX
                'dep_',
                'pix_',
                'pixup_',
                'xdpag_',
                'bspay_',
                // Outros adquirentes PIX
                'pix_',
                'pixup_',
                'xdpag_',
                'bspay_',
                'pixpay_',
                'pixgo_',
                'pixnow_',
                'pixpro_',
                'pixflow_',
                'pixnet_',
                'pixmax_',
                // Padrões genéricos
                'qr_',
                'qrcode_',
                'instant_',
                'immediate_'
            ];

            // Verificar se é pagamento via cartão
            $isCardPayment = false;
            if ($order->idTransaction) {
                $idLower = strtolower($order->idTransaction);
                foreach ($padroesCartao as $padrao) {
                    if (str_starts_with($idLower, $padrao)) {
                        $isCardPayment = true;
                        break;
                    }
                }
            }

            // Verificar se é pagamento via PIX
            $isPixPayment = false;
            if ($order->idTransaction) {
                $idLower = strtolower($order->idTransaction);
                foreach ($padroesPix as $padrao) {
                    if (str_starts_with($idLower, $padrao)) {
                        $isPixPayment = true;
                        break;
                    }
                }
                // Verificar se contém palavras-chave PIX
                if (!$isPixPayment && (
                    strpos($idLower, 'pix') !== false ||
                    strpos($idLower, 'qr') !== false ||
                    strpos($idLower, 'instant') !== false
                )) {
                    $isPixPayment = true;
                }
            }

            // Aplicar detecção inteligente
            if ($isCardPayment) {
                $metodoCorreto = 'card';
            } elseif ($isPixPayment) {
                $metodoCorreto = 'pix';
            } elseif ($order->credit_card && $order->credit_card !== null) {
                $metodoCorreto = 'card';
            } elseif ($order->metodo === 'pix') {
                $metodoCorreto = 'pix';
            } elseif ($order->metodo === 'card') {
                $metodoCorreto = 'card';
            }

            $orderData = [
                'id' => $order->id,
                'name' => $order->name,
                'cpf' => $order->cpf,
                'telefone' => $order->telefone,
                'email' => $order->email,
                'endereco' => $order->endereco,
                'numero' => $order->numero,
                'bairro' => $order->bairro,
                'cidade' => $order->cidade,
                'estado' => $order->estado,
                'cep' => $order->cep,
                'valor_total' => $order->valor_total,
                'status' => $order->status,
                'metodo' => $metodoCorreto, // Usar método corrigido
                'metodo_original' => $order->metodo, // Manter método original para debug
                'detection_debug' => [
                    'is_card_payment' => $isCardPayment,
                    'is_pix_payment' => $isPixPayment,
                    'id_transaction' => $order->idTransaction,
                    'has_credit_card_data' => !is_null($order->credit_card)
                ],
                'credit_card' => $order->credit_card,
                'idTransaction' => $order->idTransaction,
                'created_at' => $order->created_at->format('d/m/Y H:i:s'),
                'produto_name' => $order->checkout->produto_name,
                'produto_tipo' => $order->checkout->produto_tipo,
                'eventos' => $eventos
            ];

            return response()->json([
                'success' => true,
                'order' => $orderData
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar detalhes do pedido:', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar detalhes do pedido'
            ], 500);
        }
    }

    public function v1($id, Request $request)
    {
        $checkout = CheckoutBuild::where("id_unico", $id)->first();
        $user = User::where('id', $checkout->user_id)->first();
        $keys = UsersKey::where('user_id', $user->user_id)->first();
        $token = $keys->token;
        $secret = $keys->secret;

        return view('profile.checkout.v1', compact('checkout', 'secret', 'token'));
    }


    public function create(Request $request)
    {
        // Log temporário para debug
        \Log::info('Dados recebidos no create:', $request->all());

        $validated = $request->validate([
            "produto_name" => "required|string",
            "produto_valor" => "required|numeric|min:0.01",
            "produto_descricao" => "required|string",
            "produto_tipo" => "required|string",
            "produto_tipo_cob" => "required|string",
            "methods" => "required|array|min:1",
            "methods.*" => "in:pix,card,billet"
        ], [
            'produto_valor.required' => 'O preço do produto é obrigatório.',
            'produto_valor.numeric' => 'O preço do produto deve ser um número válido.',
            'produto_valor.min' => 'O preço do produto deve ser maior que R$ 0,00.',
            'produto_name.required' => 'O nome do produto é obrigatório.',
            'produto_descricao.required' => 'A descrição do produto é obrigatória.',
            'produto_tipo.required' => 'O tipo do produto é obrigatório.',
            'produto_tipo_cob.required' => 'O tipo de cobrança é obrigatório.',
            'methods.required' => 'Selecione pelo menos um método de pagamento.',
            'methods.min' => 'Selecione pelo menos um método de pagamento.',
            'methods.*.in' => 'Método de pagamento inválido.'
        ]);

        $data = $request->except(['_token', '_method', '/checkout']);

        $data['user_id'] = auth()->id();
        $data['id_unico'] = Str::uuid();
        $data['produto_valor'] = str_replace([","], '.', $data['produto_valor']);
        $data['methods'] = json_encode($request->methods);
        CheckoutBuild::create($data);
        return redirect()->back()->with('success', 'Checkout cadastrado com sucesso com sucesso!');
    }

    public function edit($id, Request $request)
    {
        // Validação dos campos obrigatórios
        $request->validate([
            "produto_name" => "required|string",
            "produto_valor" => "required|numeric|min:0.01",
            "produto_descricao" => "required|string",
            "produto_tipo" => "required|string",
            "produto_tipo_cob" => "required|string"
        ], [
            'produto_valor.required' => 'O preço do produto é obrigatório.',
            'produto_valor.numeric' => 'O preço do produto deve ser um número válido.',
            'produto_valor.min' => 'O preço do produto deve ser maior que R$ 0,00.',
            'produto_name.required' => 'O nome do produto é obrigatório.',
            'produto_descricao.required' => 'A descrição do produto é obrigatória.',
            'produto_tipo.required' => 'O tipo do produto é obrigatório.',
            'produto_tipo_cob.required' => 'O tipo de cobrança é obrigatório.'
        ]);

        // Criamos o registro sem as imagens
        $checkoutBuild = CheckoutBuild::where('id', $id)->first();
        $checkoutDir = public_path("/checkouts/{$checkoutBuild->id}/");
        if (!file_exists($checkoutDir)) {
            mkdir($checkoutDir, 0755, true);
        }
        $data = collect($request->all())
            ->reject(function ($value, $key) {
                return preg_match('/^checkout_depoimentos_/', $key)
                    || in_array($key, ['_token', '_method', 'checkout_depoimentos_id', 'checkout_depoimentos_nome', 'checkout_depoimentos_depoimento', 'checkout_depoimentos_image']);
            })
            ->toArray();

        $data['methods'] = json_encode($request->methods);
        $data['produto_valor'] = str_replace([","], '.', $data['produto_valor']);

        // Processar URL da página de obrigado
        if ($request->has('url_pagina_vendas_default') && !empty($request->url_pagina_vendas_default)) {
            // Se está usando página padrão, usar a URL padrão
            $data['url_pagina_vendas'] = $request->url_pagina_vendas_default;
        } elseif (empty($data['url_pagina_vendas'])) {
            // Se não tem URL personalizada, usar página padrão
            $data['url_pagina_vendas'] = url('/obrigado?order_id=ORDER_ID');
        }

        // Garantir que a URL tenha o placeholder ORDER_ID para substituição posterior
        if (!str_contains($data['url_pagina_vendas'], 'ORDER_ID') && !str_contains($data['url_pagina_vendas'], 'transaction_id')) {
            $data['url_pagina_vendas'] = url('/obrigado?order_id=ORDER_ID');
        }

        // Processar campos booleanos corretamente
        $data['checkout_timer_active'] = $request->has('checkout_timer_active');
        $data['checkout_header_logo_active'] = $request->has('checkout_header_logo_active');
        $data['checkout_header_image_active'] = $request->has('checkout_header_image_active');
        $data['checkout_banner_active'] = $request->has('checkout_banner_active');
        $data['checkout_topbar_active'] = $request->has('checkout_topbar_active');

        // Atualiza campos principais
        $checkoutBuild->update($data);

        // Atualiza imagens únicas como produto/banner/logo/etc
        $images_checkout = ['produto_image', 'checkout_header_logo', 'checkout_header_image', 'checkout_banner'];
        $dataImg = [];

        foreach ($images_checkout as $field) {
            if ($request->hasFile($field)) {
                $filename = 'checkout_' . $field . '.' . $request->file($field)->getClientOriginalExtension();
                $request->file($field)->move($checkoutDir, $filename);
                $dataImg[$field] = "/checkouts/{$checkoutBuild->id}/{$filename}";
            }
        }

        // Atualiza imagens únicas, se houver
        if (!empty($dataImg)) {
            $checkoutBuild->update($dataImg);
        }

        return redirect()->back()->with('success', 'Checkout alterado com sucesso!');
    }

    public function destroy($id)
    {
        // Buscar o checkout pelo ID
        $checkout = CheckoutBuild::find($id);

        if (!$checkout) {
            return redirect()->back()->with('error', 'Checkout não encontrado.');
        }

        // Verifica se o usuário autenticado pode excluir esse checkout
        /* if (auth()->user()->user_id !== $checkout->user_id) {
            return redirect()->back()->with('error', 'Você não tem permissão para excluir este checkout.');
        } */

        // Deleta as imagens associadas, se existirem
        if ($checkout->logo_produto) {
            Storage::disk('public')->delete($checkout->logo_produto);
        }
        if ($checkout->banner_produto) {
            Storage::disk('public')->delete($checkout->banner_produto);
        }

        // Exclui o checkout do banco de dados
        $checkout->delete();

        return redirect()->back()->with('success', 'Checkout excluído com sucesso!');
    }

    public function gerarPedido(Request $request)
    {
        try {
            \Log::info('🚀 Iniciando gerarPedido:', [
                'metodo' => $request->metodo,
                'checkout_id' => $request->checkout_id,
                'dados_recebidos' => $request->except(['_token'])
            ]);

            $data = $request->except(['_token']);

            \Log::info('📝 Criando CheckoutOrders com dados:', $data);
            $venda = CheckoutOrders::create($data);
            \Log::info('✅ CheckoutOrders criado com ID:', ['id' => $venda->id]);

            // Buscar o usuário do checkout para usar sua adquirente preferida
            $checkout = CheckoutBuild::where('id', $request->checkout_id)->first();
            if (!$checkout) {
                \Log::error('❌ Checkout não encontrado:', ['checkout_id' => $request->checkout_id]);
                return response()->json(['status' => 'error', 'message' => 'Checkout não encontrado.']);
            }

            $user = User::where('id', $checkout->user_id)->first();
            if (!$user) {
                \Log::error('❌ Usuário não encontrado:', ['user_id' => $checkout->user_id]);
                return response()->json(['status' => 'error', 'message' => 'Usuário não encontrado.']);
            }

            // Determinar o tipo de pagamento para buscar a adquirente correta
            $paymentType = in_array($request->metodo, ['card', 'billet']) ? 'card_billet' : 'pix';
            $default = Helper::adquirenteDefault($user->user_id, $paymentType);

            \Log::info('🏦 Adquirente detectada no gerarPedido:', [
                'default' => $default,
                'metodo' => $request->metodo,
                'payment_type' => $paymentType
            ]);

            // Verificar se é cartão e qual adquirente usar
            if ($request->metodo == 'card') {
                if ($default == 'primepay7') {
                    \Log::info('✅ Redirecionando para processamento PrimePay7');
                    return $this->processCardPaymentPrimePay7($request, $data, $venda);
                } elseif ($default == 'pagarme') {
                    \Log::info('✅ Redirecionando para processamento Pagar.me');
                    return $this->processCardPaymentPagarme($request, $data, $venda);
                } elseif ($default == 'rede') {
                    \Log::info('✅ Redirecionando para processamento Rede e.Rede');
                    return $this->processCardPaymentRede($request, $data, $venda);
                } elseif ($default == 'stripe') {
                    \Log::info('✅ Redirecionando para processamento Stripe');
                    $creditcard = json_decode($data['credit_card']);
                    $cardData = [
                        'number' => $creditcard->number ?? '',
                        'holderName' => $creditcard->holder_name ?? '',
                        'expirationMonth' => $creditcard->expiration_month ?? '',
                        'expirationYear' => $creditcard->expiration_year ?? '',
                        'cvv' => $creditcard->cvv ?? ''
                    ];
                    return $this->processCardPaymentStripe($venda, $cardData, $request);
                }
            }

            // Verificar se é boleto e qual adquirente usar
            if ($request->metodo == 'boleto') {
                if ($default == 'primepay7') {
                    \Log::info('✅ Redirecionando para processamento Boleto PrimePay7');
                    return $this->processBoletoPaymentPrimePay7($request, $data, $venda);
                } elseif ($default == 'pagarme') {
                    \Log::info('✅ Redirecionando para processamento Boleto Pagar.me');
                    return $this->processBoletoPaymentPagarme($request, $data, $venda);
                }
            }

            // Verificar se é cartão PrimePay7 (compatibilidade com código antigo)
            if ($request->metodo == 'card_primepay7') {
                return $this->processCardPaymentPrimePay7($request, $data, $venda);
            }

            if ($request->metodo == 'card') {
                \Log::info('ℹ️ Usando EFI para processar cartão');
                $creditcard = json_decode($data['credit_card']);
                $installment = json_decode($data['installment']);
                $checkout = CheckoutBuild::where('id', $request->checkout_id)->first();
                $user = User::where('id', $checkout->user_id)->first();
                $payload = [
                    "user" => $user,
                    "data" => [
                        "items" => [
                            [
                                'name'      => $checkout->produto_name,
                                'value'     => (int) $data['valor_total'] * 100,
                                'amount'    => 1
                            ]
                        ],
                        "payment" => [
                            "credit_card" => [
                                "customer" => [
                                    "name"          => $data['name'],
                                    "cpf"           => str_replace(['(', ')', ' ', '.', '-', '/'], '', $data['cpf']),
                                    "email"         => $data['email'],
                                    "phone_number"  => str_replace(['(', ')', ' ', '.', '-', '/'], '', $data['telefone']),
                                ],
                                "installments" => $installment->installment,
                                "payment_token" => $data['payment_token']
                            ]
                        ]
                    ]
                ];

                if (!is_null($user->webhook_url) && in_array('gerado', (array) $user->webhook_endpoint)) {
                    Http::withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                        ->post($user->webhook_url, [
                            'nome' => $venda->name,
                            'cpf' => preg_replace('/\D/', '', $venda->cpf),
                            'telefone' => preg_replace('/\D/', '', $venda->telefone),
                            'email' => $venda->email,
                            'status' => 'pendente'
                        ]);
                }
                $newrequest = new Request($payload);
                //            dd($newrequest->all());
                $response = EfiTrait::requestCardEfi($payload);
                //dd($response);
                $status = isset($response['status']) && $response['status'] == 200 ? 'success' : 'error';
                if ($status == "success") {
                    $cahsout = Solicitacoes::where('idTransaction', $response['data']['idTransaction'])->first();
                    $cahsout->update(['descricao_transacao' => 'PRODUTO']);

                    $venda->idTransaction = $response['data']['idTransaction'];
                    $venda->qrcode = "";
                    $venda->save();
                    $valor_text = "R$ " . number_format($venda->valor_total, '2', ',', '.');
                    return response()->json(["status" => $status, "data" => $response['data'], "valor_text" => $valor_text]);
                } else {
                    return response()->json(['status' => 'error', 'message' => $response['message'] ?? "Verifique e tente novamente."]);
                }
            } elseif ($request->metodo == 'billet') {

                $data = $request->all();
                $checkout = CheckoutBuild::where('id', $request->checkout_id)->first();
                $user = User::where('id', $checkout->user_id)->first();
                $payload = [
                    "user" => $user,
                    "items" => [
                        [
                            'name'      => $checkout->produto_name,
                            'value'     => (int) $data['valor_total'] * 100,
                            'amount'    => 1
                        ]
                    ],
                    "payment" => [
                        "banking_billet" => [
                            "customer"          => [
                                "name"          => $data['name'],
                                "cpf"           => str_replace(['(', ')', ' ', '.', '-', '/'], '', $data['cpf']),
                                "email"         => $data['email'],
                                "phone_number"  => str_replace(['(', ')', ' ', '.', '-', '/'], '', $data['telefone']),

                            ],
                            "expire_at" => "2023-12-15",
                            "configurations" => [
                                "fine" => 200,
                                "interest" => 33
                            ]
                        ]
                    ]

                ];

                if (isset($data['cep'])) {
                    $address = [
                        "street"        => $data['endereco'],
                        "number"        => $data['numero'] ?? 0,
                        "neighborhood"  => $data['bairro'],
                        "zipcode"       => str_replace(['.', '-', ' '], '', $data['cep']),
                        "city"          => $data['cidade'],
                        "complement"    => $data['complemento'] ?? "",
                        "state"         => $data['estado']
                    ];

                    $payload['payment']['banking_billet']['customer']["address"] = $address;
                }

                //dd($payload);

                if (!is_null($user->webhook_url) && in_array('gerado', (array) $user->webhook_endpoint)) {
                    Http::withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                        ->post($user->webhook_url, [
                            'nome' => $venda->name,
                            'cpf' => preg_replace('/\D/', '', $venda->cpf),
                            'telefone' => preg_replace('/\D/', '', $venda->telefone),
                            'email' => $venda->email,
                            'status' => 'pendente'
                        ]);
                }
                $newrequest = new Request($payload);
                //            dd($newrequest->all());
                $response = EfiTrait::requestBoletoEfi($newrequest);
                //dd($response);
                $status = isset($response['status']) && $response['status'] == 200 ? 'success' : 'error';
                if ($status == "success") {
                    $cahsout = Solicitacoes::where('idTransaction', $response['data']['idTransaction'])->first();
                    $cahsout->update(['descricao_transacao' => 'PRODUTO']);

                    $venda->idTransaction = $response['data']['idTransaction'];
                    $venda->qrcode = $response['data']['qrcode'];
                    $venda->save();
                    $valor_text = "R$ " . number_format($venda->valor_total, '2', ',', '.');
                    return response()->json(["status" => $status, "data" => $response['data'], "valor_text" => $valor_text]);
                } else {
                    return response()->json(['status' => 'error', 'message' => $response['message'] ?? "Verifique e tente novamente."]);
                }
            }

            if (!$venda) {
                return response()->json(['status' => 'error', 'message' => 'Houve um erro. Tente novamente!']);
            }

            $checkout = CheckoutBuild::where('id', $venda->checkout_id)->first();
            $user = User::where('id', $checkout->user_id)->first();
            $chaves = UsersKey::where('user_id', $user->user_id)->first();

            $dataRequest = [
                'token' => $chaves->token,
                'secret' => $chaves->secret,
                'amount' => $venda->valor_total,
                'debtor_name' => $venda->name,
                'email' => $venda->email,
                'debtor_document_number' => $venda->cpf,
                'phone' => $venda->telefone,
                'method_pay' => 'pix',
                'postback' => 'web',
                'user' => $user
            ];

            if (!is_null($user->webhook_url) && in_array('gerado', (array) $user->webhook_endpoint)) {
                Http::withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                    ->post($user->webhook_url, [
                        'nome' => $venda->name,
                        'cpf' => preg_replace('/\D/', '', $venda->cpf),
                        'telefone' => preg_replace('/\D/', '', $venda->telefone),
                        'email' => $venda->email,
                        'status' => 'pendente'
                    ]);
            }

            $request = new Request($dataRequest);


            switch ($default) {
                case 'cashtime':
                    $response = CashtimeTrait::requestDepositCashtime($request);
                    break;
                case 'mercadopago':
                    $response = MercadoPagoTrait::requestDepositMercadoPago($request);
                    break;
                case 'efi':
                    $response = EfiTrait::requestDepositEfi($request);
                    break;
                case 'pagarme':
                    $response = PagarMeTrait::generateQrCodePagarme($request);
                    break;
                case 'xgate':
                    $response = XgateTrait::requestDepositXgate($request);
                    break;
                case 'witetec':
                    $response = WitetecTrait::requestDepositWitetec($request);
                    break;
                case 'pixup':
                    $response = PixupTrait::requestDepositPixup($request);
                    break;
                case 'woovi':
                    // Para Woovi, usar dados originais do checkout + dados de API
                    $wooviData = array_merge($data, $dataRequest);
                    $wooviRequest = new Request($wooviData);
                    $response = WooviTrait::requestPaymentWoovi($wooviRequest);
                    break;
                case 'primepay7':
                    // Para PrimePay7, usar dados originais do checkout + dados de API
                    $primepay7Data = array_merge($data, $dataRequest);
                    $primepay7Request = new Request($primepay7Data);
                    $response = PrimePay7Trait::generateQrCodePrimePay7($primepay7Request);
                    break;
                case 'nxpay':
                    // Para NxPay, usar dados originais do checkout + dados de API
                    $nxpayData = array_merge($data, $dataRequest);
                    $nxpayRequest = new Request($nxpayData);
                    $response = NxPayTrait::generateQrCodeNxPay($nxpayRequest);
                    break;
                default:
                    \Log::error('❌ Adquirente não mapeada:', ['adquirente' => $default]);
                    $response = ['status' => 400, 'message' => 'Adquirente não configurada'];
                    break;
            }

            $status = isset($response['status']) && $response['status'] == 200 ? 'success' : 'error';
            if ($status == "success") {
                $cahsout = Solicitacoes::where('idTransaction', $response['data']['idTransaction'])->first();
                $cahsout->update(['descricao_transacao' => 'PRODUTO']);

                $venda->idTransaction = $response['data']['idTransaction'];
                $venda->qrcode = $response['data']['qrcode'];
                $venda->save();
                $valor_text = "R$ " . number_format($venda->valor_total, '2', ',', '.');
                return response()->json(["status" => $status, "data" => $response['data'], "valor_text" => $valor_text]);
            } else {
                \Log::error('❌ Erro no processamento PIX:', [
                    'response' => $response,
                    'status' => $status
                ]);
                return response()->json(['status' => 'error', 'message' => "Verifique e tente novamente."]);
            }
        } catch (\Exception $e) {
            \Log::error('❌ Erro geral no gerarPedido:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'dados' => $request->except(['_token'])
            ]);
            return response()->json(['status' => 'error', 'message' => 'Erro interno. Tente novamente.']);
        }
    }

    public function statusPedido(Request $request)
    {
        $data = $request->except(['/checkout/cliente/pedido/status']);
        $order = CheckoutOrders::where('idTransaction', $data['idTransaction'])->first();

        $status = $order->status;
        $message = "Aguardando pagamento...";
        if ($status == 'pago') {
            $message = "Pagamento realizado com sucesso!";
        }
        return response()->json(compact('status', 'message'));
        //dd($data, $order);
    }

    public function salvarDepoimento(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'depoimento' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
            'avatar' => 'nullable|string',
            'id' => 'nullable|string',
            'checkout_id' => 'required'
        ]);

        $depoimento = [
            'id' => $validated['id'],
            'nome' => $validated['nome'],
            'depoimento' => $validated['depoimento'],
            'avatar' => $validated['avatar'] ?? null,
            'checkout_id' => $validated['checkout_id'],
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'dep_' . $depoimento['id'] . '.' . $file->getClientOriginalExtension();
            $path = "checkouts/{$depoimento['id']}/";
            $file->move(public_path($path), $filename);
            $depoimento['avatar'] = '/' . $path . $filename;
        }
        //dd($depoimento);
        // Validação e sanitização dos dados antes de inserir
        $depoimento = array_map('strip_tags', $depoimento);
        $depoimento = array_map('trim', $depoimento);

        // Aqui você pode salvar em banco se quiser
        if (is_null($depoimento['id'])) {
            unset($depoimento['id']);
            $depoimento = DB::table('checkout_depoimentos')->insert($depoimento);
        } else {
            // Validação adicional para update
            $existingDepoimento = DB::table('checkout_depoimentos')->where('id', $depoimento['id'])->first();
            if ($existingDepoimento) {
                DB::table('checkout_depoimentos')->where('id', $depoimento['id'])->update($depoimento);
            }
        }


        return response()->json([
            'success' => true,
            'depoimento' => $depoimento
        ]);
    }


    public function removerDepoimento(Request $request)
    {
        $id = $request->input('id');

        if (!$id) {
            return response()->json(['success' => false, 'message' => 'ID não informado.'], 400);
        }

        $depoimento = CheckoutDepoimento::find($id);

        if (!$depoimento) {
            return response()->json(['success' => false, 'message' => 'Depoimento não encontrado.'], 404);
        }

        try {
            $depoimento->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao remover depoimento.']);
        }
    }

    /**
     * Processa pagamento com cartão usando PrimePay7
     */
    private function processCardPaymentPrimePay7(Request $request, array $data, CheckoutOrders $venda)
    {
        Log::info('=== PROCESSANDO PAGAMENTO PRIMEPAY7 CARD ===');
        Log::info('Dados recebidos:', $data);

        try {
            $checkout = CheckoutBuild::where('id', $request->checkout_id)->first();
            $user = User::where('id', $checkout->user_id)->first();

            if (!$checkout || !$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dados do checkout ou usuário não encontrados'
                ]);
            }

            // Inicializar serviço PrimePay7
            $primePay7Service = new \App\Services\PrimePay7Service();

            // Preparar dados da venda conforme documentação PrimePay7
            $saleData = [
                'amount' => (int) ($data['valor_total'] * 100), // Converter para centavos
                'installments' => (int) ($data['installments'] ?? 1),

                // Items conforme documentação: title, unitPrice, quantity, tangible
                'items' => [
                    [
                        'title' => $checkout->produto_name,
                        'unitPrice' => (int) ($data['valor_total'] * 100),
                        'quantity' => 1,
                        'tangible' => $checkout->produto_tipo === 'fisico' // true se produto físico
                    ]
                ],

                // Customer conforme documentação: document deve ser um objeto
                'customer' => [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'document' => [
                        'type' => 'cpf',
                        'number' => str_replace(['(', ')', ' ', '.', '-', '/'], '', $data['cpf'])
                    ],
                    'phone' => str_replace(['(', ')', ' ', '.', '-', '/'], '', $data['telefone'])
                ],

                'card' => [
                    'hash' => $data['card']['hash'] ?? null,
                    'number' => $data['card']['number'] ?? null,
                    'holderName' => $data['card']['holderName'] ?? null,
                    'expirationMonth' => $data['card']['expirationMonth'] ?? null,
                    'expirationYear' => $data['card']['expirationYear'] ?? null,
                    'cvv' => $data['card']['cvv'] ?? null,
                ]
            ];

            // Adicionar dados 3DS se fornecidos
            if (isset($data['threeDS'])) {
                $saleData['threeDS'] = $data['threeDS'];
            }

            // Adicionar returnURL para 3DS REDIRECT
            $saleData['returnURL'] = url('/checkout/callback-primepay7');

            // Adicionar postbackUrl para receber atualizações de status
            $saleData['postbackUrl'] = url('/checkout/webhook/primepay7');

            Log::info('PrimePay7 Card Sale Data:', $saleData);

            // Chamar API PrimePay7
            $response = $primePay7Service->createCardSale($saleData);

            // Verificar se houve erro (o serviço retorna 'error' e 'status_code' em caso de erro)
            if (isset($response['error']) || (isset($response['status_code']) && $response['status_code'] >= 400)) {
                // Erro na API PrimePay7
                $errorMessage = 'Erro ao processar pagamento';

                // Tentar extrair mensagem de erro mais específica
                if (isset($response['error']['message'])) {
                    $errorMessage = $response['error']['message'];
                } elseif (isset($response['error']['error'])) {
                    // Erros de validação da API
                    $validationErrors = [];
                    foreach ($response['error']['error'] as $field => $errors) {
                        if (is_array($errors)) {
                            foreach ($errors as $error) {
                                if (is_string($error)) {
                                    $validationErrors[] = $error;
                                } elseif (is_array($error)) {
                                    $validationErrors = array_merge($validationErrors, array_values($error));
                                }
                            }
                        } elseif (is_string($errors)) {
                            $validationErrors[] = $errors;
                        }
                    }
                    if (!empty($validationErrors)) {
                        $errorMessage = implode('; ', $validationErrors);
                    }
                }

                Log::error('❌ Erro na criação da venda PrimePay7:', [
                    'error' => $response['error'] ?? 'Erro desconhecido',
                    'status_code' => $response['status_code'] ?? 'unknown',
                    'error_message' => $errorMessage
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage
                ]);
            }

            // Sucesso - a API PrimePay7 retorna diretamente os dados da transação
            if (isset($response['id'])) {
                // Atualizar pedido com dados da transação
                $venda->update([
                    'idTransaction' => $response['id'],
                    'qrcode' => '',
                    'status' => 'pendente' // Status em português conforme ENUM da tabela
                ]);

                Log::info('✅ Pagamento PrimePay7 criado com sucesso:', [
                    'transaction_id' => $response['id'],
                    'status' => $response['status'] ?? 'unknown',
                    'amount' => $response['amount'] ?? 0
                ]);

                // Preparar resposta com dados para 3DS se necessário
                $responseData = [
                    'id' => $response['id'],
                    'order_id' => $venda->id,
                    'status' => $response['status'] ?? 'pending',
                    'valor_text' => "R$ " . number_format($venda->valor_total, 2, ',', '.')
                ];

                // Adicionar dados 3DS se presentes
                if (isset($response['threeDS'])) {
                    $responseData['threeDS'] = $response['threeDS'];
                }

                // Disparar webhook se configurado
                if (!is_null($user->webhook_url) && in_array('gerado', (array) $user->webhook_endpoint)) {
                    Http::withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                        ->post($user->webhook_url, [
                            'nome' => $venda->name,
                            'cpf' => preg_replace('/\D/', '', $venda->cpf),
                            'telefone' => preg_replace('/\D/', '', $venda->telefone),
                            'email' => $venda->email,
                            'status' => 'pendente',
                            'transaction_id' => $response['id']
                        ]);
                }

                return response()->json([
                    'status' => 'success',
                    'data' => $responseData
                ]);
            }

            // Se chegou aqui, resposta inesperada
            Log::error('❌ Resposta inesperada da PrimePay7:', $response);
            return response()->json([
                'status' => 'error',
                'message' => 'Resposta inesperada da API de pagamento'
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Exceção no processamento PrimePay7 Card:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno ao processar pagamento: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Verifica o status de uma transação (polling)
     */
    public function checkTransactionStatus(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $idTransaction = $request->input('idTransaction');

            if (!$orderId && !$idTransaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order ID ou Transaction ID não fornecido'
                ], 400);
            }

            // Se foi fornecido idTransaction (como PaymentIntent do Stripe), buscar por ele
            if ($idTransaction) {
                $order = CheckoutOrders::where('idTransaction', $idTransaction)->first();

                if (!$order) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Transação não encontrada'
                    ], 404);
                }

                // Se for Stripe, verificar status via API do Stripe
                if (strpos($idTransaction, 'pi_') === 0) {
                    \Log::info('🔍 Verificando status Stripe via API:', ['paymentIntentId' => $idTransaction]);

                    $stripeService = new \App\Services\StripeService();
                    $stripeResult = $stripeService->getPaymentIntentStatus($idTransaction);

                    if ($stripeResult['success']) {
                        $stripeStatus = $stripeResult['status'];

                        // Mapear status do Stripe para status do sistema
                        $statusMap = [
                            'succeeded' => 'approved',
                            'requires_payment_method' => 'processing',
                            'requires_confirmation' => 'processing',
                            'requires_action' => 'processing',
                            'canceled' => 'cancelled',
                            'processing' => 'processing'
                        ];

                        $frontendStatus = $statusMap[$stripeStatus] ?? 'processing';

                        // Atualizar status no banco se necessário
                        if ($stripeStatus === 'succeeded' && $order->status !== 'pago') {
                            $order->status = 'pago';
                            $order->save();
                            \Log::info('✅ Status atualizado para pago:', ['order_id' => $order->id]);

                            // Enviar email de confirmação
                            \App\Http\Controllers\ObrigadoController::enviarEmailConfirmacao($order->id);
                        }

                        return response()->json([
                            'status' => 'success',
                            'order_status' => $frontendStatus,
                            'transaction_id' => $idTransaction,
                            'stripe_status' => $stripeStatus,
                            'valor_total' => $order->valor_total
                        ]);
                    } else {
                        \Log::error('❌ Erro ao verificar status Stripe:', $stripeResult);
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Erro ao verificar status do Stripe'
                        ], 500);
                    }
                }
            } else {
                // Buscar por order_id (fluxo normal)
                $order = CheckoutOrders::find($orderId);
            }

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pedido não encontrado'
                ], 404);
            }

            Log::info('🔍 Verificando status da transação:', [
                'order_id' => $orderId,
                'transaction_id' => $order->idTransaction,
                'current_status' => $order->status
            ]);

            // Mapear status interno (português) para frontend
            $statusMap = [
                'gerado' => 'processing',
                'pendente' => 'processing',
                'pago' => 'approved',
                'cancelado' => 'cancelled',
                'encaminhado' => 'approved',
                'entregue' => 'approved'
            ];

            $frontendStatus = $statusMap[$order->status] ?? 'processing';

            return response()->json([
                'status' => 'success',
                'order_status' => $frontendStatus,
                'transaction_id' => $order->idTransaction,
                'valor_total' => $order->valor_total
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Erro ao verificar status da transação:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao verificar status'
            ], 500);
        }
    }

    /**
     * Webhook/Postback da PrimePay7 para receber atualizações de transações
     */
    public function primepay7Webhook(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('📬 PrimePay7 Webhook recebido:', $data);

            // Validar estrutura do webhook
            if (!isset($data['type']) || !isset($data['data'])) {
                Log::warning('⚠️ Webhook PrimePay7 com estrutura inválida');
                return response()->json(['status' => 'error', 'message' => 'Invalid webhook structure'], 400);
            }

            $transactionData = $data['data'];
            $transactionId = $transactionData['id'] ?? null;
            $status = $transactionData['status'] ?? null;

            if (!$transactionId || !$status) {
                Log::warning('⚠️ Webhook PrimePay7 sem ID ou status');
                return response()->json(['status' => 'error', 'message' => 'Missing transaction ID or status'], 400);
            }

            // Buscar o pedido pelo ID da transação
            $order = CheckoutOrders::where('idTransaction', $transactionId)->first();

            if (!$order) {
                Log::warning('⚠️ Pedido não encontrado para transação PrimePay7', ['transaction_id' => $transactionId]);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            Log::info('📦 Pedido encontrado:', [
                'order_id' => $order->id,
                'current_status' => $order->status,
                'new_status' => $status
            ]);

            // Mapear status da PrimePay7 para status interno (português)
            $statusMap = [
                'waiting_payment' => 'pendente',
                'pending' => 'pendente',
                'processing' => 'pendente',
                'approved' => 'pago',
                'paid' => 'pago',
                'refused' => 'cancelado',
                'cancelled' => 'cancelado',
                'refunded' => 'cancelado',
                'chargeback' => 'cancelado',
                'in_protest' => 'pendente'
            ];

            $newStatus = $statusMap[$status] ?? 'pendente';

            // Buscar checkout e usuário
            $checkout = CheckoutBuild::where('id', $order->checkout_id)->first();
            if (!$checkout) {
                Log::warning('⚠️ Checkout não encontrado para o pedido', ['order_id' => $order->id]);
                return response()->json(['status' => 'error', 'message' => 'Checkout not found'], 404);
            }

            $user = User::where('id', $checkout->user_id)->first();
            if (!$user) {
                Log::warning('⚠️ Usuário não encontrado para o checkout', ['checkout_id' => $checkout->id]);
                return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
            }

            // Se o status for "pago" e ainda não foi processado como pago
            if ($newStatus === 'pago' && $order->status !== 'pago') {
                Log::info('💰 Processando pagamento aprovado...');

                // Calcular taxa de depósito usando o helper centralizado
                $setting = \App\Models\App::first();
                $taxaCalculada = \App\Helpers\TaxaFlexivelHelper::calcularTaxaDeposito($order->valor_total, $setting, $user);
                $deposito_liquido = $taxaCalculada['deposito_liquido'];
                $taxa_cash_in = $taxaCalculada['taxa_cash_in'];

                // Criar registro na tabela de solicitações (transações financeiras)
                $solicitacao = \App\Models\Solicitacoes::create([
                    'user_id' => $user->user_id,
                    'externalreference' => 'PRIMEPAY7_CARD_' . $transactionId,
                    'amount' => $order->valor_total,
                    'client_name' => $order->name,
                    'client_document' => preg_replace('/\D/', '', $order->cpf),
                    'client_email' => $order->email,
                    'date' => now(),
                    'status' => 'PAID_OUT',
                    'idTransaction' => $transactionId,
                    'deposito_liquido' => $deposito_liquido,
                    'qrcode_pix' => '',
                    'paymentcode' => '',
                    'paymentCodeBase64' => '',
                    'adquirente_ref' => 'PrimePay7',
                    'taxa_cash_in' => $taxa_cash_in,
                    'taxa_pix_cash_in_adquirente' => 0,
                    'taxa_pix_cash_in_valor_fixo' => 0,
                    'client_telefone' => $order->telefone,
                    'executor_ordem' => 'PrimePay7 Card',
                    'descricao_transacao' => 'Pagamento com Cartão - ' . ($checkout->produto_name ?? 'Produto'),
                ]);

                // Creditar o saldo do usuário
                \App\Helpers\Helper::incrementAmount($user, $deposito_liquido, 'saldo');
                \App\Helpers\Helper::calculaSaldoLiquido($user->user_id);

                Log::info('✅ Saldo creditado ao usuário:', [
                    'user_id' => $user->user_id,
                    'valor_bruto' => $order->valor_total,
                    'valor_liquido' => $deposito_liquido,
                    'taxa' => $taxa_cash_in
                ]);

                // Se o usuário tiver gerente, criar comissão
                if (isset($user->gerente_id) && !is_null($user->gerente_id)) {
                    $gerente = User::where('id', $user->gerente_id)->first();
                    if ($gerente) {
                        $gerente_porcentagem = $gerente->gerente_percentage ?? 0;
                        $comissao_valor = (float)$taxa_cash_in * (float)$gerente_porcentagem / 100;

                        \App\Models\Transactions::create([
                            'user_id' => $user->user_id,
                            'gerente_id' => $user->gerente_id,
                            'solicitacao_id' => $solicitacao->id,
                            'comission_value' => $comissao_valor,
                            'transaction_percent' => $taxa_cash_in,
                            'comission_percent' => $gerente_porcentagem,
                        ]);

                        \App\Helpers\Helper::calculaSaldoLiquido($gerente->user_id);

                        Log::info('💼 Comissão do gerente registrada:', [
                            'gerente_id' => $gerente->id,
                            'comissao' => $comissao_valor,
                            'percentual' => $gerente_porcentagem
                        ]);
                    }
                }
            }

            // Atualizar status do pedido
            $order->update([
                'status' => $newStatus
            ]);

            Log::info('✅ Status do pedido atualizado:', [
                'order_id' => $order->id,
                'old_status' => $order->status,
                'new_status' => $newStatus,
                'transaction_id' => $transactionId
            ]);

            if ($checkout) {

                // Disparar webhook do usuário se configurado
                if ($user && !is_null($user->webhook_url) && in_array($newStatus, ['pago', 'cancelado'])) {
                    $webhookPayload = [
                        'nome' => $order->name,
                        'cpf' => preg_replace('/\D/', '', $order->cpf),
                        'telefone' => preg_replace('/\D/', '', $order->telefone),
                        'email' => $order->email,
                        'status' => $newStatus,
                        'transaction_id' => $transactionId,
                        'valor_total' => $order->valor_total,
                        'metodo_pagamento' => $transactionData['paymentMethod'] ?? 'credit_card'
                    ];

                    Log::info('📤 Disparando webhook do usuário:', [
                        'webhook_url' => $user->webhook_url,
                        'status' => $newStatus
                    ]);

                    Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ])->post($user->webhook_url, $webhookPayload);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Erro ao processar webhook PrimePay7:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Webhook/Postback da NxPay para receber atualizações de transações
     */
    public function nxpayWebhook(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('📬 NxPay Webhook recebido:', $data);

            if (!isset($data['type']) || !isset($data['data'])) {
                Log::warning('⚠️ Webhook NxPay com estrutura inválida');
                return response()->json(['status' => 'error', 'message' => 'Invalid webhook structure'], 400);
            }

            $transactionData = $data['data'];
            $transactionId = $transactionData['id'] ?? null;
            $status = $transactionData['status'] ?? null;

            if (!$transactionId || !$status) {
                return response()->json(['status' => 'error', 'message' => 'Missing transaction ID or status'], 400);
            }

            $order = CheckoutOrders::where('idTransaction', $transactionId)->first();
            if (!$order) {
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            $statusMap = [
                'waiting_payment' => 'pendente',
                'pending' => 'pendente',
                'processing' => 'pendente',
                'approved' => 'pago',
                'paid' => 'pago',
                'refused' => 'cancelado',
                'cancelled' => 'cancelado',
            ];

            $newStatus = $statusMap[$status] ?? 'pendente';

            if ($newStatus === 'pago' && $order->status !== 'pago') {
                $checkout = CheckoutBuild::find($order->checkout_id);
                $user = User::find($checkout->user_id);
                $setting = \App\Models\App::first();
                $taxaCalculada = \App\Helpers\TaxaFlexivelHelper::calcularTaxaDeposito($order->valor_total, $setting, $user);

                \App\Models\Solicitacoes::create([
                    'user_id' => $user->user_id,
                    'externalreference' => 'NXPAY_CARD_' . $transactionId,
                    'amount' => $order->valor_total,
                    'status' => 'PAID_OUT',
                    'idTransaction' => $transactionId,
                    'deposito_liquido' => $taxaCalculada['deposito_liquido'],
                    'adquirente_ref' => 'NxPay',
                    'taxa_cash_in' => $taxaCalculada['taxa_cash_in'],
                    'executor_ordem' => 'NxPay Card',
                ]);

                \App\Helpers\Helper::incrementAmount($user, $taxaCalculada['deposito_liquido'], 'saldo');
                \App\Helpers\Helper::calculaSaldoLiquido($user->user_id);
            }

            $order->update(['status' => $newStatus]);
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('❌ Erro no webhook NxPay:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Processa pagamento com cartão via Pagar.me
     */
    private function processCardPaymentPagarme(Request $request, $data, $venda)
    {
        try {
            \Log::info('💳 Processando pagamento com cartão via Pagar.me:', [
                'venda_id' => $venda->id,
                'amount' => $data['amount']
            ]);

            $creditcard = json_decode($data['credit_card']);
            $installment = json_decode($data['installment']);
            $checkout = CheckoutBuild::where('id', $request->checkout_id)->first();
            $user = User::where('id', $checkout->user_id)->first();

            // Preparar dados para PagarMeTrait
            $cardRequest = new \Illuminate\Http\Request();
            $cardRequest->merge([
                'amount' => $data['amount'],
                'installments' => $installment->installments ?? 1,
                'card_number' => $creditcard->number ?? '',
                'card_holder_name' => $creditcard->holder_name ?? '',
                'card_exp_month' => $creditcard->exp_month ?? '',
                'card_exp_year' => $creditcard->exp_year ?? '',
                'card_cvv' => $creditcard->cvv ?? '',
                'customer_name' => $data['client_name'] ?? 'Cliente',
                'customer_email' => $data['client_email'] ?? 'redacted@example.invalid',
                'customer_document' => $data['client_document'] ?? '08355037120',
                'customer_phone' => $data['client_phone'] ?? '11999999999',
                'postback' => url('/checkout/webhook'),
                'baasPostbackUrl' => 'web'
            ]);

            // Simular usuário autenticado
            $cardRequest->setUserResolver(function () use ($user) {
                return $user;
            });

            $response = PagarMeTrait::processCardPaymentPagarme($cardRequest);

            if ($response['status'] === 200) {
                // Atualizar venda com dados da transação
                $venda->update([
                    'idTransaction' => $response['data']['idTransaction'],
                    'status' => $response['data']['status'],
                    'payment_method' => 'credit_card',
                    'adquirente' => 'pagarme'
                ]);

                \Log::info('✅ Pagamento com cartão Pagar.me processado:', [
                    'venda_id' => $venda->id,
                    'transaction_id' => $response['data']['idTransaction']
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Pagamento processado com sucesso',
                    'data' => [
                        'idTransaction' => $response['data']['idTransaction'],
                        'status' => $response['data']['status'],
                        'amount' => $response['data']['amount'],
                        'installments' => $response['data']['installments'],
                        'payment_method' => 'credit_card'
                    ]
                ]);
            } else {
                \Log::error('❌ Erro ao processar cartão Pagar.me:', $response);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erro ao processar pagamento com cartão',
                    'details' => $response
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('CheckoutControlller::processCardPaymentPagarme - Erro: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Processa pagamento com boleto via Pagar.me
     */
    private function processBoletoPaymentPagarme(Request $request, $data, $venda)
    {
        try {
            \Log::info('🧾 Processando pagamento com boleto via Pagar.me:', [
                'venda_id' => $venda->id,
                'amount' => $data['amount']
            ]);

            $checkout = CheckoutBuild::where('id', $request->checkout_id)->first();
            $user = User::where('id', $checkout->user_id)->first();

            // Preparar dados para PagarMeTrait
            $boletoRequest = new \Illuminate\Http\Request();
            $boletoRequest->merge([
                'amount' => $data['amount'],
                'customer_name' => $data['client_name'] ?? 'Cliente',
                'customer_email' => $data['client_email'] ?? 'redacted@example.invalid',
                'customer_document' => $data['client_document'] ?? '08355037120',
                'customer_phone' => $data['client_phone'] ?? '11999999999',
                'postback' => url('/checkout/webhook'),
                'baasPostbackUrl' => 'web'
            ]);

            // Simular usuário autenticado
            $boletoRequest->setUserResolver(function () use ($user) {
                return $user;
            });

            $response = PagarMeTrait::processBoletoPaymentPagarme($boletoRequest);

            if ($response['status'] === 200) {
                // Atualizar venda com dados da transação
                $venda->update([
                    'idTransaction' => $response['data']['idTransaction'],
                    'status' => $response['data']['status'],
                    'payment_method' => 'boleto',
                    'adquirente' => 'pagarme'
                ]);

                \Log::info('✅ Pagamento com boleto Pagar.me processado:', [
                    'venda_id' => $venda->id,
                    'transaction_id' => $response['data']['idTransaction']
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Boleto gerado com sucesso',
                    'data' => [
                        'idTransaction' => $response['data']['idTransaction'],
                        'status' => $response['data']['status'],
                        'amount' => $response['data']['amount'],
                        'payment_method' => 'boleto',
                        'boleto_url' => $response['data']['boleto_url'],
                        'boleto_barcode' => $response['data']['boleto_barcode']
                    ]
                ]);
            } else {
                \Log::error('❌ Erro ao processar boleto Pagar.me:', $response);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erro ao gerar boleto bancário',
                    'details' => $response
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('CheckoutControlller::processBoletoPaymentPagarme - Erro: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Webhook para Pagar.me - Checkout
     */
    public function pagarmeWebhook(Request $request)
    {
        try {
            \Log::info('=== WEBHOOK PAGAR.ME CHECKOUT RECEBIDO ===');
            \Log::info('Timestamp: ' . now()->format('Y-m-d H:i:s'));
            \Log::info('IP: ' . $request->ip());
            \Log::info('Headers: ' . json_encode($request->headers->all()));
            \Log::info('Dados recebidos: ' . json_encode($request->all(), JSON_PRETTY_PRINT));
            \Log::info('=====================================');

            $data = $request->all();

            // Verificar se é um evento de pagamento
            if (isset($data['type']) && isset($data['data'])) {
                $eventType = $data['type'];
                $transactionData = $data['data'];

                \Log::info("🎪 TIPO DE EVENTO PAGAR.ME CHECKOUT: $eventType");

                if ($eventType === 'transaction.paid') {
                    $transactionId = $transactionData['id'] ?? null;
                    $status = $transactionData['status'] ?? null;

                    if ($transactionId && $status === 'paid') {
                        // Buscar a venda pelo ID da transação
                        $venda = CheckoutOrders::where('idTransaction', $transactionId)->first();

                        if ($venda) {
                            $venda->update([
                                'status' => 'paid',
                                'updated_at' => now()
                            ]);

                            \Log::info('✅ Venda atualizada via webhook Pagar.me:', [
                                'venda_id' => $venda->id,
                                'transaction_id' => $transactionId,
                                'status' => 'paid'
                            ]);
                        } else {
                            \Log::warning('⚠️ Venda não encontrada para transação:', ['transaction_id' => $transactionId]);
                        }
                    }
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            \Log::error('❌ Erro no webhook Pagar.me checkout:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Processa pagamento com cartão usando Rede e.Rede
     */
    private function processCardPaymentRede(Request $request, $data, $venda)
    {
        try {
            \Log::info('🏦 Processando pagamento Rede e.Rede:', [
                'venda_id' => $venda->id,
                'valor' => $data['valor_total']
            ]);

            $creditcard = json_decode($data['credit_card']);
            $installment = json_decode($data['installment']);

            $checkout = CheckoutBuild::where('id', $request->checkout_id)->first();
            $user = User::where('id', $checkout->user_id)->first();

            // Preparar dados para a Rede e.Rede
            $redeData = [
                'amount' => $data['valor_total'], // Valor em reais (será convertido para centavos no serviço)
                'installments' => isset($installment->installments) ? $installment->installments : 1,
                'items' => [
                    [
                        'name' => $checkout->produto_name ?? 'Pagamento via checkout',
                        'quantity' => 1,
                        'unitPrice' => $data['valor_total']
                    ]
                ],
                'customer' => [
                    'name' => $data['name'] ?? 'Cliente',
                    'email' => $data['email'] ?? 'redacted@example.invalid',
                    'document' => preg_replace('/[^0-9]/', '', $data['cpf'] ?? '08355037120')
                ],
                'card' => [
                    'number' => $creditcard->number ?? '',
                    'holderName' => $creditcard->holder_name ?? '',
                    'expirationMonth' => $creditcard->expiration_month ?? '',
                    'expirationYear' => $creditcard->expiration_year ?? '',
                    'cvv' => $creditcard->cvv ?? ''
                ],
                'externalId' => 'checkout_' . $venda->id,
                'callbackUrl' => url('/api/rede/webhook')
            ];

            \Log::info('📤 Enviando dados para Rede e.Rede:', $redeData);

            // Chamar o serviço da Rede
            $redeService = new \App\Services\RedeService();
            $response = $redeService->createCardSale($redeData);

            \Log::info('📥 Resposta da Rede e.Rede:', $response);

            if (isset($response['success']) && $response['success']) {
                // Atualizar a venda com o ID da transação
                $venda->update([
                    'idTransaction' => $response['transaction_id'] ?? 'rede_' . time(),
                    'status' => 'pendente' // Status pendente até confirmação via webhook
                ]);

                $valor_text = "R$ " . number_format($venda->valor_total, '2', ',', '.');

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'idTransaction' => $response['transaction_id'] ?? 'rede_' . time(),
                        'status' => 'pending', // Status pending para aguardar confirmação
                        'message' => 'Pagamento processado com sucesso'
                    ],
                    'valor_text' => $valor_text
                ]);
            } else {
                \Log::error('❌ Erro no processamento Rede e.Rede:', $response);

                // Usar mensagem amigável se disponível
                $userMessage = $response['message'] ?? 'Erro ao processar pagamento';

                // Log detalhado do erro
                if (isset($response['error_type'])) {
                    \Log::error('Tipo de erro Rede:', [
                        'error_type' => $response['error_type'],
                        'error_description' => $response['error_description'] ?? 'N/A',
                        'return_code' => $response['return_code'] ?? 'N/A',
                        'return_message' => $response['return_message'] ?? 'N/A'
                    ]);
                }

                return response()->json([
                    'status' => 'error',
                    'message' => $userMessage,
                    'error_type' => $response['error_type'] ?? 'unknown',
                    'error_code' => $response['return_code'] ?? null
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('💥 Erro no processamento Rede e.Rede:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno. Tente novamente.'
            ]);
        }
    }

    /**
     * Processa pagamento com cartão usando Stripe
     */
    private function processCardPaymentStripe($venda, $cardData, $request)
    {
        try {
            \Log::info('🏦 Processando pagamento Stripe:', [
                'venda_id' => $venda->id,
                'valor' => $venda->valor_total
            ]);

            // Preparar dados para o Stripe
            $stripeData = [
                'amount' => $venda->valor_total,
                'installments' => 1,
                'items' => [
                    [
                        'name' => 'Produto',
                        'quantity' => 1,
                        'unitPrice' => $venda->valor_total
                    ]
                ],
                'customer' => [
                    'name' => $venda->name,
                    'email' => $venda->email,
                    'phone' => $venda->telefone,
                    'document' => [
                        'type' => 'cpf',
                        'number' => preg_replace('/\D/', '', $venda->cpf)
                    ]
                ],
                'card' => [
                    'number' => $cardData['number'],
                    'holderName' => $cardData['holderName'],
                    'expirationMonth' => $cardData['expirationMonth'],
                    'expirationYear' => $cardData['expirationYear'],
                    'cvv' => $cardData['cvv'],
                    'customer_phone' => $venda->telefone,
                    'customer_ip' => $request->ip()
                ],
                'description' => 'Pagamento via EXEMPLO',
                'externalId' => 'checkout_' . $venda->id,
                'callbackUrl' => url('/api/stripe/webhook'),
                'customer_ip' => $request->ip()
            ];

            \Log::info('📤 Enviando dados para Stripe:', $stripeData);

            // Chamar o serviço do Stripe
            $stripeService = new \App\Services\StripeService();
            $response = $stripeService->createCardSale($stripeData);

            \Log::info('📥 Resposta do Stripe:', $response);

            if (isset($response['success']) && $response['success']) {
                // Atualizar a venda com o ID da transação
                $venda->update([
                    'idTransaction' => $response['transaction_id'] ?? 'stripe_' . time(),
                    'status' => 'pendente' // Status pendente até confirmação via webhook
                ]);

                $valor_text = "R$ " . number_format($venda->valor_total, '2', ',', '.');

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'idTransaction' => $response['transaction_id'] ?? 'stripe_' . time(),
                        'status' => 'pending', // Status pending para aguardar confirmação
                        'message' => 'Pagamento processado com sucesso'
                    ],
                    'valor_text' => $valor_text
                ]);
            } else {
                \Log::error('❌ Erro no processamento Stripe:', $response);

                // Usar mensagem amigável se disponível
                $userMessage = $response['message'] ?? 'Erro ao processar pagamento';

                // Log detalhado do erro
                if (isset($response['error_type'])) {
                    \Log::error('Tipo de erro Stripe:', [
                        'error_type' => $response['error_type'],
                        'error_description' => $response['error_description'] ?? 'N/A',
                        'status_code' => $response['status_code'] ?? 'N/A'
                    ]);
                }

                return response()->json([
                    'status' => 'error',
                    'message' => $userMessage,
                    'error_type' => $response['error_type'] ?? 'unknown',
                    'error_code' => $response['status_code'] ?? null
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('💥 Erro no processamento Stripe:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno. Tente novamente.'
            ]);
        }
    }

    public function checkoutBuilder($id)
    {
        $checkout = CheckoutBuild::where('id_unico', $id)->firstOrFail();

        // Verificar se o usuário tem permissão para editar este checkout
        if ($checkout->user_id !== auth()->id()) {
            abort(403, 'Você não tem permissão para acessar este checkout.');
        }

        return view('profile.checkout.builder', compact('checkout'));
    }

    public function saveCheckoutBuilder(Request $request, $id)
    {
        try {
            $checkout = CheckoutBuild::where('id_unico', $id)->firstOrFail();

            // Verificar se o usuário tem permissão para editar este checkout
            if ($checkout->user_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Sem permissão'], 403);
            }

            $data = $request->all();

            // Preparar dados para salvar
            $builderData = [
                'layout' => $data['layout'] ?? null,
                'settings' => $data['settings'] ?? null,
                'components' => $data['components'] ?? null,
                'updated_at' => now()
            ];

            // Salvar no campo template do checkout
            $template = json_decode($checkout->template ?? '{}', true);
            $template['builder'] = $builderData;

            $checkout->update([
                'template' => json_encode($template)
            ]);

            // Gerar arquivo v2.blade.php baseado no layout construído
            $this->generateV2BladeFile($checkout, $builderData);

            return response()->json([
                'success' => true,
                'message' => 'Checkout salvo e arquivo V2 gerado com sucesso!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar checkout builder:', [
                'error' => $e->getMessage(),
                'checkout_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar checkout'
            ], 500);
        }
    }

    /**
     * Gerar arquivo v2.blade.php baseado no layout construído
     */
    private function generateV2BladeFile($checkout, $builderData)
    {
        try {
            // Caminho do arquivo V2
            $v2Path = resource_path("views/profile/checkout/v2-{$checkout->id_unico}.blade.php");

            // Gerar HTML baseado nos componentes
            $html = $this->generateV2HTML($checkout, $builderData);

            // Salvar arquivo
            file_put_contents($v2Path, $html);

            \Log::info('Arquivo V2 gerado:', [
                'checkout_id' => $checkout->id_unico,
                'file_path' => $v2Path
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar arquivo V2:', [
                'error' => $e->getMessage(),
                'checkout_id' => $checkout->id_unico
            ]);
        }
    }

    /**
     * Gerar HTML de um componente individual
     */
    private function generateComponentHTML($componentType, $checkout, $primaryColor, $buttonColor)
    {
        switch ($componentType) {
            case 'product-info':
                return '
                <div class="product-card mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 8px; display: flex; align-items-center; justify-content-center;">
                            <i class="fas fa-box fa-2x text-muted"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $checkout->produto_name }}</h5>
                            <div class="text-success fw-bold">1 X de R$ {{ number_format($checkout->produto_valor, 2, \',\', \'.\') }}</div>
                            <small class="text-muted">ou R$ {{ number_format($checkout->produto_valor, 2, \',\', \'.\') }} à vista</small>
                        </div>
                    </div>
                </div>';

            case 'customer-data':
                return '
                <div class="product-card mb-3">
                    <h5 class="mb-3"><i class="fas fa-user me-2"></i>Seus dados</h5>
                    <form id="checkout-form">
                        <div class="mb-3">
                            <label class="form-label">Nome completo</label>
                            <input type="text" class="form-control" placeholder="Nome do comprador">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="redacted@example.invalid">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CPF</label>
                                <input type="text" class="form-control" placeholder="000.000.000-00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Celular</label>
                                <input type="text" class="form-control" placeholder="(99) 99999-9999">
                            </div>
                        </div>
                        <a href="#" class="text-primary" style="font-size: 0.9rem;">Por que pedimos esses dados?</a>
                    </form>
                </div>';

            case 'payment':
                return '
                <div class="product-card mb-3">
                    <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Pagamento</h5>
                    <div style="background: ' . $primaryColor . '; color: white; padding: 1.5rem; border-radius: 8px; text-align: center;">
                        <i class="fas fa-qrcode fa-2x mb-2"></i>
                        <h6 class="mb-0">PIX</h6>
                    </div>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Liberação imediata</li>
                        <li><i class="fas fa-check text-success me-2"></i>É simples, só usar o aplicativo de seu banco para pagar Pix</li>
                    </ul>
                </div>';

            case 'oferta-limitada':
                return '
                <div class="alert alert-warning mb-3">
                    <strong><i class="fas fa-ticket-alt me-2"></i>Oferta limitada</strong>
                    <div class="mt-2">
                        <input type="text" class="form-control mb-2" placeholder="DIGITE O CUPOM DE DESCONTO">
                        <button class="btn btn-success w-100">Aplicar</button>
                    </div>
                </div>';

            case 'oferta-especial':
                return '
                <div class="product-card mb-3" style="border: 2px dashed #dee2e6;">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="oferta-especial">
                        <label class="form-check-label fw-bold" for="oferta-especial">
                            SIM, EU ACEITO ESSA OFERTA ESPECIAL!
                        </label>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Nome do seu produto</strong>
                            <div class="text-muted small">Adicione à compra</div>
                        </div>
                        <div class="text-end">
                            <div class="text-decoration-line-through text-muted small">R$ 0,00</div>
                            <div class="text-success fw-bold">-INFINITY% OFF<br>R$ 25,00</div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mt-2 w-100">Adicionar Produto</button>
                </div>';

            case 'resumo-pedido':
                return '
                <div class="product-card mb-3">
                    <h6 class="mb-3">Resumo do pedido</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-box me-2"></i>' . htmlspecialchars($checkout->produto_name) . '</span>
                        <span>R$ ' . number_format($checkout->produto_valor, 2, ',', '.') . '</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>R$ ' . number_format($checkout->produto_valor, 2, ',', '.') . '</span>
                    </div>
                </div>';

            case 'checkout-button':
                return '
                <button class="btn btn-lg w-100 mb-3" style="background: ' . $buttonColor . '; color: white; padding: 1rem; font-weight: 600;">
                    <i class="fas fa-qrcode me-2"></i>Pagar com Pix
                </button>';

            case 'security':
                return '
                <div class="text-center mb-3">
                    <div class="mb-2">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        <span>Compra 100% segura</span>
                    </div>
                    <small class="text-muted d-block">Este site é protegido pelo reCAPTCHA do Google</small>
                    <small class="text-muted d-block"><strong>Política de privacidade</strong> e <strong>Termos de serviço</strong></small>
                    <small class="text-muted d-block">* Parcelamento com acréscimo</small>
                    <small class="text-muted d-block mt-2">Ao continuar, você concorda com os <strong>Termos de Compra</strong></small>
                </div>';

            case 'testimonial':
                return '
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 40px; height: 40px; background: ' . $primaryColor . '; color: white; border-radius: 50%; display: flex; align-items-center; justify-content-center; font-weight: 600; margin-right: 10px;">
                                C
                            </div>
                            <strong>Cliente Satisfeito</strong>
                        </div>
                        <p class="mb-0 text-muted">"Excelente produto! Recomendo muito."</p>
                    </div>
                </div>';

            default:
                return '';
        }
    }

    /**
     * Gerar HTML do checkout V2 baseado nos componentes
     */
    /**
     * Gerar HTML do checkout V2 baseado nos componentes
     */
    private function generateV2HTML($checkout, $builderData)
    {
        $settings = $builderData['settings'] ?? [];
        $components = $builderData['components'] ?? ['left' => [], 'right' => []];

        // Cores padrão
        $primaryColor = $settings['primary-color'] ?? '#0b6856';
        $secondaryColor = $settings['secondary-color'] ?? '#0f7864';
        $buttonColor = $settings['button-color'] ?? '#0b6856';

        // Obter componentes das colunas
        $leftComponents = $components['left'] ?? [];
        $rightComponents = $components['right'] ?? [];

        // Gerar HTML dos componentes da coluna esquerda
        $leftHTML = '';
        foreach ($leftComponents as $componentType) {
            $leftHTML .= $this->generateComponentHTML($componentType, $checkout, $primaryColor, $buttonColor);
        }

        // Gerar HTML dos componentes da coluna direita (depoimentos)
        $rightHTML = '';
        foreach ($rightComponents as $componentType) {
            $rightHTML .= $this->generateComponentHTML($componentType, $checkout, $primaryColor, $buttonColor);
        }

        // Template HTML com duas colunas
        $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($checkout->produto_name) . ' - Checkout</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: ' . $primaryColor . ';
            --secondary-color: ' . $secondaryColor . ';
            --button-color: ' . $buttonColor . ';
        }
        
        body {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f5f5f5;
            padding: 2rem 0;
        }
        
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .product-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .compra-segura-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .resumo-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        
        @media (max-width: 992px) {
            .checkout-container {
                padding: 0 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <div class="row">
            <!-- Coluna Esquerda -->
            <div class="col-lg-8 mb-4">
                ' . $leftHTML . '
            </div>
            
            <!-- Coluna Direita (Sidebar) -->
            <div class="col-lg-4">
                <!-- Compra Segura (Fixo) -->
                <div class="compra-segura-card">
                    <h5><i class="fas fa-shield-alt me-2"></i>Compra segura</h5>
                </div>
                
                <!-- Resumo do Pedido (Fixo) -->
                <div class="resumo-card">
                    <h6 class="mb-3">Resumo do pedido</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>' . htmlspecialchars($checkout->produto_name) . '</span>
                        <span>R$ ' . number_format($checkout->produto_valor, 2, ',', '.') . '</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span class="text-success">1 X de R$ ' . number_format($checkout->produto_valor, 2, ',', '.') . '</span>
                    </div>
                    <small class="text-muted d-block mt-2">ou R$ ' . number_format($checkout->produto_valor, 2, ',', '.') . ' à vista</small>
                    <small class="text-muted d-block">Renovação atual</small>
                </div>
                
                <!-- Depoimentos Dinâmicos -->
                ' . $rightHTML . '
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';

        return $html;
    }

    /**
     * Renderizar checkout V2 gerado pelo builder
     */
    public function v2($id)
    {
        try {
            $checkout = CheckoutBuild::where('id_unico', $id)->firstOrFail();

            // Verificar se o arquivo V2 existe
            $v2Path = resource_path("views/profile/checkout/v2-{$checkout->id_unico}.blade.php");

            if (!file_exists($v2Path)) {
                // Se não existe, gerar um arquivo padrão
                $this->generateV2BladeFile($checkout, [
                    'settings' => [],
                    'components' => []
                ]);
            }

            // Renderizar o arquivo V2
            return view("profile.checkout.v2-{$checkout->id_unico}", compact('checkout'));
        } catch (\Exception $e) {
            \Log::error('Erro ao renderizar checkout V2:', [
                'error' => $e->getMessage(),
                'checkout_id' => $id
            ]);

            abort(404, 'Checkout não encontrado');
        }
    }
}
