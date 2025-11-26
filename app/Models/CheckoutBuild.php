<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CheckoutBuild extends Model
{
    use HasFactory;

    protected $table = 'checkout_build';

    protected $fillable = [
        'id_unico',
        'user_id',
        'produto_name',
        'produto_descricao',
        'descricao_extra',
        'produto_valor',
        'produto_de_valor',
        'produto_categoria',
        'produto_tipo',
        'produto_tipo_cob',
        'produto_image',
        'whatsapp_suporte',
        'email_suporte',
        'descricao_exta',
        'checkout_color',
        'checkout_color_default',
        'checkout_color_card',
        'checkout_timer_active',
        'checkout_timer_tempo',
        'checkout_timer_cor_fundo',
        'checkout_timer_cor_texto',
        'checkout_timer_texto',
        'checkout_header_logo_active',
        'checkout_header_logo',
        'checkout_header_image_active',
        'checkout_header_image',
        'checkout_banner_active',
        'checkout_banner',
        'checkout_topbar_active',
        'checkout_topbar_text',
        'checkout_topbar_text_color',
        'checkout_topbar_color',
        'checkout_depoimentos_image',
        'checkout_depoimentos_nome',
        'checkout_depoimentos_depoimento',
        'url_pagina_vendas',
        'periodo_garantia',
        'checkout_ads_meta',
        'checkout_ads_google',
        'checkout_ads_tiktok',
        'checkout_ads_utmfy',
        'status',
        'methods',
        // Novos campos de customização avançada
        'theme_name',
        'theme_colors',
        'theme_fonts',
        'theme_spacing',
        'layout_config',
        'component_order',
        'drag_drop_enabled',
        'primary_color',
        'secondary_color',
        'accent_color',
        'text_color',
        'background_color',
        'font_family',
        'font_size_base',
        'font_weight_normal',
        'font_weight_bold',
        'border_radius',
        'padding_small',
        'padding_medium',
        'padding_large',
        'box_shadow',
        'border_color',
        'border_width',
        'animations_enabled',
        'animation_duration',
        'animation_easing',
        'breakpoints',
        'mobile_config',
        'button_styles',
        'form_styles',
        'card_styles',
        'header_styles',
        'footer_styles',
        'layout_type',
        'sidebar_enabled',
        'sidebar_position',
        'sidebar_width',
        'product_display_config',
        'pricing_display_config',
        'cta_config',
        'trust_badges_enabled',
        'trust_badges_config',
        'security_icons_enabled',
        'conversion_elements',
        'urgency_elements',
        'social_proof_config',
    ];

    protected $casts = [
        'checkout_timer_active' => 'boolean',
        'checkout_header_logo_active' => 'boolean',
        'checkout_header_image_active' => 'boolean',
        'checkout_banner_active' => 'boolean',
        'checkout_topbar_active' => 'boolean',
        'status' => 'boolean',
        'checkout_timer_tempo' => 'integer',
        'methods' => 'array',
        // Novos casts para campos JSON
        'theme_colors' => 'array',
        'theme_fonts' => 'array',
        'theme_spacing' => 'array',
        'layout_config' => 'array',
        'component_order' => 'array',
        'drag_drop_enabled' => 'boolean',
        'breakpoints' => 'array',
        'mobile_config' => 'array',
        'button_styles' => 'array',
        'form_styles' => 'array',
        'card_styles' => 'array',
        'header_styles' => 'array',
        'footer_styles' => 'array',
        'sidebar_enabled' => 'boolean',
        'product_display_config' => 'array',
        'pricing_display_config' => 'array',
        'cta_config' => 'array',
        'trust_badges_enabled' => 'boolean',
        'trust_badges_config' => 'array',
        'security_icons_enabled' => 'boolean',
        'conversion_elements' => 'array',
        'urgency_elements' => 'array',
        'social_proof_config' => 'array',
        'animations_enabled' => 'boolean',
    ];

    public $timestamps = true;

    /**
     * Relação com o usuário (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bumps()
    {
        return $this->hasMany(CheckoutOrderBump::class, 'checkout_id', 'id')->where('ativo', true);
    }

    public function allBumps()
    {
        return $this->hasMany(CheckoutOrderBump::class, 'checkout_id', 'id');
    }

    public function depoimentos()
    {
        return $this->hasMany(CheckoutDepoimento::class, 'checkout_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(CheckoutOrders::class, 'checkout_id', 'id');
    }
}
