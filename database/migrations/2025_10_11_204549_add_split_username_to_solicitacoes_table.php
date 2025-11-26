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
        Schema::table('solicitacoes', function (Blueprint $table) {
            // Adicionar campo split_username para permitir split por username
            $table->string('split_username')->nullable()->after('split_email');
            
            // Adicionar índice para performance
            $table->index(['split_username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitacoes', function (Blueprint $table) {
            $table->dropIndex(['split_username']);
            $table->dropColumn('split_username');
        });
    }
};