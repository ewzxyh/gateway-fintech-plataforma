@php
$setting = \App\Helpers\Helper::getSetting();
@endphp

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('assets-v2/css/gerente-modal.css') }}" rel="stylesheet">

<style>
  /* Navbar Background - Matching Dashboard Theme */
  .top-app-bar.navbar {
    background: var(--glass-bg, rgba(255, 255, 255, 0.1)) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border-bottom: 1px solid var(--glass-border, rgba(255, 255, 255, 0.2)) !important;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1) !important;
  }

  /* Dark theme support for navbar */
  [data-theme="dark"] .top-app-bar.navbar {
    background: var(--glass-bg, rgba(255, 255, 255, 0.08)) !important;
    border-bottom-color: var(--glass-border, rgba(255, 255, 255, 0.15)) !important;
  }

  /* Override default topbar styles */
  .topbar-nav .navbar {
    background-color: transparent !important;
  }

  .glassmorphism-toggle-drawer {
    background: var(--glass-bg, rgba(255, 255, 255, 0.1)) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.2)) !important;
    border-radius: 12px !important;
    padding: 8px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative !important;
    overflow: hidden !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1) !important;
  }

  .glassmorphism-toggle-drawer::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: -100% !important;
    width: 100% !important;
    height: 100% !important;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent) !important;
    transition: left 0.5s ease !important;
  }

  .glassmorphism-toggle-drawer:hover {
    background: var(--glass-bg, rgba(255, 255, 255, 0.15)) !important;
    border-color: var(--glass-border, rgba(255, 255, 255, 0.3)) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
  }

  .glassmorphism-toggle-drawer:hover::before {
    left: 100% !important;
  }

  .glassmorphism-toggle-drawer .toggle-icon-container {
    position: relative !important;
    z-index: 1 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }

  .glassmorphism-toggle-drawer .toggle-icon {
    color: var(--text-primary, #333) !important;
    transition: all 0.3s ease !important;
  }

  .glassmorphism-toggle-drawer:hover .toggle-icon {
    transform: scale(1.1) !important;
    filter: brightness(1.2) !important;
  }

  .glassmorphism-toggle-drawer .toggle-line {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    transform-origin: center !important;
  }

  .glassmorphism-toggle-drawer:hover .toggle-line-1 {
    transform: translateY(1px) rotate(3deg) !important;
  }

  .glassmorphism-toggle-drawer:hover .toggle-line-2 {
    transform: scaleX(0.8) !important;
    opacity: 0.7 !important;
  }

  .glassmorphism-toggle-drawer:hover .toggle-line-3 {
    transform: translateY(-1px) rotate(-3deg) !important;
  }

  /* Dark theme support */
  [data-theme="dark"] .glassmorphism-toggle-drawer {
    background: var(--glass-bg, rgba(0, 0, 0, 0.2)) !important;
    border-color: var(--glass-border, rgba(255, 255, 255, 0.1)) !important;
  }

  [data-theme="dark"] .glassmorphism-toggle-drawer:hover {
    background: var(--glass-bg, rgba(0, 0, 0, 0.3)) !important;
    border-color: var(--glass-border, rgba(255, 255, 255, 0.2)) !important;
  }

  [data-theme="dark"] .glassmorphism-toggle-drawer .toggle-icon {
    color: var(--text-primary, #fff) !important;
  }

  /* Theme toggle button styles */
  #themeToggleBtn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  }

  #themeToggleBtn:hover {
    transform: scale(1.1) !important;
  }

  #themeToggleBtn:active {
    transform: scale(0.95) !important;
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

  /* Profile Dropdown Styles */
  .dropdown-menu {
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 2px solid rgba(0, 0, 0, 0.08) !important;
    border-radius: 16px !important;
    padding: 12px !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15), 
          0 2px 8px rgba(0, 0, 0, 0.1),
          inset 0 1px 0 rgba(255, 255, 255, 0.8) !important;
    min-width: 280px !important;
    animation: slideDown 0.3s ease-out !important;
  }

  [data-theme="dark"] .dropdown-menu {
    background: rgba(30, 30, 30, 0.95) !important;
    border: 2px solid rgba(255, 255, 255, 0.12) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4), 
          0 2px 8px rgba(0, 0, 0, 0.3) !important;
  }

  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* User Info Header in Dropdown */
  .user-info-header {
    padding: 14px 12px !important;
    background: linear-gradient(135deg, var(--primary, #1976d2), var(--primary-dark, #1565c0)) !important;
    border-radius: 12px !important;
    margin-bottom: 12px !important;
    position: relative !important;
    overflow: hidden !important;
  }

  .user-info-header::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(10px) !important;
  }

  .user-info-content {
    position: relative !important;
    z-index: 1 !important;
  }

  .user-name {
    font-size: 15px !important;
    font-weight: 600 !important;
    color: white !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    text-align: center !important;
  }

  .user-name i {
    font-size: 20px !important;
  }

  .dropdown-item {
    padding: 12px 12px !important;
    border-radius: 10px !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    margin-bottom: 4px !important;
    color: var(--text-primary, #333) !important;
    font-weight: 500 !important;
    border: none !important;
    background: transparent !important;
  }

  [data-theme="dark"] .dropdown-item {
    color: var(--text-primary, #fff) !important;
  }

  .dropdown-item:hover {
    background: rgba(0, 0, 0, 0.06) !important;
    transform: translateX(4px) !important;
  }

  [data-theme="dark"] .dropdown-item:hover {
    background: rgba(255, 255, 255, 0.08) !important;
  }

  .dropdown-item:active {
    background: rgba(0, 0, 0, 0.1) !important;
    transform: scale(0.98) !important;
  }

  [data-theme="dark"] .dropdown-item:active {
    background: rgba(255, 255, 255, 0.12) !important;
  }

  .dropdown-item .leading-icon {
    font-size: 20px !important;
    min-width: 20px !important;
    transition: all 0.2s ease !important;
  }

  .dropdown-item:hover .leading-icon {
    transform: scale(1.1) !important;
  }

  .dropdown-item .me-3 {
    flex: 1 !important;
    margin: 0 !important;
  }

  .dropdown-divider {
    margin: 12px 0 !important;
    border-top: 1px solid rgba(0, 0, 0, 0.12) !important;
    opacity: 1 !important;
  }

  [data-theme="dark"] .dropdown-divider {
    border-top-color: rgba(255, 255, 255, 0.15) !important;
  }

  /* Logout button special styling */
  .dropdown-item.btn-link {
    color: #dc3545 !important;
    width: 100% !important;
    text-align: left !important;
    text-decoration: none !important;
  }

  .dropdown-item.btn-link:hover {
    background: rgba(220, 53, 69, 0.1) !important;
  }

  .dropdown-item.btn-link .leading-icon {
    color: #dc3545 !important;
  }

  /* Profile Avatar Animation */
  #dropdownMenuProfile img {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  }

  #dropdownMenuProfile:hover img {
    transform: scale(1.08) !important;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3) !important;
  }

  [data-theme="dark"] #dropdownMenuProfile:hover img {
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2) !important;
  }

  /* Dropdown menu arrow */
  .dropdown-menu::before {
    content: '' !important;
    position: absolute !important;
    top: -7px !important;
    right: 20px !important;
    width: 14px !important;
    height: 14px !important;
    background: rgba(255, 255, 255, 0.98) !important;
    border-top: 2px solid rgba(0, 0, 0, 0.08) !important;
    border-left: 2px solid rgba(0, 0, 0, 0.08) !important;
    border-bottom: none !important;
    border-right: none !important;
    transform: rotate(45deg) !important;
    border-radius: 2px !important;
  }

  [data-theme="dark"] .dropdown-menu::before {
    background: rgba(30, 30, 30, 0.95) !important;
    border-top-color: rgba(255, 255, 255, 0.12) !important;
    border-left-color: rgba(255, 255, 255, 0.12) !important;
  }

  /* Smooth transitions for all dropdown elements */
  .dropdown-menu li {
    transition: all 0.2s ease !important;
  }

  /* Icon colors */
  .dropdown-item .leading-icon {
    color: var(--primary, #1976d2) !important;
  }

  [data-theme="dark"] .dropdown-item .leading-icon {
    color: var(--primary, #64b5f6) !important;
  }

  /* Avatar Button Styles - SwiftPay */
  button.icon-navbar.btn.btn-lg.btn-icon.dropdown-toggle#dropdownMenuProfile,
  .icon-navbar.btn.btn-lg.btn-icon.dropdown-toggle,
  button.icon-navbar {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    padding: 4px !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.2s ease !important;
    width: auto !important;
    height: auto !important;
    min-width: 40px !important;
    min-height: 40px !important;
    box-shadow: none !important;
  }

  button.icon-navbar.btn.btn-lg.btn-icon.dropdown-toggle#dropdownMenuProfile:hover,
  .icon-navbar.btn.btn-lg.btn-icon.dropdown-toggle:hover,
  button.icon-navbar:hover {
    transform: scale(1.05) !important;
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
  }

  button.icon-navbar.btn.btn-lg.btn-icon.dropdown-toggle#dropdownMenuProfile:focus,
  .icon-navbar.btn.btn-lg.btn-icon.dropdown-toggle:focus,
  button.icon-navbar:focus {
    outline: none !important;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3) !important;
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
  }

  button.icon-navbar.btn.btn-lg.btn-icon.dropdown-toggle#dropdownMenuProfile:active,
  .icon-navbar.btn.btn-lg.btn-icon.dropdown-toggle:active,
  button.icon-navbar:active {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
  }

  button.icon-navbar img,
  .icon-navbar img,
  #dropdownMenuProfile img {
    display: block !important;
    border: 2px solid rgba(255, 255, 255, 0.2) !important;
    transition: border-color 0.2s ease !important;
    object-fit: cover !important;
    width: 32px !important;
    height: 32px !important;
    border-radius: 100px !important;
  }

  button.icon-navbar:hover img,
  .icon-navbar:hover img,
  #dropdownMenuProfile:hover img {
    border-color: rgba(255, 255, 255, 0.5) !important;
  }

  /* Garantir que o dropdown toggle funcione */
  #dropdownMenuProfile {
    background: transparent !important;
    border: none !important;
  }

  #dropdownMenuProfile::after {
    display: none !important;
  }

  /* Base Layout - All Screens */
  .top-app-bar.navbar .container-fluid {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: nowrap !important;
    gap: 0.5rem !important;
  }
  
  .top-app-bar.navbar .container-fluid > * {
    flex-shrink: 0 !important;
  }
  
  .logo {
    order: 1 !important;
    margin-right: 0.5rem !important;
    text-align: left !important;
  }
  
  .glassmorphism-toggle-drawer {
    order: 2 !important;
    margin: 0 !important;
  }
  
  .navbar .d-flex {
    order: 3 !important;
    margin: 0 !important;
    margin-left: auto !important;
  }

  /* Mobile Responsive Fixes */
  @media (max-width: 768px) {
    .top-app-bar.navbar .container-fluid {
      padding-left: 0.75rem !important;
      padding-right: 0.75rem !important;
      gap: 0.25rem !important;
    }
    
    .glassmorphism-toggle-drawer {
      padding: 6px !important;
      min-width: 36px !important;
      min-height: 36px !important;
    }
    
    .glassmorphism-toggle-drawer .toggle-icon {
      width: 18px !important;
      height: 18px !important;
    }
    
    .logo {
      margin: 0 0.5rem !important;
      margin-right: 0.5rem !important;
    }
    
    .logo img {
      width: 100px !important;
      max-width: 100px !important;
      height: auto !important;
    }
    
    .navbar .d-flex .icon-navbar {
      margin-right: 0.25rem !important;
      padding: 4px !important;
    }
    
    .navbar .d-flex .icon-navbar:last-child {
      margin-right: 0 !important;
    }
  }

  @media (max-width: 576px) {
    .top-app-bar.navbar .container-fluid {
      padding-left: 0.5rem !important;
      padding-right: 0.5rem !important;
    }
    
    .glassmorphism-toggle-drawer {
      margin-right: 0.25rem !important;
      padding: 4px !important;
      min-width: 32px !important;
      min-height: 32px !important;
    }
    
    .glassmorphism-toggle-drawer .toggle-icon {
      width: 16px !important;
      height: 16px !important;
    }
    
    .logo {
      margin: 0 0.25rem !important;
      margin-right: 0.5rem !important;
    }
    
    .logo img {
      width: 80px !important;
      max-width: 80px !important;
    }
    
    .navbar .d-flex {
      gap: 0.125rem !important;
    }
    
    .navbar .d-flex .icon-navbar {
      margin-right: 0.125rem !important;
      padding: 3px !important;
    }
    
    .navbar .d-flex .icon-navbar img {
      width: 28px !important;
      height: 28px !important;
    }
  }

  @media (max-width: 480px) {
    .top-app-bar.navbar .container-fluid {
      padding-left: 0.25rem !important;
      padding-right: 0.25rem !important;
    }
    
    .glassmorphism-toggle-drawer {
      margin-right: 0.125rem !important;
      padding: 3px !important;
      min-width: 28px !important;
      min-height: 28px !important;
    }
    
    .glassmorphism-toggle-drawer .toggle-icon {
      width: 14px !important;
      height: 14px !important;
    }
    
    .logo {
      margin: 0 0.125rem !important;
      margin-right: 0.5rem !important;
    }
    
    .logo img {
      width: 70px !important;
      max-width: 70px !important;
    }
    
    .navbar .d-flex .icon-navbar {
      padding: 2px !important;
    }
    
    .navbar .d-flex .icon-navbar img {
      width: 24px !important;
      height: 24px !important;
    }
  }
