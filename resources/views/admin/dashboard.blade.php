<x-app-layout :route="'[ADMIN] Dashboard'">

  <div class="main-content app-content">
    <div class="container-fluid">
      
      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-shield-halved fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Dashboard Administrativo</h1>
                  <p class="text-muted mb-0">Painel de controle e monitoramento do sistema</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Carteira Gateway Info -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-info">
            <div class="card-body p-4">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-info text-white me-3" style="width: 48px; height: 48px;">
                  <i class="fa-solid fa-info-circle"></i>
                </div>
                <div>
                  <h5 class="fw-bold mb-1 text-info">Carteira Gateway</h5>
                  <p class="text-muted mb-0 small">Carteira administrativa para saque do lucro do sistema</p>
                </div>
              </div>
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                      <i class="fa-solid fa-percentage text-primary"></i>
                    </div>
                    <div>
                      <h6 class="mb-0 fw-semibold">Taxa Sistema</h6>
                      <small class="text-muted">Taxas coletadas pelo sistema</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                      <i class="fa-solid fa-hand-holding-dollar text-danger"></i>
                    </div>
                    <div>
                      <h6 class="mb-0 fw-semibold">Taxa Adquirente</h6>
                      <small class="text-muted">Taxas pagas aos adquirentes</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                      <i class="fa-solid fa-wallet text-success"></i>
                    </div>
                    <div>
                      <h6 class="mb-0 fw-semibold">Saldo Disponível</h6>
                      <small class="text-muted">Valor disponível para saque pelos admins</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtros -->
      <div class="row mb-4">
        <div class="col-12">
          <form method="GET" action="{{ route('admin.dashboard') }}" id="filtroForm">
            <div class="row g-3 align-items-end justify-content-end">
              <!-- Filtro por Período -->
              <!-- Período removido - usando filtros globais -->

              <!-- Botão CSV -->
              <div class="col-lg-auto col-md-auto">
                <a href="{{ route('admin.dashboard.export', request()->query()) }}" class="btn btn-success">
                  <i class="fas fa-file-csv me-1"></i>Exportar CSV
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Cards de Estatísticas -->
      <div class="row g-3">
        <!-- Saldo em Carteiras -->
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Saldo em Carteiras</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($carteiras, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Lucro Líquido -->
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-dollar-sign"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Lucro Líquido</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($lucro_liquido ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Total de Taxas -->
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fa-solid fa-percentage"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total de Taxas</p>
                  <h4 class="fw-bold mb-0 text-info text-truncate" style="font-size: 1.35rem;">R$ {{ number_format(($lucroDepositos ?? 0) + ($lucroSaques ?? 0), 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Taxas aos Adquirentes -->
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-warning flex-shrink-0">
                  <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Taxas aos Adquirentes</p>
                  <h4 class="fw-bold mb-0 text-warning text-truncate" style="font-size: 1.35rem;">R$ {{ number_format(($taxasAdquirentesEntradas ?? 0) + ($taxasAdquirentesSaidas ?? 0), 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Qtde Transações Aprovadas -->
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-check-circle"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Transações Aprovadas</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">{{ (clone $solicitacoes)->where('status', 'PAID_OUT')->count() + (clone $saques)->where('status', 'COMPLETED')->count() }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Valor Transações Aprovadas -->
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Valor Aprovado</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($valor_aprovado ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Usuários Cadastrados -->
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-primary flex-shrink-0">
                  <i class="fa-solid fa-users"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Usuários Cadastrados</p>
                  <h4 class="fw-bold mb-0 text-primary text-truncate" style="font-size: 1.35rem;">{{ $cadastros_total }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Usuários em Análise -->
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-warning flex-shrink-0">
                  <i class="fa-solid fa-clock"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Usuários em Análise</p>
                  <h4 class="fw-bold mb-0 text-warning text-truncate" style="font-size: 1.35rem;">{{ $cadastros_analise }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Saques -->
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-danger flex-shrink-0">
                  <i class="fa-solid fa-arrow-up"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Saques</p>
                  <h4 class="fw-bold mb-0 text-danger text-truncate" style="font-size: 1.35rem;">R$ {{ number_format((clone $saques)->sum('amount') ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Saques Pendentes -->
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-warning flex-shrink-0">
                  <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Saques Pendentes</p>
                  <h4 class="fw-bold mb-0 text-warning text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($saques_pendentes->sum('amount') ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- === CARTEIRA GATEWAY === -->
        <!-- Taxa Sistema Total - OCULTO -->
        <!--
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-primary flex-shrink-0">
                  <i class="fa-solid fa-coins"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Taxa Sistema</p>
                  <h4 class="fw-bold mb-0 text-primary text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($gatewayWalletData['taxa_sistema_total'] ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        -->

        <!-- Taxa Adquirente - OCULTO (duplicado com"Taxas aos Adquirentes") -->
        <!--
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-danger flex-shrink-0">
                  <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Taxa Adquirente</p>
                  <h4 class="fw-bold mb-0 text-danger text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($gatewayWalletData['taxa_adquirente_total'] ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        -->

        <!-- Saldo Carteira Gateway -->
        <div class="col-xxl-3 col-xl-4 col-md-6">
          <a href="{{ route('admin.gateway-wallet.index') }}" class="text-decoration-none">
            <div class="card card-hover shadow-sm">
              <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                    <i class="fa-solid fa-wallet"></i>
                  </div>
                  <div class="flex-grow-1 min-w-0">
                    <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Carteira Gateway</p>
                    <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($gatewayWalletData['saldo_disponivel'] ?? 0, 2, ',', '.') }}</h4>
                    @if(($gatewayWalletData['saldo_pendente'] ?? 0) > 0)
                      <small class="text-warning">Pendente: R$ {{ number_format($gatewayWalletData['saldo_pendente'] ?? 0, 2, ',', '.') }}</small>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>

    </div>
  </div>

  <!-- Modal Período Personalizado -->
  <div class="modal fade" id="dateRangeModal" tabindex="-1" aria-labelledby="dateRangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold" id="dateRangeModalLabel">
            <i class="fa-solid fa-calendar-days text-primary me-2"></i>
            Selecione o Período
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label fw-semibold mb-3">
                <i class="fa-solid fa-calendar-check text-success me-1"></i>
                Data de Início
              </label>
              <div class="d-flex justify-content-center" id="calendarInicio"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold mb-3">
                <i class="fa-solid fa-calendar-xmark text-danger me-1"></i>
                Data de Fim
              </label>
              <div class="d-flex justify-content-center" id="calendarFim"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Cancelar
          </button>
          <button type="button" class="btn btn-success" id="btnAplicarDatas">
            <i class="fa-solid fa-check me-1"></i>Aplicar
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // JavaScript do período removido - usando filtros globais
      console.log('📊 Admin Dashboard carregado - usando filtros globais');
    });
  </script>
  
  <!-- JavaScript antigo comentado - usando filtros globais
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Elementos DOM
      const select = document.getElementById("periodoSelect");
      const form = document.getElementById("filtroForm");
      const modalEl = document.getElementById('dateRangeModal');
      const btnAplicar = document.getElementById("btnAplicarDatas");

      let dataInicioSelecionada = null;
      let dataFimSelecionada = null;

      // Formatar data para exibição (BR)
      function formatarDataBr(data) {
        const meses = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
        const dia = String(data.getDate()).padStart(2, '0');
        const mes = meses[data.getMonth()];
        return `${dia} ${mes}`;
      }

      // Inicializar Flatpickr para Data de Início
      flatpickr("#calendarInicio", {
        inline: true,
        locale:"pt",
        dateFormat:"Y-m-d",
        onChange: function (selectedDates) {
          dataInicioSelecionada = selectedDates[0];
        }
      });

      // Inicializar Flatpickr para Data de Fim
      flatpickr("#calendarFim", {
        inline: true,
        locale:"pt",
        dateFormat:"Y-m-d",
        onChange: function (selectedDates) {
          dataFimSelecionada = selectedDates[0];
        }
      });

      // Evento: Mudança no select de período
      select.addEventListener("change", function () {
        if (select.value ==="personalizado") {
          new bootstrap.Modal(modalEl).show();
        } else {
          form.submit();
        }
      });

      // Evento: Aplicar período personalizado
      btnAplicar.addEventListener("click", function () {
        if (dataInicioSelecionada && dataFimSelecionada) {
          const inicioStr = dataInicioSelecionada.toISOString().split("T")[0];
          const fimStr = dataFimSelecionada.toISOString().split("T")[0];
          const texto = `${formatarDataBr(dataInicioSelecionada)} – ${formatarDataBr(dataFimSelecionada)}`;
          const valor = `${inicioStr}:${fimStr}`;

          // Remover opção personalizada anterior
          const opExistente = select.querySelector('option[data-personalizado]');
          if (opExistente) select.removeChild(opExistente);

          // Criar nova opção personalizada
          const novaOpcao = document.createElement("option");
          novaOpcao.value = valor;
          novaOpcao.textContent = texto;
          novaOpcao.setAttribute("data-personalizado","1");
          novaOpcao.selected = true;
          select.appendChild(novaOpcao);

          // Fechar modal e submeter formulário
          bootstrap.Modal.getInstance(modalEl).hide();
          form.submit();
        } else {
          alert("Por favor, selecione ambas as datas.");
        }
      });

      // Restaurar período personalizado da URL
      const urlParams = new URLSearchParams(window.location.search);
      const periodo = urlParams.get('periodo');

      if (periodo && select) {
        if (periodo.includes(':')) {
          const [inicioStr, fimStr] = periodo.split(':');
          const inicioDate = new Date(inicioStr);
          const fimDate = new Date(fimStr);
          
          dataInicioSelecionada = inicioDate;
          dataFimSelecionada = fimDate;

          const texto = `${formatarDataBr(inicioDate)} – ${formatarDataBr(fimDate)}`;
          const novaOpcao = document.createElement("option");
          novaOpcao.value = periodo;
          novaOpcao.textContent = texto;
          novaOpcao.setAttribute("data-personalizado","1");
          novaOpcao.selected = true;
          
          select.appendChild(novaOpcao);
        } else {
          const optionToSelect = Array.from(select.options).find(opt => opt.value === periodo);
          if (optionToSelect) {
            optionToSelect.selected = true;
          }
        }
      }
    });
  </script>
  -->

</x-app-layout>
