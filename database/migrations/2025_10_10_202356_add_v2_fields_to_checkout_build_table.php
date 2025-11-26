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
        Schema::table('checkout_build', function (Blueprint $table) {
            // Campos para Checkout V2 - Drag & Drop Vega Style
            $table->json('component_order_v2')->nullable()->after('component_order');
            $table->json('theme_config_v2')->nullable()->after('component_order_v2');
            $table->boolean('v2_enabled')->default(false)->after('theme_config_v2');
            
            // Cores do V2
            $table->string('v2_primary_color')->default('#667eea')->after('v2_enabled');
            $table->string('v2_secondary_color')->default('#764ba2')->after('v2_primary_color');
            
            // Tipografia do V2
            $table->string('v2_font_family')->default('Inter')->after('v2_secondary_color');
            $table->string('v2_font_size')->default('16px')->after('v2_font_family');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkout_build', function (Blueprint $table) {
            $table->dropColumn([
                'component_order_v2',
                'theme_config_v2',
                'v2_enabled',
                'v2_primary_color',
                'v2_secondary_color',
                'v2_font_family',
                'v2_font_size'
            ]);
        });
    }
};
