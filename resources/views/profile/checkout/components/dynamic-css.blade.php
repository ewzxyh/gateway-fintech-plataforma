@php
// Gerar CSS dinâmico baseado nas configurações do checkout
$cssVariables = [
  '--primary-color' => $checkout->primary_color ?? '#007bff',
  '--secondary-color' => $checkout->secondary_color ?? '#ffffff',
  '--accent-color' => $checkout->accent_color ?? '#ff6b35',
  '--text-color' => $checkout->text_color ?? '#333333',
  '--background-color' => $checkout->background_color ?? '#f5f5f5',
  '--font-family' => $checkout->font_family ?? 'Inter',
  '--font-size-base' => $checkout->font_size_base ?? '16px',
  '--font-weight-normal' => $checkout->font_weight_normal ?? '400',
  '--font-weight-bold' => $checkout->font_weight_bold ?? '600',
  '--border-radius' => $checkout->border_radius ?? '8px',
  '--padding-small' => $checkout->padding_small ?? '8px',
  '--padding-medium' => $checkout->padding_medium ?? '16px',
  '--padding-large' => $checkout->padding_large ?? '24px',
  '--box-shadow' => $checkout->box_shadow ?? '0 2px 8px rgba(0,0,0,0.1)',
  '--border-color' => $checkout->border_color ?? '#e5e5e5',
  '--border-width' => $checkout->border_width ?? '1px',
  '--animation-duration' => $checkout->animation_duration ?? '0.3s',
  '--animation-easing' => $checkout->animation_easing ?? 'ease-in-out',
];

// Configurações de layout
$layoutConfig = $checkout->layout_config ?? [];
$componentOrder = $checkout->component_order ?? [];
$dragDropEnabled = $checkout->drag_drop_enabled ?? false;
@endphp

<style>
/* Sistema de Variáveis CSS Dinâmicas */
:root {
  @foreach($cssVariables as $variable => $value)
    {{ $variable }}: {{ $value }};
  @endforeach
}

/* Reset e Base */
* {
  box-sizing: border-box;
}

body {
  font-family: var(--font-family), -apple-system, BlinkMacSystemFont, sans-serif;
  font-size: var(--font-size-base);
  font-weight: var(--font-weight-normal);
  color: var(--text-color);
  background-color: var(--background-color);
  line-height: 1.6;
  margin: 0;
  padding: 0;
}

/* Layout Principal */
.checkout-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: var(--padding-medium);
  /* background: var(--secondary-color); */
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  transition: all var(--animation-duration) var(--animation-easing);
}

/* Layout Responsivo */
@media (min-width: 768px) {
  .checkout-container {
    @if($checkout->layout_type === 'two-column')
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: var(--padding-large);
    @elseif($checkout->layout_type === 'sidebar')
      display: grid;
      grid-template-columns: @if($checkout->sidebar_position === 'left') {{ $checkout->sidebar_width ?? 300 }}px 1fr @else 1fr {{ $checkout->sidebar_width ?? 300 }}px @endif;
      gap: var(--padding-large);
    @endif
  }
}

/* Componentes Base */
.checkout-component {
  background: var(--secondary-color);
  border: var(--border-width) solid var(--border-color);
  border-radius: var(--border-radius);
  padding: var(--padding-medium);
  margin-bottom: var(--padding-medium);
  transition: all var(--animation-duration) var(--animation-easing);
}

.checkout-component:hover {
  box-shadow: var(--box-shadow);
  transform: translateY(-2px);
}

/* Header */
.component-header {
  text-align: center;
  padding: var(--padding-large) var(--padding-medium);
  background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
  color: var(--secondary-color);
  border-radius: var(--border-radius);
  margin-bottom: var(--padding-large);
}

.component-header h1 {
  font-size: 2.5rem;
  font-weight: var(--font-weight-bold);
  margin: 0 0 var(--padding-small) 0;
}

.component-header p {
  font-size: 1.2rem;
  margin: 0;
  opacity: 0.9;
}

/* Produto */
.component-product {
  display: flex;
  align-items: center;
  gap: var(--padding-medium);
  padding: var(--padding-medium);
  background: var(--secondary-color);
  border-radius: var(--border-radius);
  border: var(--border-width) solid var(--border-color);
}

.component-product img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
}

.component-product h3 {
  font-size: 1.5rem;
  font-weight: var(--font-weight-bold);
  color: var(--text-color);
  margin: 0 0 var(--padding-small) 0;
}

.component-product p {
  color: var(--text-color);
  opacity: 0.8;
  margin: 0;
}

/* Preços */
.component-pricing {
  text-align: center;
  padding: var(--padding-large);
  background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
  color: var(--secondary-color);
  border-radius: var(--border-radius);
  margin: var(--padding-medium) 0;
}

.price-display {
  margin-bottom: var(--padding-small);
}

.price-old {
  font-size: 1.2rem;
  text-decoration: line-through;
  opacity: 0.7;
  margin-right: var(--padding-small);
}

.price-current {
  font-size: 2.5rem;
  font-weight: var(--font-weight-bold);
}

.price-installments {
  font-size: 1.1rem;
  opacity: 0.9;
}

/* Formulário */
.component-form {
  background: var(--secondary-color);
  padding: var(--padding-large);
  border-radius: var(--border-radius);
  border: var(--border-width) solid var(--border-color);
}

.component-form h4 {
  color: var(--text-color);
  font-weight: var(--font-weight-bold);
  margin-bottom: var(--padding-medium);
}

.form-group {
  margin-bottom: var(--padding-medium);
}

