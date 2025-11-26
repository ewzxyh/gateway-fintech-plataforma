<!-- Componente Sidebar -->
<div class="checkout-component">
  <h4 style="margin-bottom: var(--padding-large); text-align: center; color: var(--text-color);">Resumo do Pedido</h4>
  
  <!-- Produto na Sidebar -->
  <div class="sidebar-product" style="display: flex; align-items: center; gap: var(--padding-medium); margin-bottom: var(--padding-large); padding: var(--padding-medium); background: var(--background-color); border-radius: var(--border-radius);">
    @if($checkout->produto_image)
      <img src="{{ asset($checkout->produto_image) }}" alt="{{ $checkout->produto_name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: var(--border-radius);">
    @else
      <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: var(--border-radius); display: flex; align-items: center; justify-content: center; color: var(--secondary-color);">
        <i class="fa-solid fa-box" style="font-size: 1.5rem;"></i>
      </div>
    @endif
    <div>
      <h5 style="margin: 0 0 5px 0; font-size: 0.9rem; color: var(--text-color);">{{ $checkout->produto_name ?? 'Produto' }}</h5>
      <p style="margin: 0; font-size: 0.8rem; color: var(--text-color); opacity: 0.7;">{{ $checkout->produto_categoria ?? 'Categoria' }}</p>
    </div>
  </div>
  
  <!-- Preço na Sidebar -->
  <div class="sidebar-pricing" style="text-align: center; margin-bottom: var(--padding-large);">
    @if($checkout->produto_de_valor && $checkout->produto_de_valor > $checkout->produto_valor)
      <div style="font-size: 0.9rem; color: var(--text-color); opacity: 0.7; text-decoration: line-through; margin-bottom: 5px;">
        R$ {{ number_format($checkout->produto_de_valor, 2, ',', '.') }}
      </div>
    @endif
    <div style="font-size: 1.8rem; font-weight: var(--font-weight-bold); color: var(--primary-color); margin-bottom: 5px;">
      R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}
    </div>
    @if($checkout->produto_valor >= 50)
      <div style="font-size: 0.8rem; color: var(--text-color); opacity: 0.8;">
        ou 12x de R$ {{ number_format($checkout->produto_valor / 12, 2, ',', '.') }}
      </div>
    @endif
  </div>
  
  <!-- Garantia na Sidebar -->
  @if($checkout->periodo_garantia)
    <div class="sidebar-guarantee" style="background: var(--background-color); padding: var(--padding-medium); border-radius: var(--border-radius); margin-bottom: var(--padding-large); text-align: center;">
      <i class="fa-solid fa-shield-check" style="color: var(--primary-color); font-size: 1.5rem; margin-bottom: var(--padding-small);"></i>
      <div style="font-size: 0.9rem; font-weight: var(--font-weight-bold); color: var(--text-color); margin-bottom: 5px;">
        Garantia Total
      </div>
      <div style="font-size: 0.8rem; color: var(--text-color); opacity: 0.8;">
        {{ $checkout->periodo_garantia }}
      </div>
    </div>
  @endif
  
  <!-- Selos de Confiança na Sidebar -->
  <div class="sidebar-trust" style="margin-bottom: var(--padding-large);">
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--padding-small);">
      <div style="text-align: center; padding: var(--padding-small); background: var(--background-color); border-radius: var(--border-radius);">
        <i class="fa-solid fa-lock" style="color: var(--primary-color); font-size: 1.2rem; margin-bottom: 5px;"></i>
        <div style="font-size: 0.7rem; color: var(--text-color); font-weight: var(--font-weight-bold);">Seguro</div>
      </div>
      <div style="text-align: center; padding: var(--padding-small); background: var(--background-color); border-radius: var(--border-radius);">
        <i class="fa-solid fa-truck" style="color: var(--primary-color); font-size: 1.2rem; margin-bottom: 5px;"></i>
        <div style="font-size: 0.7rem; color: var(--text-color); font-weight: var(--font-weight-bold);">Rápido</div>
      </div>
      <div style="text-align: center; padding: var(--padding-small); background: var(--background-color); border-radius: var(--border-radius);">
        <i class="fa-solid fa-headset" style="color: var(--primary-color); font-size: 1.2rem; margin-bottom: 5px;"></i>
        <div style="font-size: 0.7rem; color: var(--text-color); font-weight: var(--font-weight-bold);">Suporte</div>
      </div>
      <div style="text-align: center; padding: var(--padding-small); background: var(--background-color); border-radius: var(--border-radius);">
        <i class="fa-solid fa-star" style="color: var(--primary-color); font-size: 1.2rem; margin-bottom: 5px;"></i>
        <div style="font-size: 0.7rem; color: var(--text-color); font-weight: var(--font-weight-bold);">Qualidade</div>
      </div>
    </div>
  </div>
  
  <!-- Informações de Suporte na Sidebar -->
  @if($checkout->whatsapp_suporte || $checkout->email_suporte)
    <div class="sidebar-support" style="background: var(--background-color); padding: var(--padding-medium); border-radius: var(--border-radius);">
      <h6 style="margin: 0 0 var(--padding-small) 0; color: var(--text-color); font-size: 0.9rem; text-align: center;">Precisa de Ajuda?</h6>
      
      @if($checkout->whatsapp_suporte)
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $checkout->whatsapp_suporte) }}" target="_blank" style="display: flex; align-items: center; gap: var(--padding-small); margin-bottom: var(--padding-small); color: var(--primary-color); text-decoration: none; font-size: 0.8rem;">
          <i class="fa-brands fa-whatsapp" style="color: #25D366;"></i>
          <span>{{ $checkout->whatsapp_suporte }}</span>
        </a>
      @endif
      
      @if($checkout->email_suporte)
        <a href="mailto:{{ $checkout->email_suporte }}" style="display: flex; align-items: center; gap: var(--padding-small); color: var(--primary-color); text-decoration: none; font-size: 0.8rem;">
          <i class="fa-solid fa-envelope"></i>
          <span>{{ $checkout->email_suporte }}</span>
        </a>
      @endif
    </div>
  @endif
</div>