</style>

<script>
  // A função toggleTheme está definida globalmente no layout principal (app.blade.php)
  // O ícone é sincronizado automaticamente pelo script global
  // Este componente apenas fornece o botão e o ícone com os IDs corretos
</script>

<nav class="top-app-bar navbar navbar-expand">
  <div class="px-4 container-fluid">
    <!-- Drawer toggle button - Mobile First -->
    <button class="glassmorphism-toggle-drawer" id="drawerToggle" href="javascript:void(0);">
      <div class="toggle-icon-container">
        <svg class="toggle-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path class="toggle-line toggle-line-1" d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path class="toggle-line toggle-line-2" d="M3 12h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path class="toggle-line toggle-line-3" d="M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </div>
    </button>
    
    <!-- Logo - Central -->
    <div class="text-uppercase font-monospace logo h-100">
      <div class="margin-logo"></div>
      <img src="{{ asset($setting->gateway_logo) }}" 
         height="auto" 
         width="135" 
         class="theme-logo"
         data-light-src="{{ asset($setting->gateway_logo) }}"
         data-dark-src="{{ asset($setting->gateway_logo_dark ?? $setting->gateway_logo) }}">
      {{-- {{ $setting->gateway_name }} --}}
    </div>
    
    <!-- Navbar brand - Hidden on mobile -->
    <a class="icon-navbar navbar-brand d-none d-lg-block" href="/dashboard">
      <div class="text-uppercase font-monospace">

      </div>
    </a>
    
    <!-- Navbar items - Right side -->
    <div class="d-flex align-items-center">
      <!-- Navbar-->
      {{-- <ul class="navbar-nav d-none d-lg-flex">
        <li class="nav-item"><a class="nav-link" href="index.html">Overview</a></li>
        <li class="nav-item"><a class="nav-link" href="https://docs.startbootstrap.com/material-admin-pro" target="_blank">Documentation</a></li>
      </ul> --}}
      <!-- Navbar buttons-->
      <div class="d-flex">
        <!-- Messages dropdown-->
        {{-- <div class="dropdown dropdown-notifications d-none d-sm-block">
          <button class="icon-navbar btn btn-lg btn-icon dropdown-toggle me-3" id="dropdownMenuMessages" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">mail_outline</i></button>
          <ul class="py-0 mt-3 overflow-hidden dropdown-menu dropdown-menu-end me-3" aria-labelledby="dropdownMenuMessages">
            <li><h6 class="py-3 icon-navbar dropdown-header bg-primary fw-500">Notificações</h6></li>
            <li><hr class="my-0 dropdown-divider" /></li>
            <li>
              <a class="dropdown-item unread" href="#!">
                <div class="dropdown-item-content">
                  <div class="dropdown-item-content-text"><div class="text-truncate d-inline-block" style="max-width: 18rem">{{ $setting->gateway_name }} Informa:</div></div>
                  <div class="dropdown-item-content-subtext">Seja bem vindo Sr(a) {{ isset(explode(' ',auth()->user()->name)[0]) ? explode(' ',auth()->user()->name)[0] : auth()->user()->name }} a {{ $setting->gateway_name }}.</div>
                </div>
              </a>
            </li>
          </ul>
        </div> --}}
        <!-- Global Filters button-->
        @if($status != 5)
        <button class="icon-navbar btn btn-lg btn-icon me-3" onclick="toggleGlobalFilters()" aria-label="Filtros globais" id="globalFiltersBtn">
          <i class="bi bi-calendar3"></i>
        </button>
        @endif
        <!-- Product Filters button-->
        @if($status != 5)
        <button class="icon-navbar btn btn-lg btn-icon me-3" onclick="toggleProductFilters()" aria-label="Filtros de produtos" id="productFiltersBtn">
          <i class="bi bi-box"></i>
        </button>
        @endif
        <!-- Theme toggle button-->
        <button class="icon-navbar btn btn-lg btn-icon me-3" onclick="toggleTheme()" aria-label="Toggle theme" id="themeToggleBtn">
          <i class="bi bi-sun-fill" id="themeIcon"></i>
        </button>
        <!-- Notifications and alerts dropdown-->
        <!-- <div class="dropdown dropdown-notifications d-sm-block">
          <button class="icon-navbar btn btn-lg btn-icon dropdown-toggle me-3" id="dropdownMenuNotifications" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">notifications</i></button>
          <ul class="py-0 mt-3 overflow-hidden dropdown-menu dropdown-menu-end me-3" aria-labelledby="dropdownMenuNotifications">
            <li><h6 class="py-3 icon-navbar dropdown-header fw-500" style="color:white!important;background: {{ $setting->gateway_color }}">Notificações</h6></li>
            <li><hr class="my-0 dropdown-divider" /></li>
            <li>
              <a class="dropdown-item unread" href="#!">
                <div class="dropdown-item-content">
                  <div class="dropdown-item-content-text"><div class="text-truncate d-inline-block" style="max-width: 18rem">{{ $setting->gateway_name }} Informa:</div></div>
                  <div class="dropdown-item-content-subtext">Seja bem vindo Sr(a) {{ isset(explode(' ',auth()->user()->name)[0]) ? explode(' ',auth()->user()->name)[0] : auth()->user()->name }} a {{ $setting->gateway_name }}.</div>
                </div>
              </a>
            </li>
          </ul>
        </div> -->
        <!-- User profile dropdown-->
        <div class="dropdown">
          <button class="icon-navbar btn btn-lg btn-icon dropdown-toggle" 
              id="dropdownMenuProfile" 
              type="button" 
              data-bs-toggle="dropdown" 
              aria-expanded="false" 
              style="border-radius: 100px">
            <img src="{{auth()->user()->avatar}}" 
               alt="Avatar" 
               style="width:32px;height:32px;border-radius:100px">
          </button>
          <ul class="mt-3 dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuProfile">
            <li class="user-info-header">
              <div class="user-info-content">
                <div class="user-name">
                  {{ auth()->user()->username }}
                </div>
              </div>
            </li>
            <li>
              <a class="dropdown-item" href="{{route('my.profile.index')}}">
                <i class="material-icons leading-icon">person</i>
                <span class="me-3">Meu Perfil</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="#!">
                <i class="material-icons leading-icon">settings</i>
                <span class="me-3">Configurações</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="#!">
                <i class="material-icons leading-icon">help_outline</i>
                <span class="me-3">Central de Ajuda</span>
              </a>
            </li>
            <li><hr class="dropdown-divider" /></li>
            <li>
              <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="dropdown-item btn-link">
                  <i class="material-icons leading-icon">logout</i>
                  <span class="me-3">Sair da Conta</span>
                </button>
              </form>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- Modal de Filtros Globais -->
