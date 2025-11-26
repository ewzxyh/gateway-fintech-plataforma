@php
use App\Helpers\Helper;
$setting = Helper::getSetting();
@endphp
<x-guest-layout :route="'Cadastrar-me'">
  {{-- Mantivemos apenas os estilos que são EXCLUSIVOS para o formulário de registro --}}
  <style>
    /* Body com background gradiente */
    body {
      background: linear-gradient(135deg, hsl(var(--background)) 0%, hsl(var(--muted)) 100%);
      position: relative;
    }

    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at 20% 20%, hsl(var(--primary) / 0.05) 0%, transparent 50%),
                  radial-gradient(circle at 80% 80%, hsl(var(--primary) / 0.05) 0%, transparent 50%);
      pointer-events: none;
    }

    /* Container Principal */
    .auth-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
      padding-bottom: 6rem;
      position: relative;
      z-index: 1;
    }

    /* Card Principal */
    .auth-card {
      background-color: hsl(var(--card)) !important;
      border: 1px solid hsl(var(--border)) !important;
      border-radius: calc(var(--radius) * 2) !important;
      box-shadow: 
        0 10px 15px -3px rgb(0 0 0 / 0.1), 
        0 4px 6px -4px rgb(0 0 0 / 0.1),
        0 0 0 1px hsl(var(--border) / 0.3) !important;
      width: 100%;
      max-width: 32rem;
      padding: 2rem !important;
      transition: all 0.3s ease !important;
      position: relative;
      backdrop-filter: blur(10px);
    }
    
    /* Garante borda visível no tema claro */
    :root .auth-card {
      border-color: hsl(214.3 31.8% 85%) !important;
    }
    
    [data-theme="dark"] .auth-card {
      border-color: hsl(var(--border)) !important;
    }

    .auth-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, hsl(var(--primary)) 0%, hsl(var(--primary) / 0.5) 100%);
      border-radius: calc(var(--radius) * 2) calc(var(--radius) * 2) 0 0;
      z-index: 1;
    }

    .auth-card:hover {
      box-shadow: 
        0 20px 25px -5px rgb(0 0 0 / 0.1), 
        0 8px 10px -6px rgb(0 0 0 / 0.1),
        0 0 0 1px hsl(var(--border) / 0.5) !important;
      transform: translateY(-2px) !important;
    }
    
    /* Borda mais visível no hover - tema claro */
    :root .auth-card:hover {
      border-color: hsl(214.3 31.8% 80%) !important;
    }
    
    @media (min-width: 1024px) {
      .auth-card {
        max-width: 34rem !important;
        padding: 2.5rem !important;
      }
    }

    /* Header */
    .auth-header { 
      text-align: center; 
      margin-bottom: 1.5rem;
      overflow: hidden;
    }
    .auth-logo { 
      height: 3rem; 
      width: auto;
      max-width: 100%;
      margin-bottom: 1rem;
      object-fit: contain;
      filter: drop-shadow(0 2px 4px rgb(0 0 0 / 0.1));
    }
    .auth-title { 
      font-size: 1.5rem; 
      font-weight: 700; 
      color: hsl(var(--foreground)); 
      margin-bottom: 0.375rem;
      letter-spacing: -0.025em;
    }
    .auth-subtitle { 
      font-size: 0.875rem; 
      color: hsl(var(--muted-foreground)); 
      font-weight: 400;
    }

    /* Form Groups */
    .form-group { margin-bottom: 0.75rem; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem; }
    .form-row.password-row { margin-bottom: 0.5rem; }

    /* Labels */
    .form-label { display: block; font-size: 0.8125rem; font-weight: 500; color: hsl(var(--foreground)); margin-bottom: 0.375rem; }

    /* Inputs */
    .form-input {
      width: 100%;
      height: 2.5rem;
      padding: 0 0.875rem;
      background-color: hsl(var(--background));
      border: 1.5px solid hsl(var(--input));
      border-radius: var(--radius);
      font-size: 0.875rem;
      color: hsl(var(--foreground));
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .form-input:hover {
      border-color: hsl(var(--border) / 0.8);
    }
    .form-input:focus { 
      outline: none; 
      border-color: hsl(var(--ring)); 
      box-shadow: 0 0 0 3px hsl(var(--ring) / 0.1); 
      background-color: hsl(var(--card));
    }
    .form-input::placeholder { color: hsl(var(--muted-foreground) / 0.7); }

    /* Password Input Container */
    .password-container { position: relative; }
    .password-container .form-input { padding-right: 3rem; }
    .password-toggle {
      position: absolute;
      right: 0.5rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: hsl(var(--muted-foreground));
      cursor: pointer;
      padding: 0.5rem;
      border-radius: var(--radius);
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .password-toggle:hover { 
      color: hsl(var(--foreground)); 
      background-color: hsl(var(--accent)); 
    }
    .password-toggle:active {
      transform: translateY(-50%) scale(0.95);
    }

    /* Checkbox */
    .checkbox-group { 
      display: flex; 
      align-items: center; 
      gap: 0.625rem; 
      margin: 1rem 0 0.75rem 0; 
    }
    .form-checkbox { 
      width: 1.125rem; 
      height: 1.125rem; 
      border: 2px solid hsl(var(--input)); 
      border-radius: calc(var(--radius) / 2); 
      background-color: hsl(var(--background)); 
      cursor: pointer; 
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      appearance: none;
      flex-shrink: 0;
    }
    .form-checkbox:hover {
      border-color: hsl(var(--primary) / 0.5);
    }
    .form-checkbox:checked { 
      background-color: hsl(var(--primary)); 
      border-color: hsl(var(--primary)); 
    }
    .form-checkbox:checked::after {
      content: '✓';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: hsl(var(--primary-foreground));
      font-size: 0.75rem;
      font-weight: bold;
    }
    .checkbox-label { 
      font-size: 0.875rem; 
      color: hsl(var(--muted-foreground)); 
      cursor: pointer;
      user-select: none;
    }

    /* Button */
    .btn-primary {
      width: 100%;
      height: 2.75rem;
      background: linear-gradient(135deg, hsl(var(--primary)) 0%, hsl(var(--primary) / 0.9) 100%);
      color: hsl(var(--primary-foreground));
      border: none;
      border-radius: var(--radius);
      font-size: 0.875rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      margin-bottom: 1rem;
      box-shadow: 0 2px 4px hsl(var(--primary) / 0.2);
      position: relative;
      overflow: hidden;
    }
    .btn-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s ease;
    }
    .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px hsl(var(--primary) / 0.3);
    }
    .btn-primary:hover::before {
      left: 100%;
    }
    .btn-primary:active {
      transform: translateY(0);
      box-shadow: 0 1px 2px hsl(var(--primary) / 0.2);
    }
    .btn-primary:focus { 
      outline: none; 
      box-shadow: 0 0 0 3px hsl(var(--ring) / 0.2), 0 4px 8px hsl(var(--primary) / 0.3);
    }

    /* Links */
    .auth-link { 
      color: hsl(var(--primary)); 
      text-decoration: none; 
      font-size: 0.875rem; 
      font-weight: 500; 
      transition: all 0.2s ease;
      position: relative;
    }
    .auth-link::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background-color: hsl(var(--primary));
      transition: width 0.3s ease;
    }
    .auth-link:hover { 
      color: hsl(var(--primary) / 0.8);
    }
    .auth-link:hover::after {
      width: 100%;
    }

    /* Error Messages */
    .error-message { color: hsl(var(--destructive)); font-size: 0.75rem; margin-top: 0.25rem; }

    /* Password Requirements */
    .password-requirements { 
      margin-top: 0;
      margin-bottom: 0.75rem;
      padding: 0.5rem 0.625rem; 
      background-color: hsl(var(--muted) / 0.5); 
      border-radius: var(--radius); 
      border: 1px solid hsl(var(--border) / 0.5); 
      width: 100%;
    }
    .password-requirements h4 { 
      margin: 0 0 0.25rem 0; 
      font-size: 0.65rem; 
      font-weight: 600; 
      color: hsl(var(--foreground)); 
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }
    .password-requirements ul { 
      margin: 0; 
      padding-left: 0; 
      list-style: none; 
      display: grid; 
      grid-template-columns: repeat(2, 1fr); 
      gap: 0.2rem 0.5rem; 
    }
    .password-requirements li { 
      margin-bottom: 0; 
      display: flex; 
      align-items: center; 
      gap: 0.3rem; 
      font-size: 0.625rem; 
      line-height: 1.2; 
    }
    .password-requirements li::before { 
      content:"✗"; 
      color: hsl(var(--destructive)); 
      font-weight: bold; 
      font-size: 0.65rem;
      width: 0.75rem; 
      height: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .password-requirements li.valid::before { 
      content:"✓"; 
      color: #10b981; 
    }
    .password-requirements li.valid { 
      color: #10b981; 
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .auth-container { padding: 1rem; padding-bottom: 5rem; }
      .auth-card { 
        padding: 1.75rem 1.25rem;
        margin: 0;
        max-width: 100%;
      }
      .auth-header { margin-bottom: 1.25rem; }
      .auth-logo { height: 2.5rem; margin-bottom: 0.875rem; }
      .auth-title { font-size: 1.25rem; }
      .auth-subtitle { font-size: 0.8125rem; }
      
      .form-group { margin-bottom: 0.625rem; }
      .form-row { 
        grid-template-columns: 1fr; 
        gap: 0.625rem; 
        margin-bottom: 0.625rem; 
      }
      .form-row.password-row { margin-bottom: 0.5rem; }
      
      .form-input { height: 2.5rem; font-size: 0.8125rem; }
      .btn-primary { height: 2.5rem; font-size: 0.8125rem; }
      
      .password-requirements { 
        padding: 0.5rem;
        margin-bottom: 0.625rem;
      }
      .password-requirements h4 { font-size: 0.625rem; }
      .password-requirements ul { 
        grid-template-columns: 1fr; 
        gap: 0.2rem; 
      }
      .password-requirements li { font-size: 0.6rem; }
      
      .checkbox-group { margin: 0.875rem 0 0.625rem 0; }
    }
    
    @media (max-width: 480px) {
      .auth-container { padding: 0.75rem 0.5rem; padding-bottom: 4.5rem; }
      .auth-card { padding: 1.5rem 1rem; }
      .auth-header { margin-bottom: 1rem; }
      .auth-logo { height: 2.25rem; }
      .auth-title { font-size: 1.125rem; }
      .auth-subtitle { font-size: 0.75rem; }
      
      .form-input { height: 2.25rem; font-size: 0.75rem; padding: 0 0.75rem; }
      .password-container .form-input { padding-right: 2.5rem; }
      .form-label { font-size: 0.75rem; }
      .btn-primary { height: 2.5rem; font-size: 0.75rem; }
      
      .password-requirements { padding: 0.375rem 0.5rem; }
      .password-requirements h4 { font-size: 0.6rem; margin-bottom: 0.2rem; }
      .password-requirements li { font-size: 0.575rem; gap: 0.25rem; }
    }

    @media (max-width: 380px) {
      .auth-card { padding: 1.25rem 0.875rem; }
      .auth-title { font-size: 1rem; }
    }
  </style>

  <body>
    {{-- O botão de tema e o modal foram REMOVIDOS daqui porque já existem no guest.blade.php --}}

    <div class="auth-container">
      <div class="auth-card">
        <div class="auth-header">
          <img class="auth-logo theme-logo" 
             src="{{ asset($setting->gateway_logo) }}" 
             alt="Logo"
             data-light-src="{{ asset($setting->gateway_logo) }}"
             data-dark-src="{{ asset($setting->gateway_logo_dark ?? $setting->gateway_logo) }}" />
          <h1 class="auth-title">Criar nova conta</h1>
          <p class="auth-subtitle">Preencha os dados para ter acesso à plataforma</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
          @csrf
          <input type="hidden" name="ref" value="{{ request('ref') }}">

          <div class="form-group">
            <label class="form-label" for="name">Nome Completo</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input" placeholder="Digite seu nome completo" required />
            @if ($errors->has('name'))
              <div class="error-message">{{ $errors->first('name') }}</div>
            @endif
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="telefone">Telefone</label>
              <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}" class="form-input" placeholder="(11) 99999-9999" maxlength="15" required />
              @if ($errors->has('telefone'))
                <div class="error-message">{{ $errors->first('telefone') }}</div>
              @endif
            </div>
            <div class="form-group">
              <label class="form-label" for="username">Username</label>
              <input type="text" id="username" name="username" value="{{ old('username') }}" class="form-input" placeholder="Digite seu username" required />
              @if ($errors->has('username'))
                <div class="error-message">{{ $errors->first('username') }}</div>
              @endif
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="Digite seu email" autocomplete="username" required />
            @if ($errors->has('email'))
              <div class="error-message">{{ $errors->first('email') }}</div>
            @endif
          </div>

          <div class="form-row password-row">
            <div class="form-group">
              <label class="form-label" for="password">Senha</label>
              <div class="password-container">
                <input type="password" id="password" name="password" class="form-input" placeholder="Digite sua senha" autocomplete="new-password" required />
                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
              @if ($errors->has('password'))
                <div class="error-message">{{ $errors->first('password') }}</div>
              @endif
            </div>
            <div class="form-group">
              <label class="form-label" for="password_confirmation">Confirmar Senha</label>
              <div class="password-container">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Confirme sua senha" autocomplete="new-password" required />
                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                   <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Requisitos da senha (ocupa toda a largura) -->
          <div class="password-requirements" id="passwordRequirements" style="display: none;">
            <h4>Requisitos da senha:</h4>
            <ul>
              <li id="req-length">Pelo menos 8 caracteres</li>
              <li id="req-lowercase">Uma letra minúscula (a-z)</li>
              <li id="req-uppercase">Uma letra maiúscula (A-Z)</li>
              <li id="req-number">Um número (0-9)</li>
              <li id="req-special">Um caractere especial (@$!%*?&...)</li>
            </ul>
          </div>

          <div class="checkbox-group">
            <input type="checkbox" class="form-checkbox" id="terms" name="terms" value="agree" required>
            <label class="checkbox-label" for="terms">
              Eu concordo com os <a href="#" class="auth-link" onclick="openTermsModal(event)">termos e condições</a>
            </label>
          </div>

          <button type="submit" class="btn-primary">
            Criar Conta
          </button>

          <div style="text-align: center;">
            <a href="{{ route('login') }}" class="auth-link">
              Já tem uma conta? Faça login aqui
            </a>
          </div>
        </form>
      </div>
    </div>

    {{-- As funções de tema e modal foram REMOVIDAS daqui --}}
    <script>
      // Mantivemos apenas os scripts que são EXCLUSIVOS para esta página

      // Password Toggle
      function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        const svg = button.querySelector('svg');
        
        if (input.type === 'password') {
          input.type = 'text';
          svg.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/>`;
        } else {
          input.type = 'password';
          svg.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        }
      }

      // Phone Mask
      document.getElementById('telefone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 11) {
          value = value.replace(/(\d{2})(\d)/, '($1) $2');
          value = value.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
        }
        e.target.value = value;
      });

      // Password validation
      function validatePassword(password) {
        return {
          length: password.length >= 8,
          lowercase: /[a-z]/.test(password),
          uppercase: /[A-Z]/.test(password),
          number: /\d/.test(password),
          special: /[@$!%*?&+#^~`|\\/:";'<>,.=\-_\[\]{}()]/.test(password)
        };
      }

      function updatePasswordRequirements(password) {
        const requirements = validatePassword(password);
        const requirementsDiv = document.getElementById('passwordRequirements');
        
        requirementsDiv.style.display = password.length > 0 ? 'block' : 'none';
        
        Object.keys(requirements).forEach(key => {
          const element = document.getElementById(`req-${key}`);
          if (element) {
            element.classList.toggle('valid', requirements[key]);
          }
        });
      }

      // Add event listener to password input
      document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
          passwordInput.addEventListener('input', (e) => updatePasswordRequirements(e.target.value));
          passwordInput.addEventListener('focus', (e) => updatePasswordRequirements(e.target.value));
        }
      });
    </script>
  </body>
</x-guest-layout>