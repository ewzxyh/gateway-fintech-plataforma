<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stripe extends Model
{
    use HasFactory;

    protected $table = 'stripe_configs';

    protected $fillable = [
        'publishable_key',
        'secret_key',
        'webhook_secret',
        'environment',
        'webhook_url',
        'currency',
        'country'
    ];

    protected $casts = [
        // Campos específicos podem ser adicionados aqui se necessário
    ];

    /**
     * Retorna a URL base da API do Stripe baseada no ambiente
     */
    public function getApiUrlAttribute()
    {
        return $this->environment === 'production'
            ? 'https://api.stripe.com'
            : 'https://api.stripe.com'; // Stripe usa a mesma URL para ambos os ambientes
    }

    /**
     * Retorna a URL do webhook baseada no ambiente
     */
    public function getWebhookUrlAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return $this->environment === 'production'
            ? 'https://demo.gateway.shop/api/stripe/webhook'
            : 'https://demo.gateway.shop/api/stripe/webhook';
    }

    /**
     * Retorna a moeda padrão
     */
    public function getCurrencyAttribute($value)
    {
        return $value ?: 'usd';
    }

    /**
     * Retorna o país padrão
     */
    public function getCountryAttribute($value)
    {
        return $value ?: 'US';
    }

    /**
     * Verifica se o Stripe está configurado corretamente
     */
    public function isConfigured()
    {
        return !empty($this->publishable_key) && !empty($this->secret_key);
    }

    /**
     * Verifica se o Stripe está habilitado na tabela de adquirentes
     */
    public function isEnabled()
    {
        $adquirente = \App\Models\Adquirente::where('referencia', 'stripe')->first();
        return $adquirente && $adquirente->status == 1;
    }

    /**
     * Retorna as configurações para o frontend
     */
    public function getFrontendConfig()
    {
        return [
            'publishable_key' => $this->publishable_key,
            'environment' => $this->environment,
            'currency' => $this->currency,
            'country' => $this->country,
            'enabled' => $this->isEnabled()
        ];
    }
}
