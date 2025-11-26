@php
$setting = \App\Helpers\Helper::getSetting();
$color = $setting->gateway_color;
@endphp

@php
// Função para converter HEX para RGBA
function hexToRgba($hex, $opacity = 0.5) {
$hex = str_replace('#', '', $hex);

if (strlen($hex) == 3) {
$r = hexdec(str_repeat(substr($hex, 0, 1), 2));
$g = hexdec(str_repeat(substr($hex, 1, 1), 2));
$b = hexdec(str_repeat(substr($hex, 2, 1), 2));
} else {
$r = hexdec(substr($hex, 0, 2));
$g = hexdec(substr($hex, 2, 2));
$b = hexdec(substr($hex, 4, 2));
}

return"rgba($r, $g, $b, $opacity)";
}

$opacityColor = Str::contains($color, 'rgba')
? preg_replace('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*[\d.]+\)/', 'rgba($1, $2, $3, 0.8)', $color)
: hexToRgba($color, 0.8);

$opacityColor2 = Str::contains($color, 'rgba')
? preg_replace('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*[\d.]+\)/', 'rgba($1, $2, $3, 0.1)', $color)
: hexToRgba($color, 0.1);
@endphp
@props(['route'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme-mode="light" data-header-styles="transparent"
  style="" data-menu-styles="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="Description" content="{{env('APP_NAME')}}">
  <meta name="Author" content="{{env('APP_NAME')}}">
  <meta name="keywords" content="{{env('APP_NAME')}}">
  <link rel="icon" type="image/x-icon" href="{{ asset($setting->gateway_favicon) }}">
  <title>{{ env('APP_NAME') }} - {{ $route }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Script para aplicar tema antes da renderização - evita flash -->
  <script>
    (function() {
      const savedTheme = localStorage.getItem('theme') || 'light';
      const html = document.documentElement;
      
      if (savedTheme === 'dark') {
        html.setAttribute('data-theme', 'dark');
        html.setAttribute('data-bs-theme', 'dark');
        html.setAttribute('data-theme-mode', 'dark');
      } else {
        html.removeAttribute('data-theme');
        html.removeAttribute('data-bs-theme');
        html.setAttribute('data-theme-mode', 'light');
      }
    })();
  </script>

  @include('layouts.components.styles')

  <link href="[REDACTED_BASIC_AUTH_URL]"
    rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />

  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>


  <!-- Estilos para animação do tema -->
  <style>

/* Adicione este CSS no seu arquivo de estilos global ou no <style> do layout principal */

/* Sobrescreve comportamento padrão da sidebar no desktop */
@media (min-width: 1200px) {
  /* Remove TODOS os estilos conflitantes do tema original */
  #layoutDrawer {
    display: flex !important;
  }
  
  /* Otimização de performance com aceleração de hardware */
  #layoutDrawer #layoutDrawer_nav {
    position: fixed !important;
    top: 4.5rem !important;
    left: 0 !important;
    bottom: 0 !important;
    height: calc(100vh - 4.5rem) !important;
    flex-shrink: 0 !important;
    will-change: width, flex-basis;
    overflow-x: hidden !important;
    overflow-y: auto !important;
    transform: translateZ(0) !important;
    backface-visibility: hidden !important;
    perspective: 1000px !important;
    margin: 0 !important;
    z-index: 1038 !important;
  }
  
  #layoutDrawer #layoutDrawer_content {
    position: relative !important;
    flex-grow: 1 !important;
    min-width: 0 !important;
  }
  
  /* Estado normal - sidebar expandida */
  body:not(.drawer-toggled) #layoutDrawer #layoutDrawer_nav,
  body:not(.drawer-toggled).nav-fixed #layoutDrawer #layoutDrawer_nav {
    width: 279px !important;
    flex-basis: 279px !important;
    min-width: 279px !important;
    max-width: 279px !important;
    transform: translateX(0) translateZ(0) !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
  }

  body:not(.drawer-toggled).nav-fixed #layoutDrawer #layoutDrawer_content {
    padding-left: 279px !important;
    margin-left: 0 !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
  }

  /* Estado colapsado - sidebar com 70px (apenas ícones) */
  body.drawer-toggled #layoutDrawer #layoutDrawer_nav,
  body.drawer-toggled.nav-fixed #layoutDrawer #layoutDrawer_nav {
    width: 70px !important;
    flex-basis: 70px !important;
    min-width: 70px !important;
    max-width: 70px !important;
    transform: translateX(0) translateZ(0) !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
  }

  body.drawer-toggled.nav-fixed #layoutDrawer #layoutDrawer_content {
    padding-left: 70px !important;
    margin-left: 0 !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
  }
}

    #themeIcon {
      transition: all 0.2s ease !important;
      transform-origin: center !important;
    }

    /* Efeito de rotação no ícone durante a mudança */
    #themeIcon.rotating {
      animation: rotateIcon 0.3s ease-in-out !important;
    }

    @keyframes rotateIcon {
      0% { transform: rotate(0deg) scale(1); }
      50% { transform: rotate(180deg) scale(1.2); }
      100% { transform: rotate(360deg) scale(1); }
    }

    /* Classe personalizada para texto que muda com o tema */
    .text-theme-success {
      color: #fff !important; /* Branco no tema claro */
    }

    [data-theme="dark"] .text-theme-success,
    [data-bs-theme="dark"] .text-theme-success {
      color: #fff !important; /* Branco no tema escuro */
    }

    /* Classe para ícones que ficam brancos em ambos os temas */
    .icon-theme-white {
      color: #fff !important; /* Branco no tema claro */
    }

    [data-theme="dark"] .icon-theme-white,
    [data-bs-theme="dark"] .icon-theme-white {
      color: #fff !important; /* Branco no tema escuro */
    }
  </style>

  @livewireStyles

  <!-- Sidebar CSS - Carregado por último para sobrescrever tudo -->
  <style>
    /* Ícones da sidebar - SEMPRE VISÍVEIS */
    .drawer .nav-link-icon,
    .drawer .nav-link .nav-link-icon {
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      visibility: visible !important;
      opacity: 1 !important;
    }
    
    .drawer .nav-link-icon i,
    .drawer .nav-link .nav-link-icon i {
      display: inline-block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }
    
    /* Estado expandido */
    .drawer:not(.collapsed) .nav-link-icon {
      margin-right: 0.625rem !important;
    }
    
    /* Estado colapsado - apenas ícones */
    .drawer.collapsed .nav-link-icon {
      margin: 0 !important;
    }
    
    .drawer.collapsed .nav-link-icon i {
      font-size: 1.125rem !important;
    }
  </style>

