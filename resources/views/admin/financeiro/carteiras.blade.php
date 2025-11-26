<x-app-layout :route="'[ADMIN] Carteiras'">
  
  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-wallet fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Carteiras</h1>
                  <p class="text-muted mb-0">Gerencie saldos em carteiras e relatórios de usuários</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cards de Estatísticas -->
      <div class="row mb-4">
        <!-- Total em Carteiras -->
        <div class="col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="icon-circle bg-success text-white">
                  <i class="fa-solid fa-wallet"></i>
                </div>
              </div>
              <h3 class="fw-bold text-success mb-2">R$ {{ number_format($total_em_carteiras ?? 0, 2, ',', '.') }}</h3>
              <p class="text-muted mb-0">Total em Carteiras</p>
            </div>
          </div>
        </div>

        <!-- Total no Gateway -->
        <div class="col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="icon-circle bg-info text-white">
                  <i class="fa-solid fa-server"></i>
                </div>
              </div>
              <h3 class="fw-bold text-info mb-2">R$ {{ number_format($totalBrutoGateway ?? 0, 2, ',', '.') }}</h3>
              <p class="text-muted mb-0">Total no Gateway</p>
            </div>
          </div>
        </div>
      </div>

      <!-- TOP 3 Vendas -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-warning text-dark">
              <h5 class="mb-0">
                <i class="fa-solid fa-trophy me-2"></i>
                TOP 3 com Mais Vendas
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-3">
                @forelse ($top3Users as $index => $topUser)
                  <div class="col-md-4">
                    <div class="card bg-light h-100">
                      <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                          <div class="icon-circle {{ $index === 0 ? 'bg-warning' : ($index === 1 ? 'bg-secondary' : 'bg-danger') }} text-white me-2" style="width: 40px; height: 40px;">
                            <span class="fw-bold">{{ $index + 1 }}º</span>
                          </div>
                          <div>
                            <h6 class="mb-0 fw-bold">{{ $topUser->user->name }}</h6>
                            <small class="text-muted">User ID: {{ $topUser->user->id }}</small>
                          </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                          <span class="text-muted small">Total Vendas:</span>
                          <span class="fw-bold text-success">R$ {{ number_format($topUser->total_amount, 2, ',', '.') }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="col-12">
                    <div class="text-center py-4 text-muted">
                      <i class="fa-solid fa-trophy fa-3x mb-3 d-block"></i>
                      <p class="mb-0">Nenhum usuário encontrado</p>
                    </div>
                  </div>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabela de Usuários -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                <i class="fa-solid fa-users text-primary me-2"></i>
                Relatório de Usuários
              </h5>
              <a href="{{ route('admin.financeiro.carteiras.export') }}" class="btn btn-success">
                <i class="fas fa-file-csv me-1"></i>CSV
              </a>
            </div>
            <div class="card-body p-4">
              <div class="table-responsive">
                <table id="table-carteiras" class="table text-nowrap table-hover">
                  <thead>
                    <tr>
                      <th><i class="fa-solid fa-user text-primary me-1"></i>User ID</th>
                      <th><i class="fa-solid fa-chart-line text-success me-1"></i>Faturamento</th>
                      <th><i class="fa-solid fa-wallet text-info me-1"></i>Saldo da Carteira</th>
                      <th><i class="fa-solid fa-envelope text-warning me-1"></i>Email</th>
                      <th><i class="fa-solid fa-phone text-secondary me-1"></i>Telefone</th>
                      <th class="text-center">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($usuarios as $usuario)
                      <tr>
                        <td class="fw-medium">{{ $usuario->user_id }}</td>
                        <td class="fw-semibold text-success">R$ {{ number_format($usuario->depositos()->where('status','PAID_OUT')->sum('amount'), 2, ',', '.') }}</td>
                        <td class="fw-bold text-info">R$ {{ number_format($usuario->saldo ?? 0, 2, ',', '.') }}</td>
                        <td class="small">{{ $usuario->email }}</td>
                        <td class="font-monospace small">{{ $usuario->telefone }}</td>
                        <td class="text-center">
                          <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $usuario->telefone) }}"
                            target="_blank"
                            class="btn btn-sm btn-success"
                            title="Contatar via WhatsApp"
                            data-bs-toggle="tooltip">
                            <i class="fa-brands fa-whatsapp me-1"></i>WhatsApp
                          </a>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="6" class="text-center py-5">
                          <div class="text-muted">
                            <i class="fa-solid fa-inbox fa-3x mb-3 d-block"></i>
                            <h5>Nenhum usuário encontrado</h5>
                            <p class="mb-0">Não há usuários com carteiras cadastradas.</p>
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

  <!-- JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // ==================== DATATABLE ====================
      const table = document.getElementById('table-carteiras');
      if (table) {
        const tbody = table.querySelector('tbody');
        const hasData = tbody && tbody.querySelectorAll('tr:not(.no-data-row)').length > 0;

        if (hasData) {
          $('#table-carteiras').DataTable({
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
              $('#table-carteiras_filter input[type="search"]')
                .attr('placeholder', 'Pesquisar usuários...');
            }
          });
        }
      }

      // ==================== TOOLTIPS ====================
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });
  </script>
</x-app-layout>