<div class="modal fade" id="globalFiltersModal" tabindex="-1" aria-labelledby="globalFiltersModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="globalFiltersModalLabel">
          <i class="bi bi-funnel me-2"></i>
          Filtros globais
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-4">Esses filtros serão aplicados em todos os relatórios, gráficos e tabelas.</p>
        
        <!-- Opções de Período Rápido -->
        <div class="mb-4">
          <label class="form-label">Período Rápido:</label>
          <div class="quick-period-options">
            <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-2" data-period="today">Hoje</button>
            <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-2" data-period="yesterday">Ontem</button>
            <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-2" data-period="last7days">7 dias</button>
            <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-2" data-period="last30days">30 dias</button>
            <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-2" data-period="all">Tudo</button>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="startDate" class="form-label">Data de início:</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-calendar3"></i>
              </span>
              <input type="datetime-local" class="form-control" id="startDate">
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="endDate" class="form-label">Data de término:</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-calendar3"></i>
              </span>
              <input type="datetime-local" class="form-control" id="endDate">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link" onclick="removeGlobalFilters()">
          Remover
        </button>
        <button type="button" class="btn btn-primary" onclick="applyGlobalFilters()">
          <i class="bi bi-check-circle me-2"></i>Aplicar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Filtros de Produtos -->
