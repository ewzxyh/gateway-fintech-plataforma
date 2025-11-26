<x-app-layout :route="'[ADMIN] Ajustes Gerais'">

  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-cogs fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Ajustes Gerais</h1>
                  <p class="text-muted mb-0">Configure taxas, limites, segurança e personalizações do sistema</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <form method="POST" action="{{ route('admin.ajustes.gerais') }}" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <!-- Card: Taxas de Transações -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-primary">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                  <i class="fa-solid fa-percent me-2"></i>
                  Taxas de Transações
                </h5>
              </div>
              <div class="card-body p-4">
                <!-- SEÇÃO: DEPÓSITOS -->
                <div class="mb-4">
                  <h6 class="fw-bold mb-3 pb-2 border-bottom">
                    <i class="fa-solid fa-arrow-down text-success me-2"></i>
                    Configurações de Depósito
                  </h6>
                  <div class="row g-3">
                    <div class="col-md-6 col-lg-4">
                      <label for="taxa_cash_in_padrao" class="form-label fw-semibold">
                        <i class="fa-solid fa-percentage text-primary me-1"></i>
                        Taxa Percentual Depósito (%)
                      </label>
                      <input type="text" class="form-control @error('taxa_cash_in_padrao') is-invalid @enderror" 
                          name="taxa_cash_in_padrao" 
                          value="{{ $setting->taxa_cash_in_padrao }}" required>
                      @error('taxa_cash_in_padrao')<span class="text-danger small">{{ $message }}</span>@enderror
                      <small class="text-muted d-block mt-1">Taxa percentual aplicada sobre o valor do depósito</small>
                    </div>
                    <div class="col-md-6 col-lg-4">
                      <label for="taxa_fixa_padrao" class="form-label fw-semibold">
                        <i class="fa-solid fa-dollar-sign text-success me-1"></i>
                        Taxa Fixa Depósito (R$)
                      </label>
                      <input type="text" class="form-control @error('taxa_fixa_padrao') is-invalid @enderror" 
                          name="taxa_fixa_padrao" 
                          value="{{ $setting->taxa_fixa_padrao }}" required>
                      @error('taxa_fixa_padrao')<span class="text-danger small">{{ $message }}</span>@enderror
                      <small class="text-muted d-block mt-1">Taxa fixa aplicada em todos os depósitos</small>
                    </div>
                    <div class="col-md-6 col-lg-4">
                      <label for="deposito_minimo" class="form-label fw-semibold">
                        <i class="fa-solid fa-coins text-warning me-1"></i>
                        Valor Mínimo de Depósito (R$)
                      </label>
                      <input type="text" class="form-control @error('deposito_minimo') is-invalid @enderror" 
                          name="deposito_minimo" 
                          value="{{ $setting->deposito_minimo }}" required>
                      @error('deposito_minimo')<span class="text-danger small">{{ $message }}</span>@enderror
                      <small class="text-muted d-block mt-1">Valor mínimo que o usuário pode depositar</small>
                    </div>
                  </div>
                </div>

                <!-- SEÇÃO: SAQUES -->
                <div class="mb-4">
                  <h6 class="fw-bold mb-3 pb-2 border-bottom">
                    <i class="fa-solid fa-arrow-up text-danger me-2"></i>
                    Configurações de Saque
                  </h6>
                  <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                      <label for="taxa_cash_out_padrao" class="form-label fw-semibold">
                        <i class="fa-solid fa-percentage text-primary me-1"></i>
                        Taxa Percentual PIX (%)
                      </label>
                      <input type="text" class="form-control @error('taxa_cash_out_padrao') is-invalid @enderror" 
                          name="taxa_cash_out_padrao" 
                          value="{{ $setting->taxa_cash_out_padrao }}" required>
                      @error('taxa_cash_out_padrao')<span class="text-danger small">{{ $message }}</span>@enderror
                      <small class="text-muted d-block mt-1">Taxa percentual aplicada sobre o valor do saque PIX</small>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <label for="baseline" class="form-label fw-semibold">
                        <i class="fa-solid fa-dollar-sign text-success me-1"></i>
                        Taxa Mínima PIX (R$)
                      </label>
                      <input type="text" class="form-control @error('baseline') is-invalid @enderror" 
                          name="baseline" 
                          value="{{ $setting->baseline }}" required>
                      @error('baseline')<span class="text-danger small">{{ $message }}</span>@enderror
                      <small class="text-muted d-block mt-1">Valor mínimo de taxa (se maior que percentual)</small>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <label for="taxa_fixa_pix" class="form-label fw-semibold">
                        <i class="fa-solid fa-coins text-warning me-1"></i>
                        Taxa Fixa PIX (R$)
                      </label>
                      <input type="text" class="form-control @error('taxa_fixa_pix') is-invalid @enderror" 
                          name="taxa_fixa_pix" 
                          value="{{ $setting->taxa_fixa_pix ?? 0.00 }}" required>
                      @error('taxa_fixa_pix')<span class="text-danger small">{{ $message }}</span>@enderror
                      <small class="text-muted d-block mt-1">Taxa fixa adicional (sempre somada)</small>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <label for="saque_minimo" class="form-label fw-semibold">
                        <i class="fa-solid fa-hand-holding-dollar text-info me-1"></i>
                        Valor Mínimo de Saque (R$)
                      </label>
                      <input type="text" class="form-control @error('saque_minimo') is-invalid @enderror" 
                          name="saque_minimo" 
                          value="{{ $setting->saque_minimo }}" required>
                      @error('saque_minimo')<span class="text-danger small">{{ $message }}</span>@enderror
                      <small class="text-muted d-block mt-1">Valor mínimo que o usuário pode sacar</small>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <label for="limite_saque_mensal" class="form-label fw-semibold">
                        <i class="fa-solid fa-calendar-days text-primary me-1"></i>
                        Limite Mensal PF (R$)
                      </label>
                      <input type="text" class="form-control @error('limite_saque_mensal') is-invalid @enderror" 
                          name="limite_saque_mensal" 
                          value="{{ $setting->limite_saque_mensal }}" required>
                      @error('limite_saque_mensal')<span class="text-danger small">{{ $message }}</span>@enderror
                      <small class="text-muted d-block mt-1">Limite máximo por mês (pessoa física)</small>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <label for="taxa_saque_api_padrao" class="form-label fw-semibold">
                        <i class="fa-solid fa-code text-secondary me-1"></i>
                        Taxa Saque via API (%)
                      </label>
                      <input type="text" class="form-control @error('taxa_saque_api_padrao') is-invalid @enderror" 
                          name="taxa_saque_api_padrao" 
                          value="{{ $setting->taxa_saque_api_padrao ?? 5.00 }}" required>
                      @error('taxa_saque_api_padrao')<span class="text-danger small">{{ $message }}</span>@enderror
                      <small class="text-muted d-block mt-1">Taxa para saques via API externa</small>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <label for="taxa_saque_cripto_padrao" class="form-label fw-semibold">
                        <i class="fa-brands fa-bitcoin text-warning me-1"></i>
                        Taxa Saque Cripto (%)
                      </label>
                      <input type="text" class="form-control @error('taxa_saque_cripto_padrao') is-invalid @enderror" 
                          name="taxa_saque_cripto_padrao" 
                          value="{{ $setting->taxa_saque_cripto_padrao ?? 1.00 }}" required>
                      @error('taxa_saque_cripto_padrao')<span class="text-danger small">{{ $message }}</span>@enderror
                      <small class="text-muted d-block mt-1">Taxa para criptomoedas (Bitcoin, Ethereum, etc.)</small>
                    </div>
                  </div>
                </div>

                <!-- Alert Explicativo -->
                <div class="alert alert-info d-flex align-items-start">
                  <i class="fa-solid fa-calculator fa-lg me-3 mt-1"></i>
                  <div>
                    <h6 class="alert-heading mb-2">Como funciona o cálculo de taxa de saque PIX:</h6>
                    <ul class="mb-0 small">
                      <li><strong>Taxa percentual PIX:</strong> {{ $setting->taxa_cash_out_padrao ?? 2.00 }}% do valor</li>
                      <li><strong>Taxa mínima PIX:</strong> R$ {{ $setting->baseline ?? 0.80 }}</li>
                      <li><strong>Taxa fixa PIX:</strong> R$ {{ $setting->taxa_fixa_pix ?? 0.00 }} (sempre somada)</li>
                      <li><strong>Taxa aplicada:</strong> Maior valor entre taxa percentual e taxa mínima + taxa fixa</li>
                      <li><strong>Exemplo:</strong> R$ 2,00 → 2% = R$ 0,04 < R$ 0,80 → Taxa = R$ 0,80 + R$ {{ $setting->taxa_fixa_pix ?? 0.00 }} = R$ {{ number_format(($setting->baseline ?? 0.80) + ($setting->taxa_fixa_pix ?? 0.00), 2, ',', '.') }}</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Sistema de Taxas Flexível -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-success">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                  <i class="fa-solid fa-sliders me-2"></i>
                  Sistema de Taxas Flexível para Depósitos
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">
                  <div class="col-12">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" role="switch" 
                          id="taxa_flexivel_ativa" name="taxa_flexivel_ativa" value="1" 
                          {{ $setting->taxa_flexivel_ativa ? 'checked' : '' }}
                          style="width: 3rem; height: 1.5rem; cursor: pointer;">
                      <label class="form-check-label fw-semibold ms-2" for="taxa_flexivel_ativa">
                        Ativar Sistema de Taxas Flexível
                      </label>
                    </div>
                    <small class="text-muted d-block mt-2">Quando ativado, o sistema aplicará taxas diferentes baseadas no valor do depósito</small>
                  </div>
                  <div class="col-md-4">
                    <label for="taxa_flexivel_valor_minimo" class="form-label fw-semibold">
                      <i class="fa-solid fa-hand-holding-dollar text-primary me-1"></i>
                      Valor Mínimo (R$)
                    </label>
                    <input type="text" class="form-control @error('taxa_flexivel_valor_minimo') is-invalid @enderror" 
                        name="taxa_flexivel_valor_minimo" 
                        value="{{ $setting->taxa_flexivel_valor_minimo ?? 15.00 }}" required>
                    @error('taxa_flexivel_valor_minimo')<span class="text-danger small">{{ $message }}</span>@enderror
                    <small class="text-muted d-block mt-1">Depósitos abaixo deste valor terão taxa fixa</small>
                  </div>
                  <div class="col-md-4">
                    <label for="taxa_flexivel_fixa_baixo" class="form-label fw-semibold">
                      <i class="fa-solid fa-dollar-sign text-success me-1"></i>
                      Taxa Fixa Valores Baixos (R$)
                    </label>
                    <input type="text" class="form-control @error('taxa_flexivel_fixa_baixo') is-invalid @enderror" 
                        name="taxa_flexivel_fixa_baixo" 
                        value="{{ $setting->taxa_flexivel_fixa_baixo ?? 1.00 }}" required>
                    @error('taxa_flexivel_fixa_baixo')<span class="text-danger small">{{ $message }}</span>@enderror
                    <small class="text-muted d-block mt-1">Taxa fixa para depósitos abaixo do valor mínimo</small>
                  </div>
                  <div class="col-md-4">
                    <label for="taxa_flexivel_percentual_alto" class="form-label fw-semibold">
                      <i class="fa-solid fa-percentage text-warning me-1"></i>
                      Taxa Percentual Valores Altos (%)
                    </label>
                    <input type="text" class="form-control @error('taxa_flexivel_percentual_alto') is-invalid @enderror" 
                        name="taxa_flexivel_percentual_alto" 
                        value="{{ $setting->taxa_flexivel_percentual_alto ?? 4.00 }}" required>
                    @error('taxa_flexivel_percentual_alto')<span class="text-danger small">{{ $message }}</span>@enderror
                    <small class="text-muted d-block mt-1">Taxa percentual para depósitos acima do valor mínimo</small>
                  </div>
                </div>

                <!-- Alert Explicativo -->
                <div class="alert alert-success mt-3 d-flex align-items-start">
                  <i class="fa-solid fa-lightbulb fa-lg me-3 mt-1"></i>
                  <div>
                    <h6 class="alert-heading mb-2">Como funciona:</h6>
                    <ul class="mb-0 small">
                      <li><strong>Depósitos abaixo de R$ {{ $setting->taxa_flexivel_valor_minimo ?? 15.00 }}:</strong> Taxa fixa de R$ {{ $setting->taxa_flexivel_fixa_baixo ?? 1.00 }}</li>
                      <li><strong>Depósitos acima de R$ {{ $setting->taxa_flexivel_valor_minimo ?? 15.00 }}:</strong> Taxa percentual de {{ $setting->taxa_flexivel_percentual_alto ?? 4.00 }}%</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Personalização de Relatórios -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-warning">
              <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                  <i class="fa-solid fa-chart-line me-2"></i>
                  Personalização de Relatórios
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row">
                  <!-- RELATÓRIO DE ENTRADAS -->
                  <div class="col-lg-6 mb-4">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom">
                      <i class="fa-solid fa-arrow-down text-success me-2"></i>
                      Relatório de Entradas
                    </h6>
                    <div class="row g-2">
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_entradas_mostrar_meio" name="relatorio_entradas_mostrar_meio" value="1" {{ ($setting->relatorio_entradas_mostrar_meio ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_entradas_mostrar_meio">Mostrar Meio</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_entradas_mostrar_transacao_id" name="relatorio_entradas_mostrar_transacao_id" value="1" {{ ($setting->relatorio_entradas_mostrar_transacao_id ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_entradas_mostrar_transacao_id">Mostrar Transação ID</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_entradas_mostrar_valor" name="relatorio_entradas_mostrar_valor" value="1" {{ ($setting->relatorio_entradas_mostrar_valor ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_entradas_mostrar_valor">Mostrar Valor</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_entradas_mostrar_valor_liquido" name="relatorio_entradas_mostrar_valor_liquido" value="1" {{ ($setting->relatorio_entradas_mostrar_valor_liquido ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_entradas_mostrar_valor_liquido">Mostrar Valor Líquido</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_entradas_mostrar_nome" name="relatorio_entradas_mostrar_nome" value="1" {{ ($setting->relatorio_entradas_mostrar_nome ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_entradas_mostrar_nome">Mostrar Nome</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_entradas_mostrar_documento" name="relatorio_entradas_mostrar_documento" value="1" {{ ($setting->relatorio_entradas_mostrar_documento ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_entradas_mostrar_documento">Mostrar Documento</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_entradas_mostrar_status" name="relatorio_entradas_mostrar_status" value="1" {{ ($setting->relatorio_entradas_mostrar_status ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_entradas_mostrar_status">Mostrar Status</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_entradas_mostrar_data" name="relatorio_entradas_mostrar_data" value="1" {{ ($setting->relatorio_entradas_mostrar_data ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_entradas_mostrar_data">Mostrar Data</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_entradas_mostrar_taxa" name="relatorio_entradas_mostrar_taxa" value="1" {{ ($setting->relatorio_entradas_mostrar_taxa ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_entradas_mostrar_taxa">Mostrar Taxa</label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- RELATÓRIO DE SAÍDAS -->
                  <div class="col-lg-6 mb-4">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom">
                      <i class="fa-solid fa-arrow-up text-danger me-2"></i>
                      Relatório de Saídas
                    </h6>
                    <div class="row g-2">
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_saidas_mostrar_transacao_id" name="relatorio_saidas_mostrar_transacao_id" value="1" {{ ($setting->relatorio_saidas_mostrar_transacao_id ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_saidas_mostrar_transacao_id">Mostrar Transação ID</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_saidas_mostrar_valor" name="relatorio_saidas_mostrar_valor" value="1" {{ ($setting->relatorio_saidas_mostrar_valor ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_saidas_mostrar_valor">Mostrar Valor</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_saidas_mostrar_nome" name="relatorio_saidas_mostrar_nome" value="1" {{ ($setting->relatorio_saidas_mostrar_nome ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_saidas_mostrar_nome">Mostrar Nome</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_saidas_mostrar_chave_pix" name="relatorio_saidas_mostrar_chave_pix" value="1" {{ ($setting->relatorio_saidas_mostrar_chave_pix ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_saidas_mostrar_chave_pix">Mostrar Chave PIX</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_saidas_mostrar_tipo_chave" name="relatorio_saidas_mostrar_tipo_chave" value="1" {{ ($setting->relatorio_saidas_mostrar_tipo_chave ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_saidas_mostrar_tipo_chave">Mostrar Tipo Chave</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_saidas_mostrar_status" name="relatorio_saidas_mostrar_status" value="1" {{ ($setting->relatorio_saidas_mostrar_status ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_saidas_mostrar_status">Mostrar Status</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_saidas_mostrar_data" name="relatorio_saidas_mostrar_data" value="1" {{ ($setting->relatorio_saidas_mostrar_data ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_saidas_mostrar_data">Mostrar Data</label>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="relatorio_saidas_mostrar_taxa" name="relatorio_saidas_mostrar_taxa" value="1" {{ ($setting->relatorio_saidas_mostrar_taxa ?? true) ? 'checked' : '' }}>
                          <label class="form-check-label" for="relatorio_saidas_mostrar_taxa">Mostrar Taxa</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Alert Explicativo -->
                <div class="alert alert-warning d-flex align-items-start">
                  <i class="fa-solid fa-lightbulb fa-lg me-3 mt-1"></i>
                  <div>
                    <h6 class="alert-heading mb-2">Como funciona:</h6>
                    <ul class="mb-0 small">
                      <li>Desative os campos que você não quer que apareçam nos relatórios dos usuários</li>
                      <li>Os campos desativados ficarão ocultos tanto na visualização quanto na exportação</li>
                      <li>Esta configuração é global e afeta todos os usuários do sistema</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Configurações de Segurança -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-danger">
              <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                  <i class="fa-solid fa-shield-halved me-2"></i>
                  Configurações de Segurança
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row">
                  <div class="col-12">
                    <label for="global_ips" class="form-label fw-semibold">
                      <i class="fa-solid fa-network-wired text-primary me-2"></i>
                      IPs Globais Autorizados
                    </label>
                    <input type="text" class="form-control @error('global_ips') is-invalid @enderror" 
                        name="global_ips" 
                        value="{{ is_array($setting->global_ips) ? implode(', ', $setting->global_ips) : ($setting->global_ips ?? '') }}" 
                        placeholder="Ex: 54.232.237.217, 192.168.1.100">
                    @error('global_ips')<span class="text-danger small">{{ $message }}</span>@enderror
                    <div class="mt-2">
                      <small class="text-muted d-block fw-bold">IPs que são autorizados para TODOS os usuários</small>
                      <ul class="small text-muted mb-0 mt-1">
                        <li>Separe múltiplos IPs com vírgula</li>
                        <li>Estes IPs funcionam para saques via interface web</li>
                        <li>Útil quando o servidor muda de IP</li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Alert Explicativo -->
                <div class="alert alert-danger mt-3 d-flex align-items-start">
                  <i class="fa-solid fa-exclamation-triangle fa-lg me-3 mt-1"></i>
                  <div>
                    <h6 class="alert-heading mb-2">Dica de Segurança:</h6>
                    <p class="mb-0 small">Adicione o IP atual do servidor aqui para que os saques via interface web funcionem automaticamente.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Informações do Gerente -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-success">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                  <i class="fa-solid fa-user-tie me-2"></i>
                  Informações do Gerente
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">
                  <div class="col-md-6 col-lg-4">
                    <label for="gerente_nome" class="form-label fw-semibold">
                      <i class="fa-solid fa-user text-primary me-1"></i>
                      Nome do Gerente
                    </label>
                    <input type="text" class="form-control @error('gerente_nome') is-invalid @enderror" 
                        name="gerente_nome" 
                        value="{{ $setting->gerente_nome ?? 'Kauan' }}" required>
                    @error('gerente_nome')<span class="text-danger small">{{ $message }}</span>@enderror
                    <small class="text-muted d-block mt-1">Nome que aparece no modal "Fale com seu Gerente"</small>
                  </div>
                  <div class="col-md-6 col-lg-4">
                    <label for="gerente_foto" class="form-label fw-semibold">
                      <i class="fa-solid fa-camera text-success me-1"></i>
                      Foto do Gerente
                    </label>
                    <input type="file" class="form-control @error('gerente_foto') is-invalid @enderror" 
                        name="gerente_foto" 
                        accept="image/*">
                    @error('gerente_foto')<span class="text-danger small">{{ $message }}</span>@enderror
                    <small class="text-muted d-block mt-1">Foto que aparece no modal (recomendado: 300x300px)</small>
                  </div>
                  <div class="col-md-6 col-lg-4">
                    <label class="form-label fw-semibold">
                      <i class="fa-solid fa-eye text-info me-1"></i>
                      Preview da Foto
                    </label>
                    <div class="text-center">
                      @if($setting->gerente_foto)
                        <img src="{{ asset($setting->gerente_foto) }}" 
                             alt="Foto do Gerente" 
                             class="img-thumbnail" 
                             style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                      @else
                        <div class="bg-light border rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px;">
                          <i class="fa-solid fa-user fa-2x text-muted"></i>
                        </div>
                      @endif
                    </div>
                    <small class="text-muted d-block mt-1">Preview da foto atual</small>
                  </div>
                </div>
                
                <!-- Alert Explicativo -->
                <div class="alert alert-success mt-3 d-flex align-items-start">
                  <i class="fa-solid fa-info-circle fa-lg me-3 mt-1"></i>
                  <div>
                    <h6 class="alert-heading mb-2">Como funciona:</h6>
                    <ul class="mb-0 small">
                      <li><strong>Nome:</strong> Aparece no modal "Fale com seu Gerente"</li>
                      <li><strong>Foto:</strong> Substitui o avatar 3D no modal</li>
                      <li><strong>Contato:</strong> Usa o número configurado no campo "Contato (Gerente)" acima</li>
                      <li><strong>WhatsApp:</strong> Link direto para conversa com o gerente</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Informações do Gateway -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-info">
              <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                  <i class="fa-solid fa-building me-2"></i>
                  Informações do Gateway
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">
                  <div class="col-md-6 col-lg-4">
                    <label for="gateway_name" class="form-label fw-semibold">
                      <i class="fa-solid fa-tag text-primary me-1"></i>
                      Nome do Gateway
                    </label>
                    <input type="text" class="form-control @error('gateway_name') is-invalid @enderror" 
                        name="gateway_name" 
                        value="{{ $setting->gateway_name }}" required>
                    @error('gateway_name')<span class="text-danger small">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-6 col-lg-4">
                    <label for="gateway_color" class="form-label fw-semibold">
                      <i class="fa-solid fa-palette text-success me-1"></i>
                      Cor Padrão
                    </label>
                    <input type="color" class="form-control form-control-color @error('gateway_color') is-invalid @enderror" 
                        name="gateway_color" 
                        value="{{ $setting->gateway_color }}" required>
                    @error('gateway_color')<span class="text-danger small">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-6 col-lg-4">
                    <label for="cnpj" class="form-label fw-semibold">
                      <i class="fa-solid fa-id-card text-warning me-1"></i>
                      CNPJ
                    </label>
                    <input type="text" class="form-control @error('cnpj') is-invalid @enderror" 
                        name="cnpj" 
                        value="{{ $setting->cnpj }}" required>
                    @error('cnpj')<span class="text-danger small">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-6 col-lg-4">
                    <label for="contato" class="form-label fw-semibold">
                      <i class="fa-solid fa-phone text-info me-1"></i>
                      Contato (Gerente)
                    </label>
                    <input type="text" class="form-control @error('contato') is-invalid @enderror" 
                        name="contato" 
                        value="{{ $setting->contato }}" required>
                    @error('contato')<span class="text-danger small">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-md-6 col-lg-4">
                    <x-image-upload id="gateway_logo" name="gateway_logo" label="Logo Tema Claro" :value="asset($setting->gateway_logo)" />
                  </div>
                  <div class="col-md-6 col-lg-4">
                    <x-image-upload id="gateway_logo_dark" name="gateway_logo_dark" label="Logo Tema Escuro" :value="asset($setting->gateway_logo_dark)" />
                  </div>
                  <div class="col-md-6 col-lg-4">
                    <x-image-upload id="gateway_favicon" name="gateway_favicon" label="Ícone (Favicon)" :value="asset($setting->gateway_favicon)" height="120px" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Botão de Submit -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card">
              <div class="card-body p-4 text-end">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                  <i class="fa-solid fa-save me-2"></i>
                  Salvar Configurações
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>

    </div>
  </div>
</x-app-layout>
