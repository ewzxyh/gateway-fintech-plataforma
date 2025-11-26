<?php

namespace App\Traits;

use App\Models\SplitPayment;
use App\Models\SplitInterno;
use App\Models\SplitInternoExecutado;
use App\Models\Solicitacoes;
use App\Models\User;
use App\Helpers\Helper;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

trait SplitTrait
{
    /**
     * Processa splits para uma transação
     */
    public static function processSplits(Solicitacoes $solicitacao, User $user): array
    {
        try {
            Log::info('=== INÍCIO DO PROCESSAMENTO DE SPLIT ===', [
                'solicitacao_id' => $solicitacao->id,
                'idTransaction' => $solicitacao->idTransaction,
                'valor_bruto' => $solicitacao->amount,
                'valor_liquido' => $solicitacao->deposito_liquido,
                'split_email' => $solicitacao->split_email,
                'split_percentage' => $solicitacao->split_percentage,
                'user_id_original' => $user->user_id
            ]);

            // Verificar se já existem splits processados para esta transação
            $existingSplits = SplitPayment::where('solicitacao_id', $solicitacao->id)
                ->whereIn('split_status', [SplitPayment::STATUS_COMPLETED, SplitPayment::STATUS_PROCESSING])
                ->count();

            if ($existingSplits > 0) {
                Log::info('[SPLIT] Splits já processados para esta transação', [
                    'solicitacao_id' => $solicitacao->id,
                    'existing_splits' => $existingSplits
                ]);
                return [['status' => 'skipped', 'message' => 'Splits já processados para esta transação']];
            }

            $splits = [];

            // Verificar se há splits configurados na transação (por email ou username)
            if (($solicitacao->split_email || $solicitacao->split_username) && $solicitacao->split_percentage) {
                Log::info('[SPLIT] Criando split da transação', [
                    'split_email' => $solicitacao->split_email,
                    'split_username' => $solicitacao->split_username,
                    'split_percentage' => $solicitacao->split_percentage,
                    'valor_bruto' => $solicitacao->amount,
                    'valor_liquido' => $solicitacao->deposito_liquido
                ]);
                $splits[] = self::createSplitFromSolicitacao($solicitacao, $user);
            }

            // Verificar se o usuário tem splits automáticos configurados
            $userSplits = self::getUserSplits($user);
            foreach ($userSplits as $splitConfig) {
                $splits[] = self::createSplitFromConfig($solicitacao, $user, $splitConfig);
            }

            // Processar splits internos (inclui splits da carteira gateway)
            $resultadosSplitsInternos = self::processarSplitsInternos($solicitacao, $user);

            // Processar todos os splits externos
            Log::info('[SPLIT] Iniciando processamento de splits', [
                'total_splits' => count($splits)
            ]);

            $results = [];
            foreach ($splits as $split) {
                Log::info('[SPLIT] Executando split individual', [
                    'split_id' => $split->id,
                    'split_amount' => $split->split_amount,
                    'split_email' => $split->split_email
                ]);
                $result = self::executeSplit($split, $solicitacao);
                $results[] = $result;
            }

            Log::info('[SPLIT] Splits processados com sucesso', [
                'solicitacao_id' => $solicitacao->id,
                'user_id' => $user->user_id,
                'splits_count' => count($splits),
                'splits_internos_count' => count($resultadosSplitsInternos),
                'results' => array_merge($results, $resultadosSplitsInternos)
            ]);

            Log::info('=== FIM DO PROCESSAMENTO DE SPLIT ===', [
                'solicitacao_id' => $solicitacao->id,
                'status' => 'concluido'
            ]);

            return array_merge($results, $resultadosSplitsInternos);
        } catch (\Exception $e) {
            Log::error('[SPLIT] Erro ao processar splits', [
                'solicitacao_id' => $solicitacao->id,
                'user_id' => $user->user_id,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Cria split baseado nos dados da solicitação
     */
    private static function createSplitFromSolicitacao(Solicitacoes $solicitacao, User $user): SplitPayment
    {
        // Calcular split sobre o valor líquido, não o valor bruto
        $splitAmount = ($solicitacao->deposito_liquido * $solicitacao->split_percentage) / 100;

        Log::info('[SPLIT] Calculando valor do split', [
            'valor_liquido' => $solicitacao->deposito_liquido,
            'split_percentage' => $solicitacao->split_percentage,
            'split_amount_calculado' => $splitAmount,
            'formula' => "({$solicitacao->deposito_liquido} * {$solicitacao->split_percentage}) / 100"
        ]);

        $split = SplitPayment::create([
            'solicitacao_id' => $solicitacao->id,
            'user_id' => $user->user_id,
            'split_email' => $solicitacao->split_email,
            'split_username' => $solicitacao->split_username,
            'split_percentage' => $solicitacao->split_percentage,
            'split_amount' => $splitAmount,
            'split_status' => SplitPayment::STATUS_PENDING,
            'split_type' => SplitPayment::TYPE_PERCENTAGE,
            'description' => "Split de {$solicitacao->split_percentage}% para " . ($solicitacao->split_email ?: $solicitacao->split_username)
        ]);

        Log::info('[SPLIT] Split criado no banco de dados', [
            'split_id' => $split->id,
            'split_amount' => $split->split_amount,
            'split_email' => $split->split_email,
            'split_username' => $split->split_username
        ]);

        return $split;
    }

    /**
     * Cria split baseado na configuração do usuário
     */
    private static function createSplitFromConfig(Solicitacoes $solicitacao, User $user, array $config): SplitPayment
    {
        // Calcular split sobre o valor líquido, não o valor bruto
        $splitAmount = ($solicitacao->deposito_liquido * $config['percentage']) / 100;

        return SplitPayment::create([
            'solicitacao_id' => $solicitacao->id,
            'user_id' => $user->user_id,
            'split_email' => $config['email'],
            'split_percentage' => $config['percentage'],
            'split_amount' => $splitAmount,
            'split_status' => SplitPayment::STATUS_PENDING,
            'split_type' => $config['type'] ?? SplitPayment::TYPE_PERCENTAGE,
            'description' => $config['description'] ?? "Split automático de {$config['percentage']}%"
        ]);
    }

    /**
     * Obtém splits configurados para o usuário
     */
    private static function getUserSplits(User $user): array
    {
        // Aqui você pode implementar lógica para buscar splits automáticos
        // Por exemplo, de uma tabela de configurações de split por usuário
        // Por enquanto, retornamos array vazio
        return [];
    }

    /**
     * Executa um split específico
     */
    private static function executeSplit(SplitPayment $split, Solicitacoes $solicitacao): array
    {
        try {
            $split->markAsProcessing();

            // Verificar se o valor do split é válido
            if ($split->split_amount <= 0) {
                $split->markAsFailed('Valor do split inválido');
                return ['status' => 'failed', 'message' => 'Valor do split inválido'];
            }

            // Verificar se há saldo suficiente
            if ($solicitacao->deposito_liquido < $split->split_amount) {
                $split->markAsFailed('Saldo insuficiente para split');
                return ['status' => 'failed', 'message' => 'Saldo insuficiente'];
            }

            // Buscar usuário destinatário do split (por email ou username)
            $splitUser = null;

            if ($split->split_email) {
                $splitUser = User::where('email', $split->split_email)->first();
            } elseif ($split->split_username) {
                // Normalizar username para buscar no banco (remover @ se presente)
                $normalizedUsername = $split->split_username;
                if (str_starts_with($split->split_username, '@')) {
                    $normalizedUsername = substr($split->split_username, 1);
                }
                $splitUser = User::where('username', $normalizedUsername)->first();
            }

            if (!$splitUser) {
                $split->markAsFailed('Usuário destinatário não encontrado');
                return ['status' => 'failed', 'message' => 'Usuário não encontrado'];
            }

            // Executar o split
            $result = self::transferSplitAmount($split, $splitUser, $solicitacao);

            if ($result['success']) {
                $split->markAsCompleted();
                return ['status' => 'completed', 'message' => 'Split executado com sucesso'];
            } else {
                $split->markAsFailed($result['message']);
                return ['status' => 'failed', 'message' => $result['message']];
            }
        } catch (\Exception $e) {
            $split->markAsFailed($e->getMessage());
            Log::error('[SPLIT] Erro ao executar split', [
                'split_id' => $split->id,
                'error' => $e->getMessage()
            ]);

            return ['status' => 'failed', 'message' => $e->getMessage()];
        }
    }

    /**
     * Transfere o valor do split para o usuário destinatário
     */
    private static function transferSplitAmount(SplitPayment $split, User $splitUser, Solicitacoes $solicitacao): array
    {
        try {
            Log::info('[SPLIT] Iniciando transferência de valores', [
                'split_id' => $split->id,
                'split_amount' => $split->split_amount,
                'from_user' => $solicitacao->user_id,
                'to_user' => $splitUser->user_id
            ]);

            // Buscar usuário original
            $originalUser = User::where('user_id', $solicitacao->user_id)->first();

            Log::info('[SPLIT] Saldos ANTES da transferência', [
                'usuario_original' => $originalUser ? $originalUser->saldo : 'N/A',
                'usuario_split' => $splitUser->saldo
            ]);

            // Creditar o valor para o usuário destinatário
            Helper::incrementAmount($splitUser, $split->split_amount, 'saldo');
            Helper::calculaSaldoLiquido($splitUser->user_id);

            Log::info('[SPLIT] Valor creditado para usuário de split', [
                'user_id' => $splitUser->user_id,
                'amount' => $split->split_amount,
                'novo_saldo' => $splitUser->fresh()->saldo
            ]);

            // Enviar notificação de comissão ao usuário de split
            $pushService = app(PushNotificationService::class);
            $pushService->sendCommissionNotification(
                $splitUser->user_id,
                $split->split_amount,
                "Comissão de split recebida"
            );

            // Debitar o valor do usuário original
            if ($originalUser) {
                Helper::decrementAmount($originalUser, $split->split_amount, 'saldo');
                Helper::calculaSaldoLiquido($originalUser->user_id);

                Log::info('[SPLIT] Valor debitado do usuário original', [
                    'user_id' => $originalUser->user_id,
                    'amount' => $split->split_amount,
                    'novo_saldo' => $originalUser->fresh()->saldo
                ]);
            }

            // Log da transação de split (sem criar registro na tabela transactions por enquanto)
            Log::info('[SPLIT] Transação de split registrada', [
                'split_id' => $split->id,
                'user_id' => $splitUser->user_id,
                'amount' => $split->split_amount,
                'reference' => "split_{$split->id}"
            ]);

            Log::info('[SPLIT] Saldos APÓS a transferência', [
                'usuario_original' => $originalUser ? $originalUser->fresh()->saldo : 'N/A',
                'usuario_split' => $splitUser->fresh()->saldo
            ]);

            Log::info('[SPLIT] Split transferido com sucesso', [
                'split_id' => $split->id,
                'from_user' => $solicitacao->user_id,
                'to_user' => $splitUser->user_id,
                'amount' => $split->split_amount
            ]);

            return ['success' => true, 'message' => 'Split transferido com sucesso'];
        } catch (\Exception $e) {
            Log::error('[SPLIT] Erro ao transferir split', [
                'split_id' => $split->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Obtém estatísticas de splits
     */
    public static function getSplitStats(User $user = null, $startDate = null, $endDate = null): array
    {
        $query = SplitPayment::query();

        if ($user) {
            $query->where('user_id', $user->user_id);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $stats = [
            'total_splits' => $query->count(),
            'pending_splits' => $query->where('split_status', SplitPayment::STATUS_PENDING)->count(),
            'completed_splits' => $query->where('split_status', SplitPayment::STATUS_COMPLETED)->count(),
            'failed_splits' => $query->where('split_status', SplitPayment::STATUS_FAILED)->count(),
            'total_amount' => $query->where('split_status', SplitPayment::STATUS_COMPLETED)->sum('split_amount'),
            'pending_amount' => $query->where('split_status', SplitPayment::STATUS_PENDING)->sum('split_amount'),
        ];

        return $stats;
    }

    /**
     * Processa splits pendentes em lote
     */
    public static function processPendingSplits(): array
    {
        $pendingSplits = SplitPayment::pending()->with(['solicitacao', 'user'])->get();
        $results = [];

        foreach ($pendingSplits as $split) {
            $result = self::executeSplit($split, $split->solicitacao);
            $results[] = [
                'split_id' => $split->id,
                'result' => $result
            ];
        }

        Log::info('[SPLIT] Processamento em lote concluído', [
            'processed_count' => count($pendingSplits),
            'results' => $results
        ]);

        return $results;
    }

    /**
     * Cancela um split
     */
    public static function cancelSplit(SplitPayment $split, string $reason = null): bool
    {
        try {
            if ($split->isCompleted()) {
                return false; // Não pode cancelar split já processado
            }

            $split->update([
                'split_status' => SplitPayment::STATUS_CANCELLED,
                'error_message' => $reason ?? 'Split cancelado pelo usuário',
                'processed_at' => now()
            ]);

            Log::info('[SPLIT] Split cancelado', [
                'split_id' => $split->id,
                'reason' => $reason
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[SPLIT] Erro ao cancelar split', [
                'split_id' => $split->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    // ========================================================================
    // MÉTODOS PARA SPLITS INTERNOS
    // ========================================================================

    /**
     * Processa splits internos para uma transação
     */
    public static function processarSplitsInternos(Solicitacoes $solicitacao, User $user): array
    {
        try {
            Log::info('=== PROCESSAMENTO DE SPLITS INTERNOS INICIADO ===', [
                'solicitacao_id' => $solicitacao->id,
                'user_id' => $user->id,
                'tipo_transacao' => $solicitacao->tipo ?? 'deposito'
            ]);

            $resultados = [];

            // Determinar tipo de taxa baseado na transação
            $tipoTaxa = self::determinarTipoTaxa($solicitacao);

            // PROCESSAMENTO AUTOMÁTICO DE SPLIT DE GERENTE
            // Se o usuário tem um gerente cadastrado, criar split automaticamente
            $splitGerenteResultado = self::processarSplitAutomaticoGerente($solicitacao, $user);
            if ($splitGerenteResultado) {
                $resultados[] = $splitGerenteResultado;
            }

            // PROCESSAMENTO AUTOMÁTICO DE SPLIT DE AFFILIATE
            // Se o usuário foi indicado por um affiliado ativo, criar split automaticamente
            $splitAffiliateResultado = self::processarSplitAutomaticoAffiliate($solicitacao, $user);
            if ($splitAffiliateResultado) {
                $resultados[] = $splitAffiliateResultado;
            }

            // Buscar configurações de split interno válidas para este usuário
            $configuracoesSplit = SplitInterno::obterConfiguracoesParaUsuario($user->id, $tipoTaxa);

            // Buscar também configurações da carteira gateway (tipo carteira_gateway)
            $configuracoesCarteiraGateway = SplitInterno::ativos()
                ->porTipoTaxa(SplitInterno::TAXA_CARTEIRA_GATEWAY)
                ->with(['usuarioBeneficiario'])
                ->get()
                ->toArray();

            // Combinar configurações
            $configuracoesSplit = array_merge($configuracoesSplit, $configuracoesCarteiraGateway);

            if (empty($configuracoesSplit)) {
                Log::info('[SPLIT INTERNO] Nenhuma configuração de split interno encontrada', [
                    'user_id' => $user->id,
                    'tipo_taxa' => $tipoTaxa
                ]);
                return $resultados;
            }

            Log::info('[SPLIT INTERNO] Configurações encontradas', [
                'count' => count($configuracoesSplit),
                'configuracoes' => $configuracoesSplit
            ]);

            // Calcular valor da TAXA PERCENTUAL apenas (exclui taxas fixas)
            $valorTaxaPercentual = self::calcularTaxaPercentualParaSplit($solicitacao, $user);

            // CORREÇÃO: Para splits de depósito e carteira gateway, usar o valor total da transação em vez da taxa
            $valorParaSplit = $valorTaxaPercentual;
            if ($tipoTaxa === SplitInterno::TAXA_DEPOSITO || $tipoTaxa === SplitInterno::TAXA_CARTEIRA_GATEWAY) {
                $valorParaSplit = (float) $solicitacao->amount; // Usar valor total da transação
                Log::info('[SPLIT INTERNO] Usando valor total da transação para split de ' . $tipoTaxa, [
                    'valor_transacao' => $valorParaSplit,
                    'valor_taxa_percentual' => $valorTaxaPercentual,
                    'tipo_taxa' => $tipoTaxa
                ]);
            }

            Log::info('[SPLIT INTERNO] Valor para cálculo do split', [
                'valor_para_split' => $valorParaSplit,
                'tipo_taxa' => $tipoTaxa,
                'observacao' => 'Split será aplicado sobre este valor em sua PORCENTAGEM CONFIGURADA'
            ]);

            // Processar cada configuração
            foreach ($configuracoesSplit as $configuracao) {
                // Buscar o usuário pagador da configuração (não o usuário da transação)
                $usuarioPagadorConfiguracao = User::find($configuracao['usuario_pagador_id']);
                if (!$usuarioPagadorConfiguracao) {
                    Log::warning('[SPLIT INTERNO] Usuário pagador da configuração não encontrado', [
                        'configuracao_id' => $configuracao['id'],
                        'usuario_pagador_id' => $configuracao['usuario_pagador_id']
                    ]);
                    continue;
                }

                $resultado = self::executarSplitInterno($solicitacao, $usuarioPagadorConfiguracao, $configuracao, $valorParaSplit);
                $resultados[] = $resultado;
            }

            Log::info('=== PROCESSAMENTO DE SPLITS INTERNOS FINALIZADO ===', [
                'total_processados' => count($resultados),
                'resultados' => $resultados
            ]);

            return $resultados;
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao processar splits internos', [
                'solicitacao_id' => $solicitacao->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Executa um split interno específico
     * IMPORTANTE: Split só se aplica sobre a TAXA PERCENTUAL, nunca sobre taxas fixas
     * NOVA LÓGICA: Se usuário pagador for carteira gateway, aplicar nova lógica de taxa real
     */
    private static function executarSplitInterno(Solicitacoes $solicitacao, User $userPagador, array $configuracao, float $valorTaxaPercentual): array
    {
        try {
            // Verificar se o usuário pagador é a carteira gateway
            $isCarteiraGateway = self::isCarteiraGateway($userPagador);

            if ($isCarteiraGateway) {
                // Aplicar nova lógica para carteira gateway
                return self::executarSplitInternoCarteiraGateway($solicitacao, $userPagador, $configuracao);
            }

            // Lógica normal para outros usuários
            $valorSplit = $configuracao['porcentagem_split'] * ($valorTaxaPercentual / 100);

            Log::info('[SPLIT INTERNO] Executando split interno', [
                'solicitacao_id' => $solicitacao->id,
                'configuracao_id' => $configuracao['id'],
                'usuario_pagador' => $userPagador->id,
                'usuario_beneficiario' => $configuracao['usuario_beneficiario_id'],
                'porcentagem_split' => $configuracao['porcentagem_split'] . '%',
                'valor_taxa_percentual' => 'R$ ' . number_format($valorTaxaPercentual, 2),
                'valor_split_calculado' => 'R$ ' . number_format($valorSplit, 2),
                'observacao' => 'Split aplicado APENAS sobre taxa percentual - taxas fixas não sofrem split'
            ]);

            // Criar registro do split executado
            $splitExecutado = SplitInternoExecutado::criarSplit(
                $configuracao['id'],
                $solicitacao->id,
                [
                    'usuario_pagador_id' => $userPagador->id,
                    'usuario_beneficiario_id' => $configuracao['usuario_beneficiario_id'],
                    'valor_taxa_original' => $valorTaxaPercentual,
                    'valor_split' => $valorSplit,
                    'porcentagem_aplicada' => $configuracao['porcentagem_split'],
                    'observacoes' => "Split interno de {$configuracao['porcentagem_split']}% APENAS sobre taxa percentual de {$configuracao['tipo_taxa']} (não inclui taxas fixas)"
                ]
            );

            // Validar se existe taxa percentual suficiente
            if ($valorSplit > $valorTaxaPercentual) {
                $splitExecutado->marcarComoFalhado('Valor do split maior que a taxa percentual disponível');
                return [
                    'status' => 'failed',
                    'split_id' => $splitExecutado->id,
                    'message' => 'Valor do split maior que a taxa percentual disponível'
                ];
            }

            // Buscar usuário beneficiário
            $usuarioBeneficiario = User::find($configuracao['usuario_beneficiario_id']);
            if (!$usuarioBeneficiario) {
                $splitExecutado->marcarComoFalhado('Usuário beneficiário não encontrado');
                return [
                    'status' => 'failed',
                    'split_id' => $splitExecutado->id,
                    'message' => 'Usuário beneficiário não encontrado'
                ];
            }

            // Executar transferência via split interno
            $resultadoTransferencia = self::executarTransferenciaSplitInterno(
                $splitExecutado,
                $usuarioBeneficiario,
                $valorTaxaPercentual,
                $valorSplit
            );

            if ($resultadoTransferencia['success']) {
                $splitExecutado->marcarComoProcessado('Split interno executado com sucesso');
                return [
                    'status' => 'completed',
                    'split_id' => $splitExecutado->id,
                    'valor_split' => $valorSplit,
                    'usuario_beneficiario' => $usuarioBeneficiario->name,
                    'message' => 'Split interno executado com sucesso'
                ];
            } else {
                $splitExecutado->marcarComoFalhado($resultadoTransferencia['message']);
                return [
                    'status' => 'failed',
                    'split_id' => $splitExecutado->id,
                    'message' => $resultadoTransferencia['message']
                ];
            }
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao executar split específico', [
                'configuracao_id' => $configuracao['id'] ?? 'N/A',
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Executa a transferência de valores do split interno
     */
    private static function executarTransferenciaSplitInterno(
        SplitInternoExecutado $splitExecutado,
        User $usuarioBeneficiario,
        float $valorTaxaTotal,
        float $valorSplit
    ): array {
        try {
            Log::info('[SPLIT INTERNO] Iniciando transferência de valores', [
                'split_executado_id' => $splitExecutado->id,
                'usuario_beneficiario_id' => $usuarioBeneficiario->id,
                'valor_split' => $valorSplit
            ]);

            // Creditar o valor para o usuário beneficiário
            Helper::incrementAmount($usuarioBeneficiario, $valorSplit, 'saldo');
            Helper::calculaSaldoLiquido($usuarioBeneficiario->user_id);

            Log::info('[SPLIT INTERNO] Valor creditado para beneficiário', [
                'usuario_id' => $usuarioBeneficiario->id,
                'valor_creditado' => $valorSplit,
                'novo_saldo' => $usuarioBeneficiario->fresh()->saldo
            ]);

            return ['success' => true, 'message' => 'Transferência realizada com sucesso'];
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro na transferência', [
                'split_executado_id' => $splitExecutado->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Determina o tipo de taxa baseado na transação
     */
    private static function determinarTipoTaxa(Solicitacoes $solicitacao): string
    {
        $tipoTransacao = $solicitacao->tipo ?? 'deposito';

        if (in_array(strtolower($tipoTransacao), ['saque', 'pix', 'withdrawal'])) {
            return SplitInterno::TAXA_SAQUE_PIX;
        }

        return SplitInterno::TAXA_DEPOSITO;
    }

    /**
     * Calcula o valor total da taxa cobrada na transação
     * Funciona independente do tipo de configuração (global, personalizada, flexível)
     */
    private static function calcularValorTaxaTotal(Solicitacoes $solicitacao): float
    {
        Log::info('[SPLIT INTERNO] Calculando valor total da taxa', [
            'solicitacao_id' => $solicitacao->id,
            'amount' => $solicitacao->amount,
            'deposito_liquido' => $solicitacao->deposito_liquido ?? 'N/A',
            'taxa_cash_in' => $solicitacao->taxa_cash_in ?? 'N/A',
            'taxa_cash_out' => $solicitacao->taxa_cash_out ?? 'N/A'
        ]);

        // PRIORIDADE 1: Usar campos específicos de taxa da transação (mais confiável)
        if (isset($solicitacao->taxa_cash_in) && $solicitacao->taxa_cash_in > 0) {
            Log::info('[SPLIT INTERNO] Usando taxa_cash_in', [
                'valor' => $solicitacao->taxa_cash_in
            ]);
            return (float) $solicitacao->taxa_cash_in;
        }

        if (isset($solicitacao->taxa_cash_out) && $solicitacao->taxa_cash_out > 0) {
            Log::info('[SPLIT INTERNO] Usando taxa_cash_out', [
                'valor' => $solicitacao->taxa_cash_out
            ]);
            return (float) $solicitacao->taxa_cash_out;
        }

        // PRIORIDADE 2: Calcular diferença entre valor bruto e líquido (método alternativo)
        $valorBruto = (float) $solicitacao->amount;
        $valorLiquido = (float) ($solicitacao->deposito_liquido ?? $solicitacao->amount);
        $taxaCalculada = $valorBruto - $valorLiquido;

        Log::info('[SPLIT INTERNO] Taxa calculada por diferença', [
            'valor_bruto' => $valorBruto,
            'valor_liquido' => $valorLiquido,
            'taxa_calculada' => $taxaCalculada
        ]);

        // VALIDAÇÃO: A taxa deve ser positiva
        if ($taxaCalculada <= 0) {
            Log::warning('[SPLIT INTERNO] Taxa calculada é zero ou negativa', [
                'taxa_calculada' => $taxaCalculada,
                'valor_bruto' => $valorBruto,
                'valor_liquido' => $valorLiquido
            ]);
            return 0;
        }

        return $taxaCalculada;
    }

    /**
     * Calcula APENAS a parte percentual da taxa (exclui taxas fixas)
     * IMPORTANTE: O split interno só se aplica sobre a taxa percentual, nunca sobre taxas fixas
     */
    private static function calcularTaxaPercentualParaSplit(Solicitacoes $solicitacao, User $userPagador): float
    {
        try {
            $valorDeposito = (float) $solicitacao->amount;

            Log::info('[SPLIT INTERNO] Calculando taxa percentual para split', [
                'solicitacao_id' => $solicitacao->id,
                'valor_deposito' => $valorDeposito,
                'user_id' => $userPagador->id,
                'user_taxas_personalizadas' => $userPagador->taxas_personalizadas_ativas ?? false
            ]);

            // Verificar se o usuário tem taxas personalizadas
            if ($userPagador->taxas_personalizadas_ativas) {

                // Sistema flexível ativo
                if ($userPagador->sistema_flexivel_ativo) {
                    $valorMinimoFlexivel = $userPagador->valor_minimo_flexivel ?? 15.00;

                    if ($valorDeposito >= $valorMinimoFlexivel) {
                        // Valor alto: usar taxa percentual alta
                        $taxaPercentual = $userPagador->taxa_percentual_altos ?? 4.00;
                        $taxaPercentualValor = ($valorDeposito * $taxaPercentual) / 100;
                    } else {
                        // Valor baixo: usar taxa fixa (não aplica split)
                        $taxaFixaBaixo = $userPagador->taxa_fixa_baixos ?? 1.00;
                        Log::info('[SPLIT INTERNO] Valor baixo: usando taxa fixa flexível (não aplica split)', [
                            'taxa_fixa_baixo' => $taxaFixaBaixo
                        ]);
                        return 0; // Split não se aplica em taxas fixas
                    }
                } else {
                    // Sistema normal: taxas percentuais e fixas separadas
                    $taxaPercentual = $userPagador->taxa_percentual_deposito ?? 2.00;
                    $taxaPercentualValor = ($valorDeposito * $taxaPercentual) / 100;

                    // Verificar taxa mínima (se for menor, usar fixa mínima)
                    $taxaMinima = $userPagador->valor_minimo_deposito ?? 5.00;
                    if ($taxaPercentualValor < $taxaMinima) {
                        Log::info('[SPLIT INTERNO] Taxa percentual menor que mínima: usando taxa mínima fixa (não aplica split)', [
                            'taxa_percentual_calculada' => $taxaPercentualValor,
                            'taxa_minima' => $taxaMinima
                        ]);
                        return 0; // Split não se aplica em taxas mínimas fixas
                    }
                }
            } else {
                // Usar taxas globais
                $taxaPercentual = app(\App\Models\App::class)->first()->taxa_cash_in_padrao ?? 4.00;
                $taxaPercentualValor = ($valorDeposito * $taxaPercentual) / 100;

                // Verificar taxa mínima global
                $taxaMinima = app(\App\Models\App::class)->first()->baseline ?? 5.00;
                if ($taxaPercentualValor < $taxaMinima) {
                    Log::info('[SPLIT INTERNO] Taxa global menor que mínima: usando taxa mínima fixa (não aplica split)', [
                        'taxa_percentual_calculada' => $taxaPercentualValor,
                        'taxa_minima' => $taxaMinima
                    ]);
                    return 0; // Split não se aplica em taxas mínimas fixas
                }
            }

            Log::info('[SPLIT INTERNO] Taxa percentual calculada para split', [
                'taxa_percentual_valor' => $taxaPercentualValor,
                'user_id' => $userPagador->id
            ]);

            return $taxaPercentualValor;
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO] Erro ao calcular taxa percentual para split', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Obtém estatísticas de splits internos para um usuário
     */
    public static function obterEstatisticasSplitInterno(User $usuario): array
    {
        $splitsRecebidos = SplitInternoExecutado::porBeneficiario($usuario->id)
            ->processados()
            ->get();

        $splitsPagos = SplitInternoExecutado::porPagador($usuario->id)
            ->processados()
            ->get();

        return [
            'total_recebido' => $splitsRecebidos->sum('valor_split'),
            'total_pago' => $splitsPagos->sum('valor_split'),
            'quantidade_recebidos' => $splitsRecebidos->count(),
            'quantidade_pagos' => $splitsPagos->count(),
            'detalhes_recebidos' => $splitsRecebidos,
            'detalhes_pagos' => $splitsPagos
        ];
    }

    /**
     * Processa automaticamente split de gerente se configurado
     * 
     * @param Solicitacoes $solicitacao
     * @param User $user
     * @return array|null
     */
    private static function processarSplitAutomaticoGerente(Solicitacoes $solicitacao, User $user): ?array
    {
        try {
            // Verificar se o usuário tem gerente cadastrado
            if (!$user->gerente_id || !$user->gerente_percentage) {
                Log::info('[SPLIT GERENTE] Usuário sem gerente ou porcentagem configurada', [
                    'user_id' => $user->id,
                    'gerente_id' => $user->gerente_id,
                    'gerente_percentage' => $user->gerente_percentage
                ]);
                return null;
            }

            // Buscar o gerente
            $gerente = User::find($user->gerente_id);
            if (!$gerente) {
                Log::warning('[SPLIT GERENTE] Gerente não encontrado', [
                    'user_id' => $user->id,
                    'gerente_id' => $user->gerente_id
                ]);
                return null;
            }

            // Verificar se já existe split automático configurado para este gerente
            $splitExistente = SplitInterno::query()
                ->where('usuario_pagador_id', $user->id)
                ->where('usuario_beneficiario_id', $gerente->id)
                ->ativo()
                ->first();

            if ($splitExistente) {
                Log::info('[SPLIT GERENTE] Split já configurado para este gerente', [
                    'split_id' => $splitExistente->id,
                    'user_id' => $user->id,
                    'gerente_id' => $gerente->id
                ]);
                return null;
            }

            // Criar configuração de split automática para o gerente
            $novoSplit = SplitInterno::create([
                'usuario_pagador_id' => $user->id,
                'usuario_beneficiario_id' => $gerente->id,
                'porcentagem_split' => $user->gerente_percentage,
                'tipo_taxa' => SplitInterno::TAXA_DEPOSITO,
                'ativo' => true,
                'criado_por_admin_id' => 1, // Sistema automático
                'data_inicio' => now(),
                'data_fim' => null,
            ]);

            Log::info('[SPLIT GERENTE] Split automático configurado', [
                'split_id' => $novoSplit->id,
                'user_id' => $user->id,
                'gerente_id' => $gerente->id,
                'gerente_percentage' => $user->gerente_percentage,
                'split_percentage' => $novoSplit->porcentagem_split
            ]);

            // Calcular valor da taxa percentual para execução
            $valorTaxaPercentual = self::calcularTaxaPercentualParaSplit($solicitacao, $user);

            // Executar o split agora
            $resultado = self::executarSplitInterno($solicitacao, $user, $novoSplit->toArray(), $valorTaxaPercentual);

            Log::info('[SPLIT GERENTE] Split executado automaticamente', [
                'split_id' => $novoSplit->id,
                'valor_split' => $resultado['valor_split'] ?? 0,
                'user_id' => $user->id,
                'gerente_id' => $gerente->id
            ]);

            return $resultado;
        } catch (\Exception $e) {
            Log::error('[SPLIT GERENTE] Erro ao processar split automático', [
                'user_id' => $user->id,
                'gerente_id' => $user->gerente_id,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Processa automaticamente split de affiliate se configurado
     * 
     * @param Solicitacoes $solicitacao
     * @param User $user
     * @return array|null
     */
    private static function processarSplitAutomaticoAffiliate(Solicitacoes $solicitacao, User $user): ?array
    {
        try {
            // Verificar se o usuário foi indicado por um affiliate ativo
            if (!$user->affiliate_id || !$user->affiliate_percentage) {
                Log::info('[SPLIT AFFILIATE] Usuário sem affiliate ou porcentagem configurada', [
                    'user_id' => $user->id,
                    'affiliate_id' => $user->affiliate_id,
                    'affiliate_percentage' => $user->affiliate_percentage
                ]);
                return null;
            }

            // Buscar o affiliate
            $affiliate = User::find($user->affiliate_id);
            if (!$affiliate || !$affiliate->isAffiliateAtivo()) {
                Log::warning('[SPLIT AFFILIATE] Affiliate não encontrado ou inativo', [
                    'user_id' => $user->id,
                    'affiliate_id' => $user->affiliate_id
                ]);
                return null;
            }

            // Verificar se já existe split automático configurado para este affiliate
            $splitExistente = SplitInterno::query()
                ->where('usuario_pagador_id', $user->id)
                ->where('usuario_beneficiario_id', $affiliate->id)
                ->ativo()
                ->first();

            if ($splitExistente) {
                Log::info('[SPLIT AFFILIATE] Split já configurado para este affiliate', [
                    'split_id' => $splitExistente->id,
                    'user_id' => $user->id,
                    'affiliate_id' => $affiliate->id
                ]);
                return null;
            }

            // Criar configuração de split automática para_o affiliate
            $novoSplit = SplitInterno::create([
                'usuario_pagador_id' => $user->id,
                'usuario_beneficiario_id' => $affiliate->id,
                'porcentagem_split' => $user->affiliate_percentage,
                'tipo_taxa' => SplitInterno::TAXA_DEPOSITO,
                'ativo' => true,
                'criado_por_admin_id' => 1, // Sistema automático
                'data_inicio' => now(),
                'data_fim' => null,
            ]);

            Log::info('[SPLIT AFFILIATE] Split automático configurado', [
                'split_id' => $novoSplit->id,
                'user_id' => $user->id,
                'affiliate_id' => $affiliate->id,
                'affiliate_percentage' => $user->affiliate_percentage,
                'split_percentage' => $novoSplit->porcentagem_split
            ]);

            // Calcular valor da taxa percentual para execução
            $valorTaxaPercentual = self::calcularTaxaPercentualParaSplit($solicitacao, $user);

            // Executar o split agora
            $resultado = self::executarSplitInterno($solicitacao, $user, $novoSplit->toArray(), $valorTaxaPercentual);

            Log::info('[SPLIT AFFILIATE] Split executado automaticamente', [
                'split_id' => $novoSplit->id,
                'valor_split' => $resultado['valor_split'] ?? 0,
                'user_id' => $user->id,
                'affiliate_id' => $affiliate->id
            ]);

            return $resultado;
        } catch (\Exception $e) {
            Log::error('[SPLIT AFFILIATE] Erro ao processar split automático', [
                'user_id' => $user->id,
                'affiliate_id' => $user->affiliate_id ?? null,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Obtém a taxa da adquirente específica de uma transação
     */
    private static function obterTaxaAdquirente(Solicitacoes $solicitacao): float
    {
        $taxaAdquirente = 0;
        $adquirenteRef = $solicitacao->adquirente_ref;

        Log::info('[SPLIT GATEWAY] Obtendo taxa da adquirente', [
            'adquirente_ref' => $adquirenteRef,
            'valor_transacao' => $solicitacao->amount
        ]);

        // Processar taxa da Woovi
        if ($adquirenteRef === 'woovi') {
            $woovi = \App\Models\Woovi::first();
            if ($woovi && $woovi->taxa_adquirente_entradas > 0) {
                $taxaAdquirente = ($solicitacao->amount * $woovi->taxa_adquirente_entradas) / 100;
                Log::info('[SPLIT GATEWAY] Taxa da Woovi calculada', [
                    'taxa_percentual' => $woovi->taxa_adquirente_entradas . '%',
                    'taxa_calculada' => $taxaAdquirente
                ]);
            }
        }

        // Processar taxa da XDPag
        if ($adquirenteRef === 'xdpag') {
            $xdpag = \App\Models\XDPag::first();
            if ($xdpag && $xdpag->taxa_adquirente_entradas > 0) {
                $taxaAdquirente = ($solicitacao->amount * $xdpag->taxa_adquirente_entradas) / 100;
                Log::info('[SPLIT GATEWAY] Taxa da XDPag calculada', [
                    'taxa_percentual' => $xdpag->taxa_adquirente_entradas . '%',
                    'taxa_calculada' => $taxaAdquirente
                ]);
            }
        }

        // Processar taxa da Witetec
        if ($adquirenteRef === 'witetec') {
            $witetec = \App\Models\Witetec::first();
            if ($witetec && $witetec->taxa_adquirente_entradas > 0) {
                $taxaAdquirente = ($solicitacao->amount * $witetec->taxa_adquirente_entradas) / 100;
                Log::info('[SPLIT GATEWAY] Taxa da Witetec calculada', [
                    'taxa_percentual' => $witetec->taxa_adquirente_entradas . '%',
                    'taxa_calculada' => $taxaAdquirente
                ]);
            }
        }

        // Processar taxa da BSPay
        if ($adquirenteRef === 'bspay') {
            $bspay = \App\Models\BSPay::first();
            if ($bspay && $bspay->taxa_adquirente_entradas > 0) {
                $taxaAdquirente = ($solicitacao->amount * $bspay->taxa_adquirente_entradas) / 100;
                Log::info('[SPLIT GATEWAY] Taxa da BSPay calculada', [
                    'taxa_percentual' => $bspay->taxa_adquirente_entradas . '%',
                    'taxa_calculada' => $taxaAdquirente
                ]);
            }
        }

        // Processar taxa da Pixup
        if ($adquirenteRef === 'pixup' || $adquirenteRef === 'Pixup') {
            $pixup = \App\Models\Pixup::first();
            if ($pixup && $pixup->taxa_adquirente_entradas > 0) {
                $taxaAdquirente = ($solicitacao->amount * $pixup->taxa_adquirente_entradas) / 100;
                Log::info('[SPLIT GATEWAY] Taxa da Pixup calculada', [
                    'taxa_percentual' => $pixup->taxa_adquirente_entradas . '%',
                    'taxa_calculada' => $taxaAdquirente
                ]);
            }
        }

        // Processar taxa da TrustyPix
        if ($adquirenteRef === 'trustypix') {
            $trustypix = \App\Models\TrustyPix::first();
            if ($trustypix && $trustypix->taxa_adquirente_entradas > 0) {
                $taxaAdquirente = ($solicitacao->amount * $trustypix->taxa_adquirente_entradas) / 100;
                Log::info('[SPLIT GATEWAY] Taxa da TrustyPix calculada', [
                    'taxa_percentual' => $trustypix->taxa_adquirente_entradas . '%',
                    'taxa_calculada' => $taxaAdquirente
                ]);
            }
        }

        Log::info('[SPLIT GATEWAY] Taxa da adquirente final', [
            'adquirente_ref' => $adquirenteRef,
            'taxa_adquirente' => $taxaAdquirente
        ]);

        return $taxaAdquirente;
    }

    /**
     * Verifica se o usuário é a carteira gateway
     */
    private static function isCarteiraGateway(User $user): bool
    {
        // Verificar por email, username ou user_id
        return $user->email === 'redacted@example.invalid' ||
            $user->username === '@gateway' ||
            $user->user_id === 'gateway';
    }

    /**
     * Executa split interno com nova lógica para carteira gateway
     */
    private static function executarSplitInternoCarteiraGateway(Solicitacoes $solicitacao, User $userPagador, array $configuracao): array
    {
        try {
            Log::info('[SPLIT INTERNO GATEWAY] Executando split interno com nova lógica', [
                'solicitacao_id' => $solicitacao->id,
                'configuracao_id' => $configuracao['id'],
                'usuario_pagador' => $userPagador->email,
                'usuario_beneficiario' => $configuracao['usuario_beneficiario_id']
            ]);

            $valorDeposito = (float) $solicitacao->amount;

            // Calcular lucro bruto do gateway
            $taxaCashIn = (float) ($solicitacao->taxa_cash_in ?? 0);
            $taxaAdquirente = self::obterTaxaAdquirente($solicitacao);
            $lucroBrutoGateway = $taxaCashIn - $taxaAdquirente;

            // Calcular split sobre o valor total do depósito (nova lógica)
            $valorSplit = ($valorDeposito * $configuracao['porcentagem_split']) / 100;

            Log::info('[SPLIT INTERNO GATEWAY] Cálculo do split', [
                'valor_deposito' => $valorDeposito,
                'taxa_cash_in' => $taxaCashIn,
                'taxa_adquirente' => $taxaAdquirente,
                'lucro_bruto_gateway' => $lucroBrutoGateway,
                'porcentagem_split' => $configuracao['porcentagem_split'] . '%',
                'valor_split' => $valorSplit
            ]);

            // Buscar usuário beneficiário
            $usuarioBeneficiario = User::find($configuracao['usuario_beneficiario_id']);
            if (!$usuarioBeneficiario) {
                return [
                    'status' => 'failed',
                    'message' => 'Usuário beneficiário não encontrado'
                ];
            }

            // Adicionar valor ao saldo do usuário beneficiário
            $saldoAnterior = $usuarioBeneficiario->saldo;
            $usuarioBeneficiario->saldo += $valorSplit;
            $usuarioBeneficiario->save();

            // Recalcular saldo líquido
            Helper::calculaSaldoLiquido($usuarioBeneficiario->user_id);

            // DEBITAR o valor do usuário original que fez o depósito
            $saldoAnteriorOriginal = $userPagador->saldo;
            $userPagador->saldo -= $valorSplit;
            $userPagador->save();
            Helper::calculaSaldoLiquido($userPagador->user_id);

            Log::info('[SPLIT INTERNO GATEWAY] Valor debitado do usuário original', [
                'usuario_original_id' => $userPagador->id,
                'saldo_anterior' => $saldoAnteriorOriginal,
                'valor_debitado' => $valorSplit,
                'novo_saldo' => $userPagador->fresh()->saldo
            ]);

            // Criar transação virtual para aparecer no relatório
            $idTransacaoSplit = 'split_interno_gateway_' . $configuracao['id'] . '_' . time();

            \App\Models\Solicitacoes::create([
                'user_id' => $usuarioBeneficiario->user_id,
                'externalreference' => $idTransacaoSplit,
                'idTransaction' => $idTransacaoSplit,
                'amount' => $valorSplit,
                'deposito_liquido' => $valorSplit,
                'taxa_cash_in' => 0,
                'taxa_pix_cash_in_adquirente' => 0,
                'taxa_pix_cash_in_valor_fixo' => 0,
                'status' => 'PAID_OUT',
                'method' => 'comissao',
                'adquirente_ref' => 'GATEWAY_SPLIT_INTERNO',
                'executor_ordem' => 'gateway_split_interno',
                'descricao_transacao' => 'SPLIT_INTERNO_GATEWAY',
                'client_name' => 'Split Interno Gateway',
                'client_document' => '08355037120',
                'client_email' => $usuarioBeneficiario->email,
                'client_telefone' => '11999999999',
                'date' => now()->format('Y-m-d H:i:s'),
                'qrcode_pix' => '',
                'paymentcode' => '',
                'paymentCodeBase64' => '',
                'created_at' => now(),
                'updated_at' => now(),
                'observacoes' => "Split interno da carteira gateway - {$configuracao['porcentagem_split']}% sobre valor total do depósito de R$ " . number_format($valorDeposito, 2, ',', '.') . " (Lucro Gateway: R$ " . number_format($lucroBrutoGateway, 2, ',', '.') . ")"
            ]);

            // Criar registro do split executado
            $splitExecutado = SplitInternoExecutado::criarSplit(
                $configuracao['id'],
                $solicitacao->id,
                [
                    'usuario_pagador_id' => $userPagador->id,
                    'usuario_beneficiario_id' => $usuarioBeneficiario->id,
                    'valor_taxa_original' => $valorDeposito, // Valor total do depósito
                    'valor_split' => $valorSplit,
                    'porcentagem_aplicada' => $configuracao['porcentagem_split'],
                    'observacoes' => "Split interno da carteira gateway - {$configuracao['porcentagem_split']}% sobre valor total do depósito de R$ " . number_format($valorDeposito, 2, ',', '.') . " (Lucro Gateway: R$ " . number_format($lucroBrutoGateway, 2, ',', '.') . ")"
                ]
            );

            $splitExecutado->marcarComoProcessado('Split interno da carteira gateway executado com sucesso');

            Log::info('[SPLIT INTERNO GATEWAY] Split executado com sucesso', [
                'split_executado_id' => $splitExecutado->id,
                'usuario_beneficiario' => $usuarioBeneficiario->name,
                'valor_split' => $valorSplit,
                'saldo_anterior' => $saldoAnterior,
                'novo_saldo' => $usuarioBeneficiario->fresh()->saldo
            ]);

            return [
                'status' => 'completed',
                'split_id' => $splitExecutado->id,
                'valor_split' => $valorSplit,
                'usuario_beneficiario' => $usuarioBeneficiario->name,
                'message' => 'Split interno da carteira gateway executado com sucesso',
                'lucro_bruto_base' => $lucroBrutoGateway
            ];
        } catch (\Exception $e) {
            Log::error('[SPLIT INTERNO GATEWAY] Erro ao executar split', [
                'configuracao_id' => $configuracao['id'] ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Processa splits da carteira gateway automaticamente
     * Nova lógica: Calcula splits sobre o lucro bruto do gateway (taxaCashIn - taxaAdquirente)
     */
    public static function processarSplitsCarteiraGateway(Solicitacoes $solicitacao): array
    {
        try {
            Log::info('[SPLIT GATEWAY] Iniciando processamento automático de splits da carteira gateway', [
                'solicitacao_id' => $solicitacao->id,
                'valor_deposito' => $solicitacao->amount,
                'adquirente_ref' => $solicitacao->adquirente_ref
            ]);

            // Buscar configurações de split ativas para carteira gateway
            $splitsConfigurados = \App\Models\SplitInterno::ativos()
                ->where('tipo_taxa', \App\Models\SplitInterno::TAXA_CARTEIRA_GATEWAY)
                ->with(['usuarioBeneficiario'])
                ->get();

            if ($splitsConfigurados->isEmpty()) {
                Log::info('[SPLIT GATEWAY] Nenhuma configuração de split para carteira gateway encontrada');
                return [];
            }

            Log::info('[SPLIT GATEWAY] Configurações encontradas', [
                'count' => $splitsConfigurados->count(),
                'configuracoes' => $splitsConfigurados->toArray()
            ]);

            $resultados = [];
            $valorDeposito = (float) $solicitacao->amount;

            // === NOVA LÓGICA: Calcular lucro bruto do gateway ===

            // 1. Obter taxa cash in (taxa total cobrada ao cliente)
            $taxaCashIn = (float) ($solicitacao->taxa_cash_in ?? 0);

            // 2. Obter taxa da adquirente específica
            $taxaAdquirente = self::obterTaxaAdquirente($solicitacao);

            // 3. Calcular lucro bruto do gateway
            $lucroBrutoGateway = $taxaCashIn - $taxaAdquirente;

            Log::info('[SPLIT GATEWAY] Cálculo do lucro bruto', [
                'valor_deposito' => $valorDeposito,
                'taxa_cash_in' => $taxaCashIn,
                'taxa_adquirente' => $taxaAdquirente,
                'lucro_bruto_gateway' => $lucroBrutoGateway,
                'adquirente_ref' => $solicitacao->adquirente_ref
            ]);

            // Verificar se há lucro para distribuir
            if ($lucroBrutoGateway <= 0) {
                Log::info('[SPLIT GATEWAY] Lucro bruto é zero ou negativo, não há splits para processar', [
                    'lucro_bruto_gateway' => $lucroBrutoGateway
                ]);
                return [];
            }

            // Buscar carteira gateway
            $gatewayWallet = \App\Models\GatewayWallet::first();
            if (!$gatewayWallet) {
                Log::warning('[SPLIT GATEWAY] Carteira gateway não encontrada');
                return [];
            }

            $totalSplitsProcessados = 0;

            foreach ($splitsConfigurados as $split) {
                // Calcular split sobre o valor total do depósito (como solicitado)
                $valorSplit = ($valorDeposito * $split->porcentagem_split) / 100;

                if ($valorSplit > 0) {
                    Log::info('[SPLIT GATEWAY] Processando split', [
                        'split_id' => $split->id,
                        'usuario_beneficiario' => $split->usuario_beneficiario_id,
                        'porcentagem_split' => $split->porcentagem_split . '%',
                        'lucro_bruto_gateway' => $lucroBrutoGateway,
                        'valor_split' => $valorSplit
                    ]);

                    // Adicionar valor ao saldo do usuário beneficiário
                    $usuarioBeneficiario = $split->usuarioBeneficiario;
                    $saldoAnterior = $usuarioBeneficiario->saldo;
                    $usuarioBeneficiario->saldo += $valorSplit;
                    $usuarioBeneficiario->save();

                    // Recalcular saldo líquido
                    Helper::calculaSaldoLiquido($usuarioBeneficiario->user_id);

                    // DEBITAR o valor do usuário original que fez o depósito
                    $usuarioOriginal = User::where('user_id', $solicitacao->user_id)->first();
                    if ($usuarioOriginal) {
                        $saldoAnteriorOriginal = $usuarioOriginal->saldo;
                        $usuarioOriginal->saldo -= $valorSplit;
                        $usuarioOriginal->save();
                        Helper::calculaSaldoLiquido($usuarioOriginal->user_id);

                        Log::info('[SPLIT GATEWAY] Valor debitado do usuário original', [
                            'usuario_original_id' => $usuarioOriginal->id,
                            'saldo_anterior' => $saldoAnteriorOriginal,
                            'valor_debitado' => $valorSplit,
                            'novo_saldo' => $usuarioOriginal->fresh()->saldo
                        ]);
                    }

                    Log::info('[SPLIT GATEWAY] Valor creditado para beneficiário', [
                        'usuario_id' => $usuarioBeneficiario->id,
                        'saldo_anterior' => $saldoAnterior,
                        'valor_creditado' => $valorSplit,
                        'novo_saldo' => $usuarioBeneficiario->fresh()->saldo
                    ]);

                    // Criar transação virtual para aparecer no relatório
                    $idTransacaoSplit = 'split_gateway_' . $split->id . '_' . time();

                    \App\Models\Solicitacoes::create([
                        'user_id' => $usuarioBeneficiario->user_id,
                        'externalreference' => $idTransacaoSplit,
                        'idTransaction' => $idTransacaoSplit,
                        'amount' => $valorSplit,
                        'deposito_liquido' => $valorSplit, // Valor líquido igual ao valor total (sem taxa)
                        'taxa_cash_in' => 0, // Taxa sempre zero para splits
                        'taxa_pix_cash_in_adquirente' => 0,
                        'taxa_pix_cash_in_valor_fixo' => 0,
                        'status' => 'PAID_OUT',
                        'method' => 'comissao',
                        'adquirente_ref' => 'GATEWAY_SPLIT',
                        'executor_ordem' => 'gateway_split',
                        'descricao_transacao' => 'SPLIT_GATEWAY_AUTOMATICO',
                        'client_name' => 'Split Gateway Automático',
                        'client_document' => '08355037120',
                        'client_email' => $usuarioBeneficiario->email,
                        'client_telefone' => '11999999999',
                        'date' => now()->format('Y-m-d H:i:s'),
                        'qrcode_pix' => '',
                        'paymentcode' => '',
                        'paymentCodeBase64' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                        'observacoes' => "Split automático da carteira gateway - {$split->porcentagem_split}% sobre valor total do depósito de R$ " . number_format($valorDeposito, 2, ',', '.') . " (Lucro Gateway: R$ " . number_format($lucroBrutoGateway, 2, ',', '.') . ")"
                    ]);

                    // Registrar split executado
                    $splitExecutado = \App\Models\SplitInternoExecutado::create([
                        'split_internos_id' => $split->id,
                        'solicitacao_id' => $solicitacao->id, // Referenciar a solicitação original
                        'usuario_pagador_id' => $split->usuario_pagador_id,
                        'usuario_beneficiario_id' => $usuarioBeneficiario->id,
                        'valor_taxa_original' => $valorDeposito, // Valor total do depósito
                        'valor_split' => $valorSplit,
                        'porcentagem_aplicada' => $split->porcentagem_split,
                        'status' => \App\Models\SplitInternoExecutado::STATUS_PROCESSADO,
                        'processado_em' => now(),
                        'observacoes' => "Split automático da carteira gateway - {$split->porcentagem_split}% sobre valor total do depósito de R$ " . number_format($valorDeposito, 2, ',', '.') . " (Lucro Gateway: R$ " . number_format($lucroBrutoGateway, 2, ',', '.') . ")"
                    ]);

                    $resultados[] = [
                        'success' => true,
                        'split_executado_id' => $splitExecutado->id,
                        'usuario_beneficiario' => $usuarioBeneficiario->name,
                        'valor_split' => $valorSplit,
                        'porcentagem_aplicada' => $split->porcentagem_split,
                        'tipo' => 'carteira_gateway',
                        'lucro_bruto_base' => $lucroBrutoGateway
                    ];

                    $totalSplitsProcessados += $valorSplit;

                    Log::info('[SPLIT GATEWAY] Split processado com sucesso', [
                        'split_executado_id' => $splitExecutado->id,
                        'usuario_beneficiario' => $usuarioBeneficiario->name,
                        'valor_split' => $valorSplit
                    ]);
                }
            }

            // Calcular valor restante que permanece na carteira gateway
            $valorRestanteGateway = $lucroBrutoGateway - $totalSplitsProcessados;

            Log::info('[SPLIT GATEWAY] Processamento concluído', [
                'solicitacao_id' => $solicitacao->id,
                'total_splits_processados' => count($resultados),
                'valor_total_splits' => $totalSplitsProcessados,
                'valor_restante_gateway' => $valorRestanteGateway,
                'lucro_bruto_gateway' => $lucroBrutoGateway
            ]);

            return $resultados;
        } catch (\Exception $e) {
            Log::error('[SPLIT GATEWAY] Erro ao processar splits da carteira gateway', [
                'solicitacao_id' => $solicitacao->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [];
        }
    }
}
