<!-- Componente Header -->
<div class="checkout-component component-header">
  @if($checkout->checkout_header_logo_active && $checkout->checkout_header_logo)
    <img src="{{ asset($checkout->checkout_header_logo) }}" alt="Logo" style="max-height: 80px; margin-bottom: 20px;">
  @endif
  
  <h1>{{ $checkout->produto_name ?? 'Nome do Produto' }}</h1>
  <p>{{ $checkout->produto_descricao ?? 'Descrição do produto' }}</p>
  
  @if($checkout->checkout_header_image_active && $checkout->checkout_header_image)
    <img src="{{ asset($checkout->checkout_header_image) }}" alt="Imagem" style="max-width: 100%; border-radius: var(--border-radius); margin-top: 20px;">
  @endif
</div>
