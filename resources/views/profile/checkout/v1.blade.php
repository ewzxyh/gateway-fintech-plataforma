@php
$setting = \App\Helpers\Helper::getSetting();

$produto_tipo = $checkout->produto_tipo;
$meta_active = !empty($checkout->checkout_ads_meta);
$google_active = !empty($checkout->checkout_ads_google);

$efi = \App\Models\Adquirente::where('referencia', 'efi')->first()->status;
$regefi = \App\Models\Efi::first();
$identificar_conta = $regefi->identificador_conta ?? null;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme-mode="dark" data-header-styles="dark" style="" data-menu-styles="dark">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="Description" content="{{env('APP_NAME')}}">
<meta name="Author" content="{{env('APP_NAME')}}">
<meta name="keywords" content="{{env('APP_NAME')}}">
<link rel="icon" type="image/x-icon" href="{{ asset('assets/images/site_logo/logo_white.png') }}">
<title>{{ env('APP_NAME') }} - {{ $checkout->produto_name }}</title>
<link rel="icon" href="../img/logo.png" type="image/x-icon">
<link id="style" href="{{ asset("assets-check/libs/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet">
<link href="{{ asset("assets-check/css/styles.css") }}" rel="stylesheet">
<link href="{{ asset("assets-check/icon-fonts/icons.css") }}" rel="stylesheet">
<link href="{{ asset("assets-check/libs/node-waves/waves.min.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset("assets-check/libs/simplebar/simplebar.min.css") }}">
<link rel="stylesheet" href="{{ asset("assets-check/libs/flatpickr/flatpickr.min.css") }}">

<style>
/* Estilos para detecção automática de bandeira */
.card-brand-indicator {
  border: none;
  border-radius: 4px;
  padding: 8px;
  background-color: transparent;
  transition: all 0.3s ease;
  min-height: 0;
  display: flex;
  justify-content: center;
  align-items: center;
}

.card-brand-indicator.detected {
  background-color: transparent;
}

.brand-icon-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 30px;
}

.brand-icon {
  height: 24px;
  width: auto;
  opacity: 0;
  transition: opacity 0.2s ease;
}

.brand-icon.show {
  opacity: 1;
}

.brand-text {
  display: none;
}

/* Ícones das bandeiras */
.visa-icon {
  content: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 70" width="100" height="70"><rect width="100" height="70" rx="5" fill="white"/><rect width="100" height="20" fill="%231a1f71"/><text x="50" y="45" font-family="Arial" font-size="18" font-weight="bold" text-anchor="middle" fill="%231a1f71">VISA</text></svg>');
}

.mastercard-icon {
  content: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 70" width="100" height="70"><rect width="100" height="70" rx="5" fill="white"/><circle cx="40" cy="35" r="15" fill="%23eb001b"/><circle cx="60" cy="35" r="15" fill="%23f79e1b"/><text x="50" y="65" font-family="Arial" font-size="10" font-weight="bold" text-anchor="middle" fill="%231e2b4d">MASTERCARD</text></svg>');
}

/* Animação suave */
.brand-icon-container img {
  animation: fadeInScale 0.3s ease-in-out;
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.8);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Estado de erro para bandeira não suportada */
.card-brand-indicator.error {
  border-color: #dc3545;
  background-color: #f8d7da;
}

.brand-text.error {
  color: #dc3545;
}

/* Estado placeholder para bandeira não identificada */
.card-brand-indicator.placeholder {
  background-color: transparent;
  opacity: 0.7;
}

