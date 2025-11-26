<!-- Componente Depoimentos -->
<div class="testimonials-section">
  <h3>O que nossos clientes dizem</h3>
  
  @if($checkout->depoimentos && count($checkout->depoimentos) > 0)
    @foreach($checkout->depoimentos as $depoimento)
      <div class="testimonial-item">
        <img src="{{ $depoimento->avatar ?? '/assets/images/avatar_default.png' }}" alt="{{ $depoimento->nome }}">
        <div>
          <strong>{{ $depoimento->nome }}</strong>
          <p>"{{ $depoimento->depoimento }}"</p>
        </div>
      </div>
    @endforeach
  @else
    <!-- Depoimentos de exemplo -->
    <div class="testimonial-item">
      <img src="/assets/images/avatar_default.png" alt="Cliente">
      <div>
        <strong>Maria Silva</strong>
        <p>"Produto excelente! Entrega rápida e qualidade surpreendente. Recomendo!"</p>
      </div>
    </div>
    
    <div class="testimonial-item">
      <img src="/assets/images/avatar_default.png" alt="Cliente">
      <div>
        <strong>João Santos</strong>
        <p>"Comprei há 3 meses e estou muito satisfeito. Vale cada centavo investido!"</p>
      </div>
    </div>
    
    <div class="testimonial-item">
      <img src="/assets/images/avatar_default.png" alt="Cliente">
      <div>
        <strong>Ana Costa</strong>
        <p>"Atendimento excepcional e produto de alta qualidade. Superou minhas expectativas!"</p>
      </div>
    </div>
  @endif
</div>