<div class="modal fade" id="productFiltersModal" tabindex="-1" aria-labelledby="productFiltersModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productFiltersModalLabel">
          <i class="bi bi-box me-2"></i>
          Filtros de Produtos
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-4">Selecione o produto para filtrar os dados do dashboard.</p>
        
        <div class="mb-3">
          <label for="productSelect" class="form-label">Produto:</label>
          <select id="productSelect" class="form-select">
            <option value="todos">Todos</option>
            @if(auth()->check() && auth()->user()->produtos)
              @foreach(auth()->user()->produtos as $produto)
                <option value="{{ $produto->id }}">{{ $produto->produto_name }}</option>
              @endforeach
            @endif
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link" onclick="removeProductFilters()">
          Remover
        </button>
        <button type="button" class="btn btn-primary" onclick="applyProductFilters()">
          <i class="bi bi-check-circle me-2"></i>Aplicar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Contato com Gerente -->
<div class="modal fade" id="gerenteModal" tabindex="-1" aria-labelledby="gerenteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="gerenteModalLabel">
          <i class="fas fa-headset me-2"></i>Gerente de Relacionamento
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body bg-light p-4">
        <!-- Card Principal do Gerente -->
        <div class="card shadow-sm mb-4">
          <div class="card-body p-4">
            <div class="d-flex align-items-center gap-4">
              <!-- Avatar -->
              <div class="flex-shrink-0">
                @if($setting->gerente_foto)
                  <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 4px solid #6200ea; box-shadow: 0 4px 12px rgba(98, 0, 234, 0.3);">
                    <img src="{{ asset($setting->gerente_foto) }}" 
                         alt="Foto do Gerente" 
                         style="width: 100%; height: 100%; object-fit: cover;"
                         onload="logGerenteFotoCarregada(this)"
                         onerror="logGerenteFotoErro(this)">
                  </div>
                @else
                  <div class="bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                       style="width: 80px; height: 80px; box-shadow: 0 4px 12px rgba(98, 0, 234, 0.3);">
                    <i class="fas fa-user-tie fa-2x"></i>
                  </div>
                @endif
              </div>
              
              <!-- Informações do Gerente -->
              <div class="flex-grow-1">
                <h4 class="fw-bold mb-1">{{ $setting->gerente_nome ?? 'ClosedPay' }}</h4>
                <p class="text-muted mb-2">Gerente de Relacionamento</p>
                <div class="d-flex align-items-center gap-2">
                  <i class="fas fa-phone text-primary"></i>
                  <span class="fw-semibold">{{ $setting->contato ?? '(11) 5286-1834' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cards de Ação -->
        <div class="row g-3 mb-3">
          <!-- WhatsApp -->
          <div class="col-md-6">
            <a href="https://api.whatsapp.com/send/?phone={{ str_replace(['(', ')', '-', ' '], '', $setting->contato ?? '71991221894') }}&text=Olá%2C%20sou%20{{ urlencode(auth()->user()->name ?? 'Cliente') }}%2C%20estou%20entrando%20em%20contato%20pela%20{{ urlencode($setting->gateway_name ?? 'ClosedPay') }}%20para%20falar%20com%20um%20gerente.&type=phone_number&app_absent=0" 
               target="_blank" 
               class="text-decoration-none">
              <div class="card shadow-sm h-100 card-hover" style="border-left: 4px solid #25D366; transition: all 0.3s ease;">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0">
                      <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                           style="width: 48px; height: 48px;">
                        <i class="fab fa-whatsapp fa-lg text-success"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-1 fw-semibold">WhatsApp</h6>
                      <small class="text-muted">Conversar agora</small>
                    </div>
                    <div class="flex-shrink-0">
                      <i class="fas fa-chevron-right text-muted"></i>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>

          <!-- Telefone -->
          <div class="col-md-6">
            <a href="tel:{{ str_replace(['(', ')', '-', ' '], '', $setting->contato ?? '71991221894') }}" 
               class="text-decoration-none">
              <div class="card shadow-sm h-100 card-hover" style="border-left: 4px solid #6200ea; transition: all 0.3s ease;">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0">
                      <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                           style="width: 48px; height: 48px;">
                        <i class="fas fa-phone fa-lg text-primary"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-1 fw-semibold">Telefone</h6>
                      <small class="text-muted">Ligar agora</small>
                    </div>
                    <div class="flex-shrink-0">
                      <i class="fas fa-chevron-right text-muted"></i>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>

        <!-- Informações Adicionais -->
        <div class="card shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
          <div class="card-body p-3 text-white">
            <div class="d-flex align-items-center gap-3">
              <div class="flex-shrink-0">
                <i class="fas fa-info-circle fa-2x opacity-75"></i>
              </div>
              <div class="flex-grow-1">
                <small class="d-block opacity-90 mb-1">Atendimento</small>
                <p class="mb-0 fw-semibold">Horário comercial: Seg-Sex, 9h às 18h</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer gap-2">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i>Fechar
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  /* Hover effect para os cards de ação */
  .card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
  }
  
  /* Tema escuro */
  [data-theme="dark"] #gerenteModal .modal-body {
    background-color: #1a1a1a !important;
  }
  
  [data-theme="dark"] #gerenteModal .card {
    background-color: #2d2d2d !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
  }
  
  [data-theme="dark"] #gerenteModal .card-body {
    color: #e5e7eb !important;
  }
  
  [data-theme="dark"] #gerenteModal .text-muted {
    color: #9ca3af !important;
  }