</head>

<body class="nav-fixed bg-light" style="position: relative;">
  @include('layouts.components.navbar')
  <div id="layoutDrawer">
    @include('layouts.components.sidebar')
    <div id="layoutDrawer_content">
      <main class="body-container">
        {{ $slot }}
      </main>
      {{-- @include('layouts.components.footer') --}}
    </div>
  </div>

  <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
  </script>
  <script type="module" src="{{asset('assets-v2/js/material.js')}}"></script>
  <script src="{{asset('assets-v2/js/scripts.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.0.2/chart.min.js" crossorigin="anonymous"></script>
  <script src="{{asset('assets-v2/js/charts/chart-defaults.js')}}"></script>
  <script src="{{asset('assets-v2/js/charts/demos/chart-pie-demo.js')}}"></script>
  <script src="{{asset('assets-v2/js/charts/demos/dashboard-chart-bar-grouped-demo.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
  <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>

  <script>
  // Função para mostrar notificações modernas
  function showToast(type, message) {
    console.log('🚀 showToast chamada:', { type, message });
    
    // Limpar qualquer toast anterior que possa estar"grudado"
    console.log('🧹 Limpando toasts anteriores...');
    const existingToasts = document.querySelectorAll('.swal2-toast');
    existingToasts.forEach(toast => {
      console.log('🗑️ Removendo toast existente:', toast);
      toast.remove();
    });
    
    // Mapear tipos para cores e ícones modernos
    const config = {
      success: {
        icon: 'success',
        iconColor: '#10b981',
        background: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
        color: '#ffffff'
      },
      error: {
        icon: 'error',
        iconColor: '#ef4444',
        background: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
        color: '#ffffff'
      },
      danger: {
        icon: 'error',
        iconColor: '#ef4444',
        background: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
        color: '#ffffff'
      },
      warning: {
        icon: 'warning',
        iconColor: '#f59e0b',
        background: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
        color: '#ffffff'
      },
      info: {
        icon: 'info',
        iconColor: '#3b82f6',
        background: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
        color: '#ffffff'
      }
    };

    const typeConfig = config[type] || config.info;

    console.log('🎨 Configuração do toast:', typeConfig);
    console.log('🔥 Chamando Swal.fire...');

    Swal.fire({
      toast: true,
      icon: typeConfig.icon,
      iconColor: typeConfig.iconColor,
      title: message,
      position: 'top-end',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
      showCloseButton: true,
      background: typeConfig.background,
      color: typeConfig.color,
      customClass: {
        popup: 'modern-toast-popup',
        title: 'modern-toast-title',
        timerProgressBar: 'modern-toast-progress',
        closeButton: 'modern-toast-close'
      },
      didOpen: (toast) => {
        console.log('🔔 Toast aberto:', message);
        console.log('🔔 Elemento toast:', toast);
        
        toast.style.borderRadius = '12px';
        toast.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.2)';
        toast.style.backdropFilter = 'blur(10px)';
        
        // Forçar fechamento após timer
        console.log('⏰ Configurando timer de 2000ms...');
        setTimeout(() => {
          console.log('⏰ Timer executado! Tentando fechar toast...');
          try {
            // Tentar fechar normalmente primeiro
            Swal.close();
            console.log('✅ Swal.close() executado');
            
            // Remoção física do DOM como backup
            setTimeout(() => {
              const toastElement = document.querySelector('.swal2-toast');
              if (toastElement) {
                console.log('🗑️ Removendo toast fisicamente do DOM...');
                toastElement.remove();
                console.log('✅ Toast removido fisicamente!');
              }
            }, 100);
            
          } catch (error) {
            console.error('❌ Erro ao fechar toast:', error);
            // Remoção física em caso de erro
            const toastElement = document.querySelector('.swal2-toast');
            if (toastElement) {
              console.log('🗑️ Removendo toast fisicamente após erro...');
              toastElement.remove();
            }
          }
        }, 2000);
        
        // Adicionar evento de clique no botão X
        const closeButton = toast.querySelector('.swal2-close');
        console.log('❌ Botão X encontrado:', closeButton);
        if (closeButton) {
          closeButton.addEventListener('click', () => {
            console.log('❌ Botão X clicado! Fechando toast...');
            try {
              Swal.close();
              console.log('✅ Swal.close() executado pelo botão X');
              
              // Remoção física do DOM como backup
              setTimeout(() => {
                const toastElement = document.querySelector('.swal2-toast');
                if (toastElement) {
                  console.log('🗑️ Removendo toast fisicamente pelo botão X...');
                  toastElement.remove();
                  console.log('✅ Toast removido fisicamente pelo botão X!');
                }
              }, 100);
              
            } catch (error) {
              console.error('❌ Erro ao fechar toast pelo botão X:', error);
              // Remoção física em caso de erro
              const toastElement = document.querySelector('.swal2-toast');
              if (toastElement) {
                console.log('🗑️ Removendo toast fisicamente após erro no botão X...');
                toastElement.remove();
              }
            }
          });
        }
        
        // Adicionar evento de clique em qualquer lugar do toast
        toast.addEventListener('click', (e) => {
          console.log('👆 Toast clicado!', e.target);
          try {
            Swal.close();
            console.log('✅ Swal.close() executado pelo clique');
            
            // Remoção física do DOM como backup
            setTimeout(() => {
              const toastElement = document.querySelector('.swal2-toast');
              if (toastElement) {
                console.log('🗑️ Removendo toast fisicamente pelo clique...');
                toastElement.remove();
                console.log('✅ Toast removido fisicamente pelo clique!');
              }
            }, 100);
            
          } catch (error) {
            console.error('❌ Erro ao fechar toast pelo clique:', error);
            // Remoção física em caso de erro
            const toastElement = document.querySelector('.swal2-toast');
            if (toastElement) {
              console.log('🗑️ Removendo toast fisicamente após erro no clique...');
              toastElement.remove();
            }
          }
        });
      }
    });
  }

  // Lógica da Sidebar
  const body = document.body;
  const toggleButton = document.getElementById('drawerToggle');
  const observer = new MutationObserver(() => {
    if (body.classList.contains('drawer-toggled')) {
      toggleButton.classList.add('rotated-right');
      toggleButton.classList.remove('rotated-left');
    } else {
      toggleButton.classList.add('rotated-left');
      toggleButton.classList.remove('rotated-right');
    }
  });
  observer.observe(body, {
    attributes: true,
    attributeFilter: ['class']
  });
  </script>

  @if (session('success'))
  <script>
  showToast('success',"{{ session('success') }}");
  </script>
  @endif

  @if (session('error'))
  <script>
  showToast('danger',"{{ session('error') }}");
  </script>
  @endif

  <!-- Script global para tema - funciona em todas as páginas -->
  <script>
    // Função global de toggle de tema - versão corrigida e robusta
    window.toggleTheme = function() {
      const html = document.documentElement; // Usa documentElement em vez de querySelector
      const themeIcon = document.getElementById('themeIcon');
      
      if (!html) {
        console.error('Elemento HTML não encontrado');
        return;
      }
      
      if (!themeIcon) {
        console.warn('Ícone de tema não encontrado - função continuará funcionando');
      }
      
      // Detecta o tema atual de forma mais robusta
      const currentTheme = html.getAttribute('data-theme');
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      
      // Adiciona efeito visual de rotação no ícone
      if (themeIcon) {
        themeIcon.classList.add('rotating');
      }
      
      // Aplica a mudança de tema instantaneamente
      if (newTheme === 'dark') {
        // Mudando para tema escuro
        html.setAttribute('data-theme', 'dark');
        html.setAttribute('data-bs-theme', 'dark');
        html.setAttribute('data-theme-mode', 'dark');
        if (themeIcon) {
          themeIcon.className = 'bi bi-moon-fill rotating';
        }
        localStorage.setItem('theme', 'dark');
      } else {
        // Mudando para tema claro
        html.removeAttribute('data-theme');
        html.removeAttribute('data-bs-theme');
        html.setAttribute('data-theme-mode', 'light');
        if (themeIcon) {
          themeIcon.className = 'bi bi-sun-fill rotating';
        }
        localStorage.setItem('theme', 'light');
      }
      
      // Remove a classe de rotação após a animação
      if (themeIcon) {
        setTimeout(() => {
          themeIcon.classList.remove('rotating');
        }, 300);
      }
      
      // Dispara evento customizado para sincronização
      window.dispatchEvent(new CustomEvent('themeChanged', {
        detail: { theme: newTheme }
      }));
      
      // Atualiza logos que mudam com o tema
      updateThemeLogos(newTheme);
      
      console.log('Tema alterado para:', newTheme);
    };
    
    // Função auxiliar para atualizar logos baseadas no tema
    function updateThemeLogos(theme) {
      document.querySelectorAll('.theme-logo').forEach(function(img) {
        const lightSrc = img.getAttribute('data-light-src');
        const darkSrc = img.getAttribute('data-dark-src');
        
        if (theme === 'dark' && darkSrc) {
          img.src = darkSrc;
        } else if (lightSrc) {
          img.src = lightSrc;
        }
      });
    }
    
    // Inicialização quando a página carrega
    document.addEventListener('DOMContentLoaded', function() {
      // Sincroniza o ícone com o tema atual
      const savedTheme = localStorage.getItem('theme') || 'light';
      const themeIcon = document.getElementById('themeIcon');
      const html = document.documentElement;
      
      // Garante que o tema esteja aplicado (já foi aplicado no head, mas confirma)
      if (savedTheme === 'dark') {
        html.setAttribute('data-theme', 'dark');
        html.setAttribute('data-bs-theme', 'dark');
        html.setAttribute('data-theme-mode', 'dark');
      } else {
        html.removeAttribute('data-theme');
        html.removeAttribute('data-bs-theme');
        html.setAttribute('data-theme-mode', 'light');
      }
      
      // Sincroniza o ícone
      if (themeIcon) {
        if (savedTheme === 'dark') {
          themeIcon.className = 'bi bi-moon-fill';
        } else {
          themeIcon.className = 'bi bi-sun-fill';
        }
      }
      
      // Atualiza logos
      updateThemeLogos(savedTheme);
    });
    
    // Listener para sincronização entre abas/janelas
    window.addEventListener('storage', function(e) {
      if (e.key === 'theme') {
        const newTheme = e.newValue || 'light';
        const html = document.documentElement;
        const themeIcon = document.getElementById('themeIcon');
        
        if (newTheme === 'dark') {
          html.setAttribute('data-theme', 'dark');
          html.setAttribute('data-bs-theme', 'dark');
          html.setAttribute('data-theme-mode', 'dark');
          if (themeIcon) themeIcon.className = 'bi bi-moon-fill';
        } else {
          html.removeAttribute('data-theme');
          html.removeAttribute('data-bs-theme');
          html.setAttribute('data-theme-mode', 'light');
          if (themeIcon) themeIcon.className = 'bi bi-sun-fill';
        }
        
        updateThemeLogos(newTheme);
      }
    });
  </script>

  <!-- Modais Globais - Depósito e Saque -->
  @if(auth()->check())
    @include('layouts.components.modals.deposito')
    @include('layouts.components.modals.saque')
  @endif

  @livewireScripts
</body>

</html>