@php
$setting = \App\Helpers\Helper::getSetting();
@endphp

@props(['route'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  @if($route)
  <title>{{env('APP_NAME')}} - {{ $route }}</title>
  @else
  <title>{{env('APP_NAME')}}</title>
  @endif
  
  <link href="[REDACTED_BASIC_AUTH_URL]" rel="stylesheet">
  
  <link rel="icon" type="image/x-icon" href="{{ asset($setting->gateway_favicon) }}">
  
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Script para aplicar tema antes da renderização - evita flash -->
  <script>
    (function() {
      const savedTheme = localStorage.getItem('theme') || 'light';
      const html = document.documentElement;
      
      if (savedTheme === 'dark') {
        html.setAttribute('data-theme', 'dark');
      } else {
        html.removeAttribute('data-theme');
      }
    })();
  </script>

  <style>
    /* CSS Variables - Sistema de Cores */
    

    /* Dark theme */
    [data-theme="dark"] {
      --background: 222.2 84% 4.9%;
      --foreground: 210 40% 98%;
      --card: 222.2 84% 4.9%;
      --card-foreground: 210 40% 98%;
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
      border-color: hsl(var(--border));
    }

    body {
      background-color: hsl(var(--background));
      color: hsl(var(--foreground));
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      transition: background-color 0.2s, color 0.2s;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1rem;
    }

    /* Botão de tema */
    .theme-toggle {
      position: fixed;
      top: 1rem;
      right: 1rem;
      background: hsl(var(--secondary));
      border: 1px solid hsl(var(--border));
      border-radius: 50%;
      width: 3rem;
      height: 3rem;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
      z-index: 1000;
    }

    .theme-toggle:hover {
      background: hsl(var(--accent));
    }

    .theme-toggle svg {
      width: 1.5rem;
      height: 1.5rem;
      color: hsl(var(--foreground));
    }

    /* Footer */
    footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: hsl(var(--secondary));
      border-top: 1px solid hsl(var(--border));
      padding: 1rem;
      transition: background-color 0.2s, border-color 0.2s;
    }

    footer a {
      color: hsl(var(--primary));
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }

    /* Responsivo */
    @media (max-width: 768px) {
      .container {
        padding: 0 0.5rem;
      }
      
      footer {
        padding: 0.5rem;
      }
      
      footer div {
        flex-direction: column;
        gap: 0.5rem;
      }
    }
  </style>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
  <!-- Botão de alternância de tema -->
  <button class="theme-toggle" onclick="toggleTheme()" title="Alternar tema">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="5"/>
      <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
    </svg>
  </button>
  
  <main style="min-height: 100vh; padding-bottom: 100px;">
    @yield('content')
  </main>

  <footer>
    <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; font-size: 0.875rem; color: hsl(var(--muted-foreground));">
      <div style="margin-bottom: 0.5rem;">
        <span>Todos os direitos reservados © </span>
        <a href="{{ env('APP_URL') }}" target="_blank">{{ $setting->gateway_name }}</a>
        <span> {{ date('Y') }}</span>
      </div>
      <div style="display: flex; gap: 1.5rem;">
        <a href="/">Início</a>
        <a href="#" onclick="openTermsModal(event)">Termos</a>
        <a href="#!">Suporte</a>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    function toggleTheme() {
      const html = document.documentElement;
      const currentTheme = html.getAttribute('data-theme');
      
      if (currentTheme === 'dark') {
        html.removeAttribute('data-theme');
        localStorage.setItem('theme', 'light');
      } else {
        html.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
      }
    }

    function openTermsModal(event) {
      event.preventDefault();
      alert('Termos de Serviço e Política de KYC\n\nEsta é uma página de demonstração.');
    }
  </script>
</body>

</html>