</style>

<script>
// Função para abrir o modal do gerente
    function openGerenteModal() {
      const modal = new bootstrap.Modal(document.getElementById('gerenteModal'));
      modal.show();

      // Adicionar efeito de animação no botão
      const btn = document.getElementById('gerenteContactBtn');
      btn.style.transform = 'scale(0.95)';
      setTimeout(() => {
        btn.style.transform = 'scale(1)';
      }, 150);
    }

    // Função para logar quando a foto do gerente é carregada
    function logGerenteFotoCarregada(img) {
      console.log('🖼️ Foto do Gerente Carregada:', {
        src: img.src,
        alt: img.alt,
        width: img.naturalWidth,
        height: img.naturalHeight,
        timestamp: new Date().toISOString(),
        modal: 'gerenteModal'
      });
      
      // Log adicional para debug
      console.log('✅ Imagem carregada com sucesso:', img.src);
    }

    // Função para logar quando há erro ao carregar a foto
    function logGerenteFotoErro(img) {
      console.error('❌ Erro ao carregar foto do gerente:', {
        src: img.src,
        alt: img.alt,
        timestamp: new Date().toISOString(),
        modal: 'gerenteModal'
      });
      
      // Log adicional para debug
      console.error('🚨 Falha no carregamento da imagem:', img.src);
    }

// Função para abrir o modal de filtros globais
function toggleGlobalFilters() {
  const modal = new bootstrap.Modal(document.getElementById('globalFiltersModal'));
  modal.show();
  
  // Carregar filtros salvos se existirem, senão usar datas padrão do mês atual
  loadSavedFilters();
}

// Função para abrir o modal de filtros de produtos
function toggleProductFilters() {
  const modal = new bootstrap.Modal(document.getElementById('productFiltersModal'));
  modal.show();
  
  // Carregar produto salvo se existir
  loadSavedProduct();
}

// Função para carregar produto salvo
function loadSavedProduct() {
  console.log('📦 Carregando produto salvo...');
  
  const savedProduct = localStorage.getItem('globalProductFilter');
  if (savedProduct) {
    console.log('💾 Produto salvo encontrado:', savedProduct);
    document.getElementById('productSelect').value = savedProduct;
  } else {
    console.log('📦 Nenhum produto salvo, usando padrão');
    document.getElementById('productSelect').value = 'todos';
  }
}

// Função para aplicar filtros de produtos
function applyProductFilters() {
  const selectedProduct = document.getElementById('productSelect').value;
  
  console.log('📦 Aplicando filtro de produto:', selectedProduct);
  
  // Salvar produto no localStorage
  localStorage.setItem('globalProductFilter', selectedProduct);
  
  // Fechar modal
  const modal = bootstrap.Modal.getInstance(document.getElementById('productFiltersModal'));
  modal.hide();
  
  // Mostrar notificação de sucesso
  if (typeof showToast === 'function') {
    showToast('success', 'Filtro de produto aplicado com sucesso!');
  }
  
  // Recarregar a página com o filtro de produto
  reloadPageWithProductFilter(selectedProduct);
  
  // Disparar evento customizado
  window.dispatchEvent(new CustomEvent('productFiltersChanged', { 
    detail: { product: selectedProduct } 
  }));
}

// Função para remover filtros de produtos
function removeProductFilters() {
  localStorage.removeItem('globalProductFilter');
  
  // Fechar modal
  const modal = bootstrap.Modal.getInstance(document.getElementById('productFiltersModal'));
  modal.hide();
  
  // Mostrar notificação
  if (typeof showToast === 'function') {
    showToast('info', 'Filtro de produto removido.');
  }
  
  // Recarregar a página sem filtro de produto
  reloadPageWithoutProductFilter();
  
  // Disparar evento customizado
  window.dispatchEvent(new CustomEvent('productFiltersChanged', { 
    detail: { product: null } 
  }));
}

