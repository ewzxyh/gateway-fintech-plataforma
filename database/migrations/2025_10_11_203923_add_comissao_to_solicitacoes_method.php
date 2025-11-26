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
        // Alterar a coluna method para aceitar o novo valor 'comissao'
        DB::statement("ALTER TABLE solicitacoes MODIFY COLUMN method ENUM('pix', 'billet', 'card', 'comissao') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para os valores originais
        DB::statement("ALTER TABLE solicitacoes MODIFY COLUMN method ENUM('pix', 'billet', 'card') NOT NULL");
    }
};