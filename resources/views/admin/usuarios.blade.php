@php
$setting = \App\Helpers\Helper::getSetting();

function formatarCpfOuCnpj($valor)
{
  // Remove tudo que não for número
  $numeros = preg_replace('/\D/', '', $valor);

  if (strlen($numeros) === 11) {
    // Formato CPF
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/","$1.$2.$3-$4", $numeros);
  } elseif (strlen($numeros) > 11) {
    // Formato CNPJ
    return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/","$1.$2.$3/$4-$5", $numeros);
  }

  // Retorna apenas os números se não for CPF nem CNPJ esperado
  return $numeros;
}
@endphp

<x-app-layout :route="'[ADMIN] Usuários'">
  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-users fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Gerenciar Usuários</h1>
                  <p class="text-muted mb-0">Administração completa de usuários do sistema</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtros -->
      <div class="row mb-4">
        <div class="col-12">
          <form method="GET" action="{{ route('admin.usuarios') }}" id="filtroCompleto">
            <div class="row g-3 align-items-end justify-content-end">
              <!-- Campo de Busca -->
              <div class="col-lg-3 col-md-4 col-sm-6">
                <label class="form-label mb-2">
                  <i class="fa-solid fa-search text-primary me-1"></i>
                  Pesquisar
                </label>
                <input type="search" 
                  class="form-control" 
                  id="buscar" 
                  name="buscar"
                  placeholder="Nome, CPF, CNPJ, Email..." 
                  value="{{ request('buscar') }}" 
                  autofocus>
              </div>

              <!-- Filtro Status -->
              <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label mb-2">
                  <i class="fa-solid fa-filter text-warning me-1"></i>
                  Status
                </label>
                <select class="form-select" id="status" name="status">
                  <option value="ativos" {{ request('status') =="ativos" ? 'selected' : '' }}>Ativos</option>
                  <option value="banidos" {{ request('status') =="banidos" ? 'selected' : '' }}>Banidos</option>
                  <option value="pendentes" {{ request('status') =="pendentes" ? 'selected' : '' }}>Pendentes</option>
                  <option value="todos" {{ request('status') =="todos" ? 'selected' : '' }}>Todos</option>
                </select>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Cards de Estatísticas -->
      <div class="row g-3">
        @include('admin.usuarios.partials.card-count', [
          'label' => 'Cadastros Totais',
          'info' => (clone $users)->count(),
          'icon' => 'fa-users',
          'color' => 'success'
        ])

        @include('admin.usuarios.partials.card-count', [
          'label' => 'Cadastros do Mês',
          'info' => (clone $users)->whereBetween('data_cadastro', [now()->startOfMonth(), now()->endOfMonth()])->count(),
          'icon' => 'fa-user-plus',
          'color' => 'info'
        ])

        @include('admin.usuarios.partials.card-count', [
          'label' => 'Cadastros Pendentes',
          'info' => (clone $users)->where('status', 0)->count(),
          'icon' => 'fa-clock',
          'color' => 'warning'
        ])

        @include('admin.usuarios.partials.card-count', [
          'label' => 'Usuários Banidos',
          'info' => (clone $users)->where('banido', 1)->count(),
          'icon' => 'fa-ban',
          'color' => 'danger'
        ])
      </div>

      <!-- Tabela de Usuários -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="fa-solid fa-table text-primary me-2"></i>
                Lista de Usuários
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="table-responsive">
                <table id="table-listar-usuarios" class="table text-nowrap table-hover">
                  <thead>
                    <tr>
                      <th><i class="fa-solid fa-user text-primary me-1"></i>Usuário</th>
                      <th><i class="fa-solid fa-id-card text-info me-1"></i>CPF/CNPJ</th>
                      <th><i class="fa-solid fa-shield text-warning me-1"></i>Permissão</th>
                      <th><i class="fa-solid fa-building text-secondary me-1"></i>Adquirente</th>
                      <th><i class="fa-solid fa-chart-line text-success me-1"></i>Vendas 7d</th>
                      <th class="text-center"><i class="fa-solid fa-circle-check text-success me-1"></i>Status</th>
                      <th class="text-center"><i class="fa-solid fa-file-lines text-info me-1"></i>Doc</th>
                      <th><i class="fa-solid fa-calendar text-warning me-1"></i>Criado</th>
                      <th class="text-center">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($users as $user)
                    <tr>
                      <!-- Usuário -->
                      <td>
                        <div class="d-flex align-items-center">
                          <img src="{{ $user->avatar }}" 
                             alt="{{ $user->username }}" 
                             class="rounded me-2" 
                             style="width: 32px; height: 32px; object-fit: cover;">
                          <strong class="fw-medium">{{ $user->username }}</strong>
                        </div>
                      </td>

                      <!-- CPF/CNPJ -->
                      <td class="font-monospace small">{{ formatarCpfOuCnpj($user->cpf_cnpj) }}</td>

                      <!-- Permissão -->
                      <td>
                        @switch($user->permission)
                          @case(5)
                            <span class="badge bg-warning text-dark">Gerente</span>
                            @break
                          @case(3)
                            <span class="badge bg-danger">Admin</span>
                            @break
                          @default
                            <span class="badge bg-secondary">Cliente</span>
                        @endswitch
                      </td>

                      <!-- Adquirente -->
                      <td class="small">
                        @if($user->preferred_adquirente && $user->adquirente_override)
                          @php
                            $adquirente = $adquirentes->where('referencia', $user->preferred_adquirente)->first();
                          @endphp
                          <span class="badge bg-info">{{ $adquirente->adquirente ?? $user->preferred_adquirente }}</span>
                        @else
                          <span class="text-muted">Padrão</span>
                        @endif
                      </td>

                      <!-- Vendas 7 dias -->
                      <td>
                        @php
                          $ultimos7Dias = $user->depositos()
                            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(7))
                            ->whereIn('status', ['PAID_OUT', 'COMPLETED', 'paid', 'completed', 'pago', 'approved'])
                            ->sum('deposito_liquido');
                        @endphp
                        <span class="badge bg-success">R$ {{ number_format($ultimos7Dias, 2, ',', '.') }}</span>
                      </td>

                      <!-- Status -->
                      <td class="text-center">
                        @if($user->banido == 0)
                          <span class="badge bg-success">
                            <i class="fa-solid fa-circle me-1" style="font-size: 6px;"></i>Ativo
                          </span>
                        @else
                          <span class="badge bg-danger">
                            <i class="fa-solid fa-circle me-1" style="font-size: 6px;"></i>Bloqueado
                          </span>
                        @endif
                      </td>

                      <!-- Documento -->
                      <td class="text-center">
                        @if($user->status == 1)
                          <span class="badge bg-success">
                            <i class="fa-solid fa-check me-1"></i>OK
                          </span>
                        @else
                          <span class="badge bg-warning text-dark">
                            <i class="fa-solid fa-clock me-1"></i>Análise
                          </span>
                        @endif
                      </td>

                      <!-- Criado em -->
                      <td class="small">{{ $user->created_at->format('d/m/Y') }}</td>

                      <!-- Ações -->
                      <td>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                          <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#visModal-{{ $user->id }}" title="Visualizar" data-bs-toggle="tooltip">
                            <i class="fa-solid fa-eye"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#editModal-{{ $user->id }}" title="Editar" data-bs-toggle="tooltip">
                            <i class="fa-solid fa-edit"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#trocarSenhaModal-{{ $user->id }}" title="Alterar Senha" data-bs-toggle="tooltip">
                            <i class="fa-solid fa-user-lock"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-{{ $user->status == 1 ? 'warning' : 'success' }}" data-bs-toggle="modal" data-bs-target="#aprovarModal-{{ $user->id }}" title="{{ $user->status == 1 ? 'Reprovar' : 'Aprovar' }}" data-bs-toggle="tooltip">
                            <i class="fa-solid {{ $user->status == 1 ? 'fa-ban' : 'fa-check' }}"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-{{ $user->banido == 0 ? 'danger' : 'success' }}" data-bs-toggle="modal" data-bs-target="#banModal-{{ $user->id }}" title="{{ $user->banido == 0 ? 'Banir' : 'Desbanir' }}" data-bs-toggle="tooltip">
                            <i class="fa-solid {{ $user->banido == 0 ? 'fa-user-slash' : 'fa-user-shield' }}"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $user->id }}" title="Excluir" data-bs-toggle="tooltip">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#taxasModal-{{ $user->id }}" title="Taxas" data-bs-toggle="tooltip">
                            <i class="fa-solid fa-dollar-sign"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#afiliadosModal-{{ $user->id }}" title="Afiliados" data-bs-toggle="tooltip">
                            <i class="fa-solid fa-user-tag"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Modais (fora da tabela) -->
  @foreach ($users as $user)
    @include('admin.usuarios.partials.modal-visualizar', ['user' => $user])
    @include('admin.usuarios.partials.modal-edit', ['user' => $user, 'gerentes' => $gerentes])
    @include('admin.usuarios.partials.modal-delete', ['user' => $user])
    @include('admin.usuarios.partials.modal-frente-rg', ['user' => $user])
    @include('admin.usuarios.partials.modal-verso-rg', ['user' => $user])
    @include('admin.usuarios.partials.modal-selfie-rg', ['user' => $user])
    @include('admin.usuarios.partials.modal-contrato-social', ['user' => $user])
    @include('admin.usuarios.partials.modal-aprovar', ['user' => $user])
    @include('admin.usuarios.partials.modal-banir', ['user' => $user])
    @include('admin.usuarios.partials.modal-trocar-senha', ['user' => $user])
    @include('admin.usuarios.partials.modal-taxas', ['user' => $user])
    @include('admin.usuarios.partials.modal-afiliados', ['user' => $user])
  @endforeach

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Inicializar DataTable
      $("#table-listar-usuarios").DataTable({
        responsive: true,
        info: false,
        ordering: true,
        lengthChange: true,
        pageLength: 25,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        dom: 'rt<"bottom"lp><"clear">',
        columnDefs: [
          { orderable: false, targets: -1 } // Desabilita ordenação na coluna de ações
        ]
      });

      // Filtro de busca com delay
      const input = document.getElementById('buscar');
      if (input) {
        input.focus();
        
        let timeout = null;
        input.addEventListener('input', function() {
          clearTimeout(timeout);
          if (this.value.length >= 3 || this.value.length === 0) {
            timeout = setTimeout(() => {
              document.getElementById('filtroCompleto').submit();
            }, 800);
          }
        });
      }

      // Submeter ao mudar status
      const selectStatus = document.getElementById('status');
      if (selectStatus) {
        selectStatus.addEventListener('change', function() {
          document.getElementById('filtroCompleto').submit();
        });
      }

      // Inicializar tooltips
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });

    // Gerar Chave Secret (UUID v4)
    function gerarChaveSecret(id) {
      const chave = generateUUIDv4();
      document.getElementById(`e-secret-${id}`).value = chave;
    }

    // Gerar Chave Token (UUID v4)
    function gerarChaveToken(id) {
      const chave = generateUUIDv4();
      document.getElementById(`e-token-${id}`).value = chave;
    }

    // Gerar UUID v4
    function generateUUIDv4() {
      return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
        (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
      );
    }
  </script>
</x-app-layout>