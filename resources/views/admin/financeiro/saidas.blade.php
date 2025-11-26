<x-app-layout :route="'[ADMIN] Saídas'">

  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-arrow-up fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Relatórios de Saídas</h1>
                  <p class="text-muted mb-0">Gerencie e monitore todas as transações de saída do sistema</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Cards de Estatísticas -->
      <div class="row g-3 mb-4">
        <!-- Aprovadas Total -->
        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-check"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Aprovadas (Total)</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">{{ $totalaprovadas }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Aprovadas Hoje -->
        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-check-circle"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Aprovadas (Hoje)</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">{{ $totalaprovadasHoje }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Aprovadas Mês -->
        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Aprovadas (Mês)</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">{{ $totalaprovadasMes }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Transações Geral -->
        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-warning flex-shrink-0">
                  <i class="fa-solid fa-sync"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Transações Geral</p>
                  <h4 class="fw-bold mb-0 text-warning text-truncate" style="font-size: 1.35rem;">{{ $totalsolicitacoes }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cards de Valores -->
      <div class="row g-3 mb-4">
        <!-- Valor Total Aprovado -->
        <div class="col-xxl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-primary flex-shrink-0">
                  <i class="fa-solid fa-dollar-sign"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Valor Aprovado (Total)</p>
                  <h4 class="fw-bold mb-0 text-primary text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($valorAprovadoTotal, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Valor Hoje -->
        <div class="col-xxl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fa-solid fa-coins"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Valor Aprovado (Hoje)</p>
                  <h4 class="fw-bold mb-0 text-info text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($valorAprovadoHoje, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Valor Mês -->
        <div class="col-xxl-4 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern flex-shrink-0" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;">
                  <i class="fa-solid fa-chart-bar"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Valor Aprovado (Mês)</p>
                  <h4 class="fw-bold mb-0 text-secondary text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($valorAprovadoMes, 2, ',', '.') }}</h4>
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
                Relatório de Saídas
              </h5>
            </div>
            <div class="card-body p-4">
              <!-- Filtros -->
              <div class="mb-4">
                <form method="GET" action="{{ route('admin.financeiro.saidas') }}" id="filtersForm">
                  <div class="row g-3 align-items-end">
                    <!-- Campo de Busca -->
                    <div class="col-lg-3 col-md-6">
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
                        <option value="COMPLETED" {{ $statusFilter == 'COMPLETED' ? 'selected' : '' }}>Aprovado</option>
                        <option value="PAID_OUT" {{ $statusFilter == 'PAID_OUT' ? 'selected' : '' }}>Pago</option>
                        <option value="PENDING" {{ $statusFilter == 'PENDING' ? 'selected' : '' }}>Pendente</option>
                        <option value="REJECTED" {{ $statusFilter == 'REJECTED' ? 'selected' : '' }}>Rejeitado</option>
                        <option value="CANCELLED" {{ $statusFilter == 'CANCELLED' ? 'selected' : '' }}>Cancelado</option>
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
                    <div class="col-lg-5 col-md-12">
                      <div class="d-flex gap-2 justify-content-md-start justify-content-lg-end">
                        <button type="submit" class="btn btn-primary">
                          <i class="fas fa-filter me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('admin.financeiro.saidas') }}" class="btn btn-outline-secondary">
                          <i class="fas fa-times me-1"></i>Limpar
                        </a>
                        <a href="{{ route('admin.financeiro.saidas.export', request()->query()) }}" class="btn btn-success">
                          <i class="fas fa-file-csv me-1"></i>CSV
                        </a>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <!-- Tabela -->
              <div class="table-responsive">
                <table id="table-financeiro-saidas" class="table text-nowrap table-hover">
                  <thead>
                    <tr>
                      <th class="text-center"><i class="fa-solid fa-user text-primary me-1"></i>User ID</th>
                      <th class="text-center"><i class="fa-solid fa-hashtag text-secondary me-1"></i>Transação ID</th>
                      <th class="text-center"><i class="fa-solid fa-dollar-sign text-success me-1"></i>Valor</th>
                      <th class="text-center"><i class="fa-solid fa-coins text-success me-1"></i>Valor Líquido</th>
                      <th class="text-center"><i class="fa-solid fa-circle-check text-info me-1"></i>Status</th>
                      <th class="text-center"><i class="fa-solid fa-user-circle text-primary me-1"></i>Nome</th>
                      <th class="text-center"><i class="fa-brands fa-pix text-success me-1"></i>Chave PIX</th>
                      <th class="text-center"><i class="fa-solid fa-id-card text-warning me-1"></i>Documento</th>
                      <th class="text-center"><i class="fa-solid fa-calendar text-info me-1"></i>Data</th>
                      <th class="text-center"><i class="fa-solid fa-percentage text-danger me-1"></i>Taxa</th>
                      <th class="text-center"><i class="fa-solid fa-server text-secondary me-1"></i>Resposta</th>
                      <th class="text-center"><i class="fa-solid fa-cog text-dark me-1"></i>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($cashOuts as $cashOut)
                    <tr class="transaction-row" style="cursor: pointer;" data-transaction-id="{{ $cashOut->id }}" title="Clique para ver detalhes">
                      <td class="text-center fw-medium">{{ $cashOut->user_id }}</td>
                      <td class="text-center"><span class="font-monospace small">{{ $cashOut->externalreference }}</span></td>
                      <td class="text-center fw-semibold text-success">R$ {{ number_format($cashOut->amount, 2, ',', '.') }}</td>
                      <td class="text-center fw-semibold text-info">R$ {{ number_format($cashOut->cash_out_liquido, 2, ',', '.') }}</td>
                      <td class="text-center">
                        @switch($cashOut->status)
                          @case('COMPLETED')
                          @case('PAID_OUT')
                            <span class="badge bg-success">
                              <i class="fa-solid fa-check-circle me-1"></i>Aprovado
                            </span>
                            @break
                          @case('PENDING')
                            <span class="badge bg-warning">
                              <i class="fa-solid fa-clock me-1"></i>Pendente
                            </span>
                            @break
                          @case('REJECTED')
                            <span class="badge bg-danger">
                              <i class="fa-solid fa-times-circle me-1"></i>Rejeitado
                            </span>
                            @break
                          @case('CANCELLED')
                          @case('CANCELED')
                            <span class="badge bg-danger">
                              <i class="fa-solid fa-ban me-1"></i>Cancelado
                            </span>
                            @break
                          @default
                            <span class="badge bg-secondary">{{ $cashOut->status }}</span>
                        @endswitch
                      </td>
                      <td class="text-center">{{ $cashOut->beneficiaryname }}</td>
                      <td class="text-center font-monospace small">{{ $cashOut->pixkey }}</td>
                      <td class="text-center font-monospace">{{ $cashOut->beneficiarydocument }}</td>
                      <td class="text-center">{{ \Carbon\Carbon::parse($cashOut->date)->format('d/m/Y H:i') }}</td>
                      <td class="text-center fw-semibold text-danger">R$ {{ number_format((float)$cashOut->amount - (float)$cashOut->cash_out_liquido, 2, ',', '.') }}</td>
                      <td class="text-center"><small>{{ Str::limit($cashOut->descricao_externa, 50) }}</small></td>
                      <td class="text-center">
                        <select class="form-select form-select-sm status-select" data-id="{{ $cashOut->id }}" data-type="saida">
                          <option value="COMPLETED" {{ $cashOut->status == 'COMPLETED' ? 'selected' : '' }}>Aprovado</option>
                          <option value="PAID_OUT" {{ $cashOut->status == 'PAID_OUT' ? 'selected' : '' }}>Pago</option>
                          <option value="PENDING" {{ $cashOut->status == 'PENDING' ? 'selected' : '' }}>Pendente</option>
                          <option value="REJECTED" {{ $cashOut->status == 'REJECTED' ? 'selected' : '' }}>Rejeitado</option>
                          <option value="CANCELLED" {{ $cashOut->status == 'CANCELLED' || $cashOut->status == 'CANCELED' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="12" class="text-center py-5">
                        <i class="fa-solid fa-inbox fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted">Nenhum registro encontrado</p>
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

    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // ======================================================
      // 1. INICIALIZAÇÃO DO DATATABLE
      // ======================================================
      const table = document.getElementById('table-financeiro-saidas');
      if (table) {
        const tbody = table.querySelector('tbody');
        const dataRows = tbody ? tbody.querySelectorAll('tr.transaction-row') : [];
        const hasData = dataRows.length > 0;

        if (hasData) {
          $('#table-financeiro-saidas').DataTable({
            responsive: true,
            info: false,
            ordering: false,
            lengthChange: false,
            pageLength: 25,
            language: {
              url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
              emptyTable:"Nenhum registro encontrado",
              search:"_INPUT_",
              searchPlaceholder:"Buscar..."
            },
            dom: 'rt<"bottom"p><"clear">',
            columnDefs: [
              { targets: '_all', className: 'text-center' },
              { targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], className: 'text-center' }
            ],
            initComplete: function() {
              $('#table-financeiro-saidas_filter input[type="search"]')
                .attr('placeholder', 'Pesquisar saídas...');
            }
          });
        } else {
          // Se não há dados, apenas aplicar estilos básicos
          console.log('Nenhum dado encontrado para inicializar DataTable');
        }
      }

      // ======================================================
      // 2. FILTROS AUTO-SUBMIT
      // ======================================================
      // Auto-submit para selects de status e período
      document.querySelectorAll('select[name="status"], select[name="period"]').forEach(function(select) {
        select.addEventListener('change', function() {
          document.getElementById('filtersForm').submit();
        });
      });
      
      // Auto-submit para busca com delay
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

      // ======================================================
      // 3. MUDANÇA DE STATUS VIA AJAX
      // ======================================================
      $('.status-select').on('change', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const newStatus = $(this).val();
        const selectElement = $(this);
        const oldStatus = selectElement.data('old-status') || selectElement.val();

        if (confirm('Tem certeza que deseja alterar o status desta transação?')) {
          $.ajax({
            url: `/admin/financeiro/${type}/${id}/status`,
            method: 'PUT',
            data: {
              status: newStatus,
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              if (response.success) {
                // Atualiza o badge de status
                const statusCell = selectElement.closest('tr').find('td:nth-child(5)');
                let badgeHtml = '';

                switch(newStatus) {
                  case 'COMPLETED':
                  case 'PAID_OUT':
                    badgeHtml = '<span class="badge bg-success"><i class="fa-solid fa-check-circle me-1"></i>Aprovado</span>';
                    break;
                  case 'PENDING':
                    badgeHtml = '<span class="badge bg-warning"><i class="fa-solid fa-clock me-1"></i>Pendente</span>';
                    break;
                  case 'CANCELLED':
                  case 'CANCELED':
                    badgeHtml = '<span class="badge bg-danger"><i class="fa-solid fa-ban me-1"></i>Cancelado</span>';
                    break;
                  case 'REJECTED':
                    badgeHtml = '<span class="badge bg-danger"><i class="fa-solid fa-times-circle me-1"></i>Rejeitado</span>';
                    break;
                }

                statusCell.html(badgeHtml);
                selectElement.data('old-status', newStatus);
                
                // Notificação de sucesso
                alert('Status atualizado com sucesso!');
              }
            },
            error: function(xhr) {
              alert('Erro ao atualizar status: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
              // Reverte para o status anterior
              selectElement.val(oldStatus);
            }
          });
        } else {
          // Cancela a mudança
          selectElement.val(oldStatus);
        }
      });

      // Armazena o status inicial de cada select
      $('.status-select').each(function() {
        $(this).data('old-status', $(this).val());
      });

      // ======================================================
      // 4. EVENTO DE CLIQUE NAS LINHAS DA TABELA
      // ======================================================
      $('#table-financeiro-saidas tbody').on('click', 'tr.transaction-row', function(e) {
        console.log('🎯 Clique capturado na linha da tabela');
        
        // Evitar clique na coluna de ações (select)
        if ($(e.target).closest('select').length > 0) {
          console.log('⚠️ Clique ignorado - área de ações');
          return;
        }

        var transactionId = $(this).data('transaction-id');
        console.log('🆔 Transaction ID:', transactionId);
        
        if (transactionId) {
          carregarDetalhesSaida(transactionId);
        } else {
          console.error('❌ Transaction ID não encontrado');
          alert('Erro: ID da transação não encontrado');
        }
      });
    });

    // ======================================================
    // 5. FUNÇÕES PARA MODAL DE DETALHES
    // ======================================================
    
    // Função para carregar detalhes da saída
    function carregarDetalhesSaida(transactionId) {
      console.log('🔍 Carregando detalhes da saída:', transactionId);
      
      $.ajax({
        url: '/admin/financeiro/saidas/detalhes/' + transactionId,
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        beforeSend: function() {
          console.log('📤 Enviando requisição AJAX...');
        },
        success: function(response) {
          console.log('✅ Resposta recebida:', response);
          if (response.success) {
            preencherModalSaida(response.data);
            $('#detalhesModal').modal('show');
          } else {
            alert('Erro ao carregar detalhes da transação: ' + (response.message || 'Erro desconhecido'));
          }
        },
        error: function(xhr, status, error) {
          console.error('❌ Erro ao carregar detalhes:', xhr);
          console.error('Status:', status);
          console.error('Error:', error);
          console.error('Response Text:', xhr.responseText);
          alert('Erro ao carregar detalhes da transação. Status: ' + xhr.status + ' - ' + error);
        }
      });
    }

    // Função para preencher o modal com os dados da saída
    function preencherModalSaida(dados) {
      $('#modal-valor').text('R$ ' + parseFloat(dados.valor).toLocaleString('pt-BR', {
        minimumFractionDigits: 2
      }));
      $('#modal-valor-liquido').text('R$ ' + parseFloat(dados.valor_liquido).toLocaleString('pt-BR', {
        minimumFractionDigits: 2
      }));
      $('#modal-taxa').text('R$ ' + parseFloat(dados.taxa).toLocaleString('pt-BR', {
        minimumFractionDigits: 2
      }));
      $('#modal-beneficiario').text(dados.beneficiario || 'N/A');
      $('#modal-documento').text(dados.documento || 'N/A');
      $('#modal-id-transacao').text(dados.idTransaction || 'N/A');
      $('#modal-data').text(dados.data || 'N/A');
      $('#modal-chave-pix').text(dados.chavePix || 'N/A');
      $('#modal-tipo-chave').text(dados.tipoChave || 'N/A');

      // Status
      var statusText = getStatusTextSaida(dados.status);
      var statusClass = getStatusClassSaida(dados.status);
      $('#modal-status').text(statusText).attr('class', 'mb-0 fw-bold ' + statusClass);
    }

    // Funções auxiliares para status
    function getStatusTextSaida(status) {
      const statusMap = {
        'COMPLETED': 'APROVADO',
        'PAID_OUT': 'PAGO',
        'PENDING': 'PENDENTE',
        'REJECTED': 'REJEITADO',
        'CANCELLED': 'CANCELADO',
        'CANCELED': 'CANCELADO'
      };
      return statusMap[status] || status;
    }

    function getStatusClassSaida(status) {
      const classMap = {
        'COMPLETED': 'text-success',
        'PAID_OUT': 'text-success',
        'PENDING': 'text-warning',
        'REJECTED': 'text-danger',
        'CANCELLED': 'text-danger',
        'CANCELED': 'text-danger'
      };
      return classMap[status] || 'text-secondary';
    }
  </script>

  <!-- Modal Detalhes da Transação -->
  <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold" id="detalhesModalLabel">
            <i class="fas fa-receipt me-2"></i>Detalhes da Saída
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body bg-light">
          <!-- Cards de Resumo -->
          <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
              <div class="card shadow-sm bg-danger text-white h-100">
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
              <div class="card shadow-sm bg-info text-white h-100">
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
              <div class="card shadow-sm bg-secondary text-white h-100">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                      <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-percent fa-lg"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <small class="d-block opacity-75">TAXA</small>
                      <h6 class="mb-0 fw-bold" id="modal-taxa">R$ 0,00</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Informações do Beneficiário -->
          <div class="card shadow-sm mb-3">
            <div class="card-header bg-white border-bottom">
              <h6 class="mb-0 fw-bold">
                <i class="fas fa-user-circle me-2 text-primary"></i>Informações do Beneficiário
              </h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-user text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">NOME</small>
                      <span class="fw-semibold" id="modal-beneficiario">N/A</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-id-card text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">DOCUMENTO</small>
                      <span class="fw-semibold" id="modal-documento">N/A</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-hashtag text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">ID TRANSAÇÃO</small>
                      <span class="fw-semibold" id="modal-id-transacao">N/A</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-calendar text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">DATA</small>
                      <span class="fw-semibold" id="modal-data">N/A</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Dados do PIX -->
          <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
              <h6 class="mb-0 fw-bold">
                <i class="fas fa-qrcode me-2 text-primary"></i>Dados do PIX
              </h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-key text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">CHAVE PIX</small>
                      <span class="fw-semibold" id="modal-chave-pix">N/A</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-tag text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">TIPO CHAVE</small>
                      <span class="fw-semibold" id="modal-tipo-chave">N/A</span>
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
        </div>
      </div>
    </div>
  </div>

</x-app-layout>