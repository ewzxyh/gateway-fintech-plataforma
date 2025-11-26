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
        Schema::create('stripe_configs', function (Blueprint $table) {
            $table->id();
            $table->string('publishable_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->string('webhook_url')->nullable();
            $table->string('currency', 3)->default('usd');
            $table->string('country', 2)->default('US');
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_configs');
    }
};