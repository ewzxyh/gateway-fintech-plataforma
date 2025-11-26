<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GatewayWallet;
use App\Models\User;

class SyncGatewayWalletBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:sync-balance {--force : Força a sincronização mesmo se os valores já estiverem iguais}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza o saldo do usuário gateway_wallet com o saldo da carteira gateway';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando sincronização do saldo da carteira gateway...');

        // Buscar usuário gateway_wallet
        $gatewayUser = User::where('user_id', 'gateway_wallet')->first();
        
        if (!$gatewayUser) {
            $this->error('❌ Usuário gateway_wallet não encontrado!');
            return 1;
        }

        // Calcular saldo total da carteira gateway
        $saldoTotal = GatewayWallet::calcularSaldoTotal();
        
        $this->info('📊 Saldo calculado da carteira gateway:');
        $this->table(
            ['Item', 'Valor'],
            [
                ['Taxa Sistema Total', 'R$ ' . number_format($saldoTotal['taxa_sistema_total'], 2, ',', '.')],
                ['Taxa Adquirente Total', 'R$ ' . number_format($saldoTotal['taxa_adquirente_total'], 2, ',', '.')],
                ['Saldo Gateway Total', 'R$ ' . number_format($saldoTotal['saldo_gateway_total'], 2, ',', '.')],
                ['Saldo Disponível', 'R$ ' . number_format($saldoTotal['saldo_disponivel'], 2, ',', '.')],
                ['Saldo Pendente', 'R$ ' . number_format($saldoTotal['saldo_pendente'], 2, ',', '.')],
                ['Total Sacado', 'R$ ' . number_format($saldoTotal['total_sacado'], 2, ',', '.')],
            ]
        );

        $saldoAtualUsuario = $gatewayUser->saldo;
        $saldoCarteiraGateway = $saldoTotal['saldo_disponivel'];

        $this->info("💰 Saldo atual do usuário gateway_wallet: R$ " . number_format($saldoAtualUsuario, 2, ',', '.'));
        $this->info("💰 Saldo disponível da carteira gateway: R$ " . number_format($saldoCarteiraGateway, 2, ',', '.'));

        // Verificar se já está sincronizado
        if (!$this->option('force') && $saldoAtualUsuario == $saldoCarteiraGateway) {
            $this->info('✅ Os saldos já estão sincronizados!');
            $this->info('💡 Use --force para forçar a sincronização mesmo assim.');
            return 0;
        }

        // Confirmar sincronização
        if (!$this->option('force')) {
            if (!$this->confirm('Deseja continuar com a sincronização?')) {
                $this->info('❌ Sincronização cancelada.');
                return 0;
            }
        }

        // Executar sincronização
        $gatewayUser->saldo = $saldoCarteiraGateway;
        $gatewayUser->save();

        $this->info('✅ Sincronização concluída com sucesso!');
        $this->info("💰 Novo saldo do usuário gateway_wallet: R$ " . number_format($gatewayUser->fresh()->saldo, 2, ',', '.'));

        // Log da operação
        \Log::info('Comando SyncGatewayWalletBalance executado', [
            'saldo_antes' => $saldoAtualUsuario,
            'saldo_depois' => $gatewayUser->fresh()->saldo,
            'saldo_carteira_gateway' => $saldoCarteiraGateway,
            'executado_por' => 'artisan_command'
        ]);

        return 0;
    }
}