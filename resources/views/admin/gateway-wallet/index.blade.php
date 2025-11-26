<x-app-layout :route="'[ADMIN] Carteira Gateway'">

  <div class="main-content app-content">
    <div class="container-fluid">
      
      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-success">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-success text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-wallet fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Carteira Gateway</h1>
                  <p class="text-muted mb-0">Carteira administrativa para saque do lucro do sistema</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cards de Saldo -->
      <div class="row g-3 mb-4">
        <!-- Saldo Disponível -->
        <div class="col-md-4">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Saldo Disponível</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($gatewayWallet->saldo_disponivel ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Saldo Pendente -->
        <div class="col-md-4">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-warning flex-shrink-0">
                  <i class="fa-solid fa-clock"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Saldo Pendente</p>
                  <h4 class="fw-bold mb-0 text-warning text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($gatewayWallet->saldo_pendente ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Sacado -->
        <div class="col-md-4">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fa-solid fa-arrow-up"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Sacado</p>
                  <h4 class="fw-bold mb-0 text-info text-truncate" style="font-size: 1.35rem;">R$ {{ number_format($gatewayWallet->total_sacado ?? 0, 2, ',', '.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Formulário de Saque -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-money-bill-wave me-2"></i>
                Solicitar Saque
              </h5>
            </div>
            <div class="card-body">
              <form method="POST" action="{{ route('admin.gateway-wallet.sacar') }}">
                @csrf
                <div class="row g-3">
                  <!-- Seleção de Adquirente -->
                  <div class="col-12">
                    <label for="adquirente" class="form-label fw-semibold">
                      <i class="fa-solid fa-building text-warning me-2"></i>
                      Adquirente para Saque
                    </label>
                    <select class="form-control @error('adquirente') is-invalid @enderror"
                      name="adquirente" id="adquirente" required>
                      <option value="">Selecione o adquirente</option>
                      @foreach($adquirentes as $adquirente)
                        @if($adquirente->status)
                        @php
                        $nomeFormatado = match(strtolower($adquirente->referencia)) {
                          'cashtime' => 'Cash Time',
                          'xgate' => 'XGate',
                          'witetec' => 'WiteTec',
                          'pixup' => 'PixUP BR',
                          'bspay' => 'BSPay',
                          'woovi' => 'Woovi',
                          'primepay7' => 'PrimePay7',
                          'xdpag' => 'XDPag',
                          'rede' => 'Rede e.Rede',
                          'pagarme' => 'PagarMe',
                          'stripe' => 'Stripe',
                          default => $adquirente->adquirente
                        };
                        @endphp
                        <option value="{{ $adquirente->referencia }}">
                          {{ $nomeFormatado }}
                        </option>
                        @endif
                      @endforeach
                    </select>
                    @error('adquirente')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">
                      <i class="fa-solid fa-lightbulb me-1"></i>
                      Selecione qual adquirente será usado para processar o saque
                    </small>
                  </div>

                  <div class="col-md-6">
                    <label for="valor" class="form-label fw-semibold">
                      <i class="fa-solid fa-dollar-sign text-success me-2"></i>
                      Valor do Saque
                    </label>
                    <input type="number" step="0.01" min="0.01" max="{{ $gatewayWallet->saldo_disponivel ?? 0 }}"
                      class="form-control @error('valor') is-invalid @enderror"
                      name="valor" id="valor" placeholder="Ex: 100.00" required>
                    @error('valor')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Saldo disponível: R$ {{ number_format($gatewayWallet->saldo_disponivel ?? 0, 2, ',', '.') }}</small>
                  </div>

                  <div class="col-md-6">
                    <label for="pix_key_type" class="form-label fw-semibold">
                      <i class="fa-solid fa-key text-primary me-2"></i>
                      Tipo da Chave PIX
                    </label>
                    <select class="form-control @error('pix_key_type') is-invalid @enderror"
                      name="pix_key_type" id="pix_key_type" required>
                      <option value="">Selecione o tipo</option>
                      <option value="cpf">CPF</option>
                      <option value="cnpj">CNPJ</option>
                      <option value="email">E-mail</option>
                      <option value="phone">Telefone</option>
                      <option value="random">Chave Aleatória</option>
                    </select>
                    @error('pix_key_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-12">
                    <label for="pix_key" class="form-label fw-semibold">
                      <i class="fa-solid fa-qrcode text-info me-2"></i>
                      Chave PIX
                    </label>
                    <input type="text" class="form-control @error('pix_key') is-invalid @enderror"
                      name="pix_key" id="pix_key" placeholder="Digite a chave PIX" required>
                    @error('pix_key')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-12">
                    <label for="observacoes" class="form-label fw-semibold">
                      <i class="fa-solid fa-comment text-secondary me-2"></i>
                      Observações (Opcional)
                    </label>
                    <textarea class="form-control @error('observacoes') is-invalid @enderror"
                      name="observacoes" id="observacoes" rows="3" placeholder="Observações sobre o saque..."></textarea>
                    @error('observacoes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                      <i class="fa-solid fa-money-bill-wave me-2"></i>Solicitar Saque
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Saques Pendentes -->
      @if($saquesPendentes->count() > 0)
      <div class="row">
        <div class="col-12">
          <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
              <h5 class="mb-0">
                <i class="fa-solid fa-clock me-2"></i>
                Saques Pendentes ({{ $saquesPendentes->count() }})
              </h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Valor</th>
                      <th>Chave PIX</th>
                      <th>Data</th>
                      <th>Status</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($saquesPendentes as $saque)
                    <tr>
                      <td>{{ $saque->id }}</td>
                      <td class="fw-bold text-success">R$ {{ number_format($saque->amount, 2, ',', '.') }}</td>
                      <td>{{ $saque->pix }}</td>
                      <td>{{ $saque->created_at->format('d/m/Y H:i') }}</td>
                      <td>
                        <span class="badge bg-warning">{{ $saque->status }}</span>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <form method="POST" action="{{ route('admin.gateway-wallet.confirmar', $saque->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" 
                                onclick="return confirm('Confirmar este saque?')">
                              <i class="fa-solid fa-check"></i>
                            </button>
                          </form>
                          <form method="POST" action="{{ route('admin.gateway-wallet.cancelar', $saque->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Cancelar este saque?')">
                              <i class="fa-solid fa-times"></i>
                            </button>
                          </form>
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
      @endif

    </div>
  </div>

  <!-- JavaScript para melhorar UX -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const adquirenteSelect = document.getElementById('adquirente');
    const valorInput = document.getElementById('valor');
    
    // Informações dos adquirentes
    const adquirenteInfo = {
      'woovi': { nome: 'Woovi', cor: '#00a782', descricao: 'Processamento instantâneo via PIX' },
      'pixup': { nome: 'PixUP BR', cor: '#ff6b35', descricao: 'Gateway brasileiro especializado em PIX' },
      'pagarme': { nome: 'PagarMe', cor: '#00d4aa', descricao: 'Gateway completo com múltiplas opções' },
      'cashtime': { nome: 'Cash Time', cor: '#6c5ce7', descricao: 'Soluções financeiras integradas' },
      'xgate': { nome: 'XGate', cor: '#fd79a8', descricao: 'Gateway de pagamentos flexível' },
      'witetec': { nome: 'WiteTec', cor: '#00b894', descricao: 'Tecnologia em pagamentos' },
      'bspay': { nome: 'BSPay', cor: '#e17055', descricao: 'Soluções de pagamento B2B' },
      'primepay7': { nome: 'PrimePay7', cor: '#74b9ff', descricao: 'Gateway de pagamentos premium' },
      'xdpag': { nome: 'XDPag', cor: '#a29bfe', descricao: 'Pagamentos digitais avançados' },
      'rede': { nome: 'Rede e.Rede', cor: '#fd79a8', descricao: 'Rede de pagamentos consolidada' },
      'stripe': { nome: 'Stripe', cor: '#6366f1', descricao: 'Gateway internacional líder' }
    };
    
    // Criar elemento para mostrar informações do adquirente
    const infoDiv = document.createElement('div');
    infoDiv.id = 'adquirente-info';
    infoDiv.className = 'alert alert-info mt-2';
    infoDiv.style.display = 'none';
    adquirenteSelect.parentNode.appendChild(infoDiv);
    
    // Event listener para mudança de adquirente
    adquirenteSelect.addEventListener('change', function() {
      const selectedValue = this.value;
      const info = adquirenteInfo[selectedValue];
      
      if (info) {
        infoDiv.innerHTML = `
          <i class="fa-solid fa-info-circle me-2"></i>
          <strong>${info.nome}:</strong> ${info.descricao}
        `;
        infoDiv.style.display = 'block';
        infoDiv.style.borderLeftColor = info.cor;
        infoDiv.style.borderLeftWidth = '4px';
      } else {
        infoDiv.style.display = 'none';
      }
    });
    
    // Validação em tempo real do valor
    valorInput.addEventListener('input', function() {
      const valor = parseFloat(this.value);
      const saldoDisponivel = {{ $gatewayWallet->saldo_disponivel ?? 0 }};
      
      if (valor > saldoDisponivel) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
      } else if (valor > 0) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
      } else {
        this.classList.remove('is-valid', 'is-invalid');
      }
    });
  });
  </script>

</x-app-layout>
