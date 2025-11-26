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
            // Sistema de temas avançado
            $table->string('theme_name')->default('default')->after('checkout_color_card');
            $table->json('theme_colors')->nullable()->after('theme_name');
            $table->json('theme_fonts')->nullable()->after('theme_colors');
            $table->json('theme_spacing')->nullable()->after('theme_fonts');
            
            // Layout drag & drop
            $table->json('layout_config')->nullable()->after('theme_spacing');
            $table->json('component_order')->nullable()->after('layout_config');
            $table->boolean('drag_drop_enabled')->default(false)->after('component_order');
            
            // Customizações avançadas
            $table->string('primary_color')->default('#00C26E')->after('drag_drop_enabled');
            $table->string('secondary_color')->default('#FFFFFF')->after('primary_color');
            $table->string('accent_color')->default('#FF6B35')->after('secondary_color');
            $table->string('text_color')->default('#333333')->after('accent_color');
            $table->string('background_color')->default('#F5F5F5')->after('text_color');
            
            // Tipografia
            $table->string('font_family')->default('Inter')->after('background_color');
            $table->string('font_size_base')->default('16px')->after('font_family');
            $table->string('font_weight_normal')->default('400')->after('font_size_base');
            $table->string('font_weight_bold')->default('600')->after('font_weight_normal');
            
            // Espaçamento
            $table->string('border_radius')->default('8px')->after('font_weight_bold');
            $table->string('padding_small')->default('8px')->after('border_radius');
            $table->string('padding_medium')->default('16px')->after('padding_small');
            $table->string('padding_large')->default('24px')->after('padding_medium');
            
            // Sombras e efeitos
            $table->string('box_shadow')->default('0 2px 8px rgba(0,0,0,0.1)')->after('padding_large');
            $table->string('border_color')->default('#E5E5E5')->after('box_shadow');
            $table->string('border_width')->default('1px')->after('border_color');
            
            // Animações
            $table->boolean('animations_enabled')->default(true)->after('border_width');
            $table->string('animation_duration')->default('0.3s')->after('animations_enabled');
            $table->string('animation_easing')->default('ease-in-out')->after('animation_duration');
            
            // Responsividade
            $table->json('breakpoints')->nullable()->after('animation_easing');
            $table->json('mobile_config')->nullable()->after('breakpoints');
            
            // Componentes personalizáveis
            $table->json('button_styles')->nullable()->after('mobile_config');
            $table->json('form_styles')->nullable()->after('button_styles');
            $table->json('card_styles')->nullable()->after('form_styles');
            $table->json('header_styles')->nullable()->after('card_styles');
            $table->json('footer_styles')->nullable()->after('header_styles');
            
            // Configurações de layout
            $table->string('layout_type')->default('single-column')->after('footer_styles');
            $table->boolean('sidebar_enabled')->default(false)->after('layout_type');
            $table->string('sidebar_position')->default('right')->after('sidebar_enabled');
            $table->integer('sidebar_width')->default(300)->after('sidebar_position');
            
            // Configurações de produto
            $table->json('product_display_config')->nullable()->after('sidebar_width');
            $table->json('pricing_display_config')->nullable()->after('product_display_config');
            $table->json('cta_config')->nullable()->after('pricing_display_config');
            
            // Configurações de segurança
            $table->boolean('trust_badges_enabled')->default(true)->after('cta_config');
            $table->json('trust_badges_config')->nullable()->after('trust_badges_enabled');
            $table->boolean('security_icons_enabled')->default(true)->after('trust_badges_config');
            
            // Configurações de conversão
            $table->json('conversion_elements')->nullable()->after('security_icons_enabled');
            $table->json('urgency_elements')->nullable()->after('conversion_elements');
            $table->json('social_proof_config')->nullable()->after('urgency_elements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkout_build', function (Blueprint $table) {
            $table->dropColumn([
                'theme_name', 'theme_colors', 'theme_fonts', 'theme_spacing',
                'layout_config', 'component_order', 'drag_drop_enabled',
                'primary_color', 'secondary_color', 'accent_color', 'text_color', 'background_color',
                'font_family', 'font_size_base', 'font_weight_normal', 'font_weight_bold',
                'border_radius', 'padding_small', 'padding_medium', 'padding_large',
                'box_shadow', 'border_color', 'border_width',
                'animations_enabled', 'animation_duration', 'animation_easing',
                'breakpoints', 'mobile_config',
                'button_styles', 'form_styles', 'card_styles', 'header_styles', 'footer_styles',
                'layout_type', 'sidebar_enabled', 'sidebar_position', 'sidebar_width',
                'product_display_config', 'pricing_display_config', 'cta_config',
                'trust_badges_enabled', 'trust_badges_config', 'security_icons_enabled',
                'conversion_elements', 'urgency_elements', 'social_proof_config'
            ]);
        });
    }
};