// Função para recarregar a página com filtro de produto
function reloadPageWithProductFilter(product) {
  console.log('🔄 Recarregando página com filtro de produto:', product);
  
  // Construir nova URL
  const currentUrl = new URL(window.location);
  currentUrl.searchParams.set('produto', product);
  
  console.log('🔗 Nova URL:', currentUrl.toString());
  
  // Recarregar a página
  window.location.href = currentUrl.toString();
}

// Função para recarregar a página sem filtro de produto
function reloadPageWithoutProductFilter() {
  console.log('🔄 Removendo filtro de produto e recarregando página...');
  
  // Construir nova URL sem parâmetro de produto
  const currentUrl = new URL(window.location);
  currentUrl.searchParams.delete('produto');
  
  console.log('🔗 Nova URL sem filtro:', currentUrl.toString());
  
  // Recarregar a página
  window.location.href = currentUrl.toString();
}

// Função para aplicar os filtros globais
function applyGlobalFilters() {
  const startDate = document.getElementById('startDate').value;
  const endDate = document.getElementById('endDate').value;
  
  if (!startDate || !endDate) {
    alert('Por favor, selecione ambas as datas.');
    return;
  }
  
  if (new Date(startDate) > new Date(endDate)) {
    alert('A data de início deve ser anterior à data de término.');
    return;
  }
  
  // Salvar filtros no localStorage
  const filters = {
    startDate: startDate,
    endDate: endDate,
    applied: true
  };
  
  localStorage.setItem('globalFilters', JSON.stringify(filters));
  
  // Fechar modal
  const modal = bootstrap.Modal.getInstance(document.getElementById('globalFiltersModal'));
  modal.hide();
  
  // Mostrar notificação de sucesso
  if (typeof showToast === 'function') {
    showToast('success', 'Filtros globais aplicados com sucesso!');
  }
  
  // Recarregar a página com os filtros aplicados
  reloadPageWithGlobalFilters(filters);
  
  // Disparar evento customizado para que outras páginas possam reagir
  window.dispatchEvent(new CustomEvent('globalFiltersChanged', { 
    detail: filters 
  }));
}

// Função para recarregar a página com filtros globais
function reloadPageWithGlobalFilters(filters) {
  console.log('🔄 Recarregando página com filtros globais:', filters);
  
  // Excluir páginas específicas dos filtros globais
  if (window.location.pathname.includes('/splits-internos') || 
      window.location.pathname.includes('/gateway-wallet') ||
      window.location.pathname.includes('/admin/dashboard') ||
      window.location.pathname.includes('/admin/ajustes/adquirentes') ||
      window.location.pathname.includes('/admin/usuarios') ||
      window.location.pathname.includes('/documentacao') ||
      window.location.pathname.includes('/chaves') ||
      window.location.pathname.includes('/webhook') ||
      window.location.pathname.includes('/affiliate') ||
      window.location.pathname.includes('/financeiro') ||
      window.location.pathname.includes('/admin/aprovar-saques') ||
      window.location.pathname.includes('/admin/saque-config') ||
      window.location.pathname.includes('/admin/transacoes/entrada') ||
      window.location.pathname.includes('/admin/transacoes/saida') ||
      window.location.pathname.includes('/admin/ajustes/gerais') ||
      window.location.pathname.includes('/admin/ajustes/niveis') ||
      window.location.pathname.includes('/admin/ajustes/apoio') ||
      window.location.pathname.includes('/admin/ajustes/smtp') ||
      window.location.pathname.includes('/integracoes') ||
      window.location.pathname.includes('/produtos') ||
      window.location.pathname.includes('/my-profile')) {
    console.log('🚫 Página excluída dos filtros globais detectada, não aplicando filtros');
    return;
  }
  
  // Converter datas para formato do sistema
  const startDateStr = filters.startDate.split('T')[0]; // YYYY-MM-DD
  const endDateStr = filters.endDate.split('T')[0]; // YYYY-MM-DD
  
  // Determinar qual período usar baseado nas datas
  const periodo = determinePeriodFromDates(startDateStr, endDateStr);
  
  console.log('📅 Período determinado:', periodo);
  
  // Construir nova URL
  const currentUrl = new URL(window.location);
  currentUrl.searchParams.set('periodo', periodo);
  
  // Se for período personalizado, usar formato dataInicio:dataFim
  if (periodo === 'personalizado') {
    currentUrl.searchParams.set('periodo', `${startDateStr}:${endDateStr}`);
  }
  
  console.log('🔗 Nova URL:', currentUrl.toString());
  
  // Recarregar a página
  window.location.href = currentUrl.toString();
}

// Função para determinar o período baseado nas datas
function determinePeriodFromDates(startDateStr, endDateStr) {
  const startDate = new Date(startDateStr);
  const endDate = new Date(endDateStr);
  const now = new Date();
  
  // Hoje
  const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  const todayEnd = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  if (startDate.getTime() === todayStart.getTime() && endDate.getTime() === todayEnd.getTime()) {
    return 'hoje';
  }
  
  // Ontem
  const yesterday = new Date(now);
  yesterday.setDate(yesterday.getDate() - 1);
  const yesterdayStart = new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate());
  const yesterdayEnd = new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate());
  if (startDate.getTime() === yesterdayStart.getTime() && endDate.getTime() === yesterdayEnd.getTime()) {
    return 'ontem';
  }
  
  // 7 dias
  const last7Start = new Date(now);
  last7Start.setDate(last7Start.getDate() - 6);
  const last7End = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  if (startDate.getTime() === last7Start.getTime() && endDate.getTime() === last7End.getTime()) {
    return '7dias';
  }
  
  // 30 dias
  const last30Start = new Date(now);
  last30Start.setDate(last30Start.getDate() - 29);
  const last30End = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  if (startDate.getTime() === last30Start.getTime() && endDate.getTime() === last30End.getTime()) {
    return '30dias';
  }
  
  // Tudo (período muito amplo)
  const allStart = new Date(2020, 0, 1);
  const allEnd = new Date(now.getFullYear() + 1, 11, 31);
  if (startDate.getTime() === allStart.getTime() && endDate.getTime() === allEnd.getTime()) {
    return 'tudo';
  }
  
  return 'personalizado'; // Período personalizado
}

