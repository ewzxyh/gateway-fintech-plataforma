@php
use App\Helpers\Helper;
$setting = Helper::getSetting();
@endphp
<x-guest-layout :route="'Login'">
  <link href="[REDACTED_BASIC_AUTH_URL]" rel="stylesheet">
  <style>
    /* CSS Variables - Sistema de Cores Shadcn */
    :root {
      --background: 0 0% 100%;
      --foreground: 222.2 84% 4.9%;
      --card: 0 0% 100%;
      --card-foreground: 222.2 84% 4.9%;
      --popover: 0 0% 100%;
      --popover-foreground: 222.2 84% 4.9%;
      --primary: 222.2 47.4% 11.2%;
      --primary-foreground: 210 40% 98%;
      --secondary: 210 40% 96.1%;
      --secondary-foreground: 222.2 47.4% 11.2%;
      --muted: 210 40% 96.1%;
      --muted-foreground: 215.4 16.3% 46.9%;
      --accent: 210 40% 96.1%;
      --accent-foreground: 222.2 47.4% 11.2%;
      --destructive: 0 84.2% 60.2%;
      --destructive-foreground: 210 40% 98%;
      --border: 214.3 31.8% 91.4%;
      --input: 214.3 31.8% 91.4%;
      --ring: 222.2 84% 4.9%;
      --radius: 0.5rem;
    }

    [data-theme="dark"] {
      --background: 222.2 84% 4.9%;
      --foreground: 210 40% 98%;
      --card: 222.2 84% 4.9%;
      --card-foreground: 210 40% 98%;
      --popover: 222.2 84% 4.9%;
      --popover-foreground: 210 40% 98%;
      --primary: 210 40% 98%;
      --primary-foreground: 222.2 47.4% 11.2%;
      --secondary: 217.2 32.6% 17.5%;
      --secondary-foreground: 210 40% 98%;
      --muted: 217.2 32.6% 17.5%;
      --muted-foreground: 215 20.2% 65.1%;
      --accent: 217.2 32.6% 17.5%;
      --accent-foreground: 210 40% 98%;
      --destructive: 0 62.8% 30.6%;
      --destructive-foreground: 210 40% 98%;
      --border: 217.2 32.6% 17.5%;
      --input: 217.2 32.6% 17.5%;
      --ring: 212.7 26.8% 83.9%;
    }

    * {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, hsl(var(--background)) 0%, hsl(var(--muted)) 100%);
      color: hsl(var(--foreground));
      min-height: 100vh;
      transition: all 0.3s ease;
      margin: 0;
      padding: 0;
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
      background-color: hsl(var(--card));
      border: 1px solid hsl(var(--border));
      border-radius: calc(var(--radius) * 2);
      box-shadow: 
        0 10px 15px -3px rgb(0 0 0 / 0.1), 
        0 4px 6px -4px rgb(0 0 0 / 0.1),
        0 0 0 1px hsl(var(--border) / 0.3);
      width: 100%;
      max-width: 26rem;
      padding: 2rem;
      transition: all 0.3s ease;
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
    }

    .auth-card:hover {
      box-shadow: 
        0 20px 25px -5px rgb(0 0 0 / 0.1), 
        0 8px 10px -6px rgb(0 0 0 / 0.1),
        0 0 0 1px hsl(var(--border) / 0.5);
      transform: translateY(-2px);
    }
    
    /* Borda mais visível no hover - tema claro */
    :root .auth-card:hover {
      border-color: hsl(214.3 31.8% 80%) !important;
    }

    /* Header */
    .auth-header {
      text-align: center;
      margin-bottom: 1.75rem;
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
    .form-group {
      margin-bottom: 1rem;
    }

    /* Labels */
    .form-label {
      display: block;
      font-size: 0.875rem;
      font-weight: 500;
      color: hsl(var(--foreground));
      margin-bottom: 0.5rem;
    }

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

    .form-input::placeholder {
      color: hsl(var(--muted-foreground) / 0.7);
    }

    /* Password Input Container */
    .password-container {
      position: relative;
    }

    .password-container .form-input {
      padding-right: 3rem;
    }

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
      margin: 1.5rem 0;
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

    /* Buttons */
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
      margin-bottom: 1.25rem;
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
    .error-message {
      color: hsl(var(--destructive));
      font-size: 0.75rem;
      margin-top: 0.25rem;
    }

    /* Alert */
    .alert {
      background: linear-gradient(135deg, hsl(var(--destructive) / 0.08) 0%, hsl(var(--destructive) / 0.12) 100%);
      border: 2px solid hsl(var(--destructive) / 0.25);
      border-left: 4px solid hsl(var(--destructive));
      color: hsl(var(--destructive));
      padding: 0.875rem 1rem;
      border-radius: var(--radius);
      font-size: 0.875rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .alert::before {
      content: '⚠';
      font-size: 1.25rem;
      flex-shrink: 0;
    }

    /* Theme Toggle */
    .theme-toggle {
      position: fixed;
      top: 1.5rem;
      right: 1.5rem;
      width: 2.75rem;
      height: 2.75rem;
      background-color: hsl(var(--card));
      border: 2px solid hsl(var(--border));
      border-radius: calc(var(--radius) * 1.5);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 1000;
      box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
    }

    .theme-toggle:hover {
      background-color: hsl(var(--accent));
      transform: rotate(15deg) scale(1.1);
      box-shadow: 0 4px 12px rgb(0 0 0 / 0.15);
    }

    .theme-toggle:active {
      transform: rotate(15deg) scale(1);
    }

    /* Form Actions */
    .form-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 1.5rem;
    }

    /* Responsive */
    @media (max-width: 640px) {
      .auth-container {
        padding: 1rem;
        padding-bottom: 5rem;
      }

      .auth-card {
        padding: 1.75rem 1.25rem;
        margin: 0;
        max-width: 100%;
      }

      .auth-header {
        margin-bottom: 1.5rem;
      }

      .auth-logo {
        height: 2.5rem;
        margin-bottom: 0.875rem;
      }

      .auth-title {
        font-size: 1.25rem;
      }

      .auth-subtitle {
        font-size: 0.8125rem;
      }

      .form-input {
        height: 2.5rem;
        font-size: 0.8125rem;
      }

      .btn-primary {
        height: 2.5rem;
        font-size: 0.8125rem;
      }

      .form-actions {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
        text-align: center;
      }

      .theme-toggle {
        top: 1rem;
        right: 1rem;
        width: 2.5rem;
        height: 2.5rem;
      }
    }

    @media (max-width: 480px) {
      .auth-container {
        padding: 0.75rem 0.5rem;
        padding-bottom: 4.5rem;
      }

      .auth-card {
        padding: 1.5rem 1rem;
      }
      
      .auth-title {
        font-size: 1.125rem;
      }
      
      .auth-subtitle {
        font-size: 0.75rem;
      }
      
      .form-input {
        height: 2.25rem;
        font-size: 0.75rem;
        padding: 0 0.75rem;
      }
      
      .btn-primary {
        height: 2.5rem;
        font-size: 0.75rem;
      }
    }

    @media (max-width: 380px) {
      .auth-card {
        padding: 1.25rem 0.875rem;
      }
      
      .auth-title {
        font-size: 1rem;
      }
    }
  </style>

  <body>
    <!-- Theme Toggle -->
    <button class="theme-toggle" onclick="toggleTheme()">
      <svg id="theme-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="5"/>
        <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
      </svg>
    </button>

    <div class="auth-container">
      <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
          <img class="auth-logo theme-logo" 
             src="{{ asset($setting->gateway_logo) }}" 
             alt="Logo"
             data-light-src="{{ asset($setting->gateway_logo) }}"
             data-dark-src="{{ asset($setting->gateway_logo_dark ?? $setting->gateway_logo) }}" />
          <h1 class="auth-title">Efetue o login</h1>
          <p class="auth-subtitle">para continuar na plataforma</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}">
          @csrf
          
          <!-- Error Alert -->
          @if (session('error'))
            <div class="alert">{{ session('error') }}</div>
          @endif

          <!-- Login (Username ou Email) -->
          <div class="form-group">
            <label class="form-label" for="login">{{ __('Usuário ou Email') }}</label>
            <input 
              type="text" 
              id="login" 
              name="login" 
              value="{{ old('login') }}" 
              class="form-input" 
              placeholder="Digite seu usuário ou email"
              autocomplete="username"
              required 
            />
            @if ($errors->has('login'))
              <div class="error-message">{{ $errors->first('login') }}</div>
            @endif
          </div>

          <!-- Password -->
          <div class="form-group">
            <label class="form-label" for="password">{{ __('Senha') }}</label>
            <div class="password-container">
              <input 
                type="password" 
                id="password" 
                name="password" 
                class="form-input" 
                placeholder="Digite sua senha"
                autocomplete="current-password"
                required 
              />
              <button type="button" class="password-toggle" onclick="togglePassword('password')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
            @if ($errors->has('password'))
              <div class="error-message">{{ $errors->first('password') }}</div>
            @endif
          </div>

          <!-- Remember Me -->
          <div class="checkbox-group">
            <input type="checkbox" class="form-checkbox" id="remember" name="remember">
            <label class="checkbox-label" for="remember">Lembrar-me</label>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn-primary">
            Acessar
          </button>

          <!-- Form Actions -->
          <div class="form-actions">
            <a href="{{ route('register') }}" class="auth-link">
              Não tem conta? Cadastre-se
            </a>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="auth-link">
                Esqueci minha senha
              </a>
            @endif
          </div>
        </form>
      </div>
    </div>

    <script>
      // Theme Toggle
      function toggleTheme() {
        const body = document.body;
        const themeIcon = document.getElementById('theme-icon');
        
        if (body.getAttribute('data-theme') === 'dark') {
          body.removeAttribute('data-theme');
          themeIcon.innerHTML = `
            <circle cx="12" cy="12" r="5"/>
            <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
          `;
          localStorage.setItem('theme', 'light');
        } else {
          body.setAttribute('data-theme', 'dark');
          themeIcon.innerHTML = `
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
          `;
          localStorage.setItem('theme', 'dark');
        }
        
        // Atualiza todas as logos baseadas no tema
        updateAllThemeLogos();
      }
      
      // Função para atualizar todas as logos baseadas no tema
      function updateAllThemeLogos() {
        const body = document.body;
        const currentTheme = body.getAttribute('data-theme') || 'light';
        
        // Atualiza logos com classe theme-logo
        document.querySelectorAll('.theme-logo').forEach(function(img) {
          const lightSrc = img.getAttribute('data-light-src');
          const darkSrc = img.getAttribute('data-dark-src');
          
          if (currentTheme === 'dark' && darkSrc) {
            img.src = darkSrc;
          } else if (lightSrc) {
            img.src = lightSrc;
          }
        });
      }

      // Password Toggle
      function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        const svg = button.querySelector('svg');
        
        if (input.type === 'password') {
          input.type = 'text';
          svg.innerHTML = `
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
            <path d="M1 1l22 22"/>
          `;
        } else {
          input.type = 'password';
          svg.innerHTML = `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
          `;
        }
      }

      // Load saved theme
      document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        const themeIcon = document.getElementById('theme-icon');
        
        if (savedTheme === 'dark') {
          document.body.setAttribute('data-theme', 'dark');
          themeIcon.innerHTML = `<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>`;
        } else {
          themeIcon.innerHTML = `
            <circle cx="12" cy="12" r="5"/>
            <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
          `;
        }
        
        // Inicializa as logos baseadas no tema atual
        updateAllThemeLogos();
      });
    </script>
  </body>
</x-guest-layout>