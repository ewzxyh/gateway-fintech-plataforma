<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Inserir a Rede e.Rede na tabela de adquirentes
        DB::table('adquirentes')->insert([
            'adquirente' => 'Rede e.Rede',
            'status' => 0, // Inativo por padrão
            'url' => 'https://api.userede.com.br/erede',
            'referencia' => 'rede',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover a Rede e.Rede da tabela de adquirentes
        DB::table('adquirentes')->where('referencia', 'rede')->delete();
    }
};