// Função para remover os filtros globais
function removeGlobalFilters() {
  localStorage.removeItem('globalFilters');
  
  // Limpar campos e definir datas padrão do mês atual
  setDefaultDates();
  
  // Fechar modal
  const modal = bootstrap.Modal.getInstance(document.getElementById('globalFiltersModal'));
  modal.hide();
  
  // Mostrar notificação
  if (typeof showToast === 'function') {
    showToast('info', 'Filtros globais removidos.');
  }
  
  // Recarregar a página sem filtros (período padrão)
  reloadPageWithoutGlobalFilters();
  
  // Disparar evento customizado
  window.dispatchEvent(new CustomEvent('globalFiltersChanged', { 
    detail: { applied: false } 
  }));
}

// Função para recarregar a página sem filtros globais
function reloadPageWithoutGlobalFilters() {
  console.log('🔄 Removendo filtros globais e recarregando página...');
  
  // Construir nova URL sem parâmetro de período (usar padrão)
  const currentUrl = new URL(window.location);
  currentUrl.searchParams.delete('periodo');
  
  console.log('🔗 Nova URL sem filtros:', currentUrl.toString());
  
  // Recarregar a página
  window.location.href = currentUrl.toString();
}

// Função para definir datas padrão do mês atual
function setDefaultDates() {
  const now = new Date();
  const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
  const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59);
  
  // Formatar para datetime-local (YYYY-MM-DDTHH:MM)
  const startDateStr = firstDay.toISOString().slice(0, 16);
  const endDateStr = lastDay.toISOString().slice(0, 16);
  
  document.getElementById('startDate').value = startDateStr;
  document.getElementById('endDate').value = endDateStr;
}

// Função para carregar filtros salvos ou usar datas padrão
function loadSavedFilters() {
  console.log('📂 Carregando filtros salvos...');
  
  const savedFilters = localStorage.getItem('globalFilters');
  if (savedFilters) {
    const filters = JSON.parse(savedFilters);
    console.log('💾 Filtros salvos encontrados:', filters);
    
    document.getElementById('startDate').value = filters.startDate || '';
    document.getElementById('endDate').value = filters.endDate || '';
    
    // Detectar qual período rápido corresponde às datas salvas
    const detectedPeriod = detectQuickPeriod(filters.startDate, filters.endDate);
    if (detectedPeriod) {
      console.log('🎯 Período detectado:', detectedPeriod);
      updateQuickPeriodButtons(detectedPeriod);
    }
  } else {
    console.log('📅 Nenhum filtro salvo, usando datas padrão do mês atual');
    // Se não há filtros salvos, usar datas padrão do mês atual
    setDefaultDates();
    updateQuickPeriodButtons(null); // Limpar seleção
  }
}

// Função para detectar qual período rápido corresponde às datas
function detectQuickPeriod(startDateStr, endDateStr) {
  if (!startDateStr || !endDateStr) return null;
  
  const startDate = new Date(startDateStr);
  const endDate = new Date(endDateStr);
  const now = new Date();
  
  // Hoje
  const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0);
  const todayEnd = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
  if (startDate.getTime() === todayStart.getTime() && endDate.getTime() === todayEnd.getTime()) {
    return 'today';
  }
  
  // Ontem
  const yesterday = new Date(now);
  yesterday.setDate(yesterday.getDate() - 1);
  const yesterdayStart = new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate(), 0, 0, 0);
  const yesterdayEnd = new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate(), 23, 59, 59);
  if (startDate.getTime() === yesterdayStart.getTime() && endDate.getTime() === yesterdayEnd.getTime()) {
    return 'yesterday';
  }
  
  // 7 dias
  const last7Start = new Date(now);
  last7Start.setDate(last7Start.getDate() - 6);
  last7Start.setHours(0, 0, 0, 0);
  const last7End = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
  if (startDate.getTime() === last7Start.getTime() && endDate.getTime() === last7End.getTime()) {
    return 'last7days';
  }
  
  // 30 dias
  const last30Start = new Date(now);
  last30Start.setDate(last30Start.getDate() - 29);
  last30Start.setHours(0, 0, 0, 0);
  const last30End = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
  if (startDate.getTime() === last30Start.getTime() && endDate.getTime() === last30End.getTime()) {
    return 'last30days';
  }
  
  // Tudo (período muito amplo)
  const allStart = new Date(2020, 0, 1, 0, 0, 0);
  const allEnd = new Date(now.getFullYear() + 1, 11, 31, 23, 59, 59);
  if (startDate.getTime() === allStart.getTime() && endDate.getTime() === allEnd.getTime()) {
    return 'all';
  }
  
  return null; // Período personalizado
}

// Função para obter filtros atuais (para uso em outras páginas)
function getGlobalFilters() {
  const savedFilters = localStorage.getItem('globalFilters');
  return savedFilters ? JSON.parse(savedFilters) : null;
}

// Indicar visualmente quando há filtros aplicados
function updateFilterButtonState() {
  const filters = getGlobalFilters();
  const button = document.getElementById('globalFiltersBtn');
  
  if (filters && filters.applied) {
    button.style.color = 'hsl(var(--primary))';
    button.title = 'Filtros aplicados: ' + filters.startDate + ' até ' + filters.endDate;
  } else {
    button.style.color = '';
    button.title = 'Filtros globais';
  }
}

// Atualizar estado do botão quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
  updateFilterButtonState();
});

// Escutar mudanças nos filtros para atualizar o estado do botão
window.addEventListener('globalFiltersChanged', function() {
  updateFilterButtonState();
});

