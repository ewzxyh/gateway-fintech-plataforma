<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rede extends Model
{
    use HasFactory;

    protected $table = 'rede';
    
    protected $fillable = [
        'pv',
        'token',
        'environment',
        'status',
        'api_url',
        'tokenization_url',
        'webhook_url',
        'callback_url',
        'client_id',
        'client_secret',
        'access_token',
        'token_expires_at',
        'taxa_adquirente_entradas',
        'taxa_adquirente_saidas'
    ];

    protected $casts = [
        'status' => 'boolean',
        'token_expires_at' => 'datetime',
    ];

    /**
     * Retorna a URL da API baseada no ambiente
     */
    public function getApiUrl()
    {
        if ($this->api_url) {
            return $this->api_url;
        }
        
        return $this->environment === 'production' 
            ? 'https://api.userede.com.br'
            : 'https://sandbox-erede.useredecloud.com.br';
    }

    public function getTokenUrl()
    {
        return $this->environment === 'production' 
            ? 'https://api.userede.com.br/redelabs/oauth2/token'
            : 'https://rl7-sandbox-api.useredecloud.com.br/oauth2/token';
    }

    public function getTokenizationUrl()
    {
        return $this->environment === 'production' 
            ? 'https://rl7-api.useredecloud.com.br'
            : 'https://rl7-sandbox-api.useredecloud.com.br';
    }

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
        return $this->status && !empty($this->pv) && !empty($this->token);
    }
}