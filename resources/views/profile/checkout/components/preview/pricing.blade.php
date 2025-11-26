<!-- Componente Preços -->
<div class="price-section">
  <div class="price-display">
    @if($checkout->produto_de_valor && $checkout->produto_de_valor > $checkout->produto_valor)
      <span class="price-old">R$ {{ number_format($checkout->produto_de_valor, 2, ',', '.') }}</span>
    @endif
    <span class="price-current">R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}</span>
  </div>
  
  @if($checkout->produto_de_valor && $checkout->produto_de_valor > $checkout->produto_valor)
    @php
      $desconto = (($checkout->produto_de_valor - $checkout->produto_valor) / $checkout->produto_de_valor) * 100;
    @endphp
    <div class="discount-badge">
      {{ number_format($desconto, 0) }}% OFF
    </div>
  @endif
  
  <div class="price-installments">
    @if($checkout->produto_valor >= 50)
      ou 12x de R$ {{ number_format($checkout->produto_valor / 12, 2, ',', '.') }} sem juros
    @else
      Pagamento à vista
    @endif
  </div>
  
  @if($checkout->periodo_garantia)
    <div class="guarantee-info" style="margin-top: 15px; padding: 10px; background: rgba(255, 255, 255, 0.1); border-radius: var(--border-radius);">
      <i class="fa-solid fa-shield-check" style="margin-right: 8px;"></i>
      <span>Garantia de {{ $checkout->periodo_garantia }}</span>
    </div>
  @endif
</div>
