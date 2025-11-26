@php
  use App\Helpers\Helper;

  $setting = Helper::getSetting();
  $nivel = Helper::meuNivel(auth()->user());

  function formatK($value) {
    if ($value >= 1000000 && fmod($value, 1000000) === 0.0) {
      return 'R$ ' . number_format($value / 1000000, 0, ',', '.') . 'M';
    } elseif ($value >= 1000 && fmod($value, 1000) === 0.0) {
      return 'R$ ' . number_format($value / 1000, 0, ',', '.') . 'k';
    } else {
      return 'R$ ' . number_format($value, 2, ',', '.');
    }
  }

  $totalDepositos = $nivel['total_depositos'];
  $min = $nivel['nivel_atual']?->minimo ?? 0;
  $max = $nivel['nivel_atual']?->maximo ?? 0;

  $porcentagem = 0;

  if ($max > 0) {
    // Calcular porcentagem do total em relação ao máximo
    $raw = ($totalDepositos / $max) * 100;
    $porcentagem = min(100, max(0, round($raw, 2)));
  }
@endphp

<style>
  .drawer {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 14px;
    position: relative;
    width: 100% !important;
    height: 100%;
    overflow-x: hidden !important;
    contain: layout style;
    will-change: transform;
  }
  
  /* Scrollbar customizada para a sidebar */
  #layoutDrawer_nav::-webkit-scrollbar {
    width: 6px;
  }
  
  #layoutDrawer_nav::-webkit-scrollbar-track {
    background: transparent;
  }
  
  #layoutDrawer_nav::-webkit-scrollbar-thumb {
    background: #e4e4e7;
    border-radius: 3px;
    transition: background 0.2s ease;
  }
  
  #layoutDrawer_nav::-webkit-scrollbar-thumb:hover {
    background: #d4d4d8;
  }
  
  #layoutDrawer_nav {
    scrollbar-width: thin;
    scrollbar-color: #e4e4e7 transparent;
  }


  /* ========================================
    SIDEBAR COLAPSADA (desktop)
    Usa body.drawer-toggled em desktop
    ======================================== */
  
  /* Textos controlados */
  @media (min-width: 1200px) {
    /* Estado expandido - textos visíveis */
    body:not(.drawer-toggled) .drawer .nav-link-text,
    body:not(.drawer-toggled) .drawer .drawer-collapse-arrow,
    body:not(.drawer-toggled) .drawer .drawer-menu-heading {
      display: inline-block !important;
      opacity: 1 !important;
      width: auto !important;
      visibility: visible !important;
      white-space: nowrap !important;
      overflow: visible !important;
    }
    
    /* Estado colapsado - textos escondidos */
    body.drawer-toggled .drawer .nav-link-text,
    body.drawer-toggled .drawer .drawer-collapse-arrow,
    body.drawer-toggled .drawer .drawer-menu-heading {
      display: none !important;
      opacity: 0 !important;
      width: 0 !important;
      overflow: hidden !important;
      visibility: hidden !important;
    }
    
    body.drawer-toggled .drawer .nivel-progress-container {
      display: none !important;
    }
    
    /* Links centralizados - apenas ícones */
    body.drawer-toggled .drawer .nav-link {
      justify-content: center !important;
      align-items: center !important;
      padding: 0.75rem 0 !important;
      margin: 0.25rem 0.25rem !important;
      width: calc(100% - 0.5rem) !important;
      min-height: 44px !important;
    }
    
    /* Ícones - estado expandido */
    body:not(.drawer-toggled) .drawer .nav-link-icon {
      margin-right: 0.625rem !important;
    }
    
    body:not(.drawer-toggled) .drawer .nav-link-icon i {
      font-size: 0.875rem !important;
    }
    
    /* Ícones - estado colapsado (centralizados e maiores) */
    body.drawer-toggled .drawer .nav-link-icon {
      margin: 0 !important;
      width: auto !important;
      height: auto !important;
    }
    
    body.drawer-toggled .drawer .nav-link-icon i {
      font-size: 1.125rem !important;
      color: #a1a1aa !important;
    }
    
    body.drawer-toggled .drawer .nav-link:hover .nav-link-icon i {
      color: #18181b !important;
    }
    
    body.drawer-toggled .drawer .nav-link.active .nav-link-icon i {
      color: #fafafa !important;
    }
    
    /* Submenus escondidos */
    body.drawer-toggled .drawer .drawer-menu-nested {
      display: none !important;
    }
    
    body.drawer-toggled .drawer .drawer-menu-divider {
      margin: 0.75rem 0.5rem !important;
    }
    
    /* Sidebar fica menor */
    body.drawer-toggled #layoutDrawer_nav {
      width: 70px !important;
      flex-basis: 70px !important;
    }
    
    /* Conteúdo se ajusta */
    body.drawer-toggled #layoutDrawer_content {
      padding-left: 70px !important;
    }
  }

  /* Tooltip para modo colapsado - funciona com body.drawer-toggled */
  @media (min-width: 1200px) {
    body.drawer-toggled .drawer .nav-link {
      position: relative;
    }

    body.drawer-toggled .drawer .nav-link[data-tooltip]::after {
      content: attr(data-tooltip);
      position: absolute;
      left: calc(100% + 10px);
      top: 50%;
      transform: translateY(-50%);
      padding: 0.5rem 0.75rem;
      background-color: #18181b;
      color: #fafafa;
      font-size: 0.875rem;
      font-weight: 500;
      border-radius: 0.5rem;
      white-space: nowrap;
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transition: all 0.2s ease;
      z-index: 10000;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    body.drawer-toggled .drawer .nav-link[data-tooltip]:hover::after {
      opacity: 1;
      visibility: visible;
    }
  }

  /* Previne flash dos collapse - Otimizado */
  .drawer .collapse {
    display: none;
  }
  
  .drawer .collapse.show {
    display: block;
  }
  
  .drawer .collapse.collapsing {
    display: block;
    height: 0;
    overflow: hidden;
    transition: height 0.2s ease;
    will-change: height;
    transform: translateZ(0);
    backface-visibility: hidden;
  }
  
  /* Links do menu */
  .drawer .nav-link {
    display: flex !important;
    align-items: center;
    padding: 0.5rem 0.75rem;
    color: #71717a;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.375rem;
    margin: 0.125rem 0.5rem;
    transition: all 0.15s ease;
    text-decoration: none;
  }
  
  .drawer .nav-link:hover {
    background-color: #f4f4f5;
    color: #18181b;
  }
  
  .drawer .nav-link.active {
    background-color: var(--white-sidebar-color);
    color: #fafafa;
  }
  
  .drawer .nav-link.active .nav-link-icon i {
    color: #fafafa;
  }
  
  /* Seta de expansão */
  .drawer-collapse-arrow {
    margin-left: auto;
    display: flex;
    align-items: center;
    transition: transform 0.2s ease;
  }
  
  .drawer-collapse-arrow i {
    font-size: 0.875rem;
    color: #a1a1aa;
  }
  
  .nav-link[aria-expanded="true"] .drawer-collapse-arrow {
    transform: rotate(180deg);
  }
  
  /* Submenu */
  .drawer-menu-nested {
    padding-left: 0;
    margin: 0.125rem 0;
    contain: layout;
  }
  
  .drawer-menu-nested .nav-link {
    padding: 0.4rem 0.75rem 0.4rem 2.5rem;
    font-size: 0.8125rem;
    font-weight: 400;
    color: #a1a1aa;
  }
  
  .drawer-menu-nested .nav-link:hover {
    color: #18181b;
    background-color: #f4f4f5;
  }
  
  .drawer-menu-nested .nav-link.active {
    background-color: #f4f4f5;
    color: #18181b;
    font-weight: 500;
  }
  
  /* Cabeçalho de seção */
  .drawer-menu-heading {
    padding: 1rem 0.75rem 0.375rem;
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #a1a1aa;
  }
  
  /* Divider */
  .drawer-menu-divider {
    height: 1px;
    background-color: #e4e4e7;
    margin: 0.75rem 0.75rem;
  }
  
  /* Barra de progresso */
  .nivel-progress-container {
    padding: 0.75rem;
    margin: 0.5rem;
    background-color: #fafafa;
    border: 1px solid #e4e4e7;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
  }
  
  .nivel-progress-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
  }
  
  .nivel-progress-label small {
    font-size: 0.6875rem;
    font-weight: 600;
    color: #52525b;
  }
  
  .progress {
    border-radius: 50px;
    background-color: #e4e4e7;
    height: 6px;
    overflow: hidden;
  }
  
  .progress-bar {
    background-color: #18181b;
    border-radius: 50px;
    transition: width 0.4s ease;
  }
  
  .nivel-percentage {
    font-size: 0.6875rem;
    font-weight: 600;
    color: #18181b;
  }
