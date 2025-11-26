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
        Schema::table('rede', function (Blueprint $table) {
            $table->string('client_id')->nullable()->after('token');
            $table->string('client_secret')->nullable()->after('client_id');
            $table->text('access_token')->nullable()->after('client_secret');
            $table->timestamp('token_expires_at')->nullable()->after('access_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rede', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'client_secret', 'access_token', 'token_expires_at']);
        });
    }
};