.card-brand-indicator.placeholder .brand-icon {
  opacity: 0.5;
}
</style>
<link rel="stylesheet" href="{{ asset("assets-check/libs/@simonwep/pickr/themes/nano.min.css") }}">
<link rel="stylesheet" href="{{ asset("assets-check/libs/@tarekraafat/autocomplete.js/css/autoComplete.css") }}">
<link rel="stylesheet" href="{{ asset("assets-check/libs/choices.js/public/assets/styles/choices.min.css") }}">
<script src="{{ asset("assets-check/libs/choices.js/public/assets/scripts/choices.min.js") }}"></script>
<script src="{{ asset("assets-check/js/main.js") }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.9/jquery.inputmask.min.js" integrity="sha512-F5Ul1uuyFlGnIT1dk2c4kB4DBdi5wnBJjVhL7gQlGh46Xn0VhvD8kgxLtjdZ5YN83gybk/aASUAlpdoWUjRR3g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="/assets-checkout/css/style-checkout.css">
<style>
  /* ========== SISTEMA DE CORES DINÂMICO ========== */
  :root {
    --checkout-color: {{$checkout->checkout_color_default ?? $setting->gateway_color}};
    --color-default: {{$checkout->checkout_color_default ?? $setting->gateway_color}};
    --checkout-bg-color: {{$checkout->checkout_color ?? '#0a0a0a'}};
    --checkout-card-color: {{$checkout->checkout_color_card ?? '#1a1a1a'}};
    
    /* Sistema de cores dinâmico baseado na luminosidade */
    --bg-primary: {{$checkout->checkout_color ?? '#0a0a0a'}};
    --bg-secondary: {{ \App\Helpers\ColorHelper::adjustBrightness($checkout->checkout_color_card ?? '#1a1a1a', 0.1) }};
    --bg-card: {{$checkout->checkout_color_card ?? '#1a1a1a'}};
    --bg-card-hover: {{ \App\Helpers\ColorHelper::adjustBrightness($checkout->checkout_color_card ?? '#1a1a1a', 0.15) }};
    --bg-input: {{ \App\Helpers\ColorHelper::adjustBrightness($checkout->checkout_color_card ?? '#1a1a1a', 0.2) }};
    
    /* Cores dinâmicas baseadas na luminosidade do fundo */
    --text-primary: {{ \App\Helpers\ColorHelper::getContrastColor($checkout->checkout_color ?? '#0a0a0a') }};
    --text-secondary: {{ \App\Helpers\ColorHelper::getContrastColor($checkout->checkout_color ?? '#0a0a0a', 0.8) }};
    --text-muted: {{ \App\Helpers\ColorHelper::getContrastColor($checkout->checkout_color ?? '#0a0a0a', 0.6) }};
    --border-color: {{ \App\Helpers\ColorHelper::getContrastColor($checkout->checkout_color ?? '#0a0a0a', 0.3) }};
    --border-light: {{ \App\Helpers\ColorHelper::getContrastColor($checkout->checkout_color ?? '#0a0a0a', 0.2) }};
    
    /* Cores de destaque */
    --accent-green: #00ff88;
    --accent-gold: #ffd700;
    --accent-red: #ff6b6b;
  }
  
  /* 
   * SISTEMA DE CORES DINÂMICO
   * 
   * Este sistema calcula automaticamente as cores de contraste baseado na cor de fundo escolhida:
   * - Se o fundo for ESCURO → texto fica BRANCO
   * - Se o fundo for CLARO → texto fica PRETO
   * 
   * As cores são calculadas usando a fórmula de luminosidade padrão:
   * Luminosidade = (0.299 * R + 0.587 * G + 0.114 * B) / 255
   * 
   * Variáveis disponíveis:
   * --text-primary: Cor principal do texto (branco ou preto)
   * --text-secondary: Cor secundária (80% de opacidade)
   * --text-muted: Cor suave (60% de opacidade)
   * --border-color: Cor das bordas (30% de opacidade)
   * --border-light: Cor das bordas claras (20% de opacidade)
   */
  
  /* Body & Background */
  body {
    background: var(--bg-primary) !important;
    color: var(--text-primary) !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
  }
  
  #background_color {
    background: var(--bg-primary) !important;
  }
  
  /* Cards */
  .card-bg,
  .body-container,
  .bg-steps-form {
    background: var(--bg-card) !important;
    border: 1px solid var(--border-color) !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5) !important;
  }
  
  /* Text Colors - Melhor contraste */
  .default-font-color,
  .step-text,
  .cart,
  .subtotal,
  .about_purchase,
  .sub,
  label,
  p,
  span,
  h1, h2, h3, h4, h5, h6 {
    color: var(--text-primary) !important;
    font-weight: 500 !important;
  }
  
  .text-muted,
  .obs {
    color: var(--text-muted) !important;
  }
  
  /* Labels dos formulários */
  .form-label {
    color: var(--text-secondary) !important;
    font-weight: 600 !important;
    margin-bottom: 8px !important;
  }
  
  /* Inputs & Selects */
  input,
  select,
  textarea,
  .form-control,
  [type='search'] {
    background-color: var(--bg-input) !important;
    border: 2px solid var(--border-color) !important;
    color: var(--text-primary) !important;
    transition: all 0.3s ease;
    border-radius: 8px !important;
    padding: 12px 16px !important;
    font-size: 16px !important;
  }
  
  input::placeholder,
  textarea::placeholder {
    color: var(--text-muted) !important;
    opacity: 0.8 !important;
  }
  
  input:focus,
  select:focus,
  textarea:focus,
  .form-control:focus {
    background-color: var(--bg-card-hover) !important;
    border-color: var(--checkout-color) !important;
    box-shadow: 0 0 0 3px rgba(var(--checkout-color), 0.1) !important;
    color: var(--text-primary) !important;
    outline: none !important;
  }
  
  /* Select Options */
  select option {
    background-color: var(--bg-card) !important;
    color: var(--text-primary) !important;
    padding: 8px !important;
  }
  
  /* Steps */
  .guide.current .guide-text .step-number,
  .qtde {
    background: var(--checkout-color) !important;
    color: #ffffff !important;
    box-shadow: 0 0 20px rgba(var(--checkout-color), 0.5);
  }
  
  .step-number {
    background: var(--bg-input) !important;
    border: 2px solid var(--border-light) !important;
    color: var(--text-secondary) !important;
  }
  
  /* Payment Buttons */
  .btn-outline-custom {
    border: 2px solid var(--border-light) !important;
    width: 100px !important;
    height: 100px !important;
    color: var(--text-primary) !important;
    font-weight: bold;
    background-color: var(--bg-card) !important;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding-top: 0;
  }

  .btn-outline-custom:hover {
    border: 2px solid var(--checkout-color) !important;
    background-color: var(--bg-card-hover) !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  }

  .btn-outline-custom i {
    font-size: 24px;
  }
  
  .btn-check:checked + .btn-outline-custom {
    background: linear-gradient(135deg, var(--checkout-color), var(--checkout-color)) !important;
    color: #fff !important;
    border-color: var(--checkout-color) !important;
    box-shadow: 0 0 30px rgba(var(--checkout-color), 0.6);
  }
  
  /* Primary Buttons */
  .btn-form-checkout,
  .btn-primary,
  .btn-success,
  .btn-warning,
  .btn-danger {
    background: var(--checkout-color) !important;
    border: 2px solid var(--checkout-color) !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    padding: 12px 24px !important;
    border-radius: 8px !important;
    transition: all 0.3s ease !important;
    font-size: 16px !important;
  }
  
  .btn-form-checkout:hover,
  .btn-primary:hover,
  .btn-success:hover,
  .btn-warning:hover,
  .btn-danger:hover {
    background: var(--checkout-color) !important;
    border-color: var(--checkout-color) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(var(--checkout-color), 0.3) !important;
    color: #ffffff !important;
  }
  
  /* Botões secundários */
  .btn-secondary,
  .btn-outline-secondary {
    background: transparent !important;
    border: 2px solid var(--border-color) !important;
    color: var(--text-primary) !important;
    font-weight: 600 !important;
    padding: 12px 24px !important;
    border-radius: 8px !important;
    transition: all 0.3s ease !important;
  }
  
  .btn-secondary:hover,
  .btn-outline-secondary:hover {
    background: var(--bg-card-hover) !important;
    border-color: var(--checkout-color) !important;
    color: var(--text-primary) !important;
  }
  
  /* Links */
  a {
    color: var(--checkout-color) !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
  }
  
  a:hover {
    color: var(--accent-green) !important;
    text-decoration: underline !important;
  }
  
  /* Badges e Labels */
  .badge,
  .label {
    background: var(--checkout-color) !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 6px !important;
    font-size: 0.875rem !important;
  }
  
  /* Alertas */
  .alert {
    background: var(--bg-card) !important;
    border: 1px solid var(--border-color) !important;
    color: var(--text-primary) !important;
    border-radius: 8px !important;
    padding: 16px !important;
  }
  
  .alert-success {
    background: rgba(0, 255, 136, 0.1) !important;
    border-color: var(--accent-green) !important;
    color: var(--accent-green) !important;
  }
  
  .alert-danger {
    background: rgba(255, 107, 107, 0.1) !important;
    border-color: #ff6b6b !important;
    color: #ff6b6b !important;
  }
  
  .alert-warning {
    background: rgba(255, 215, 0, 0.1) !important;
    border-color: var(--accent-gold) !important;
    color: var(--accent-gold) !important;
  }
  .btn-form-checkout,
  .btn-form-checkout-prev {
    background: linear-gradient(135deg, var(--checkout-color), var(--checkout-color)) !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    padding: 12px 30px !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
  }
  
  .btn-form-checkout:hover,
  .btn-form-checkout-prev:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(var(--checkout-color), 0.4) !important;
  }
  
  /* Secondary Buttons */
  .btn-outline-dark {
    background: var(--bg-input) !important;
    border: 1px solid var(--border-light) !important;
    color: var(--text-primary) !important;
  }
  
  .btn-outline-dark:hover {
    background: var(--bg-card-hover) !important;
    border-color: var(--text-primary) !important;
  }
  
  /* Card Form */
  .card-form {
    background: var(--bg-secondary) !important;
    border: 1px solid var(--border-color) !important;
    border-radius: 12px;
    padding: 20px;
    margin-top: 15px;
  }
  
  .card-form .form-control {
    height: 42px;
    background-color: var(--bg-input) !important;
    border: 1px solid var(--border-color) !important;
    color: var(--text-primary) !important;
  }
  
  .card-form .form-label {
    font-weight: 600;
    color: var(--text-primary) !important;
    margin-bottom: 5px;
  }
  
  /* Payment Method Sections */
  #pix_data_payment,
  #billet_data_payment,
  #card_data_payment {
    background: var(--bg-secondary) !important;
    border: 1px solid var(--border-light) !important;
    border-radius: 12px !important;
  }
  
  /* Discount Badge */
  .percent-discount {
    background: linear-gradient(135deg, var(--accent-green), #00cc70) !important;
    color: #000 !important;
    font-weight: bold;
    border-radius: 8px;
    padding: 8px 12px;
  }
  
  .discount-text {
    color: var(--text-primary) !important;
  }
  
  .economize-value {
    color: var(--accent-green) !important;
    font-weight: bold;
  }
  
  /* Info Segura */
  .info-segura {
    background: var(--bg-secondary) !important;
    border: 1px solid var(--border-color) !important;
    border-radius: 12px;
  }
  
  /* Product Card */
  .produto {
    background: var(--bg-card) !important;
    border: 1px solid var(--border-color) !important;
  }
  
  .product-img {
    border: 1px solid var(--border-light);
    border-radius: 8px;
  }
  
  /* Bumps */
  .container-item {
    background: var(--bg-card) !important;
    border: 2px solid var(--border-color) !important;
    border-radius: 12px;
    transition: all 0.3s ease;
    padding: 20px !important;
    margin-bottom: 20px !important;
  }
  
  .container-item:hover {
    border-color: var(--checkout-color) !important;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
    transform: translateY(-2px);
  }
  
  .container-item h6 {
    color: var(--text-primary) !important;
    font-weight: bold !important;
    font-size: 1.2rem !important;
    margin-bottom: 8px !important;
  }
  
  .container-item p {
    color: var(--text-secondary) !important;
    margin-bottom: 8px !important;
    line-height: 1.4 !important;
  }
  
  .container-item .text-danger {
    color: #ff6b6b !important;
    font-size: 0.9rem !important;
    text-decoration: line-through !important;
  }
  
  .container-item .text-success {
    color: var(--accent-green) !important;
    font-size: 1.3rem !important;
    font-weight: bold !important;
  }
  
  .btn-add-bump {
    background: linear-gradient(135deg, var(--accent-gold), #ffed4e) !important;
    color: #000 !important;
    border: none !important;
    font-weight: bold !important;
    transition: all 0.3s ease !important;
    padding: 12px 24px !important;
    border-radius: 8px !important;
    font-size: 1rem !important;
  }
  
  .btn-add-bump:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 20px rgba(255, 215, 0, 0.4);
    background: linear-gradient(135deg, #ffed4e, var(--accent-gold)) !important;
  }
  
  .ob-purchased {
    background: var(--accent-green) !important;
    color: #000 !important;
    padding: 8px 16px !important;
    border-radius: 6px !important;
    font-weight: bold !important;
    font-size: 0.9rem !important;
  }
  
  /* Depoimentos */
  .depoimento-container {
    background: var(--bg-card) !important;
    border: 1px solid var(--border-color) !important;
    transition: all 0.3s ease;
  }
  
  .depoimento-container:hover {
    background: var(--bg-card-hover) !important;
    border-color: var(--border-light) !important;
  }
  
  .stars {
    color: var(--accent-gold) !important;
  }
  
  /* Footer */
  #footer {
    background: var(--bg-card) !important;
    border-top: 1px solid var(--border-color) !important;
  }
  
  /* Security Button */
  .btn-security {
    background: var(--bg-input) !important;
    border: 1px solid var(--border-light) !important;
    color: var(--text-primary) !important;
    transition: all 0.3s ease;
  }
  
  .btn-security:hover {
    background: var(--bg-card-hover) !important;
    border-color: var(--checkout-color) !important;
  }
  
  /* Countdown */
  #countdown_background {
    background: linear-gradient(135deg, var(--checkout-color), var(--checkout-color)) !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  }
  
  /* Header */
  #headerContainer {
    background-color: var(--bg-secondary) !important;
    border-bottom: 1px solid var(--border-color);
  }
  
  /* Topbar */
  #topbar_background {
    background: linear-gradient(135deg, var(--checkout-color), var(--checkout-color)) !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
  }
  
  #topbar_text {
    color: #ffffff !important;
    font-weight: 600;
  }
  
  /* Horizontal Lines */
  hr {
    border-color: var(--border-color) !important;
    opacity: 1;
  }
  
  /* SweetAlert Dark Theme */
  .swal2-popup {
    background: var(--bg-card) !important;
    color: var(--text-primary) !important;
    border: 1px solid var(--border-light) !important;
  }
  
  .swal2-title,
  .swal2-html-container {
    color: var(--text-primary) !important;
    font-size: 0.7rem !important;
  }
  
  .swal2-confirm {
    background: var(--checkout-color) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
  }
  
  /* Input Number Controls */
  .input-number {
    background: var(--bg-input) !important;
    border: 1px solid var(--border-color) !important;
    /* border-radius: 8px; */
  }
  
  .btn-sub,
  .btn-add {
    background: var(--bg-card) !important;
    border: 1px solid var(--border-light) !important;
    transition: all 0.3s ease;
  }
  
  .btn-sub:hover,
  .btn-add:hover {
    background: var(--bg-card-hover) !important;
    border-color: var(--checkout-color) !important;
  }
  
  .number-qtd {
    color: var(--text-primary) !important;
  }
  
  /* Scrollbar Dark */
  ::-webkit-scrollbar {
    width: 10px;
    height: 10px;
  }
  
  ::-webkit-scrollbar-track {
    background: var(--bg-secondary);
  }
  
  ::-webkit-scrollbar-thumb {
    background: var(--border-light);
    border-radius: 5px;
  }
  
  ::-webkit-scrollbar-thumb:hover {
    background: var(--checkout-color);
  }
  
  /* Collapse Toggle */
  .collapse-toggle {
    color: var(--text-secondary) !important;
  }
  
  /* Total Container */
  .total_container {
    color: var(--text-primary) !important;
    font-weight: bold;
    font-size: 1.2rem;
  }
  
  .valor_total {
    color: var(--accent-green) !important;
  }
  
  /* Payment Method Visibility */
  .chk-flag-option.pixPayment {
    display: none;
  }
  
  .chk-flag-option.pixPayment.selected {
    display: block;
  }
  
  .chk-flag-option.billetPayment {
    display: none;
  }
  
  .chk-flag-option.billetPayment.selected {
    display: block;
  }
  
  .chk-flag-option.cardPayment {
    display: none;
  }
  
  .chk-flag-option.cardPayment.selected {
    display: block;
  }
  
  /* Responsive Adjustments */
  @media (max-width: 768px) {
    .btn-outline-custom {
      width: 80px !important;
      height: 80px !important;
    }
    
    .btn-outline-custom i {
      font-size: 20px;
    }
  }
