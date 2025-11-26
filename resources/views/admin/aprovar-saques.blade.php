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
                  <i class="fa-solid fa-money-bill-wave fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Saques Pendentes</h1>
                  <p class="text-muted mb-0">Aprovação de solicitações de saque</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabela de Saques -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="fa-solid fa-table text-primary me-2"></i>
                Lista de Saques Pendentes
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="table-responsive">
                <table id="table-aprovar-saques" class="table text-nowrap table-hover">
                  <thead>
                    <tr>
                      <th><i class="fa-solid fa-user text-primary me-1"></i>Cliente</th>
                      <th><i class="fa-solid fa-tag text-info me-1"></i>Tipo</th>
                      <th><i class="fa-solid fa-key text-warning me-1"></i>Chave / Endereço</th>
                      <th><i class="fa-solid fa-dollar-sign text-success me-1"></i>Valor Total</th>
                      <th><i class="fa-solid fa-coins text-success me-1"></i>Valor Líquido</th>
                      <th class="text-center"><i class="fa-solid fa-circle-check text-warning me-1"></i>Status</th>
                      <th><i class="fa-solid fa-calendar text-secondary me-1"></i>Data</th>
                      <th class="text-center">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($saques as $row)
                    @php
                    // Ajustar exibição do status
                    switch ($row->status) {
                      case 'COMPLETED':
                        $statusBadge = 'success';
                        $statusText = 'Aprovado';
                        $statusIcon = 'check-circle';
                        break;
                      case 'PENDING':
                        $statusBadge = 'warning';
                        $statusText = 'Pendente';
                        $statusIcon = 'clock';
                        break;
                      default:
                        $statusBadge = 'danger';
                        $statusText = 'Cancelado';
                        $statusIcon = 'times-circle';
                    }
                    @endphp
                    <tr>
                      <td class="fw-medium">{{ $row->user_id }}</td>
                      <td>
                        <span class="badge bg-info">{{ $row->type }}</span>
                      </td>
                      <td class="font-monospace small">{{ $row->pix }}</td>
                      <td class="fw-semibold text-success">R$ {{ number_format($row->amount, 2, ',', '.') }}</td>
                      <td class="fw-semibold text-success">R$ {{ number_format($row->cash_out_liquido, 2, ',', '.') }}</td>
                      <td class="text-center">
                        <span class="badge bg-{{ $statusBadge }} {{ $statusBadge === 'warning' ? 'text-dark' : '' }}">
                          <i class="fa-solid fa-{{ $statusIcon }} me-1"></i>{{ $statusText }}
                        </span>
                      </td>
                      <td class="small">{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y H:i') }}</td>
                      <td>
                        <div class="d-flex justify-content-center gap-1">
                          <button class="btn btn-sm btn-success" 
                              data-bs-toggle="modal" 
                              data-bs-target="#aprovarModal-{{ $row->id }}"
                              title="Aprovar">
                            <i class="fa-solid fa-check me-1"></i>Aprovar
                          </button>
                          <button class="btn btn-sm btn-danger" 
                              data-bs-toggle="modal" 
                              data-bs-target="#rejeitarModal-{{ $row->id }}"
                              title="Rejeitar">
                            <i class="fa-solid fa-times me-1"></i>Rejeitar
                          </button>
                        </div>
                      </td>
                    </tr>
                    @empty
                    <tr class="no-data-row">
                      <td colspan="8" class="text-center py-5">
                        <div class="text-muted">
                          <i class="fa-solid fa-inbox fa-3x mb-3 d-block"></i>
                          <h5>Nenhum saque pendente</h5>
                          <p class="mb-0">Não há solicitações de saque aguardando aprovação.</p>
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
    </div>
  </div>

  <!-- Modais -->
  @forelse($saques as $row)
    <!-- Modal Aprovar Saque -->
    <div class="modal fade" id="aprovarModal-{{ $row->id }}" tabindex="-1" aria-labelledby="aprovarModalLabel-{{ $row->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="{{ route('admin.saques.aprovar', ['id' => $row->id ]) }}">
            @csrf
            @method('PUT')
            <div class="modal-header bg-success text-white">
              <h5 class="modal-title fw-semibold" id="aprovarModalLabel-{{ $row->id }}">
                <i class="fa-solid fa-check-circle me-2"></i>Aprovar Saque
              </h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
              <div class="text-center mb-4">
                <div class="icon-circle bg-success text-white mx-auto mb-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-question-circle fa-lg"></i>
                </div>
                <h6 class="fw-semibold mb-2">Confirmar Aprovação</h6>
                <p class="text-muted mb-0">Você tem certeza que deseja <strong class="text-success">aprovar</strong> este saque?</p>
              </div>
              <div class="alert alert-success mb-0">
                <i class="fa-solid fa-info-circle me-2"></i>
                <small>Esta ação não poderá ser desfeita e o valor será transferido ao cliente.</small>
              </div>
              <div class="bg-light rounded p-3 mt-3">
                <div class="row g-2 small">
                  <div class="col-6"><strong>Cliente:</strong></div>
                  <div class="col-6 text-end">{{ $row->user_id }}</div>
                  <div class="col-6"><strong>Tipo:</strong></div>
                  <div class="col-6 text-end"><span class="badge bg-info">{{ $row->type }}</span></div>
                  <div class="col-6"><strong>Valor:</strong></div>
                  <div class="col-6 text-end fw-semibold text-success">R$ {{ number_format($row->amount, 2, ',', '.') }}</div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fa-solid fa-times me-1"></i>Cancelar
              </button>
              <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-check me-1"></i>Confirmar Aprovação
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Rejeitar Saque -->
    <div class="modal fade" id="rejeitarModal-{{ $row->id }}" tabindex="-1" aria-labelledby="rejeitarModalLabel-{{ $row->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="{{ route('admin.saques.rejeitar', ['id' => $row->id ]) }}">
            @csrf
            @method('PUT')
            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title fw-semibold" id="rejeitarModalLabel-{{ $row->id }}">
                <i class="fa-solid fa-times-circle me-2"></i>Rejeitar Saque
              </h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
              <div class="text-center mb-4">
                <div class="icon-circle bg-danger text-white mx-auto mb-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-exclamation-triangle fa-lg"></i>
                </div>
                <h6 class="fw-semibold mb-2">Confirmar Rejeição</h6>
                <p class="text-muted mb-0">Você tem certeza que deseja <strong class="text-danger">rejeitar</strong> este saque?</p>
              </div>
              <div class="alert alert-danger mb-0">
                <i class="fa-solid fa-exclamation-circle me-2"></i>
                <small>Esta ação não poderá ser desfeita e o saldo será devolvido à carteira do cliente.</small>
              </div>
              <div class="bg-light rounded p-3 mt-3">
                <div class="row g-2 small">
                  <div class="col-6"><strong>Cliente:</strong></div>
                  <div class="col-6 text-end">{{ $row->user_id }}</div>
                  <div class="col-6"><strong>Tipo:</strong></div>
                  <div class="col-6 text-end"><span class="badge bg-info">{{ $row->type }}</span></div>
                  <div class="col-6"><strong>Valor:</strong></div>
                  <div class="col-6 text-end fw-semibold text-danger">R$ {{ number_format($row->amount, 2, ',', '.') }}</div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fa-solid fa-times me-1"></i>Cancelar
              </button>
              <button type="submit" class="btn btn-danger">
                <i class="fa-solid fa-ban me-1"></i>Confirmar Rejeição
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @empty
  @endforelse

  <!-- JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      console.log('🔧 Inicializando página de aprovação de saques...');
      
      // ==================== DATATABLE ====================
      const table = document.getElementById('table-aprovar-saques');
      
      if (table) {
        const tbody = table.querySelector('tbody');
        const dataRows = tbody ? tbody.querySelectorAll('tr:not(.no-data-row)') : [];
        const hasData = dataRows.length > 0;
        
        console.log('📊 Linhas de dados encontradas:', dataRows.length);
        
        if (hasData) {
          try {
            // Contar colunas do thead
            const theadCols = table.querySelectorAll('thead th').length;
            console.log('📋 Colunas no thead:', theadCols);
            
            // Contar colunas da primeira linha de dados
            const firstRowCols = dataRows[0].querySelectorAll('td').length;
            console.log('📋 Colunas na primeira linha:', firstRowCols);
            
            if (theadCols !== firstRowCols) {
              console.error('❌ Erro: Número de colunas não corresponde!');
              console.error('  thead tem', theadCols, 'colunas');
              console.error('  tbody tem', firstRowCols, 'colunas');
              return;
            }
            
            $('#table-aprovar-saques').DataTable({
              responsive: true,
              info: false,
              ordering: false,
              lengthChange: false,
              pageLength: 25,
              language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
              },
              dom: '<"top"f>rt<"bottom"p><"clear">',
              initComplete: function() {
                $('#table-aprovar-saques_filter input[type="search"]')
                  .attr('placeholder', 'Pesquisar saques...');
                console.log('✅ DataTable inicializado com sucesso!');
              }
            });
          } catch (error) {
            console.error('❌ Erro ao inicializar DataTable:', error);
          }
        } else {
          console.log('ℹ️ Nenhum dado para exibir, DataTable não será inicializado.');
        }
      } else {
        console.error('❌ Tabela não encontrada!');
      }

      // ==================== TOOLTIPS ====================
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
      
      console.log('✅ Página inicializada com sucesso!');
    });
  </script>
</x-app-layout>
