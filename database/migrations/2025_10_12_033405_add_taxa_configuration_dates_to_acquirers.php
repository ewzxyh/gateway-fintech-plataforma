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
        // Adicionar campos de data de configuração das taxas para todas as adquirentes
        $acquirers = ['woovi', 'witetec', 'bspay', 'pixup', 'trustypix', 'xdpag'];
        
        foreach ($acquirers as $acquirer) {
            $tableName = $acquirer . 's'; // Pluralizar nome da tabela
            
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->timestamp('taxa_adquirente_entradas_configurada_em')->nullable()->after('taxa_adquirente_entradas');
                    $table->timestamp('taxa_adquirente_saidas_configurada_em')->nullable()->after('taxa_adquirente_saidas');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover campos de data de configuração das taxas
        $acquirers = ['woovi', 'witetec', 'bspay', 'pixup', 'trustypix', 'xdpag'];
        
        foreach ($acquirers as $acquirer) {
            $tableName = $acquirer . 's'; // Pluralizar nome da tabela
            
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn(['taxa_adquirente_entradas_configurada_em', 'taxa_adquirente_saidas_configurada_em']);
                });
            }
        }
    }
};