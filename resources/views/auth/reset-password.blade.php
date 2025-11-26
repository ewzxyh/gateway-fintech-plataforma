@php
use App\Helpers\Helper;
$setting = Helper::getSetting();
@endphp
<x-guest-layout :route="'Redefinir Senha'">
  <link href="[REDACTED_BASIC_AUTH_URL]" rel="stylesheet">
  <style>
    /* CSS Variables - Sistema de Cores Shadcn */
    
    :root {
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
      height: 5rem;
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

    /* Password Input Container */
    .password-container {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      right: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: hsl(var(--muted-foreground));
      cursor: pointer;
      padding: 0.25rem;
      border-radius: calc(var(--radius) / 2);
      transition: all 0.2s ease;
    }

    .password-toggle:hover {
      color: hsl(var(--foreground));
      background-color: hsl(var(--accent));
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

    /* Alert */
    .alert {
      background-color: hsl(var(--destructive) / 0.1);
      border: 1px solid hsl(var(--destructive) / 0.2);
      color: hsl(var(--destructive));
      padding: 0.75rem 1rem;
      border-radius: var(--radius);
      font-size: 0.875rem;
      margin-bottom: 1.5rem;
    }

    .alert-success {
      background-color: hsl(142 76% 36% / 0.1);
      border: 1px solid hsl(142 76% 36% / 0.2);
      color: hsl(142 76% 36%);
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

    /* Form Actions */
    .form-actions {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 1.5rem;
    }

    /* Responsive */
    @media (max-width: 640px) {
      .auth-card {
        padding: 1.5rem;
        margin: 1rem;
      }

      .form-actions {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
      }

      .btn-primary {
        width: 100%;
        text-align: center;
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
          <h1 class="auth-title">Redefinir Senha</h1>
          <p class="auth-subtitle">Digite sua nova senha</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('password.store') }}">
          @csrf
          
          <!-- Password Reset Token -->
          <input type="hidden" name="token" value="{{ $request->route('token') }}">

          <!-- Success Alert -->
          @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
          @endif

          <!-- Error Alert -->
          @if ($errors->any())
            <div class="alert">
              @foreach ($errors->all() as $error)
                {{ $error }}
              @endforeach
            </div>
          @endif

          <!-- Email -->
          <div class="form-group">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              value="{{ old('email', $request->email) }}" 
              class="form-input" 
              placeholder="Digite seu email"
              autocomplete="username"
              readonly
              required 
            />
          </div>

          <!-- Password -->
          <div class="form-group">
            <label class="form-label" for="password">{{ __('Nova Senha') }}</label>
            <div class="password-container">
              <input 
                type="password" 
                id="password" 
                name="password" 
                class="form-input" 
                placeholder="Digite sua nova senha"
                autocomplete="new-password"
                required 
              />
              <button type="button" class="password-toggle" onclick="togglePassword('password')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="form-group">
            <label class="form-label" for="password_confirmation">{{ __('Confirmar Nova Senha') }}</label>
            <div class="password-container">
              <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                class="form-input" 
                placeholder="Confirme sua nova senha"
                autocomplete="new-password"
                required 
              />
              <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn-primary">
            Redefinir Senha
          </button>

          <!-- Form Actions -->
          <div class="form-actions">
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