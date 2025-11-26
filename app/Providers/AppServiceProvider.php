<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Solicitacoes;
use App\Models\SolicitacoesCashOut;
use App\Models\Woovi;
use App\Models\Witetec;
use App\Models\BSPay;
use App\Models\Pixup;
use App\Models\TrustyPix;
use App\Models\XDPag;
use App\Observers\SolicitacoesObserver;
use App\Observers\SolicitacoesCashOutObserver;
use App\Observers\AcquirerTaxObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void 
    {
        // Registrar Observers para monitorar mudanças de status
        Solicitacoes::observe(SolicitacoesObserver::class);
        SolicitacoesCashOut::observe(SolicitacoesCashOutObserver::class);
        
        // Registrar Observer para monitorar mudanças nas taxas das adquirentes
        Woovi::observe(AcquirerTaxObserver::class);
        Witetec::observe(AcquirerTaxObserver::class);
        BSPay::observe(AcquirerTaxObserver::class);
        Pixup::observe(AcquirerTaxObserver::class);
        TrustyPix::observe(AcquirerTaxObserver::class);
        XDPag::observe(AcquirerTaxObserver::class);
    }
}
