<x-app-layout :route="'[ADMIN] Transações'">
  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-exchange-alt fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Transações Financeiras</h1>
                  <p class="text-muted mb-0">Gerenciamento completo de transações</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cards de Estatísticas -->
      <div class="row g-3 mb-4">
        <!-- Transações Aprovadas -->
        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-primary flex-shrink-0">
                  <i class="fa-solid fa-sync"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Transações Aprovadas</p>
                  <h4 class="fw-bold mb-0 text-primary text-truncate" style="font-size: 1.35rem;">{{ $transacoes_aprovadas }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Lucro Líquido Hoje -->
        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-dollar-sign"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Lucro Líquido (Hoje)</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($lucro_liquido_hoje ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Lucro Líquido Mês -->
        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Lucro Líquido (Mês)</p>
                  <h4 class="fw-bold mb-0 text-info text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($lucro_liquido_mes ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Lucro Líquido Total -->
        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-warning flex-shrink-0">
                  <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Lucro Líquido (Total)</p>
                  <h4 class="fw-bold mb-0 text-warning text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($lucro_liquido_total ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabela de Transações -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="fa-solid fa-table text-primary me-2"></i>
                Lista de Transações
              </h5>
            </div>
            <div class="card-body p-4">
              <!-- Filtros -->
              <div class="mb-4">
                <form method="GET" action="{{ route('admin.financeiro.transacoes') }}" id="filtersForm">
                  <div class="row g-3 align-items-end">
                    <!-- Campo de Busca -->
                    <div class="col-lg-2 col-md-6">
                      <label class="form-label mb-2">
                        <i class="fa-solid fa-search text-primary me-1"></i>Pesquisar
                      </label>
                      <input type="text" class="form-control" name="search"
                        placeholder="Buscar..."
                        value="{{ $searchFilter }}">
                    </div>

                    <!-- Filtro por Status -->
                    <div class="col-lg-2 col-md-6">
                      <label class="form-label mb-2">
                        <i class="fa-solid fa-circle-check text-success me-1"></i>Status
                      </label>
                      <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="PAID_OUT" {{ $statusFilter == 'PAID_OUT' ? 'selected' : '' }}>Aprovado</option>
                        <option value="WAITING_FOR_APPROVAL" {{ $statusFilter == 'WAITING_FOR_APPROVAL' ? 'selected' : '' }}>Pendente</option>
                        <option value="RELEASE" {{ $statusFilter == 'RELEASE' ? 'selected' : '' }}>A Liberar</option>
                        <option value="CANCELLED" {{ $statusFilter == 'CANCELLED' ? 'selected' : '' }}>Cancelado</option>
                        <option value="MEDIATION" {{ $statusFilter == 'MEDIATION' ? 'selected' : '' }}>Mediação</option>
                      </select>
                    </div>

                    <!-- Filtro por Meio -->
                    <div class="col-lg-2 col-md-6">
                      <label class="form-label mb-2">
                        <i class="fa-solid fa-credit-card text-warning me-1"></i>Meio
                      </label>
                      <select class="form-select" name="method">
                        <option value="">Todos</option>
                        <option value="pix" {{ $methodFilter == 'pix' ? 'selected' : '' }}>PIX</option>
                        <option value="card" {{ $methodFilter == 'card' ? 'selected' : '' }}>Cartão</option>
                        <option value="billet" {{ $methodFilter == 'billet' ? 'selected' : '' }}>Boleto</option>
                        <option value="med_chargeback" {{ $methodFilter == 'med_chargeback' ? 'selected' : '' }}>MED/CB</option>
                      </select>
                    </div>

                    <!-- Filtro por Período -->
                    <div class="col-lg-2 col-md-6">
                      <label class="form-label mb-2">
                        <i class="fa-solid fa-calendar text-info me-1"></i>Período
                      </label>
                      <select class="form-select" name="period">
                        <option value="">Todos</option>
                        <option value="today" {{ $periodFilter == 'today' ? 'selected' : '' }}>Hoje</option>
                        <option value="week" {{ $periodFilter == 'week' ? 'selected' : '' }}>Semana</option>
                        <option value="month" {{ $periodFilter == 'month' ? 'selected' : '' }}>Mês</option>
                        <option value="year" {{ $periodFilter == 'year' ? 'selected' : '' }}>Ano</option>
                      </select>
                    </div>

                    <!-- Botões -->
                    <div class="col-lg-4 col-md-12">
                      <div class="d-flex gap-2 justify-content-md-start justify-content-lg-end">
                        <button type="submit" class="btn btn-primary">
                          <i class="fas fa-filter me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('admin.financeiro.transacoes') }}" class="btn btn-outline-secondary">
                          <i class="fas fa-times me-1"></i>Limpar
                        </a>
                        <a href="{{ route('admin.financeiro.transacoes.export', request()->query()) }}" class="btn btn-success">
                          <i class="fas fa-file-csv me-1"></i>CSV
                        </a>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <!-- Tabela -->
              <div class="table-responsive">
                <table id="table-transacoes" class="table text-nowrap table-hover">
                  <thead>
                    <tr>
                      <th class="text-center"><i class="fa-solid fa-wallet text-primary me-1"></i>Meio</th>
                      <th class="text-center"><i class="fa-solid fa-user text-info me-1"></i>Cliente</th>
                      <th class="text-center"><i class="fa-solid fa-hashtag text-warning me-1"></i>ID Transação</th>
                      <th class="text-center"><i class="fa-solid fa-dollar-sign text-success me-1"></i>Valor Total</th>
                      <th class="text-center"><i class="fa-solid fa-coins text-success me-1"></i>Valor Líquido</th>
                      <th class="text-center"><i class="fa-solid fa-circle-check text-success me-1"></i>Status</th>
                      <th class="text-center"><i class="fa-solid fa-calendar text-secondary me-1"></i>Data</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($deposits as $row)
                    @php
                    // Ajustar exibição do status
                    switch ($row->status) {
                    case 'PAID_OUT':
                    $statusBadge = 'success';
                    $statusText = 'Aprovado';
                    $statusIcon = 'check-circle';
                    break;
                    case 'WAITING_FOR_APPROVAL':
                    case 'PENDING':
                    $statusBadge = 'warning';
                    $statusText = 'Pendente';
                    $statusIcon = 'clock';
                    break;
                    case 'RELEASE':
                    $statusBadge = 'info';
                    $statusText = 'A Liberar';
                    $statusIcon = 'hourglass-half';
                    break;
                    case 'CANCELLED':
                    $statusBadge = 'danger';
                    $statusText = 'Cancelado';
                    $statusIcon = 'times-circle';
                    break;
                    case 'MEDIATION':
                    $statusBadge = 'warning';
                    $statusText = 'Mediação';
                    $statusIcon = 'gavel';
                    break;
                    default:
                    $statusBadge = 'secondary';
                    $statusText = $row->status;
                    $statusIcon = 'circle';
                    }
                    @endphp
                    <tr class="transaction-row" style="cursor: pointer;"
                      data-transaction-id="{{ $row->id }}"
                      title="Clique para ver detalhes">
                      <!-- Meio -->
                      <td class="text-center">
                        @if(isset($row->method))
                        @switch($row->method)
                        @case('pix')
                        <i class="fa-brands fa-pix text-success" title="PIX"></i>
                        @break
                        @case('billet')
                        <i class="fa-solid fa-barcode text-info" title="Boleto"></i>
                        @break
                        @case('card')
                        <i class="fa-solid fa-credit-card text-warning" title="Cartão"></i>
                        @break
                        @case('med_chargeback')
                        <i class="fa-solid fa-exclamation-triangle text-danger" title="MED/CHARGEBACK"></i>
                        @break
                        @endswitch
                        @else
                        <span class="text-muted">--</span>
                        @endif
                      </td>

                      <!-- Cliente ID -->
                      <td class="text-center fw-medium">{{ $row->user_id }}</td>

                      <!-- Transação ID -->
                      <td class="text-center font-monospace small">{{ $row->idTransaction }}</td>

                      <!-- Valor Total -->
                      <td class="text-center fw-semibold text-success">R$ {{ number_format($row->amount, 2, ',', '.') }}</td>

                      <!-- Valor Líquido -->
                      <td class="text-center fw-semibold text-success">R$ {{ number_format($row->deposito_liquido, 2, ',', '.') }}</td>

                      <!-- Status -->
                      <td class="text-center">
                        @if($statusText =="A Liberar")
                        <span class="badge bg-{{ $statusBadge }} {{ $statusBadge === 'warning' ? 'text-dark' : '' }}"
                          data-bs-toggle="popover"
                          data-bs-trigger="hover focus"
                          data-bs-content="Será liberado em {{ \Carbon\Carbon::parse($row->date)->addDays($row->days_availability ?? 21)->format('d/m/Y H:i') }}"
                          data-bs-placement="top">
                          <i class="fa-solid fa-{{ $statusIcon }} me-1"></i>{{ $statusText }}
                        </span>
                        @else
                        <span class="badge bg-{{ $statusBadge }} {{ $statusBadge === 'warning' ? 'text-dark' : '' }}">
                          <i class="fa-solid fa-{{ $statusIcon }} me-1"></i>{{ $statusText }}
                        </span>
                        @endif
                      </td>

                      <!-- Data -->
                      <td class="text-center small">{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="7" class="text-center py-5">
                        <div class="text-muted">
                          <i class="fa-solid fa-inbox fa-3x mb-3 d-block"></i>
                          <h5>Nenhuma transação encontrada</h5>
                          <p class="mb-0">Não há transações que correspondam aos filtros aplicados.</p>
                        </div>
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Editar (Comentado - não utilizado) -->
      <!-- <div class="modal fade" id="editModal">...</div> -->

      <!-- Modal Excluir (Comentado - não utilizado) -->
      <!-- <div class="modal fade" id="deleteModal">...</div> -->

      <!-- JavaScript -->
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          // ==================== DATATABLE ====================
          const table = document.getElementById('table-transacoes');
          if (table) {
            const tbody = table.querySelector('tbody');
            const dataRows = tbody ? tbody.querySelectorAll('tr.transaction-row') : [];
            const hasData = dataRows.length > 0;

            if (hasData) {
              $('#table-transacoes').DataTable({
                responsive: true,
                info: false,
                ordering: false,
                lengthChange: false,
                pageLength: 25,
                language: {
                  url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                },
                dom: 'rt<"bottom"p><"clear">',
                columnDefs: [{
                    targets: '_all',
                    className: 'text-center'
                  },
                  {
                    targets: [0, 1, 2, 3, 4, 5, 6],
                    className: 'text-center'
                  }
                ],
                initComplete: function() {
                  $('#table-transacoes_filter input[type="search"]')
                    .attr('placeholder', 'Pesquisar transações...');
                }
              });
            } else {
              // Se não há dados, apenas aplicar estilos básicos
              console.log('Nenhum dado encontrado para inicializar DataTable');
            }
          }

          // ==================== FILTROS ====================
          // Auto-submit ao mudar selects
          document.querySelectorAll('select[name="status"], select[name="method"], select[name="period"]').forEach(function(select) {
            select.addEventListener('change', function() {
              document.getElementById('filtersForm').submit();
            });
          });

          // Auto-submit da busca com delay
          let searchTimeout;
          const searchInput = document.querySelector('input[name="search"]');
          if (searchInput) {
            searchInput.addEventListener('input', function() {
              clearTimeout(searchTimeout);
              searchTimeout = setTimeout(function() {
                document.getElementById('filtersForm').submit();
              }, 800);
            });
          }

          // ==================== TOOLTIPS & POPOVERS ====================
          const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
          tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
          });

          const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
          popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
          });

          // ==================== CLICK NA LINHA DA TABELA ====================
          $('#table-transacoes tbody').on('click', 'tr.transaction-row', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const transactionId = $(this).data('transaction-id');
            if (transactionId) {
              window.carregarDetalhesTransacao(transactionId);
            }
          });

          // ==================== CARREGAR DETALHES DA TRANSAÇÃO ====================
          window.carregarDetalhesTransacao = function(transactionId) {
            $.ajax({
              url: '/admin/financeiro/entradas/detalhes/' + transactionId,
              method: 'GET',
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                if (response.success) {
                  preencherModal(response.transaction);
                  $('#detalhesModal').modal('show');
                } else {
                  alert('Erro ao carregar detalhes: ' + (response.message || 'Erro desconhecido'));
                }
              },
              error: function(xhr) {
                console.error('Erro ao carregar detalhes:', xhr);
                alert('Erro ao carregar detalhes da transação');
              }
            });
          }

          // ==================== PREENCHER MODAL ====================
          window.preencherModal = function(dados) {
            console.log('Dados recebidos:', dados);

            // Barra de resumo
            $('#modal-valor').text('R$ ' + parseFloat(dados.amount).toLocaleString('pt-BR', {
              minimumFractionDigits: 2
            }));
            $('#modal-produto').text('Depósito');
            $('#modal-cliente').text(dados.client_name || 'N/A');
            $('#modal-metodo').text(dados.method === 'pix' ? 'PIX' : dados.method === 'card' ? 'Cartão' : 'Boleto');

            // Ícone do método
            if (dados.method === 'pix') {
              $('#modal-icone-metodo').attr('class', 'fa fa-brands fa-pix me-2').css('color', '#00a782');
            } else if (dados.method === 'card') {
              $('#modal-icone-metodo').attr('class', 'fa fa-credit-card me-2').css('color', '#ff9a02');
            } else if (dados.method === 'billet') {
              $('#modal-icone-metodo').attr('class', 'fa fa-barcode me-2').css('color', '#17a2b8');
            }

            // Status
            const statusText = window.getStatusText(dados.status);
            const statusClass = window.getStatusClass(dados.status);
            $('#modal-status').text(statusText).attr('class', 'badge ' + statusClass);
            $('#modal-status-historico').text(statusText).attr('class', 'badge me-2 ' + statusClass);

            // Dados da transação
            $('#modal-id').text(dados.idTransaction);
            $('#modal-valor-detalhes').text('R$ ' + parseFloat(dados.amount).toLocaleString('pt-BR', {
              minimumFractionDigits: 2
            }));
            $('#modal-taxa').text('R$ ' + (parseFloat(dados.amount) - parseFloat(dados.deposito_liquido)).toLocaleString('pt-BR', {
              minimumFractionDigits: 2
            }));
            $('#modal-valor-liquido').text('R$ ' + parseFloat(dados.deposito_liquido).toLocaleString('pt-BR', {
              minimumFractionDigits: 2
            }));
            $('#modal-data-cadastro').text(new Date(dados.date).toLocaleString('pt-BR'));
            $('#modal-empresa').text('EXEMPLO');
            $('#modal-data-historico').text(new Date(dados.date).toLocaleString('pt-BR'));
            $('#modal-tid').text(dados.idTransaction);
            $('#modal-assinatura-status').text('ATIVA');
            $('#modal-assinatura-data').text(new Date(dados.date).toLocaleString('pt-BR'));

            // Dados do pagamento
            if (dados.method === 'card') {
              const cardNumber = dados.card_number || '**** **** **** ****';
              const cardBrand = window.getCardBrand(cardNumber);

              $('#modal-dados-pagamento .d-flex.align-items-center.mb-2').html(`
                <div style="width: 40px; height: 25px; background: ${cardBrand.color}; border-radius: 4px; margin-right: 10px; display: flex; align-items: center; justify-content: center;">
                  <span style="color: white; font-size: 10px; font-weight: bold;">${cardBrand.name}</span>
                </div>
                <span id="modal-cartao-numero">${cardNumber}</span>
              `);
              $('#modal-cartao-nome').text(dados.client_name || 'Nome do Portador');
              $('#modal-cartao-validade').text(dados.card_expiry || 'válido até 12/25');
            } else if (dados.method === 'pix') {
              $('#modal-dados-pagamento .d-flex.align-items-center.mb-2').html(`
                <div style="width: 40px; height: 25px; background: #00a782; border-radius: 4px; margin-right: 10px; display: flex; align-items: center; justify-content: center;">
                  <i class="fa fa-brands fa-pix" style="color: white; font-size: 12px;"></i>
                </div>
                <span>PIX Instantâneo</span>
              `);
              $('#modal-cartao-nome').text('Pagamento via PIX');
              $('#modal-cartao-validade').text('Processamento instantâneo');
            } else if (dados.method === 'billet') {
              $('#modal-dados-pagamento .d-flex.align-items-center.mb-2').html(`
                <div style="width: 40px; height: 25px; background: #17a2b8; border-radius: 4px; margin-right: 10px; display: flex; align-items: center; justify-content: center;">
                  <i class="fa fa-barcode" style="color: white; font-size: 12px;"></i>
                </div>
                <span>Boleto Bancário</span>
              `);
              $('#modal-cartao-nome').text('Pagamento via Boleto');
              $('#modal-cartao-validade').text('Vencimento em 3 dias');
            }

            // Botões de ação
            $('#estornarBtn').data('transaction-id', dados.id).toggle(dados.status === 'PAID_OUT');
            $('#mediacaoBtn').data('transaction-id', dados.id).toggle(dados.status === 'PAID_OUT');
            $('#reverterMediacaoBtn').data('transaction-id', dados.id).toggle(dados.status === 'MEDIATION');
          }

          // ==================== EVENTOS BOTÕES DE AÇÃO ====================
          $(document).on('click', '#mediacaoBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const transactionId = $(this).data('transaction-id');
            if (!transactionId) {
              alert('Erro: ID da transação não encontrado');
              return;
            }

            if (confirm('Tem certeza que deseja enviar esta transação para mediação?')) {
              executarMediacao(transactionId);
            }
          });

          $(document).on('click', '#reverterMediacaoBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const transactionId = $(this).data('transaction-id');
            if (!transactionId) {
              alert('Erro: ID da transação não encontrado');
              return;
            }

            if (confirm('Tem certeza que deseja reverter a mediação desta transação? O valor será liberado de volta ao cliente.')) {
              executarReversaoMediacao(transactionId);
            }
          });

          $(document).on('click', '#estornarBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const transactionId = $(this).data('transaction-id');
            if (confirm('Tem certeza que deseja estornar esta transação?')) {
              executarEstorno(transactionId);
            }
          });

          // ==================== FUNÇÕES DE AÇÃO ====================
          function executarMediacao(transactionId) {
            $.ajax({
              url: '/admin/financeiro/entradas/mediacao/' + transactionId,
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                alert('Transação enviada para mediação com sucesso!');
                $('#detalhesModal').modal('hide');
                location.reload();
              },
              error: function(xhr) {
                console.error('Erro ao enviar para mediação:', xhr);
                alert('Erro ao enviar transação para mediação');
              }
            });
          }

          function executarReversaoMediacao(transactionId) {
            $.ajax({
              url: '/admin/financeiro/entradas/reverter-mediacao/' + transactionId,
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                alert('Mediação revertida com sucesso! O valor foi liberado de volta ao cliente.');
                $('#detalhesModal').modal('hide');
                location.reload();
              },
              error: function(xhr) {
                console.error('Erro ao reverter mediação:', xhr);
                alert('Erro ao reverter mediação da transação');
              }
            });
          }

          function executarEstorno(transactionId) {
            $.ajax({
              url: '/admin/financeiro/entradas/estornar/' + transactionId,
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                alert('Transação estornada com sucesso!');
                $('#detalhesModal').modal('hide');
                location.reload();
              },
              error: function(xhr) {
                console.error('Erro ao estornar transação:', xhr);
                alert('Erro ao estornar transação');
              }
            });
          }

          // ==================== FUNÇÕES AUXILIARES ====================
          window.getStatusText = function(status) {
            const statusMap = {
              'PAID_OUT': 'APROVADO',
              'WAITING_FOR_APPROVAL': 'PENDENTE',
              'PENDING': 'PENDENTE',
              'RELEASE': 'A LIBERAR',
              'MEDIATION': 'EM MEDIAÇÃO',
              'CANCELLED': 'CANCELADO'
            };
            return statusMap[status] || status;
          }

          window.getStatusClass = function(status) {
            const classMap = {
              'PAID_OUT': 'bg-success',
              'WAITING_FOR_APPROVAL': 'bg-warning',
              'PENDING': 'bg-warning',
              'RELEASE': 'bg-info',
              'MEDIATION': 'bg-warning',
              'CANCELLED': 'bg-danger'
            };
            return classMap[status] || 'bg-secondary';
          }

          window.getCardBrand = function(cardNumber) {
            const cleanNumber = cardNumber.replace(/\D/g, '');

            if (cleanNumber.startsWith('4')) {
              return {
                name: 'VISA',
                color: '#1A1F71'
              };
            } else if (cleanNumber.startsWith('5') || cleanNumber.startsWith('2')) {
              return {
                name: 'Mastercard',
                color: '#EB001B'
              };
            } else if (cleanNumber.startsWith('3')) {
              return {
                name: 'American Express',
                color: '#006FCF'
              };
            } else if (cleanNumber.startsWith('6')) {
              return {
                name: 'ELO',
                color: '#FFD700'
              };
            } else {
              return {
                name: 'Cartão',
                color: '#6c757d'
              };
            }
          }
        });
      </script>

    </div>
  </div>

  <!-- Modal Detalhes da Transação -->
  <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold" id="detalhesModalLabel">
            <i class="fas fa-receipt me-2"></i>Detalhes da Transação
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body bg-light">
          <!-- Cards de Resumo -->
          <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
              <div class="card shadow-sm bg-success text-white h-100">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                      <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-dollar-sign fa-lg"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <small class="d-block opacity-75">VALOR TOTAL</small>
                      <h4 class="mb-0 fw-bold" id="modal-valor">R$ 0,00</h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="card shadow-sm bg-primary text-white h-100">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                      <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-wallet fa-lg"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <small class="d-block opacity-75">VALOR LÍQUIDO</small>
                      <h4 class="mb-0 fw-bold" id="modal-valor-liquido">R$ 0,00</h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="card shadow-sm bg-warning text-white h-100">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                      <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-check-circle fa-lg"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <small class="d-block opacity-75">STATUS</small>
                      <h5 class="mb-0 fw-bold" id="modal-status">Aprovado</h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="card shadow-sm bg-info text-white h-100">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                      <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i id="modal-icone-metodo" class="fas fa-credit-card fa-lg"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <small class="d-block opacity-75">MÉTODO</small>
                      <h6 class="mb-0 fw-bold" id="modal-metodo">Cartão</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Informações do Cliente -->
          <div class="card shadow-sm mb-3">
            <div class="card-header bg-white border-bottom">
              <h6 class="mb-0 fw-bold">
                <i class="fas fa-user-circle me-2 text-primary"></i>Informações do Cliente
              </h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-user text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">NOME</small>
                      <span class="fw-semibold" id="modal-cliente">N/A</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-box text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">PRODUTO</small>
                      <span class="fw-semibold" id="modal-produto">Depósito</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-hashtag text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">ID TRANSAÇÃO</small>
                      <span class="fw-semibold" id="modal-id">N/A</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-building text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">EMPRESA</small>
                      <span class="fw-semibold" id="modal-empresa">EXEMPLO</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row g-3">
            <!-- Dados da Transação -->
            <div class="col-md-6">
              <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                  <h6 class="mb-0 fw-bold">
                    <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Dados da Transação
                  </h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <div class="d-flex align-items-center p-3 bg-light rounded mb-2">
                      <i class="fas fa-dollar-sign text-success me-3"></i>
                      <div>
                        <small class="text-muted d-block">VALOR</small>
                        <span class="fw-semibold" id="modal-valor-detalhes">R$ 0,00</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded mb-2">
                      <i class="fas fa-percent text-warning me-3"></i>
                      <div>
                        <small class="text-muted d-block">TAXA</small>
                        <span class="fw-semibold" id="modal-taxa">R$ 0,00</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded mb-2">
                      <i class="fas fa-calendar text-info me-3"></i>
                      <div>
                        <small class="text-muted d-block">DATA CADASTRO</small>
                        <span class="fw-semibold" id="modal-data-cadastro">-</span>
                      </div>
                    </div>
                  </div>

                  <h6 class="fw-bold mb-3">
                    <i class="fas fa-university me-2 text-primary"></i>Adquirente
                  </h6>
                  <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                    <i class="fas fa-hashtag text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">ID TID</small>
                      <span class="fw-semibold" id="modal-tid">-</span>
                    </div>
                  </div>

                  <h6 class="fw-bold mb-3">
                    <i class="fas fa-file-signature me-2 text-primary"></i>Assinatura
                  </h6>
                  <div class="d-flex align-items-center p-3 bg-light rounded mb-2">
                    <i class="fas fa-circle-check text-success me-3"></i>
                    <div>
                      <small class="text-muted d-block">STATUS</small>
                      <span class="badge bg-success" id="modal-assinatura-status">ATIVA</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-calendar text-info me-3"></i>
                    <div>
                      <small class="text-muted d-block">DATA CADASTRO</small>
                      <span class="fw-semibold" id="modal-assinatura-data">-</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Histórico e Dados do Pagamento -->
            <div class="col-md-6">
              <!-- Histórico -->
              <div class="card shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                  <h6 class="mb-0 fw-bold">
                    <i class="fas fa-history me-2 text-primary"></i>Histórico
                  </h6>
                </div>
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <span id="modal-status-historico" class="badge bg-success me-2">APROVADA</span>
                  </div>
                  <div class="small text-muted">
                    <div class="mb-1" id="modal-data-historico">06/09/2025 20:05</div>
                    <div class="mb-1">00 APROVADA</div>
                    <div>SISTEMA</div>
                  </div>
                </div>
              </div>

              <!-- Dados do Pagamento -->
              <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                  <h6 class="mb-0 fw-bold">
                    <i class="fas fa-credit-card me-2 text-primary"></i>Dados do Pagamento
                  </h6>
                </div>
                <div class="card-body">
                  <div id="modal-dados-pagamento">
                    <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                      <div style="width: 50px; height: 32px; background: linear-gradient(45deg, #1e3c72, #2a5298); border-radius: 6px; margin-right: 12px; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 11px; font-weight: bold;">VISA</span>
                      </div>
                      <span id="modal-cartao-numero" class="fw-semibold">**** **** **** ****</span>
                    </div>
                    <div class="text-muted small">
                      <div class="mb-1" id="modal-cartao-nome">Nome do Portador</div>
                      <div id="modal-cartao-validade">válido até 12/25</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Fechar
          </button>
          <button type="button" class="btn btn-warning" id="mediacaoBtn">
            <i class="fas fa-gavel me-1"></i>Mediação
          </button>
          <button type="button" class="btn btn-success" id="reverterMediacaoBtn">
            <i class="fas fa-undo me-1"></i>Reverter Mediação
          </button>
          <button type="button" class="btn btn-danger" id="estornarBtn">
            <i class="fas fa-undo me-1"></i>Estornar
          </button>
        </div>
      </div>
    </div>
  </div>

</x-app-layout>