<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teste de Logos por Tema - EXEMPLO</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background: #f5f5f5;
      transition: background-color 0.3s ease;
    }

    body[data-theme="dark"] {
      background: #1a1a1a;
      color: #ffffff;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      transition: background-color 0.3s ease;
    }

    body[data-theme="dark"] .container {
      background: #2a2a2a;
    }

    .theme-toggle {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    .theme-toggle:hover {
      background: #0056b3;
    }

    .logo-section {
      text-align: center;
      margin: 30px 0;
      padding: 20px;
      border: 2px dashed #ddd;
      border-radius: 10px;
    }

    body[data-theme="dark"] .logo-section {
      border-color: #555;
    }

    .logo-section h3 {
      margin-bottom: 20px;
      color: #333;
    }

    body[data-theme="dark"] .logo-section h3 {
      color: #fff;
    }

    .theme-logo {
      max-width: 200px;
      height: auto;
      margin: 10px;
    }

    .info-box {
      background: #e7f3ff;
      border: 1px solid #b3d9ff;
      padding: 15px;
      border-radius: 5px;
      margin: 20px 0;
    }

    body[data-theme="dark"] .info-box {
      background: #1e3a5f;
      border-color: #3a5f8a;
    }

    .current-theme {
      font-weight: bold;
      color: #007bff;
    }

    body[data-theme="dark"] .current-theme {
      color: #66b3ff;
    }
  </style>
</head>

<body>
  <button class="theme-toggle" onclick="toggleTheme()">
    <span id="themeIcon">🌙</span> Alternar Tema
  </button>

  <div class="container">
    <h1>Teste de Logos por Tema - EXEMPLO</h1>

    <div class="info-box">
      <strong>Status do Tema:</strong> <span class="current-theme" id="themeStatus">Claro</span><br>
      <strong>Logo Atual:</strong> <span id="logoStatus">Tema Claro</span>
    </div>

    <div class="logo-section">
      <h3>Logo Atual (Muda com o tema)</h3>
      <img src="{{ asset($setting->gateway_logo) }}"
        alt="Logo EXEMPLO"
        class="theme-logo"
        data-light-src="{{ asset($setting->gateway_logo) }}"
        data-dark-src="{{ asset($setting->gateway_logo_dark ?? $setting->gateway_logo) }}"
        id="mainLogo">
    </div>

    <div class="logo-section">
      <h3>Logo Tema Claro (Sempre)</h3>
      <img src="{{ asset($setting->gateway_logo) }}"
        alt="Logo Tema Claro"
        class="theme-logo"
        style="border: 2px solid #007bff;">
    </div>

    @if($setting->gateway_logo_dark)
    <div class="logo-section">
      <h3>Logo Tema Escuro (Sempre)</h3>
      <img src="{{ asset($setting->gateway_logo_dark) }}"
        alt="Logo Tema Escuro"
        class="theme-logo"
        style="border: 2px solid #6c757d;">
    </div>
    @else
    <div class="logo-section">
      <h3>Logo Tema Escuro</h3>
      <p style="color: #666;">Nenhuma logo para tema escuro foi configurada ainda.</p>
      <p><a href="/admin/ajustes/gerais" style="color: #007bff;">Configure a logo tema escuro aqui</a></p>
    </div>
    @endif

    <div class="info-box">
      <h4>Como funciona:</h4>
      <ul>
        <li>Clique no botão"Alternar Tema" para mudar entre tema claro e escuro</li>
        <li>A primeira logo muda automaticamente baseada no tema atual</li>
        <li>As outras logos mostram as versões específicas de cada tema</li>
        <li>Se não houver logo tema escuro configurada, usa a logo tema claro como fallback</li>
      </ul>
    </div>
  </div>

  <script>
    function toggleTheme() {
      const body = document.body;
      const themeIcon = document.getElementById('themeIcon');
      const themeStatus = document.getElementById('themeStatus');
      const logoStatus = document.getElementById('logoStatus');

      if (body.getAttribute('data-theme') === 'dark') {
        // Mudando para tema claro
        body.removeAttribute('data-theme');
        themeIcon.textContent = '🌙';
        themeStatus.textContent = 'Claro';
        logoStatus.textContent = 'Tema Claro';
        localStorage.setItem('theme', 'light');
      } else {
        // Mudando para tema escuro
        body.setAttribute('data-theme', 'dark');
        themeIcon.textContent = '☀️';
        themeStatus.textContent = 'Escuro';
        logoStatus.textContent = 'Tema Escuro';
        localStorage.setItem('theme', 'dark');
      }

      // Atualiza todas as logos baseadas no tema
      updateAllThemeLogos();
    }

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

    // Inicializa o tema baseado no localStorage
    document.addEventListener('DOMContentLoaded', function() {
      const savedTheme = localStorage.getItem('theme') || 'light';
      const themeIcon = document.getElementById('themeIcon');
      const themeStatus = document.getElementById('themeStatus');
      const logoStatus = document.getElementById('logoStatus');

      if (savedTheme === 'dark') {
        document.body.setAttribute('data-theme', 'dark');
        themeIcon.textContent = '☀️';
        themeStatus.textContent = 'Escuro';
        logoStatus.textContent = 'Tema Escuro';
      } else {
        themeIcon.textContent = '🌙';
        themeStatus.textContent = 'Claro';
        logoStatus.textContent = 'Tema Claro';
      }

      // Inicializa as logos baseadas no tema atual
      updateAllThemeLogos();
    });
  </script>
</body>

</html>