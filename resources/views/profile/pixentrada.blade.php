<x-app-layout :route="'Relatório de entradas'">
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
                    <i class="fa-solid fa-arrow-down fa-lg"></i>
                  </div>
                  <div>
                    <h1 class="display-6 fw-bold mb-1">Relatório de Entradas</h1>
                    <p class="text-muted mb-0">Monitore todas as transações de entrada em sua conta</p>
                  </div>
                </div>
                <div class="d-flex gap-2">
                  <!-- Campo de Busca -->
                  <form method="GET" action="{{ route('profile.relatorio.pixentrada') }}" id="filtroForm" class="d-flex">
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
          <form method="GET" action="{{ route('profile.relatorio.pixentrada') }}" id="filtroFormCompleto">
            <div class="row g-3 align-items-end justify-content-end">
              
              <!-- Filtro por Status -->
              <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label mb-2">Status</label>
                <select class="form-select" name="status">
                  <option value="">Todos</option>
                  <option value="PAID_OUT" {{ request('status') == 'PAID_OUT' ? 'selected' : '' }}>Aprovado</option>
                  <option value="WAITING_FOR_APPROVAL" {{ request('status') == 'WAITING_FOR_APPROVAL' ? 'selected' : '' }}>Aguardando</option>
                  <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pendente</option>
                  <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelado</option>
                </select>
              </div>
              
              <!-- Período removido - usando filtros globais -->
              
              <!-- Botões -->
              <div class="col-lg-auto col-md-12">
                <div class="d-flex gap-3 justify-content-md-start justify-content-lg-end">
                  <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-filter me-1"></i>Filtrar
                  </button>
                  <a href="{{ route('profile.relatorio.pixentrada') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Limpar
                  </a>
                  <a href="{{ route('profile.relatorio.export.entradas', request()->query()) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-csv me-1"></i>CSV
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Resumo de Entradas - Cards Modernos -->
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

        <!-- Faturamento -->
        <div class="col-lg-3 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                  <div class="bg-success rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-arrow-down-short-wide text-white" style="font-size: 14px;"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold text-dark">Faturamento</h6>
                    <small class="text-muted">Bruto</small>
                  </div>
                </div>
                <span class="badge bg-success-subtle text-success">Total</span>
              </div>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <small class="text-muted">Valor</small>
                  <div class="fw-bold text-success">R$ {{ number_format($faturamento, 0, ',', '.') }}</div>
                </div>
                <div class="text-end">
                  <small class="text-muted">Período</small>
                  <div class="fw-semibold">Atual</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Valor Líquido -->
        <div class="col-lg-3 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                  <div class="bg-primary rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-dollar-sign text-white" style="font-size: 14px;"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold text-dark">Valor Líquido</h6>
                    <small class="text-muted">Disponível</small>
                  </div>
                </div>
                <span class="badge bg-primary-subtle text-primary">Líquido</span>
              </div>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <small class="text-muted">Saldo</small>
                  <div class="fw-bold text-primary">R$ {{ number_format($valorLiquido, 0, ',', '.') }}</div>
                </div>
                <div class="text-end">
                  <small class="text-muted">Taxa</small>
                  <div class="fw-semibold">Descontada</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Ticket Médio -->
        <div class="col-lg-3 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                  <div class="bg-warning rounded-circle p-2 me-2" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-receipt text-white" style="font-size: 14px;"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold text-dark">Ticket Médio</h6>
                    <small class="text-muted">Por transação</small>
                  </div>
                </div>
                @php
                  $ticketMedio = 0;
                  $totalTransacoesFiltradas = (clone $transactions)->count();
                  if ($totalTransacoesFiltradas > 0) {
                    $somaDepositoLiquido = (clone $transactions)->sum('deposito_liquido');
                    $ticketMedio = $somaDepositoLiquido / $totalTransacoesFiltradas;
                  }
                @endphp
                <span class="badge bg-warning-subtle text-warning">Média</span>
              </div>
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <small class="text-muted">Valor</small>
                  <div class="fw-bold text-warning">R$ {{ number_format($ticketMedio, 0, ',', '.') }}</div>
                </div>
                <div class="text-end">
                  <small class="text-muted">Cálculo</small>
                  <div class="fw-semibold">Automático</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabela de Transações Moderna -->
      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <h6 class="mb-0 fw-semibold text-dark">
                    <i class="fas fa-list me-2 text-primary"></i>Transações de Entrada
                  </h6>
                  <small class="text-muted">Histórico detalhado de todas as transações</small>
                </div>
                <span class="badge bg-primary-subtle text-primary">{{ count($transactions) }} registros</span>
              </div>
              
              <div class="table-responsive">
                <table id="table-pix-entradas" class="table table-hover">
                  <thead class="table-light">
                    <tr>
                      @if($settings->relatorio_entradas_mostrar_meio ?? true)
                      <th scope="col" class="text-center" style="width: 60px;">
                        <i class="fas fa-credit-card me-1"></i>Meio
                      </th>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_transacao_id ?? true)
                      <th scope="col">
                        <i class="fas fa-hashtag me-1"></i>ID
                      </th>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_valor ?? true)
                      <th scope="col" class="text-end">
                        <i class="fas fa-dollar-sign me-1"></i>Valor
                      </th>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_valor_liquido ?? true)
                      <th scope="col" class="text-end">
                        <i class="fas fa-wallet me-1"></i>Líquido
                      </th>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_nome ?? true)
                      <th scope="col">
                        <i class="fas fa-user me-1"></i>Cliente
                      </th>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_documento ?? true)
                      <th scope="col">
                        <i class="fas fa-id-card me-1"></i>Documento
                      </th>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_status ?? true)
                      <th scope="col" class="text-center">
                        <i class="fas fa-check-circle me-1"></i>Status
                      </th>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_data ?? true)
                      <th scope="col">
                        <i class="fas fa-calendar me-1"></i>Data
                      </th>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_taxa ?? true)
                      <th scope="col" class="text-end">
                        <i class="fas fa-percent me-1"></i>Taxa
                      </th>
                      @endif
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($transactions as $transaction)
                    <tr class="transaction-row border-bottom" style="cursor: pointer;" data-transaction-id="{{ $transaction->id }}" data-id-transaction="{{ $transaction->idTransaction }}">
                      @if($settings->relatorio_entradas_mostrar_meio ?? true)
                      <td class="text-center">
                        @switch($transaction->method)
                          @case('pix')
                            <div class="bg-success-subtle rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                              <i class="fa-brands fa-pix text-success" style="font-size: 10px;"></i>
                            </div>
                            @break
                          @case('billet')
                            <div class="bg-info-subtle rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                              <i class="fa-solid fa-barcode text-info" style="font-size: 10px;"></i>
                            </div>
                            @break
                          @case('card')
                            <div class="bg-warning-subtle rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                              <i class="fa-solid fa-credit-card text-warning" style="font-size: 10px;"></i>
                            </div>
                            @break
                          @case('comissao')
                            <div class="bg-primary-subtle rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                              <i class="fa-solid fa-handshake text-primary" style="font-size: 10px;"></i>
                            </div>
                            @break
                        @endswitch
                      </td>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_transacao_id ?? true)
                      <td>
                        <code class="text-dark">{{ $transaction->idTransaction }}</code>
                      </td>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_valor ?? true)
                      <td class="text-end">
                        <span class="fw-semibold text-success">R$ {{ number_format($transaction->amount, 2, ',', '.') }}</span>
                      </td>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_valor_liquido ?? true)
                      <td class="text-end">
                        <span class="fw-semibold text-primary">R$ {{ number_format($transaction->deposito_liquido, 2, ',', '.') }}</span>
                      </td>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_nome ?? true)
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 20px; height: 20px;">
                            <i class="fas fa-user text-primary" style="font-size: 8px;"></i>
                          </div>
                          <span class="fw-medium small">{{ $transaction->client_name }}</span>
                        </div>
                      </td>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_documento ?? true)
                      <td>
                        <span class="text-muted">{{ $transaction->client_document }}</span>
                      </td>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_status ?? true)
                      <td class="text-center">
                        @switch($transaction->status)
                          @case('PAID_OUT')
                            <span class="badge bg-success-subtle text-success">Aprovado</span>
                            @break
                          @case('WAITING_FOR_APPROVAL')
                            <span class="badge bg-warning-subtle text-warning">Aguardando</span>
                            @break
                          @case('PENDING')
                            <span class="badge bg-info-subtle text-info">Pendente</span>
                            @break
                          @case('CANCELLED')
                            <span class="badge bg-danger-subtle text-danger">Cancelado</span>
                            @break
                          @case('RELEASE')
                            <span class="badge bg-primary-subtle text-primary" data-bs-toggle="popover" data-bs-trigger="hover focus"
                               data-bs-content="Será liberado em {{ \Carbon\Carbon::parse($transaction->date)->addDays($transaction->days_availability ?? 21)->format('d/m/Y \à\s H:i:s') }}">
                              A Liberar
                            </span>
                            @break
                          @default
                            <span class="badge bg-secondary-subtle text-secondary">{{ $transaction->status }}</span>
                        @endswitch
                      </td>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_data ?? true)
                      <td>
                        <div class="d-flex flex-column">
                          <span class="fw-medium">{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</span>
                          <small class="text-muted">{{ \Carbon\Carbon::parse($transaction->date)->format('H:i:s') }}</small>
                        </div>
                      </td>
                      @endif
                      @if($settings->relatorio_entradas_mostrar_taxa ?? true)
                      <td class="text-end">
                        <span class="text-muted">R$ {{ number_format((float)$transaction->amount - (float)$transaction->deposito_liquido, 2, ',', '.') }}</span>
                      </td>
                      @endif
                    </tr>
                    @empty
                    <tr class="no-data-row">
                      <td colspan="9" class="text-center py-5">
                        <div class="text-muted">
                          <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="fa fa-inbox fa-2x text-muted"></i>
                          </div>
                          <h6 class="mb-2">Nenhuma transação encontrada</h6>
                          <p class="mb-0 small">Não há dados para o período selecionado.</p>
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
            @if($settings->relatorio_entradas_mostrar_valor ?? true)
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
            @endif

            @if($settings->relatorio_entradas_mostrar_valor_liquido ?? true)
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
            @endif

            @if($settings->relatorio_entradas_mostrar_status ?? true)
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
            @endif

            @if($settings->relatorio_entradas_mostrar_data ?? true)
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
            @endif
          </div>

          <!-- Informações do Cliente -->
          @if(($settings->relatorio_entradas_mostrar_nome ?? true) || ($settings->relatorio_entradas_mostrar_documento ?? true) || ($settings->relatorio_entradas_mostrar_transacao_id ?? true))
          <div class="card shadow-sm mb-3">
            <div class="card-header bg-white border-bottom">
              <h6 class="mb-0 fw-bold">
                <i class="fas fa-user-circle me-2 text-primary"></i>Informações do Cliente
              </h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                @if($settings->relatorio_entradas_mostrar_nome ?? true)
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-user text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">NOME</small>
                      <span class="fw-semibold" id="modal-cliente">N/A</span>
                    </div>
                  </div>
                </div>
                @endif
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-envelope text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">EMAIL</small>
                      <span class="fw-semibold" id="modal-email">N/A</span>
                    </div>
                  </div>
                </div>
                @if($settings->relatorio_entradas_mostrar_documento ?? true)
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-id-card text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">DOCUMENTO</small>
                      <span class="fw-semibold" id="modal-documento">N/A</span>
                    </div>
                  </div>
                </div>
                @endif
                @if($settings->relatorio_entradas_mostrar_transacao_id ?? true)
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-hashtag text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">ID TRANSAÇÃO</small>
                      <span class="fw-semibold" id="modal-id-transacao">N/A</span>
                    </div>
                  </div>
                </div>
                @endif
              </div>
            </div>
          </div>
          @endif

          <!-- Dados do Pagamento -->
          <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
              <h6 class="mb-0 fw-bold">
                <i class="fas fa-credit-card me-2 text-primary"></i>Dados do Pagamento
              </h6>
            </div>
            <div class="card-body">
              <div id="modal-dados-pagamento" class="mb-3">
                <!-- Conteúdo preenchido via JavaScript -->
              </div>
              @if($settings->relatorio_entradas_mostrar_taxa ?? true)
              <div class="d-flex align-items-center p-3 bg-light rounded">
                <i class="fas fa-percent text-primary me-3"></i>
                <div>
                  <small class="text-muted d-block">TAXA</small>
                  <span class="fw-semibold" id="modal-taxa-valor">R$ 0,00</span>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Fechar
          </button>
          <button type="button" class="btn btn-sm btn-danger d-none" id="estornarBtn">
            <i class="fas fa-undo me-1"></i>Estornar
          </button>
          <button type="button" class="btn btn-sm btn-warning d-none" id="cancelarBtn">
            <i class="fas fa-ban me-1"></i>Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>

  <style>
    /* Tabela Moderna - Estilos Limpos */
    #table-pix-entradas {
      border-collapse: collapse !important;
      width: 100%;
      font-size: 0.875rem !important;
    }

    #table-pix-entradas thead th {
      background-color: #f8f9fa !important;
      /* border: 1px solid #dee2e6 !important; */
      padding: 8px 12px !important;
      font-weight: 600 !important;
      color: #495057 !important;
      font-size: 0.8rem !important;
      letter-spacing: 0.025em !important;
      text-align: center !important;
    }

    #table-pix-entradas tbody tr {
      /* border: 1px solid #dee2e6 !important; */
      transition: all 0.2s ease !important;
    }

    #table-pix-entradas tbody tr:hover {
      background-color: #f8f9fa !important;
    }

    #table-pix-entradas tbody td {
      padding: 8px 12px !important;
      /* border: 1px solid #dee2e6 !important; */
      vertical-align: middle !important;
      font-size: 0.8rem !important;
    }

    #table-pix-entradas tbody tr.transaction-row {
      cursor: pointer !important;
    }

    /* Ícones dos métodos de pagamento */
    #table-pix-entradas .bg-success-subtle {
      background-color: rgba(25, 135, 84, 0.1) !important;
    }

    #table-pix-entradas .bg-info-subtle {
      background-color: rgba(13, 202, 240, 0.1) !important;
    }

    #table-pix-entradas .bg-warning-subtle {
      background-color: rgba(255, 193, 7, 0.1) !important;
    }

    #table-pix-entradas .bg-primary-subtle {
      background-color: rgba(13, 110, 253, 0.1) !important;
    }

    /* Tema escuro */
    [data-theme="dark"] #table-pix-entradas thead th {
      background-color: rgba(255, 255, 255, 0.05) !important;
      color: #e5e7eb !important;
      border-bottom-color: rgba(255, 255, 255, 0.15) !important;
    }

    [data-theme="dark"] #table-pix-entradas tbody tr:hover {
      background-color: rgba(255, 255, 255, 0.08) !important;
    }

    [data-theme="dark"] #table-pix-entradas tbody td {
      border-bottom-color: rgba(255, 255, 255, 0.1) !important;
    }

    /* Responsividade */
    @media (max-width: 768px) {
      #table-pix-entradas tbody td {
        padding: 6px 8px !important;
        font-size: 0.75rem !important;
      }
      
      #table-pix-entradas thead th {
        padding: 6px 8px !important;
        font-size: 0.75rem !important;
      }
      
      #table-pix-entradas {
        font-size: 0.75rem !important;
      }
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
        url: '/relatorio/entradas/detalhes/' + transactionId,
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
                      <h4 class="mb-0 fw-bold" id="modal-valor">R$ ${dados.valor}</h4>
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
                      <h4 class="mb-0 fw-bold" id="modal-valor-liquido">R$ ${dados.valor_liquido}</h4>
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
                      <span class="fw-semibold" id="modal-cliente">${dados.cliente || 'N/A'}</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-envelope text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">EMAIL</small>
                      <span class="fw-semibold" id="modal-email">${dados.email || 'N/A'}</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="fas fa-id-card text-primary me-3"></i>
                    <div>
                      <small class="text-muted d-block">DOCUMENTO</small>
                      <span class="fw-semibold" id="modal-documento">${dados.documento || 'N/A'}</span>
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

          <!-- Dados do Pagamento -->
          <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
              <h6 class="mb-0 fw-bold">
                <i class="fas fa-credit-card me-2 text-primary"></i>Dados do Pagamento
              </h6>
            </div>
            <div class="card-body">
              <div id="modal-dados-pagamento" class="mb-3">
                <!-- Conteúdo preenchido via JavaScript -->
              </div>
              <div class="d-flex align-items-center p-3 bg-light rounded">
                <i class="fas fa-percent text-primary me-3"></i>
                <div>
                  <small class="text-muted d-block">TAXA</small>
                  <span class="fw-semibold" id="modal-taxa-valor">R$ ${dados.taxa}</span>
                </div>
              </div>
            </div>
          </div>
        `);

        // Dados do Pagamento
        const container = $('#modal-dados-pagamento');
        container.empty();

        if (dados.method === 'card' && dados.method_details) {
          container.html(`
            <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded">
              <div><i class="fas fa-credit-card text-primary me-2"></i><strong>Bandeira:</strong> ${dados.method_details.brand || 'N/A'}</div>
              <div><i class="fas fa-hashtag text-primary me-2"></i><strong>Final:</strong> ****${dados.method_details.last_four || 'N/A'}</div>
              <div><i class="fas fa-calendar text-primary me-2"></i><strong>Expira:</strong> ${dados.method_details.expiry || 'N/A'}</div>
            </div>
          `);
        } else if (dados.method === 'pix' && dados.method_details) {
          let html = `<div class="d-flex flex-wrap gap-3 p-3 bg-light rounded">
            <div><i class="fas fa-qrcode text-primary me-2"></i><strong>Tipo:</strong> ${dados.method_details.description || 'PIX Instantâneo'}</div>`;
          if (dados.method_details.qr_code_image) {
            html += `<div><img src="${dados.method_details.qr_code_image}" alt="QR Code" class="rounded" style="height: 40px;"></div>`;
          }
          html += `</div>`;
          container.html(html);
        } else if (dados.method === 'billet' && dados.method_details) {
          let html = `<div class="d-flex flex-wrap gap-3 p-3 bg-light rounded">
            <div><i class="fas fa-barcode text-primary me-2"></i><strong>Tipo:</strong> ${dados.method_details.description || 'Boleto Bancário'}</div>`;
          if (dados.method_details.billet_url) {
            html += `<div><a href="${dados.method_details.billet_url}" target="_blank" class="btn btn-sm btn-primary">Ver Boleto</a></div>`;
          }
          html += `</div>`;
          container.html(html);
        }

      // Botões de ação
      $('#estornarBtn').data('transaction-id', dados.id).toggleClass('d-none', dados.status !== 'PAID_OUT');
      $('#cancelarBtn').data('transaction-id', dados.id).toggleClass('d-none', !['PENDING', 'PROCESSING'].includes(dados.status));

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
        'WAITING_FOR_APPROVAL': 'AGUARDANDO',
        'REFUNDED': 'ESTORNADO',
        'CANCELLED': 'CANCELADO',
        'MEDIATION': 'EM MEDIAÇÃO'
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
    const periodoSelect = document.getElementById('periodoSelect');
    const buscarInput = document.getElementById('buscarInput');
    const filtroForm = document.getElementById('filtroForm');
    const dateRangeModal = document.getElementById('dateRangeModal');
    const btnAplicar = document.getElementById('btnAplicarDatas');

    let dataInicio = null;
    let dataFim = null;

    // Formatador de data BR
    function formatarDataBr(data) {
      const meses = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
      const dia = String(data.getDate()).padStart(2, '0');
      const mes = meses[data.getMonth()];
      return `${dia} ${mes}`;
    }

    // Flatpickr - Data de Início
    flatpickr('#calendarInicio', {
      inline: true,
      locale: 'pt',
      dateFormat: 'Y-m-d',
      onChange: function(selectedDates) {
        dataInicio = selectedDates[0];
      }
    });

    // Flatpickr - Data de Fim
    flatpickr('#calendarFim', {
      inline: true,
      locale: 'pt',
      dateFormat: 'Y-m-d',
      onChange: function(selectedDates) {
        dataFim = selectedDates[0];
      }
    });

    // Evento: Mudança no período
    periodoSelect.addEventListener('change', function() {
      if (this.value === 'personalizado') {
        const modal = new bootstrap.Modal(dateRangeModal);
        modal.show();
      } else {
        filtroForm.submit();
      }
    });

    // Evento: Busca com delay
    let searchTimeout;
    buscarInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        filtroForm.submit();
      }, 800);
    });

    // Evento: Aplicar período personalizado
    btnAplicar.addEventListener('click', function() {
      if (dataInicio && dataFim) {
        const inicioStr = dataInicio.toISOString().split('T')[0];
        const fimStr = dataFim.toISOString().split('T')[0];
        const texto = `${formatarDataBr(dataInicio)} – ${formatarDataBr(dataFim)}`;
        const valor = `${inicioStr}:${fimStr}`;

        // Remover opção anterior
        const opExistente = periodoSelect.querySelector('option[data-personalizado]');
        if (opExistente) opExistente.remove();

        // Criar nova opção
        const novaOpcao = document.createElement('option');
        novaOpcao.value = valor;
        novaOpcao.textContent = texto;
        novaOpcao.setAttribute('data-personalizado', '1');
        novaOpcao.selected = true;
        periodoSelect.appendChild(novaOpcao);

        // Fechar modal e submeter
        bootstrap.Modal.getInstance(dateRangeModal).hide();
        filtroForm.submit();
      } else {
        alert('Por favor, selecione ambas as datas.');
      }
    });

    // Restaurar período personalizado da URL
    const urlParams = new URLSearchParams(window.location.search);
    const periodoParam = urlParams.get('periodo');
    if (periodoParam && periodoParam.includes(':')) {
      const [inicioStr, fimStr] = periodoParam.split(':');
      const inicioDate = new Date(inicioStr);
      const fimDate = new Date(fimStr);
      
      const texto = `${formatarDataBr(inicioDate)} – ${formatarDataBr(fimDate)}`;
      const novaOpcao = document.createElement('option');
      novaOpcao.value = periodoParam;
      novaOpcao.textContent = texto;
      novaOpcao.setAttribute('data-personalizado', '1');
      novaOpcao.selected = true;
      periodoSelect.appendChild(novaOpcao);
    }

    // ==================== DATATABLE ====================
    const table = document.getElementById('table-pix-entradas');
    if (table) {
      const tbody = table.querySelector('tbody');
      const hasData = tbody && tbody.querySelectorAll('tr:not(.no-data-row)').length > 0;

      if (hasData) {
        // Inicializar DataTable
        const dataTable = $('#table-pix-entradas').DataTable({
          responsive: true,
          info: false,
          ordering: false,
          searching: false,
          lengthChange: false,
          language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
          },
          dom: '<"top"f>rt<"bottom"p><"clear">',
          drawCallback: function() {
            // Re-aplicar event listeners após cada re-desenho da tabela
            attachRowClickEvents();
          }
        });
      }

    }

    // ==================== FUNCOES LOCAIS ====================

    // Eventos dos botões de ação
    $(document).on('click', '#estornarBtn', function(e) {
      e.preventDefault();
      const transactionId = $(this).data('transaction-id');
      if (confirm('Tem certeza que deseja estornar esta transação? O valor será removido do seu saldo.')) {
        executarAcao(transactionId, 'estornar');
      }
    });

    $(document).on('click', '#cancelarBtn', function(e) {
      e.preventDefault();
      const transactionId = $(this).data('transaction-id');
      if (confirm('Tem certeza que deseja cancelar esta transação?')) {
        executarAcao(transactionId, 'cancelar');
      }
    });

    function executarAcao(transactionId, acao) {
      $.ajax({
        url: `/relatorio/entradas/${acao}/` + transactionId,
        method: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
          if (response.success) {
            alert(`Transação ${acao === 'estornar' ? 'estornada' : 'cancelada'} com sucesso!`);
            $('#detalhesModal').modal('hide');
            location.reload();
          } else {
            alert('Erro: ' + response.message);
          }
        },
        error: function() {
          alert(`Erro ao ${acao} transação`);
        }
      });
    }
  });
  </script>
</x-app-layout>
