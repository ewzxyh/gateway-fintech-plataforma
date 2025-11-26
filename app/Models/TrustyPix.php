<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustyPix extends Model
{
    protected $table = 'trustypix';
    
    protected $fillable = [
        'webhook_secret',
        'taxa_pix_cash_in',
        'taxa_pix_cash_out',
        'status',
        'url',
        'client_id',
        'client_secret',
        'taxa_adquirente_entradas',
        'taxa_adquirente_saidas'
    ];

    protected $casts = [
        'status' => 'boolean',
        'taxa_pix_cash_in' => 'decimal:2',
        'taxa_pix_cash_out' => 'decimal:2',
        'taxa_adquirente_entradas' => 'decimal:2',
        'taxa_adquirente_saidas' => 'decimal:2'
    ];

    /**
     * Retorna a configuração ativa da TrustyPix
     */
    public static function getActiveConfig()
    {
        return self::where('status', true)->first();
    }

    /**
     * Retorna se a configuração está ativa
     */
    public function isActive()
    {
        return $this->status && !empty($this->client_id) && !empty($this->client_secret);
    }

    /**
     * Retorna a URL da API
     */
    public function getApiUrl()
    {
        return $this->url ?: 'https://api.trustypix.com';
    }
}
