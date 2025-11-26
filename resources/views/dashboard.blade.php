@php
$setting = \App\Helpers\Helper::getSetting();
\App\Helpers\Helper::calculaSaldoLiquido(auth()->user()->user_id);
@endphp

<x-app-layout :route="'Dashboard'">

  <!-- Modal Status -->
  <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title fw-bold" id="statusModalLabel">
            <i class="fas fa-exclamation-triangle me-2"></i>Atenção
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-0">Você precisa concluir o cadastro para ativar sua conta.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <a href="{{ url('/enviar-doc') }}" class="btn btn-success">
            <i class="fas fa-upload me-1"></i>Enviar Documentos
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="main-content app-content">
    <div class="container-fluid">
      <!-- Alerta Status 0 -->
      @if($status == 0)
      <div class="row mt-4 mb-4">
        <div class="col-12">
          <div class="card border-start border-warning border-4">
            <div class="card-body p-4">
              <div class="d-flex align-items-start gap-3">
                <div class="flex-shrink-0">
                  <div class="icon-circle bg-warning text-white">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h5 class="h4 mb-3">Ativação de Conta</h5>
                  <p class="text-muted mb-4">Para ativar sua conta, é necessário o envio de documentos. Por favor, envie os documentos para análise.</p>
                  <a href="{{ url('/enviar-doc') }}" class="btn btn-success d-inline-flex align-items-center gap-2">
                    <i class="fas fa-upload"></i>
                    Enviar Documentos
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif

      <!-- Alerta Status 5 -->
      @if($status == 5)
      <div class="row mt-4 mb-4">
        <div class="col-12">
          <div class="card border-start border-info border-4">
            <div class="card-body p-4">
              <div class="d-flex align-items-start gap-3">
                <div class="flex-shrink-0">
                  <div class="icon-circle bg-info text-white">
                    <i class="fas fa-clock"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h5 class="h4 mb-3">Sua conta está em Análise</h5>
                  <p class="text-muted mb-0">Nossa equipe está analisando seus documentos e logo vai entrar em contato.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif

      @if($status == 1)

      <!-- Page Header -->
      <div class="row mb-5">
        <!-- Card de Título Estilizado -->
        <!-- <div class="col-12 mb-4">
          <div class="card border-primary">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-primary text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-chart-line fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Dashboard</h1>
                  <p class="text-muted mb-0">Painel de controle e monitoramento das suas transações</p>
                </div>
              </div>
            </div>
          </div>
        </div> -->

        <!-- Filtros -->
        <div class="col-12">
          <form method="GET" action="{{ route('dashboard') }}" id="filtroForm">
            <div class="row g-3 align-items-end justify-content-end">
              <!-- Produto removido - usando filtros globais -->
            </div>
          </form>
        </div>
      </div>

      <!-- Cards de Métricas Principais -->
      <div class="row mb-4 g-3">
        <!-- Saldo Disponível -->
        <div class="col-xxl-3 col-lg-6 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fas fa-wallet"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Saldo disponível</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">R$ {{ number_format(auth()->user()->saldo ?? 0, 2, ',', '.') }}</h4>
                  <span class="badge badge-soft-warning mt-1" style="font-size: 0.65rem;">
                    Pendente: R$ {{ number_format(auth()->user()->valor_saque_pendente, 2, ',', '.') }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Vendas Realizadas -->
        <div class="col-xxl-3 col-lg-6 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fas fa-arrow-trend-up"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Vendas Realizadas</p>
                  <h4 class="fw-bold mb-0 text-info text-truncate" style="font-size: 1.35rem;">R$ {{ number_format((clone $solicitacoes)->where('status', 'PAID_OUT')->sum('amount') ?? 0, 2, ',', '.') }}</h4>
                  <div class="d-flex align-items-center gap-1 mt-1" style="font-size: 0.7rem;">
                    <span class="text-success"><i class="fas fa-arrow-up"></i> +12.5%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quantidade de Vendas -->
        <div class="col-xxl-3 col-lg-6 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-primary flex-shrink-0">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total de Vendas</p>
                  <h4 class="fw-bold mb-0 text-primary text-truncate" style="font-size: 1.35rem;">{{ (clone $solicitacoes)->where('status', 'PAID_OUT')->count() }}</h4>
                  <div class="d-flex align-items-center gap-1 mt-1" style="font-size: 0.7rem;">
                    <span class="text-success"><i class="fas fa-arrow-up"></i> +8.2%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Ticket Médio -->
        <div class="col-xxl-3 col-lg-6 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-warning flex-shrink-0">
                  <i class="fas fa-calculator"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Ticket Médio</p>
                  @php
                  $paidOutSolicitacoes = (clone $solicitacoes)->where('status', 'PAID_OUT');
                  $countPaidOut = $paidOutSolicitacoes->count();
                  $ticketMedio = $countPaidOut > 0 ? $paidOutSolicitacoes->sum('amount') / $countPaidOut : 0;
                  @endphp
                  <h4 class="fw-bold mb-0 text-warning text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</h4>
                  <div class="d-flex align-items-center gap-1 mt-1" style="font-size: 0.7rem;">
                    <span class="text-danger"><i class="fas fa-arrow-down"></i> -2.1%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gateway de Pagamento - Cards Modernos -->
      <div class="row g-3 mb-4">
        <!-- PIX -->
        <div class="col-lg-3 col-md-6">
          <div class="card  shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                  <div class="bg-success rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                    <i class="fa-brands fa-pix text-white" style="font-size: 14px;"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold text-dark">PIX</h6>
                    <small class="text-muted">Instantâneo</small>
                  </div>
                </div>
                @php
                $totalFiltradasPix = (clone $solicitacoes)->where('method', 'pix')->count();
                $totalAprovadasPix = (clone $solicitacoes)->where('method', 'pix')->where('status', 'PAID_OUT')->count();
                $porcentagemAprovadasPix = $totalFiltradasPix > 0 ? ($totalAprovadasPix / $totalFiltradasPix) * 100 : 0;
                @endphp
                <span class="badge bg-success-subtle text-success">{{ number_format($porcentagemAprovadasPix, 1) }}%</span>
              </div>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <small class="text-muted">Volume</small>
                  <div class="fw-bold text-success">R$ {{ number_format((clone $solicitacoes)->where('method', 'pix')->where('status', 'PAID_OUT')->sum('amount') ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="text-end">
                  <small class="text-muted">Transações</small>
                  <div class="fw-semibold">{{ $totalAprovadasPix }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cartão -->
        <div class="col-lg-3 col-md-6">
          <div class="card  shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                  <div class="bg-primary rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                    <i class="fas fa-credit-card text-white" style="font-size: 14px;"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold text-dark">Cartão</h6>
                    <small class="text-muted">Crédito/Débito</small>
                  </div>
                </div>
                @php
                $totalFiltradasCartao = (clone $solicitacoes)->where('method', 'card')->count();
                $totalAprovadasCartao = (clone $solicitacoes)->where('method', 'card')->where('status', 'PAID_OUT')->count();
                $porcentagemAprovadasCartao = $totalFiltradasCartao > 0 ? ($totalAprovadasCartao / $totalFiltradasCartao) * 100 : 0;
                @endphp
                <span class="badge bg-primary-subtle text-primary">{{ number_format($porcentagemAprovadasCartao, 1) }}%</span>
              </div>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <small class="text-muted">Volume</small>
                  <div class="fw-bold text-primary">R$ {{ number_format((clone $solicitacoes)->where('method', 'card')->where('status', 'PAID_OUT')->sum('amount') ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="text-end">
                  <small class="text-muted">Transações</small>
                  <div class="fw-semibold">{{ $totalAprovadasCartao }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Boleto -->
        <div class="col-lg-3 col-md-6">
          <div class="card  shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                  <div class="bg-info rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                    <i class="fas fa-barcode text-white" style="font-size: 14px;"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold text-dark">Boleto</h6>
                    <small class="text-muted">Bancário</small>
                  </div>
                </div>
                @php
                $totalFiltradasBoleto = (clone $solicitacoes)->where('method', 'billet')->count();
                $totalAprovadasBoleto = (clone $solicitacoes)->where('method', 'billet')->where('status', 'PAID_OUT')->count();
                $porcentagemAprovadasBoleto = $totalFiltradasBoleto > 0 ? ($totalAprovadasBoleto / $totalFiltradasBoleto) * 100 : 0;
                @endphp
                <span class="badge bg-info-subtle text-info">{{ number_format($porcentagemAprovadasBoleto, 1) }}%</span>
              </div>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <small class="text-muted">Volume</small>
                  <div class="fw-bold text-info">R$ {{ number_format((clone $solicitacoes)->where('method', 'billet')->where('status', 'PAID_OUT')->sum('amount') ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="text-end">
                  <small class="text-muted">Transações</small>
                  <div class="fw-semibold">{{ $totalAprovadasBoleto }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Métricas de Risco -->
        <div class="col-lg-3 col-md-6">
          <div class="card  shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                  <div class="bg-warning rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                    <i class="fas fa-shield-halved text-white" style="font-size: 14px;"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold text-dark">Risco</h6>
                    <small class="text-muted">Controle</small>
                  </div>
                </div>
                <span class="badge bg-warning-subtle text-warning">Baixo</span>
              </div>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <small class="text-muted">Chargeback</small>
                  <div class="fw-bold text-warning">0.2%</div>
                </div>
                <div class="text-end">
                  <small class="text-muted">MED</small>
                  <div class="fw-semibold">0.1%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Análise de Performance -->
      <div class="row g-3 mb-4">
        <!-- Gráfico de Faturamento -->
        <div class="col-12">
          <div class="card  shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <h6 class="mb-0 fw-semibold text-dark">
                    <i class="fas fa-chart-line me-2 text-success"></i>Fluxo de Caixa
                  </h6>
                  <small class="text-muted">Entradas vs Saídas</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                  @if($crescimentoVendas >= 0)
                    <span class="badge bg-success-subtle text-success">
                      <i class="fas fa-arrow-up me-1"></i>+{{ number_format($crescimentoVendas, 1) }}%
                    </span>
                  @else
                    <span class="badge bg-danger-subtle text-danger">
                      <i class="fas fa-arrow-down me-1"></i>{{ number_format($crescimentoVendas, 1) }}%
                    </span>
                  @endif
                </div>
              </div>
              <div id="faturamentoChart" style="position: relative; width: 100%; height: 280px;"></div>
            </div>
          </div>
        </div>

      </div>

      <!-- Transações Recentes -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card  shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <h6 class="mb-0 fw-semibold text-dark">
                    <i class="fas fa-clock me-2 text-info"></i>Transações Recentes
                  </h6>
                  <small class="text-muted">Histórico de movimentações</small>
                </div>
                <span class="badge bg-info-subtle text-info">{{ count($ultimasTransacoes) }} transações</span>
              </div>
              <div class="row g-2">
                @foreach($ultimasTransacoes as $row)
                @php
                $isPayment = isset($row->beneficiaryname);
                $data = isset($row->date) ? \Carbon\Carbon::parse($row->date) : \Carbon\Carbon::parse($row->date);
                $valor = isset($row->amount) ? $row->amount : $row->cash_out_liquido;
                @endphp
                <div class="col-12">
                  <div class="d-flex align-items-center justify-content-between p-2 border rounded">
                    <div class="d-flex align-items-center gap-2">
                      @if($isPayment)
                      <div class="bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fas fa-arrow-up text-warning" style="font-size: 12px;"></i>
                      </div>
                      @else
                      <div class="bg-success-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fas fa-arrow-down text-success" style="font-size: 12px;"></i>
                      </div>
                      @endif
                      <div>
                        @if($isPayment)
                        <div class="fw-semibold text-dark small">Pagamento realizado</div>
                        <div class="text-muted" style="font-size: 11px;">{{ $data->format('d/m H:i') }}</div>
                        @else
                        <div class="fw-semibold text-dark small">Pagamento recebido</div>
                        <div class="text-muted" style="font-size: 11px;">{{ $data->format('d/m H:i') }}</div>
                        @endif
                      </div>
                    </div>
                    <div class="text-end">
                      @if($isPayment)
                      <div class="fw-bold text-warning small">- R$ {{ number_format($valor, 0, ',', '.') }}</div>
                      <span class="badge bg-warning-subtle text-warning" style="font-size: 10px;">Débito</span>
                      @else
                      <div class="fw-bold text-success small">+ R$ {{ number_format($valor, 0, ',', '.') }}</div>
                      <span class="badge bg-success-subtle text-success" style="font-size: 10px;">Crédito</span>
                      @endif
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Status do Sistema -->
      <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
          <div class="card  shadow-sm h-100">
            <div class="card-body p-3 text-center">
              <div class="bg-success-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas fa-shield-halved text-success" style="font-size: 16px;"></i>
              </div>
              <h6 class="mb-1 fw-semibold text-dark">Segurança</h6>
              <p class="text-muted mb-0 small">Sistema protegido</p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="card  shadow-sm h-100">
            <div class="card-body p-3 text-center">
              <div class="bg-primary-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas fa-gear text-primary" style="font-size: 16px;"></i>
              </div>
              <h6 class="mb-1 fw-semibold text-dark">Configurações</h6>
              <p class="text-muted mb-0 small">Personalize sua conta</p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="card  shadow-sm h-100">
            <div class="card-body p-3 text-center">
              <div class="bg-info-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas fa-bell text-info" style="font-size: 16px;"></i>
              </div>
              <h6 class="mb-1 fw-semibold text-dark">Notificações</h6>
              <p class="text-muted mb-0 small">{{ rand(3, 12) }} novas</p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="card  shadow-sm h-100">
            <div class="card-body p-3 text-center">
              <div class="bg-warning-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas fa-headset text-warning" style="font-size: 16px;"></i>
              </div>
              <h6 class="mb-1 fw-semibold text-dark">Suporte</h6>
              <p class="text-muted mb-0 small">24/7 disponível</p>
            </div>
          </div>
        </div>
      </div>

      @endif
    </div>
  </div>

  <!-- Modal de período personalizado removido - usando filtros globais -->

  <script>
  document.addEventListener("DOMContentLoaded", function() {
    // JavaScript do período removido - usando filtros globais
    console.log('📊 Dashboard carregado - usando filtros globais');
  });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  
  <script>
  document.addEventListener("DOMContentLoaded", function() {
    // Verificar se o elemento existe antes de criar o gráfico
    const chartElement = document.querySelector("#faturamentoChart");
    if (!chartElement) {
      console.warn('Elemento #faturamentoChart não encontrado');
      return;
    }

    // Detectar tema atual
    const isDarkTheme = document.documentElement.getAttribute('data-theme') === 'dark';
    
    // Cores baseadas no tema
    const colors = {
      light: {
        background: '#ffffff',
        text: '#1e293b',
        textMuted: '#64748b',
        grid: '#e2e8f0',
        tooltipBg: '#ffffff',
        tooltipBorder: '#e2e8f0'
      },
      dark: {
        background: '#1a1a1a',
        text: '#e5e5e5',
        textMuted: '#a0aec0',
        grid: '#404040',
        tooltipBg: '#2d2d2d',
        tooltipBorder: '#404040'
      }
    };
    
    const currentColors = isDarkTheme ? colors.dark : colors.light;

    // Dados simulados para demonstração (entradas e saídas)
    const dates = @json($dates ?? []);
    const values = @json($values ?? []);
    
    // Garantir que sempre há pelo menos alguns pontos para o gráfico não ficar muito vazio
    let processedDates = dates;
    let processedValues = values;
    
    if (dates.length === 0) {
      // Se não há dados, mostrar pelo menos o dia atual
      const hoje = new Date().toISOString().split('T')[0];
      processedDates = [hoje];
      processedValues = [0];
    } else if (dates.length === 1) {
      // Se há apenas um dia, adicionar alguns dias anteriores para contexto
      const dataUnica = new Date(dates[0]);
      processedDates = [];
      processedValues = [];
      
      for (let i = 2; i >= 0; i--) {
        const data = new Date(dataUnica);
        data.setDate(data.getDate() - i);
        processedDates.push(data.toISOString().split('T')[0]);
        processedValues.push(i === 2 ? values[0] : 0);
      }
    }
    
    // Simular dados de saídas (para demonstração)
    const saidas = processedValues.map(val => Math.max(0, val * 0.3 + Math.random() * val * 0.2));

    // ApexCharts para Gráfico de Faturamento
    var options = {
      chart: {
        height: 280,
        type: 'bar',
        stacked: false,
        toolbar: {
          show: false
        },
        zoom: {
          enabled: false
        },
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 800
        },
        fontFamily: 'inherit',
        background: 'transparent'
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        show: false
      },
      series: [
        {
          name:"Entradas",
          data: processedValues
        },
        {
          name:"Saídas", 
          data: saidas
        }
      ],
      xaxis: {
        type: 'category',
        categories: processedDates,
        labels: {
          style: {
            colors: currentColors.textMuted,
            fontSize: '12px'
          },
          rotate: -45
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        }
      },
      yaxis: {
        labels: {
          formatter: function (val) {
            return"R$" + val.toLocaleString('pt-BR');
          },
          style: {
            colors: currentColors.textMuted,
            fontSize: '12px'
          }
        }
      },
      colors: ['#10b981', '#059669'],
      fill: {
        type: 'solid',
        opacity: 0.8
      },
      grid: {
        borderColor: currentColors.grid,
        strokeDashArray: 3,
        xaxis: {
          lines: {
            show: false
          }
        },
        yaxis: {
          lines: {
            show: true
          }
        },
        padding: {
          top: 10,
          right: 10,
          bottom: 10,
          left: 10
        }
      },
      tooltip: {
        enabled: true,
        shared: true,
        followCursor: true,
        intersect: false,
        inverseOrder: false,
        custom: function({series, seriesIndex, dataPointIndex, w}) {
          const entradas = series[0][dataPointIndex];
          const saidas = series[1][dataPointIndex];
          const date = w.globals.labels[dataPointIndex];
          
          return `
            <div class="faturamento-tooltip">
              <div class="faturamento-tooltip-date">${date}</div>
              <div class="faturamento-tooltip-value faturamento-tooltip-entradas">
                <span class="faturamento-tooltip-dot faturamento-tooltip-dot-entradas"></span>
                Entradas: R$ ${entradas.toLocaleString('pt-BR', {minimumFractionDigits: 2})}
              </div>
              <div class="faturamento-tooltip-value faturamento-tooltip-saidas">
                <span class="faturamento-tooltip-dot faturamento-tooltip-dot-saidas"></span>
                Saídas: R$ ${saidas.toLocaleString('pt-BR', {minimumFractionDigits: 2})}
              </div>
            </div>
          `;
        }
      },
      legend: {
        show: false
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          columnWidth: processedDates.length <= 7 ? '40%' : '60%',
          barHeight: '70%'
        }
      },
      responsive: [{
        breakpoint: 768,
        options: {
          chart: {
            height: 300
          },
          xaxis: {
            labels: {
              rotate: -45
            }
          }
        }
      }]
    };

    try {
      var chart = new ApexCharts(chartElement, options);
      chart.render();
      
      // Adicionar legenda personalizada
      const legendHtml = `
        <div class="faturamento-legend">
          <div class="faturamento-legend-item">
            <span class="faturamento-legend-dot faturamento-legend-dot-entradas"></span>
            Entradas
          </div>
          <div class="faturamento-legend-item">
            <span class="faturamento-legend-dot faturamento-legend-dot-saidas"></span>
            Saídas
          </div>
        </div>
      `;
      
      chartElement.insertAdjacentHTML('afterend', legendHtml);
      
    } catch (error) {
      console.error('Erro ao renderizar gráfico de faturamento:', error);
    }
  });
  </script>

  <script>
  function setHidden(buttonId, labelId, value) {
    const btn = document.getElementById(buttonId);
    if (!btn) return;

    const lbl = document.getElementById(labelId);
    const hiddenKey = `hidden-${buttonId}`;

    const isCurrentlyHidden = localStorage.getItem(hiddenKey) === 'true';

    const currentIcon = btn.querySelector('i');
    if (currentIcon) {
      currentIcon.remove();
    }

    const newIcon = document.createElement('i');

    if (!isCurrentlyHidden) {
      newIcon.className = 'fas fa-eye-slash';
      lbl.innerText = '---';
      localStorage.setItem(hiddenKey, 'true');
    } else {
      newIcon.className = 'fas fa-eye';
      lbl.innerText = value;
      localStorage.setItem(hiddenKey, 'false');
    }

    btn.appendChild(newIcon);
  }

  function restoreVisibility(buttonId, labelId, value) {
    const btn = document.getElementById(buttonId);
    if (!btn) return;

    const lbl = document.getElementById(labelId);
    const hiddenKey = `hidden-${buttonId}`;
    const isHidden = localStorage.getItem(hiddenKey) === 'true';

    const currentIcon = btn.querySelector('i');
    if (currentIcon) {
      currentIcon.remove();
    }

    const newIcon = document.createElement('i');

    if (isHidden) {
      newIcon.className = 'fas fa-eye-slash';
      lbl.innerText = '---';
    } else {
      newIcon.className = 'fas fa-eye';
      lbl.innerText = value;
    }

    btn.appendChild(newIcon);
  }

  document.addEventListener('DOMContentLoaded', function() {
    restoreVisibility('vis-abandono', 'label-abandono', '0%');
    restoreVisibility('vis-reembolso', 'label-reembolso', '0%');
    restoreVisibility('vis-chargeback', 'label-chargeback', '0%');
    restoreVisibility('vis-med', 'label-med', '0%');
  });
  </script>
</x-app-layout>

