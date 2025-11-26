<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SplitInterno;
use App\Models\SplitInternoExecutado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SplitInternosController extends Controller
{

    /**
     * Exibe a lista de configurações de splits internos
     */
    public function index(Request $request)
    {
        try {
            // Log de teste
            Log::info('SplitInternosController::index - Acesso iniciado', [
                'user_id' => auth()->id(),
                'permission' => auth()->user() ? auth()->user()->permission : null,
                'request_data' => $request->all()
            ]);

            $query = SplitInterno::with(['usuarioBeneficiario', 'usuarioPagador', 'criadoPorAdmin', 'splitsExecutados']);

            // Filtros
            if ($request->filled('status')) {
                if ($request->status === 'ativos') {
                    $query->ativos();
                } elseif ($request->status === 'inativos') {
                    $query->where('ativo', false);
                }
            }

            if ($request->filled('tipo_taxa')) {
                $query->porTipoTaxa($request->tipo_taxa);
            }

            if ($request->filled('usuario_pagador')) {
                $query->porUsuarioPagador($request->usuario_pagador);
            }

            $configuracoes = $query->orderBy('created_at', 'desc')->paginate(20);

            // Estatísticas gerais
            $estatisticas = $this->obterEstatisticasGerais();

            Log::info('SplitInternosController::index - Configurações encontradas', [
                'total' => $configuracoes->total(),
                'count' => $configuracoes->count()
            ]);

            return view('admin.splits-internos.index', compact('configuracoes', 'estatisticas'));
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao carregar lista', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao carregar configurações de splits internos.');
        }
    }

    /**
     * Exibe o formulário para criar nova configuração
     */
    public function create()
    {
        $tiposTaxa = [
            SplitInterno::TAXA_DEPOSITO => 'Depósito',
            SplitInterno::TAXA_SAQUE_PIX => 'Saque PIX',
            SplitInterno::TAXA_CARTEIRA_GATEWAY => 'Carteira Gateway'
        ];

        // Buscar usuários para dropdown
        $usuarios = User::select('id', 'name', 'email', 'user_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.splits-internos.create', compact('tiposTaxa', 'usuarios'));
    }

    /**
     * Armazena nova configuração de split interno
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'usuario_pagador_id' => 'required|different:usuario_beneficiario_id',
                'usuario_beneficiario_id' => 'required|exists:users,id',
                'porcentagem_split' => 'required|numeric|min:0.01|max:100',
                'tipo_taxa' => 'required|in:deposito,saque_pix,carteira_gateway',
                'ativo' => 'boolean',
                'data_inicio' => 'nullable|date|after_or_equal:today',
                'data_fim' => 'nullable|date|after:data_inicio'
            ], [
                'usuario_pagador_id.different' => 'O usuário pagador deve ser diferente do usuário beneficiário.',
                'porcentagem_split.max' => 'A porcentagem não pode ser maior que 100%.',
                'porcentagem_split.min' => 'A porcentagem deve ser maior que 0%.',
                'data_fim.after' => 'A data de fim deve ser posterior à data de início.'
            ]);

            // Validação customizada para usuario_pagador_id
            if ($request->usuario_pagador_id !== 'gateway') {
                $validator->after(function ($validator) use ($request) {
                    if (!User::where('id', $request->usuario_pagador_id)->exists()) {
                        $validator->errors()->add('usuario_pagador_id', 'Usuário pagador não encontrado.');
                    }
                });
            }

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro de validação',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $configuracao = SplitInterno::criarConfiguracao($request->all(), Auth::id());

            Log::info('[SPLIT INTERNO] Nova configuração criada', [
                'id' => $configuracao->id,
                'admin_id' => Auth::id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Configuração de split interno criada com sucesso!',
                    'data' => $configuracao
                ]);
            }

            return redirect()->route('admin.splits-internos.index')
                ->with('success', 'Configuração de split interno criada com sucesso!');
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao criar configuração', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Exibe uma configuração específica
     */
    public function show(SplitInterno $splitInterno)
    {
        try {
            $splitInterno->load(['usuarioBeneficiario', 'usuarioPagador', 'criadoPorAdmin', 'splitsExecutados']);

            // Estatísticas desta configuração
            $estatisticas = [
                'total_executado' => $splitInterno->splitsExecutados()->processados()->sum('valor_split'),
                'quantidade_executados' => $splitInterno->splitsExecutados->count(),
                'quantidade_processados' => $splitInterno->splitsExecutados->where('status', 'processado')->count(),
                'quantidade_falhados' => $splitInterno->splitsExecutados->where('status', 'falhado')->count()
            ];

            // Se for requisição AJAX, retornar JSON
            if (request()->ajax()) {
                // Converter ID 1 para 'gateway' para o frontend
                $splitInternoData = $splitInterno->toArray();
                if ($splitInternoData['usuario_pagador_id'] === 1) {
                    $splitInternoData['usuario_pagador_id'] = 'gateway';
                }

                return response()->json([
                    'success' => true,
                    'splitInterno' => $splitInternoData,
                    'estatisticas' => $estatisticas
                ]);
            }

            return view('admin.splits-internos.show', compact('splitInterno', 'estatisticas'));
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao carregar configuração', [
                'id' => $splitInterno->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao carregar configuração.');
        }
    }

    /**
     * Exibe o formulário de edição
     */
    public function edit(SplitInterno $splitInterno)
    {
        try {
            $splitInterno->load(['usuarioBeneficiario', 'usuarioPagador']);

            // Buscar usuários para o select
            $usuarios = User::where('permission', '!=', 3) // Excluir admins
                ->orderBy('name')
                ->get();

            return view('admin.splits-internos.edit', compact('splitInterno', 'usuarios'));
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao carregar formulário de edição', [
                'id' => $splitInterno->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao carregar formulário de edição.');
        }
    }

    /**
     * Atualiza uma configuração
     */
    public function update(Request $request, SplitInterno $splitInterno)
    {
        try {
            $validator = Validator::make($request->all(), [
                'usuario_beneficiario_id' => 'required|exists:users,id',
                'usuario_pagador_id' => 'required',
                'porcentagem_split' => 'required|numeric|min:0.01|max:100',
                'tipo_taxa' => 'required|in:deposito,saque_pix,carteira_gateway',
                'ativo' => 'boolean',
                'data_inicio' => 'nullable|date',
                'data_fim' => 'nullable|date|after:data_inicio'
            ], [
                'usuario_pagador_id.required' => 'O usuário pagador é obrigatório.',
                'usuario_beneficiario_id.required' => 'O usuário beneficiário é obrigatório.',
                'usuario_beneficiario_id.exists' => 'Usuário beneficiário não encontrado.',
                'porcentagem_split.required' => 'A porcentagem é obrigatória.',
                'porcentagem_split.numeric' => 'A porcentagem deve ser um número.',
                'porcentagem_split.min' => 'A porcentagem deve ser maior que 0%.',
                'porcentagem_split.max' => 'A porcentagem não pode ser maior que 100%.',
                'tipo_taxa.required' => 'O tipo de taxa é obrigatório.',
                'tipo_taxa.in' => 'Tipo de taxa inválido.',
                'data_fim.after' => 'A data de fim deve ser posterior à data de início.'
            ]);

            // Validação customizada para usuario_pagador_id
            if ($request->usuario_pagador_id !== 'gateway') {
                $validator->after(function ($validator) use ($request) {
                    if (!User::where('id', $request->usuario_pagador_id)->exists()) {
                        $validator->errors()->add('usuario_pagador_id', 'Usuário pagador não encontrado.');
                    }
                });
            }

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro de validação',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Verificar se não existe configuração duplicada (excluindo a atual)
            $existente = SplitInterno::where([
                'usuario_pagador_id' => $request->usuario_pagador_id === 'gateway' ? 1 : $request->usuario_pagador_id,
                'usuario_beneficiario_id' => $request->usuario_beneficiario_id,
                'tipo_taxa' => $request->tipo_taxa
            ])->where('id', '!=', $splitInterno->id)->first();

            if ($existente) {
                $message = 'Já existe uma configuração de split interno entre estes usuários para este tipo de transação.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }
                return redirect()->back()
                    ->with('error', $message)
                    ->withInput();
            }

            // Validar se o beneficiário não é o próprio pagador
            if ($request->usuario_pagador_id === $request->usuario_beneficiario_id) {
                $message = 'O usuário beneficiário não pode ser o mesmo usuário pagador.';
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }
                return redirect()->back()
                    ->with('error', $message)
                    ->withInput();
            }

            // Atualizar configuração
            $splitInterno->update([
                'usuario_beneficiario_id' => $request->usuario_beneficiario_id,
                'usuario_pagador_id' => $request->usuario_pagador_id === 'gateway' ? 1 : $request->usuario_pagador_id,
                'porcentagem_split' => $request->porcentagem_split,
                'tipo_taxa' => $request->tipo_taxa,
                'ativo' => $request->has('ativo'),
                'data_inicio' => $request->data_inicio ? Carbon::parse($request->data_inicio) : null,
                'data_fim' => $request->data_fim ? Carbon::parse($request->data_fim) : null
            ]);

            Log::info('[SPLIT INTERNO] Configuração atualizada', [
                'id' => $splitInterno->id,
                'admin_id' => Auth::id(),
                'dados' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Configuração atualizada com sucesso!'
                ]);
            }

            return redirect()->route('admin.splits-internos.index')
                ->with('success', 'Configuração atualizada com sucesso!');
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao atualizar configuração', [
                'id' => $splitInterno->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor. Tente novamente.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Erro ao atualizar configuração.')
                ->withInput();
        }
    }

    /**
     * Ativa/desativa uma configuração
     */
    public function toggleStatus(SplitInterno $splitInterno)
    {
        try {
            $splitInterno->update([
                'ativo' => !$splitInterno->ativo
            ]);

            $status = $splitInterno->ativo ? 'ativada' : 'desativada';

            Log::info('[SPLIT INTERNO] Configuração alterada', [
                'id' => $splitInterno->id,
                'novo_status' => $status,
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('success', "Configuração {$status} com sucesso!");
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao alterar status', [
                'id' => $splitInterno->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao alterar status da configuração.');
        }
    }

    /**
     * Remove uma configuração
     */
    public function destroy(SplitInterno $splitInterno)
    {
        try {
            // Verificar se há splits executados para aviso
            $splitsExecutados = $splitInterno->splitsExecutados()->count();

            $splitInterno->delete();

            Log::info('[SPLIT INTERNO] Configuração removida', [
                'id' => $splitInterno->id,
                'admin_id' => Auth::id(),
                'splits_executados_anteriormente' => $splitsExecutados
            ]);

            $message = 'Configuração removida com sucesso!';
            if ($splitsExecutados > 0) {
                $message .= " (Atenção: Esta configuração tinha {$splitsExecutados} split(s) executado(s) anteriormente)";
            }

            return redirect()->route('admin.splits-internos.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao remover configuração', [
                'id' => $splitInterno->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao remover configuração.');
        }
    }

    /**
     * Exibe histórico de splits executados
     */
    public function historico(Request $request)
    {
        try {
            $query = SplitInternoExecutado::with([
                'splitInterno.usuarioBeneficiario',
                'splitInterno.usuarioPagador',
                'usuarioBeneficiario',
                'usuarioPagador',
                'solicitacao'
            ]);

            // Filtros
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('usuario_beneficiario')) {
                $query->porBeneficiario($request->usuario_beneficiario);
            }

            if ($request->filled('usuario_pagador')) {
                $query->porPagador($request->usuario_pagador);
            }

            if ($request->filled('data_inicio')) {
                $query->whereDate('created_at', '>=', $request->data_inicio);
            }

            if ($request->filled('data_fim')) {
                $query->whereDate('created_at', '<=', $request->data_fim);
            }

            $splitsExecutados = $query->orderBy('created_at', 'desc')->paginate(20);

            // Estatísticas do período
            $estatisticas = $this->obterEstatisticasPeriodo($request);

            // Se for requisição AJAX, retornar JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'splitsExecutados' => $splitsExecutados->items(),
                    'estatisticas' => $estatisticas,
                    'pagination' => [
                        'current_page' => $splitsExecutados->currentPage(),
                        'last_page' => $splitsExecutados->lastPage(),
                        'per_page' => $splitsExecutados->perPage(),
                        'total' => $splitsExecutados->total()
                    ]
                ]);
            }

            return view('admin.splits-internos.historico', compact('splitsExecutados', 'estatisticas'));
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao carregar histórico', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao carregar histórico de splits internos.');
        }
    }

    /**
     * Busca usuário por email ou nome para AJAX
     */
    public function buscarUsuario(Request $request)
    {
        try {
            $termo = $request->get('q', '');

            $usuarios = User::select('id', 'name', 'email', 'user_id', 'username')
                ->where('status', 1);

            // Se há termo de busca, aplicar filtro
            if (strlen($termo) >= 1) {
                // Remover @ do início se presente para busca no banco
                $termoBusca = ltrim($termo, '@');

                $usuarios->where(function ($query) use ($termoBusca, $termo) {
                    $query->where('username', 'ILIKE', "%{$termoBusca}%")
                        ->orWhere('name', 'ILIKE', "%{$termoBusca}%")
                        ->orWhere('email', 'ILIKE', "%{$termoBusca}%")
                        ->orWhere('user_id', 'ILIKE', "%{$termoBusca}%");
                });
            }

            $usuarios = $usuarios->limit(50)->get();

            $resultado = $usuarios->map(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'text' => "{$usuario->username} ({$usuario->email}) - {$usuario->user_id}",
                    'name' => $usuario->name,
                    'username' => $usuario->username,
                    'email' => $usuario->email,
                    'user_id' => $usuario->user_id
                ];
            });

            // Adicionar Carteira Gateway como usuário especial
            $carteiraGateway = [
                'id' => 'gateway',
                'text' => 'CARTEIRA GATEWAY (redacted@example.invalid) - gateway',
                'name' => 'CARTEIRA GATEWAY',
                'username' => '@gateway',
                'email' => 'redacted@example.invalid',
                'user_id' => 'gateway'
            ];

            // Adicionar no início da lista
            $resultado->prepend($carteiraGateway);

            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro na busca de usuário', [
                'termo' => $request->get('q'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([]);
        }
    }

    /**
     * Obtém estatísticas gerais do sistema de splits internos
     */
    private function obterEstatisticasGerais(): array
    {
        try {
            return [
                'total_configuracoes_ativas' => SplitInterno::ativos()->count(),
                'total_splits_executados' => SplitInternoExecutado::processados()->count(),
                'total_valor_distribuido' => SplitInternoExecutado::processados()->sum('valor_split'),
                'configuracoes_por_tipo' => [
                    'deposito' => SplitInterno::ativos()->porTipoTaxa(SplitInterno::TAXA_DEPOSITO)->count(),
                    'saque_pix' => SplitInterno::ativos()->porTipoTaxa(SplitInterno::TAXA_SAQUE_PIX)->count()
                ],
                'movimento_mes_atual' => SplitInternoExecutado::processados()
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('valor_split')
            ];
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao calcular estatísticas gerais', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Obtém estatísticas de um período específico
     */
    private function obterEstatisticasPeriodo(Request $request): array
    {
        try {
            $query = SplitInternoExecutado::processados();

            if ($request->filled('data_inicio')) {
                $query->whereDate('created_at', '>=', $request->data_inicio);
            }

            if ($request->filled('data_fim')) {
                $query->whereDate('created_at', '<=', $request->data_fim);
            }

            return [
                'total_executados' => $query->count(),
                'total_valor_splits' => $query->sum('valor_split'),
                'total_valor_taxas' => $query->sum('valor_taxa_original'),
                'porcentagem_media' => $query->avg('porcentagem_aplicada') ?? 0,
                'status_breakdown' => [
                    'processados' => SplitInternoExecutado::where('status', 'processado')->count(),
                    'falhados' => SplitInternoExecutado::where('status', 'falhado')->count(),
                    'pendentes' => SplitInternoExecutado::where('status', 'pendente')->count()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao calcular estatísticas de período', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Processa splits da carteira gateway manualmente
     */
    public function processarSplitsGateway(Request $request)
    {
        try {
            // Buscar carteira gateway com maior saldo
            $gatewayWallet = \App\Models\GatewayWallet::orderBy('saldo_gateway', 'desc')->first();

            if (!$gatewayWallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma carteira gateway encontrada.'
                ], 404);
            }

            // Buscar configurações de split ativas para carteira gateway
            $splitsConfigurados = SplitInterno::ativos()
                ->where('tipo_taxa', SplitInterno::TAXA_CARTEIRA_GATEWAY)
                ->get();

            if ($splitsConfigurados->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma configuração de split para carteira gateway encontrada.'
                ], 404);
            }

            // Processar splits
            $valorTotalDepositos = \App\Models\Solicitacoes::where('status', 'PAID_OUT')->sum('amount');
            $totalProcessado = $gatewayWallet->processarSplits($valorTotalDepositos);

            Log::info('[SPLIT GATEWAY] Splits processados manualmente', [
                'admin_id' => Auth::id(),
                'total_processado' => $totalProcessado,
                'splits_count' => $splitsConfigurados->count()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Splits processados com sucesso!',
                'total_processado' => $totalProcessado,
                'saldo_atualizado' => $gatewayWallet->fresh()->saldo_disponivel
            ]);
        } catch (\Exception $e) {
            Log::error('[SPLIT GATEWAY] Erro ao processar splits', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar splits: ' . $e->getMessage()
            ], 500);
        }
    }
}
