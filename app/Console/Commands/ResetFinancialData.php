<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Solicitacoes;
use App\Models\SolicitacoesCashOut;
use App\Models\GatewayWallet;
use App\Models\SplitPayment;
use App\Models\SplitInternoExecutado;
use App\Models\Transactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetFinancialData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'financial:reset {--confirm : Confirma a operação sem perguntar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zera todos os dados financeiros do sistema (saldos, transações, splits, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚨 ATENÇÃO: Este comando irá ZERAR TODOS os dados financeiros do sistema!');
        $this->warn('Isso inclui:');
        $this->warn('- Todos os saldos dos usuários');
        $this->warn('- Todas as transações (depósitos e saques)');
        $this->warn('- Carteira gateway');
        $this->warn('- Todos os splits executados');
        $this->warn('- Histórico de transações');
        
        if (!$this->option('confirm')) {
            if (!$this->confirm('Tem certeza que deseja continuar? Esta ação é IRREVERSÍVEL!')) {
                $this->info('Operação cancelada.');
                return;
            }
        }

        $this->info('Iniciando limpeza dos dados financeiros...');
        
        try {
            DB::beginTransaction();
            
            // 1. Zerar saldos de todos os usuários
            $this->info('1. Zerando saldos dos usuários...');
            $usersUpdated = User::query()->update([
                'saldo' => 0,
                'updated_at' => now()
            ]);
            $this->info("   ✅ {$usersUpdated} usuários atualizados");
            
            // 2. Limpar todas as transações de depósito
            $this->info('2. Limpando transações de depósito...');
            $depositosCount = Solicitacoes::count();
            Solicitacoes::query()->delete();
            $this->info("   ✅ {$depositosCount} transações de depósito removidas");
            
            // 3. Limpar todas as transações de saque
            $this->info('3. Limpando transações de saque...');
            $saquesCount = SolicitacoesCashOut::count();
            SolicitacoesCashOut::query()->delete();
            $this->info("   ✅ {$saquesCount} transações de saque removidas");
            
            // 4. Limpar splits externos
            $this->info('4. Limpando splits externos...');
            $splitsCount = SplitPayment::count();
            SplitPayment::query()->delete();
            $this->info("   ✅ {$splitsCount} splits externos removidos");
            
            // 5. Limpar splits internos executados
            $this->info('5. Limpando splits internos executados...');
            $splitsInternosCount = SplitInternoExecutado::count();
            SplitInternoExecutado::query()->delete();
            $this->info("   ✅ {$splitsInternosCount} splits internos executados removidos");
            
            // 6. Limpar tabela de transações (se existir)
            $this->info('6. Limpando tabela de transações...');
            if (DB::getSchemaBuilder()->hasTable('transactions')) {
                $transactionsCount = Transactions::count();
                Transactions::query()->delete();
                $this->info("   ✅ {$transactionsCount} transações da tabela transactions removidas");
            } else {
                $this->info("   ⚠️  Tabela 'transactions' não encontrada");
            }
            
            // 7. Zerar carteira gateway
            $this->info('7. Zerando carteira gateway...');
            $gatewayWallets = GatewayWallet::all();
            foreach ($gatewayWallets as $wallet) {
                $wallet->update([
                    'saldo_gateway' => 0,
                    'saldo_disponivel' => 0,
                    'saldo_pendente' => 0,
                    'total_sacado' => 0,
                    'updated_at' => now()
                ]);
            }
            $this->info("   ✅ " . $gatewayWallets->count() . " carteiras gateway zeradas");
            
            // 8. Resetar auto-increment das tabelas principais
            $this->info('8. Resetando auto-increment das tabelas...');
            $tablesToReset = [
                'solicitacoes',
                'solicitacoes_cash_out', 
                'split_payments',
                'split_interno_executados',
                'transactions'
            ];
            
            foreach ($tablesToReset as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
                    $this->info("   ✅ Auto-increment da tabela '{$table}' resetado");
                } else {
                    $this->info("   ⚠️  Tabela '{$table}' não encontrada");
                }
            }
            
            
            $this->info('');
            $this->info('🎉 LIMPEZA CONCLUÍDA COM SUCESSO!');
            $this->info('');
            $this->info('📊 RESUMO:');
            $this->info("- Usuários atualizados: {$usersUpdated}");
            $this->info("- Depósitos removidos: {$depositosCount}");
            $this->info("- Saques removidos: {$saquesCount}");
            $this->info("- Splits externos removidos: {$splitsCount}");
            $this->info("- Splits internos removidos: {$splitsInternosCount}");
            $this->info("- Carteiras gateway zeradas: " . $gatewayWallets->count());
            $this->info('');
            $this->info('✅ O sistema está agora limpo e pronto para desenvolvimento!');
            
            // Log da operação
            Log::info('[RESET FINANCIAL] Sistema financeiro zerado', [
                'users_updated' => $usersUpdated,
                'depositos_removidos' => $depositosCount,
                'saques_removidos' => $saquesCount,
                'splits_externos_removidos' => $splitsCount,
                'splits_internos_removidos' => $splitsInternosCount,
                'gateway_wallets_zeradas' => $gatewayWallets->count(),
                'executed_by' => 'console_command'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ ERRO durante a limpeza:');
            $this->error($e->getMessage());
            Log::error('[RESET FINANCIAL] Erro durante limpeza', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
        
        return 0;
    }
}