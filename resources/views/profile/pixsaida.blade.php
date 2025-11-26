<x-app-layout :route="'Relatório de saídas'">
  <div class="main-content app-content">
    <div class="container-fluid">
      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12 mb-4">
          <div class="card border-primary">
            <div class="card-body p-5">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                  <div class="icon-circle bg-primary text-white me-3" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-arrow-up fa-lg"></i>
                  </div>
                  <div>
                    <h1 class="display-6 fw-bold mb-1">Relatório de Saídas</h1>
                    <p class="text-muted mb-0">Monitore todas as transações de saída de sua conta</p>
                  </div>
                </div>
                <div class="d-flex gap-2">
                  <!-- Campo de Busca -->
                  <form method="GET" action="{{ route('profile.relatorio.pixsaida') }}" id="filtroForm" class="d-flex">
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="fas fa-search"></i>
                      </span>
                      <input type="text" class="form-control" id="buscarInput" name="buscar" 
                          placeholder="Buscar registros..." 
                          value="{{ request('buscar') }}">
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtros Adicionais -->
      <div class="row mb-4">
        <div class="col-12">
          <form method="GET" action="{{ route('profile.relatorio.pixsaida') }}" id="filtroFormCompleto">
            <div class="row g-3 align-items-end justify-content-end">
              
              <!-- Filtro por Status -->
              <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label mb-2">Status</label>
                <select class="form-select" name="status">
                  <option value="">Todos</option>
                  <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Concluído</option>
                  <option value="PAID_OUT" {{ request('status') == 'PAID_OUT' ? 'selected' : '' }}>Pago</option>
                  <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pendente</option>
                  <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelado</option>
                  <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Rejeitado</option>
                </select>
              </div>
              
              <!-- Período removido - usando filtros globais -->
              
              <!-- Botões -->
              <div class="col-lg-auto col-md-12">
                <div class="d-flex gap-2 justify-content-md-start justify-content-lg-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-1"></i>Filtrar
                  </button>
                  <a href="{{ route('profile.relatorio.pixsaida') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Limpar
                  </a>
                  <a href="{{ route('profile.relatorio.export.saidas', request()->query()) }}" class="btn btn-success">
                    <i class="fas fa-file-csv me-1"></i>CSV
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Cards Resumo - Padrão Moderno -->
      <div class="row g-3 mb-4">
        <!-- Total Transações -->
        <div class="col-lg-3 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                  <div class="bg-info rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-sync text-white" style="font-size: 14px;"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold text-dark">Transações</h6>
                    <small class="text-muted">Total</small>
                  </div>
                </div>
                <span class="badge bg-info-subtle text-info">{{ (clone $transactions)->count() }}</span>
              </div>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <small class="text-muted">Registros</small>
                  <div class="fw-bold text-info">{{ (clone $transactions)->count() }}</div>
                </div>
                <div class="text-end">
                  <small class="text-muted">Status</small>
                  <div class="fw-semibold">Ativas</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Saídas -->
        <div class="col-lg-3 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                  <div class="bg-danger rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-arrow-up-short-wide text-white" style="font-size: 14px;"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold text-dark">Total Saídas</h6>
                    <small class="text-muted">Bruto</small>
                  </div>
                </div>
                <span class="badge bg-danger-subtle text-danger">Total</span>
              </div>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <small class="text-muted">Valor</small>
                  <div class="fw-bold text-danger">R$ {{ number_format($saidas, 2, ',', '.') }}</div>
                </div>
                <div class="text-end">
                  <small class="text-muted">Período</small>
                  <div class="fw-semibold">Atual</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Chargebacks -->
        <div class="col-lg-3 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                  <div class="bg-warning rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-triangle-exclamation text-white" style="font-size: 14px;"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold text-dark">Chargebacks</h6>
                    <small class="text-muted">Contestações</small>
                  </div>
                </div>
                <span class="badge bg-warning-subtle text-warning">Contestado</span>
              </div>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <small class="text-muted">Valor</small>
                  <div class="fw-bold text-warning">R$ {{ number_format((clone $transactions)->where('status', 'CHARGEBACK')->sum('cash_out_liquido'), 2, ',', '.') }}</div>
                </div>
                <div class="text-end">
                  <small class="text-muted">Status</small>
                  <div class="fw-semibold">Contestado</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MED -->
        <div class="col-lg-3 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                  <div class="bg-secondary rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-ban text-white" style="font-size: 14px;"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold text-dark">MED</h6>
                    <small class="text-muted">Mediação</small>
                  </div>
                </div>
                <span class="badge bg-secondary-subtle text-secondary">Mediação</span>
              </div>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <small class="text-muted">Valor</small>
                  <div class="fw-bold text-secondary">R$ {{ number_format((clone $transactions)->where('status', 'MED')->sum('cash_out_liquido'), 2, ',', '.') }}</div>
                </div>
                <div class="text-end">
                  <small class="text-muted">Status</small>
                  <div class="fw-semibold">Em Mediação</div>
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
            <div class="card-body p-4">
              <div class="table-responsive">
                <table id="table-pix-saidas" class="table text-nowrap">
                  <thead>
                    <tr>
                      @if($settings->relatorio_saidas_mostrar_transacao_id ?? true)
                      <th scope="col">Transação ID</th>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_valor ?? true)
                      <th scope="col">Valor</th>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_nome ?? true)
                      <th scope="col">Nome</th>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_chave_pix ?? true)
                      <th scope="col">Chave PIX</th>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_tipo_chave ?? true)
                      <th scope="col">Tipo Chave</th>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_status ?? true)
                      <th scope="col">Status</th>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_data ?? true)
                      <th scope="col">Data</th>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_taxa ?? true)
                      <th scope="col">Taxa</th>
                      @endif
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($transactions as $transaction)
                    <tr class="transaction-row" style="cursor: pointer;" data-transaction-id="{{ $transaction->id }}" data-id-transaction="{{ $transaction->externalreference ?? $transaction->idTransaction }}">
                      @if($settings->relatorio_saidas_mostrar_transacao_id ?? true)
                      <td>{{ $transaction->externalreference ?? $transaction->idTransaction }}</td>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_valor ?? true)
                      <td>R$ {{ number_format($transaction->amount, 2, ',', '.') }}</td>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_nome ?? true)
                      <td>{{ $transaction->beneficiaryname }}</td>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_chave_pix ?? true)
                      <td>{{ $transaction->pix }}</td>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_tipo_chave ?? true)
                      <td>{{ $transaction->pixkey }}</td>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_status ?? true)
                      <td>
                        @switch($transaction->status)
                          @case('COMPLETED')
                            <span class="badge bg-success">Aprovado</span>
                            @break
                          @case('PAID_OUT')
                            <span class="badge bg-success">Aprovado</span>
                            @break
                          @case('PENDING')
                            <span class="badge bg-warning">Pendente</span>
                            @break
                          @case('CANCELLED')
                            <span class="badge bg-danger">Cancelado</span>
                            @break
                          @case('REJECTED')
                            <span class="badge bg-danger">Rejeitado</span>
                            @break
                          @default
                            <span class="badge bg-secondary">{{ $transaction->status }}</span>
                        @endswitch
                      </td>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_data ?? true)
                      <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y H:i:s') }}</td>
                      @endif
                      @if($settings->relatorio_saidas_mostrar_taxa ?? true)
                      <td>R$ {{ number_format((float)$transaction->taxa_cash_out, 2, ',', '.') }}</td>
                      @endif
                    </tr>
                    @empty
                    <tr class="no-data-row">
                      <td colspan="8" class="text-center py-5">
                        <div class="text-muted">
                          <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                          <h5>Nenhuma transação encontrada</h5>
                          <p class="mb-0">Não há dados para o período selecionado.</p>
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

  <!-- Modal de Detalhes da Transação -->
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
                        <i class="fas fa-calendar-alt fa-lg"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <small class="d-block opacity-75">DATA</small>
                      <h6 class="mb-0 fw-bold" id="modal-data">01/01/2024</h6>
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
                    <i class="fas fa-hashtag text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">ID TRANSAÇÃO</small>
                      <span class="fw-semibold" id="modal-id-transacao">N/A</span>
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
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Fechar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Período Personalizado -->
  <div class="modal fade" id="dateRangeModal" tabindex="-1" aria-labelledby="dateRangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold" id="dateRangeModalLabel">
            <i class="fas fa-calendar-alt me-2"></i>Selecione o período
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-4">
            <div class="col-md-6 text-center">
              <strong class="d-block mb-3">Data de Início</strong>
              <div id="calendarInicio"></div>
            </div>
            <div class="col-md-6 text-center">
              <strong class="d-block mb-3">Data de Fim</strong>
              <div id="calendarFim"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Cancelar
          </button>
          <button type="button" class="btn btn-sm btn-primary" id="btnAplicarDatas">
            <i class="fas fa-check me-1"></i>Aplicar
          </button>
        </div>
      </div>
    </div>
  </div>

  <style>
    /* Garantir que as bordas da tabela apareçam no tema white */
    #table-pix-saidas {
      border-collapse: separate;
      border-spacing: 0;
    }

    #table-pix-saidas thead th {
      border-bottom: 2px solid #dee2e6;
      padding: 12px;
    }

    #table-pix-saidas tbody tr {
      border-bottom: 1px solid #e5e7eb;
    }

    #table-pix-saidas tbody tr:last-child {
      border-bottom: none;
    }

    #table-pix-saidas tbody td {
      padding: 12px;
      border-bottom: 1px solid #e5e7eb;
    }

    #table-pix-saidas tbody tr:hover {
      background-color: rgba(0, 0, 0, 0.02);
    }

    /* Tema escuro */
    [data-theme="dark"] #table-pix-saidas tbody tr {
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    [data-theme="dark"] #table-pix-saidas tbody td {
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    [data-theme="dark"] #table-pix-saidas thead th {
      border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    [data-theme="dark"] #table-pix-saidas tbody tr:hover {
      background-color: rgba(255, 255, 255, 0.05);
    }
  </style>

  <script>
  // ==================== FUNCOES GLOBAIS ====================
  
  // Função global para carregar detalhes da transação
  window.carregarDetalhesTransacao = function(transactionId) {
    // Verificar se o modal existe
    const modalElement = document.getElementById('detalhesModal');
    if (!modalElement) {
      alert('Erro: Modal não encontrado na página');
      return;
    }
    
    // Mostrar loading
    const modalBody = $('#detalhesModal .modal-body');
    const originalContent = modalBody.html();
    modalBody.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-3">Carregando detalhes...</p></div>');
    
    // Abrir modal imediatamente com loading
    try {
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    } catch (error) {
      alert('Erro ao abrir modal: ' + error.message);
      return;
    }
    
    // Fazer requisição AJAX
    $.ajax({
      url: '/relatorio/saidas/detalhes/' + transactionId,
      method: 'GET',
      dataType: 'json',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      success: function(response) {
        if (response.success) {
          preencherModal(response.data);
        } else {
          modalBody.html(originalContent);
          modal.hide();
          alert('Erro ao carregar detalhes: ' + (response.message || 'Erro desconhecido'));
        }
      },
      error: function(xhr, status, error) {
        modalBody.html(originalContent);
        modal.hide();
        alert('Erro ao carregar detalhes da transação. Status: ' + xhr.status + ' - ' + error);
      }
    });
  };

  // Função global para preencher o modal
  window.preencherModal = function(dados) {
    try {
      // Restaurar conteúdo do modal body
      const modalBody = $('#detalhesModal .modal-body');
      modalBody.html(`
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
                    <h4 class="mb-0 fw-bold" id="modal-valor">R$ ${dados.valor}</h4>
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
                    <h5 class="mb-0 fw-bold" id="modal-status">${getStatusText(dados.status)}</h5>
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
                      <i class="fas fa-calendar-alt fa-lg"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <small class="d-block opacity-75">DATA</small>
                    <h6 class="mb-0 fw-bold" id="modal-data">${dados.data}</h6>
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
                    <h6 class="mb-0 fw-bold" id="modal-taxa">R$ ${dados.taxa}</h6>
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
                    <span class="fw-semibold" id="modal-beneficiario">${dados.beneficiario || 'N/A'}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="d-flex align-items-center p-3 bg-light rounded">
                  <i class="fas fa-hashtag text-primary me-3"></i>
                  <div>
                    <small class="text-muted d-block">ID TRANSAÇÃO</small>
                    <span class="fw-semibold" id="modal-id-transacao">${dados.idTransaction || 'N/A'}</span>
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
                    <span class="fw-semibold" id="modal-chave-pix">${dados.chavePix || 'N/A'}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="d-flex align-items-center p-3 bg-light rounded">
                  <i class="fas fa-tag text-primary me-3"></i>
                  <div>
                    <small class="text-muted d-block">TIPO CHAVE</small>
                    <span class="fw-semibold" id="modal-tipo-chave">${dados.tipoChave || 'N/A'}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `);

    } catch (error) {
      alert('Erro ao exibir detalhes da transação');
    }
  };

  // Função global para obter texto do status
  window.getStatusText = function(status) {
    const map = {
      'PENDING': 'PENDENTE',
      'PROCESSING': 'PROCESSANDO',
      'PAID_OUT': 'APROVADO',
      'COMPLETED': 'CONCLUÍDO',
      'CANCELLED': 'CANCELADO',
      'REJECTED': 'REJEITADO',
      'MED': 'EM MEDIAÇÃO'
    };
    return map[status] || status;
  };

  // Captura de clique nas linhas da tabela usando delegação de eventos
  document.addEventListener('click', function(e) {
    if (e.target.closest('tr.transaction-row')) {
      const row = e.target.closest('tr.transaction-row');
      const transactionId = row.getAttribute('data-transaction-id');
      
      if (transactionId) {
        carregarDetalhesTransacao(transactionId);
      }
    }
  });

  document.addEventListener('DOMContentLoaded', function() {
    // ==================== FILTROS ====================
    const buscarInput = document.getElementById('buscarInput');
    const filtroForm = document.getElementById('filtroForm');

    // Evento: Busca com delay
    let searchTimeout;
    buscarInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        filtroForm.submit();
      }, 800);
    });

    // ==================== DATATABLE ====================
    const table = document.getElementById('table-pix-saidas');
    if (table) {
      const tbody = table.querySelector('tbody');
      const hasData = tbody && tbody.querySelectorAll('tr:not(.no-data-row)').length > 0;

      if (hasData) {
        $('#table-pix-saidas').DataTable({
          responsive: true,
          info: false,
          ordering: false,
          searching: false,
          lengthChange: false,
          language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
          },
          dom: '<"top"f>rt<"bottom"p><"clear">'
        });
      }
    }
  });
  </script>
</x-app-layout>

