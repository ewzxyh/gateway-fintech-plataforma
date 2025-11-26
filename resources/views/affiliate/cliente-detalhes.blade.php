@php
$setting = \App\Helpers\Helper::getSetting();
$color = $setting->gateway_color;
@endphp

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalhes do Cliente - Gateway</title>
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
              <li class="breadcrumb-item active text-warning" aria-current="page">{{ $cliente->name }}</li>
            </ol>
          </nav>
        </div>
        <div class="col-md-6">
          <div class="d-flex align-items-center justify-content-end">
            <a href="/affiliate" class="btn btn-outline-warning btn-sm me-2">
              <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <strong>{{ substr(strtoupper($cliente->name), 0, 2) }}</strong>
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
              <h1 class="text-light mb-1">{{ $cliente->name }}</h1>
              <p class="text-light">Detalhes e histórico de transações deste referido</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Informações do Cliente -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card bg-dark border-secondary">
            <div class="card-header">
              <h5 class="text-warning mb-0">
                <i class="fas fa-user"></i> Informações do Cliente
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  @if($cliente->avatar)
                  <img src="{{ $cliente->avatar }}"
                    class="rounded-circle mb-3"
                    width="80" height="80"
                    alt="Avatar">
                  @else
                  <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center mb-3"
                    style="width: 80px; height: 80px;">
                    <strong style="font-size: 24px;">{{ substr(strtoupper($cliente->name), 0, 2) }}</strong>
                  </div>
                  @endif
                </div>
                <div class="col-md-6">
                  <h6 class="text-light">Dados Básicos</h6>
                  <p class="text-light mb-1"><strong>Nome:</strong> {{ $cliente->name }}</p>
                  <p class="text-light mb-1"><strong>Email:</strong> {{ $cliente->email }}</p>
                  <p class="text-light mb-1"><strong>ID:</strong> {{ $cliente->id }}</p>
                  <p class="text-info mb-0"><strong>Cadastrado em:</strong> {{ $cliente->created_at->format('d/m/Y H:i') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card bg-dark border-success">
            <div class="card-header text-success">
              <h5 class="text-success mb-0">
                <i class="fas fa-chart-line"></i> Suas Comissões com Este Cliente
              </h5>
            </div>
            <div class="card-body text-center">
              <h2 class="text-success">R$ {{ number_format($totalComissoesCliente, 2, ',', '.') }}</h2>
              <p class="text-light mb-3">Total Recebido</p>
              <div class="row">
                <div class="col-md-4">
                  <div class="text-info">{{ $comissoesRecebidas->count() }}</div>
                  <div class="text-muted small">Transações</div>
                </div>
                <div class="col-md-4">
                  <div class="text-warning">{{ $cliente->depositos->count() }}</div>
                  <div class="text-muted small">Total Depósitos</div>
                </div>
                <div class="col-md-4">
                  <div class="text-light">R$ {{ number_format($cliente->depositos->sum('valor'), 2, ',', '.') }}</div>
                  <div class="text-muted small">Volume Total</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Histórico de Transações -->
      <div class="row">
        <div class="col-md-12">
          <div class="card bg-dark border-secondary">
            <div class="card-header">
              <h5 class="text-light mb-0">
                <i class="fas fa-list"></i> Transações e Comissões (últimas 20)
              </h5>
            </div>
            <div class="card-body p-0">
              @if($comissoesRecebidas->count() > 0)
              <div class="table-responsive">
                <table class="table table-dark table-borderless mb-0">
                  <thead>
                    <tr class="border-bottom border-secondary">
                      <th class="text-light ps-4 pt-3">Data/Hora</th>
                      <th class="text-light pt-3">Tipo Transação</th>
                      <th class="text-light pt-3">Valor Transação</th>
                      <th class="text-light pt-3">Taxa Cobrada</th>
                      <th class="text-light pt-3">Sua Comissão</th>
                      <th class="text-light pt-3">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($comissoesRecebidas->slice(0, 20) as $comissao)
                    <tr class="border-bottom border-secondary">
                      <td class="ps-4 py-3">
                        <div>
                          <div class="text-light">{{ $comissao->created_at->format('d/m/Y') }}</div>
                          <small class="text-muted">{{ $comissao->created_at->format('H:i:s') }}</small>
                        </div>
                      </td>
                      <td class="py-3">
                        @if($comissao->solicitacao)
                        <div>
                          <strong class="text-light">{{ $comissao->solicitacao->tipo }}</strong><br>
                          <small class="text-muted">{{ $comissao->solicitacao->metodo ?? 'PIX' }}</small>
                        </div>
                        @else
                        <span class="text-muted">Transação removida</span>
                        @endif
                      </td>
                      <td class="py-3">
                        @if($comissao->solicitacao)
                        <strong class="text-light">R$ {{ number_format($comissao->solicitacao->valor, 2, ',', '.') }}</strong>
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                      </td>
                      <td class="py-3">
                        @if($comissao->solicitacao)
                        <div>
                          <div class="text-light">Taxa: R$ {{ number_format($comissao->valor_taxa_original ?? 0, 2, ',', '.') }}</div>
                          <small class="text-muted">{{ $comissao->porcentagem_split }}% do percentual</small>
                        </div>
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                      </td>
                      <td class="py-3">
                        <div>
                          <strong class="text-success">R$ {{ number_format($comissao->valor_split, 2, ',', '.') }}</strong><br>
                          <small class="text-muted">Sua parte</small>
                        </div>
                      </td>
                      <td class="py-3">
                        @if($comissao->status === 'PROCESSADO')
                        <span class="badge bg-success">
                          <i class="fas fa-check"></i> Ok
                        </span>
                        @elseif($comissao->status === 'FALHADO')
                        <span class="badge bg-danger">
                          <i class="fas fa-times"></i> Erro
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

              @if($comissoesRecebidas->count() > 20)
              <div class="text-center p-3">
                <small class="text-muted">Mostrando últimas 20 transações</small><br>
                <a href="/affiliate/historico?user_id={{ $cliente->id }}" class="btn btn-outline-warning btn-sm">
                  <i class="fas fa-history"></i> Ver Histórico Completo
                </a>
              </div>
              @endif
              @else
              <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-light">Nenhuma comissão ainda</h5>
                <p class="text-muted">As comissões aparecerão aqui quando {{ $cliente->name }} fizer transações.</p>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
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

    .table-responsive {
      max-height: 600px;
      overflow-y: auto;
    }

    .badge {
      font-size: 0.85em;
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