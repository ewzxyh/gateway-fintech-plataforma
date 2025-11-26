<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GatewayWallet;
use App\Models\SplitInterno;
use Carbon\Carbon;

class ProcessarSplitsGateway extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:processar-splits {--periodo=hoje : Período para processar (hoje, ontem, 7dias, 30dias, tudo)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa splits da carteira gateway para usuários configurados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $periodo = $this->option('periodo');
        
        $this->info("Processando splits da carteira gateway para o período: {$periodo}");
        
        // Buscar carteira gateway com maior saldo
        $gatewayWallet = GatewayWallet::orderBy('saldo_gateway', 'desc')->first();
        
        if (!$gatewayWallet) {
            $this->error('Nenhuma carteira gateway encontrada.');
            return 1;
        }
        
        $this->info("Carteira Gateway encontrada:");
        $this->info("- Saldo Gateway: R$ " . number_format($gatewayWallet->saldo_gateway, 2, ',', '.'));
        $this->info("- Saldo Disponível: R$ " . number_format($gatewayWallet->saldo_disponivel, 2, ',', '.'));
        
        // Buscar configurações de split ativas para carteira gateway
        $splitsConfigurados = SplitInterno::ativos()
            ->where('tipo_taxa', SplitInterno::TAXA_CARTEIRA_GATEWAY)
            ->get();
            
        if ($splitsConfigurados->isEmpty()) {
            $this->info('Nenhuma configuração de split para carteira gateway encontrada.');
            return 0;
        }
        
        $this->info("Configurações de split encontradas: " . $splitsConfigurados->count());
        
        // Calcular valor total dos depósitos
        $valorTotalDepositos = \App\Models\Solicitacoes::where('status', 'PAID_OUT')->sum('amount');
        $totalSplits = 0;
        
        foreach ($splitsConfigurados as $split) {
            $valorSplit = ($valorTotalDepositos * $split->porcentagem_split) / 100;
            $totalSplits += $valorSplit;
            
            $this->info("- {$split->usuarioBeneficiario->name}: {$split->porcentagem_split}% = R$ " . number_format($valorSplit, 2, ',', '.'));
        }
        
        $this->info("Valor total dos depósitos: R$ " . number_format($valorTotalDepositos, 2, ',', '.'));
        $this->info("Total de splits a processar: R$ " . number_format($totalSplits, 2, ',', '.'));
        
        if ($this->confirm('Deseja processar os splits?')) {
            $totalProcessado = $gatewayWallet->processarSplits($valorTotalDepositos);
            
            $this->info("Splits processados com sucesso!");
            $this->info("Total processado: R$ " . number_format($totalProcessado, 2, ',', '.'));
            
            // Mostrar saldo atualizado
            $gatewayWallet->refresh();
            $this->info("Saldo Disponível atualizado: R$ " . number_format($gatewayWallet->saldo_disponivel, 2, ',', '.'));
        } else {
            $this->info('Processamento cancelado.');
        }
        
        return 0;
    }
}