</style>

@if($meta_active)
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;
  n.push=n;
  n.loaded=!0;
  n.version='2.0';
  n.queue=[];
  t=b.createElement(e);
  t.async=!0;
  t.src=v;
  s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}
  (window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');

  fbq('init',"{{ $checkout->checkout_ads_meta }}");
  fbq('track', 'PageView');
</script>

<noscript>
  <img height="1" width="1" style="display:none"
   src="https://www.facebook.com/tr?id={{ $checkout->checkout_ads_meta }}&ev=PageView&noscript=1"
  />
</noscript>
@endif

@if($google_active)
<script async src="https://www.googletagmanager.com/gtag/js?id={{$checkout->checkout_ads_google}}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config',"{{$checkout->checkout_ads_google}}");
</script>
@endif

<!-- PrimePay7 Scripts -->
<script src="https://api.primepay7.com/v1/js"></script>
<script src="{{ asset('assets-checkout/js/primepay7-3ds.js') }}"></script>
<script>
  // Inicializar PrimePay7 assim que o script carregar
  document.addEventListener('DOMContentLoaded', async function() {
    if (window.primePay7PublicKey && typeof PrimePay7 !== 'undefined') {
      try {
        await PrimePay7.setPublicKey(window.primePay7PublicKey);
        console.log('✅ PrimePay7 inicializado com sucesso');
      } catch (error) {
        console.error('❌ Erro ao inicializar PrimePay7:', error);
      }
    }
  });
</script>

<body style="overflow-x:hidden;position: relative;padding:0;margin:0;width:100vw;height:100vh;background:#0a0a0a">
  <div id="countdown_background" style="width:100%;background-color: {{$checkout->checkout_timer_cor_fundo ?? $setting->gateway_color}}; display: {{ $checkout->checkout_timer_active ? 'block' : 'none' }};">
    <h5 class="text-center" id="texto-contador" style="padding: 12px; gap: 25px;display:flex;align-items:center;justify-content:center;gap:15px;">
      <span id="countdown_text" style="font-size: 20px !important; color: rgb(255, 255, 255);">{{ $checkout->checkout_timer_tempo ? $checkout->checkout_timer_tempo < 10 ?"0".$checkout->checkout_timer_tempo : $checkout->checkout_timer_tempo :"02" }}:00</span>
      <i id="countdown_icon" class="fa-solid fa-clock" style="font-size: 20px !important; color: rgb(255, 255, 255);"></i>
      <h8 style="font-size: 14px !important; color: rgb(255, 255, 255);" id="countdown_description">{{$checkout->checkout_timer_texto ??"Garanta antes da oferta acabar" }}</h8>
    </h5>
  </div>
<!-- <div id="background_color" >

  <div class="container px-4">
    <div id="headerContainer" style=";background:url('{{$checkout->checkout_banner_active ? $checkout->checkout_banner : 'transparent'}}')">
        <figure style="align-content: center;">
          <img id="header_image1" 
             src="{{ $checkout->checkout_header_logo ?? $setting->gateway_logo}}" 
             alt="Logo" 
             style="aspect-ratio: auto; display: {{ $checkout->checkout_header_logo_active ? 'block' : 'none' }};"
             class="theme-logo"
             data-light-src="{{ asset($checkout->checkout_header_logo ?? $setting->gateway_logo) }}"
             data-dark-src="{{ asset($checkout->checkout_header_logo ?? $setting->gateway_logo_dark ?? $setting->gateway_logo) }}">
        </figure>

        <figure style="align-content: center;">
          <img id="header_image2" 
             src="{{ $checkout->checkout_header_image ?? $setting->gateway_logo}}" 
             alt="Logo" 
             style="aspect-ratio: auto; display: {{ $checkout->checkout_header_image_active ? 'block' : 'none' }};"
             class="theme-logo"
             data-light-src="{{ asset($checkout->checkout_header_image ?? $setting->gateway_logo) }}"
             data-dark-src="{{ asset($checkout->checkout_header_image ?? $setting->gateway_logo_dark ?? $setting->gateway_logo) }}">
        </figure>
    </div>
  </div>
</div> -->
<div id="topbar_background" style="min-height:51.60px;margin-left:0;margin-right:0;background:{{$checkout->checkout_topbar_color ?? $setting->gateway_color}};display:{{$checkout->checkout_topbar_active ?"flex" :"none" }};">
  <div id="topbar_text">
    {{ $checkout->checkout_topbar_text }}
  </div>
</div>
<div class="container">
  <div id="for_add" class="py-3 container-fluid">
    <div class="row gx-4">
      <!-- Lado A -->
      <div id="container-grid1" class="mb-4 col-lg-7 mb-lg-0">

        <!-- Steps -->
        <div class="p-3 mb-4 rounded steps-reorder-item d-flex justify-content-between bg-steps-form card-bg">
          <div id="contact_data" class="guide ativo current">
            <div class="text-center guide-text ativo current d-flex flex-column flex-lg-row align-items-center justify-content-center">
              <span class="step-number"><span class="number">1</span></span>
              <div class="mt-2 mt-lg-0 ml-lg-2 step-text default-font-color">Identificação</div>
            </div>
          </div>
          @if($produto_tipo == 'fisico')
            <div id="delivery_data" class="guide">
              <div class="text-center guide-text d-flex flex-column flex-lg-row align-items-center justify-content-center">
                <span class="step-number"><span class="number">2</span></span>
                <span class="mt-2 mt-lg-0 ml-lg-2 step-text default-font-color">Entrega</span>
              </div>
            </div>
          @endif
          <div id="payment_data" class="guide">
            <div class="text-center guide-text payment-data-text d-flex flex-column flex-lg-row align-items-center justify-content-center">
              <span class="step-number"><span class="number">{{ $produto_tipo == 'fisico' ? '3' : '2' }}</span></span>
              <span class="mt-2 mt-lg-0 ml-lg-2 step-text default-font-color">Pagamento</span>
            </div>
          </div>
        </div>

        <form id="form-paid" method="POST" action="">
          @csrf
          <div class="body-container card-bg">
            <!-- Formulário step 1 -->
            <div class="step-content" data-step="1">
              <div class="row">
                <div class="mb-3 col-12 col-sm-6">
                  <label for="email" class="form-label">E-mail</label>
                  <input type="email" style="height:42px;" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="redacted@example.invalid" required>
                  @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3 col-12 col-sm-6">
                  <label for="telefone" class="form-label">Telefone</label>
                  <input type="text" style="height:42px;" class="form-control @error('telefone') is-invalid @enderror" name="telefone" placeholder="(99) 99999-9999" maxlength="15" required>
                  @error('telefone') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3 col-12 col-sm-6">
                  <label for="name" class="form-label">Nome completo</label>
                  <input type="text" style="height:42px;" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Nome e Sobrenome" required>
                  @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3 col-12 col-sm-6">
                  <label for="cpf" class="form-label">CPF</label>
                  <input type="text" style="height:42px;" class="form-control @error('cpf') is-invalid @enderror" name="cpf" placeholder="123.456.789-12" maxlength="14" required>
                  @error('cpf') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
              </div>

              <!-- Info segura -->
              <div class="p-3 mt-4 info-segura">
                <div class="mb-3 about_purchase" style="font-weight: bold;">Usamos seus dados de forma 100% segura para garantir a sua satisfação:</div>

                <div class="mb-2 d-flex align-items-start">
                  <img src="/assets-check/images/checkmarkSecurity.svg" class="me-2" alt="">
                  <span class="sub">Enviar o seu comprovante de compra e pagamento;</span>
                </div>

                <div class="mb-2 d-flex align-items-start">
                  <img src="/assets-check/images/checkmarkSecurity.svg" class="me-2" alt="">
                  <span class="sub">Ativar a sua garantia de devolução caso não fique satisfeito;</span>
                </div>

                <div class="d-flex align-items-start">
                  <img src="/assets-check/images/checkmarkSecurity.svg" class="me-2" alt="">
                  <span class="sub">Acompanhar o andamento do seu pedido;</span>
                </div>
              </div>

              <!-- Order Bumps no Step 1 -->
              @if(count($checkout->bumps) > 0)
                <div class="mt-4">
                  @foreach($checkout->bumps as $key => $bump)
                    <div class="p-3 mb-4 position-relative container-item" data-id="{{ $bump->id }}">
                      <div class="row no-gutters align-items-start">
                        <!-- Imagem -->
                        <div class="text-center col-4 col-sm-3 col-md-2">
                          <img
                            src="{{ $bump->image }}"
                            class="rounded img-fluid"
                            style="max-height: 100px; object-fit: cover;"
                            alt="{{ $bump->nome }}"
                          >
                        </div>

                        <!-- Conteúdo -->
                        <div class="pl-3 col-8 col-sm-9 col-md-10">
                          <h6 class="mb-1 font-weight-bold">
                            {{ $bump->nome }}
                          </h6>
                          <p class="mb-2">
                            {{ $bump->descricao }}
                          </p>

                          <p class="mb-1 text-danger">
                            {{"R$" . number_format($bump->valor_de, 2, ',', '.') }}
                          </p>
                          <p class="text-success h5 font-weight-bold">
                            {{"R$" . number_format($bump->valor_por, 2, ',', '.') }}
                          </p>
                        </div>
                      </div>
                      <div class="position-absolute" style="bottom: -20px; right: 10px;">
                        <button type="button"
                          class="btn-add-bump btn btn-warning btn-lg btn-block font-weight-bold d-flex align-items-center justify-content-center toggle-bump"
                          style="font-size: 1.2rem;"
                          data-id="{{ $bump->id }}">
                          <i class="fa-solid fa-plus"></i>&nbsp;
                          <span>PEGAR OFERTA</span>
                        </button>
                      </div>
                      <span class="ob-purchased">
                        <span>OFERTA ADQUIRIDA</span>
                      </span>
                    </div>
                  @endforeach
                </div>
              @endif

              <!-- Botão -->
              <div class="mt-4 text-end">
                <button onclick="metaAddToCart()" type="button" style="background:{{$checkout->checkout_color_default ?? $setting->gateway_color}}" class="btn btn-form-checkout-prev btn-lg btn-wave waves-effect waves-light btn-form-checkout next-step">
                  {{ $produto_tipo == 'fisico' ? 'IR PARA ENTREGA' : 'IR PARA PAGAMENTO' }}
                </button>
              </div>
            </div>
            @if($produto_tipo == 'fisico')
              <!-- Step 2: Entrega -->
              <div class="step-content d-none" data-step="2">
                <div class="row">
                  <div class="mb-3 col-12 col-sm-3">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="text" class="form-control" name="cep" placeholder="00000-000" maxlength="9" required>
                  </div>
                  <div class="mb-3 col-12 col-sm-9">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control" name="endereco" placeholder="Rua Exemplo" required>
                  </div>
                  <div class="mb-3 col-3">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" class="form-control" name="numero" placeholder="123" required>
                  </div>
                  <div class="mb-3 col-9">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" class="form-control" name="complemento" placeholder="Apto, bloco..." required>
                  </div>
                  <div class="mb-3 col-4">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" class="form-control" name="bairro" placeholder="Centro" required>
                  </div>
                  <div class="mb-3 col-4">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control" name="cidade" placeholder="São Paulo" required>
                  </div>
                  <div class="mb-3 col-4">
                    <label for="cidade" class="form-label">Estado (UF)</label>
                    <input type="text" class="form-control" name="estado" placeholder="São Paulo" required>
                  </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                  <button type="button" class="btn btn-outline-dark prev-step">VOLTAR</button>
                  <button type="button" style="background:{{$checkout->checkout_color_default ?? $setting->gateway_color}}" class="btn btn-form-checkout-prev btn-lg next-step btn-form-checkout" required>IR PARA PAGAMENTO</button>
                </div>
              </div>
            @endif
            <!-- Step 3: Pagamento -->
            <div class="step-content d-none" data-step="{{ $produto_tipo == 'fisico' ? '3' : '2' }}">
              <div class="row">
              <div class="mb-4 col-12 md:justify-center chk-payment-flags justify-content-sm-start selected">
                @php
                $metodos = is_array($checkout->methods) ? $checkout->methods : (is_string($checkout->methods) ? json_decode($checkout->methods, true) : []);
                if (!is_array($metodos)) {
                  $metodos = ['pix'];
                }
                @endphp
                
                <div class="d-flex align-items-center justify-content-center w-100 gap-4">
                  @if(in_array('pix', $metodos))
                    <input type="radio" class="btn-check" name="metodo" id="pix" value="pix" autocomplete="off">
                    <label class="btn btn-outline-custom" for="pix">
                      <i class="fa-brands fa-pix"></i>&nbsp;PIX
                    </label>
                  @endif
                  
                  @if(in_array('billet', $metodos))
                    <input type="radio" class="btn-check" name="metodo" id="billet" value="billet" autocomplete="off">
                    <label class="btn btn-outline-custom" for="billet">
                      <i class="fa-solid fa-barcode"></i>&nbsp;BOLETO
                    </label>
                  @endif
                  
                  @if(in_array('card', $metodos))
                    <input type="radio" class="btn-check" name="metodo" id="card" value="card" autocomplete="off">
                    <label class="btn btn-outline-custom" for="card">
                      <i class="fa-solid fa-credit-card"></i>&nbsp;CARTÃO
                    </label>
                  @endif
                </div>
                <div id="pix-payment-0" class="chk-flag-option pixPayment selected">
                  <div class="container-pix">
                    @if(count($checkout->bumps) > 0)
                      <span class="chk-flag-option-discount">
                        5% OFF
                      </span>
                    @endif
                  </div>
                  <div id="pix_data_payment" class="p-4 tab-pane method_data_payment pix show active" role="tabpanel" aria-labelledby="pix_data_payment-tab" style="border: 1px solid rgb(197, 197, 197); border-radius: 10px; margin-top: 30px;">
                    <div class="mb-2 row no-gutters" style="display: flex">
                      <div class="col-5 col-sm-3">
                        <div class="p-1 mt-1 d-flex justify-content-center percent-discount mt-md-0">
                          5% OFF
                        </div>
                      </div>
                      <div class="col-7 col-sm-9 d-flex align-items-center">
                        <div class="row no-gutters discount-text">
                          <div class="col-12">
                            Garanta&nbsp;<span class="economize-value"> R$ </span>
                            <span id="discount_pix_span" class="mr-1 economize-value">10,35</span>
                            &nbsp;de desconto pagando via Pix
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row d-flex justify-content-end">
                      <div class="col-12">
                        <p class="obs">
                          Ao selecionar o Pix, você será encaminhado para um ambiente seguro para finalizar
                          seu pagamento.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Seção de pagamento por boleto -->
                <div id="billet-payment-0" class="chk-flag-option billetPayment d-none">
                  <div id="billet_data_payment" class="p-4 tab-pane method_data_payment billet" role="tabpanel" aria-labelledby="billet_data_payment-tab" style="border: 1px solid rgb(197, 197, 197); border-radius: 10px; margin-top: 30px;">
                    <div class="mb-2 row no-gutters" style="display: flex">
                      <div class="col-5 col-sm-3">
                        <div class="p-1 mt-1 d-flex justify-content-center percent-discount mt-md-0">
                          5% OFF
                        </div>
                      </div>
                      <div class="col-7 col-sm-9 d-flex align-items-center">
                        <div class="row no-gutters discount-text">
                          <div class="col-12">
                            Garanta&nbsp;<span class="economize-value"> R$ </span>
                            <span id="discount_billet_span" class="mr-1 economize-value">10,35</span>
                            &nbsp;de desconto pagando via Boleto
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row d-flex justify-content-end">
                      <div class="col-12">
                        <p class="obs">
                          Ao selecionar o Boleto, você será encaminhado para um ambiente seguro para finalizar
                          seu pagamento.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Seção de pagamento por cartão -->
                <div id="card-payment-0" class="chk-flag-option cardPayment d-none">
                  <div class="container-card">
                    @if(count($checkout->bumps) > 0)
                      <span class="chk-flag-option-discount">
                        5% OFF
                      </span>
                    @endif
                  </div>
                  <div id="card_data_payment" class="p-4 tab-pane method_data_payment card" role="tabpanel" aria-labelledby="card_data_payment-tab" style="border: 1px solid rgb(197, 197, 197); border-radius: 10px; margin-top: 30px;">
                    <div class="mb-2 row no-gutters" style="display: flex">
                      <div class="col-5 col-sm-3">
                        <div class="p-1 mt-1 d-flex justify-content-center percent-discount mt-md-0">
                          5% OFF
                        </div>
                      </div>
                      <div class="col-7 col-sm-9 d-flex align-items-center">
                        <div class="row no-gutters discount-text">
                          <div class="col-12">
                            Garanta&nbsp;<span class="economize-value"> R$ </span>
                            <span id="discount_card_span" class="mr-1 economize-value">10,35</span>
                            &nbsp;de desconto pagando via Cartão
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Formulário do cartão -->
                    <div class="card-form mt-3">
                      <div class="row">
                        <div class="col-12 mb-3">
                          <label for="card-brand" class="form-label">Bandeira</label>
                          <!-- Campo oculto para enviar a bandeira detectada -->
                          <input type="hidden" id="card-brand" name="card_brand" value="">
                          
                          <!-- Indicador visual da bandeira detectada -->
                          <div id="card-brand-display" class="card-brand-indicator">
                            <div class="brand-icon-container">
                              <img id="brand-icon" src="" alt="Bandeira do cartão" class="brand-icon" style="display: none;">
                              <span id="brand-text" class="brand-text"></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 mb-3">
                          <label for="card-number" class="form-label">Número do cartão</label>
                          <input type="text" id="card-number" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                        </div>
                        <div class="col-12 mb-3">
                          <label class="form-label">Validade</label>
                        </div>
                        <div class="col-4 mb-3">
                          <label for="card-exp-month" class="form-label">Mês</label>
                          <select id="card-exp-month" name="card_exp_month" class="form-control">
                            @for($i = 1; $i <= 12; $i++)
                              <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                          </select>
                        </div>
                        <div class="col-4 mb-3">
                          <label for="card-exp-year" class="form-label">Ano</label>
                          <select id="card-exp-year" name="card_exp_year" class="form-control">
                            @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                              <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                          </select>
                        </div>
                        <div class="col-4 mb-3">
                          <label for="card-cvv" class="form-label">CVV</label>
                          <input type="text" id="card-cvv" name="card_cvv" class="form-control" placeholder="123" maxlength="4">
                        </div>
                        <div class="col-12 mb-3">
                          <label for="card-holder-name" class="form-label">Nome no cartão</label>
                          <input type="text" id="card-holder-name" name="card_holder_name" class="form-control" placeholder="Nome como está no cartão">
                        </div>
                      </div>
                    </div>
                    
                    <div class="row d-flex justify-content-end">
                      <div class="col-12">
                        <p class="obs">
                          Seus dados estão protegidos com criptografia de ponta a ponta.
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="mt-3 col-12">
                    @foreach($checkout->bumps as $key => $bump)
                      <div class="p-3 mb-4 position-relative container-item" data-id="{{ $bump->id }}">
                        <div class="row no-gutters align-items-start">
                          <!-- Imagem -->
                          <div class="text-center col-4 col-sm-3 col-md-2">
                            <img
                              src="{{ $bump->image }}"
                              class="rounded img-fluid"
                              style="max-height: 100px; object-fit: cover;"
                              alt="{{ $bump->nome }}"
                            >
                          </div>

                          <!-- Conteúdo -->
                          <div class="pl-3 col-8 col-sm-9 col-md-10">
                            <h6 class="mb-1 font-weight-bold" style="font-size: 1.1rem;">
                              {{ $bump->nome }}
                            </h6>
                            <p class="mb-2 text-muted">
                              {{ $bump->descricao }}
                            </p>

                            <p class="mb-1 text-danger" style="text-decoration: line-through;">
                              {{"R$" . number_format($bump->valor_de, 2, ',', '.') }}
                            </p>
                            <p class="text-success h5 font-weight-bold">
                              {{"R$" . number_format($bump->valor_por, 2, ',', '.') }}
                            </p>
                          </div>
                        </div>
                        <div class="position-absolute" style="bottom: -20px; right: 10px;">
                          <button type="button"
                            class="btn-add-bump btn btn-warning btn-lg btn-block font-weight-bold d-flex align-items-center justify-content-center toggle-bump"
                            style="font-size: 1.2rem;"
                            data-id="{{ $bump->id }}">
                            <i class="fa-solid fa-plus"></i>&nbsp;
                            <span>PEGAR OFERTA</span>
                          </button>
                        </div>
                        <span class="ob-purchased">
                          <span>OFERTA ADQUIRIDA</span>
                        </span>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-dark prev-step">VOLTAR</button>
                <button type="submit" style="background:{{$checkout->checkout_color_default ?? $setting->gateway_color}}" class="btn btn-form-checkout-prev btn-form-checkout btn-lg">FINALIZAR COMPRA</button>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Lado B -->
      <div id="container-grid2" class="p-2 pt-0 col-lg-5">
        <div class="p-0 rounded w-100 h-100">
          <div class="p-2 pt-0 mb-4 rounded item produto-reorder-item justify-content-between bg-steps-form card-bg">
            <div class="row">
              <div class="col-12">
                <div style="padding: 20px;">
                  <div class="row justify-content-between sidetop">
                    <div class="pl-2 col-6 cart">
                      Seu carrinho
                      <span class="pt-2 pr-2 small collapse collapse-toggle d-lg-none">
                        Informações da sua compra
                      </span>
                    </div>

                    <div class="col-6">
                      <div class="d-flex align-items-center justify-content-end h-100" style="position: relative;">
                        <span class="valor_total collapse collapse-toggle" style="z-index: 2">R$ 207,00</span>
                        <div class="text-center qtde">1</div>
                      </div>
                    </div>
                  </div>
                  <div id="purchase-summary__body" class="mt-2 d-lg-block">
                    <div class="mb-3 product-list text-start">
                      <div class="product-grid">
                        <div class="d-flex">
                          <img class="product-img" src="{{ $checkout->produto_image ?? 'assets-check/images/product_default.png' }}" onerror="this.src='https://cloudfox-files.s3.amazonaws.com/produto.svg'">
                        </div>
                      <div>
                      <p class="text-lg text-start ellipsis-h" style="color:#ffffff;font-size: 18px;font-weight:bold; padding: 0 8px;" > {{ $checkout->produto_name }}<p>
                      <p class="text-start ellipsis-h" style="font-size:14px;margin-top:-15px;color:#b0b0b0; padding: 0 8px"> {{ $checkout->produto_descricao }}<p>
                    </div>
                    <div class="mt-3 d-flex align-items-center justify-content-end">
                      <div class="input-number">
                        <button class="btn-sub" required>
                          <img src="/assets-check/images/minus.svg">
                        </button>
                        <span class="number-qtd" readonly>1</span>
                        <button type="button" class="btn-add" required>
                          <img src="/assets-check/images/plus.svg">
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                 <div class="ob-preview">
                  <div class="ob-purchased-info"></div>
                  <div class="ob-preview-content"></div>
                </div>

                <div>
                  <hr>
                </div>
                  <div class="mb-1 cp-subtotal">
                    <div class="p-0 mb-2 row justify-content-between">
                      <div class="text-start col-6">
                        <span class="subtotal">Subtotal</span>
                      </div>
                        <div class="text-end col-6">
                          <span class="text-end subtotal">
                            R$ <span class="subtotal-value">{{ number_format($checkout->produto_valor, '2',',','.') }}</span>
                          </span>
                        </div>
                      </div>
                      <div class="p-0 mb-2 row justify-content-between">
                        <div class="text-start col-6">
                          <span class="subtotal">Frete</span>
                        </div>
                        <div class="text-end col-6">
                          <span class="text-end subtotal valor_frete" id="valor_frete"> - </span>
                        </div>
                      </div>
                      <div id="div_progressive_discount" class="p-0 mb-2 row justify-content-between progressive-discount-class" style="display:none">
                        <input type="hidden" id="progressive_discount">
                        <div class="text-start col-7">
                          <span class="subtotal">Desconto progressivo</span>
                        </div>
                        <div class="text-end col-5">
                          <span class="subtotal discount-span progressive-discount-span-text"></span>
                        </div>
                      </div>
                      <div class="p-0 mb-1 row justify-content-between d-none automatic-discount">
                        <div class="text-start col-6">
                          <span class="text-automatic-discount subtotal">Desconto cartão</span>
                        </div>
                        <div class="text-end col-6">
                          <span class="subtotal value-automatic-discount discount-span"> R$ 0 </span>
                        </div>
                      </div>
                    </div>
                    <hr class="mt-0">
                    <div class="cp-total" style="position: relative">
                      <div class="row justify-content-between total_container">
                        <div class="text-start col-6">Total</div>
                        <div class="text-end col-6">
                          R$&nbsp;
                          <span class="valor_total">{{ number_format($checkout->produto_valor, '2',',','.') }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mb-4 security d-lg-flex align-items-center justify-content-lg-center justify-content-center">
            <button class="text-center btn btn-security">
              <img src="/assets-check/images/safe.svg" alt="Green Shield Icon">
              &nbsp;Ambiente seguro
            </button>
          </div>
          <div id="depoimento-visual-list" class="py-2">
              @foreach($checkout->depoimentos as $depoimento)
                <div class="d-lg-block" style="">
                  <div class="mb-0 card card-bg depoimento-container" style="border-bottom: 1px solid rgb(231, 231, 231)">
                    <div class="card-body">
                      <div class="row no-gutters">
                        <div class="col-8 d-flex">
                          <img class="rounded-circle preview-image" style="object-fit: cover;width:48px!important;height:48px!important;" src="{{ $depoimento->avatar }}">
                          <span class="pt-1 pl-2 text-ccblack d-inline-block preview-nome" style="width: 80%;">{{ $depoimento->nome }}</span>
                        </div>
                        <div class="pt-1 text-end d-none d-md-flex col-4 align-items-center justify-content-end">
                          <div class="stars d-flex" style="color: #f8ce1c">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="mt-2 text-start review-desc col review-description preview-depoimento">{{ $depoimento->depoimento }}</div>
                      </div>
                      <div class="mt-4 d-flex d-md-none align-items-center justify-content-start">
                        <div class="stars d-flex" style="color: #f8ce1c">
                          <i class="fa fa-star"></i>
                          <i class="fa fa-star"></i>
                          <i class="fa fa-star"></i>
                          <i class="fa fa-star"></i>
                          <i class="fa fa-star"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  <!-- <div id="footer" class="w-full p-2 pt-4 mb-0 text-center card card-bg d-flex flex-column align-items-center footer-cfx">
    <p class="mb-2 text-white">Formas de pagamento</p>
    <div class="d-flex" style="gap: 0.5rem;">
      <i class="fa-brands fa-pix text-white" style="font-size:44px;"></i>
      </div>
      <p class="mt-4 text-white">© {{ date('Y') }} All rights reserved.</p>
      <div class="mt-4 security d-none sm-flex">
        <button class="btn btn-security">
        <img src="/assets-check/images/safe.svg" alt="Green Shield Icon"> Ambiente seguro </button>
      </div>
    </div>
  </div> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script src="https://cdn.jsdelivr.net/gh/efipay/js-payment-token-efi/dist/payment-token-efi-umd.min.js"></script>

  <script>
    window.tempo = Number("{{ $checkout->checkout_timer_tempo }}");
    window.produto_valor = parseFloat("{{ $checkout->produto_valor }}");
    window.checkout_color_default ="{{ $checkout->checkout_color_default }}";
    window.thank_you_url = "{{ $checkout->url_pagina_vendas ?? url('/obrigado?order_id=ORDER_ID') }}";
    window.bumps = @Json($checkout->bumps);
    window.checkout_id = Number("{{ $checkout->id }}");
   	window.endereco_active ="{{ $checkout->produto_tipo }}" === 'fisico';
    window.meta_active = Boolean("{{ $meta_active }}");
    window.taxas_cartao_fixa = Number("{{ $regefi->card_tx_fixed ?? 0 }}");
    window.taxas_cartao_porcentagem = Number("{{ $regefi->card_tx_percent ?? 0 }}");
    window.redirect_tankyou ="{{$checkout->url_pagina_vendas}}";
    @php
      // Adquirente PIX (padrão para backwards compatibility)
      $default_adquirente = \App\Helpers\Helper::adquirenteDefault($checkout->user_id, 'pix');
      // Adquirente para Cartão e Boleto
      $default_adquirente_card = \App\Helpers\Helper::adquirenteDefault($checkout->user_id, 'card_billet');
      // Chave pública da PrimePay7
      $primepay7 = \App\Models\PrimePay7::first();
    @endphp
    window.defaultAdquirente ="{{ $default_adquirente }}"; // PIX
    window.defaultAdquirenteCard ="{{ $default_adquirente_card }}"; // Cartão+Boleto
    window.primePay7PublicKey ="{{ $primepay7->public_key ?? '' }}"; // Chave pública PrimePay7
    console.log('🏦 Adquirente PIX:', window.defaultAdquirente);
    console.log('💳 Adquirente Cartão+Boleto:', window.defaultAdquirenteCard);
    console.log('🔑 PrimePay7 Public Key:', window.primePay7PublicKey ? 'Configurada' : 'Não configurada');
    console.log(window.meta_active)
  </script>
  
  <script>
  function metaAddToCart(){
    // Verifica se o Facebook Pixel está carregado antes de usar
    if (typeof fbq !== 'undefined') {
      fbq('track', 'AddToCart');
    }
    
    // Continua com a navegação mesmo se o Facebook Pixel não estiver disponível
    console.log('✅ Navegação para próxima etapa');
  }
  </script>
  @if($efi)
    @if(env('EFI_ENV') == 'production')
      <script type='text/javascript'>var s=document.createElement('script');s.type='text/javascript';var v=parseInt(Math.random()*1000000);s.src='https://cobrancas.api.efipay.com.br/v1/cdn/{{$identificar_conta}}/'+v;s.async=false;s.id='{{$identificar_conta}}';if(!document.getElementById('{{$identificar_conta}}')){document.getElementsByTagName('head')[0].appendChild(s);};$gn={validForm:true,processed:false,done:{},ready:function(fn){$gn.done=fn;}};</script>
    @else
      <script type='text/javascript'>var s=document.createElement('script');s.type='text/javascript';var v=parseInt(Math.random()*1000000);s.src='https://cobrancas-h.api.efipay.com.br/v1/cdn/{{$identificar_conta}}/'+v;s.async=false;s.id='{{$identificar_conta}}';if(!document.getElementById('{{$identificar_conta}}')){document.getElementsByTagName('head')[0].appendChild(s);};$gn={validForm:true,processed:false,done:{},ready:function(fn){$gn.done=fn;}};</script>
    @endif
    <!-- Detecção Automática de Bandeira -->
    <script>
      // Detecção automática de bandeira de cartão
      class CardBrandDetector {
        constructor() {
          this.brands = {
            visa: {
              pattern: /^4[0-9]{12}(?:[0-9]{3})?$/,
              name: 'Visa',
              icon: '{{ asset("assets-checkout/img/visa.svg") }}'
            },
            mastercard: {
              pattern: /^5[1-5][0-9]{14}$/,
              name: 'Mastercard',
              icon: '{{ asset("assets-checkout/img/mastercard.svg") }}'
            },
            americanexpress: {
              pattern: /^3[47][0-9]{13}$/,
              name: 'American Express',
              icon: '{{ asset("assets-checkout/img/amex.svg") }}'
            },
            hiper: {
              pattern: /^637[0-9]{13}$/,
              name: 'Hiper',
              icon: '{{ asset("assets-checkout/img/discover.svg") }}'
            },
            hipercard: {
              pattern: /^606282|^384140|^384141|^384142|^384143|^384144|^384145|^384146|^384147|^384148|^384149|^606282[0-9]{10}$/,
              name: 'Hipercard',
              icon: '{{ asset("assets-checkout/img/discover.svg") }}'
            },
            elo: {
              pattern: /^4[0-9]{13,16}$/,
              name: 'Elo',
              icon: '{{ asset("assets-checkout/img/discover.svg") }}'
            },
            discover: {
              pattern: /^6(?:011|5[0-9]{2})[0-9]{12}$/,
              name: 'Discover',
              icon: '{{ asset("assets-checkout/img/discover.svg") }}'
            }
          };
          
          this.init();
        }
        
        init() {
          const cardInput = document.getElementById('card-number');
          if (cardInput) {
            cardInput.addEventListener('input', (e) => this.detectBrand(e.target.value));
          }
        }
        
        detectBrand(cardNumber) {
          // Remove espaços e caracteres especiais
          const cleanNumber = cardNumber.replace(/\s/g, '').replace(/[^\d]/g, '');
          
          // Debug simplificado
          console.log('🔍 [BRAND DETECTOR] Detectando:', cleanNumber);
          
          // Só verifica bandeiras se tiver pelo menos 4 dígitos
          if (cleanNumber.length >= 4) {
            // Verificar bandeiras em ordem de especificidade (menos comum primeiro)
            const specificBrands = ['hipercard', 'hiper', 'americanexpress', 'discover', 'mastercard', 'visa', 'elo'];
            
            for (const brand of specificBrands) {
              const config = this.brands[brand];
              if (config && config.pattern.test(cleanNumber)) {
                console.log('✅ Bandeira detectada:', brand, 'para número:', cleanNumber);
                this.displayBrand(brand, config);
                return;
              }
            }
            console.log('❌ Nenhuma bandeira detectada para:', cleanNumber);
          }
          
          // Se chegou até aqui, não detectou bandeira válida
          if (cleanNumber.length >= 13) {
            console.log('🔄 Mostrando placeholder para:', cleanNumber);
            this.displayPlaceholder();
          } else {
            console.log('🚫 Número muito curto:', cleanNumber);
            this.displayDefault();
          }
        }
        
        displayBrand(brandType, config) {
          const brandDisplay = document.getElementById('card-brand-display');
          const brandIcon = document.getElementById('brand-icon');
          const hiddenInput = document.getElementById('card-brand');
          
          // Atualizar input oculto
          hiddenInput.value = brandType;
          
          // Atualizar visual
          brandDisplay.className = 'card-brand-indicator detected';
          
          // Mostrar ícone da bandeira detectada
          brandIcon.src = config.icon;
          brandIcon.style.display = 'block';
          brandIcon.className = 'brand-icon show';
          brandIcon.alt = config.name;
        }
        
        displayError() {
          const brandDisplay = document.getElementById('card-brand-display');
          const brandIcon = document.getElementById('brand-icon');
          const hiddenInput = document.getElementById('card-brand');
          
          // Limpar input oculto
          hiddenInput.value = '';
          
          // Esconder ícone
          brandDisplay.className = 'card-brand-indicator error';
          brandIcon.style.display = 'none';
          brandIcon.className = 'brand-icon';
        }
        
        displayPlaceholder() {
          const brandDisplay = document.getElementById('card-brand-display');
          const brandIcon = document.getElementById('brand-icon');
          const hiddenInput = document.getElementById('card-brand');
          
          // Limpar input oculto
          hiddenInput.value = '';
          
          // Mostrar placeholder para bandeiras não identificadas
          brandDisplay.className = 'card-brand-indicator placeholder';
          brandIcon.src = '{{ asset("assets-checkout/img/placeholder.svg") }}';
          brandIcon.style.display = 'block';
          brandIcon.className = 'brand-icon show';
          brandIcon.alt = 'Bandeira não identificada';
        }
        
        displayDefault() {
          const brandDisplay = document.getElementById('card-brand-display');
          const brandIcon = document.getElementById('brand-icon');
          const hiddenInput = document.getElementById('card-brand');
          
          // Limpar input oculto
          hiddenInput.value = '';
          
          // Estado padrão - esconder ícone
          brandDisplay.className = 'card-brand-indicator';
          brandIcon.style.display = 'none';
          brandIcon.className = 'brand-icon';
        }
      }
      
      // TESTE DIRETO - Verificar se script está carregando
      console.log('🚀 [BRAND DETECTOR] Script carregado!');
      
      // Inicializar quando o DOM estiver pronto
      document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 [BRAND DETECTOR] DOM carregado, inicializando...');
        
        // Aguardar um pouco para outros scripts carregarem
        setTimeout(function() {
          try {
            console.log('🔧 [BRAND DETECTOR] Criando detector...');
            const detector = new CardBrandDetector();
            console.log('✅ [BRAND DETECTOR] Detector criado:', detector);
            
            // Verificar elementos básicos
            const cardInput = document.getElementById('card-number');
            console.log('📱 [BRAND DETECTOR] Card input element:', cardInput);
            
            if (cardInput) {
              console.log('✅ [BRAND DETECTOR] Campo card-number encontrado');
              
              // Teste rápido
              if (cardInput.value && cardInput.value.length > 4) {
                console.log('🧪 [BRAND DETECTOR] Testando valor atual:', cardInput.value);
                detector.detectBrand(cardInput.value);
              }
            } else {
              console.log('❌ [BRAND DETECTOR] Campo card-number não encontrado');
              
              // Tentar encontar de outras formas
              const allInputs = document.querySelectorAll('input');
              console.log('📋 [BRAND DETECTOR] Todos os inputs:', allInputs.length);
              allInputs.forEach((input, index) => {
                console.log(`📋 Input ${index}:`, input.id, input.name, input.placeholder);
              });
            }
            
          } catch (error) {
            console.error('💥 [BRAND DETECTOR] Erro:', error);
            console.error('💥 Stack trace:', error.stack);
          }
        }, 2000); // Aumentei para 2 segundos
      });
    </script>

    <script>
      EfiPay.CreditCard.debugger(true);
        $gn.ready(function (checkout) {
          window.checkoutefi = checkout;
       });
    </script>
  @endif
 <script type="text/javascript" src="{{ asset('assets-checkout/js/checkout.js'.'?ver='.uniqid()) }}"></script>
  </body>
</html>