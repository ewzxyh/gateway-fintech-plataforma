@php
use App\Helpers\Helper;
$setting = Helper::getSetting();
@endphp
<x-guest-layout :route="'Recuperar Senha'">
  <link href="[REDACTED_BASIC_AUTH_URL]" rel="stylesheet">
  <style>
    /* CSS Variables - Sistema de Cores Shadcn */
    

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
      background-color: hsl(var(--background));
      color: hsl(var(--foreground));
      min-height: 100vh;
      transition: all 0.3s ease;
      margin: 0;
      padding: 0;
    }

    /* Container Principal */
    .auth-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    /* Card Principal */
    .auth-card {
      background-color: hsl(var(--card));
      border: 1px solid hsl(var(--border));
      border-radius: calc(var(--radius) * 2);
      box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
      width: 100%;
      max-width: 24rem;
      padding: 2rem;
      transition: all 0.3s ease;
    }

    /* Header */
    .auth-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .auth-logo {
      height: 3rem;
      margin-bottom: 1rem;
    }

    .auth-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: hsl(var(--foreground));
      margin-bottom: 0.5rem;
    }

    .auth-subtitle {
      font-size: 0.875rem;
      color: hsl(var(--muted-foreground));
      line-height: 1.5;
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
      padding: 0 0.75rem;
      background-color: hsl(var(--background));
      border: 1px solid hsl(var(--input));
      border-radius: var(--radius);
      font-size: 0.875rem;
      color: hsl(var(--foreground));
      transition: all 0.2s ease;
    }

    .form-input:focus {
      outline: none;
      border-color: hsl(var(--ring));
      box-shadow: 0 0 0 2px hsl(var(--ring) / 0.2);
    }

    .form-input::placeholder {
      color: hsl(var(--muted-foreground));
    }

    /* Buttons */
    .btn-primary {
      width: 100%;
      height: 2.5rem;
      background-color: hsl(var(--primary));
      color: hsl(var(--primary-foreground));
      border: none;
      border-radius: var(--radius);
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      margin-bottom: 1rem;
    }

    .btn-primary:hover {
      background-color: hsl(var(--primary) / 0.9);
    }

    .btn-primary:focus {
      outline: none;
      box-shadow: 0 0 0 2px hsl(var(--ring) / 0.2);
    }

    .btn-primary:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    /* Links */
    .auth-link {
      color: hsl(var(--primary));
      text-decoration: none;
      font-size: 0.875rem;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .auth-link:hover {
      text-decoration: underline;
    }

    /* Error Messages */
    .error-message {
      color: hsl(var(--destructive));
      font-size: 0.75rem;
      margin-top: 0.25rem;
    }

    /* Success Alert */
    .alert-success {
      background-color: hsl(142 76% 36% / 0.1);
      border: 1px solid hsl(142 76% 36% / 0.2);
      color: hsl(142 76% 36%);
      padding: 0.75rem 1rem;
      border-radius: var(--radius);
      font-size: 0.875rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    /* Theme Toggle */
    .theme-toggle {
      position: fixed;
      top: 1rem;
      right: 1rem;
      width: 2.5rem;
      height: 2.5rem;
      background-color: hsl(var(--card));
      border: 1px solid hsl(var(--border));
      border-radius: var(--radius);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      z-index: 1000;
    }

    .theme-toggle:hover {
      background-color: hsl(var(--accent));
    }

    /* Loading State */
    .spinner {
      display: inline-block;
      width: 1rem;
      height: 1rem;
      border: 2px solid hsl(var(--muted));
      border-radius: 50%;
      border-top-color: hsl(var(--primary));
      animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Info Box */
    .info-box {
      background-color: hsl(var(--muted));
      border: 1px solid hsl(var(--border));
      border-radius: var(--radius);
      padding: 1rem;
      margin-bottom: 1.5rem;
      font-size: 0.875rem;
      color: hsl(var(--muted-foreground));
      line-height: 1.5;
    }

    /* Responsive */
    @media (max-width: 640px) {
      .auth-card {
        padding: 1.5rem;
        margin: 1rem;
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
          <h1 class="auth-title">Recuperar senha</h1>
          <p class="auth-subtitle">Digite seu email para receber um link de redefinição de senha</p>
        </div>

        <!-- Success Status -->
        @if (session('status'))
          <div class="alert-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 12l2 2 4-4"/>
              <circle cx="12" cy="12" r="10"/>
            </svg>
            {{ session('status') }}
          </div>
        @endif

        <!-- Info Box -->
        <div class="info-box">
          <strong>Esqueceu sua senha?</strong> Sem problemas! Digite seu endereço de email e enviaremos um link para redefinir sua senha.
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}" id="resetForm">
          @csrf

          <!-- Email -->
          <div class="form-group">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              value="{{ old('email') }}" 
              class="form-input" 
              placeholder="Digite seu email"
              autocomplete="username"
              required 
              autofocus
            />
            @if ($errors->has('email'))
              <div class="error-message">{{ $errors->first('email') }}</div>
            @endif
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn-primary" id="resetBtn">
            Enviar Link de Redefinição
          </button>

          <!-- Back to Login -->
          <div style="text-align: center;">
            <a href="{{ route('login') }}" class="auth-link">
              ← Voltar para o login
            </a>
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
      
      // Inicialização quando a página carrega
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

      // Form submission with loading state
      document.getElementById('resetForm').addEventListener('submit', function(e) {
        const resetBtn = document.getElementById('resetBtn');
        resetBtn.innerHTML = '<span class="spinner"></span> Enviando...';
        resetBtn.disabled = true;
      });

      // Load saved theme
      document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme');
        const themeIcon = document.getElementById('theme-icon');
        
        if (savedTheme === 'dark') {
          document.body.setAttribute('data-theme', 'dark');
          themeIcon.innerHTML = `<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>`;
        }
      });
    </script>
  </body>
</x-guest-layout>