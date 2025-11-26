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
        Schema::create('rede', function (Blueprint $table) {
            $table->id();
            $table->string('pv')->nullable(); // PV (Ponto de Venda)
            $table->string('token')->nullable(); // Token de autenticação
            $table->string('environment')->default('sandbox'); // sandbox ou production
            $table->boolean('status')->default(false); // Status ativo/inativo
            $table->string('api_url')->nullable(); // URL da API (será definida automaticamente baseada no ambiente)
            $table->string('tokenization_url')->nullable(); // URL da API de tokenização
            $table->string('client_id')->nullable(); // Client ID para OAuth 2.0
            $table->string('client_secret')->nullable(); // Client Secret para OAuth 2.0
            $table->text('access_token')->nullable(); // Access token OAuth 2.0
            $table->timestamp('token_expires_at')->nullable(); // Data de expiração do token
            $table->text('webhook_url')->nullable(); // URL para webhooks
            $table->text('callback_url')->nullable(); // URL para callbacks de tokenização
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rede');
    }
};