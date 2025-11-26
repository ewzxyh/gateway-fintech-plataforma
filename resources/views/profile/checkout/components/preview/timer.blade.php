<!-- Componente Timer -->
@if($checkout->checkout_timer_active ?? false)
<div class="timer-section">
  <span class="timer-text">{{ $checkout->checkout_timer_texto ?? 'Oferta termina em:' }}</span>
  <div class="timer-countdown">
    <span class="timer-hours">02</span>:
    <span class="timer-minutes">30</span>:
    <span class="timer-seconds">45</span>
  </div>
</div>
@endif
