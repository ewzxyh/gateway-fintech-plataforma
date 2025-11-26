<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AcquirerTaxObserver
{
    /**
     * Handle the model "updating" event.
     */
    public function updating(Model $model): void
    {
        // Verificar se é uma adquirente e se as taxas estão sendo alteradas
        $acquirerModels = [
            'App\Models\Woovi',
            'App\Models\Witetec', 
            'App\Models\BSPay',
            'App\Models\Pixup',
            'App\Models\TrustyPix',
            'App\Models\XDPag'
        ];

        if (!in_array(get_class($model), $acquirerModels)) {
            return;
        }

        $now = Carbon::now();

        // Verificar se taxa_adquirente_entradas foi alterada
        if ($model->isDirty('taxa_adquirente_entradas')) {
            $model->taxa_adquirente_entradas_configurada_em = $now;
        }

        // Verificar se taxa_adquirente_saidas foi alterada
        if ($model->isDirty('taxa_adquirente_saidas')) {
            $model->taxa_adquirente_saidas_configurada_em = $now;
        }
    }

    /**
     * Handle the model "created" event.
     */
    public function created(Model $model): void
    {
        // Verificar se é uma adquirente
        $acquirerModels = [
            'App\Models\Woovi',
            'App\Models\Witetec', 
            'App\Models\BSPay',
            'App\Models\Pixup',
            'App\Models\TrustyPix',
            'App\Models\XDPag'
        ];

        if (!in_array(get_class($model), $acquirerModels)) {
            return;
        }

        $now = Carbon::now();

        // Se as taxas já estão definidas na criação, marcar a data atual
        if ($model->taxa_adquirente_entradas > 0 && !$model->taxa_adquirente_entradas_configurada_em) {
            $model->taxa_adquirente_entradas_configurada_em = $now;
        }

        if ($model->taxa_adquirente_saidas > 0 && !$model->taxa_adquirente_saidas_configurada_em) {
            $model->taxa_adquirente_saidas_configurada_em = $now;
        }
    }
}