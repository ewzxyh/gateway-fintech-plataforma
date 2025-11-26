<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagarme extends Model
{
    protected $table = "pagarme";
    
    protected $fillable = [
        "token",
        "secret",
        "url",
        "url_cash_in",
        "url_cash_out",
        "taxa_adquirente_entradas",
        "taxa_adquirente_saidas",
    ];
}
