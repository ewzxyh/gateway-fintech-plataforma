<x-app-layout :route="'[ADMIN] Configurações de Saque'">
  
  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-cog fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Configurações de Saque</h1>
                  <p class="text-muted mb-0">Defina os parâmetros globais para processamento automático de saques</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Formulário de Configuração -->
      <div class="row">
        <div class="col-lg-8 col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="fa-solid fa-sliders text-primary me-2"></i>
                Parâmetros de Saque Automático
              </h5>
            </div>
            <div class="card-body p-4">
              <form action="{{ route('admin.saque-config.update') }}" method="POST" id="configForm">
                @csrf
                @method('PUT')

                <!-- Switch de Ativação -->
                <div class="row mb-4">
                  <div class="col-12">
                    <div class="card bg-light">
                      <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                          <div class="flex-grow-1">
                            <h6 class="mb-2">
                              <i class="fa-solid fa-power-off text-success me-2"></i>
                              Saque Automático Global
                            </h6>
                            <p class="text-muted mb-0 small">
                              Quando ativo, saques até o limite configurado são processados automaticamente.
                              Valores acima do limite serão direcionados para aprovação manual.
                            </p>
                          </div>
                          <div class="form-check form-switch ms-4">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="saque_automatico" name="saque_automatico" value="1"
                                style="width: 3rem; height: 1.5rem; cursor: pointer;"
                                {{ $config->saque_automatico ? 'checked' : '' }}>
                            <label class="form-check-label visually-hidden" for="saque_automatico">
                              Ativar Saque Automático
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Campo de Limite -->
                <div class="row mb-4">
                  <div class="col-md-8 col-12">
                    <label for="limite_saque_automatico" class="form-label fw-semibold">
                      <i class="fa-solid fa-dollar-sign text-success me-2"></i>
                      Limite para Saque Automático
                    </label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="fa-solid fa-brazilian-real-sign"></i>
                      </span>
                      <input type="text" class="form-control form-control-lg" 
                          id="limite_saque_automatico" name="limite_saque_automatico"
                          value="{{ number_format($config->limite_saque_automatico, 2, ',', '.') }}"
                          placeholder="Ex: 1.000,00">
                    </div>
                    <small class="form-text text-muted mt-2 d-block">
                      <i class="fa-solid fa-info-circle me-1"></i>
                      Valores <strong>até este limite</strong> serão processados automaticamente. 
                      Valores <strong>acima</strong> exigirão aprovação manual.
                    </small>
                  </div>
                </div>

                <!-- Card de Alerta Informativo -->
                <div class="row mb-4">
                  <div class="col-12">
                    <div class="alert alert-info d-flex align-items-start" role="alert">
                      <i class="fa-solid fa-lightbulb fa-lg me-3 mt-1"></i>
                      <div>
                        <h6 class="alert-heading mb-2">Sobre o Saque Automático</h6>
                        <p class="mb-2">
                          O sistema verificará automaticamente os saques solicitados e processará 
                          aqueles que estiverem dentro do limite configurado, seguindo estas regras:
                        </p>
                        <ul class="mb-0 small">
                          <li>Verificação de saldo disponível do usuário</li>
                          <li>Validação de dados bancários (chave PIX)</li>
                          <li>Aplicação de taxas conforme configurado no sistema</li>
                          <li>Processamento imediato via gateway de pagamento</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Botões de Ação -->
                <div class="row">
                  <div class="col-12">
                    <div class="d-flex gap-2 justify-content-start">
                      <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-save me-2"></i>Salvar Configurações
                      </button>
                      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fa-solid fa-arrow-left me-2"></i>Voltar ao Dashboard
                      </a>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Card de Estatísticas (opcional) -->
        <div class="col-lg-4 col-12">
          <div class="card border-success">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0">
                <i class="fa-solid fa-chart-line me-2"></i>
                Status Atual
              </h6>
            </div>
            <div class="card-body p-4">
              <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="text-muted">Status do Sistema</span>
                  @if($config->saque_automatico)
                    <span class="badge bg-success">
                      <i class="fa-solid fa-check-circle me-1"></i>Ativo
                    </span>
                  @else
                    <span class="badge bg-danger">
                      <i class="fa-solid fa-times-circle me-1"></i>Inativo
                    </span>
                  @endif
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <span class="text-muted">Limite Atual</span>
                  <span class="fw-bold text-success">
                    R$ {{ number_format($config->limite_saque_automatico, 2, ',', '.') }}
                  </span>
                </div>
              </div>

              <hr>

              <div class="mt-4">
                <h6 class="text-muted mb-3">
                  <i class="fa-solid fa-shield-halved text-warning me-2"></i>
                  Segurança
                </h6>
                <ul class="list-unstyled small text-muted mb-0">
                  <li class="mb-2">
                    <i class="fa-solid fa-check text-success me-2"></i>
                    Validação em tempo real
                  </li>
                  <li class="mb-2">
                    <i class="fa-solid fa-check text-success me-2"></i>
                    Logs de todas operações
                  </li>
                  <li class="mb-2">
                    <i class="fa-solid fa-check text-success me-2"></i>
                    Notificações automáticas
                  </li>
                  <li>
                    <i class="fa-solid fa-check text-success me-2"></i>
                    Reversão manual disponível
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // ======================================================
      // MÁSCARA DE MOEDA PARA O CAMPO DE LIMITE
      // ======================================================
      const limiteInput = document.getElementById('limite_saque_automatico');
      
      if (limiteInput) {
        limiteInput.addEventListener('keyup', function(e) {
          let value = e.target.value.replace(/\D/g, '');
          value = (value / 100).toFixed(2) + '';
          value = value.replace(".",",");
          value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
          e.target.value = value;
        });
      }

      // ======================================================
      // FEEDBACK VISUAL DO SWITCH
      // ======================================================
      const switchElement = document.getElementById('saque_automatico');
      
      if (switchElement) {
        switchElement.addEventListener('change', function() {
          if (this.checked) {
            console.log('Saque automático será ATIVADO após salvar');
          } else {
            console.log('Saque automático será DESATIVADO após salvar');
          }
        });
      }

      // ======================================================
      // VALIDAÇÃO DO FORMULÁRIO
      // ======================================================
      const configForm = document.getElementById('configForm');
      
      if (configForm) {
        configForm.addEventListener('submit', function(e) {
          const limite = limiteInput.value.replace(/\D/g, '');
          
          if (!limite || parseFloat(limite) < 0) {
            e.preventDefault();
            alert('Por favor, informe um limite válido maior ou igual a zero.');
            limiteInput.focus();
            return false;
          }
        });
      }
    });
  </script>

</x-app-layout>