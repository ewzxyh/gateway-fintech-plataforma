@php
$setting = \App\Helpers\Helper::getSetting();
$color = $setting->gateway_color;
@endphp

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Histórico de Comissões - Gateway</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  @include('layouts.components.navigation')
</head>

<body>
  @include('layouts.components.sidebar')

  <div class="main-content">
    <div class="container-fluid py-4">
      <!-- Header -->
      <div class="row mb-4">
        <div class="col-md-6">
          <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="/dashboard" class="text-light">
                  <i class="fas fa-home"></i> Home
                </a>
              </li>
              <li class="breadcrumb-item">
                <a href="/affiliate" class="text-light">Meus Referidos</a>
              </li>
              <li class="breadcrumb-item active text-warning" aria-current="page">Histórico de Comissões</li>
            </ol>
          </nav>
        </div>
        <div class="col-md-6">
          <div class="d-flex align-items-center justify-content-end">
            <a href="/affiliate" class="btn btn-outline-warning btn-sm me-2">
              <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <strong>{{ substr(strtoupper(auth()->user()->name), 0, 2) }}</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Título -->
      <div class="row mb-4">
        <div class="col-md-12">
          <div class="d-flex align-items-center">
            <div class="yellow-bar me-3"></div>
            <div>
              <h1 class="text-light mb-1">Histórico de Comissões</h1>
              <p class="text-light">Transações que geraram comissões para você</p>
            </div>
          </div>
        </div>
      </div>

      @if($comissoes->count() > 0)
      <!-- Resumo das comissões -->
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card bg-dark border-success text-center">
            <div class="card-body">
              <h3 class="text-success">
                R$ {{ number_format($comissoes->sum('valor_split'), 2, ',', '.') }}
              </h3>
              <p class="text-light mb-0">Total Recebido</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-dark border-info text-center">
            <div class="card-body">
              <h3 class="text-info">{{ $comissoes->count() }}</h3>
              <p class="text-light mb-0">Transações com Comissão</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-dark border-warning text-center">
            <div class="card-body">
              <h3 class="text-warning">
                {{ $comissoes->where('created_at', '>=', now()->subDays(30))->count() }}
              </h3>
              <p class="text-light mb-0">Últimos 30 Dias</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabela de comissões -->
      <div class="row">
        <div class="col-md-12">
          <div class="card bg-dark border-secondary">
            <div class="card-header bg-secondary">
              <h5 class="text-light mb-0">
                <i class="fas fa-list"></i> Transações com Comissão ({{ $comissoes->total() }} registros)
              </h5>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-dark table-borderless mb-0">
                  <thead>
                    <tr class="border-bottom border-secondary">
                      <th class="text-light ps-4 pt-3">Data/Hora</th>
                      <th class="text-light pt-3">Cliente</th>
                      <th class="text-light pt-3">Valor da Transação</th>
                      <th class="text-light pt-3">Sua Comissão</th>
                      <th class="text-light pt-3">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($comissoes as $comissao)
                    <tr class="border-bottom border-secondary">
                      <td class="ps-4 py-3">
                        <div>
                          <div class="text-light">{{ $comissao->created_at->format('d/m/Y') }}</div>
                          <small class="text-muted">{{ $comissao->created_at->format('H:i:s') }}</small>
                        </div>
                      </td>
                      <td class="py-3">
                        @if($comissao->pagador)
                        <div class="d-flex align-items-center">
                          <div class="bg-warning text-dark rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            <strong>{{ substr(strtoupper($comissao->pagador->name), 0, 2) }}</strong>
                          </div>
                          <div>
                            <strong class="text-light">{{ $comissao->pagador->name }}</strong><br>
                            <small class="text-muted">ID: {{ $comissao->pagador->id }}</small>
                          </div>
                        </div>
                        @else
                        <span class="text-muted">Usuário removido</span>
                        @endif
                      </td>
                      <td class="py-3">
                        @if($comissao->solicitacao)
                        <div>
                          <strong class="text-light">R$ {{ number_format($comissao->solicitacao->valor, 2, ',', '.') }}</strong><br>
                          <small class="text-muted">{{ $comissao->solicitacao->tipo }}</small>
                        </div>
                        @else
                        <span class="text-muted">Transação removida</span>
                        @endif
                      </td>
                      <td class="py-3">
                        <div>
                          <strong class="text-success">R$ {{ number_format($comissao->valor_split, 2, ',', '.') }}</strong><br>
                          <small class="text-muted">{{ $comissao->porcentagem_split }}%</small>
                        </div>
                      </td>
                      <td class="py-3">
                        @if($comissao->status === 'PROCESSADO')
                        <span class="badge bg-success">
                          <i class="fas fa-check"></i> Processado
                        </span>
                        @elseif($comissao->status === 'FALHADO')
                        <span class="badge bg-danger">
                          <i class="fas fa-times"></i> Falhado
                        </span>
                        @else
                        <span class="badge bg-warning">
                          <i class="fas fa-clock"></i> Pendente
                        </span>
                        @endif
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <!-- Paginação -->
              <div class="d-flex justify-content-center p-3">
                {{ $comissoes->links() }}
              </div>
            </div>
          </div>
        </div>
      </div>
      @else
      <div class="text-center py-5">
        <i class="fas fa-history fa-3x text-muted mb-3"></i>
        <h5 class="text-light">Nenhuma comissão encontrada</h5>
        <p class="text-muted">As comissões aparecerão aqui conforme seus referidos fizem transações.</p>
        <a href="/affiliate" class="btn btn-outline-warning">
          <i class="fas fa-arrow-left"></i> Voltar para Meus Referidos
        </a>
      </div>
      @endif
    </div>
  </div>

  <style>
    body {
      background-color: #1a1a1a;
      color: #fff;
    }

    .yellow-bar {
      width: 4px;
      height: 60px;
      background-color: #ffc107;
      flex-shrink: 0;
    }

    .breadcrumb {
      background: transparent;
      padding: 0;
    }

    .breadcrumb-nav .breadcrumb-item a {
      color: #fff !important;
      text-decoration: none;
    }

    .breadcrumb-nav .breadcrumb-item.active {
      color: #ffc107 !important;
    }

    .badge {
      font-size: 0.85em;
    }

    .table-responsive {
      max-height: 600px;
      overflow-y: auto;
    }

    @media (max-width: 768px) {
      .yellow-bar {
        height: 40px;
      }

      .table-responsive {
        font-size: 0.9em;
      }
    }
  </style>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>