.form-group label {
  display: block;
  font-weight: var(--font-weight-bold);
  color: var(--text-color);
  margin-bottom: var(--padding-small);
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: var(--padding-medium);
  border: var(--border-width) solid var(--border-color);
  border-radius: var(--border-radius);
  font-family: var(--font-family);
  font-size: var(--font-size-base);
  transition: all var(--animation-duration) var(--animation-easing);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

/* Botões */
.btn-checkout {
  background: var(--primary-color);
  color: var(--secondary-color);
  border: none;
  padding: var(--padding-medium) var(--padding-large);
  border-radius: var(--border-radius);
  font-family: var(--font-family);
  font-size: var(--font-size-base);
  font-weight: var(--font-weight-bold);
  cursor: pointer;
  transition: all var(--animation-duration) var(--animation-easing);
  width: 100%;
  margin-top: var(--padding-medium);
}

.btn-checkout:hover {
  background: var(--accent-color);
  transform: translateY(-2px);
  box-shadow: var(--box-shadow);
}

.btn-checkout:active {
  transform: translateY(0);
}

/* Depoimentos */
.component-testimonials {
  background: var(--secondary-color);
  padding: var(--padding-large);
  border-radius: var(--border-radius);
  border: var(--border-width) solid var(--border-color);
}

.component-testimonials h4 {
  color: var(--text-color);
  font-weight: var(--font-weight-bold);
  margin-bottom: var(--padding-medium);
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
}

.testimonial-item strong {
  color: var(--text-color);
  font-weight: var(--font-weight-bold);
}

.testimonial-item p {
  color: var(--text-color);
  opacity: 0.8;
  margin: var(--padding-small) 0 0 0;
}

/* Selos de Confiança */
.component-trust-badges {
  display: flex;
  justify-content: center;
  gap: var(--padding-medium);
  flex-wrap: wrap;
  padding: var(--padding-medium);
  background: var(--secondary-color);
  border-radius: var(--border-radius);
  border: var(--border-width) solid var(--border-color);
}

.trust-badge {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--padding-small);
  padding: var(--padding-medium);
  background: var(--background-color);
  border-radius: var(--border-radius);
  min-width: 120px;
  transition: all var(--animation-duration) var(--animation-easing);
}

.trust-badge:hover {
  transform: translateY(-2px);
  box-shadow: var(--box-shadow);
}

.trust-badge i {
  font-size: 2rem;
  color: var(--primary-color);
}

.trust-badge span {
  font-size: 0.9rem;
  font-weight: var(--font-weight-bold);
  color: var(--text-color);
  text-align: center;
}

/* Timer */
.component-timer {
  background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
  color: var(--secondary-color);
  padding: var(--padding-large);
  border-radius: var(--border-radius);
  text-align: center;
  margin: var(--padding-medium) 0;
}

.timer-display {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--padding-small);
}

.timer-text {
  font-size: 1.2rem;
  font-weight: var(--font-weight-bold);
}

.timer-countdown {
  font-size: 2rem;
  font-weight: var(--font-weight-bold);
  font-family: monospace;
}

.timer-countdown span {
  background: rgba(255, 255, 255, 0.2);
  padding: var(--padding-small) var(--padding-medium);
  border-radius: var(--border-radius);
  margin: 0 2px;
}

/* Footer */
.component-footer {
  text-align: center;
  padding: var(--padding-large);
  background: var(--background-color);
  border-radius: var(--border-radius);
  margin-top: var(--padding-large);
}

.component-footer p {
  color: var(--text-color);
  opacity: 0.7;
  margin: 0 0 var(--padding-medium) 0;
}

.footer-links {
  display: flex;
  justify-content: center;
  gap: var(--padding-medium);
  flex-wrap: wrap;
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

/* Sidebar */
.checkout-sidebar {
  background: var(--secondary-color);
  border-radius: var(--border-radius);
  padding: var(--padding-medium);
  border: var(--border-width) solid var(--border-color);
  height: fit-content;
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

/* Responsividade Mobile */
@media (max-width: 767px) {
  .checkout-container {
    padding: var(--padding-small);
    margin: var(--padding-small);
  }
  
  .component-product {
    flex-direction: column;
    text-align: center;
  }
  
  .component-product img {
    width: 100px;
    height: 100px;
  }
  
  .trust-badge {
    min-width: 100px;
  }
  
  .footer-links {
    flex-direction: column;
    gap: var(--padding-small);
  }
  
  .timer-countdown {
    font-size: 1.5rem;
  }
}

/* Estados de Loading */
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

/* Utilitários */
.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }

.mb-0 { margin-bottom: 0; }
.mb-1 { margin-bottom: var(--padding-small); }
.mb-2 { margin-bottom: var(--padding-medium); }
.mb-3 { margin-bottom: var(--padding-large); }

.mt-0 { margin-top: 0; }
.mt-1 { margin-top: var(--padding-small); }
.mt-2 { margin-top: var(--padding-medium); }
.mt-3 { margin-top: var(--padding-large); }

.p-0 { padding: 0; }
.p-1 { padding: var(--padding-small); }
.p-2 { padding: var(--padding-medium); }
.p-3 { padding: var(--padding-large); }

/* Modo Escuro (se habilitado) */
@if(isset($checkout->theme_name) && $checkout->theme_name === 'dark')
:root {
  --primary-color: #343a40;
  --secondary-color: #212529;
  --accent-color: #6c757d;
  --text-color: #ffffff;
  --background-color: #121212;
  --border-color: #495057;
}

.checkout-container {
  background: var(--secondary-color);
  color: var(--text-color);
}

.component-product,
.component-form,
.component-testimonials,
.component-trust-badges,
.checkout-sidebar {
  background: var(--secondary-color);
  border-color: var(--border-color);
}
@endif
</style>
