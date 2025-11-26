<?php

namespace App\Observers;

use App\Models\Solicitacoes;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Log;

class SolicitacoesObserver
{
    private $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Handle the Solicitacoes "created" event.
     */
    public function created(Solicitacoes $solicitacoes): void
    {
        Log::info('[OBSERVER] SolicitacoesObserver::created chamado', [
            'solicitacao_id' => $solicitacoes->id,
            'status' => $solicitacoes->status,
            'user_id' => $solicitacoes->user_id,
            'amount' => $solicitacoes->deposito_liquido
        ]);
        
        // Status que indicam depósito aprovado (varia por adquirente)
        $approvedStatuses = ['PAID_OUT', 'COMPLETED', 'PAID', 'APPROVED'];
        
        // Verificar se o depósito já foi criado com status aprovado
        if (in_array($solicitacoes->status, $approvedStatuses)) {
            Log::info('[OBSERVER] Depósito criado com status aprovado - enviando notificação', [
                'solicitacao_id' => $solicitacoes->id,
                'user_id' => $solicitacoes->user_id,
                'status' => $solicitacoes->status,
                'amount' => $solicitacoes->deposito_liquido
            ]);
            
            // Processar taxa adquirente automaticamente
            $this->processarTaxaAdquirente($solicitacoes);
            
            // Processar splits internos automaticamente
            $this->processarSplitsInternos($solicitacoes);
            
            $this->sendDepositNotification($solicitacoes);
        } else {
            Log::info('[OBSERVER] Depósito criado sem status aprovado', [
                'solicitacao_id' => $solicitacoes->id,
                'status' => $solicitacoes->status
            ]);
        }
    }

    /**
     * Handle the Solicitacoes "updated" event.
     */
    public function updated(Solicitacoes $solicitacoes): void
    {
        Log::info('[OBSERVER] SolicitacoesObserver::updated chamado', [
            'solicitacao_id' => $solicitacoes->id,
            'old_status' => $solicitacoes->getOriginal('status'),
            'new_status' => $solicitacoes->status,
            'wasChanged' => $solicitacoes->wasChanged('status'),
            'user_id' => $solicitacoes->user_id
        ]);
        
        // Status que indicam depósito aprovado (varia por adquirente)
        $approvedStatuses = ['PAID_OUT', 'COMPLETED', 'PAID', 'APPROVED'];
        
        // Verificar se o status mudou para um status de aprovado
        if ($solicitacoes->wasChanged('status') && in_array($solicitacoes->status, $approvedStatuses)) {
            Log::info('[OBSERVER] Status mudou para aprovado - enviando notificação', [
                'solicitacao_id' => $solicitacoes->id,
                'user_id' => $solicitacoes->user_id,
                'status' => $solicitacoes->status,
                'amount' => $solicitacoes->deposito_liquido
            ]);
            
            // Processar taxa adquirente automaticamente
            $this->processarTaxaAdquirente($solicitacoes);
            
            // Processar splits internos automaticamente
            $this->processarSplitsInternos($solicitacoes);
            
            $this->sendDepositNotification($solicitacoes);
        } else {
            Log::info('[OBSERVER] Condições não atendidas para envio de notificação', [
                'wasChanged' => $solicitacoes->wasChanged('status'),
                'status' => $solicitacoes->status,
                'is_approved' => in_array($solicitacoes->status, $approvedStatuses)
            ]);
        }
    }

    /**
     * Handle the Solicitacoes "deleted" event.
     */
    public function deleted(Solicitacoes $solicitacoes): void
    {
        //
    }

    /**
     * Handle the Solicitacoes "restored" event.
     */
    public function restored(Solicitacoes $solicitacoes): void
    {
        //
    }

    /**
     * Handle the Solicitacoes "force deleted" event.
     */
    public function forceDeleted(Solicitacoes $solicitacoes): void
    {
        //
    }

