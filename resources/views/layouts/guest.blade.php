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
    
    /* Tema Claro (Light Mode) - Padrão */
    :root {
      --background: 0 0% 100%;
      --foreground: 222.2 84% 4.9%;
      --card: 0 0% 100%;
      --card-foreground: 222.2 84% 4.9%;
      --primary: 262.1 83.3% 57.8%;
      --primary-foreground: 210 40% 98%;
      --secondary: 210 40% 96.1%;
      --secondary-foreground: 222.2 47.4% 11.2%;
      --muted: 210 40% 96.1%;
      --muted-foreground: 215.4 16.3% 46.9%;
      --accent: 210 40% 96.1%;
      --accent-foreground: 222.2 47.4% 11.2%;
      --border: 214.3 31.8% 91.4%;
      --input: 214.3 31.8% 91.4%;
      --ring: 262.1 83.3% 57.8%;
      --radius: 0.5rem;
    }

    /* Tema Escuro (Dark Mode) */
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
      --border: 217.2 32.6% 17.5%;
      --input: 217.2 32.6% 17.5%;
      --ring: 212.7 26.8% 83.9%;
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: hsl(var(--background));
      color: hsl(var(--foreground));
      transition: background-color 0.2s, color 0.2s;
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* SweetAlert2 customization */
    .swal2-popup { background: hsl(var(--card)) !important; }
    .swal2-title { color: hsl(var(--foreground)) !important; font-size: 0.7rem !important; font-weight: 600 !important; }

    /* Theme Toggle Button */
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
    .theme-toggle:hover { background-color: hsl(var(--accent)); }
    .theme-toggle svg { color: hsl(var(--foreground)); }

    /* Modal Styles */
    .modal-overlay {
      display: none; /* CORRIGIDO: O modal agora começa escondido */
      position: fixed;
      z-index: 1010;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background-color: hsl(var(--card));
      color: hsl(var(--foreground));
      padding: 2rem;
      border: 1px solid hsl(var(--border));
      border-radius: calc(var(--radius) * 2);
      width: 90%;
      max-width: 600px;
      position: relative;
      box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1), 0 10px 10px -5px rgb(0 0 0 / 0.04);
      max-height: 85vh;
      overflow-y: auto;
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid hsl(var(--border));
      padding-bottom: 1rem;
      margin-bottom: 1rem;
    }

    .modal-header h2 {
      font-size: 1.25rem;
      font-weight: 600;
    }

    .modal-close-btn {
      background: none;
      border: none;
      font-size: 2rem;
      line-height: 1;
      cursor: pointer;
      color: hsl(var(--muted-foreground));
      transition: color 0.2s;
    }

    .modal-close-btn:hover {
      color: hsl(var(--foreground));
    }

    .modal-body h3 {
      font-size: 1rem;
      font-weight: 600;
      margin-top: 1.5rem;
      margin-bottom: 0.5rem;
      color: hsl(var(--primary));
    }

    .modal-body p, .modal-body ul {
      margin-bottom: 1rem;
      line-height: 1.7;
      font-size: 0.875rem;
    }

    .modal-body ul {
      padding-left: 20px;
    }
  </style>
</head>

