<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GatewayWallet extends Model
{
    use HasFactory;

    protected $table = 'gateway_wallets';
    
    protected $fillable = [
        'taxa_sistema_total',
        'taxa_adquirente_total',
        'saldo_gateway',
        'saldo_disponivel',
        'saldo_pendente',
        'total_sacado',
        'periodo_inicio',
        'periodo_fim',
        'transacoes_count'
    ];

    protected $casts = [
        'taxa_sistema_total' => 'decimal:2',
        'taxa_adquirente_total' => 'decimal:2',
        'saldo_gateway' => 'decimal:2',
        'saldo_disponivel' => 'decimal:2',
        'saldo_pendente' => 'decimal:2',
        'total_sacado' => 'decimal:2',
        'periodo_inicio' => 'date',
        'periodo_fim' => 'date',
        'transacoes_count' => 'integer'
    ];

    /**
     * Calcula o saldo da carteira gateway baseado nas taxas do sistema
     * menos as taxas pagas aos adquirentes
     */
    public function calcularSaldoGateway()
    {
        $this->saldo_gateway = $this->taxa_sistema_total - $this->taxa_adquirente_total;
        
        // Atualizar saldo disponível (saldo gateway - total sacado - saldo pendente)
        $this->saldo_disponivel = $this->saldo_gateway - $this->total_sacado - $this->saldo_pendente;
        
        return $this->saldo_gateway;
    }

    /**
     * Adiciona valor ao saldo disponível (quando há lucro)
     */
    public function adicionarSaldo($valor)
    {
        $this->saldo_gateway += $valor;
        $this->saldo_disponivel += $valor;
        $this->save();
        
        // Sincronizar saldo do usuário gateway_wallet
        $this->sincronizarSaldoUsuario();
        
        return $this;
    }

    /**
     * Processa um saque da carteira gateway
     */
    public function processarSaque($valor)
    {
        if ($this->saldo_disponivel >= $valor) {
            $this->saldo_pendente += $valor;
            $this->saldo_disponivel -= $valor;
            $this->save();
            
            // Sincronizar saldo do usuário gateway_wallet
            $this->sincronizarSaldoUsuario();
            
            return true;
        }
        return false;
    }

    /**
     * Confirma um saque (move de pendente para sacado)
     */
    public function confirmarSaque($valor)
    {
        $this->saldo_pendente -= $valor;
        $this->total_sacado += $valor;
        $this->save();
        
        // Sincronizar saldo do usuário gateway_wallet
        $this->sincronizarSaldoUsuario();
        
        return $this;
    }

    /**
     * Busca ou cria registro da carteira gateway para um período específico
     */
    public static function buscarOuCriar($dataInicio, $dataFim)
    {
        $gatewayWallet = self::where('periodo_inicio', $dataInicio)
                           ->where('periodo_fim', $dataFim)
                           ->first();

        if (!$gatewayWallet) {
            $gatewayWallet = self::create([
                'taxa_sistema_total' => 0,
                'taxa_adquirente_total' => 0,
                'saldo_gateway' => 0,
                'saldo_disponivel' => 0,
                'saldo_pendente' => 0,
                'total_sacado' => 0,
                'periodo_inicio' => $dataInicio,
                'periodo_fim' => $dataFim,
                'transacoes_count' => 0
            ]);
        }

        return $gatewayWallet;
    }

    /**
     * Atualiza os valores da carteira gateway baseado nas transações reais
     * Usa as taxas calculadas dinamicamente pelo sistema
     */
    public function atualizarValores($taxaSistemaTotal, $taxaAdquirenteTotal, $transacoesCount)
    {
        $this->taxa_sistema_total = $taxaSistemaTotal;
        $this->taxa_adquirente_total = $taxaAdquirenteTotal;
        $this->transacoes_count = $transacoesCount;
        
        // Recalcular saldo gateway e disponível
        $this->calcularSaldoGateway();
        $this->save();

        // Sincronizar saldo do usuário gateway_wallet
        $this->sincronizarSaldoUsuario();

        return $this;
    }

    /**
     * Processa splits da carteira gateway para usuários específicos
     * Calcula sobre o valor total dos depósitos, não sobre o lucro da carteira
     */
    public function processarSplits($valorTotalDepositos)
    {
        // Buscar configurações de split ativas para carteira gateway
        $splitsConfigurados = \App\Models\SplitInterno::ativos()
            ->where('tipo_taxa', \App\Models\SplitInterno::TAXA_CARTEIRA_GATEWAY)
            ->get();

        $totalSplits = 0;

        foreach ($splitsConfigurados as $split) {
            // Calcular split sobre o valor total dos depósitos
            $valorSplit = ($valorTotalDepositos * $split->porcentagem_split) / 100;
            
            if ($valorSplit > 0) {
                // Adicionar valor ao saldo do usuário beneficiário
                $usuarioBeneficiario = $split->usuarioBeneficiario;
                $usuarioBeneficiario->saldo += $valorSplit;
                $usuarioBeneficiario->save();

                // Criar transação virtual para aparecer no relatório
                $idTransacaoSplit = 'split_' . $split->id . '_' . time();
                
                \App\Models\Solicitacoes::create([
                    'user_id' => $usuarioBeneficiario->user_id,
                    'externalreference' => $idTransacaoSplit,
                    'amount' => $valorSplit,
                    'deposito_liquido' => $valorSplit, // Valor líquido igual ao valor total (sem taxa)
                    'taxa_cash_in' => 0, // Taxa sempre zero para splits
                    'taxa_pix_cash_in_adquirente' => 0,
                    'taxa_pix_cash_in_valor_fixo' => 0,
                    'client_name' => 'GATEWAY ADMIN',
                    'client_email' => 'redacted@example.invalid',
                    'client_document' => '40.073.909/0001-94',
                    'client_telefone' => '11999999999',
                    'method' => 'comissao', // Novo método para identificar splits
                    'status' => 'PAID_OUT',
                    'date' => now(),
                    'idTransaction' => $idTransacaoSplit,
                    'adquirente_ref' => 'gateway_split',
                    'executor_ordem' => 'gateway_split',
                    'descricao_transacao' => 'COMISSAO_SPLIT',
                    'qrcode_pix' => '',
                    'paymentcode' => '',
                    'paymentCodeBase64' => '',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Registrar split executado
                \App\Models\SplitInternoExecutado::create([
                    'split_internos_id' => $split->id,
                    'solicitacao_id' => null, // Não há solicitação específica para splits da carteira gateway
                    'usuario_pagador_id' => $split->usuario_pagador_id, // Usar o ID do pagador da configuração
                    'usuario_beneficiario_id' => $usuarioBeneficiario->id,
                    'valor_taxa_original' => $valorTotalDepositos,
                    'valor_split' => $valorSplit,
                    'porcentagem_aplicada' => $split->porcentagem_split,
                    'status' => \App\Models\SplitInternoExecutado::STATUS_PROCESSADO,
                    'processado_em' => now(),
                    'observacoes' => 'Split automático da carteira gateway sobre valor total dos depósitos'
                ]);

                $totalSplits += $valorSplit;
            }
        }

        // Subtrair o total de splits do saldo disponível da carteira gateway
        if ($totalSplits > 0) {
            $this->saldo_disponivel -= $totalSplits;
            $this->save();
        }

        return $totalSplits;
    }

    /**
     * Sincroniza o saldo do usuário gateway_wallet com o saldo da carteira gateway
     * Este método deve ser chamado sempre que o saldo da carteira gateway for atualizado
     */
    public function sincronizarSaldoUsuario()
    {
        $gatewayUser = \App\Models\User::where('user_id', 'gateway_wallet')->first();
        
        if (!$gatewayUser) {
            \Log::error('GatewayWallet::sincronizarSaldoUsuario - Usuário gateway_wallet não encontrado');
            return false;
        }

        // Atualizar saldo do usuário para refletir o saldo disponível da carteira gateway
        $gatewayUser->saldo = $this->saldo_disponivel;
        $gatewayUser->save();

        \Log::info('GatewayWallet::sincronizarSaldoUsuario - Saldo sincronizado', [
            'saldo_carteira_gateway' => $this->saldo_disponivel,
            'saldo_usuario_atualizado' => $gatewayUser->saldo
        ]);

        return true;
    }

    /**
     * Sincroniza o saldo do usuário gateway_wallet com o saldo total calculado da carteira gateway
     * Versão estática que calcula o saldo em tempo real
     */
    public static function sincronizarSaldoUsuarioComSaldoTotal()
    {
        $gatewayUser = \App\Models\User::where('user_id', 'gateway_wallet')->first();
        
        if (!$gatewayUser) {
            \Log::error('GatewayWallet::sincronizarSaldoUsuarioComSaldoTotal - Usuário gateway_wallet não encontrado');
            return false;
        }

        // Calcular saldo total atual da carteira gateway
        $saldoTotal = self::calcularSaldoTotal();
        
        // Atualizar saldo do usuário para refletir o saldo disponível calculado
        $gatewayUser->saldo = $saldoTotal['saldo_disponivel'];
        $gatewayUser->save();

        \Log::info('GatewayWallet::sincronizarSaldoUsuarioComSaldoTotal - Saldo sincronizado', [
            'saldo_carteira_gateway_calculado' => $saldoTotal['saldo_disponivel'],
            'saldo_usuario_atualizado' => $gatewayUser->saldo
        ]);

        return true;
    }

    /**
     * Calcula o saldo total da carteira gateway independente do período
     * Similar ao Helper::calculaSaldoLiquido() para usuários
     */
    public static function calcularSaldoTotal()
    {
        try {
            // Calcular taxas do sistema totais (todas as transações PAID_OUT)
            $taxaSistemaTotal = \App\Models\Solicitacoes::where('status', 'PAID_OUT')
                ->sum('taxa_cash_in') + 
                \App\Models\SolicitacoesCashOut::where('status', 'COMPLETED')
                ->sum('taxa_cash_out');

            // Calcular taxas pagas aos adquirentes totais
            $taxaAdquirenteTotal = 0;
            
            // Buscar taxas de todas as adquirentes configuradas
            $xdpag = \App\Models\XDPag::first();
            $woovi = \App\Models\Woovi::first();
            $witetec = \App\Models\Witetec::first();
            $bspay = \App\Models\BSPay::first();
            $pixup = \App\Models\Pixup::first();
            $trustypix = \App\Models\TrustyPix::first();
            
            // Calcular taxas da XDPag
            if ($xdpag && $xdpag->taxa_adquirente_entradas > 0) {
                $query = \App\Models\Solicitacoes::where('status', 'PAID_OUT')
                    ->where('adquirente_ref', 'xdpag');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($xdpag->taxa_adquirente_entradas_configurada_em) {
                    $query->where('created_at', '>=', $xdpag->taxa_adquirente_entradas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($xdpag->taxa_adquirente_entradas / 100)));
            }
            if ($xdpag && $xdpag->taxa_adquirente_saidas > 0) {
                $query = \App\Models\SolicitacoesCashOut::where('status', 'COMPLETED')
                    ->where('executor_ordem', 'xdpag');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($xdpag->taxa_adquirente_saidas_configurada_em) {
                    $query->where('created_at', '>=', $xdpag->taxa_adquirente_saidas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($xdpag->taxa_adquirente_saidas / 100)));
            }
            
            // Calcular taxas da Woovi
            if ($woovi && $woovi->taxa_adquirente_entradas > 0) {
                $query = \App\Models\Solicitacoes::where('status', 'PAID_OUT')
                    ->where('adquirente_ref', 'woovi');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($woovi->taxa_adquirente_entradas_configurada_em) {
                    $query->where('created_at', '>=', $woovi->taxa_adquirente_entradas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($woovi->taxa_adquirente_entradas / 100)));
            }
            if ($woovi && $woovi->taxa_adquirente_saidas > 0) {
                $query = \App\Models\SolicitacoesCashOut::where('status', 'COMPLETED')
                    ->where('executor_ordem', 'woovi');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($woovi->taxa_adquirente_saidas_configurada_em) {
                    $query->where('created_at', '>=', $woovi->taxa_adquirente_saidas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($woovi->taxa_adquirente_saidas / 100)));
            }
            
            // Calcular taxas da Witetec
            if ($witetec && $witetec->taxa_adquirente_entradas > 0) {
                $query = \App\Models\Solicitacoes::where('status', 'PAID_OUT')
                    ->where('adquirente_ref', 'witetec');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($witetec->taxa_adquirente_entradas_configurada_em) {
                    $query->where('created_at', '>=', $witetec->taxa_adquirente_entradas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($witetec->taxa_adquirente_entradas / 100)));
            }
            if ($witetec && $witetec->taxa_adquirente_saidas > 0) {
                $query = \App\Models\SolicitacoesCashOut::where('status', 'COMPLETED')
                    ->where('executor_ordem', 'witetec');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($witetec->taxa_adquirente_saidas_configurada_em) {
                    $query->where('created_at', '>=', $witetec->taxa_adquirente_saidas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($witetec->taxa_adquirente_saidas / 100)));
            }

            // Calcular taxas da BSPay
            if ($bspay && $bspay->taxa_adquirente_entradas > 0) {
                $query = \App\Models\Solicitacoes::where('status', 'PAID_OUT')
                    ->where('adquirente_ref', 'bspay');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($bspay->taxa_adquirente_entradas_configurada_em) {
                    $query->where('created_at', '>=', $bspay->taxa_adquirente_entradas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($bspay->taxa_adquirente_entradas / 100)));
            }
            if ($bspay && $bspay->taxa_adquirente_saidas > 0) {
                $query = \App\Models\SolicitacoesCashOut::where('status', 'COMPLETED')
                    ->where('executor_ordem', 'bspay');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($bspay->taxa_adquirente_saidas_configurada_em) {
                    $query->where('created_at', '>=', $bspay->taxa_adquirente_saidas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($bspay->taxa_adquirente_saidas / 100)));
            }

            // Calcular taxas da Pixup
            if ($pixup && $pixup->taxa_adquirente_entradas > 0) {
                $query = \App\Models\Solicitacoes::where('status', 'PAID_OUT')
                    ->where('adquirente_ref', 'pixup');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($pixup->taxa_adquirente_entradas_configurada_em) {
                    $query->where('created_at', '>=', $pixup->taxa_adquirente_entradas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($pixup->taxa_adquirente_entradas / 100)));
            }
            if ($pixup && $pixup->taxa_adquirente_saidas > 0) {
                $query = \App\Models\SolicitacoesCashOut::where('status', 'COMPLETED')
                    ->where('executor_ordem', 'pixup');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($pixup->taxa_adquirente_saidas_configurada_em) {
                    $query->where('created_at', '>=', $pixup->taxa_adquirente_saidas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($pixup->taxa_adquirente_saidas / 100)));
            }

            // Calcular taxas da TrustyPix
            if ($trustypix && $trustypix->taxa_adquirente_entradas > 0) {
                $query = \App\Models\Solicitacoes::where('status', 'PAID_OUT')
                    ->where('adquirente_ref', 'trustypix');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($trustypix->taxa_adquirente_entradas_configurada_em) {
                    $query->where('created_at', '>=', $trustypix->taxa_adquirente_entradas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($trustypix->taxa_adquirente_entradas / 100)));
            }
            if ($trustypix && $trustypix->taxa_adquirente_saidas > 0) {
                $query = \App\Models\SolicitacoesCashOut::where('status', 'COMPLETED')
                    ->where('executor_ordem', 'trustypix');
                
                // Aplicar taxa apenas para transações posteriores à configuração
                if ($trustypix->taxa_adquirente_saidas_configurada_em) {
                    $query->where('created_at', '>=', $trustypix->taxa_adquirente_saidas_configurada_em);
                }
                
                $taxaAdquirenteTotal += $query->sum(\DB::raw('amount * ' . ($trustypix->taxa_adquirente_saidas / 100)));
            }
            
            // Calcular saldo total da carteira gateway
            $saldoGatewayTotal = $taxaSistemaTotal - $taxaAdquirenteTotal;

            // Calcular saques pendentes da carteira gateway
            $saquesPendentes = \App\Models\SolicitacoesCashOut::where('user_id', 'gateway_wallet')
                ->where('status', 'PENDING')
                ->sum('amount');

            // Calcular total já sacado da carteira gateway
            $totalSacado = \App\Models\SolicitacoesCashOut::where('user_id', 'gateway_wallet')
                ->whereIn('status', ['COMPLETED', 'PAID_OUT'])
                ->sum('amount');

            // Calcular total de splits executados (que devem ser descontados do saldo disponível)
            // CORREÇÃO: Descontar apenas splits relacionados à carteira gateway
            $totalSplitsExecutados = \App\Models\SplitInternoExecutado::where('status', \App\Models\SplitInternoExecutado::STATUS_PROCESSADO)
                ->where(function($query) {
                    $query->where('usuario_pagador_id', 'gateway_wallet')
                          ->orWhere('usuario_beneficiario_id', 'gateway_wallet');
                })
                ->sum('valor_split');

            // Calcular saldo disponível (saldo total - saques pendentes - total sacado - splits executados)
            $saldoDisponivel = $saldoGatewayTotal - $saquesPendentes - $totalSacado - $totalSplitsExecutados;

            return [
                'taxa_sistema_total' => $taxaSistemaTotal,
                'taxa_adquirente_total' => $taxaAdquirenteTotal,
                'saldo_gateway_total' => $saldoGatewayTotal,
                'saldo_disponivel' => $saldoDisponivel,
                'saldo_pendente' => $saquesPendentes,
                'total_sacado' => $totalSacado,
                'total_splits_executados' => $totalSplitsExecutados
            ];

        } catch (\Exception $e) {
            \Log::error('GatewayWallet::calcularSaldoTotal - Erro: ' . $e->getMessage());
            return [
                'taxa_sistema_total' => 0,
                'taxa_adquirente_total' => 0,
                'saldo_gateway_total' => 0,
                'saldo_disponivel' => 0,
                'saldo_pendente' => 0,
                'total_sacado' => 0
            ];
        }
    }
}
