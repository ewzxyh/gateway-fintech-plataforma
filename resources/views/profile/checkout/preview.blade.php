<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $checkout->produto_name ?? 'Checkout' }} - Preview</title>
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $checkout->font_family ?? 'Inter') }}:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- CSS Dinâmico -->
  @include('profile.checkout.components.dynamic-css', ['checkout' => $checkout])
  
  <style>
    /* ===== ESTILO VEGA CHECKOUT - MODERNO E PROFISSIONAL ===== */
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      margin: 0;
      padding: 0;
      background: #F9FAFB;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      overflow-x: hidden;
      color: #1F2937;
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
    }
    
    /* Top Bar - Estilo Vega */
    .top-bar {
      background: linear-gradient(135deg, {{ $checkout->checkout_topbar_color ?? '#4F46E5' }}, {{ $checkout->checkout_color_default ?? '#4338CA' }});
      color: {{ $checkout->checkout_topbar_text_color ?? '#FFFFFF' }};
      padding: 16px 0;
      text-align: center;
      font-weight: 700;
      font-size: 14px;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
    }
    
    .top-bar i {
      font-size: 18px;
      animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.8; transform: scale(1.1); }
    }
    
    /* Header com Logo - Estilo Vega */
    .checkout-header {
      background: #FFFFFF;
      padding: 40px 20px;
      text-align: center;
      border-bottom: 1px solid #E5E7EB;
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .checkout-header img {
      max-height: 70px;
      margin-bottom: 24px;
      object-fit: contain;
    }
    
    .checkout-header h1 {
      font-size: 2.5rem;
      font-weight: 800;
      color: #111827;
      margin: 0 0 12px 0;
      letter-spacing: -0.02em;
    }
    
    .checkout-header p {
      font-size: 1.125rem;
      color: #6B7280;
      margin: 0;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    
    /* Container Principal - Layout Moderno */
    .checkout-main {
      max-width: 1200px;
      margin: 0 auto;
      padding: 48px 20px;
    }
    
    /* Grid Layout - Estilo Vega (Conteúdo + Sidebar) */
    .checkout-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 32px;
    }
    
    @media (min-width: 992px) {
      .checkout-grid {
        grid-template-columns: 1fr 400px;
      }
      
      .checkout-sidebar {
        order: 2;
      }
    }
    
    /* Sidebar - Card Flutuante Moderno */
    .checkout-sidebar {
      background: #FFFFFF;
      border-radius: 16px;
      padding: 32px;
      border: 1px solid #E5E7EB;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
      height: fit-content;
      position: sticky;
      top: 100px;
    }
    
    /* Componentes - Cards Modernos */
    .checkout-component {
      background: #FFFFFF;
      border-radius: 16px;
      padding: 32px;
      margin-bottom: 24px;
      border: 1px solid #E5E7EB;
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .checkout-component:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      border-color: {{ $checkout->checkout_color_default ?? '#4F46E5' }};
    }
    
    /* Produto */
    .product-display {
      display: flex;
      align-items: center;
      gap: var(--padding-large);
      margin-bottom: var(--padding-large);
    }
    
    .product-image {
      flex-shrink: 0;
    }
    
    .product-image img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
    }
    
    .product-info h2 {
      font-size: 1.8rem;
      font-weight: var(--font-weight-bold);
      color: var(--text-color);
      margin: 0 0 10px 0;
    }
    
    .product-info p {
      color: var(--text-color);
      opacity: 0.8;
      margin: 0;
      line-height: 1.6;
    }
    
    /* Preços */
    .price-section {
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      color: var(--secondary-color);
      padding: var(--padding-large);
      border-radius: var(--border-radius);
      text-align: center;
      margin-bottom: var(--padding-large);
    }
    
    .price-display {
      margin-bottom: 15px;
    }
    
    .price-old {
      font-size: 1.2rem;
      text-decoration: line-through;
      opacity: 0.7;
      margin-right: 15px;
    }
    
    .price-current {
      font-size: 2.8rem;
      font-weight: var(--font-weight-bold);
    }
    
    .price-installments {
      font-size: 1.1rem;
      opacity: 0.9;
      margin-bottom: 15px;
    }
    
    .discount-badge {
      display: inline-block;
      background: rgba(255, 255, 255, 0.2);
      padding: 8px 16px;
      border-radius: var(--border-radius);
      font-weight: var(--font-weight-bold);
      font-size: 0.9rem;
    }
    
    /* Formulário - Estilo Moderno */
    .form-section {
      background: #FFFFFF;
      padding: 32px;
      border-radius: 16px;
      border: 1px solid #E5E7EB;
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .form-section h3 {
      font-size: 1.875rem;
      font-weight: 700;
      color: #111827;
      margin: 0 0 32px 0;
      text-align: left;
      letter-spacing: -0.02em;
    }
    
    .form-group {
      margin-bottom: 24px;
    }
    
    .form-group label {
      display: block;
      font-weight: 600;
      color: #374151;
      margin-bottom: 8px;
      font-size: 0.875rem;
      letter-spacing: 0.01em;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 14px 16px;
      border: 1px solid #D1D5DB;
      border-radius: 10px;
      font-family: 'Inter', sans-serif;
      font-size: 1rem;
      background: #FFFFFF;
      color: #111827;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .form-group input:hover {
      border-color: #9CA3AF;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: {{ $checkout->checkout_color_default ?? '#4F46E5' }};
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
      background: #FFFFFF;
    }
    
    .form-group input::placeholder {
      color: #9CA3AF;
    }
    
    /* Métodos de Pagamento - Estilo Vega */
    .payment-methods {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 16px;
      margin: 24px 0;
    }
    
    .payment-method {
      position: relative;
    }
    
    .payment-method input[type="radio"] {
      position: absolute;
      opacity: 0;
      pointer-events: none;
    }
    
    .payment-method label {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 24px 16px;
      border: 2px solid #E5E7EB;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      background: #FFFFFF;
      text-align: center;
      height: 120px;
    }
    
    .payment-method label:hover {
      border-color: {{ $checkout->checkout_color_default ?? '#4F46E5' }};
      background: rgba(79, 70, 229, 0.02);
      transform: translateY(-2px);
    }
    
    .payment-method input[type="radio"]:checked + label {
      border-color: {{ $checkout->checkout_color_default ?? '#4F46E5' }};
      background: rgba(79, 70, 229, 0.05);
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    
    .payment-method i {
      font-size: 2rem;
      margin-bottom: 12px;
      color: {{ $checkout->checkout_color_default ?? '#4F46E5' }};
    }
    
    .payment-method span {
      font-weight: 600;
      font-size: 0.875rem;
      color: #374151;
    }
    
    .payment-method input[type="radio"]:checked + label span {
      color: {{ $checkout->checkout_color_default ?? '#4F46E5' }};
    }
    
    /* Botão de Compra - Estilo Vega Moderno */
    .buy-button {
      width: 100%;
      background: linear-gradient(135deg, {{ $checkout->checkout_color_default ?? '#4F46E5' }}, {{ $checkout->checkout_color_default ?? '#4338CA' }});
      color: #FFFFFF;
      border: none;
      padding: 18px 32px;
      border-radius: 12px;
      font-family: 'Inter', sans-serif;
      font-size: 1.125rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      margin-top: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .buy-button:hover {
      transform: translateY(-2px) scale(1.02);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.08);
    }
    
    .buy-button:active {
      transform: translateY(0) scale(0.98);
    }
    
    .buy-button:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
    }
    
    .buy-button i {
      font-size: 1.25rem;
    }
    
    /* Timer */
    .timer-section {
      background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
      color: var(--secondary-color);
      padding: var(--padding-large);
      border-radius: var(--border-radius);
      text-align: center;
      margin-bottom: var(--padding-large);
    }
    
    .timer-text {
      font-size: 1.1rem;
      font-weight: var(--font-weight-bold);
      margin-bottom: 15px;
      display: block;
    }
    
    .timer-countdown {
      font-size: 2.2rem;
      font-weight: var(--font-weight-bold);
      font-family: monospace;
    }
    
    .timer-countdown span {
      background: rgba(255, 255, 255, 0.2);
      padding: 8px 12px;
      border-radius: var(--border-radius);
      margin: 0 2px;
    }
    
    /* Selos de Confiança */
    .trust-section {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: var(--padding-medium);
      margin: var(--padding-large) 0;
    }
    
    .trust-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: var(--padding-medium);
      background: var(--background-color);
      border-radius: var(--border-radius);
      text-align: center;
      transition: all var(--animation-duration) var(--animation-easing);
    }
    
    .trust-item:hover {
      transform: translateY(-2px);
      box-shadow: var(--box-shadow);
    }
    
    .trust-item i {
      font-size: 2.5rem;
      color: var(--primary-color);
      margin-bottom: var(--padding-small);
    }
    
    .trust-item span {
      font-weight: var(--font-weight-bold);
      color: var(--text-color);
      font-size: 0.9rem;
    }
    
    /* Depoimentos */
    .testimonials-section {
      margin: var(--padding-large) 0;
    }
    
    .testimonials-section h3 {
      font-size: 1.5rem;
      font-weight: var(--font-weight-bold);
      color: var(--text-color);
      margin: 0 0 var(--padding-large) 0;
      text-align: center;
    }
    
    .testimonial-item {
      display: flex;
      align-items: center;
      gap: var(--padding-medium);
      padding: var(--padding-medium);
      background: var(--background-color);
      border-radius: var(--border-radius);
      margin-bottom: var(--padding-medium);
    }
    
    .testimonial-item img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      flex-shrink: 0;
    }
    
    .testimonial-item strong {
      color: var(--text-color);
      font-weight: var(--font-weight-bold);
      display: block;
      margin-bottom: 5px;
    }
    
    .testimonial-item p {
      color: var(--text-color);
      opacity: 0.8;
      margin: 0;
      font-style: italic;
    }
    
    /* Footer */
    .checkout-footer {
      background: var(--background-color);
      padding: var(--padding-large);
      border-radius: var(--border-radius);
      margin-top: var(--padding-large);
      text-align: center;
    }
    
    .checkout-footer p {
      color: var(--text-color);
      opacity: 0.7;
      margin: 0 0 var(--padding-medium) 0;
    }
    
    .footer-links {
      display: flex;
      justify-content: center;
      gap: var(--padding-large);
      flex-wrap: wrap;
      margin-bottom: var(--padding-medium);
    }
    
    .footer-links a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: var(--font-weight-bold);
      transition: color var(--animation-duration) var(--animation-easing);
    }
    
    .footer-links a:hover {
      color: var(--accent-color);
    }
    
    .support-info {
      background: var(--secondary-color);
      padding: var(--padding-medium);
      border-radius: var(--border-radius);
      border: var(--border-width) solid var(--border-color);
    }
    
    .support-info h6 {
      color: var(--text-color);
      margin: 0 0 var(--padding-small) 0;
      font-weight: var(--font-weight-bold);
    }
    
    .support-info a {
      color: var(--primary-color);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: var(--padding-small);
      margin-bottom: var(--padding-small);
    }
    
    .support-info a:hover {
      color: var(--accent-color);
    }
    
    /* STEPS VISUAIS - Estilo Vega Checkout */
    .checkout-steps {
      background: #FFFFFF;
      border-radius: 16px;
      padding: 32px;
      margin-bottom: 32px;
      border: 1px solid #E5E7EB;
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .steps-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
    }
    
    .steps-container::before {
      content: '';
      position: absolute;
      top: 20px;
      left: 50px;
      right: 50px;
      height: 2px;
      background: #E5E7EB;
      z-index: 0;
    }
    
    .step-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
      position: relative;
      z-index: 1;
      flex: 1;
    }
    
    .step-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #FFFFFF;
      border: 2px solid #E5E7EB;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: #9CA3AF;
      transition: all 0.3s ease;
    }
    
    .step-item.active .step-circle {
      background: {{ $checkout->checkout_color_default ?? '#4F46E5' }};
      border-color: {{ $checkout->checkout_color_default ?? '#4F46E5' }};
      color: #FFFFFF;
      box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }
    
    .step-item.completed .step-circle {
      background: #10B981;
      border-color: #10B981;
      color: #FFFFFF;
    }
    
    .step-item.completed .step-circle::before {
      content: '\f00c';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
    }
    
    .step-label {
      font-size: 0.875rem;
      font-weight: 600;
      color: #9CA3AF;
      text-align: center;
      transition: color 0.3s ease;
    }
    
    .step-item.active .step-label {
      color: {{ $checkout->checkout_color_default ?? '#4F46E5' }};
    }
    
    .step-item.completed .step-label {
      color: #10B981;
    }
    
    /* Trust Badges - Estilo Moderno */
    .trust-badges {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 32px;
      padding: 24px;
      background: rgba(16, 185, 129, 0.05);
      border-radius: 12px;
      border: 1px solid rgba(16, 185, 129, 0.1);
      margin: 24px 0;
    }
    
    .trust-badge {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #374151;
      font-size: 0.875rem;
      font-weight: 600;
    }
    
    .trust-badge i {
      color: #10B981;
      font-size: 1.25rem;
    }
    
    /* Responsividade */
    @media (max-width: 991px) {
      .checkout-grid {
        grid-template-columns: 1fr;
      }
      
      .checkout-sidebar {
        order: -1;
        position: relative;
        top: 0;
      }
    }
    
    @media (max-width: 767px) {
      .checkout-main {
        padding: 24px 16px;
      }
      
      .checkout-header h1 {
        font-size: 1.875rem;
      }
      
      .checkout-component {
        padding: 24px;
      }
      
      .product-display {
        flex-direction: column;
        text-align: center;
      }
      
      .product-image img {
        width: 100px;
        height: 100px;
      }
      
      .price-current {
        font-size: 2rem;
      }
      
      .payment-methods {
        grid-template-columns: 1fr;
      }
      
      .trust-section {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .footer-links {
        flex-direction: column;
        gap: 12px;
      }
      
      .steps-container {
        flex-direction: column;
        gap: 24px;
      }
      
      .steps-container::before {
        display: none;
      }
      
      .trust-badges {
        flex-direction: column;
        gap: 16px;
      }
    }
    
    /* Animações */
    @if($checkout->animations_enabled ?? true)
    .checkout-component {
      animation: slideInUp 0.6s var(--animation-easing);
    }
    
    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .checkout-component:nth-child(1) { animation-delay: 0.1s; }
    .checkout-component:nth-child(2) { animation-delay: 0.2s; }
    .checkout-component:nth-child(3) { animation-delay: 0.3s; }
    .checkout-component:nth-child(4) { animation-delay: 0.4s; }
    .checkout-component:nth-child(5) { animation-delay: 0.5s; }
    @endif
    
    /* Loading States */
    .loading {
      opacity: 0.6;
      pointer-events: none;
    }
    
    .loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 20px;
      height: 20px;
      margin: -10px 0 0 -10px;
      border: 2px solid var(--primary-color);
      border-top: 2px solid transparent;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    /* Success States */
    .success-message {
      background: #d4edda;
      color: #155724;
      padding: var(--padding-medium);
      border-radius: var(--border-radius);
      margin: var(--padding-medium) 0;
      border: 1px solid #c3e6cb;
    }
    
    .error-message {
      background: #f8d7da;
      color: #721c24;
      padding: var(--padding-medium);
      border-radius: var(--border-radius);
      margin: var(--padding-medium) 0;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>
  @if($checkout->checkout_topbar_active && $checkout->checkout_topbar_text)
    <div class="top-bar">
      <i class="fas fa-clock"></i>
      {{ $checkout->checkout_topbar_text }}
    </div>
  @endif
  
  <!-- Header -->
  <div class="checkout-header">
    @if($checkout->checkout_header_logo_active && $checkout->checkout_header_logo)
      <img src="{{ asset($checkout->checkout_header_logo) }}" alt="Logo">
    @endif
    <h1>{{ $checkout->produto_name ?? 'Nome do Produto' }}</h1>
    <p>{{ $checkout->produto_descricao ?? 'Descrição do produto' }}</p>
  </div>
  
  <!-- Container Principal -->
  <div class="checkout-main">
    <!-- Steps Visuais - Estilo Vega -->
    <div class="checkout-steps">
      <div class="steps-container">
        <div class="step-item active">
          <div class="step-circle">1</div>
          <div class="step-label">Identificação</div>
        </div>
        @if($checkout->produto_tipo === 'fisico')
        <div class="step-item">
          <div class="step-circle">2</div>
          <div class="step-label">Entrega</div>
        </div>
        @endif
        <div class="step-item">
          <div class="step-circle">{{ $checkout->produto_tipo === 'fisico' ? '3' : '2' }}</div>
          <div class="step-label">Pagamento</div>
        </div>
      </div>
    </div>
    
    <!-- Trust Badges -->
    <div class="trust-badges">
      <div class="trust-badge">
        <i class="fas fa-lock"></i>
        <span>Compra Segura</span>
      </div>
      <div class="trust-badge">
        <i class="fas fa-shield-check"></i>
        <span>Dados Protegidos</span>
      </div>
      <div class="trust-badge">
        <i class="fas fa-truck"></i>
        <span>Entrega Garantida</span>
      </div>
    </div>
    
    <div class="checkout-grid">
      <!-- Conteúdo Principal -->
      <div class="checkout-content">
        @if($checkout->component_order && count($checkout->component_order) > 0)
          @foreach($checkout->component_order as $componentType)
            @include('profile.checkout.components.preview.' . $componentType, ['checkout' => $checkout])
          @endforeach
        @else
          <!-- Layout padrão -->
          @include('profile.checkout.components.preview.product', ['checkout' => $checkout])
          @include('profile.checkout.components.preview.pricing', ['checkout' => $checkout])
          @include('profile.checkout.components.preview.form', ['checkout' => $checkout])
          @include('profile.checkout.components.preview.testimonials', ['checkout' => $checkout])
          @include('profile.checkout.components.preview.trust-badges', ['checkout' => $checkout])
        @endif
      </div>
      
      <!-- Sidebar (se habilitada) -->
      @if($checkout->sidebar_enabled)
        <div class="checkout-sidebar">
          @include('profile.checkout.components.preview.sidebar', ['checkout' => $checkout])
        </div>
      @endif
    </div>
  </div>
  
  <!-- Footer -->
  @include('profile.checkout.components.preview.footer', ['checkout' => $checkout])
  
  <!-- Scripts -->
  <script>
    // Configurações dinâmicas
    document.addEventListener('DOMContentLoaded', function() {
      // Aplicar tema
      const root = document.documentElement;
      
      // Cores
      root.style.setProperty('--primary-color', '{{ $checkout->primary_color ??"#007bff" }}');
      root.style.setProperty('--secondary-color', '{{ $checkout->secondary_color ??"#ffffff" }}');
      root.style.setProperty('--accent-color', '{{ $checkout->accent_color ??"#ff6b35" }}');
      root.style.setProperty('--text-color', '{{ $checkout->text_color ??"#333333" }}');
      root.style.setProperty('--background-color', '{{ $checkout->background_color ??"#f5f5f5" }}');
      
      // Tipografia
      root.style.setProperty('--font-family', '{{ $checkout->font_family ??"Inter" }}');
      root.style.setProperty('--font-size-base', '{{ $checkout->font_size_base ??"16px" }}');
      
      // Espaçamento
      root.style.setProperty('--border-radius', '{{ $checkout->border_radius ??"8px" }}');
      root.style.setProperty('--padding-small', '{{ $checkout->padding_small ??"8px" }}');
      root.style.setProperty('--padding-medium', '{{ $checkout->padding_medium ??"16px" }}');
      root.style.setProperty('--padding-large', '{{ $checkout->padding_large ??"24px" }}');
      
      // Inicializar funcionalidades
      initializeFormValidation();
      initializePaymentMethods();
      initializeTimer();
      initializeAnimations();
    });
    
    // Validação de formulário
    function initializeFormValidation() {
      const form = document.querySelector('form');
      if (!form) return;
      
      const inputs = form.querySelectorAll('input[required], select[required]');
      inputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearError);
      });
      
      form.addEventListener('submit', handleFormSubmit);
    }
    
    function validateField(e) {
      const field = e.target;
      const value = field.value.trim();
      
      // Remover erro anterior
      clearError(e);
      
      // Validações específicas
      if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
          showFieldError(field, 'Email inválido');
          return false;
        }
      }
      
      if (field.name === 'cpf' && value) {
        const cpfRegex = /^\d{3}\.\d{3}\.\d{3}-\d{2}$/;
        if (!cpfRegex.test(value)) {
          showFieldError(field, 'CPF inválido');
          return false;
        }
      }
      
      if (field.name === 'telefone' && value) {
        const phoneRegex = /^\(\d{2}\)\s\d{4,5}-\d{4}$/;
        if (!phoneRegex.test(value)) {
          showFieldError(field, 'Telefone inválido');
          return false;
        }
      }
      
      if (field.name === 'cep' && value) {
        const cepRegex = /^\d{5}-\d{3}$/;
        if (!cepRegex.test(value)) {
          showFieldError(field, 'CEP inválido');
          return false;
        }
      }
      
      return true;
    }
    
    function showFieldError(field, message) {
      const errorDiv = document.createElement('div');
      errorDiv.className = 'error-message';
      errorDiv.textContent = message;
      errorDiv.style.fontSize = '0.8rem';
      errorDiv.style.marginTop = '5px';
      
      field.parentNode.appendChild(errorDiv);
      field.style.borderColor = '#dc3545';
    }
    
    function clearError(e) {
      const field = e.target;
      const errorDiv = field.parentNode.querySelector('.error-message');
      if (errorDiv) {
        errorDiv.remove();
      }
      field.style.borderColor = '';
    }
    
    function handleFormSubmit(e) {
      e.preventDefault();
      
      const form = e.target;
      const formData = new FormData(form);
      const data = Object.fromEntries(formData);
      
      // Validar todos os campos
      const inputs = form.querySelectorAll('input[required], select[required]');
      let isValid = true;
      
      inputs.forEach(input => {
        if (!validateField({ target: input })) {
          isValid = false;
        }
      });
      
      if (!isValid) {
        showMessage('Por favor, corrija os erros no formulário', 'error');
        return;
      }
      
      // Simular processamento
      const button = form.querySelector('.buy-button');
      button.disabled = true;
      button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processando...';
      
      setTimeout(() => {
        showMessage('Compra realizada com sucesso! Redirecionando...', 'success');
        
        setTimeout(() => {
          // Simular redirecionamento
          window.location.href = '/obrigado?transaction_id=preview_' + Date.now();
        }, 2000);
      }, 3000);
    }
    
    // Métodos de pagamento
    function initializePaymentMethods() {
      const paymentMethods = document.querySelectorAll('.payment-method input[type="radio"]');
      paymentMethods.forEach(method => {
        method.addEventListener('change', updatePaymentInfo);
      });
    }
    
    function updatePaymentInfo(e) {
      const method = e.target.value;
      const button = document.querySelector('.buy-button');
      
      // Atualizar ícone do botão
      const icon = button.querySelector('i');
      switch(method) {
        case 'pix':
          icon.className = 'fa-solid fa-qrcode';
          break;
        case 'card':
          icon.className = 'fa-solid fa-credit-card';
          break;
        case 'boleto':
          icon.className = 'fa-solid fa-barcode';
          break;
      }
    }
    
    // Timer
    function initializeTimer() {
      @if($checkout->checkout_timer_active ?? false)
      const timerElement = document.querySelector('.timer-countdown');
      if (!timerElement) return;
      
      const hoursElement = timerElement.querySelector('.timer-hours');
      const minutesElement = timerElement.querySelector('.timer-minutes');
      const secondsElement = timerElement.querySelector('.timer-seconds');
      
      let totalSeconds = {{ $checkout->checkout_timer_tempo ?? 2 }} * 60;
      
      function updateTimer() {
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        
        if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, '0');
        if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, '0');
        if (secondsElement) secondsElement.textContent = seconds.toString().padStart(2, '0');
        
        if (totalSeconds <= 0) {
          timerElement.innerHTML = '<span style="color: var(--accent-color); font-weight: bold;">OFERTA EXPIRADA!</span>';
          return;
        }
        
        totalSeconds--;
        setTimeout(updateTimer, 1000);
      }
      
      updateTimer();
      @endif
    }
    
    // Animações
    function initializeAnimations() {
      @if($checkout->animations_enabled ?? true)
      // Animar elementos quando entram na viewport
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      });
      
      document.querySelectorAll('.checkout-component').forEach(component => {
        component.style.opacity = '0';
        component.style.transform = 'translateY(30px)';
        component.style.transition = 'all 0.6s ease';
        observer.observe(component);
      });
      @endif
    }
    
    // Máscaras de input
    function applyMasks() {
      // CPF
      const cpfInput = document.querySelector('input[name="cpf"]');
      if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
          let value = e.target.value.replace(/\D/g, '');
          value = value.replace(/(\d{3})(\d)/, '$1.$2');
          value = value.replace(/(\d{3})(\d)/, '$1.$2');
          value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
          e.target.value = value;
        });
      }
      
      // Telefone
      const phoneInput = document.querySelector('input[name="telefone"]');
      if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
          let value = e.target.value.replace(/\D/g, '');
          value = value.replace(/(\d{2})(\d)/, '($1) $2');
          value = value.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
          e.target.value = value;
        });
      }
      
      // CEP
      const cepInput = document.querySelector('input[name="cep"]');
      if (cepInput) {
        cepInput.addEventListener('input', function(e) {
          let value = e.target.value.replace(/\D/g, '');
          value = value.replace(/(\d{5})(\d)/, '$1-$2');
          e.target.value = value;
        });
      }
    }
    
    // Aplicar máscaras quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', applyMasks);
    
    // Função para mostrar mensagens
    function showMessage(message, type = 'info') {
      const messageDiv = document.createElement('div');
      messageDiv.className = `${type}-message`;
      messageDiv.textContent = message;
      
      const form = document.querySelector('form');
      if (form) {
        form.insertBefore(messageDiv, form.firstChild);
        
        setTimeout(() => {
          messageDiv.remove();
        }, 5000);
      }
    }
    
    // Smooth scroll para elementos
    function smoothScrollTo(element) {
      element.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
    
    // Adicionar efeitos visuais
    function addVisualEffects() {
      // Efeito de hover nos cards
      document.querySelectorAll('.checkout-component').forEach(component => {
        component.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-5px)';
        });
        
        component.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
        });
      });
      
      // Efeito de clique nos botões
      document.querySelectorAll('.buy-button').forEach(button => {
        button.addEventListener('click', function() {
          this.style.transform = 'scale(0.98)';
          setTimeout(() => {
            this.style.transform = 'scale(1)';
          }, 150);
        });
      });
    }
    
    // Inicializar efeitos visuais
    document.addEventListener('DOMContentLoaded', addVisualEffects);
  </script>
</body>
</html>