</style>

<div id="layoutDrawer_nav">
  <nav class="bg-white drawer accordion drawer-light" id="drawerAccordion">
    <div class="drawer-menu">
      <div class="nav" style="padding: 1rem 0 2rem 0;">

        @if($setting->niveis_ativo && $nivel['nivel_atual'])
          <div class="nivel-progress-container">
            <div class="nivel-progress-label">
              <small>{{ formatK($totalDepositos) }} / {{ formatK($max) }}</small>
              <small class="nivel-percentage">{{ number_format($porcentagem, 2, ',', '.') }}%</small>
            </div>
            <div class="progress">
              <div class="progress-bar" 
                 role="progressbar" 
                 aria-valuemin="0" 
                 aria-valuemax="100"
                 aria-valuenow="{{ (int) $porcentagem }}"
                 style="width: {{ $porcentagem }}%;">
              </div>
            </div>
          </div>
        @endif

        @if($status == 1)
          <!-- Dashboard -->
          <a class="nav-link" href="/dashboard?produto=todos&periodo=hoje" data-tooltip="Dashboard">
            <div class="nav-link-icon"><i class="fa-solid fa-house"></i></div>
            <span class="nav-link-text">Dashboard</span>
          </a>

          <!-- Fale com seu Gerente -->
          <a class="nav-link" href="javascript:void(0);" onclick="openGerenteModal()" data-tooltip="Fale com seu Gerente">
            <div class="nav-link-icon"><i class="fa-solid fa-headset"></i></div>
            <span class="nav-link-text">Fale com seu Gerente</span>
          </a>
        @elseif($status == 5)
          <!-- Fale com seu Gerente - Para usuários em análise -->
          <a class="nav-link" href="javascript:void(0);" onclick="openGerenteModal()" data-tooltip="Fale com seu Gerente">
            <div class="nav-link-icon"><i class="fa-solid fa-headset"></i></div>
            <span class="nav-link-text">Fale com seu Gerente</span>
          </a>
        @endif

        @if($status == 1)
          <!-- Relatórios -->
          <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseDashboards" aria-expanded="false" aria-controls="collapseDashboards" data-tooltip="Relatórios">
            <div class="nav-link-icon"><i class="fa-solid fa-chart-line"></i></div>
            <span class="nav-link-text">Relatórios</span>
            <div class="drawer-collapse-arrow"><i class="fa-solid fa-chevron-down"></i></div>
          </a>
          <div class="collapse" id="collapseDashboards" data-bs-parent="#drawerAccordion">
            <nav class="drawer-menu-nested nav">
              <a class="nav-link" href="/relatorio/entradas?periodo=hoje">Entradas</a>
              <a class="nav-link" href="/relatorio/saidas?periodo=hoje">Saídas</a>
            </nav>
          </div>

          <!-- Financeiro -->
          <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseFinanceiro" aria-expanded="false" aria-controls="collapseFinanceiro" data-tooltip="Financeiro">
            <div class="nav-link-icon"><i class="fa-solid fa-wallet"></i></div>
            <span class="nav-link-text">Financeiro</span>
            <div class="drawer-collapse-arrow"><i class="fa-solid fa-chevron-down"></i></div>
          </a>
          <div class="collapse" id="collapseFinanceiro" data-bs-parent="#drawerAccordion">
            <nav class="drawer-menu-nested nav">
              <a class="nav-link" href="/financeiro">Dashboard Financeiro</a>
              <a class="nav-link" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addsaldo">
                <i class="fas fa-credit-card me-2"></i>
                Depositar Agora
              </a>
              <a class="nav-link" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#saquepix" data-saldo="8470.04">
                <i class="fas fa-money-bill-wave me-2"></i>
                Sacar Agora
              </a>
            </nav>
          </div>

          <!-- Produtos & Integrações -->
          <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseProdutos" aria-expanded="false" aria-controls="collapseProdutos" data-tooltip="Produtos & Integrações">
            <div class="nav-link-icon"><i class="fa-solid fa-cube"></i></div>
            <span class="nav-link-text">Produtos & Integrações</span>
            <div class="drawer-collapse-arrow"><i class="fa-solid fa-chevron-down"></i></div>
          </a>
          <div class="collapse" id="collapseProdutos" data-bs-parent="#drawerAccordion">
            <nav class="drawer-menu-nested nav">
              <a class="nav-link" href="/produtos">Produtos</a>
              <a class="nav-link" href="/integracoes">Integrações</a>
            </nav>
          </div>

          <!-- Desenvolvedor -->
          <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseDev" aria-expanded="false" aria-controls="collapseDev" data-tooltip="Desenvolvedor">
            <div class="nav-link-icon"><i class="fa-solid fa-code"></i></div>
            <span class="nav-link-text">Desenvolvedor</span>
            <div class="drawer-collapse-arrow"><i class="fa-solid fa-chevron-down"></i></div>
          </a>
          <div class="collapse" id="collapseDev" data-bs-parent="#drawerAccordion">
            <nav class="drawer-menu-nested nav">
              <a class="nav-link" href="/documentacao">Documentação</a>
              <a class="nav-link" href="/chaves">Chave API</a>
              <a class="nav-link" href="/webhook">Webhooks</a>
            </nav>
          </div>

          <!-- Afiliados -->
          <a class="nav-link" href="/affiliate" data-tooltip="Meus Referidos">
            <div class="nav-link-icon"><i class="fa-solid fa-users"></i></div>
            <span class="nav-link-text">Meus Referidos</span>
          </a>
        @endif

        @if($permission >= 3 && $permission < 5)
          <div class="drawer-menu-divider"></div>
          <div class="drawer-menu-heading">Administração</div>

          <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/dashboard" data-tooltip="Dashboard Admin">
            <div class="nav-link-icon"><i class="fa-solid fa-gauge-high"></i></div>
            <span class="nav-link-text">Dashboard Admin</span>
          </a>

          <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/usuarios?status=todos" data-tooltip="Usuários">
            <div class="nav-link-icon"><i class="fa-solid fa-user-group"></i></div>
            <span class="nav-link-text">Usuários</span>
          </a>

          <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/aprovar-saques" data-tooltip="Aprovar Saques">
            <div class="nav-link-icon"><i class="fa-solid fa-circle-check"></i></div>
            <span class="nav-link-text">Aprovar Saques</span>
          </a>

          <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#financeiroAdmin" aria-expanded="false" aria-controls="financeiroAdmin" data-tooltip="Financeiro">
            <div class="nav-link-icon"><i class="fa-solid fa-building-columns"></i></div>
            <span class="nav-link-text">Financeiro</span>
            <div class="drawer-collapse-arrow"><i class="fa-solid fa-chevron-down"></i></div>
          </a>
          <div class="collapse" id="financeiroAdmin" data-bs-parent="#drawerAccordion">
            <nav class="drawer-menu-nested nav">
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/financeiro/transacoes">Transações</a>
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/financeiro/carteiras">Carteiras</a>
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/financeiro/entradas">Entradas</a>
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/financeiro/saidas">Saídas</a>
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/saque-config">Config. de Saque</a>
            </nav>
          </div>

          <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#transacoesAdmin" aria-expanded="false" aria-controls="transacoesAdmin" data-tooltip="Criar Transações">
            <div class="nav-link-icon"><i class="fa-solid fa-money-bill-transfer"></i></div>
            <span class="nav-link-text">Criar Transações</span>
            <div class="drawer-collapse-arrow"><i class="fa-solid fa-chevron-down"></i></div>
          </a>
          <div class="collapse" id="transacoesAdmin" data-bs-parent="#drawerAccordion">
            <nav class="drawer-menu-nested nav">
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/transacoes/entrada">Entrada</a>
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/transacoes/saida">Saída</a>
            </nav>
          </div>

          <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#settingsAdmin" aria-expanded="false" aria-controls="settingsAdmin" data-tooltip="Configurações">
            <div class="nav-link-icon"><i class="fa-solid fa-gear"></i></div>
            <span class="nav-link-text">Configurações</span>
            <div class="drawer-collapse-arrow"><i class="fa-solid fa-chevron-down"></i></div>
          </a>
          <div class="collapse" id="settingsAdmin" data-bs-parent="#drawerAccordion">
            <nav class="drawer-menu-nested nav">
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/gerais">Gerais</a>
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/niveis">Níveis</a>
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/adquirentes">Adquirentes</a>
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/apoio">Gerentes</a>
              {{-- <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/landing-page">Landing Page</a> --}}
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/smtp">SMTP</a>
              <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/splits-internos">Splits Internos</a>
            </nav>
          </div>
        @endif

        @if($permission == 5)
          <div class="drawer-menu-divider"></div>
          <div class="drawer-menu-heading">Gerenciar</div>

          <a class="nav-link" href="/gerencia/clientes" data-tooltip="Dashboard">
            <div class="nav-link-icon"><i class="fa-solid fa-briefcase"></i></div>
            <span class="nav-link-text">Dashboard</span>
          </a>

          <a class="nav-link" href="/gerencia/relatorio" data-tooltip="Relatório">
            <div class="nav-link-icon"><i class="fa-solid fa-chart-pie"></i></div>
            <span class="nav-link-text">Relatório</span>
          </a>

          <a class="nav-link" href="/gerencia/material" data-tooltip="Material de Apoio">
            <div class="nav-link-icon"><i class="fa-solid fa-folder-open"></i></div>
            <span class="nav-link-text">Material de Apoio</span>
          </a>
        @endif

      </div>
    </div>
  </nav>
</div>

<script>
  // Salva e restaura o estado da sidebar (como o tema escuro)
  document.addEventListener('DOMContentLoaded', function() {
    // Restaura o estado salvo ao carregar
    const savedSidebarState = localStorage.getItem('sidebarCollapsed');
    if (savedSidebarState === 'true') {
      document.body.classList.add('drawer-toggled');
      console.log('🔄 Sidebar restaurada: COLAPSADA');
    } else if (savedSidebarState === 'false') {
      document.body.classList.remove('drawer-toggled');
      console.log('🔄 Sidebar restaurada: EXPANDIDA');
    }
    
    // Monitora quando o botão do navbar é clicado
    const drawerToggle = document.getElementById('drawerToggle');
    if (drawerToggle) {
      drawerToggle.addEventListener('click', function() {
        setTimeout(() => {
          const isToggled = document.body.classList.contains('drawer-toggled');
          // Salva o estado no localStorage
          localStorage.setItem('sidebarCollapsed', isToggled);
          console.log('🔄 Sidebar toggle:', isToggled ? 'COLAPSADA (70px)' : 'EXPANDIDA (279px)');
        }, 100);
      });
    }
    
    // Fecha dropdowns quando a sidebar é colapsada no desktop (com throttling)
    let throttleTimer = null;
    const observer = new MutationObserver(function(mutations) {
      if (throttleTimer) return;
      
      throttleTimer = setTimeout(() => {
        throttleTimer = null;
      }, 100);
      
      mutations.forEach(function(mutation) {
        if (mutation.attributeName === 'class') {
          const isToggled = document.body.classList.contains('drawer-toggled');
          if (isToggled && window.innerWidth >= 1200) {
            // Fecha todos os dropdowns abertos
            const drawer = document.getElementById('drawerAccordion');
            if (drawer) {
              const openCollapses = drawer.querySelectorAll('.collapse.show');
              openCollapses.forEach(collapse => {
                collapse.classList.remove('show');
                const toggle = document.querySelector(`[data-bs-target="#${collapse.id}"]`);
                if (toggle) {
                  toggle.classList.add('collapsed');
                  toggle.setAttribute('aria-expanded', 'false');
                }
              });
            }
          }
        }
      });
    });
    
    observer.observe(document.body, {
      attributes: true,
      attributeFilter: ['class']
    });
    
    // Faz links com dropdown clicarem quando sidebar colapsada
    const drawer = document.getElementById('drawerAccordion');
    if (drawer) {
      const dropdownLinks = drawer.querySelectorAll('.nav-link[data-bs-toggle="collapse"]');
      
      dropdownLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          // Se estiver colapsado no desktop, vai para a primeira página do dropdown
          const isToggled = document.body.classList.contains('drawer-toggled');
          if (isToggled && window.innerWidth >= 1200) {
            e.preventDefault();
            e.stopPropagation();
            
            // Pega o ID do collapse target
            const targetId = this.getAttribute('data-bs-target');
            const collapseMenu = document.querySelector(targetId);
            
            if (collapseMenu) {
              // Pega o primeiro link dentro do collapse
              const firstLink = collapseMenu.querySelector('.nav-link[href]');
              if (firstLink && firstLink.getAttribute('href') !== '#') {
                window.location.href = firstLink.getAttribute('href');
              }
            }
          }
        });
      });
    }
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const currentPath = window.location.pathname;

    // Usando requestAnimationFrame para melhor performance
    requestAnimationFrame(function() {
      document.querySelectorAll(".drawer .nav-link").forEach(link => {
        const href = link.getAttribute("href");
        if (!href || href ==="javascript:void(0);") return;

        const linkUrl = new URL(href, window.location.origin);
        const linkPath = linkUrl.pathname;

        if (currentPath === linkPath || currentPath.startsWith(linkPath + '/')) {
          link.classList.add("active");

          const collapseTarget = link.getAttribute("data-bs-target");
          if (collapseTarget) {
            const collapseEl = document.querySelector(collapseTarget);
            if (collapseEl) {
              collapseEl.classList.add("show");
              link.setAttribute("aria-expanded","true");
              link.classList.remove("collapsed");
            }
          }

          const nestedNav = link.closest(".drawer-menu-nested");
          if (nestedNav) {
            const parentCollapse = nestedNav.closest(".collapse");
            if (parentCollapse) {
              parentCollapse.classList.add("show");
              const toggle = document.querySelector(`[data-bs-target="#${parentCollapse.id}"]`);
              if (toggle) {
                toggle.classList.remove("collapsed");
                toggle.setAttribute("aria-expanded","true");
                // Adicionar classe active no link pai do dropdown
                toggle.classList.add("active");
              }
            }
          }
        }
      });
    });

    // Adicionar lógica para detectar dropdowns ativos baseado na URL atual
    function setActiveDropdown() {
      const currentPath = window.location.pathname;
      
      // Lista de rotas que pertencem a cada dropdown
      const dropdownRoutes = {
        'collapseRelatorios': [
          '/relatorio/entradas',
          '/relatorio/saidas', 
          '/relatorio/entradas-pix',
          '/relatorio/saidas-pix',
          '/relatorio/entradas-boleto',
          '/relatorio/saidas-boleto',
          '/relatorio/entradas-cartao',
          '/relatorio/saidas-cartao'
        ],
        'collapseFinanceiro': [
          '/financeiro'
        ],
        'collapseProdutos': [
          '/produtos',
          '/integracoes'
        ],
        'collapseDev': [
          '/documentacao',
          '/chaves',
          '/webhook'
        ],
        'financeiroAdmin': [
          '/admin/financeiro/transacoes',
          '/admin/financeiro/carteiras',
          '/admin/financeiro/entradas',
          '/admin/financeiro/saidas',
          '/admin/saque-config'
        ],
        'transacoesAdmin': [
          '/admin/transacoes/entrada',
          '/admin/transacoes/saida'
        ]
      };

      // Verificar se a rota atual pertence a algum dropdown
      Object.keys(dropdownRoutes).forEach(collapseId => {
        const routes = dropdownRoutes[collapseId];
        const isActive = routes.some(route => currentPath.startsWith(route));
        
        if (isActive) {
          const toggle = document.querySelector(`[data-bs-target="#${collapseId}"]`);
          const collapseEl = document.querySelector(`#${collapseId}`);
          
          if (toggle && collapseEl) {
            // Adicionar classe active no toggle
            toggle.classList.add("active");
            // Expandir o dropdown
            collapseEl.classList.add("show");
            toggle.setAttribute("aria-expanded", "true");
            toggle.classList.remove("collapsed");
          }
        }
      });
    }

    // Executar a função
    setActiveDropdown();
  });
</script>