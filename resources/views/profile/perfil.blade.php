<x-app-layout :route="'Configurações'">

  <div class="main-content app-content">
    <div class="container-fluid">
      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12 mb-4">
          <div class="card border-primary">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-primary text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-gear fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Configurações</h1>
                  <p class="text-muted mb-0">Gerencie suas informações pessoais e configurações de conta</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
          <div class="card">
            <div class="card-body p-4">
              <h5 class="mb-4 fw-semibold">Menu</h5>
              <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action active" onclick="showSection('pessoal'); return false;">
                  <i class="fas fa-user me-2"></i>Pessoal
                </a>
                <a href="#" class="list-group-item list-group-item-action" onclick="showSection('seguranca'); return false;">
                  <i class="fas fa-lock me-2"></i>Segurança
                </a>
                <a href="#" class="list-group-item list-group-item-action" onclick="showSection('credenciais'); return false;">
                  <i class="fas fa-code me-2"></i>Credenciais
                </a>
                <a href="#" class="list-group-item list-group-item-action" onclick="showSection('limites'); return false;">
                  <i class="fas fa-list me-2"></i>Limites
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="col-lg-9">
          <!-- Banner do Perfil -->
          <div class="card bg-primary text-white mb-4">
            <div class="card-body p-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="icon-circle bg-white text-primary" style="width: 64px; height: 64px;">
                    <i class="fas fa-user fa-2x"></i>
                  </div>
                  <div>
                    <h4 class="mb-1">{{ strtoupper(auth()->user()->name) }}</h4>
                    <p class="mb-2 opacity-75">Usuário: {{ auth()->user()->username }}</p>
                    <span class="badge bg-success">
                      <i class="fas fa-check-circle me-1"></i>Conta ativa
                    </span>
                  </div>
                </div>
                <div class="text-end">
                  <div class="d-flex align-items-center gap-2 bg-white bg-opacity-25 rounded px-3 py-2">
                    <i class="fas fa-wallet"></i>
                    <span class="fw-bold">R$ {{ number_format(auth()->user()->saldo, 2, ',', '.') }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Seção Pessoal -->
          <div id="pessoal" class="form-section active">
            <!-- Informações Pessoais -->
            <div class="card mb-4">
              <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">
                  <i class="fas fa-user-circle me-2 text-primary"></i>Informações Pessoais
                </h6>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                      <i class="fas fa-user text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">USUÁRIO</small>
                        <span class="fw-semibold">{{ auth()->user()->username }}</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                      <i class="fas fa-id-card text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">NOME</small>
                        <span class="fw-semibold">{{ strtoupper(auth()->user()->name) }}</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                      <i class="fas fa-file-alt text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">DOCUMENTO</small>
                        <span class="fw-semibold">{{ auth()->user()->cpf_cnpj }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                      <i class="fas fa-envelope text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">EMAIL</small>
                        <span class="fw-semibold">{{ auth()->user()->email }}</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                      <i class="fas fa-phone text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">CELULAR</small>
                        <span class="fw-semibold">{{ auth()->user()->telefone }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Taxas -->
            <div class="card">
              <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold d-flex align-items-center flex-wrap gap-2">
                  <i class="fas fa-percentage me-2 text-primary"></i>Taxas
                  @if($taxas['sistema_flexivel_ativo'])
                    <span class="badge bg-info">Sistema Flexível Ativo</span>
                    @if($taxas['fonte'] === 'usuario')
                      <span class="badge bg-warning">Configuração Personalizada</span>
                    @else
                      <span class="badge bg-info">Configuração Global</span>
                    @endif
                  @endif
                </h6>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">
                  <div class="col-md-6">
                    @if($taxas['entrada']['tipo'] === 'flexivel')
                      <!-- Sistema de Taxas Flexível -->
                      <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                        <i class="fas fa-arrow-down text-success me-3"></i>
                        <div>
                          <small class="text-muted d-block">ENTRADA (VALORES BAIXOS)</small>
                          <span class="fw-semibold">R$ {{ number_format($taxas['entrada']['valor_baixo'], 2, ',', '.') }} fixo</span>
                        </div>
                      </div>
                      <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                        <i class="fas fa-arrow-down text-success me-3"></i>
                        <div>
                          <small class="text-muted d-block">ENTRADA (VALORES ALTOS)</small>
                          <span class="fw-semibold">{{ number_format($taxas['entrada']['percentual_alto'], 2, ',', '.') }}%</span>
                        </div>
                      </div>
                      <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="fas fa-arrow-up text-primary me-3"></i>
                        <div>
                          <small class="text-muted d-block">VALOR MÍNIMO</small>
                          <span class="fw-semibold">R$ {{ number_format($taxas['entrada']['valor_minimo'], 2, ',', '.') }}</span>
                        </div>
                      </div>
                    @else
                      <!-- Sistema Padrão -->
                      <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                        <i class="fas fa-arrow-down text-success me-3"></i>
                        <div>
                          <small class="text-muted d-block">ENTRADA</small>
                          <span class="fw-semibold">{{ number_format($taxas['entrada']['percentual'], 2, ',', '.') }}%</span>
                        </div>
                      </div>
                      <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="fas fa-cog text-primary me-3"></i>
                        <div>
                          <small class="text-muted d-block">TAXA MÍNIMA</small>
                          <span class="fw-semibold">R$ {{ number_format($taxas['entrada']['taxa_minima'], 2, ',', '.') }}</span>
                        </div>
                      </div>
                    @endif
                    <div class="d-flex align-items-center p-3 bg-light rounded mt-3">
                      <i class="fas fa-tachometer-alt text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">SAQUE VIA DASHBOARD</small>
                        <span class="fw-semibold">{{ number_format($taxas['saida']['dashboard'], 2, ',', '.') }}%</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                      <i class="fas fa-code text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">SAQUE VIA API</small>
                        <span class="fw-semibold">{{ number_format($taxas['saida']['api'], 2, ',', '.') }}%</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                      <i class="fab fa-bitcoin text-warning me-3"></i>
                      <div>
                        <small class="text-muted d-block">SAQUE CRIPTO</small>
                        <span class="fw-semibold">{{ number_format($taxas['saida']['cripto'], 2, ',', '.') }}%</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                      <i class="fas fa-info-circle text-info me-3"></i>
                      <div>
                        <small class="text-muted d-block">TAXA FIXA DO USUÁRIO</small>
                        <span class="fw-semibold">R$ {{ number_format($taxas['entrada']['taxa_fixa_adicional'], 2, ',', '.') }}</span>
                      </div>
                    </div>
                  </div>
                </div>
                @if($taxas['sistema_flexivel_ativo'])
                  <div class="alert alert-info mt-3 mb-0">
                    <strong><i class="fas fa-info-circle me-2"></i>Como funciona o sistema flexível:</strong><br>
                    • <strong>Depósitos abaixo de R$ {{ number_format($taxas['entrada']['valor_minimo'], 2, ',', '.') }}:</strong> Taxa fixa de R$ {{ number_format($taxas['entrada']['valor_baixo'], 2, ',', '.') }}<br>
                    • <strong>Depósitos acima de R$ {{ number_format($taxas['entrada']['valor_minimo'], 2, ',', '.') }}:</strong> Taxa percentual de {{ number_format($taxas['entrada']['percentual_alto'], 2, ',', '.') }}%<br>
                    • <strong>Taxa fixa adicional:</strong> R$ {{ number_format($taxas['entrada']['taxa_fixa_adicional'], 2, ',', '.') }} (sempre aplicada)
                    @if($taxas['fonte'] === 'usuario')
                      <br><br><strong>ℹ️ Você está usando configurações personalizadas de taxa!</strong>
                    @endif
                  </div>
                @elseif($taxas['fonte'] === 'usuario')
                  <div class="alert alert-warning mt-3 mb-0">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Você está usando configurações personalizadas de taxa!</strong><br>
                    Suas taxas personalizadas têm prioridade sobre as configurações globais do sistema.
                  </div>
                @endif
              </div>
            </div>
          </div>

          <!-- Seção Segurança -->
          <div id="seguranca" class="form-section" style="display: none;">
            <h5 class="mb-4 fw-semibold">Segurança</h5>
            
            <!-- 2FA -->
            <div class="card mb-4">
              <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                  <div class="icon-circle bg-warning text-white me-3">
                    <i class="fas fa-shield-halved"></i>
                  </div>
                  <div>
                    <h6 class="mb-1 fw-semibold">Autenticação 2FA</h6>
                    <span id="2fa-status-badge" class="badge bg-warning text-dark">Verificando...</span>
                  </div>
                </div>
                <p class="text-muted mb-3">Com a autenticação de dois fatores, além da sua senha, você precisará de um código gerado por um aplicativo de autenticação, garantindo uma proteção adicional.</p>
                <div id="2fa-actions">
                  <button id="btn-2fa-setup" class="btn btn-primary" style="display: none;">
                    <i class="fas fa-cog me-1"></i>Configurar 2FA
                  </button>
                  <button id="btn-2fa-disable" class="btn btn-danger" style="display: none;">
                    <i class="fas fa-times me-1"></i>Desativar 2FA
                  </button>
                </div>
              </div>
            </div>

            <!-- PIN -->
            <div class="card mb-4">
              <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                  <div class="icon-circle bg-success text-white me-3">
                    <i class="fas fa-key"></i>
                  </div>
                  <div>
                    <h6 class="mb-1 fw-semibold">PIN</h6>
                    @php
                      $pinInfo = \App\Helpers\PinHelper::getPinInfo(auth()->user());
                    @endphp
                    @if($pinInfo['is_active'])
                      <span class="badge bg-success">Ativo</span>
                    @elseif($pinInfo['has_pin'])
                      <span class="badge bg-warning">Inativo</span>
                    @else
                      <span class="badge bg-secondary">Não configurado</span>
                    @endif
                  </div>
                </div>
                <p class="text-muted mb-3">Adicione uma camada extra de segurança à sua conta com um PIN. O PIN será necessário para realizar transferências e fazer alterações na conta, garantindo uma proteção adicional.</p>
                
                <div class="d-flex gap-2 mb-3">
                  @if($pinInfo['has_pin'])
                    @if($pinInfo['is_active'])
                      <button class="btn btn-outline-warning btn-sm" onclick="togglePin(false)">
                        <i class="fas fa-toggle-off me-1"></i>Desativar
                      </button>
                    @else
                      <button class="btn btn-outline-success btn-sm" onclick="togglePin(true)">
                        <i class="fas fa-toggle-on me-1"></i>Ativar
                      </button>
                    @endif
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changePinModal">
                      <i class="fas fa-edit me-1"></i>Alterar
                    </button>
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#removePinModal">
                      <i class="fas fa-trash me-1"></i>Remover
                    </button>
                  @else
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPinModal">
                      <i class="fas fa-plus me-1"></i>Criar PIN
                    </button>
                  @endif
                </div>
                
                @if($pinInfo['has_pin'])
                  <small class="text-muted">
                    @if($pinInfo['created_at'])
                      Criado em: {{ $pinInfo['created_at'] }}
                    @endif
                    @if($pinInfo['days_since_creation'])
                      ({{ $pinInfo['days_since_creation'] }} dias atrás)
                    @endif
                  </small>
                @endif
              </div>
            </div>

            <!-- Senha -->
            <div class="card">
              <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                  <div class="icon-circle bg-primary text-white me-3">
                    <i class="fas fa-lock"></i>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-semibold">Senha de acesso</h6>
                  </div>
                </div>
                <p class="text-muted mb-3">Mantenha a segurança da sua conta mantendo sua senha atualizada.</p>
                <button class="btn btn-primary" onclick="openPasswordModal()">
                  <i class="fas fa-key me-1"></i>Alterar senha
                </button>
              </div>
            </div>
          </div>

          <!-- Seção Credenciais -->
          <div id="credenciais" class="form-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="mb-0 fw-semibold">Gerenciar Credenciais</h5>
              <a href="{{ route('documentacao') }}" class="btn btn-outline-secondary" target="_blank">
                <i class="fas fa-book me-1"></i>Documentação API
              </a>
            </div>

            <div class="card mb-4">
              <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">
                  <i class="fas fa-key me-2 text-primary"></i>Informações da API
                </h6>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                      <i class="fas fa-user-lock text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">CLIENT ID</small>
                        <span class="fw-semibold">{{ auth()->user()->cliente_id ?? 'hkscripts_2737188679518767' }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                      <i class="fas fa-globe text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">PROJETO</small>
                        <span class="fw-semibold">{{ auth()->user()->username }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                      <i class="fas fa-clock text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">GERADO ÀS</small>
                        <span class="fw-semibold">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                      <i class="fas fa-list text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">DESCRIÇÃO</small>
                        <span class="fw-semibold">{{ auth()->user()->username }} sistema</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">
                  <i class="fas fa-network-wired me-2 text-primary"></i>IP's Permitidos
                </h6>
              </div>
              <div class="card-body p-4">
                <p class="text-muted mb-3">Endereços liberados para controle do seu financeiro via API.</p>
                
                <!-- Lista de IPs -->
                <div id="ips-list" class="mb-3">
                  @php
                    $allowedIPs = \App\Traits\IPManagementTrait::getAllowedIPs(auth()->user());
                  @endphp
                  
                  @if(count($allowedIPs) > 0)
                    <div class="list-group">
                      @foreach($allowedIPs as $ip)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                          <div>
                            <i class="fas fa-globe text-primary me-2"></i>
                            <span class="fw-semibold">{{ $ip }}</span>
                          </div>
                          <button class="btn btn-outline-danger btn-sm" onclick="removeIP('{{ $ip }}')">
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      @endforeach
                    </div>
                  @else
                    <div class="text-center text-muted py-4 bg-light rounded">
                      <i class="fas fa-info-circle fa-2x mb-2"></i>
                      <p class="mb-0">Nenhum IP configurado</p>
                    </div>
                  @endif
                </div>
                
                <!-- Formulário para adicionar IP -->
                <form action="{{ route('profile.add-ip') }}" method="POST">
                  @csrf
                  <div class="row g-2">
                    <div class="col-md-8">
                      <input type="text" name="ip" class="form-control" 
                          placeholder="Ex: 192.168.1.1, 192.168.1.0/24, 192.168.1.*" required>
                    </div>
                    <div class="col-md-4">
                      <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-1"></i>Adicionar IP
                      </button>
                    </div>
                  </div>
                  <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle me-1"></i>Suporte: IP único, CIDR (192.168.1.0/24) ou wildcard (192.168.1.*)
                  </small>
                </form>
              </div>
            </div>
          </div>

          <!-- Seção Limites -->
          <div id="limites" class="form-section" style="display: none;">
            <h5 class="mb-4 fw-semibold">Gerenciamento de Limites</h5>
            <div class="card">
              <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">
                  <i class="fas fa-chart-line me-2 text-primary"></i>Limites de Transação
                </h6>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                      <i class="fas fa-calendar-day text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">LIMITE DIÁRIO</small>
                        <span class="fw-semibold">R$ {{ number_format(auth()->user()->limite_diario ?? 10000, 2, ',', '.') }}</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                      <i class="fas fa-id-card text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">LIMITE POR CPF</small>
                        <span class="fw-semibold">R$ {{ number_format(auth()->user()->limite_por_cpf ?? 5000, 2, ',', '.') }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                      <i class="fas fa-arrow-up text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">LIMITE POR SAQUE</small>
                        <span class="fw-semibold">R$ {{ number_format(auth()->user()->limite_por_saque ?? 10000, 2, ',', '.') }}</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                      <i class="fas fa-list-ol text-primary me-3"></i>
                      <div>
                        <small class="text-muted d-block">SAQUES POR CPF</small>
                        <span class="fw-semibold">{{ auth()->user()->saques_por_cpf ?? 5 }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function showSection(sectionName) {
      // Esconder todas as seções
      const sections = document.querySelectorAll('.form-section');
      sections.forEach(section => {
        section.style.display = 'none';
      });

      // Remover active de todos os nav items
      const navItems = document.querySelectorAll('.list-group-item');
      navItems.forEach(item => {
        item.classList.remove('active');
      });

      // Mostrar a seção selecionada
      document.getElementById(sectionName).style.display = 'block';

      // Adicionar active ao nav item clicado
      event.target.classList.add('active');
    }

    function removeIP(ip) {
      if (confirm('Tem certeza que deseja remover este IP?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("profile.remove-ip") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const ipInput = document.createElement('input');
        ipInput.type = 'hidden';
        ipInput.name = 'ip';
        ipInput.value = ip;
        
        form.appendChild(csrfToken);
        form.appendChild(ipInput);
        document.body.appendChild(form);
        form.submit();
      }
    }

    function togglePin(active) {
      if (confirm(active ? 'Tem certeza que deseja ativar o PIN?' : 'Tem certeza que deseja desativar o PIN?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("profile.toggle-pin") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const activeInput = document.createElement('input');
        activeInput.type = 'hidden';
        activeInput.name = 'active';
        activeInput.value = active ? '1' : '0';
        
        form.appendChild(csrfToken);
        form.appendChild(activeInput);
        document.body.appendChild(form);
        form.submit();
      }
    }

    // ===== FUNCIONALIDADES DO 2FA =====
    
    document.addEventListener('DOMContentLoaded', function() {
      check2FAStatus();
    });

    async function check2FAStatus() {
      try {
        const response = await fetch('/2fa/status');
        const data = await response.json();
        update2FAUI(data);
      } catch (error) {
        console.error('Erro ao verificar status do 2FA:', error);
        update2FAUI({ enabled: false, configured: false });
      }
    }

    function update2FAUI(status) {
      const statusBadge = document.getElementById('2fa-status-badge');
      const setupBtn = document.getElementById('btn-2fa-setup');
      const disableBtn = document.getElementById('btn-2fa-disable');

      if (status.enabled) {
        statusBadge.textContent = 'Ativo';
        statusBadge.className = 'badge bg-success';
        setupBtn.style.display = 'none';
        disableBtn.style.display = 'inline-block';
      } else if (status.configured) {
        statusBadge.textContent = 'Inativo';
        statusBadge.className = 'badge bg-warning text-dark';
        setupBtn.textContent = 'Ativar 2FA';
        setupBtn.style.display = 'inline-block';
        disableBtn.style.display = 'none';
      } else {
        statusBadge.textContent = 'Não configurado';
        statusBadge.className = 'badge bg-secondary';
        setupBtn.innerHTML = '<i class="fas fa-cog me-1"></i>Configurar 2FA';
        setupBtn.style.display = 'inline-block';
        disableBtn.style.display = 'none';
      }
    }

    const setupBtn = document.getElementById('btn-2fa-setup');
    if (setupBtn) {
      setupBtn.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('setup2FAModal'));
        modal.show();
      });
    }

    document.getElementById('btn-2fa-disable').addEventListener('click', function() {
      const modal = new bootstrap.Modal(document.getElementById('disable2FAModal'));
      modal.show();
    });

    async function generateQrCode() {
      const btn = document.getElementById('btn-generate-qr');
      if (!btn) return;
      
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Gerando...';

      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const response = await fetch('/2fa/generate-qr', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          }
        });

        const data = await response.json();

        if (data.success) {
          document.getElementById('qr-code-container').innerHTML = data.qr_code;
          document.getElementById('manual-key').value = data.manual_entry_key;
          document.getElementById('2fa-setup-step1').style.display = 'none';
          document.getElementById('2fa-setup-step2').style.display = 'block';
        } else {
          showAlert('Erro ao gerar QR Code: ' + (data.message || 'Erro desconhecido'), 'danger');
        }
      } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro ao gerar QR Code: ' + error.message, 'danger');
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-qrcode me-1"></i>Gerar QR Code';
      }
    }

    document.addEventListener('click', function(e) {
      if (e.target && e.target.id === 'btn-generate-qr') {
        generateQrCode();
      }
    });

    async function verifyAndEnable2FA() {
      const code = document.getElementById('verification-code').value;
      
      if (!code || code.length !== 6) {
        showAlert('Digite um código de 6 dígitos', 'warning');
        return;
      }

      const btn = document.getElementById('btn-verify-code');
      if (!btn) return;
      
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verificando...';

      try {
        const response = await fetch('/2fa/enable', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ code: code })
        });

        const data = await response.json();

        if (data.success) {
          showAlert('2FA ativado com sucesso!', 'success');
          bootstrap.Modal.getInstance(document.getElementById('setup2FAModal')).hide();
          check2FAStatus();
        } else {
          showAlert(data.message || 'Código inválido', 'danger');
        }
      } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro ao ativar 2FA', 'danger');
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Verificar e Ativar 2FA';
      }
    }

    document.addEventListener('click', function(e) {
      if (e.target && e.target.id === 'btn-verify-code') {
        verifyAndEnable2FA();
      }
    });

    async function disable2FA() {
      const code = document.getElementById('disable-code').value;
      
      if (!code || code.length !== 6) {
        showAlert('Digite um código de 6 dígitos', 'warning');
        return;
      }

      const btn = document.getElementById('btn-disable-2fa');
      if (!btn) return;
      
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Desativando...';

      try {
        const response = await fetch('/2fa/disable', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ code: code })
        });

        const data = await response.json();

        if (data.success) {
          showAlert('2FA desativado com sucesso!', 'success');
          bootstrap.Modal.getInstance(document.getElementById('disable2FAModal')).hide();
          check2FAStatus();
        } else {
          showAlert(data.message || 'Código inválido', 'danger');
        }
      } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro ao desativar 2FA', 'danger');
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-times me-1"></i>Desativar 2FA';
      }
    }

    document.addEventListener('click', function(e) {
      if (e.target && e.target.id === 'btn-disable-2fa') {
        disable2FA();
      }
    });

    function copyToClipboard(elementId) {
      const element = document.getElementById(elementId);
      element.select();
      element.setSelectionRange(0, 99999);
      document.execCommand('copy');
      showAlert('Chave copiada para área de transferência!', 'success');
    }

    function showAlert(message, type) {
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
      alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      document.body.appendChild(alertDiv);
      
      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.parentNode.removeChild(alertDiv);
        }
      }, 5000);
    }

    document.getElementById('setup2FAModal').addEventListener('hidden.bs.modal', function() {
      document.getElementById('2fa-setup-step1').style.display = 'block';
      document.getElementById('2fa-setup-step2').style.display = 'none';
      document.getElementById('qr-code-container').innerHTML = '';
      document.getElementById('manual-key').value = '';
      document.getElementById('verification-code').value = '';
    });

    document.getElementById('disable2FAModal').addEventListener('hidden.bs.modal', function() {
      document.getElementById('disable-code').value = '';
    });
  </script>

  <!-- Modal Criar PIN -->
  <div class="modal fade" id="createPinModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">
            <i class="fas fa-key me-2"></i>Criar PIN
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('profile.create-pin') }}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="pin" class="form-label">PIN (6 dígitos)</label>
              <input type="text" class="form-control" 
                  id="pin" name="pin" maxlength="6" pattern="\d{6}" 
                  placeholder="000000" required>
              <div class="form-text">Digite um PIN de 6 dígitos numéricos</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-check me-1"></i>Criar PIN
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Alterar PIN -->
  <div class="modal fade" id="changePinModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">
            <i class="fas fa-edit me-2"></i>Alterar PIN
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('profile.change-pin') }}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="current_pin" class="form-label">PIN Atual</label>
              <input type="text" class="form-control" 
                  id="current_pin" name="current_pin" maxlength="6" pattern="\d{6}" 
                  placeholder="000000" required>
            </div>
            <div class="mb-3">
              <label for="new_pin" class="form-label">Novo PIN (6 dígitos)</label>
              <input type="text" class="form-control" 
                  id="new_pin" name="new_pin" maxlength="6" pattern="\d{6}" 
                  placeholder="000000" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-check me-1"></i>Alterar PIN
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Remover PIN -->
  <div class="modal fade" id="removePinModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title fw-bold">
            <i class="fas fa-trash me-2"></i>Remover PIN
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('profile.remove-pin') }}" method="POST">
          @csrf
          <div class="modal-body">
            <p class="text-muted mb-3">Digite seu PIN atual para confirmar a remoção:</p>
            <div class="mb-3">
              <label for="current_pin_remove" class="form-label">PIN Atual</label>
              <input type="text" class="form-control" 
                  id="current_pin_remove" name="current_pin" maxlength="6" pattern="\d{6}" 
                  placeholder="000000" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">
              <i class="fas fa-trash me-1"></i>Remover PIN
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Configurar 2FA -->
  <div class="modal fade" id="setup2FAModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold">
            <i class="fas fa-shield-halved me-2"></i>Configurar Autenticação 2FA
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="2fa-setup-step1">
            <div class="alert alert-info">
              <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i>Passo 1: Instalar App Autenticador</h6>
              <p class="mb-0">Instale um aplicativo autenticador no seu celular:</p>
              <ul class="mb-0 mt-2">
                <li><strong>Google Authenticator</strong> (iOS/Android)</li>
                <li><strong>Microsoft Authenticator</strong> (iOS/Android)</li>
                <li><strong>Authy</strong> (iOS/Android)</li>
              </ul>
            </div>
            <div class="text-center">
              <button id="btn-generate-qr" class="btn btn-primary">
                <i class="fas fa-qrcode me-1"></i>Gerar QR Code
              </button>
            </div>
          </div>
          
          <div id="2fa-setup-step2" style="display: none;" class="mt-3">
            <div class="alert alert-success">
              <h6 class="fw-bold"><i class="fas fa-qrcode me-2"></i>Passo 2: Escanear QR Code</h6>
              <p class="mb-0">Escaneie o QR Code abaixo com seu app autenticador:</p>
            </div>
            <div class="text-center mb-3">
              <div id="qr-code-container"></div>
            </div>
            <div class="alert alert-warning">
              <h6 class="fw-bold"><i class="fas fa-key me-2"></i>Chave Manual (se não conseguir escanear)</h6>
              <div class="input-group">
                <input type="text" id="manual-key" class="form-control" readonly>
                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('manual-key')">
                  <i class="fas fa-copy"></i>
                </button>
              </div>
            </div>
            <div class="alert alert-info">
              <h6 class="fw-bold"><i class="fas fa-check-circle me-2"></i>Passo 3: Verificar Código</h6>
              <p class="mb-0">Digite o código de 6 dígitos gerado pelo seu app:</p>
            </div>
            <div class="mb-3">
              <label for="verification-code" class="form-label">Código de Verificação</label>
              <input type="text" id="verification-code" class="form-control" placeholder="000000" maxlength="6" pattern="[0-9]{6}">
            </div>
            <div class="text-center">
              <button id="btn-verify-code" class="btn btn-success">
                <i class="fas fa-check me-1"></i>Verificar e Ativar 2FA
              </button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Desativar 2FA -->
  <div class="modal fade" id="disable2FAModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title fw-bold">
            <i class="fas fa-shield-xmark me-2"></i>Desativar Autenticação 2FA
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning">
            <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Atenção!</h6>
            <p class="mb-0">Desativar o 2FA reduzirá a segurança da sua conta. Para confirmar, digite o código atual do seu app autenticador:</p>
          </div>
          <div class="mb-3">
            <label for="disable-code" class="form-label">Código de Verificação</label>
            <input type="text" id="disable-code" class="form-control" placeholder="000000" maxlength="6" pattern="[0-9]{6}">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button id="btn-disable-2fa" class="btn btn-danger">
            <i class="fas fa-times me-1"></i>Desativar 2FA
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Alteração de Senha -->
  <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold" id="passwordModalLabel">
            <i class="fas fa-lock me-2"></i>Alterar Senha
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('password.update') }}" id="passwordForm">
            @csrf
            @method('put')
            
            <!-- Senha Atual -->
            <div class="mb-3">
              <label for="current_password" class="form-label">Senha Atual</label>
              <div class="position-relative">
                <input type="password" 
                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                    id="current_password" 
                    name="current_password" 
                    required>
                <button type="button" class="btn btn-link position-absolute" onclick="togglePassword('current_password')" style="right: 5px; top: 0;">
                  <i class="fas fa-eye text-muted"></i>
                </button>
              </div>
              @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Nova Senha -->
            <div class="mb-3">
              <label for="password" class="form-label">Nova Senha</label>
              <div class="position-relative">
                <input type="password" 
                    class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                    id="password" 
                    name="password" 
                    required>
                <button type="button" class="btn btn-link position-absolute" onclick="togglePassword('password')" style="right: 5px; top: 0;">
                  <i class="fas fa-eye text-muted"></i>
                </button>
              </div>
              
              <!-- Requisitos da Senha -->
              <div class="password-requirements mt-2 small text-muted" id="passwordRequirements" style="display: none;">
                <strong>Requisitos da senha:</strong>
                <ul class="mb-0">
                  <li id="req-length">Pelo menos 8 caracteres</li>
                  <li id="req-lowercase">Uma letra minúscula (a-z)</li>
                  <li id="req-uppercase">Uma letra maiúscula (A-Z)</li>
                  <li id="req-number">Um número (0-9)</li>
                  <li id="req-special">Um caractere especial (@$!%*?&+#^~`|/:";'<>,.=-_[]{}())</li>
                </ul>
              </div>
              
              @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Confirmar Nova Senha -->
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
              <div class="position-relative">
                <input type="password" 
                    class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required>
                <button type="button" class="btn btn-link position-absolute" onclick="togglePassword('password_confirmation')" style="right: 5px; top: 0;">
                  <i class="fas fa-eye text-muted"></i>
                </button>
              </div>
              @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" onclick="submitPasswordForm()">
            <i class="fas fa-check me-1"></i>Alterar Senha
          </button>
        </div>
      </div>
    </div>
  </div>

  <style>
    .form-section {
      display: none;
    }
    .form-section.active {
      display: block;
    }
    .password-requirements li.valid {
      color: #198754;
    }
    .password-requirements li.valid::before {
      content:"✓";
      font-weight: bold;
    }
    
    /* Corrige o problema do hover sobrescrever o active */
    .list-group-item-action.active {
      color: #fff !important;
      background-color: #6200ea !important;
      border-color: #6200ea !important;
      z-index: 2 !important;
    }
    
    .list-group-item-action.active:hover,
    .list-group-item-action.active:focus {
      color: #fff !important;
      background-color: #6200ea !important;
      border-color: #6200ea !important;
      opacity: 0.95;
    }
  </style>

  <script>
    function validatePassword(password) {
      const requirements = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        number: /\d/.test(password),
        special: /[@$!%*?&+#^~`|\\/:";'<>,.=\-_\[\]{}()]/.test(password)
      };
      
      return requirements;
    }

    function updatePasswordRequirements(password) {
      const requirements = validatePassword(password);
      const requirementsDiv = document.getElementById('passwordRequirements');
      
      if (password.length > 0) {
        requirementsDiv.style.display = 'block';
      } else {
        requirementsDiv.style.display = 'none';
      }
      
      Object.keys(requirements).forEach((key) => {
        const element = document.getElementById(`req-${key}`);
        if (element) {
          if (requirements[key]) {
            element.classList.add('valid');
          } else {
            element.classList.remove('valid');
          }
        }
      });
    }

    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      const button = input.nextElementSibling;
      const icon = button.querySelector('i');
      
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }

    function openPasswordModal() {
      const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
      modal.show();
    }

    function submitPasswordForm() {
      document.getElementById('passwordForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
      const passwordInput = document.getElementById('password');
      if (passwordInput) {
        passwordInput.addEventListener('input', function(e) {
          updatePasswordRequirements(e.target.value);
        });
        
        passwordInput.addEventListener('focus', function(e) {
          if (e.target.value.length > 0) {
            updatePasswordRequirements(e.target.value);
          }
        });
      }
    });
  </script>

</x-app-layout>
