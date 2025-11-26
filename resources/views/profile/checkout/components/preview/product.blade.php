<!-- Componente Produto -->
<div class="checkout-component">
  <div class="product-display">
    <div class="product-image">
      @if($checkout->produto_image)
        <img src="{{ asset($checkout->produto_image) }}" alt="{{ $checkout->produto_name }}">
      @else
        <div style="width: 120px; height: 120px; background: var(--background-color); border-radius: var(--border-radius); display: flex; align-items: center; justify-content: center; color: var(--text-color); opacity: 0.5;">
          <i class="fa-solid fa-image" style="font-size: 2rem;"></i>
        </div>
      @endif
    </div>
    
    <div class="product-info">
      <h2>{{ $checkout->produto_name ?? 'Nome do Produto' }}</h2>
      <p>{{ $checkout->produto_descricao ?? 'Descrição do produto' }}</p>
      
      @if($checkout->descricao_extra)
        <div class="product-extra" style="margin-top: 15px; padding: 15px; background: var(--background-color); border-radius: var(--border-radius);">
          <p style="margin: 0; font-size: 0.9rem;">{{ $checkout->descricao_extra }}</p>
        </div>
      @endif
      
      @if($checkout->produto_categoria)
        <div class="product-category" style="margin-top: 10px;">
          <span style="background: var(--primary-color); color: var(--secondary-color); padding: 4px 12px; border-radius: var(--border-radius); font-size: 0.8rem; font-weight: var(--font-weight-bold);">
            {{ $checkout->produto_categoria }}
          </span>
        </div>
      @endif
    </div>
  </div>
</div>