    /**
     * Enviar notificação de depósito aprovado
     */
    private function sendDepositNotification(Solicitacoes $solicitacao): void
    {
        try {
            Log::info('[OBSERVER] Enviando notificação de depósito aprovado', [
                'solicitacao_id' => $solicitacao->id,
                'user_id' => $solicitacao->user_id,
                'amount' => $solicitacao->deposito_liquido,
                'transaction_id' => $solicitacao->idTransaction ?? $solicitacao->id
            ]);

            $result = $this->pushService->sendDepositNotification(
                $solicitacao->user_id,
                $solicitacao->deposito_liquido,
                $solicitacao->idTransaction ?? $solicitacao->id
            );

            Log::info('[OBSERVER] Resultado do envio da notificação', [
                'solicitacao_id' => $solicitacao->id,
                'success' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('[OBSERVER] Erro ao enviar notificação de depósito via Observer', [
                'solicitacao_id' => $solicitacao->id,
                'user_id' => $solicitacao->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Processa taxa adquirente automaticamente quando um depósito é aprovado
     */
    private function processarTaxaAdquirente(Solicitacoes $solicitacao): void
    {
        try {
            Log::info('[OBSERVER] Processando taxa adquirente automaticamente', [
                'solicitacao_id' => $solicitacao->id,
                'adquirente_ref' => $solicitacao->adquirente_ref,
                'amount' => $solicitacao->amount
            ]);

            // Buscar carteira gateway atual
            $gatewayWallet = \App\Models\GatewayWallet::orderBy('saldo_gateway', 'desc')->first();
            if (!$gatewayWallet) {
                Log::warning('[OBSERVER] Carteira gateway não encontrada');
                return;
            }

            $taxaAdquirente = 0;

            // Processar taxa da Woovi
            if ($solicitacao->adquirente_ref === 'woovi') {
                $woovi = \App\Models\Woovi::first();
                if ($woovi && $woovi->taxa_adquirente_entradas > 0) {
                    $taxaAdquirente = ($solicitacao->amount * $woovi->taxa_adquirente_entradas) / 100;
                    
                    Log::info('[OBSERVER] Taxa da Woovi calculada', [
                        'taxa_percentual' => $woovi->taxa_adquirente_entradas . '%',
                        'valor_transacao' => $solicitacao->amount,
                        'taxa_calculada' => $taxaAdquirente
                    ]);
                }
            }

            // Processar taxa da XDPag
            if ($solicitacao->adquirente_ref === 'xdpag') {
                $xdpag = \App\Models\XDPag::first();
                if ($xdpag && $xdpag->taxa_adquirente_entradas > 0) {
                    $taxaAdquirente = ($solicitacao->amount * $xdpag->taxa_adquirente_entradas) / 100;
                    
                    Log::info('[OBSERVER] Taxa da XDPag calculada', [
                        'taxa_percentual' => $xdpag->taxa_adquirente_entradas . '%',
                        'valor_transacao' => $solicitacao->amount,
                        'taxa_calculada' => $taxaAdquirente
                    ]);
                }
            }

            // Processar taxa da Witetec
            if ($solicitacao->adquirente_ref === 'witetec') {
                $witetec = \App\Models\Witetec::first();
                if ($witetec && $witetec->taxa_adquirente_entradas > 0) {
                    $taxaAdquirente = ($solicitacao->amount * $witetec->taxa_adquirente_entradas) / 100;
                    
                    Log::info('[OBSERVER] Taxa da Witetec calculada', [
                        'taxa_percentual' => $witetec->taxa_adquirente_entradas . '%',
                        'valor_transacao' => $solicitacao->amount,
                        'taxa_calculada' => $taxaAdquirente
                    ]);
                }
            }

            // Processar taxa do Stripe
            if ($solicitacao->adquirente_ref === 'stripe' || $solicitacao->adquirente_ref === 'Stripe') {
                $stripe = \App\Models\StripeConfig::first();
                if ($stripe && $stripe->taxa_adquirente_entradas > 0) {
                    $taxaAdquirente = ($solicitacao->amount * $stripe->taxa_adquirente_entradas) / 100;
                    
                    Log::info('[OBSERVER] Taxa do Stripe calculada', [
                        'taxa_percentual' => $stripe->taxa_adquirente_entradas . '%',
                        'valor_transacao' => $solicitacao->amount,
                        'taxa_calculada' => $taxaAdquirente
                    ]);
                }
            }

            // Processar taxa do Pagar.me
            if ($solicitacao->adquirente_ref === 'pagarme' || $solicitacao->adquirente_ref === 'PagarMe') {
                $pagarme = \App\Models\PagarMe::first();
                if ($pagarme && $pagarme->taxa_adquirente_entradas > 0) {
                    $taxaAdquirente = ($solicitacao->amount * $pagarme->taxa_adquirente_entradas) / 100;
                    
                    Log::info('[OBSERVER] Taxa do Pagar.me calculada', [
                        'taxa_percentual' => $pagarme->taxa_adquirente_entradas . '%',
                        'valor_transacao' => $solicitacao->amount,
                        'taxa_calculada' => $taxaAdquirente
                    ]);
                }
            }

            // Processar taxa da Rede
            if ($solicitacao->adquirente_ref === 'rede' || $solicitacao->adquirente_ref === 'Rede') {
                $rede = \App\Models\Rede::first();
                if ($rede && $rede->taxa_adquirente_entradas > 0) {
                    $taxaAdquirente = ($solicitacao->amount * $rede->taxa_adquirente_entradas) / 100;
                    
                    Log::info('[OBSERVER] Taxa da Rede calculada', [
                        'taxa_percentual' => $rede->taxa_adquirente_entradas . '%',
                        'valor_transacao' => $solicitacao->amount,
                        'taxa_calculada' => $taxaAdquirente
                    ]);
                }
            }

            // Processar taxa da Asaas
            if ($solicitacao->adquirente_ref === 'asaas' || $solicitacao->adquirente_ref === 'Asaas') {
                $asaas = \App\Models\Asaas::first();
                if ($asaas && $asaas->taxa_adquirente_entradas > 0) {
                    $taxaAdquirente = ($solicitacao->amount * $asaas->taxa_adquirente_entradas) / 100;
                    
                    Log::info('[OBSERVER] Taxa da Asaas calculada', [
                        'taxa_percentual' => $asaas->taxa_adquirente_entradas . '%',
                        'valor_transacao' => $solicitacao->amount,
                        'taxa_calculada' => $taxaAdquirente
                    ]);
                }
            }

            // Processar taxa da Pixup
            if ($solicitacao->adquirente_ref === 'pixup' || $solicitacao->adquirente_ref === 'Pixup') {
                $pixup = \App\Models\Pixup::first();
                if ($pixup && $pixup->taxa_adquirente_entradas > 0) {
                    $taxaAdquirente = ($solicitacao->amount * $pixup->taxa_adquirente_entradas) / 100;
                    
                    Log::info('[OBSERVER] Taxa da Pixup calculada', [
                        'taxa_percentual' => $pixup->taxa_adquirente_entradas . '%',
                        'valor_transacao' => $solicitacao->amount,
                        'taxa_calculada' => $taxaAdquirente
                    ]);
                }
            }

            // Processar taxa da TrustyPix
            if ($solicitacao->adquirente_ref === 'trustypix') {
                $trustypix = \App\Models\TrustyPix::first();
                if ($trustypix && $trustypix->taxa_adquirente_entradas > 0) {
                    $taxaAdquirente = ($solicitacao->amount * $trustypix->taxa_adquirente_entradas) / 100;
                    
                    Log::info('[OBSERVER] Taxa da TrustyPix calculada', [
                        'taxa_percentual' => $trustypix->taxa_adquirente_entradas . '%',
                        'valor_transacao' => $solicitacao->amount,
                        'taxa_calculada' => $taxaAdquirente
                    ]);
                }
            }

            // Aplicar taxa adquirente se calculada
            if ($taxaAdquirente > 0) {
                $taxaAdquirenteAnterior = $gatewayWallet->taxa_adquirente_total;
                $gatewayWallet->taxa_adquirente_total += $taxaAdquirente;
                $gatewayWallet->calcularSaldoGateway();
                $gatewayWallet->save();

                Log::info('[OBSERVER] Taxa adquirente aplicada à carteira gateway', [
                    'solicitacao_id' => $solicitacao->id,
                    'adquirente' => $solicitacao->adquirente_ref,
                    'taxa_anterior' => $taxaAdquirenteAnterior,
                    'taxa_adicionada' => $taxaAdquirente,
                    'taxa_atual' => $gatewayWallet->taxa_adquirente_total,
                    'saldo_gateway_atual' => $gatewayWallet->saldo_gateway
                ]);
            } else {
                Log::info('[OBSERVER] Nenhuma taxa adquirente aplicável', [
                    'solicitacao_id' => $solicitacao->id,
                    'adquirente_ref' => $solicitacao->adquirente_ref
                ]);
            }

        } catch (\Exception $e) {
            Log::error('[OBSERVER] Erro ao processar taxa adquirente', [
                'solicitacao_id' => $solicitacao->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Processar splits internos automaticamente quando depósito é aprovado
     */
    private function processarSplitsInternos(Solicitacoes $solicitacao): void
    {
        try {
            Log::info('[OBSERVER] Processando splits internos', [
                'solicitacao_id' => $solicitacao->id,
                'user_id' => $solicitacao->user_id,
                'amount' => $solicitacao->amount
            ]);

            // Buscar usuário
            $user = \App\Models\User::where('user_id', $solicitacao->user_id)->first();
            if (!$user) {
                Log::warning('[OBSERVER] Usuário não encontrado para processar splits', [
                    'solicitacao_id' => $solicitacao->id,
                    'user_id' => $solicitacao->user_id
                ]);
                return;
            }

            // Processar splits internos usando o SplitTrait (inclui splits da carteira gateway)
            $resultadosSplitsInternos = \App\Traits\SplitTrait::processarSplitsInternos($solicitacao, $user);

            Log::info('[OBSERVER] Splits internos processados', [
                'solicitacao_id' => $solicitacao->id,
                'splits_internos' => count($resultadosSplitsInternos)
            ]);

        } catch (\Exception $e) {
            Log::error('[OBSERVER] Erro ao processar splits internos', [
                'solicitacao_id' => $solicitacao->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
