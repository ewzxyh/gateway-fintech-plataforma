<x-app-layout :route="'[ADMIN] Ajustes de adquirentes'">

  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-building-columns fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Gerenciamento de Adquirentes</h1>
                  <p class="text-muted mb-0">Configure e gerencie todas as adquirentes de pagamento do sistema</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Adquirentes Padrão Globais -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-globe me-2"></i>
                Adquirentes Padrão Globais
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="alert alert-info">
                <i class="fa-solid fa-info-circle me-2"></i>
                <strong>Informação:</strong> As adquirentes padrão globais serão usadas para todos os usuários que não possuem uma adquirente específica configurada.
              </div>

              <form method="POST" action="{{ route('admin.adquirentes.default') }}">
                @csrf
                <div class="row g-4">
                  <!-- Adquirente PIX -->
                  <div class="col-md-6">
                    <label for="adquirente_pix" class="form-label fw-semibold">
                      <i class="fa-brands fa-pix text-success me-2"></i>
                      Adquirente PIX Global
                    </label>
                    <select class="form-select form-select-lg @error('adquirente_pix') is-invalid @enderror"
                      name="adquirente_pix" id="adquirente_pix" required>
                      @foreach($adquirentes as $adquirente)
                        @if(!in_array(strtolower($adquirente->referencia), ['sixxpayments', 'mercadopago', 'efi', 'syscoop', 'asaas']) && $adquirente->status)
                        <option value="{{ $adquirente->referencia }}"
                          {{ $default == $adquirente->referencia ? 'selected' : '' }}>
                          @php
                          $nomeFormatado = match(strtolower($adquirente->adquirente)) {
                            'cashtime' => 'Cash Time',
                            'xgate' => 'XGate',
                            'witetec' => 'WiteTec',
                            'pixup' => 'PixUP BR',
                            'bspay' => 'BSPay',
                            'woovi' => 'Woovi',
                            'primepay7' => 'PrimePay7',
                            'xdpag' => 'XDPag',
                            'rede' => 'Rede e.Rede',
                            'trustypix' => 'TrustyPix',
                            default => $adquirente->adquirente
                          };
                          @endphp
                          {{ $nomeFormatado }}
                        </option>
                        @endif
                      @endforeach
                    </select>
                    <small class="text-muted">
                      <i class="fa-solid fa-lightbulb me-1"></i>
                      Adquirente usada para pagamentos via PIX
                    </small>
                    @error('adquirente_pix')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>

                  <!-- Adquirente Cartão+Boleto -->
                  <div class="col-md-6">
                    <label for="adquirente_card_billet" class="form-label fw-semibold">
                      <i class="fa-solid fa-credit-card text-primary me-2"></i>
                      Adquirente Cartão+Boleto Global
                    </label>
                    <select class="form-select form-select-lg @error('adquirente_card_billet') is-invalid @enderror"
                      name="adquirente_card_billet" id="adquirente_card_billet" required>
                      @foreach($adquirentes as $adquirente)
                        @if(!in_array(strtolower($adquirente->referencia), ['sixxpayments', 'mercadopago', 'efi', 'syscoop', 'asaas']) && $adquirente->status)
                        <option value="{{ $adquirente->referencia }}"
                          {{ ($adquirente->is_default_card_billet ?? false) ? 'selected' : '' }}>
                          @php
                          $nomeFormatado = match(strtolower($adquirente->adquirente)) {
                            'cashtime' => 'Cash Time',
                            'xgate' => 'XGate',
                            'witetec' => 'WiteTec',
                            'pixup' => 'PixUP BR',
                            'bspay' => 'BSPay',
                            'woovi' => 'Woovi',
                            'primepay7' => 'PrimePay7',
                            'xdpag' => 'XDPag',
                            'rede' => 'Rede e.Rede',
                            'trustypix' => 'TrustyPix',
                            default => $adquirente->adquirente
                          };
                          @endphp
                          {{ $nomeFormatado }}
                        </option>
                        @endif
                      @endforeach
                    </select>
                    <small class="text-muted">
                      <i class="fa-solid fa-lightbulb me-1"></i>
                      Adquirente usada para pagamentos via Cartão de Crédito e Boleto
                    </small>
                    @error('adquirente_card_billet')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                      <i class="fa-solid fa-save me-2"></i>Salvar Configurações Globais
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Gerenciar Adquirentes (Ativar/Desativar) -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-success">
            <div class="card-header bg-success text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-toggle-on me-2"></i>
                Gerenciar Status das Adquirentes
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-4">
                <i class="fa-solid fa-info-circle me-2"></i>
                Ative ou desative adquirentes individuais. Usuários podem usar qualquer adquirente ativa.
              </p>
              
              <div class="row g-4">
                @foreach($adquirentes as $adquirente)
                  @if(!in_array(strtolower($adquirente->referencia), ['sixxpayments', 'mercadopago', 'efi', 'syscoop', 'asaas']))
                  <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card h-100 {{ $adquirente->status ? 'border-success' : 'border-secondary' }} border-2">
                      <div class="card-body p-4 text-center">
                        <div class="mb-3">
                          <h5 class="fw-bold mb-3">
                            @php
                            $nomeFormatado = match(strtolower($adquirente->adquirente)) {
                              'cashtime' => 'Cash Time',
                              'xgate' => 'XGate',
                              'witetec' => 'WiteTec',
                              'pixup' => 'PixUP BR',
                              'bspay' => 'BSPay',
                              'woovi' => 'Woovi',
                              'primepay7' => 'PrimePay7',
                            'xdpag' => 'XDPag',
                            'rede' => 'Rede e.Rede',
                            'stripe' => 'Stripe',
                            'pagarme' => 'PagarMe',
                            'trustypix' => 'TrustyPix',
                            default => $adquirente->adquirente
                            };
                            @endphp
                            {{ $nomeFormatado }}
                          </h5>
                          <span class="badge {{ $adquirente->status ? 'bg-success' : 'bg-secondary' }} badge-lg mb-2">
                            <i class="fa-solid {{ $adquirente->status ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                            {{ $adquirente->status ? 'ATIVA' : 'INATIVA' }}
                          </span>
                        </div>

                        <!-- Texto pequeno da referência ocultado -->
                        <!-- <p class="text-muted small mb-3">
                          <code>{{ $adquirente->referencia }}</code>
                        </p> -->

                        <div class="d-grid gap-2">
                          <form method="POST" action="{{ route('admin.adquirentes.toggle') }}" class="mb-0">
                            @csrf
                            <input type="hidden" name="adquirente" value="{{ $adquirente->referencia }}">
                            <button type="submit" class="btn {{ $adquirente->status ? 'btn-outline-danger' : 'btn-outline-success' }} w-100">
                              <i class="fa-solid {{ $adquirente->status ? 'fa-toggle-off' : 'fa-toggle-on' }} me-1"></i>
                              {{ $adquirente->status ? 'Desativar' : 'Ativar' }}
                            </button>
                          </form>
                          <button type="button" class="btn btn-outline-primary w-100" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modal{{ ucfirst($adquirente->referencia) }}">
                            <i class="fa-solid fa-cog me-1"></i>
                            Configurar
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  @endif
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>


    </div>
  </div>

  <!-- ========================================== -->
  <!-- MODAIS DE CONFIGURAÇÃO -->
  <!-- ========================================== -->

  <!-- Modal Cash Time -->
  <div class="modal fade" id="modalCashtime" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar Cash Time
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/api/cashtime/webhook</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.cashtime') }}" id="formCashtime">
            @csrf
            <div class="row g-3">
              <div class="col-12">
                <label for="cashtime_secret" class="form-label fw-semibold">
                  <i class="fa-solid fa-key text-warning me-2"></i>Chave Secreta
                </label>
                <input type="text" class="form-control" name="secret" id="cashtime_secret" 
                  value="{{ $cashtime->secret }}" required>
              </div>
              <div class="col-md-6">
                <label for="cashtime_taxa_pix_cash_in" class="form-label fw-semibold">
                  <i class="fa-solid fa-arrow-down text-success me-2"></i>Taxa PIX-IN (%)
                </label>
                <input type="number" step="0.01" class="form-control" name="taxa_pix_cash_in" 
                  id="cashtime_taxa_pix_cash_in" value="{{ $cashtime->taxa_pix_cash_in }}" required>
              </div>
              <div class="col-md-6">
                <label for="cashtime_taxa_pix_cash_out" class="form-label fw-semibold">
                  <i class="fa-solid fa-arrow-up text-danger me-2"></i>Taxa PIX-OUT (%)
                </label>
                <input type="number" step="0.01" class="form-control" name="taxa_pix_cash_out" 
                  id="cashtime_taxa_pix_cash_out" value="{{ $cashtime->taxa_pix_cash_out }}" required>
              </div>
              <div class="col-md-6">
                <label for="cashtime_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="cashtime_taxa_entradas"
                  value="{{ $cashtime->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="cashtime_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="cashtime_taxa_saidas"
                  value="{{ $cashtime->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formCashtime" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal PagarMe -->
  <div class="modal fade" id="modalPagarme" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar PagarMe
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/pagarme/webhook</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.pagarme') }}" id="formPagarme">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="pagarme_secret" class="form-label fw-semibold">
                  <i class="fa-solid fa-key text-warning me-2"></i>Chave Secreta
                </label>
                <input type="text" class="form-control" name="secret" id="pagarme_secret" 
                  value="{{ $pagarme->secret ?? null }}" required>
              </div>
              <div class="col-md-6">
                <label for="pagarme_token" class="form-label fw-semibold">
                  <i class="fa-solid fa-lock text-primary me-2"></i>Chave Pública
                </label>
                <input type="text" class="form-control" name="token" id="pagarme_token" 
                  value="{{ $pagarme->token ?? null }}" required>
              </div>
              <div class="col-md-6">
                <label for="pagarme_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="pagarme_taxa_entradas"
                  value="{{ $pagarme->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="pagarme_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="pagarme_taxa_saidas"
                  value="{{ $pagarme->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formPagarme" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal XGate -->
  <div class="modal fade" id="modalXgate" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar XGate
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/api/xgate/webhook</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.xgate') }}" id="formXgate">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="xgate_email" class="form-label fw-semibold">
                  <i class="fa-solid fa-envelope text-primary me-2"></i>Email
                </label>
                <input type="email" class="form-control" name="email" id="xgate_email" 
                  value="{{ $xgate->email ?? null }}" required>
              </div>
              <div class="col-md-6">
                <label for="xgate_password" class="form-label fw-semibold">
                  <i class="fa-solid fa-lock text-warning me-2"></i>Senha
                </label>
                <input type="text" class="form-control" name="password" id="xgate_password" 
                  value="{{ $xgate->password ?? null }}" required>
              </div>
              <div class="col-md-6">
                <label for="xgate_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="xgate_taxa_entradas"
                  value="{{ $xgate->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="xgate_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="xgate_taxa_saidas"
                  value="{{ $xgate->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formXgate" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Witetec -->
  <div class="modal fade" id="modalWitetec" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar WiteTec
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/api/witetec/webhook</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.witetec') }}" id="formWitetec">
            @csrf
            <div class="row g-3">
              <div class="col-12">
                <label for="witetec_api_token" class="form-label fw-semibold">
                  <i class="fa-solid fa-key text-warning me-2"></i>API Key
                </label>
                <input type="text" class="form-control" name="api_token" id="witetec_api_token" 
                  value="{{ $witetec->api_token ?? null }}" required>
              </div>
              <div class="col-md-6">
                <label for="witetec_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="witetec_taxa_entradas"
                  value="{{ $witetec->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="witetec_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="witetec_taxa_saidas"
                  value="{{ $witetec->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formWitetec" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal PixUP -->
  <div class="modal fade" id="modalPixup" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar PixUP BR
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/api/pixup/webhook</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.pixup') }}" id="formPixup">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="pixup_client_id" class="form-label fw-semibold">
                  <i class="fa-solid fa-id-card text-primary me-2"></i>Client ID
                </label>
                <input type="text" class="form-control" name="client_id" id="pixup_client_id" 
                  value="{{ $pixup->client_id ?? null }}" required>
              </div>
              <div class="col-md-6">
                <label for="pixup_url" class="form-label fw-semibold">
                  <i class="fa-solid fa-link text-info me-2"></i>URL
                </label>
                <input type="text" class="form-control" name="url" id="pixup_url" 
                  value="{{ $pixup->url ?? 'https://api.pixupbr.com/v2/' }}" required>
              </div>
              <div class="col-12">
                <label for="pixup_client_secret" class="form-label fw-semibold">
                  <i class="fa-solid fa-key text-warning me-2"></i>Client Secret
                </label>
                <input type="text" class="form-control" name="client_secret" id="pixup_client_secret" 
                  value="{{ $pixup->client_secret ?? null }}" required>
              </div>
              <div class="col-md-6">
                <label for="pixup_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="pixup_taxa_entradas"
                  value="{{ $pixup->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="pixup_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="pixup_taxa_saidas"
                  value="{{ $pixup->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formPixup" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal BSPay -->
  <div class="modal fade" id="modalBspay" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar BSPay
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/api/bspay/webhook</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.bspay') }}" id="formBspay">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="bspay_client_id" class="form-label fw-semibold">
                  <i class="fa-solid fa-id-card text-primary me-2"></i>Client ID
                </label>
                <input type="text" class="form-control" name="client_id" id="bspay_client_id" 
                  value="{{ $bspay->client_id ?? null }}" required>
              </div>
              <div class="col-md-6">
                <label for="bspay_url" class="form-label fw-semibold">
                  <i class="fa-solid fa-link text-info me-2"></i>URL
                </label>
                <input type="text" class="form-control" name="url" id="bspay_url" 
                  value="{{ $bspay->url ?? 'https://api.bspay.co/v2/' }}" required>
              </div>
              <div class="col-12">
                <label for="bspay_client_secret" class="form-label fw-semibold">
                  <i class="fa-solid fa-key text-warning me-2"></i>Client Secret
                </label>
                <input type="text" class="form-control" name="client_secret" id="bspay_client_secret" 
                  value="{{ $bspay->client_secret ?? null }}" required>
              </div>
              <div class="col-md-6">
                <label for="bspay_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="bspay_taxa_entradas"
                  value="{{ $bspay->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="bspay_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="bspay_taxa_saidas"
                  value="{{ $bspay->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formBspay" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Woovi -->
  <div class="modal fade" id="modalWoovi" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar Woovi
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/api/woovi/callback</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.woovi') }}" id="formWoovi">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="woovi_api_key" class="form-label fw-semibold">
                  <i class="fa-solid fa-key text-warning me-2"></i>API Key
                </label>
                <input type="text" class="form-control" name="api_key" id="woovi_api_key" 
                  value="{{ $woovi->api_key ?? null }}" required>
              </div>
              <div class="col-md-6">
                <label for="woovi_webhook_secret" class="form-label fw-semibold">
                  <i class="fa-solid fa-shield text-danger me-2"></i>Webhook Secret
                </label>
                <div class="input-group">
                  <input type="text" class="form-control" name="webhook_secret" id="webhook_secret_input" 
                    value="{{ $woovi->webhook_secret ?? null }}">
                  <button type="button" class="btn btn-outline-secondary" id="generate_webhook_token">
                    <i class="fa-solid fa-refresh"></i> Gerar
                  </button>
                </div>
              </div>
              <div class="col-md-6">
                <label for="woovi_api_url" class="form-label fw-semibold">
                  <i class="fa-solid fa-link text-info me-2"></i>URL da API
                </label>
                <input type="text" class="form-control" id="api_url_display" 
                  value="{{ $woovi->getApiUrl() ?? 'https://api.woovi.com' }}" readonly>
              </div>
              <div class="col-md-6">
                <label for="woovi_sandbox" class="form-label fw-semibold">
                  <i class="fa-solid fa-flask text-warning me-2"></i>Modo
                </label>
                <select class="form-select" name="sandbox" id="woovi_sandbox">
                  <option value="0" {{ ($woovi->sandbox ?? false) ? '' : 'selected' }}>Produção</option>
                  <option value="1" {{ ($woovi->sandbox ?? false) ? 'selected' : '' }}>Sandbox</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="woovi_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="woovi_taxa_entradas"
                  value="{{ $woovi->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="woovi_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="woovi_taxa_saidas"
                  value="{{ $woovi->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formWoovi" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal PrimePay7 -->
  <div class="modal fade" id="modalPrimepay7" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar PrimePay7
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/api/primepay7/webhook</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.primepay7') }}" id="formPrimepay7">
            @csrf
            <div class="row g-3">
              <div class="col-12">
                <label for="primepay7_url" class="form-label fw-semibold">
                  <i class="fa-solid fa-link text-info me-2"></i>URL
                </label>
                <input type="text" class="form-control" name="url" id="primepay7_url" 
                  value="{{ $primepay7->url ?? 'https://api.primepay7.com' }}" required>
              </div>
              <div class="col-md-6">
                <label for="primepay7_private_key" class="form-label fw-semibold">
                  <i class="fa-solid fa-key text-warning me-2"></i>Chave Privada
                </label>
                <input type="text" class="form-control" name="private_key" id="primepay7_private_key" 
                  value="{{ $primepay7->private_key ?? null }}" placeholder="sk_...">
              </div>
              <div class="col-md-6">
                <label for="primepay7_public_key" class="form-label fw-semibold">
                  <i class="fa-solid fa-lock text-primary me-2"></i>Chave Pública
                </label>
                <input type="text" class="form-control" name="public_key" id="primepay7_public_key" 
                  value="{{ $primepay7->public_key ?? null }}" placeholder="pk_...">
              </div>
              <div class="col-12">
                <label for="primepay7_withdrawal_key" class="form-label fw-semibold">
                  <i class="fa-solid fa-money-bill-transfer text-success me-2"></i>Chave de Saque Externo
                </label>
                <input type="text" class="form-control" name="withdrawal_key" id="primepay7_withdrawal_key" 
                  value="{{ $primepay7->withdrawal_key ?? null }}" placeholder="wk_...">
              </div>
              <div class="col-md-6">
                <label for="primepay7_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="primepay7_taxa_entradas"
                  value="{{ $primepay7->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="primepay7_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="primepay7_taxa_saidas"
                  value="{{ $primepay7->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formPrimepay7" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Rede e.Rede -->
  <div class="modal fade" id="modalRede" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar Rede e.Rede
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/api/rede/webhook</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.rede') }}" id="formRede">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="rede_pv" class="form-label fw-semibold">
                  <i class="fa-solid fa-shop text-primary me-2"></i>PV (Ponto de Venda)
                </label>
                <input type="text" class="form-control" name="pv" id="rede_pv" 
                  value="{{ $rede->pv ?? null }}" placeholder="Ex: 51576170" required>
              </div>
              <div class="col-md-6">
                <label for="rede_token" class="form-label fw-semibold">
                  <i class="fa-solid fa-ticket text-warning me-2"></i>Token (Legado)
                </label>
                <input type="text" class="form-control" name="token" id="rede_token" 
                  value="{{ $rede->token ?? null }}" placeholder="Ex: bfa6005bbc..." required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa-solid fa-flask text-info me-2"></i>Ambiente
                </label>
                <div class="btn-group w-100" role="group">
                  <input type="radio" class="btn-check" name="environment" id="rede_env_sandbox" 
                    value="sandbox" {{ ($rede->environment ?? 'sandbox') == 'sandbox' ? 'checked' : '' }} required>
                  <label class="btn btn-outline-secondary" for="rede_env_sandbox">
                    <i class="fa-solid fa-flask"></i> Sandbox
                  </label>
                  <input type="radio" class="btn-check" name="environment" id="rede_env_production" 
                    value="production" {{ ($rede->environment ?? 'sandbox') == 'production' ? 'checked' : '' }} required>
                  <label class="btn btn-outline-primary" for="rede_env_production">
                    <i class="fa-solid fa-rocket"></i> Produção
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <label for="rede_api_url" class="form-label fw-semibold">
                  <i class="fa-solid fa-link text-info me-2"></i>URL da API
                </label>
                <input type="text" class="form-control" id="rede_api_url" name="api_url" 
                  value="{{ $rede->api_url ?? 'https://sandbox-erede.useredecloud.com.br' }}" readonly>
              </div>
              <div class="col-md-6">
                <label for="rede_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="rede_taxa_entradas"
                  value="{{ $rede->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="rede_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="rede_taxa_saidas"
                  value="{{ $rede->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formRede" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Stripe -->
  <div class="modal fade" id="modalStripe" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar Stripe
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/api/stripe/webhook</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.stripe') }}" id="formStripe">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="stripe_publishable_key" class="form-label fw-semibold">
                  <i class="fa-solid fa-key text-primary me-2"></i>Chave Pública
                </label>
                <input type="text" class="form-control" name="publishable_key" id="stripe_publishable_key" 
                  value="{{ $stripe->publishable_key ?? null }}" placeholder="pk_test_..." required>
              </div>
              <div class="col-md-6">
                <label for="stripe_secret_key" class="form-label fw-semibold">
                  <i class="fa-solid fa-lock text-warning me-2"></i>Chave Secreta
                </label>
                <input type="password" class="form-control" name="secret_key" id="stripe_secret_key" 
                  value="{{ $stripe->secret_key ?? null }}" placeholder="sk_test_..." required>
              </div>
              <div class="col-md-6">
                <label for="stripe_webhook_secret" class="form-label fw-semibold">
                  <i class="fa-solid fa-shield text-danger me-2"></i>Webhook Secret
                </label>
                <input type="text" class="form-control" name="webhook_secret" id="stripe_webhook_secret" 
                  value="{{ $stripe->webhook_secret ?? null }}" placeholder="whsec_...">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa-solid fa-flask text-info me-2"></i>Ambiente
                </label>
                <div class="btn-group w-100" role="group">
                  <input type="radio" class="btn-check" name="environment" id="stripe_env_test" 
                    value="sandbox" {{ ($stripe->environment ?? 'sandbox') == 'sandbox' ? 'checked' : '' }} required>
                  <label class="btn btn-outline-secondary" for="stripe_env_test">
                    <i class="fa-solid fa-flask"></i> Teste
                  </label>
                  <input type="radio" class="btn-check" name="environment" id="stripe_env_live" 
                    value="production" {{ ($stripe->environment ?? 'sandbox') == 'production' ? 'checked' : '' }} required>
                  <label class="btn btn-outline-primary" for="stripe_env_live">
                    <i class="fa-solid fa-rocket"></i> Produção
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <label for="stripe_api_url" class="form-label fw-semibold">
                  <i class="fa-solid fa-link text-info me-2"></i>URL da API
                </label>
                <input type="text" class="form-control" id="stripe_api_url" name="api_url" 
                  value="{{ $stripe->api_url ?? 'https://api.stripe.com' }}" readonly>
              </div>
              <div class="col-md-6">
                <label for="stripe_currency" class="form-label fw-semibold">
                  <i class="fa-solid fa-dollar-sign text-success me-2"></i>Moeda Padrão
                </label>
                <select class="form-select" name="currency" id="stripe_currency">
                  <option value="brl" {{ ($stripe->currency ?? 'brl') == 'brl' ? 'selected' : '' }}>BRL</option>
                  <option value="usd" {{ ($stripe->currency ?? 'brl') == 'usd' ? 'selected' : '' }}>USD</option>
                  <option value="eur" {{ ($stripe->currency ?? 'brl') == 'eur' ? 'selected' : '' }}>EUR</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="stripe_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="stripe_taxa_entradas"
                  value="{{ $stripe->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="stripe_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="stripe_taxa_saidas"
                  value="{{ $stripe->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formStripe" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal XDPag -->
  <div class="modal fade" id="modalXdpag" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar XDPag
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/api/xdpag/webhook</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.xdpag') }}" id="formXdpag">
            @csrf
            <div class="row g-3">
              <div class="col-12">
                <label for="xdpag_url" class="form-label fw-semibold">
                  <i class="fa-solid fa-link text-info me-2"></i>URL
                </label>
                <input type="text" class="form-control" name="url" id="xdpag_url" 
                  value="{{ $xdpag->url ?? 'https://api.xdpag.com' }}" required>
              </div>
              <div class="col-md-6">
                <label for="xdpag_client_id" class="form-label fw-semibold">
                  <i class="fa-solid fa-id-card text-primary me-2"></i>Client ID
                </label>
                <input type="text" class="form-control" name="client_id" id="xdpag_client_id" 
                  value="{{ $xdpag->client_id ?? null }}" placeholder="Seu Client ID" required>
              </div>
              <div class="col-md-6">
                <label for="xdpag_client_secret" class="form-label fw-semibold">
                  <i class="fa-solid fa-key text-warning me-2"></i>Client Secret
                </label>
                <input type="password" class="form-control" name="client_secret" id="xdpag_client_secret" 
                  value="{{ $xdpag->client_secret ?? null }}" placeholder="Sua Client Secret" required>
              </div>
              <div class="col-md-6">
                <label for="xdpag_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="xdpag_taxa_entradas"
                  value="{{ $xdpag->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="xdpag_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="xdpag_taxa_saidas"
                  value="{{ $xdpag->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formXdpag" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal TrustyPix -->
  <div class="modal fade" id="modalTrustypix" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">
            <i class="fa-solid fa-cog me-2"></i>Configurar TrustyPix
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info mb-3">
            <i class="fa-solid fa-webhook me-2"></i>
            <strong>Webhook:</strong> <code>{{env('APP_URL')}}/api/trustypix/callback/deposit</code>
          </div>
          <form method="POST" action="{{ route('admin.adquirentes.trustypix') }}" id="formTrustypix">
            @csrf
            <div class="row g-3">
              <div class="col-12">
                <label for="trustypix_url" class="form-label fw-semibold">
                  <i class="fa-solid fa-link text-primary me-2"></i>URL
                </label>
                <input type="text" class="form-control" name="url" id="trustypix_url" 
                  value="{{ $trustypix->url ?? 'https://api.trustypix.com' }}" required>
              </div>
              <div class="col-md-6">
                <label for="trustypix_client_id" class="form-label fw-semibold">
                  <i class="fa-solid fa-id-card text-primary me-2"></i>Client ID
                </label>
                <input type="text" class="form-control" name="client_id" id="trustypix_client_id" 
                  value="{{ $trustypix->client_id ?? null }}" placeholder="Seu Client ID" required>
              </div>
              <div class="col-md-6">
                <label for="trustypix_client_secret" class="form-label fw-semibold">
                  <i class="fa-solid fa-key text-warning me-2"></i>Client Secret
                </label>
                <input type="password" class="form-control" name="client_secret" id="trustypix_client_secret" 
                  value="{{ $trustypix->client_secret ?? null }}" placeholder="Sua Client Secret" required>
              </div>
              <div class="col-md-6">
                <label for="trustypix_taxa_entradas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-primary me-2"></i>Taxa Adquirente - Entradas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_entradas" id="trustypix_taxa_entradas"
                  value="{{ $trustypix->taxa_adquirente_entradas ?? 0 }}" placeholder="Ex: 2.5" required>
              </div>
              <div class="col-md-6">
                <label for="trustypix_taxa_saidas" class="form-label fw-semibold">
                  <i class="fa-solid fa-percent text-info me-2"></i>Taxa Adquirente - Saídas (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                  name="taxa_adquirente_saidas" id="trustypix_taxa_saidas"
                  value="{{ $trustypix->taxa_adquirente_saidas ?? 0 }}" placeholder="Ex: 1.5" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="submit" form="formTrustypix" class="btn btn-sm btn-success">
            <i class="fa-solid fa-save me-1"></i>Salvar Configurações
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // ======================================================
      // ATUALIZAR URL DA API (WOOVI)
      // ======================================================
      function updateApiUrl() {
        const sandboxSelect = document.querySelector('select[name="sandbox"]');
        const urlDisplay = document.getElementById('api_url_display');

        if (sandboxSelect && urlDisplay) {
          const isSandbox = sandboxSelect.value === '1';
          const apiUrl = isSandbox ? 'https://api.woovi-sandbox.com' : 'https://api.woovi.com';
          urlDisplay.value = apiUrl;
        }
      }

      // ======================================================
      // GERAR TOKEN DO WEBHOOK (WOOVI)
      // ======================================================
      function generateWebhookToken() {
        const tokenLength = 32;
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let token = '';

        for (let i = 0; i < tokenLength; i++) {
          token += chars.charAt(Math.floor(Math.random() * chars.length));
        }

        const webhookInput = document.getElementById('webhook_secret_input');
        const generateBtn = document.getElementById('generate_webhook_token');

        if (webhookInput && generateBtn) {
          generateBtn.disabled = true;
          generateBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';

          fetch('{{ route("admin.adquirentes.woovi.webhook-token") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ webhook_secret: token })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              webhookInput.value = token;
              showToast('success', 'Token gerado e salvo com sucesso!');
            } else {
              showToast('error', 'Erro ao salvar token: ' + data.message);
            }
          })
          .catch(error => {
            console.error('Erro:', error);
            showToast('error', 'Erro ao salvar token');
          })
          .finally(() => {
            generateBtn.disabled = false;
            generateBtn.innerHTML = '<i class="fa-solid fa-refresh"></i> Gerar';
          });
        }
      }

      // ======================================================
      // ATUALIZAR URL DA REDE (SANDBOX/PRODUÇÃO)
      // ======================================================
      function updateRedeUrl() {
        const sandbox = document.getElementById('rede_env_sandbox');
        const production = document.getElementById('rede_env_production');
        const apiUrlInput = document.getElementById('rede_api_url');

        if (!apiUrlInput) return;

        const url = sandbox?.checked 
          ? 'https://sandbox-erede.useredecloud.com.br' 
          : 'https://api.userede.com.br/erede';

        apiUrlInput.value = url;
      }

      // ======================================================
      // ATUALIZAR URL DA STRIPE (TESTE/PRODUÇÃO)
      // ======================================================
      function updateStripeUrl() {
        const test = document.getElementById('stripe_env_test');
        const live = document.getElementById('stripe_env_live');
        const apiUrlInput = document.getElementById('stripe_api_url');

        if (!apiUrlInput) return;

        const url = test?.checked 
          ? 'https://api.stripe.com' 
          : 'https://api.stripe.com';

        apiUrlInput.value = url;
      }

      // ======================================================
      // EVENT LISTENERS
      // ======================================================

      // Woovi - Sandbox Select
      const sandboxSelect = document.querySelector('select[name="sandbox"]');
      if (sandboxSelect) {
        sandboxSelect.addEventListener('change', updateApiUrl);
      }

      // Woovi - Generate Token Button
      const generateBtn = document.getElementById('generate_webhook_token');
      if (generateBtn) {
        generateBtn.addEventListener('click', generateWebhookToken);
      }

      // Rede - Environment Radio Buttons
      const redeEnvSandbox = document.getElementById('rede_env_sandbox');
      const redeEnvProduction = document.getElementById('rede_env_production');
      if (redeEnvSandbox) {
        redeEnvSandbox.addEventListener('change', updateRedeUrl);
      }
      if (redeEnvProduction) {
        redeEnvProduction.addEventListener('change', updateRedeUrl);
      }

      // Stripe - Environment Radio Buttons
      const stripeEnvTest = document.getElementById('stripe_env_test');
      const stripeEnvLive = document.getElementById('stripe_env_live');
      if (stripeEnvTest) {
        stripeEnvTest.addEventListener('change', updateStripeUrl);
      }
      if (stripeEnvLive) {
        stripeEnvLive.addEventListener('change', updateStripeUrl);
      }
    });
  </script>
</x-app-layout>
