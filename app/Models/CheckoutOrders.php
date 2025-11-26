<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckoutOrders extends Model
{
    protected $table = "checkout_orders";

    protected $fillable = [
        "bairro",
        "cep",
        "cidade",
        'estado',
        "complemento",
        "cpf",
        "email",
        "endereco",
        "name",
        "numero",
        "telefone",
        "status",
        "valor_total",
        "checkout_id",
        "quantidade",
        "order_bumps",
        "idTransaction",
        "qrcode",
    ];

    /**
     * Substitui ORDER_ID pela URL real do pedido
     */
    public function getThankYouUrl()
    {
        $checkout = CheckoutBuild::find($this->checkout_id);
        if (!$checkout || !$checkout->url_pagina_vendas) {
            return url('/obrigado?order_id=' . $this->id);
        }
        
        // Substituir ORDER_ID pelo ID real do pedido
        $url = str_replace('ORDER_ID', $this->id, $checkout->url_pagina_vendas);
        
        // Se ainda não tem order_id na URL, adicionar
        if (!str_contains($url, 'order_id=') && !str_contains($url, 'transaction_id=')) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator . 'order_id=' . $this->id;
        }
        
        return $url;
    }

    public function checkout()
    {
        return $this->belongsTo(CheckoutBuild::class);
    }
}