// Função para aplicar filtros globais automaticamente ao carregar a página
function applyGlobalFiltersOnPageLoad() {
  console.log('🔄 Verificando filtros globais ao carregar a página...');
  
  // Excluir páginas específicas dos filtros globais
  if (window.location.pathname.includes('/splits-internos') || 
      window.location.pathname.includes('/gateway-wallet') ||
      window.location.pathname.includes('/admin/dashboard') ||
      window.location.pathname.includes('/admin/ajustes/adquirentes') ||
      window.location.pathname.includes('/documentacao') ||
      window.location.pathname.includes('/chaves') ||
      window.location.pathname.includes('/webhook') ||
      window.location.pathname.includes('/affiliate') ||
      window.location.pathname.includes('/financeiro') ||
      window.location.pathname.includes('/admin/aprovar-saques') ||
      window.location.pathname.includes('/admin/saque-config') ||
      window.location.pathname.includes('/admin/transacoes/entrada') ||
      window.location.pathname.includes('/admin/transacoes/saida') ||
      window.location.pathname.includes('/admin/ajustes/gerais') ||
      window.location.pathname.includes('/admin/ajustes/niveis') ||
      window.location.pathname.includes('/admin/ajustes/apoio') ||
      window.location.pathname.includes('/admin/ajustes/smtp') ||
      window.location.pathname.includes('/integracoes') ||
      window.location.pathname.includes('/produtos') ||
      window.location.pathname.includes('/my-profile')) {
    console.log('🚫 Página excluída dos filtros globais detectada, pulando filtros globais');
    return;
  }
  
  // Verificar se há parâmetro de período na URL
  const urlParams = new URLSearchParams(window.location.search);
  const periodoParam = urlParams.get('periodo');
  
  if (periodoParam) {
    console.log('📅 Período já definido na URL:', periodoParam);
    return; // Já tem período, não fazer nada
  }
  
  // Verificar se há filtros globais salvos
  const savedFilters = localStorage.getItem('globalFilters');
  if (!savedFilters) {
    console.log('📅 Nenhum filtro global salvo, usando padrão');
    return; // Não há filtros salvos, usar padrão
  }
  
  const filters = JSON.parse(savedFilters);
  console.log('💾 Filtros globais encontrados:', filters);
  
  // Aplicar filtros globais automaticamente
  reloadPageWithGlobalFilters(filters);
}

// Função para aplicar filtros de produtos automaticamente ao carregar a página
function applyProductFiltersOnPageLoad() {
  console.log('📦 Verificando filtros de produtos ao carregar a página...');
  
  // Verificar se há parâmetro de produto na URL
  const urlParams = new URLSearchParams(window.location.search);
  const produtoParam = urlParams.get('produto');
  
  if (produtoParam) {
    console.log('📦 Produto já definido na URL:', produtoParam);
    return; // Já tem produto, não fazer nada
  }
  
  // Verificar se há filtro de produto salvo
  const savedProduct = localStorage.getItem('globalProductFilter');
  if (!savedProduct) {
    console.log('📦 Nenhum filtro de produto salvo, usando padrão');
    return; // Não há filtro salvo, usar padrão
  }
  
  console.log('💾 Filtro de produto encontrado:', savedProduct);
  
  // Aplicar filtro de produto automaticamente
  reloadPageWithProductFilter(savedProduct);
}

// Aplicar filtros globais quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
  // Pequeno delay para garantir que tudo carregou
  setTimeout(applyGlobalFiltersOnPageLoad, 100);
  setTimeout(applyProductFiltersOnPageLoad, 150);
});

// Função para definir período rápido
function setQuickPeriod(period) {
  console.log('📅 Definindo período rápido:', period);
  
  const now = new Date();
  let startDate, endDate;
  
  switch(period) {
    case 'today':
      startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0);
      endDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
      break;
      
    case 'yesterday':
      const yesterday = new Date(now);
      yesterday.setDate(yesterday.getDate() - 1);
      startDate = new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate(), 0, 0, 0);
      endDate = new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate(), 23, 59, 59);
      break;
      
    case 'last7days':
      startDate = new Date(now);
      startDate.setDate(startDate.getDate() - 6);
      startDate.setHours(0, 0, 0, 0);
      endDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
      break;
      
    case 'last30days':
      startDate = new Date(now);
      startDate.setDate(startDate.getDate() - 29);
      startDate.setHours(0, 0, 0, 0);
      endDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
      break;
      
    case 'all':
      startDate = new Date(2020, 0, 1, 0, 0, 0); // Data muito antiga
      endDate = new Date(now.getFullYear() + 1, 11, 31, 23, 59, 59); // Data futura
      break;
      
    default:
      console.log('❌ Período não reconhecido:', period);
      return;
  }
  
  // Formatar para datetime-local
  const startDateStr = startDate.toISOString().slice(0, 16);
  const endDateStr = endDate.toISOString().slice(0, 16);
  
  console.log('📅 Datas calculadas:', { startDateStr, endDateStr });
  
  // Preencher os campos
  document.getElementById('startDate').value = startDateStr;
  document.getElementById('endDate').value = endDateStr;
  
  // Atualizar estado visual dos botões
  updateQuickPeriodButtons(period);
  
  console.log('✅ Período rápido aplicado:', period);
}

// Função para atualizar estado visual dos botões de período rápido
function updateQuickPeriodButtons(activePeriod) {
  console.log('🎨 Atualizando botões de período rápido. Período ativo:', activePeriod);
  
  const buttons = document.querySelectorAll('.quick-period-options button');
  console.log('🔘 Botões encontrados:', buttons.length);
  
  buttons.forEach(button => {
    const period = button.dataset.period;
    console.log('🔘 Processando botão:', period, 'Ativo:', activePeriod);
    
    // Remover classes ativas
    button.classList.remove('btn-primary');
    button.classList.add('btn-outline-secondary');
    
    // Adicionar classe ativa se for o período selecionado
    if (period === activePeriod) {
      console.log('✅ Ativando botão:', period);
      button.classList.remove('btn-outline-secondary');
      button.classList.add('btn-primary');
    }
  });
  
  console.log('✅ Botões atualizados');
}

// Adicionar event listeners para os botões de período rápido
document.addEventListener('DOMContentLoaded', function() {
  console.log('🔧 Configurando event listeners para botões de período rápido...');
  
  const quickPeriodButtons = document.querySelectorAll('.quick-period-options button');
  console.log('🔘 Botões encontrados para configurar:', quickPeriodButtons.length);
  
  quickPeriodButtons.forEach(button => {
    const period = button.dataset.period;
    console.log('🔘 Configurando botão:', period);
    
    button.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      console.log('👆 Botão clicado:', period);
      setQuickPeriod(period);
    });
  });
  
  console.log('✅ Event listeners configurados');
});
</script>
