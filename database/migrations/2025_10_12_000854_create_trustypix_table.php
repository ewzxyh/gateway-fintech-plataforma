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
        Schema::create('trustypix', function (Blueprint $table) {
            $table->id();
            $table->string('webhook_secret')->nullable();
            $table->decimal('taxa_pix_cash_in', 5, 2)->default(0.00);
            $table->decimal('taxa_pix_cash_out', 5, 2)->default(0.00);
            $table->boolean('status')->default(false);
            $table->string('url')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->decimal('taxa_adquirente_entradas', 5, 2)->default(0.00);
            $table->decimal('taxa_adquirente_saidas', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trustypix');
    }
};