<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Histórico de Splits Internos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="container-fluid mt-4">
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1><i class="fas fa-history"></i> Histórico de Splits Internos</h1>
          <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-2">
              <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('admin.splits-internos.index') }}" class="btn btn-info">
              <i class="fas fa-list"></i> Configurações
            </a>
          </div>
        </div>

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-d�ismiss="alert"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <!-- Filtros -->
        <div class="card mb-4">
          <div class="card-header">
            <h5><i class="fas fa-filter"></i> Filtros</h5>
          </div>
          <div class="card-body">
            <form method="GET">
              <div class="row">
                <div class="col-md-3 mb-3">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="processado" {{ request('status') == 'processado' ? 'selected' : '' }}>Processado</option>
                    <option value="falhado" {{ request('status') == 'falhado' ? 'selected' : '' }}>Falhado</option>
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="data_inicio" class="form-label">Data Início</label>
                  <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ request('data_inicio') }}">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="data_fim" class="form-label">Data Fim</label>
                  <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ request('data_fim') }}">
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">&nbsp;</label>
                  <div class="d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                      <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.splits-internos.historico') }}" class="btn btn-secondary">
                      <i class="fas fa-times"></i> Limpar
                    </a>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Estatísticas -->
        @if(isset($estatisticas))
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="card bg-success text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="card-title">{{ $estatisticas['total_executados'] ?? 0 }}</h4>
                    <p class="card-text">Total Executados</p>
                  </div>
                  <div class="align-self-center">
                    <i class="fas fa-exchange-alt fa-2x"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class---

## Cómprar R$ 10,00 (R$ 10,00) - Banco Bradesco (boleto)

            <div class="card bg-info text-white">
              <div>
                <h4 class="card-title">R$ {{ number_format($estatisticas['total_valor_splits'] ?? 0, 2, ',', '.') }}</h4>
                <p class="card-text">Valor Total Splits</p>
              </div>
              <div class="align-self-center">
                <i class="fas fa-dollar-sign fa-2x"></i>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-warning text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="card-title">{{ number_format($estatisticas['porcentagem_media'] ?? 0, 2) }}%</h4>
                    <p class="card-text">Porcentagem Média</p>
                  </div>
                  <div class="align-self-center">
                    <i class="fas fa-percentage fa-2x"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-danger text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h4 class="card-title">{{ $estatisticas['status_breakdown']['falhados'] ?? 0 }}</h4>
                    <p class="card-text">Falhados</p>
                  </div>
                  <div class="align-self-center">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif

        <!-- Lista de Splits Executados -->
        <div class="card">
          <div class="card-header">
            <h5><i class="fas fa-list"></i> Splits Executados</h5>
          </div>
          <div class="card-body">
            @if(isset($splitsExecutados) && $splitsExecutados->count() > 0)
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Solicitação</th>
                      <th>Usuário Pagador</th>
                      <th>Usuário Beneficiário</th>
                      <th>Taxa Original</th>
                      <th>Valor Split</th>
                      <th>Porcentagem</th>
                      <th>Status</th>
                      <th>Executado Em</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($splitsExecutados as $split)
                    <tr>
                      <td><span class="badge bg-secondary">#{{ $split->id }}</span></td>
                      <td>
                        #{{ $split->solicitacao_id }}
                        @if($split->solicitacao)
                          <br><small class="text-muted">R$ {{ number_format($split->solicitacao->amount, 2, ',', '.') }}</small>
                        @endif
                      </td>
                      <td>
                        <div>
                          <strong>{{ $split->usuarioPagador->name }}</strong><br>
                          <small class="text-muted">{{ $split->usuarioPagador->email }}</small>
                        </div>
                      </td>
                      <td>
                        <div>
                          <strong>{{ $split->usuarioBeneficiario->name }}</strong><br>
                          <small class="text-muted">{{ $split->usuarioBeneficiario->email }}</small>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-info">
                          R$ {{ number_format($split->valor_taxa_original, 2, ',', '.') }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-success">
                          R$ {{ number_format($split->valor_split, 2, ',', '.') }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-primary">
                          {{ number_format($split->porcentagem_aplicada, 2, ',', '.') }}%
                        </span>
                      </td>
                      <td>
                        <span class="badge {{ $split->estatisticas['status_color'] }}">
                          {{ $split->statusFormatado }}
                        </span>
                      </td>
                      <td>
                        @if($split->processado_em)
                          {{ $split->processado_em->format('d/m/Y H:i') }}
                        @else
                          {{ $split->created_at->format('d/m/Y H:i') }}
                        @endif
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              
              <!-- Paginação -->
              <div class="d-flex justify-content-center mt-3">
                {{ $splitsExecutados->links() }}
              </div>
            @else
              <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum split executado encontrado</h5>
                <p class="text-muted">Configure alguns splits internos para ver o histórico aqui.</p>
                <a href="{{ route('admin.splits-internos.index') }}" class="btn btn-primary">
                  <i class="fas fa-cogs"></i> Ir para Configurações
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>