<body>
  <button class="theme-toggle" onclick="toggleTheme()">
    <svg id="theme-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="5"/>
      <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
    </svg>
  </button>
  
  <main>
    {{ $slot }}
  </main>

  <footer style="position: fixed; bottom: 0; left: 0; right: 0; background: hsl(var(--secondary)); border-top: 1px solid hsl(var(--border)); padding: 1rem; transition: background-color 0.2s, border-color 0.2s; z-index: 1200; pointer-events: auto;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; font-size: 0.875rem; color: hsl(var(--muted-foreground));">
      <div style="margin-bottom: 0.5rem;">
        <span>Todos os direitos reservados © </span>
        <a href="{{ env('APP_URL') }}" target="_blank" style="color: hsl(var(--primary)); text-decoration: none;">{{ $setting->gateway_name }}</a>
        <span> {{ date('Y') }}</span>
      </div>
      <div style="display: flex; gap: 1.5rem;">
        <a href="/" style="color: hsl(var(--primary)); text-decoration: none;">Início</a>
        <a href="#" onclick="openTermsModal(event)" style="color: hsl(var(--primary)); text-decoration: none;">Termos</a>
        <a href="#!" style="color: hsl(var(--primary)); text-decoration: none;">Suporte</a>
      </div>
    </div>
  </footer>

  <div id="terms-modal" class="modal-overlay" onclick="closeTermsModalOnClickOutside(event)">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Termos de Serviço e Política de KYC</h2>
        <button class="modal-close-btn" onclick="closeTermsModal()">&times;</button>
      </div>
      <div class="modal-body">
        <h3>1. Aceitação dos Termos</h3>
        <p>Ao criar uma conta e utilizar nossa plataforma, você concorda em cumprir integralmente estes Termos de Serviço e todas as leis e regulamentos aplicáveis. Se você não concorda com algum destes termos, está proibido de usar ou acessar este site.</p>

        <h3>2. Procedimentos de KYC (Conheça seu Cliente)</h3>
        <p>Para cumprir as regulamentações financeiras e prevenir fraudes e lavagem de dinheiro, exigimos que todos os usuários passem por um processo de verificação de identidade (KYC). Isso pode incluir, mas não se limita a:</p>
        <ul>
          <li>Envio de um documento de identidade válido com foto (RG, CNH ou Passaporte).</li>
          <li>Comprovante de residência recente.</li>
          <li>Uma selfie segurando o documento de identidade.</li>
        </ul>
        <p>As informações fornecidas serão usadas exclusivamente para fins de verificação e mantidas em segurança, de acordo com nossa Política de Privacidade.</p>

        <h3>3. Regras e Limites de Transação</h3>
        <p>As transações estão sujeitas a limites diários, semanais e mensais, que podem variar de acordo com o seu nível de verificação KYC. Reservamo-nos o direito de revisar e/ou reter qualquer transação que seja considerada suspeita ou que viole estes termos, e de solicitar informações adicionais a qualquer momento.</p>
        
        <h3>4. Conduta do Usuário</h3>
        <p>É estritamente proibido o uso da plataforma para atividades ilegais, incluindo, mas não se limitando a, fraude, financiamento de atividades ilícitas e lavagem de dinheiro. Qualquer conta envolvida em tais atividades será imediatamente suspensa e reportada às autoridades competentes.</p>
        
        <p><strong>Atenção:</strong> Este é um texto de exemplo. Substitua-o pelos seus termos legais oficiais.</p>
      </div>
    </div>
  </div>

  <script>
    // Theme Toggle Logic
    function toggleTheme() {
      const body = document.body;
      const themeIcon = document.getElementById('theme-icon');
      if (body.getAttribute('data-theme') === 'dark') {
        body.removeAttribute('data-theme');
        themeIcon.innerHTML = `<circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>`;
        localStorage.setItem('theme', 'light');
      } else {
        body.setAttribute('data-theme', 'dark');
        themeIcon.innerHTML = `<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>`;
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

    document.addEventListener('DOMContentLoaded', function() {
      const savedTheme = localStorage.getItem('theme') || 'light';
      if (savedTheme === 'dark') {
        document.body.setAttribute('data-theme', 'dark');
        document.getElementById('theme-icon').innerHTML = `<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>`;
      } else {
        document.getElementById('theme-icon').innerHTML = `<circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>`;
      }
      
      // Inicializa as logos baseadas no tema atual
      updateAllThemeLogos();
    });

    // Modal Logic
    const termsModal = document.getElementById('terms-modal');

    function openTermsModal(event) {
      event.preventDefault();
      if (termsModal) {
        termsModal.style.display = 'flex';
      }
    }

    function closeTermsModal() {
      if (termsModal) {
        termsModal.style.display = 'none';
      }
    }
    
    function closeTermsModalOnClickOutside(event) {
      if (event.target === termsModal) {
        closeTermsModal();
      }
    }
  </script>
  
  @if (session('success'))
  <script>
  showToast('success',"{{ session('success') }}");
  </script>
  @endif

  @if (session('error'))
  <script>
  showToast('error',"{{ session('error') }}");
  </script>
  @endif

  <script>
  function showToast(type, message) {
    // Mapear tipos para cores e ícones modernos
    const config = {
      success: {
        icon: 'success',
        iconColor: '#10b981',
        background: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
        color: '#ffffff'
      },
      error: {
        icon: 'error',
        iconColor: '#ef4444',
        background: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
        color: '#ffffff'
      },
      danger: {
        icon: 'error',
        iconColor: '#ef4444',
        background: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
        color: '#ffffff'
      },
      warning: {
        icon: 'warning',
        iconColor: '#f59e0b',
        background: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
        color: '#ffffff'
      },
      info: {
        icon: 'info',
        iconColor: '#3b82f6',
        background: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
        color: '#ffffff'
      }
    };

    const typeConfig = config[type] || config.info;

    Swal.fire({
      toast: true,
      icon: typeConfig.icon,
      iconColor: typeConfig.iconColor,
      title: message,
      position: 'top-end',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
      showCloseButton: true,
      background: typeConfig.background,
      color: typeConfig.color,
      customClass: {
        popup: 'modern-toast-popup',
        title: 'modern-toast-title',
        timerProgressBar: 'modern-toast-progress',
        closeButton: 'modern-toast-close'
      },
      didOpen: (toast) => {
        toast.style.borderRadius = '12px';
        toast.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.2)';
        toast.style.backdropFilter = 'blur(10px)';
        
        // Forçar fechamento após timer
        setTimeout(() => {
          Swal.close();
        }, 2000);
        
        // Adicionar evento de clique no botão X
        const closeButton = toast.querySelector('.swal2-close');
        if (closeButton) {
          closeButton.addEventListener('click', () => {
            Swal.close();
          });
        }
        
        // Adicionar evento de clique em qualquer lugar do toast
        toast.addEventListener('click', () => {
          Swal.close();
        });
      }
    });
  }
  </script>
</body>

</html>