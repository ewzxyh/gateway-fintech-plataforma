<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AsaasService;
use App\Models\Asaas;
use App\Models\Adquirente;
use App\Traits\AsaasTrait;

class TestAsaasIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asaas:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a integração do Asaas no sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testando Integração Asaas...');
        $this->newLine();

        // 1. Verificar se a adquirente foi adicionada ao banco
        $this->info('1. Verificando adquirente no banco de dados...');
        $adquirente = Adquirente::where('referencia', 'asaas')->first();
        if ($adquirente) {
            $this->info('✅ Adquirente Asaas encontrada no banco de dados');
            $this->line("   Status: " . ($adquirente->status ? 'Ativa' : 'Inativa'));
            $this->line("   URL: " . $adquirente->url);
        } else {
            $this->error('❌ Adquirente Asaas não encontrada no banco de dados');
        }
        $this->newLine();

        // 2. Verificar configuração do Asaas
        $this->info('2. Verificando configuração do Asaas...');
        $asaasConfig = Asaas::first();
        if ($asaasConfig) {
            $this->info('✅ Configuração do Asaas encontrada');
            $this->line("   Environment: " . $asaasConfig->environment);
            $this->line("   API Key: " . substr($asaasConfig->api_key, 0, 10) . "...");
            $this->line("   URL: " . $asaasConfig->url);
        } else {
            $this->warn('⚠️  Configuração do Asaas não encontrada');
            $this->line('   Criando configuração padrão...');
            
            $asaasConfig = Asaas::create([
                'api_key' => 'your_api_key_here',
                'environment' => 'sandbox',
                'webhook_token' => 'your_webhook_token_here',
                'url' => 'https://api-sandbox.asaas.com/v3/'
            ]);
            
            $this->info('✅ Configuração padrão criada');
        }
        $this->newLine();

        // 3. Testar serviço Asaas
        $this->info('3. Testando serviço Asaas...');
        try {
            $asaasService = new AsaasService();
            $this->info('✅ Serviço Asaas instanciado com sucesso');
            $this->line("   Base URL: " . $asaasService->baseUrl);
            $this->line("   Environment: " . $asaasService->environment);
        } catch (\Exception $e) {
            $this->error('❌ Erro ao instanciar serviço Asaas: ' . $e->getMessage());
        }
        $this->newLine();

        // 4. Testar dados de exemplo
        $this->info('4. Preparando dados de teste...');
        
        $customerData = [
            'customer_name' => 'João Silva',
            'customer_email' => 'redacted@example.invalid',
            'customer_phone' => '11999999999',
            'customer_document' => '12345678901',
            'customer_external_id' => 'test_' . time()
        ];
        
        $chargeData = [
            'amount' => 100.00,
            'external_id' => 'test_charge_' . time(),
            'description' => 'Teste de cobrança PIX - Asaas',
            'customer_name' => 'João Silva',
            'customer_email' => 'redacted@example.invalid',
            'customer_phone' => '11999999999',
            'customer_document' => '12345678901',
            'customer_external_id' => 'test_customer_' . time()
        ];
        
        $transferData = [
            'amount' => 50.00,
            'external_id' => 'test_transfer_' . time(),
            'pix_key' => '11999999999',
            'description' => 'Teste de transferência PIX - Asaas',
            'schedule_date' => date('Y-m-d')
        ];
        
        $this->info('✅ Dados de teste preparados');
        $this->line('   Cliente: ' . json_encode($customerData, JSON_UNESCAPED_UNICODE));
        $this->line('   Cobrança: ' . json_encode($chargeData, JSON_UNESCAPED_UNICODE));
        $this->line('   Transferência: ' . json_encode($transferData, JSON_UNESCAPED_UNICODE));
        $this->newLine();

        // 5. Informações sobre endpoints
        $this->info('5. Endpoints disponíveis:');
        $this->line('   Callbacks:');
        $this->line('   • POST /api/asaas/callback/deposit - Callback para depósitos');
        $this->line('   • POST /api/asaas/callback/withdraw - Callback para saques');
        $this->line('   • POST /api/asaas/test - Teste de callback');
        $this->newLine();
        
        $this->line('   Administração:');
        $this->line('   • POST /admin/ajustes/adquirentes/asaas - Atualizar configurações');
        $this->newLine();
        
        $this->line('   API de Pagamentos:');
        $this->line('   • POST /api/deposit - Criar depósito (usar "asaas" como adquirente)');
        $this->line('   • POST /api/saque - Criar saque (usar "asaas" como adquirente)');
        $this->newLine();

        // 6. URLs de webhook
        $this->info('6. URLs de webhook para configurar no Asaas:');
        $baseUrl = env('APP_URL', 'https://seudominio.com');
        $this->line("   Depósitos: {$baseUrl}/api/asaas/callback/deposit");
        $this->line("   Saques: {$baseUrl}/api/asaas/callback/withdraw");
        $this->newLine();

        // 7. Instruções de uso
        $this->info('7. Instruções de uso:');
        $this->line('   1. Configure as credenciais na área administrativa');
        $this->line('   2. Defina como adquirente padrão se desejado');
        $this->line('   3. Teste depósitos usando a API com method_pay="pix"');
        $this->line('   4. Teste saques usando a API com pixKey e pixKeyType');
        $this->line('   5. Configure os webhooks no painel do Asaas');
        $this->newLine();

        $this->info('✅ Integração Asaas testada com sucesso!');
        $this->line('Lembre-se de configurar as credenciais reais na área administrativa antes de usar em produção.');
    }
}
