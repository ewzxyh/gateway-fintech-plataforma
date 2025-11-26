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

  if ($max > $min) {
    $raw = (($totalDepositos - $min) / ($max - $min)) * 100;
    $porcentagem = min(100, max(0, floor($raw * 100) / 100)); // Trunca para 2 casas sem arredondar
  }
@endphp


<div id="layoutDrawer_nav" style="overflow-x: hidden">

  <!-- Drawer navigation-->
  <nav class="bg-white drawer accordion drawer-light" id="drawerAccordion" >
    <div class="drawer-menu">
      <div class="nav" style="overflow-x: hidden">

        @if($setting->niveis_ativo && $nivel['nivel_atual'])
          <div class="mb-2">
           <div class="px-4 mb-1 d-flex justify-content-between">
             <small class="text-muted fw-bold">{{ formatK($totalDepositos) }} / {{ formatK($max) }}</small>
           </div>

           <div class="px-4">
             <div class="gap-2 d-flex align-items-center">
               <div class="flex-grow-1">
                 <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                   aria-valuenow="{{ (int) $porcentagem }}"
                   style="border-radius: 50px; background-color: var(--color-gateway-opacity2); height: 10px;">
                   <div class="progress-bar"
                     style="width: {{ $porcentagem }}%; background-color: var(--color-gateway); height: 100%;">
                   </div>
                 </div>
               </div>
               <div class="flex-shrink-0" style="min-width: 50px; text-align: right;">
                 <small class="text-muted">{{ number_format($porcentagem, 2, ',', '.') }}%</small>
               </div>
             </div>
           </div>
         </div>
        @endif

        <!-- Drawer section heading (Interface)-->
        <!-- <div class="pb-1 drawer-menu-heading">MAIN</div> -->
        <!-- Drawer link (Overview)-->
        <a class="nav-link" href="/dashboard?produto=todos&periodo=hoje">
          <div class="nav-link-icon"><i class="fa-solid fa-desktop"></i></div>
          Dashboard
        </a>
        @if($status == 1)
          <!-- Drawer link (Dashboards)-->
          <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseDashboards" aria-expanded="false" aria-controls="collapseDashboards">
            <div class="nav-link-icon"><i class="fa-solid fa-chart-pie"></i></div>
            Relatórios
            <div class="drawer-collapse-arrow"><i class="material-icons">expand_more</i></div>
          </a>
          <!-- Nested drawer nav (Dashboards)-->
          <div class="collapse" id="collapseDashboards" aria-labelledby="headingOne" data-bs-parent="#drawerAccordion">
            <nav class="drawer-menu-nested nav">
              <a class="nav-link" href="/relatorio/entradas?periodo=hoje">Entradas</a>
              <a class="nav-link" href="/relatorio/saidas?periodo=hoje">Saidas</a>
            </nav>
          </div>
          <a class="nav-link" href="/financeiro">
            <div class="nav-link-icon"><i class="fa-solid fa-building-columns"></i></div>
            Financeiro
          </a>
          
          <div class="drawer-menu-divider d-sm-none"></div>
          <a class="nav-link" href="/documentacao">
            <div class="nav-link-icon"><i class="fa-solid fa-book"></i></div>
            Documentação
          </a>
          <a class="nav-link" href="/chaves">
            <div class="nav-link-icon"><i class="fa-solid fa-code"></i></div>
            Chave API
          </a>
          <a class="nav-link" href="/webhook">
            <div class="nav-link-icon"><i class="fa-solid fa-satellite-dish"></i></div>
            Webhooks
          </a>
           <a class="nav-link" href="/produtos">
            <div class="nav-link-icon"><i class="fa-solid fa-box"></i></div>
            Produtos
          </a>
          <a class="nav-link" href="/affiliate">
            <div class="nav-link-icon"><i class="fa-solid fa-users"></i></div>
            Meus Referidos
          </a>
          <a class="nav-link" href="/integracoes">
            <div class="nav-link-icon"><i class="fa-solid fa-link"></i></div>
            Integrações
          </a>
        @endif

        @if($permission >= 3&& $permission < 5)
        <!-- Divider-->
        <div class="pb-0 my-0 drawer-menu-divider"></div>
        <!-- Drawer section heading (UI Toolkit)-->
        <div class="pt-2 pb-1 drawer-menu-heading">ADMINISTRAÇÃO</div>
        <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/dashboard">
          <div class="nav-link-icon"><i class="material-icons">dashboard</i></div>
          Dashboard
        </a>
        <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/usuarios?status=todos">
          <div class="nav-link-icon"><i class="material-icons">groups</i></div>
          Usuários
        </a>
        <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/aprovar-saques">
          <div class="nav-link-icon"><i class="material-icons">checklist</i></div>
          Aprovar saques
        </a>

        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#financeiro" aria-expanded="false" aria-controls="financeiro">
          <div class="nav-link-icon"><i class="material-icons">calculate</i></div>
          Financeiro
          <div class="drawer-collapse-arrow"><i class="material-icons">expand_more</i></div>
        </a>
        <!-- Nested drawer nav (Dashboards)-->
        <div class="collapse" id="financeiro" aria-labelledby="headingOne" data-bs-parent="#financeiro">
          <nav class="drawer-menu-nested nav">
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/financeiro/transacoes">Transações</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/financeiro/carteiras">Carteiras</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/financeiro/entradas">Entradas</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/financeiro/saidas">Saidas</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/saque-config">Configurações de Saque</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/splits-internos">Split</a>
          </nav>
        </div>

        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#transacoes" aria-expanded="false" aria-controls="transacoes">
          <div class="nav-link-icon"><i class="material-icons">currency_exchange</i></div>
          Criar Transações
          <div class="drawer-collapse-arrow"><i class="material-icons">expand_more</i></div>
        </a>
        <!-- Nested drawer nav (Dashboards)-->
        <div class="collapse" id="transacoes" aria-labelledby="headingOne" data-bs-parent="#transacoes">
          <nav class="drawer-menu-nested nav">
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/transacoes/entrada">Entrada</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/transacoes/saida">Saída</a>
          </nav>
        </div>

        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#settings" aria-expanded="false" aria-controls="settings">
          <div class="nav-link-icon"><i class="material-icons">settings</i></div>
          Configurações
          <div class="drawer-collapse-arrow"><i class="material-icons">expand_more</i></div>
        </a>
        <!-- Nested drawer nav (Dashboards)-->
        <div class="collapse" id="settings" aria-labelledby="headingOne" data-bs-parent="#settings">
          <nav class="drawer-menu-nested nav">
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/gerais">Gerais</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/niveis">Níveis</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/adquirentes">Adquirentes</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/apoio">Gerentes</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/landing-page">Landing Page</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/smtp">SMTP</a>
            <a class="nav-link" href="/{{ env('ADM_ROUTE') }}/ajustes/splits-internos">Splits Internos</a>
          </nav>
        </div>
        @endif
        @if($permission == 5)
        <div class="pb-0 my-0 drawer-menu-divider"></div>
         <!-- Drawer section heading (UI Toolkit)-->
         <div class="pt-2 pb-1 drawer-menu-heading">GERENCIAR</div>
           <a class="nav-link" href="/gerencia/clientes">
             <div class="nav-link-icon"><i class="fa-solid fa-desktop"></i></div>
             Dashboard
           </a>
           <a class="nav-link" href="/gerencia/relatorio">
             <div class="nav-link-icon"><i class="fa-solid fa-chart-pie"></i></div>
             Relatório
           </a>
           <a class="nav-link" href="/gerencia/material">
            <div class="nav-link-icon"><i class="fa-solid fa-folder-open"></i></div>
            Material de apoio
          </a>
        @endif
      </div>
    </div>
    <!-- Drawer footer    -->

  </nav>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const currentPath = window.location.pathname;

    // Normaliza e compara apenas a parte do path (sem query ou hash)
    document.querySelectorAll(".drawer .nav-link").forEach(link => {
      const href = link.getAttribute("href");
      if (!href || href ==="javascript:void(0);") return;

      const linkUrl = new URL(href, window.location.origin);
      const linkPath = linkUrl.pathname;

      // Verifica se o caminho atual começa com o caminho do link
      if (currentPath === linkPath || currentPath.startsWith(linkPath + '/')) {
        // Marca o link como ativo
        link.classList.add("active");

        // Se for um submenu, expande o colapso correspondente
        const collapseTarget = link.getAttribute("data-bs-target");
        if (collapseTarget) {
          const collapseEl = document.querySelector(collapseTarget);
          if (collapseEl) {
            collapseEl.classList.add("show");
            link.setAttribute("aria-expanded","true");
            link.classList.remove("collapsed");
          }
        }

        // Se for um item dentro de submenu, expande o pai
        const nestedNav = link.closest(".drawer-menu-nested");
        if (nestedNav) {
          const parentCollapse = nestedNav.closest(".collapse");
          if (parentCollapse) {
            parentCollapse.classList.add("show");

            // Marca o botão de expansão do pai como aberto
            const toggle = document.querySelector(`[data-bs-target="#${parentCollapse.id}"]`);
            if (toggle) {
              toggle.classList.remove("collapsed");
              toggle.setAttribute("aria-expanded","true");
            }
          }
        }
      }
    });
  });
  </script>

