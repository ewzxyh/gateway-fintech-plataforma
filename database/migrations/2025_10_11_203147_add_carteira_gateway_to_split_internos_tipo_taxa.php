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
        // Alterar a coluna tipo_taxa para aceitar o novo valor
        DB::statement("ALTER TABLE split_internos MODIFY COLUMN tipo_taxa ENUM('deposito', 'saque_pix', 'carteira_gateway') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para os valores originais
        DB::statement("ALTER TABLE split_internos MODIFY COLUMN tipo_taxa ENUM('deposito', 'saque_pix') NOT NULL");
    }
};