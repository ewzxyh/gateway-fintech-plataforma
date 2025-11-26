<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeConfig extends Model
{
    protected $table = 'stripe_configs';
    
    protected $fillable = [
        'publishable_key',
        'secret_key',
        'webhook_secret',
        'environment',
        'webhook_url',
        'currency',
        'country',
        'enabled',
        'taxa_adquirente_entradas',
        'taxa_adquirente_saidas'
    ];

    protected $casts = [
        'enabled' => 'boolean'
    ];

    /**
     * Retorna se está em ambiente de produção
     */
    public function isProduction()
    {
        return $this->environment === 'production';
    }

    /**
     * Retorna se está em ambiente de sandbox
     */
    public function isSandbox()
    {
        return $this->environment === 'sandbox';
    }

    /**
     * Retorna se a configuração está ativa
     */
    public function isActive()
    {
        return $this->enabled && !empty($this->secret_key);
    }
}
