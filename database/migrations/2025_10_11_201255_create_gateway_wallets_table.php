<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gateway_wallets', function (Blueprint $table) {
            $table->id();
            $table->decimal('taxa_sistema_total', 15, 2)->default(0)->comment('Total das taxas do sistema coletadas');
            $table->decimal('taxa_adquirente_total', 15, 2)->default(0)->comment('Total das taxas pagas aos adquirentes');
            $table->decimal('saldo_gateway', 15, 2)->default(0)->comment('Saldo líquido da carteira gateway (sistema - adquirente)');
            $table->date('periodo_inicio')->comment('Data de início do período');
            $table->date('periodo_fim')->comment('Data de fim do período');
            $table->integer('transacoes_count')->default(0)->comment('Quantidade de transações no período');
            $table->timestamps();
            
            // Índices para melhor performance
            $table->index(['periodo_inicio', 'periodo_fim']);
            $table->unique(['periodo_inicio', 'periodo_fim'], 'unique_periodo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateway_wallets');
    }
};
