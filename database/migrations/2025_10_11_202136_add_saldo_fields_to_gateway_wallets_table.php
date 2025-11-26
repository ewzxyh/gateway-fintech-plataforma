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
        Schema::table('gateway_wallets', function (Blueprint $table) {
            $table->decimal('saldo_disponivel', 15, 2)->default(0)->comment('Saldo disponível para saque');
            $table->decimal('saldo_pendente', 15, 2)->default(0)->comment('Saldo pendente de confirmação');
            $table->decimal('total_sacado', 15, 2)->default(0)->comment('Total já sacado da carteira');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gateway_wallets', function (Blueprint $table) {
            $table->dropColumn(['saldo_disponivel', 'saldo_pendente', 'total_sacado']);
        });
    }
};
