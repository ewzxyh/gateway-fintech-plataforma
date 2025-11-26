<x-app-layout :route="'Splits Internos'">

  <div class="main-content app-content">
    <div class="container-fluid">
      <!-- Seção Principal -->
      <div id="secaoPrincipal">
        <!-- Page Header -->
      <div class="row mt-4 mb-5">
      <div class="col-12">
          <div class="card border-primary">
            <div class="card-body p-5">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                  <div class="icon-circle bg-primary text-white me-3" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-share-alt fa-lg"></i>
                  </div>
          <div>
                    <h1 class="display-6 fw-bold mb-1">Sistema de Splits Internos</h1>
                    <p class="text-muted mb-0">Gerencie as configurações de distribuição automática de valores</p>
                  </div>
                </div>
                <div class="d-flex gap-2">
                  <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                  </a>
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovaConfiguracao">
                    <i class="fas fa-plus me-1"></i> Nova Configuração
                  </button>
                  <button type="button" class="btn btn-info" id="btnMostrarHistorico">
                    <i class="fas fa-history me-1"></i> Histórico
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
          </div>

      <!-- Cards de Estatísticas -->
        @if(isset($estatisticas))
      <div class="row g-3 mb-4">
        <!-- Configurações Ativas -->
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle-modern bg-gradient-primary flex-shrink-0">
                  <i class="fa-solid fa-cogs"></i>
                  </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Configurações Ativas</p>
                  <h4 class="fw-bold mb-0 text-primary text-truncate" style="font-size: 1.35rem;">{{ $estatisticas['total_configuracoes_ativas'] ?? 0 }}</h4>
                </div>
              </div>
            </div>
          </div>
                  </div>

        <!-- Splits Executados -->
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-exchange-alt"></i>
                  </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Splits Executados</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">{{ $estatisticas['total_splits_executados'] ?? 0 }}</h4>
                </div>
              </div>
            </div>
          </div>
                  </div>

        <!-- Valor Total Distribuído -->
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fa-solid fa-dollar-sign"></i>
                  </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Valor Total Distribuído</p>
                  <h4 class="fw-bold mb-0 text-info text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($estatisticas['total_valor_distribuido'] ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
                  </div>

        <!-- Movimento deste Mês -->
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle-modern bg-gradient-warning flex-shrink-0">
                  <i class="fa-solid fa-calendar-alt"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Movimento deste Mês</p>
                  <h4 class="fw-bold mb-0 text-warning text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($estatisticas['movimento_mes_atual'] ?? 0, 2, ',', '.') }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif

        <!-- Lista de Configurações -->
      <div class="row mb-4">
        <div class="col-12">
        <div class="card">
          <div class="card-header">
              <h5 class="mb-0">
                <i class="fa-solid fa-list text-primary me-2"></i>
                Configurações de Splits Internos
              </h5>
          </div>
            <div class="card-body p-4">
            @if($configuracoes->count() > 0)
              <div class="table-responsive">
                  <table class="table text-nowrap">
                  <thead>
                    <tr>
                        <th><i class="fa-solid fa-hashtag text-primary me-1"></i>ID</th>
                        <th><i class="fa-solid fa-user text-info me-1"></i>Usuário Pagador</th>
                        <th><i class="fa-solid fa-user-check text-success me-1"></i>Usuário Beneficiário</th>
                        <th><i class="fa-solid fa-tag text-warning me-1"></i>Tipo de Taxa</th>
                        <th><i class="fa-solid fa-percent text-primary me-1"></i>Porcentagem</th>
                        <th class="text-center"><i class="fa-solid fa-circle-check text-success me-1"></i>Status</th>
                        <th><i class="fa-solid fa-user-cog text-secondary me-1"></i>Criado Por</th>
                        <th><i class="fa-solid fa-calendar text-info me-1"></i>Criado Em</th>
                        <th class="text-center">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($configuracoes as $config)
                    <tr>
                      <td><span class="badge bg-secondary">#{{ $config->id }}</span></td>
                      <td>
                        <div>
                          <strong>{{ $config->usuarioPagador->name }}</strong><br>
                          <small class="text-muted">{{ $config->usuarioPagador->email }}</small>
                        </div>
                      </td>
                      <td>
                        <div>
                          <strong>{{ $config->usuarioBeneficiario->name }}</strong><br>
                          <small class="text-muted">{{ $config->usuarioBeneficiario->email }}</small>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-info">
                          <i class="fas fa-tag"></i>
                          {{ $config->tipoTaxaFormatado }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-primary">
                          <i class="fas fa-percentage"></i>
                          {{ number_format($config->porcentagem_split, 2) }}%
                        </span>
                      </td>
                        <td class="text-center">
                        @if($config->ativo)
                          <span class="badge bg-success">
                            <i class="fas fa-check"></i> Ativo
                          </span>
                        @else
                          <span class="badge bg-secondary">
                            <i class="fas fa-pause"></i> Inativo
                          </span>
                        @endif
                      </td>
                      <td>{{ $config->criadoPorAdmin->name }}</td>
                      <td>{{ $config->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                        <div class="btn-group" role="group">
                          <button type="button" 
                              class="btn btn-sm btn-outline-primary"
                              title="Ver detalhes"
                              data-bs-toggle="tooltip"
                              data-configuracao-id="{{ $config->id }}">
                            <i class="fas fa-eye"></i>
                          </button>
                          <button type="button" 
                              class="btn btn-sm btn-outline-info"
                              title="Editar"
                              data-bs-toggle="tooltip"
                              data-configuracao-id="{{ $config->id }}"
                              onclick="abrirModalEdicao({{ $config->id }})">
                            <i class="fas fa-edit"></i>
                          </button>
                          <form action="{{ route('admin.splits-internos.toggle-status', $config->id) }}" 
                             method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" 
                                class="btn btn-sm {{ $config->ativo ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                title="{{ $config->ativo ? 'Desativar' : 'Ativar' }}"
                                  data-bs-toggle="tooltip"
                                onclick="return confirm('Tem certeza?')">
                              <i class="fas {{ $config->ativo ? 'fa-pause' : 'fa-play' }}"></i>
                            </button>
                          </form>
                          <form action="{{ route('admin.splits-internos.destroy', $config->id) }}" 
                             method="POST" style="display: inline;" 
                             onsubmit="return confirm('Tem certeza que deseja excluir esta configuração?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="btn btn-sm btn-outline-danger" 
                                title="Excluir configuração" 
                                data-bs-toggle="tooltip">
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              
              <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                {{ $configuracoes->links() }}
              </div>
            @else
                <!-- Empty State -->
              <div class="text-center py-5">
                  <div class="icon-circle bg-light text-muted mx-auto mb-4" style="width: 80px; height: 80px;">
                    <i class="fa-solid fa-share-alt fa-2x"></i>
                  </div>
                  <h5 class="fw-semibold mb-2">Nenhuma configuração de split interno encontrada</h5>
                  <p class="text-muted mb-4">Clique em "Nova Configuração" para criar a primeira configuração.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovaConfiguracao">
                    <i class="fas fa-plus me-2"></i>Criar Primeira Configuração
                </button>
              </div>
            @endif
          </div>
        </div>
      </div>
      </div> <!-- Fechamento da seçãoPrincipal -->

      <!-- Seção de Histórico (inicialmente oculta) -->
      <div id="secaoHistorico" class="d-none">
        <!-- Header do Histórico -->
        <div class="row mt-4 mb-5">
          <div class="col-12">
            <div class="card border-info">
              <div class="card-body p-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="d-flex align-items-center">
                    <div class="icon-circle bg-info text-white me-3" style="width: 64px; height: 64px;">
                      <i class="fa-solid fa-history fa-lg"></i>
                    </div>
                    <div>
                      <h1 class="display-6 fw-bold mb-1">Histórico de Splits Executados</h1>
                      <p class="text-muted mb-0">Acompanhe todas as execuções de splits internos</p>
                    </div>
                  </div>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" id="btnVoltarConfiguracoes">
                      <i class="fas fa-arrow-left me-1"></i> Voltar às Configurações
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filtros do Histórico -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">
                  <i class="fa-solid fa-filter text-primary me-2"></i>
                  Filtros do Histórico
                </h5>
              </div>
              <div class="card-body p-4">
                <form id="formFiltrosHistorico">
                  <div class="row g-3">
                    <div class="col-md-3">
                      <label for="filtro_status" class="form-label">
                        <i class="fa-solid fa-circle-check text-success me-1"></i> Status
                      </label>
                      <select class="form-select" id="filtro_status" name="status">
                        <option value="">Todos</option>
                        <option value="pendente">Pendente</option>
                        <option value="processado">Processado</option>
                        <option value="falhado">Falhado</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label for="filtro_data_inicio" class="form-label">
                        <i class="fa-solid fa-calendar-alt text-info me-1"></i> Data Início
                      </label>
                      <input type="date" class="form-control" id="filtro_data_inicio" name="data_inicio">
                    </div>
                    <div class="col-md-3">
                      <label for="filtro_data_fim" class="form-label">
                        <i class="fa-solid fa-calendar-times text-warning me-1"></i> Data Fim
                      </label>
                      <input type="date" class="form-control" id="filtro_data_fim" name="data_fim">
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">&nbsp;</label>
                      <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" id="btnFiltrarHistorico">
                          <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-secondary" id="btnLimparFiltros">
                          <i class="fas fa-times me-1"></i> Limpar
                        </button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Cards de Estatísticas do Histórico -->
        <div class="row g-3 mb-4" id="cardsEstatisticasHistorico">
          <!-- Serão carregados via AJAX -->
        </div>

        <!-- Lista de Splits Executados -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">
                  <i class="fa-solid fa-list text-primary me-2"></i>
                  Splits Executados
                </h5>
              </div>
              <div class="card-body p-4">
                <div id="loadingHistorico" class="text-center py-5 d-none">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                  </div>
                  <p class="mt-3 text-muted">Carregando histórico...</p>
                </div>
                
                <div id="conteudoHistorico">
                  <!-- Conteúdo será carregado via AJAX -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Nova Configuração -->
  <div class="modal fade" id="modalNovaConfiguracao" tabindex="-1" aria-labelledby="modalNovaConfiguracaoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalNovaConfiguracaoLabel">
            <i class="fas fa-plus text-primary me-2"></i>
            Nova Configuração de Split Interno
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formNovaConfiguracao" action="{{ route('admin.splits-internos.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <div id="alertErrors" class="alert alert-danger d-none">
              <ul class="mb-0" id="errorList"></ul>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="usuario_pagador_id" class="form-label">
                  <i class="fas fa-user-times text-danger me-1"></i> Usuário Pagador <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="usuario_pagador_id" name="usuario_pagador_id" required>
                  <option value="">Selecione o usuário pagador</option>
                </select>
                <div class="form-text">Este usuário terá suas taxas compartilhadas.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="usuario_beneficiario_id" class="form-label">
                  <i class="fas fa-user-check text-success me-1"></i> Usuário Beneficiário <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="usuario_beneficiario_id" name="usuario_beneficiario_id" required>
                  <option value="">Selecione o usuário beneficiário</option>
                </select>
                <div class="form-text">Este usuário receberá a porcentagem das taxas.</div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="porcentagem_split" class="form-label">
                  <i class="fas fa-percentage text-primary me-1"></i> Porcentagem do Split <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <input type="number" 
                      class="form-control" 
                      id="porcentagem_split" 
                      name="porcentagem_split" 
                      step="0.01" 
                      min="0.01" 
                      max="100" 
                      required>
                  <span class="input-group-text">%</span>
                </div>
                <div class="form-text">Porcentagem das taxas que será enviada como split interno.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="tipo_taxa" class="form-label">
                  <i class="fas fa-tag text-warning me-1"></i> Tipo de Taxa <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="tipo_taxa" name="tipo_taxa" required>
                  <option value="">Selecione o tipo de taxa</option>
                  <option value="deposito">Depósito</option>
                  <option value="saque_pix">Saque PIX</option>
                  <option value="carteira_gateway">Carteira Gateway</option>
                </select>
                <div class="form-text">
                  Tipo de transação onde o split será aplicado.
                  <br><small class="text-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Carteira Gateway:</strong> Aplica nova lógica baseada no lucro bruto do gateway (taxaCashIn - taxaAdquirente)
                  </small>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="data_inicio" class="form-label">
                  <i class="fas fa-calendar-alt text-info me-1"></i> Data de Início
                </label>
                <input type="datetime-local" 
                    class="form-control" 
                    id="data_inicio" 
                    name="data_inicio">
                <div class="form-text">Data de início da configuração (opcional).</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="data_fim" class="form-label">
                  <i class="fas fa-calendar-times text-secondary me-1"></i> Data de Fim
                </label>
                <input type="datetime-local" 
                    class="form-control" 
                    id="data_fim" 
                    name="data_fim">
                <div class="form-text">Data de fim da configuração (opcional).</div>
              </div>
            </div>

            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" 
                    type="checkbox" 
                    id="ativo" 
                    name="ativo" 
                    value="1" 
                    checked>
                <label class="form-check-label" for="ativo">
                  <i class="fas fa-power-off text-success me-1"></i> Configuração Ativa
                </label>
              </div>
              <div class="form-text">Marque esta opção para ativar a configuração imediatamente.</div>
            </div>

            <div class="alert alert-info">
              <i class="fas fa-info-circle text-info me-2"></i>
              <strong>Como funciona:</strong><br>
              Quando o usuário pagador realizar uma transação (depósito ou saque PIX), 
              uma porcentagem da taxa cobrada será automaticamente creditada na conta do usuário beneficiário.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i> Cancelar
            </button>
            <button type="submit" class="btn btn-primary" id="btnSalvar">
              <i class="fas fa-save me-1"></i> Criar Configuração
            </button>
          </div>
        </form>
    </div>
  </div>
</div>

<!-- Modal Detalhes da Configuração -->
<div class="modal fade" id="modalDetalhesConfiguracao" tabindex="-1" aria-labelledby="modalDetalhesConfiguracaoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetalhesConfiguracaoLabel">
          <i class="fas fa-eye text-primary me-2"></i>
          Detalhes da Configuração de Split Interno
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalDetalhesBody">
        <!-- Conteúdo será carregado via AJAX -->
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
          </div>
          <p class="mt-3 text-muted">Carregando detalhes...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Fechar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar Configuração -->
<div class="modal fade" id="modalEditarConfiguracao" tabindex="-1" aria-labelledby="modalEditarConfiguracaoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarConfiguracaoLabel">
          <i class="fas fa-edit text-primary me-2"></i>
          Editar Configuração de Split Interno
          <small class="text-muted ms-2" id="modalDebugInfo"></small>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditarConfiguracao" method="POST" action="">
        @csrf
        <div class="modal-body">
          <div id="alertErrorsEdit" class="alert alert-danger d-none">
            <ul class="mb-0" id="errorListEdit"></ul>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="edit_usuario_pagador_id" class="form-label">
                <i class="fas fa-user-times text-danger me-1"></i> Usuário Pagador <span class="text-danger">*</span>
              </label>
              <select class="form-select" id="edit_usuario_pagador_id" name="usuario_pagador_id" required>
                <option value="">Selecione o usuário pagador</option>
              </select>
              <div class="form-text">Este usuário terá suas taxas compartilhadas.</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="edit_usuario_beneficiario_id" class="form-label">
                <i class="fas fa-user-check text-success me-1"></i> Usuário Beneficiário <span class="text-danger">*</span>
              </label>
              <select class="form-select" id="edit_usuario_beneficiario_id" name="usuario_beneficiario_id" required>
                <option value="">Selecione o usuário beneficiário</option>
              </select>
              <div class="form-text">Este usuário receberá a porcentagem das taxas.</div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="edit_porcentagem_split" class="form-label">
                <i class="fas fa-percentage text-primary me-1"></i> Porcentagem do Split <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <input type="number" 
                    class="form-control" 
                    id="edit_porcentagem_split" 
                    name="porcentagem_split" 
                    step="0.01" 
                    min="0.01" 
                    max="100" 
                    required>
                <span class="input-group-text">%</span>
              </div>
              <div class="form-text">Porcentagem das taxas que será enviada como split interno.</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="edit_tipo_taxa" class="form-label">
                <i class="fas fa-tag text-warning me-1"></i> Tipo de Taxa <span class="text-danger">*</span>
              </label>
              <select class="form-select" id="edit_tipo_taxa" name="tipo_taxa" required>
                <option value="">Selecione o tipo de taxa</option>
                <option value="deposito">Depósito</option>
                <option value="saque_pix">Saque PIX</option>
                <option value="carteira_gateway">Carteira Gateway</option>
              </select>
              <div class="form-text">Tipo de transação onde o split será aplicado.</div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="edit_data_inicio" class="form-label">
                <i class="fas fa-calendar-alt text-info me-1"></i> Data de Início
              </label>
              <input type="datetime-local" 
                  class="form-control" 
                  id="edit_data_inicio" 
                  name="data_inicio">
              <div class="form-text">Data de início da configuração (opcional).</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="edit_data_fim" class="form-label">
                <i class="fas fa-calendar-times text-secondary me-1"></i> Data de Fim
              </label>
              <input type="datetime-local" 
                  class="form-control" 
                  id="edit_data_fim" 
                  name="data_fim">
              <div class="form-text">Data de fim da configuração (opcional).</div>
            </div>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" 
                  type="checkbox" 
                  id="edit_ativo" 
                  name="ativo" 
                  value="1">
              <label class="form-check-label" for="edit_ativo">
                <i class="fas fa-power-off text-success me-1"></i> Configuração Ativa
              </label>
            </div>
            <div class="form-text">Marque esta opção para ativar a configuração.</div>
          </div>

          <div class="alert alert-info">
            <i class="fas fa-info-circle text-info me-2"></i>
            <strong>Como funciona:</strong><br>
            Quando o usuário pagador realizar uma transação (depósito, saque PIX ou carteira gateway), 
            uma porcentagem da taxa cobrada será automaticamente creditada na conta do usuário beneficiário.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" id="btnAtualizar">
            <i class="fas fa-save me-1"></i> Atualizar Configuração
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    // Inicializar tooltips
    document.addEventListener('DOMContentLoaded', function() {
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });

      // Carregar usuários quando o modal abrir
      $('#modalNovaConfiguracao').on('show.bs.modal', function() {
        carregarUsuarios();
      });

      // Limpar formulário quando o modal fechar
      $('#modalNovaConfiguracao').on('hidden.bs.modal', function() {
        limparFormulario();
      });

      // Configurar modal de detalhes
      $('#modalDetalhesConfiguracao').on('show.bs.modal', function(event) {
        const configuracaoId = window.configuracaoIdAtual;
        
        if (configuracaoId) {
          carregarDetalhesConfiguracao(configuracaoId);
        }
      });

      // Adicionar event listener direto nos botões de detalhes
      $(document).on('click', 'button[data-configuracao-id]', function(e) {
        const configuracaoId = $(this).data('configuracao-id');
        if (configuracaoId) {
          // Armazenar o ID globalmente para usar no modal
          window.configuracaoIdAtual = configuracaoId;
          
          $('#modalDetalhesConfiguracao').modal('show');
        }
      });


      // Limpar conteúdo quando o modal de detalhes fechar
      $('#modalDetalhesConfiguracao').on('hidden.bs.modal', function() {
        $('#modalDetalhesBody').html(`
          <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-3 text-muted">Carregando detalhes...</p>
          </div>
        `);
        
        // Limpar variável global
        window.configuracaoIdAtual = null;
      });

      // Configurar modal de edição
      $('#modalEditarConfiguracao').on('show.bs.modal', function() {
        limparErrosEdicao();
        
        // Garantir que o formulário não tenha action com parâmetros de query
        const currentAction = $('#formEditarConfiguracao').attr('action');
        if (currentAction && currentAction.includes('?')) {
          const cleanAction = currentAction.split('?')[0];
          console.log('Limpando action do formulário:', cleanAction);
          $('#formEditarConfiguracao').attr('action', cleanAction);
        }
      });

      // Limpar formulário quando o modal de edição fechar
      $('#modalEditarConfiguracao').on('hidden.bs.modal', function() {
        limparErrosEdicao();
        window.configuracaoIdEdicao = null;
      });

      // Validação de usuários diferentes no modal de edição
      $('#edit_usuario_pagador_id, #edit_usuario_beneficiario_id').on('change', function() {
        validarUsuariosEdicao();
      });

      // Validação de datas no modal de edição
      $('#edit_data_inicio, #edit_data_fim').on('change', function() {
        const dataInicio = $('#edit_data_inicio').val();
        const dataFim = $('#edit_data_fim').val();
        
        if (dataInicio && dataFim && dataInicio > dataFim) {
          mostrarErroEdicao('A data de fim deve ser posterior à data de início.');
          $('#edit_data_fim').val('');
        }
      });

      // Controle de navegação dinâmica
      $('#btnMostrarHistorico').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        mostrarHistorico();
      });

      $('#btnVoltarConfiguracoes').on('click', function() {
        voltarConfiguracoes();
      });

      $('#btnFiltrarHistorico').on('click', function() {
        carregarHistorico();
      });

      $('#btnLimparFiltros').on('click', function() {
        limparFiltrosHistorico();
      });
    });

    // Função para carregar usuários via AJAX
    function carregarUsuarios() {
      $.ajax({
        url: '{{ route("admin.splits-internos.buscar-usuario") }}',
        method: 'GET',
        data: { q: '' },
        success: function(data) {
          const selectPagador = $('#usuario_pagador_id');
          const selectBeneficiario = $('#usuario_beneficiario_id');
          
          // Limpar opções existentes (exceto a primeira)
          selectPagador.find('option:not(:first)').remove();
          selectBeneficiario.find('option:not(:first)').remove();
          
          // Adicionar usuários
          data.forEach(function(usuario) {
            const option = `<option value="${usuario.id}">${usuario.text}</option>`;
            selectPagador.append(option);
            selectBeneficiario.append(option);
          });
        },
        error: function() {
          console.error('Erro ao carregar usuários');
        }
      });
    }

    // Função para limpar formulário
    function limparFormulario() {
      $('#formNovaConfiguracao')[0].reset();
      $('#alertErrors').addClass('d-none');
      $('#errorList').empty();
      $('#usuario_pagador_id, #usuario_beneficiario_id').val('').trigger('change');
    }

    // Função para abrir modal de edição
    function abrirModalEdicao(configuracaoId) {
      console.log('Abrindo modal de edição para ID:', configuracaoId);
      
      // Armazenar o ID globalmente
      window.configuracaoIdEdicao = configuracaoId;
      
      // Carregar dados da configuração
      carregarDadosEdicao(configuracaoId);
      
      // Abrir modal
      $('#modalEditarConfiguracao').modal('show');
    }

    // Função para carregar dados da configuração para edição
    function carregarDadosEdicao(configuracaoId) {
      // Usar o ID armazenado globalmente se não foi passado como parâmetro
      const id = configuracaoId || window.configuracaoIdEdicao;
      
      console.log('Carregando dados para configuração ID:', id);
      
      $.ajax({
        url: `/admin/ajustes/splits-internos/${id}`,
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        success: function(response) {
          console.log('Resposta recebida:', response);
          if (response.success) {
            const config = response.splitInterno;
            
            // Armazenar valores dos usuários antes de carregar os selects
            const pagadorId = config.usuario_pagador_id;
            const beneficiarioId = config.usuario_beneficiario_id;
            
            // Preencher campos que não dependem dos selects
            $('#edit_porcentagem_split').val(config.porcentagem_split);
            $('#edit_tipo_taxa').val(config.tipo_taxa);
            $('#edit_ativo').prop('checked', config.ativo);
            
            // Preencher datas (converter para formato datetime-local)
            if (config.data_inicio) {
              const dataInicio = new Date(config.data_inicio);
              $('#edit_data_inicio').val(dataInicio.toISOString().slice(0, 16));
            }
            if (config.data_fim) {
              const dataFim = new Date(config.data_fim);
              $('#edit_data_fim').val(dataFim.toISOString().slice(0, 16));
            }
            
            // Atualizar action do formulário
            const actionUrl = `/admin/ajustes/splits-internos/${id}`;
            console.log('Definindo action do formulário:', actionUrl);
            $('#formEditarConfiguracao').attr('action', actionUrl);
            
            // Atualizar informações de debug no modal
            $('#modalDebugInfo').text(`ID: ${id} | Action: ${actionUrl}`);
            
            // Garantir que não há parâmetros de query na URL
            const currentUrl = window.location.origin + actionUrl;
            console.log('URL completa do formulário:', currentUrl);
            
            // Carregar usuários nos selects e depois preencher
            carregarUsuariosEdicao(pagadorId, beneficiarioId);
          } else {
            console.error('Resposta sem sucesso:', response);
            mostrarErroEdicao('Erro na resposta do servidor.');
          }
        },
        error: function(xhr) {
          console.error('Erro ao carregar dados:', xhr);
          console.error('Status:', xhr.status);
          console.error('Response:', xhr.responseText);
          mostrarErroEdicao('Erro ao carregar dados da configuração.');
        }
      });
    }

    // Função para carregar usuários no modal de edição
    function carregarUsuariosEdicao(pagadorId, beneficiarioId) {
      $.ajax({
        url: '{{ route("admin.splits-internos.buscar-usuario") }}',
        method: 'GET',
        data: { q: '' },
        success: function(data) {
          console.log('Usuários carregados:', data);
          
          const selectPagador = $('#edit_usuario_pagador_id');
          const selectBeneficiario = $('#edit_usuario_beneficiario_id');
          
          // Limpar opções existentes (exceto a primeira)
          selectPagador.find('option:not(:first)').remove();
          selectBeneficiario.find('option:not(:first)').remove();
          
          // Adicionar usuários
          data.forEach(function(usuario) {
            const option = `<option value="${usuario.id}">${usuario.name} (${usuario.email}) - ${usuario.user_id}</option>`;
            selectPagador.append(option);
            selectBeneficiario.append(option);
          });
          
          // Preencher valores selecionados
          if (pagadorId) {
            selectPagador.val(pagadorId);
          }
          if (beneficiarioId) {
            selectBeneficiario.val(beneficiarioId);
          }
        },
        error: function(xhr) {
          console.error('Erro ao carregar usuários:', xhr);
          console.error('Status:', xhr.status);
          console.error('Response:', xhr.responseText);
          mostrarErroEdicao('Erro ao carregar usuários.');
        }
      });
    }

    // Função para mostrar erro no modal de edição
    function mostrarErroEdicao(mensagem) {
      $('#alertErrorsEdit').removeClass('d-none');
      $('#errorListEdit').html(`<li>${mensagem}</li>`);
    }

    // Função para limpar erros do modal de edição
    function limparErrosEdicao() {
      $('#alertErrorsEdit').addClass('d-none');
      $('#errorListEdit').empty();
    }

    // Validação de usuários diferentes no modal de edição
    function validarUsuariosEdicao() {
      const pagador = $('#edit_usuario_pagador_id').val();
      const beneficiario = $('#edit_usuario_beneficiario_id').val();
      
      if (pagador && beneficiario && pagador === beneficiario) {
        mostrarErroEdicao('O usuário pagador deve ser diferente do usuário beneficiário!');
        return false;
      }
      return true;
    }

    // Validação de usuários diferentes
    function validarUsuarios() {
      const pagador = $('#usuario_pagador_id').val();
      const beneficiario = $('#usuario_beneficiario_id').val();
      
      if (pagador && beneficiario && pagador === beneficiario) {
        mostrarErro('O usuário pagador deve ser diferente do usuário beneficiário!');
        return false;
      }
      return true;
    }

    // Função para mostrar erros
    function mostrarErro(mensagem) {
      $('#alertErrors').removeClass('d-none');
      $('#errorList').html(`<li>${mensagem}</li>`);
    }

    // Função para esconder erros
    function esconderErros() {
      $('#alertErrors').addClass('d-none');
      $('#errorList').empty();
    }

    // Mostrar informações adicionais quando Carteira Gateway for selecionado
    $('#tipo_taxa').on('change', function() {
      const tipoTaxa = $(this).val();
      const infoDiv = $('#infoCarteiraGateway');
      
      if (tipoTaxa === 'carteira_gateway') {
        if (infoDiv.length === 0) {
          $('#tipo_taxa').closest('.mb-3').after(`
            <div id="infoCarteiraGateway" class="alert alert-info mb-3">
              <h6><i class="fas fa-lightbulb me-2"></i>Nova Lógica da Carteira Gateway</h6>
              <p class="mb-2">Quando selecionar "Carteira Gateway", o sistema aplicará automaticamente a nova lógica:</p>
              <ul class="mb-0">
                <li><strong>Cálculo:</strong> Split baseado no valor total do depósito</li>
                <li><strong>Lucro Bruto:</strong> Taxa Cash In - Taxa Adquirente</li>
                <li><strong>Exemplo:</strong> Depósito R$ 100,00 → Split 8% = R$ 8,00</li>
                <li><strong>Automático:</strong> Detecta carteira gateway e aplica nova lógica</li>
              </ul>
            </div>
          `);
        }
      } else {
        infoDiv.remove();
      }
    });

    // Submissão do formulário via AJAX
    $('#formNovaConfiguracao').on('submit', function(e) {
      e.preventDefault();
      
      if (!validarUsuarios()) {
        return;
      }

      esconderErros();
      
      const formData = $(this).serialize();
      const btnSalvar = $('#btnSalvar');
      const originalText = btnSalvar.html();
      
      // Desabilitar botão e mostrar loading
      btnSalvar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Salvando...');

      $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          // Fechar modal
          $('#modalNovaConfiguracao').modal('hide');
          
          // Mostrar mensagem de sucesso
          if (typeof showToast === 'function') {
            showToast('success', 'Configuração de split interno criada com sucesso!');
          }
          
          // Recarregar página após um pequeno delay
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        },
        error: function(xhr) {
          if (xhr.status === 422) {
            // Erros de validação
            const errors = xhr.responseJSON.errors;
            let errorHtml = '';
            
            Object.keys(errors).forEach(function(key) {
              errors[key].forEach(function(error) {
                errorHtml += `<li>${error}</li>`;
              });
            });
            
            $('#errorList').html(errorHtml);
            $('#alertErrors').removeClass('d-none');
          } else {
            // Erro geral
            mostrarErro('Erro ao criar configuração. Tente novamente.');
          }
        },
        complete: function() {
          // Reabilitar botão
          btnSalvar.prop('disabled', false).html(originalText);
        }
      });
    });

    // Submissão do formulário de edição via AJAX
    $('#formEditarConfiguracao').on('submit', function(e) {
      e.preventDefault();
      
      // Verificar se o action está definido
      const currentAction = $(this).attr('action');
      console.log('Action atual do formulário:', currentAction);
      
      // Atualizar informações de debug no modal
      $('#modalDebugInfo').text(`Action: ${currentAction}`);
      
      if (!currentAction || currentAction === '' || currentAction.includes('?')) {
        console.error('Action do formulário não está definido corretamente:', currentAction);
        mostrarErroEdicao('Erro: URL do formulário não está definida corretamente.');
        return;
      }
      
      // Garantir que a URL não tem parâmetros de query
      const cleanAction = currentAction.split('?')[0];
      if (cleanAction !== currentAction) {
        console.log('Removendo parâmetros de query da URL:', cleanAction);
        $(this).attr('action', cleanAction);
      }
      
      // Log adicional para debug
      console.log('URL final que será usada:', $(this).attr('action'));
      
      if (!validarUsuariosEdicao()) {
        return;
      }

      limparErrosEdicao();
      
      const formData = $(this).serialize();
      const btnAtualizar = $('#btnAtualizar');
      const originalText = btnAtualizar.html();
      
      // Desabilitar botão e mostrar loading
      btnAtualizar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Atualizando...');

      // Usar rota POST separada para evitar problemas com PUT
      const url = currentAction.replace('/splits-internos/', '/splits-internos/') + '/update';
      const method = 'POST';
      const token = $('meta[name="csrf-token"]').attr('content');
      
      console.log('🚀 Enviando requisição FETCH (POST para rota separada):');
      console.log('URL:', url);
      console.log('Método:', method);
      console.log('Dados:', formData);
      
      fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-CSRF-TOKEN': token,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      })
      .then(response => {
        console.log('📡 Resposta recebida:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('✅ Dados recebidos:', data);
        // Fechar modal
        $('#modalEditarConfiguracao').modal('hide');
        
        // Mostrar mensagem de sucesso
        if (data.success) {
          showToast('success', data.message || 'Configuração atualizada com sucesso!');
          
          // Recarregar a página para mostrar as alterações
          setTimeout(() => {
            window.location.reload();
          }, 1500);
        } else {
          showToast('error', data.message || 'Erro ao atualizar configuração.');
        }
      })
      .catch(error => {
        console.error('❌ Erro na requisição:', error);
        mostrarErroEdicao('Erro na comunicação com o servidor: ' + error.message);
      })
      .finally(() => {
        // Reabilitar botão
        btnAtualizar.prop('disabled', false).html(originalText);
      });
    });

    // Funções de navegação dinâmica
    function mostrarHistorico() {
      
      try {
        const secaoPrincipal = document.getElementById('secaoPrincipal');
        
        if (!secaoPrincipal) {
          console.error('Seção principal não encontrada!');
          return;
        }
        
        
        // Substituir o conteúdo da seção principal pelo histórico
        secaoPrincipal.innerHTML = `
          <!-- Header do Histórico -->
          <div class="row mt-4 mb-5">
            <div class="col-12">
              <div class="card border-info">
                <div class="card-body p-5">
                  <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                      <div class="icon-circle bg-info text-white me-3" style="width: 64px; height: 64px;">
                        <i class="fa-solid fa-history fa-2x"></i>
                      </div>
                      <div>
                        <h4 class="fw-bold mb-1 text-dark">Histórico de Splits Executados</h4>
                        <p class="text-muted mb-0">Visualize todos os splits internos que foram processados</p>
                      </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary" id="btnVoltarConfiguracoes">
                      <i class="fas fa-arrow-left me-1"></i> Voltar às Configurações
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Cards de Estatísticas do Histórico -->
          <div class="row mb-4" id="cardsEstatisticasHistorico">
            <!-- Cards serão inseridos aqui via JavaScript -->
          </div>

          <!-- Filtros do Histórico -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card shadow-sm">
                <div class="card-body">
                  <form id="formFiltrosHistorico">
                    <div class="row g-3">
                      <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="filtro_status">
                          <option value="">Todos os Status</option>
                          <option value="pendente">Pendente</option>
                          <option value="processado">Processado</option>
                          <option value="falhado">Falhado</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Início</label>
                        <input type="date" class="form-control" id="filtro_data_inicio">
                      </div>
                      <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Fim</label>
                        <input type="date" class="form-control" id="filtro_data_fim">
                      </div>
                      <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary me-2" id="btnFiltrarHistorico">
                          <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnLimparFiltros">
                          <i class="fas fa-times me-1"></i> Limpar
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Loading -->
          <div id="loadingHistorico" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-3 text-muted">Carregando histórico...</p>
          </div>

          <!-- Conteúdo do Histórico -->
          <div id="conteudoHistorico">
            <!-- Conteúdo será inserido aqui via JavaScript -->
          </div>
        `;
        
        
        // Reconfigurar event listeners para os novos elementos
        document.getElementById('btnVoltarConfiguracoes').addEventListener('click', voltarConfiguracoes);
        document.getElementById('btnFiltrarHistorico').addEventListener('click', carregarHistorico);
        document.getElementById('btnLimparFiltros').addEventListener('click', limparFiltrosHistorico);
        
        // Carregar histórico
        carregarHistorico();
        
        // Scroll para o topo
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
      } catch (error) {
        console.error('Erro ao mostrar histórico:', error);
      }
    }

    function voltarConfiguracoes() {
      // Recarregar a página para voltar ao estado original
      window.location.reload();
    }

    function carregarHistorico() {
      
      try {
        const loadingDiv = document.getElementById('loadingHistorico');
        const conteudoDiv = document.getElementById('conteudoHistorico');
        const cardsDiv = document.getElementById('cardsEstatisticasHistorico');
        
        
        
        // Mostrar loading
        loadingDiv.classList.remove('d-none');
        conteudoDiv.innerHTML = '';
        cardsDiv.innerHTML = '';
        
        // Coletar filtros
        const filtros = {
          status: document.getElementById('filtro_status')?.value || '',
          data_inicio: document.getElementById('filtro_data_inicio')?.value || '',
          data_fim: document.getElementById('filtro_data_fim')?.value || ''
        };
        
        
        // Carregar histórico real via AJAX
        fetch('{{ route("admin.splits-internos.historico") }}', {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          
          try {
            renderizarCardsEstatisticas(data.estatisticas);
            
            renderizarConteudoHistorico(data.splitsExecutados);
            
          } catch (renderError) {
            console.error('Erro durante renderização:', renderError);
          }
        })
        .catch(error => {
          console.error('Erro ao carregar histórico:', error);
          conteudoDiv.innerHTML = `
            <div class="text-center py-5">
              <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
              <h5 class="text-danger">Erro ao carregar histórico</h5>
              <p class="text-muted">Erro: ${error.message}</p>
              <button class="btn btn-primary" onclick="carregarHistorico()">
                <i class="fas fa-refresh me-1"></i> Tentar Novamente
              </button>
            </div>
          `;
        })
        .finally(() => {
          loadingDiv.classList.add('d-none');
          
          // Teste adicional para verificar se o conteúdo está visível
          setTimeout(() => {
            const secaoHistorico = document.getElementById('secaoHistorico');
            const conteudoDiv = document.getElementById('conteudoHistorico');
            const cardsDiv = document.getElementById('cardsEstatisticasHistorico');
            
            
            // Forçar visibilidade se necessário
            if (secaoHistorico.offsetHeight === 0) {
              secaoHistorico.style.display = 'block';
              secaoHistorico.style.visibility = 'visible';
              secaoHistorico.style.opacity = '1';
              secaoHistorico.style.height = 'auto';
              secaoHistorico.style.minHeight = '500px';
              secaoHistorico.style.backgroundColor = '#f8f9fa';
              secaoHistorico.style.border = '2px solid #007bff';
              secaoHistorico.style.padding = '20px';
              secaoHistorico.style.position = 'relative';
              secaoHistorico.style.zIndex = '9999';
              
            }
          }, 1000);
        });
        
      } catch (error) {
        console.error('Erro geral ao carregar histórico:', error);
        const conteudoDiv = document.getElementById('conteudoHistorico');
        if (conteudoDiv) {
          conteudoDiv.innerHTML = `
            <div class="text-center py-5">
              <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
              <h5 class="text-danger">Erro ao carregar histórico</h5>
              <p class="text-muted">Erro: ${error.message}</p>
              <button class="btn btn-primary" onclick="carregarHistorico()">
                <i class="fas fa-refresh me-1"></i> Tentar Novamente
              </button>
            </div>
          `;
        }
      }
    }

    function renderizarCardsEstatisticas(estatisticas) {
      
      try {
        const cardsDiv = document.getElementById('cardsEstatisticasHistorico');
        
        
        const cardsHtml = `
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-exchange-alt"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Executados</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">${estatisticas.total_executados || 0}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fa-solid fa-dollar-sign"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Valor Total Splits</p>
                  <h4 class="fw-bold mb-0 text-info text-truncate" style="font-size: 1.35rem;">R$ ${(estatisticas.total_valor_splits || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle-modern bg-gradient-warning flex-shrink-0">
                  <i class="fa-solid fa-percent"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Porcentagem Média</p>
                  <h4 class="fw-bold mb-0 text-warning text-truncate" style="font-size: 1.35rem;">${(estatisticas.porcentagem_media || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}%</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle-modern bg-gradient-danger flex-shrink-0">
                  <i class="fa-solid fa-exclamation-triangle"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Falhados</p>
                  <h4 class="fw-bold mb-0 text-danger text-truncate" style="font-size: 1.35rem;">${estatisticas.status_breakdown?.falhados || 0}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      
      cardsDiv.innerHTML = cardsHtml;
      
      
      } catch (error) {
        console.error('Erro em renderizarCardsEstatisticas:', error);
      }
    }

    function renderizarConteudoHistorico(splitsExecutados) {
      
      try {
        const conteudoDiv = document.getElementById('conteudoHistorico');
        
        
        if (!splitsExecutados || splitsExecutados.length === 0) {
          conteudoDiv.innerHTML = `
          <div class="text-center py-5">
            <div class="icon-circle bg-light text-muted mx-auto mb-4" style="width: 80px; height: 80px;">
              <i class="fa-solid fa-history fa-2x"></i>
            </div>
            <h5 class="fw-semibold mb-2">Nenhum split executado encontrado</h5>
            <p class="text-muted mb-4">Configure alguns splits internos para ver o histórico aqui.</p>
          </div>
        `;
          return;
        }

      let tabelaHtml = `
        <div class="table-responsive">
          <table class="table text-nowrap">
            <thead>
              <tr>
                <th><i class="fa-solid fa-hashtag text-primary me-1"></i>ID</th>
                <th><i class="fa-solid fa-receipt text-info me-1"></i>Solicitação</th>
                <th><i class="fa-solid fa-user text-danger me-1"></i>Usuário Pagador</th>
                <th><i class="fa-solid fa-user-check text-success me-1"></i>Usuário Beneficiário</th>
                <th><i class="fa-solid fa-tag text-warning me-1"></i>Taxa Original</th>
                <th><i class="fa-solid fa-dollar-sign text-success me-1"></i>Valor Split</th>
                <th><i class="fa-solid fa-percent text-primary me-1"></i>Porcentagem</th>
                <th class="text-center"><i class="fa-solid fa-circle-check text-success me-1"></i>Status</th>
                <th><i class="fa-solid fa-calendar text-info me-1"></i>Executado Em</th>
              </tr>
            </thead>
            <tbody>
      `;

      splitsExecutados.forEach(function(split) {
        const statusClass = split.status === 'processado' ? 'success' : 
                  split.status === 'falhado' ? 'danger' : 'warning';
        const statusText = split.status === 'processado' ? 'Processado' : 
                 split.status === 'falhado' ? 'Falhado' : 'Pendente';
        
        const dataExecucao = split.processado_em ? 
          new Date(split.processado_em).toLocaleDateString('pt-BR') + ' ' + 
          new Date(split.processado_em).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'}) :
          new Date(split.created_at).toLocaleDateString('pt-BR') + ' ' + 
          new Date(split.created_at).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});

        tabelaHtml += `
          <tr>
            <td><span class="badge bg-secondary">#${split.id}</span></td>
            <td>
              #${split.solicitacao_id}
              ${split.solicitacao ? `<br><small class="text-muted">R$ ${parseFloat(split.solicitacao.amount).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</small>` : ''}
            </td>
            <td>
              <div>
                <strong>${split.usuario_pagador?.name || 'N/A'}</strong><br>
                <small class="text-muted">${split.usuario_pagador?.email || 'N/A'}</small>
              </div>
            </td>
            <td>
              <div>
                <strong>${split.usuario_beneficiario?.name || 'N/A'}</strong><br>
                <small class="text-muted">${split.usuario_beneficiario?.email || 'N/A'}</small>
              </div>
            </td>
            <td>
              <span class="badge bg-info">
                R$ ${parseFloat(split.valor_taxa_original || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}
              </span>
            </td>
            <td>
              <span class="badge bg-success">
                R$ ${parseFloat(split.valor_split || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}
              </span>
            </td>
            <td>
              <span class="badge bg-primary">
                ${parseFloat(split.porcentagem_aplicada || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}%
              </span>
            </td>
            <td class="text-center">
              <span class="badge bg-${statusClass}">
                <i class="fas fa-circle me-1" style="font-size: 6px;"></i>
                ${statusText}
              </span>
            </td>
            <td>${dataExecucao}</td>
          </tr>
        `;
      });

      tabelaHtml += `
            </tbody>
          </table>
        </div>
      `;

      
      conteudoDiv.innerHTML = tabelaHtml;
      
      
      } catch (error) {
        console.error('Erro em renderizarConteudoHistorico:', error);
      }
    }

    function limparFiltrosHistorico() {
      $('#formFiltrosHistorico')[0].reset();
      carregarHistorico();
    }

    // Função para carregar detalhes da configuração
    function carregarDetalhesConfiguracao(configuracaoId) {
      fetch(`/admin/ajustes/splits-internos/${configuracaoId}`, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('Detalhes carregados:', data);
        renderizarDetalhesConfiguracao(data.splitInterno, data.estatisticas);
      })
      .catch(error => {
        console.error('Erro ao carregar detalhes:', error);
        $('#modalDetalhesBody').html(`
          <div class="text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
            <h5 class="text-danger">Erro ao carregar detalhes</h5>
            <p class="text-muted">Erro: ${error.message}</p>
            <button class="btn btn-primary" onclick="carregarDetalhesConfiguracao(${configuracaoId})">
              <i class="fas fa-refresh me-1"></i> Tentar Novamente
            </button>
          </div>
        `);
      });
    }

    // Função para renderizar os detalhes da configuração
    function renderizarDetalhesConfiguracao(splitInterno, estatisticas) {
      const dataInicio = splitInterno.data_inicio ? 
        new Date(splitInterno.data_inicio).toLocaleDateString('pt-BR') : 'Não definida';
      const dataFim = splitInterno.data_fim ? 
        new Date(splitInterno.data_fim).toLocaleDateString('pt-BR') : 'Não definida';
      const dataCriacao = new Date(splitInterno.created_at).toLocaleDateString('pt-BR') + ' ' + 
        new Date(splitInterno.created_at).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});

      const html = `
        <div class="row">
          <!-- Informações Principais -->
          <div class="col-md-6">
            <div class="card bg-light">
              <div class="card-body">
                <h6 class="card-title text-primary mb-3">
                  <i class="fas fa-info-circle me-2"></i>Informações Principais
                </h6>
                
                <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">ID da Configuração</label>
                  <p class="mb-0">
                    <span class="badge bg-secondary">#${splitInterno.id}</span>
                  </p>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Usuário Pagador</label>
                  <p class="mb-0">
                    <strong>${splitInterno.usuario_pagador?.name || 'N/A'}</strong><br>
                    <small class="text-muted">${splitInterno.usuario_pagador?.email || 'N/A'}</small>
                  </p>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Usuário Beneficiário</label>
                  <p class="mb-0">
                    <strong>${splitInterno.usuario_beneficiario?.name || 'N/A'}</strong><br>
                    <small class="text-muted">${splitInterno.usuario_beneficiario?.email || 'N/A'}</small>
                  </p>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Tipo de Taxa</label>
                  <p class="mb-0">
                    <span class="badge bg-info">
                      ${splitInterno.tipo_taxa === 'deposito' ? 'Depósito' : 
                        splitInterno.tipo_taxa === 'saque_pix' ? 'Saque PIX' : 
                        splitInterno.tipo_taxa === 'carteira_gateway' ? 'Carteira Gateway' : 
                        'Desconhecido'}
                    </span>
                  </p>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Porcentagem do Split</label>
                  <p class="mb-0">
                    <span class="badge bg-primary fs-6">
                      ${parseFloat(splitInterno.porcentagem_split).toLocaleString('pt-BR', {minimumFractionDigits: 2})}%
                    </span>
                  </p>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Status</label>
                  <p class="mb-0">
                    <span class="badge bg-${splitInterno.ativo ? 'success' : 'danger'}">
                      <i class="fas fa-circle me-1" style="font-size: 6px;"></i>
                      ${splitInterno.ativo ? 'Ativo' : 'Inativo'}
                    </span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Datas e Estatísticas -->
          <div class="col-md-6">
            <div class="card bg-light">
              <div class="card-body">
                <h6 class="card-title text-success mb-3">
                  <i class="fas fa-chart-bar me-2"></i>Datas e Estatísticas
                </h6>

                <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Data de Início</label>
                  <p class="mb-0">${dataInicio}</p>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Data de Fim</label>
                  <p class="mb-0">${dataFim}</p>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Criado Por</label>
                  <p class="mb-0">
                    <strong>${splitInterno.criado_por_admin?.name || 'N/A'}</strong><br>
                    <small class="text-muted">${splitInterno.criado_por_admin?.email || 'N/A'}</small>
                  </p>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold text-muted">Data de Criação</label>
                  <p class="mb-0">${dataCriacao}</p>
                </div>

                <hr class="my-3">

                <h6 class="text-warning mb-3">
                  <i class="fas fa-history me-2"></i>Estatísticas de Execução
                </h6>

                <div class="row g-2">
                  <div class="col-6">
                    <div class="text-center p-2 bg-white rounded">
                      <div class="text-success fw-bold fs-5">${estatisticas.quantidade_executados || 0}</div>
                      <small class="text-muted">Total Executados</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="text-center p-2 bg-white rounded">
                      <div class="text-primary fw-bold fs-5">${estatisticas.quantidade_processados || 0}</div>
                      <small class="text-muted">Processados</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="text-center p-2 bg-white rounded">
                      <div class="text-danger fw-bold fs-5">${estatisticas.quantidade_falhados || 0}</div>
                      <small class="text-muted">Falhados</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="text-center p-2 bg-white rounded">
                      <div class="text-info fw-bold fs-5">R$ ${(estatisticas.total_executado || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</div>
                      <small class="text-muted">Valor Total</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;

      $('#modalDetalhesBody').html(html);
    }
  </script>
</x-app-layout>
