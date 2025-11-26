<x-app-layout :route="'Check-out'">
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
                    <i class="fa-solid fa-cube fa-lg"></i>
                  </div>
                  <div>
                    <h1 class="display-6 fw-bold mb-1">Produtos</h1>
                    <p class="text-muted mb-0">Gerencie seus produtos e acompanhe as vendas</p>
                  </div>
                </div>
                <div class="d-flex gap-2">
                  <!-- Campo de Busca -->
                  <form method="GET" action="{{route('profile.checkout')}}" id="filtroCompleto" class="d-flex">
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="fas fa-search"></i>
                      </span>
                      <input type="search" 
                          class="form-control" 
                          id="buscar" 
                          name="buscar" 
                          placeholder="Buscar produtos..." 
                          value="{{ request('buscar') }}" 
                          autofocus>
                    </div>
                  </form>
                  
                  <!-- Botão Adicionar -->
                  <button type="button" 
                      class="btn btn-primary"
                      data-bs-toggle="modal" 
                      data-bs-target="#addproduto">
                    <i class="fas fa-plus me-1"></i>Adicionar Produto
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabela de Produtos -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body p-4">
              <div class="table-responsive">
                <table class="table text-nowrap" id="table-produtos">
                  <thead>
                    <tr>
                      <th scope="col">Produto</th>
                      <th scope="col" class="text-center">Preço</th>
                      <th scope="col" class="text-center">Status</th>
                      <th scope="col" class="text-center">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($checkouts as $checkout)
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <img src="{{$checkout->produto_image}}" 
                             alt="{{$checkout->produto_name}}"
                             class="rounded me-3"
                             style="width: 48px; height: 48px; object-fit: cover;">
                          <span class="fw-medium">{{ $checkout->produto_name }}</span>
                        </div>
                      </td>
                      
                      <td class="text-center">
                        <span class="fw-semibold">R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}</span>
                      </td>
                      
                      <td class="text-center">
                        @if ($checkout->status)
                          <span class="badge bg-success">
                            <i class="fas fa-circle me-1" style="font-size: 6px;"></i>Ativo
                          </span>
                        @else
                          <span class="badge bg-warning">
                            <i class="fas fa-circle me-1" style="font-size: 6px;"></i>Inativo
                          </span>
                        @endif
                      </td>
                      
                      <td>
                        <div class="d-flex justify-content-center gap-2">

                          <a href="/produtos/visualizar/{{ $checkout->id_unico }}#links"
                            class="btn btn-sm btn-outline-primary"
                            title="Ver links"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top">
                            <i class="fas fa-link"></i>
                          </a>

                          <a href="/produtos/visualizar/{{ $checkout->id_unico }}#orders"
                            class="btn btn-sm btn-outline-success"
                            title="Pedidos"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top">
                            <i class="fas fa-shopping-cart"></i>
                          </a>

                          <a href="{{ route('profile.checkout.produto', ['id' => $checkout->id_unico]) }}"
                            class="btn btn-sm btn-outline-warning"
                            title="Editar"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top">
                            <i class="fas fa-pencil"></i>
                          </a>
                          
                          <button type="button"
                              class="btn btn-sm btn-outline-danger"
                              data-bs-toggle="modal"
                              data-bs-target="#editModal-{{$checkout->id}}"
                              title="Excluir"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top">
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>


                        <!-- Modal Excluir -->
                        <div class="modal fade" 
                           id="editModal-{{$checkout->id}}" 
                           tabindex="-1" 
                           aria-labelledby="editModal-{{$checkout->id}}Label" 
                           aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title fw-semibold" id="editModal-{{$checkout->id}}Label">
                                  <i class="fas fa-exclamation-triangle text-danger me-2"></i>Excluir Produto
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body p-4">
                                <div class="alert alert-danger mb-3">
                                  <h6 class="alert-heading fw-semibold mb-2">
                                    Tem certeza que deseja excluir este produto?
                                  </h6>
                                  <p class="mb-0 small">Esta ação não pode ser desfeita.</p>
                                </div>
                                <div class="bg-light rounded p-3">
                                  <strong>Produto:</strong> {{ $checkout->produto_name }}
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                  Cancelar
                                </button>
                                <form method="POST" action="{{ route('profile.checkout.delete', ['id'=> $checkout->id]) }}" class="d-inline">
                                  @csrf
                                  @method('DELETE')
                                  <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i>Excluir
                                  </button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                    @empty
                    <tr class="no-data-row">
                      <td colspan="4" class="text-center py-5">
                        <div class="text-muted">
                          <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                          <h5>Nenhum produto encontrado</h5>
                          <p class="mb-0">Comece adicionando seu primeiro produto.</p>
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

  <!-- Modal Adicionar Produto -->
  <div class="modal fade" id="addproduto" tabindex="-1" aria-labelledby="addprodutoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold" id="addprodutoLabel">
            <i class="fas fa-plus-circle me-2"></i>Novo Produto
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <form method="POST" action="{{ route('profile.checkout.create') }}" enctype="multipart/form-data">
          @csrf
          @method('POST')
          <div class="modal-body p-4">
            <div class="row g-3">
              <!-- Nome do Produto -->
              <div class="col-12">
                <label for="produto_name" class="form-label">Nome do Produto</label>
                <input type="text" 
                    id="produto_name" 
                    name="produto_name" 
                    class="form-control @error('produto_name') is-invalid @enderror" 
                    placeholder="Digite o nome do produto" 
                    value="{{ old('produto_name') }}" 
                    required>
                @error('produto_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Valor do Produto -->
              <div class="col-12">
                <label for="produto_valor" class="form-label">Valor do Produto</label>
                <div class="input-group">
                  <span class="input-group-text">R$</span>
                  <input type="number" 
                      id="produto_valor" 
                      name="produto_valor" 
                      step="0.01" 
                      min="0.01"
                      class="form-control @error('produto_valor') is-invalid @enderror" 
                      placeholder="0,00" 
                      value="{{ old('produto_valor') }}" 
                      required>
                  @error('produto_valor')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <!-- Descrição do Produto -->
              <div class="col-12">
                <label for="produto_descricao" class="form-label">Descrição do Produto</label>
                <textarea id="produto_descricao" 
                     name="produto_descricao" 
                     rows="3"
                     class="form-control @error('produto_descricao') is-invalid @enderror" 
                     placeholder="Descreva o produto..." 
                     required>{{ old('produto_descricao') }}</textarea>
                @error('produto_descricao')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Tipo do Produto -->
              <div class="col-md-6">
                <label for="produto_tipo" class="form-label">Tipo do Produto</label>
                <select id="produto_tipo" 
                    name="produto_tipo" 
                    class="form-select @error('produto_tipo') is-invalid @enderror" 
                    required>
                  <option value="">Selecione o tipo</option>
                  <option value="info" {{ old('produto_tipo') == 'info' ? 'selected' : '' }}>Info Produto</option>
                  <option value="fisico" {{ old('produto_tipo') == 'fisico' ? 'selected' : '' }}>Produto Físico</option>
                </select>
                @error('produto_tipo')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Tipo de Cobrança -->
              <div class="col-md-6">
                <label for="produto_tipo_cob" class="form-label">Tipo de Cobrança</label>
                <select id="produto_tipo_cob" 
                    name="produto_tipo_cob" 
                    class="form-select @error('produto_tipo_cob') is-invalid @enderror" 
                    required>
                  <option value="">Selecione o tipo de cobrança</option>
                  <option value="unico" {{ old('produto_tipo_cob') == 'unico' ? 'selected' : '' }}>Único</option>
                  <option value="recorrente" {{ old('produto_tipo_cob') == 'recorrente' ? 'selected' : '' }}>Recorrente</option>
                </select>
                @error('produto_tipo_cob')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Meios de Pagamento -->
              <div class="col-12">
                <label class="form-label">Meios de Pagamento Aceitos</label>
                <div class="d-flex flex-wrap gap-3">
                  <div class="form-check">
                    <input type="checkbox" 
                        id="method_pix" 
                        name="methods[]" 
                        value="pix" 
                        class="form-check-input"
                        {{ in_array('pix', old('methods', ['pix'])) ? 'checked' : '' }}>
                    <label for="method_pix" class="form-check-label">
                      <i class="fa-brands fa-pix text-success me-1"></i>PIX
                    </label>
                  </div>
                  
                  <div class="form-check">
                    <input type="checkbox" 
                        id="method_card" 
                        name="methods[]" 
                        value="card" 
                        class="form-check-input"
                        {{ in_array('card', old('methods', [])) ? 'checked' : '' }}>
                    <label for="method_card" class="form-check-label">
                      <i class="fa-solid fa-credit-card text-primary me-1"></i>Cartão de Crédito
                    </label>
                  </div>
                  
                  <div class="form-check">
                    <input type="checkbox" 
                        id="method_boleto" 
                        name="methods[]" 
                        value="billet" 
                        class="form-check-input"
                        {{ in_array('billet', old('methods', [])) ? 'checked' : '' }}>
                    <label for="method_boleto" class="form-check-label">
                      <i class="fa-solid fa-barcode text-warning me-1"></i>Boleto Bancário
                    </label>
                  </div>
                </div>
                <div class="form-text">
                  <i class="fas fa-info-circle"></i> Selecione os métodos de pagamento que estarão disponíveis para seus clientes.
                </div>
                @error('methods')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>

              <!-- Status -->
              <div class="col-12">
                <label for="status" class="form-label">Status</label>
                <select id="status" 
                    name="status" 
                    class="form-select @error('status') is-invalid @enderror" 
                    required>
                  <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Ativo</option>
                  <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inativo</option>
                </select>
                @error('status')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-check me-1"></i>Cadastrar Produto
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // DataTable Initialization
    const table = document.getElementById('table-produtos');
    if (table) {
      const tbody = table.querySelector('tbody');
      const hasData = tbody && tbody.querySelectorAll('tr:not(.no-data-row)').length > 0;

      if (hasData) {
        $('#table-produtos').DataTable({
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

    // Busca com delay
    const buscarInput = document.getElementById('buscar');
    if (buscarInput) {
      buscarInput.focus();
      buscarInput.select();

      let searchTimeout;
      buscarInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          document.getElementById('filtroCompleto').submit();
        }, 800);
      });
    }
  });
  </script>

</x-app-layout>