<x-app-layout :route="'Meus Referidos'">

  <div class="main-content app-content">
    <div class="container-fluid">
      
      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-primary">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-primary text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-users fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Meus Referidos</h1>
                  <p class="text-muted mb-0">Acompanhe suas comissões e as {{ $qtdeReferidos }} empresas referidas</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Link de Referência -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-body p-4">
              <div class="row align-items-center g-3">
                <div class="col-lg-9 col-md-8">
                  <label class="form-label fw-semibold mb-2">
                    <i class="fa-solid fa-link text-primary me-2"></i>
                    Seu Link de Indicação
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fa-solid fa-globe"></i>
                    </span>
                    <input type="text" 
                      class="form-control font-monospace" 
                      id="affiliate-link" 
                      value="{{ url('register?ref=' . auth()->user()->affiliate_code) }}" 
                      readonly>
                    <button class="btn btn-warning" id="copy-btn" onclick="copiarLink()">
                      <i class="fas fa-copy me-1"></i>Copiar
                    </button>
                  </div>
                  <small class="form-text text-muted">Compartilhe este link para receber comissões</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cards de Estatísticas -->
      <div class="row g-3 mb-4">
        <!-- Total em Comissões -->
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3 position-relative">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-piggy-bank"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total em Comissões</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($totalComissoes, 2, ',', '.') }}</h4>
                </div>
                <button class="btn btn-success btn-sm rounded-circle p-0 position-absolute d-flex align-items-center justify-content-center" 
                  onclick="solicitarSaque()"
                  style="width: 32px; height: 32px; top: 0; right: 0;"
                  title="Solicitar Saque"
                  data-bs-toggle="tooltip"
                  data-bs-placement="left">
                  <i class="fa-solid fa-dollar-sign"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Taxa de Comissão -->
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fa-solid fa-percent"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Taxa de Comissão</p>
                  <h4 class="fw-bold mb-0 text-info text-truncate" style="font-size: 1.35rem;">{{ number_format($taxaComissao, 2) }}%</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Qtde. de Referidos -->
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-primary flex-shrink-0">
                  <i class="fa-solid fa-user-group"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Empresas Referidas</p>
                  <h4 class="fw-bold mb-0 text-primary text-truncate" style="font-size: 1.35rem;">{{ $qtdeReferidos }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Qtde. de Transações -->
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-warning flex-shrink-0">
                  <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total de Transações</p>
                  <h4 class="fw-bold mb-0 text-warning text-truncate" style="font-size: 1.35rem;">{{ $qtdeTransacoes }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Lista de Referidos -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="fa-solid fa-list text-primary me-2"></i>
                Lista de Referidos
              </h5>
            </div>
            <div class="card-body p-4">
              @if($clientes->count() > 0)
                <div class="table-responsive">
                  <table class="table text-nowrap">
                    <thead>
                      <tr>
                        <th><i class="fa-solid fa-user text-primary me-1"></i>Nome</th>
                        <th><i class="fa-solid fa-envelope text-info me-1"></i>Email</th>
                        <th class="text-center"><i class="fa-solid fa-circle-check text-success me-1"></i>Status</th>
                        <th class="text-center"><i class="fa-solid fa-calendar text-warning me-1"></i>Data de Cadastro</th>
                        <th class="text-center"><i class="fa-solid fa-money-bill text-success me-1"></i>Depósitos</th>
                        <th class="text-center">Ações</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($clientes as $cliente)
                        <tr>
                          <td class="fw-medium">{{ $cliente->name }}</td>
                          <td>{{ $cliente->email }}</td>
                          <td class="text-center">
                            <span class="badge bg-{{ $cliente->status == 'ACTIVE' ? 'success' : 'warning' }}">
                              <i class="fa-solid fa-circle me-1" style="font-size: 6px;"></i>
                              {{ $cliente->status == 'ACTIVE' ? 'Ativo' : 'Inativo' }}
                            </span>
                          </td>
                          <td class="text-center">{{ $cliente->created_at->format('d/m/Y H:i') }}</td>
                          <td class="text-center">
                            <span class="badge bg-success">
                              R$ {{ number_format($cliente->depositos->sum('valor'), 2, ',', '.') }}
                            </span>
                          </td>
                          <td class="text-center">
                            <a href="{{ route('affiliate.cliente-detalhes', $cliente->id) }}" 
                              class="btn btn-sm btn-outline-primary"
                              title="Ver Detalhes"
                              data-bs-toggle="tooltip">
                              <i class="fas fa-eye"></i>
                            </a>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                  {{ $clientes->links() }}
                </div>
              @else
                <!-- Empty State -->
                <div class="text-center py-5">
                  <div class="icon-circle bg-light text-muted mx-auto mb-4" style="width: 80px; height: 80px;">
                    <i class="fa-solid fa-users fa-2x"></i>
                  </div>
                  <h5 class="fw-semibold mb-2">Nenhum referido ainda</h5>
                  <p class="text-muted mb-4">Use seu link de indicação para começar a receber comissões!</p>
                  <button class="btn btn-warning" onclick="copiarLink()">
                    <i class="fas fa-copy me-2"></i>Copiar Link de Indicação
                  </button>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Copiar Link de Afiliado
    function copiarLink() {
      const input = document.getElementById('affiliate-link');
      const btn = document.getElementById('copy-btn');
      
      // Usar Clipboard API moderna
      navigator.clipboard.writeText(input.value)
        .then(() => {
          // Feedback visual no botão
          const originalHTML = btn.innerHTML;
          btn.classList.remove('btn-warning');
          btn.classList.add('btn-success');
          btn.innerHTML = '<i class="fas fa-check me-1"></i>Copiado!';
          
          // Restaurar após 2 segundos
          setTimeout(() => {
            btn.classList.remove('btn-success');
            btn.classList.add('btn-warning');
            btn.innerHTML = originalHTML;
          }, 2000);
          
          // Mostrar toast de sucesso
          if (typeof showToast === 'function') {
            showToast('success', 'Link copiado com sucesso!');
          }
        })
        .catch(err => {
          console.error('Erro ao copiar link: ', err);
          if (typeof showToast === 'function') {
            showToast('error', 'Falha ao copiar o link.');
          }
        });
    }

    // Solicitar Saque (abre modal de saque)
    function solicitarSaque() {
      // Abre o modal de saque PIX
      const modal = new bootstrap.Modal(document.getElementById('saquepix'));
      modal.show();
    }

    // Auto-seleção do link ao clicar
    document.addEventListener('DOMContentLoaded', function() {
      const input = document.getElementById('affiliate-link');
      if (input) {
        input.addEventListener('click', function() {
          this.select();
        });
      }
      
      // Inicializar tooltips
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });
  </script>
</x-app-layout>