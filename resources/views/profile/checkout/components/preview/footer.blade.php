<!-- Componente Footer -->
<div class="checkout-component component-footer">
  <div class="footer-content">
    <p>© {{ date('Y') }} {{ env('APP_NAME') }}. Todos os direitos reservados.</p>
    
    <div class="footer-links">
      <a href="#">Termos de Uso</a>
      <a href="#">Política de Privacidade</a>
      <a href="#">Contato</a>
    </div>
    
    @if($checkout->whatsapp_suporte || $checkout->email_suporte)
      <div class="support-info" style="margin-top: 20px; padding: 15px; background: var(--background-color); border-radius: var(--border-radius);">
        <h6 style="margin: 0 0 10px 0; color: var(--text-color);">Precisa de Ajuda?</h6>
        
        @if($checkout->whatsapp_suporte)
          <div style="margin-bottom: 8px;">
            <i class="fa-brands fa-whatsapp" style="color: #25D366; margin-right: 8px;"></i>
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $checkout->whatsapp_suporte) }}" target="_blank" style="color: var(--primary-color); text-decoration: none;">
              {{ $checkout->whatsapp_suporte }}
            </a>
          </div>
        @endif
        
        @if($checkout->email_suporte)
          <div>
            <i class="fa-solid fa-envelope" style="color: var(--primary-color); margin-right: 8px;"></i>
            <a href="mailto:{{ $checkout->email_suporte }}" style="color: var(--primary-color); text-decoration: none;">
              {{ $checkout->email_suporte }}
            </a>
          </div>
        @endif
      </div>
    @endif
    
    @if($checkout->periodo_garantia)
      <div class="guarantee-info" style="margin-top: 15px; padding: 10px; background: var(--background-color); border-radius: var(--border-radius); text-align: center;">
        <i class="fa-solid fa-shield-check" style="color: var(--primary-color); margin-right: 8px;"></i>
        <span style="font-size: 0.9rem;">Garantia de {{ $checkout->periodo_garantia }}</span>
      </div>
    @endif
  </div>
</div>
