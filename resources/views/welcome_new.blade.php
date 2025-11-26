@php
$setting = \App\Helpers\Helper::getSetting();
$color = $setting->gateway_color;
@endphp

@php
  // Função para converter HEX para RGBA
  function hexToRgba($hex, $opacity = 0.5) {
    $hex = str_replace('#', '', $hex);

    if (strlen($hex) == 3) {
      $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
      $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
      $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
    } else {
      $r = hexdec(substr($hex, 0, 2));
      $g = hexdec(substr($hex, 2, 2));
      $b = hexdec(substr($hex, 4, 2));
    }

    return"rgba($r, $g, $b, $opacity)";
  }

  $opacityColor = Str::contains($color, 'rgba')
    ? preg_replace('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*[\d.]+\)/', 'rgba($1, $2, $3, 0.5)', $color)
    : hexToRgba($color, 0.5);
@endphp

<!DOCTYPE html>
<html data-wf-domain="{{ 'www'.str_replace('https://', '' ,env('APP_URL')) }}" data-wf-page="65bc6a7ca6983c7153f2f401" data-wf-site="65bc6a7ca6983c7153f2f401" lang="pt-BR">
  <head>
    <meta charset="utf-8"/>
    <title>{{ $setting->gateway_name }} | Estrutura de Pagamentos para o seu negócio digital</title>
    <meta content="Cadastre seu infoproduto ou SaaS e receba pagamentos em minutos. Oferecemos Checkout 100% customizável e soluções nunca vistas no mercado, como o domínio de checkout customizável, e esse é o nosso objetivo. Trazer tudo que o mercado deixa a desejar." name="description"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="Webflow" name="generator"/>
    <script src="{{ asset('LandingPage/js/jquery.js') }}" type="text/javascript"></script>
    <link href="{{ asset('LandingPage/css/thigasdev.css?v=bKmKpImyj9-7kqwksaEbgiKfz6sc5JX1Jw7lZ4B6gfE') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('LandingPage/js/thigasdev.js?v=pYgLGVXrWXnegH3l0Cb3ABHajXeSHfDlF2ijzjvUUEY') }}"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous"/>
    <link rel="icon" type="image/x-icon" href="{{ asset($setting->gateway_favicon) }}">
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
    <script type="text/javascript">
      WebFont.load({
        google: {
          families: ["Roboto:300,regular,500,700,900","Manrope:200,300,regular,500,600,700,800:cyrillic,cyrillic-ext,greek,latin,latin-ext,vietnamese","Inter:300,400,500,600,700,800,900"]
        }
      });
    </script>
    <script type="text/javascript">
      !function(o, c) {
        var n = c.documentElement
         , t =" w-mod-";
        n.className += t +"js",
        ("ontouchstart"in o || o.DocumentTouch && c instanceof DocumentTouch) && (n.className += t +"touch")
      }(window, document);
    </script>
    <link href="{{ asset('LandingPage/img/MainIcon.svg') }}" rel="shortcut icon" type="image/x-icon"/>
    <link href="{{ asset('LandingPage/img/MainIcon.svg') }}" rel="apple-touch-icon" rel="apple-touch-icon"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet"/>
    <link href="[REDACTED_BASIC_AUTH_URL]" rel="stylesheet">
    <script>
      var source ="https://www.dropbox.com/scl/fi/fsy055uxt7zzlcw862z49/cash-register-kaching-sound-effect-125042.mp3?rlkey=d6vg92r861ykf4ptqxuiwzw8c&dl=1"
      var audio = document.createElement("audio");
      audio.autoplay = true;
      audio.load()
      audio.addEventListener("load", function() {
        audio.play();
      }, true);
      audio.src = source;
    </script>
    <style>
      };
        --color-gateway-opacity: {{ $opacityColor }};
        --blue: {{ $setting->gateway_color }};
        --light-blue: {{ $opacityColor }};
        --black: #000000;
        --white: #ffffff;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-400: #ced4da;
        --gray-500: #adb5bd;
        --gray-600: #6c757d;
        --gray-700: #495057;
        --gray-800: #343a40;
        --gray-900: #212529;
        --primary: #007bff;
        --success: #28a745;
        --info: #17a2b8;
        --warning: #ffc107;
        --danger: #dc3545;
        --light: #f8f9fa;
        --dark: #343a40;
      }

      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.6;
        color: var(--gray-800);
        background-color: var(--white);
      }

      /* Scrollbar */
      ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
      }

      ::-webkit-scrollbar-track {
        background: var(--gray-100);
        border-radius: 4px;
      }

      ::-webkit-scrollbar-thumb {
        background: var(--gray-400);
        border-radius: 4px;
      }

      ::-webkit-scrollbar-thumb:hover {
        background: var(--gray-500);
      }

      /* Header */
      .header {
        background: var(--white);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        padding: 1rem 0;
      }

      .header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .logo {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--color-gateway);
        text-decoration: none;
      }

      .nav {
        display: flex;
        gap: 2rem;
        align-items: center;
      }

      .nav-link {
        color: var(--gray-700);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
      }

      .nav-link:hover {
        color: var(--color-gateway);
      }

      .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-block;
        text-align: center;
      }

      .btn-primary {
        background: var(--color-gateway);
        color: var(--white);
      }

      .btn-primary:hover {
        background: var(--color-gateway-opacity);
        transform: translateY(-2px);
      }

      .btn-outline {
        background: transparent;
        color: var(--color-gateway);
        border: 2px solid var(--color-gateway);
      }

      .btn-outline:hover {
        background: var(--color-gateway);
        color: var(--white);
      }

      /* Hero Section */
      .hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: var(--white);
        padding: 8rem 0 4rem;
        text-align: center;
        position: relative;
        overflow: hidden;
      }

      .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="300" fill="url(%23a)"/><circle cx="800" cy="800" r="400" fill="url(%23a)"/></svg>');
        opacity: 0.3;
      }

      .hero-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        position: relative;
        z-index: 2;
      }

      .hero h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        line-height: 1.2;
      }

      .hero p {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
      }

      .hero-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
      }

      /* Features Section */
      .features {
        padding: 6rem 0;
        background: var(--white);
      }

      .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
      }

      .section-title {
        text-align: center;
        margin-bottom: 3rem;
      }

      .section-title h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1rem;
      }

      .section-title p {
        font-size: 1.125rem;
        color: var(--gray-600);
        max-width: 600px;
        margin: 0 auto;
      }

      .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 4rem;
      }

      .feature-card {
        background: var(--white);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
      }

      .feature-icon {
        width: 60px;
        height: 60px;
        background: var(--color-gateway);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 1.5rem;
        color: var(--white);
      }

      .feature-card h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 1rem;
      }

      .feature-card p {
        color: var(--gray-600);
        line-height: 1.6;
      }

      /* Pricing Section */
      .pricing {
        padding: 6rem 0;
        background: var(--gray-100);
      }

      .pricing-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
      }

      .pricing-text h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1.5rem;
      }

      .pricing-text p {
        font-size: 1.125rem;
        color: var(--gray-600);
        margin-bottom: 2rem;
      }

      .price-highlight {
        background: var(--color-gateway);
        color: var(--white);
        padding: 1rem 2rem;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 2rem;
      }

      .price-highlight .price {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
      }

      .price-highlight .period {
        font-size: 1rem;
        opacity: 0.9;
      }

      .pricing-features {
        list-style: none;
      }

      .pricing-features li {
        padding: 0.5rem 0;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }

      .pricing-features li::before {
        content: '✓';
        color: var(--success);
        font-weight: bold;
      }

      /* Benefits Section */
      .benefits {
        padding: 6rem 0;
        background: var(--white);
      }

      .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 4rem;
      }

      .benefit-item {
        text-align: center;
        padding: 1.5rem;
      }

      .benefit-item h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 1rem;
      }

      .benefit-item p {
        color: var(--gray-600);
        line-height: 1.6;
      }

      /* API Section */
      .api-section {
        padding: 6rem 0;
        background: var(--gray-900);
        color: var(--white);
      }

      .api-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
      }

      .api-text h2 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
      }

      .api-text p {
        font-size: 1.125rem;
        opacity: 0.9;
        margin-bottom: 2rem;
      }

      .code-block {
        background: var(--gray-800);
        border-radius: 8px;
        padding: 1.5rem;
        overflow-x: auto;
        border: 1px solid var(--gray-700);
      }

      .code-block pre {
        color: var(--gray-300);
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.875rem;
        line-height: 1.5;
      }

      /* App Section */
      .app-section {
        padding: 6rem 0;
        background: var(--white);
        text-align: center;
      }

      .app-content h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1.5rem;
      }

      .app-content p {
        font-size: 1.125rem;
        color: var(--gray-600);
        margin-bottom: 2rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
      }

      .app-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
      }

      .app-button {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        background: var(--gray-900);
        color: var(--white);
        text-decoration: none;
        border-radius: 8px;
        transition: transform 0.3s ease;
      }

      .app-button:hover {
        transform: translateY(-2px);
      }

      /* FAQ Section */
      .faq {
        padding: 6rem 0;
        background: var(--gray-100);
      }

      .faq-content {
        text-align: center;
        max-width: 600px;
        margin: 0 auto;
      }

      .faq h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1rem;
      }

      .faq p {
        font-size: 1.125rem;
        color: var(--gray-600);
        margin-bottom: 2rem;
      }

      /* Footer */
      .footer {
        background: var(--gray-900);
        color: var(--white);
        padding: 4rem 0 2rem;
      }

      .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
      }

      .footer-section h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
      }

      .footer-section a {
        color: var(--gray-400);
        text-decoration: none;
        display: block;
        padding: 0.25rem 0;
        transition: color 0.3s ease;
      }

      .footer-section a:hover {
        color: var(--white);
      }

      .footer-bottom {
        border-top: 1px solid var(--gray-700);
        padding-top: 2rem;
        text-align: center;
        color: var(--gray-400);
      }

      /* Responsive */
      @media (max-width: 768px) {
        .hero h1 {
          font-size: 2.5rem;
        }

        .hero p {
          font-size: 1rem;
        }

        .pricing-content,
        .api-content {
          grid-template-columns: 1fr;
          gap: 2rem;
        }

        .nav {
          display: none;
        }

        .hero-buttons {
          flex-direction: column;
          align-items: center;
        }

        .app-buttons {
          flex-direction: column;
          align-items: center;
        }
      }

      /* Legacy classes for compatibility */
      .btn-gateway {
        background: var(--color-gateway) !important;
        background-color: var(--color-gateway) !important;
      }

      .btn-gateway:hover {
        background: var(--color-gateway-opacity) !important;
        background-color: var(--color-gateway-opacity) !important;
      }
    </style>
  </head>
  <body class="body-2">
    <input type="hidden" id="src"/>
    <input type="hidden" id="utm"/>
    
    <!-- Header -->
    <header class="header">
      <div class="header-content">
        <a href="#" class="logo">
          <img src="{{ asset($setting->gateway_logo) }}" 
             alt="{{ $setting->gateway_name }}" 
             style="height: 40px; width: auto;"
             class="theme-logo"
             data-light-src="{{ asset($setting->gateway_logo) }}"
             data-dark-src="{{ asset($setting->gateway_logo_dark ?? $setting->gateway_logo) }}">
        </a>
        <nav class="nav">
          <a href="#funcionalidades" class="nav-link">Funcionalidades</a>
          <a href="#taxas" class="nav-link">Taxas e prazos</a>
          <a href="#faq" class="nav-link">FAQ</a>
          <a href="/login" class="nav-link">Acessar</a>
          <a onclick="redirectCadastrar();" class="btn btn-primary">Comece agora</a>
        </nav>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1>PLATAFORMA DE PAGAMENTOS</h1>
        <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1.5rem;">Pagamentos otimizados para impulsionar o desempenho do seu negócio digital</h2>
        <p>Junte-se à transformação das plataformas de pagamento: na {{ $setting->gateway_name }}, suas vendas no Cartão, Boleto e Pix tem taxa negociável!</p>
        <p>Sua privacidade é nossa prioridade: na {{ $setting->gateway_name }}, suas vendas no Pix tem taxa negociável!</p>
        <div class="hero-buttons">
          <a onclick="redirectCadastrar();" class="btn btn-primary">Criar conta</a>
          <a href="/login" class="btn btn-outline">Login</a>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="funcionalidades">
      <div class="container">
        <div class="section-title">
          <h2>Uma plataforma completa e otimizada para potencializar seus resultados no mundo digital</h2>
        </div>
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">🎧</div>
            <h3>Suporte que funciona</h3>
            <p>Conte com suporte dedicado sempre que precisar, com uma equipe especializada e pronta para te auxiliar.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Relatórios</h3>
            <p>Relatórios para uma gestão completa.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">🎛️</div>
            <h3>Controle</h3>
            <p>Controle completo da plataforma nas suas mãos.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing" id="taxas">
      <div class="container">
        <div class="pricing-content">
          <div class="pricing-text">
            <h2>Taxas a partir de</h2>
            <div class="price-highlight">
              <div class="price">2,99%</div>
              <div class="period">por transação</div>
            </div>
            <p>Crie sua conta sem custo, e um de nossos especialistas entrará em contato para apresentar as melhores condições de taxas disponíveis no mercado.</p>
            <div class="hero-buttons">
              <a onclick="redirectCadastrar();" class="btn btn-primary">Criar conta</a>
              <a href="https://api.whatsapp.com/send?phone=55{{ $setting->contato }}&text=Ol%C3%A1!%20Vim%20pelo%20site%20da%20{{ $setting->gateway_name }}." class="btn btn-outline">Saiba mais com gerente de contas</a>
            </div>
          </div>
          <div>
            <img src="{{ asset($landing->section5_image) }}" alt="Pricing" style="width: 100%; max-width: 500px; height: auto;">
          </div>
        </div>
      </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits">
      <div class="container">
        <div class="section-title">
          <h2>Porque escolher a {{ $setting->gateway_name }}?</h2>
        </div>
        <div class="benefits-grid">
          <div class="benefit-item">
            <h3>Eficiência nas transações</h3>
            <p>Realize pagamentos rápidos e sem complicações.</p>
          </div>
          <div class="benefit-item">
            <h3>Transparência e controle</h3>
            <p>Tenha total transparência e controle sobre todas as operações financeiras</p>
          </div>
          <div class="benefit-item">
            <h3>Pix e Cripto</h3>
            <p>Confie em uma empresa com um histórico de sucesso comprovado em soluções de pagamento.</p>
          </div>
          <div class="benefit-item">
            <h3>Suporte personalizado 24h</h3>
            <p>Com um grupo de WhatsApp exclusivo para cada cliente, disponível 24h por dia, 7dias por semana.</p>
          </div>
          <div class="benefit-item">
            <h3>Segurança reforçada</h3>
            <p>Pin da transação e autenticação de dois fatores para proteger suas operações.</p>
          </div>
          <div class="benefit-item">
            <h3>Facilidade de integração</h3>
            <p>Integre a {{ $setting->gateway_name }} em qualquer tipo de plataforma em menos de 1 hora.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- API Section -->
    <section class="api-section">
      <div class="container">
        <div class="api-content">
          <div class="api-text">
            <h2>Fácil de integrar</h2>
            <p>Integre nossa API de pagamentos em minutos</p>
            <ul style="list-style: none; padding: 0;">
              <li style="margin-bottom: 1rem;">• Com nossa API você pode integrar pagamentos ao seu site de maneira rápida e eficiente.</li>
              <li>• Fornecemos guias detalhados e suporte técnico, garantindo uma implementação suave e sem complicações, para que sua equipe possa focar no que realmente importa.</li>
            </ul>
            <a href="#" class="btn btn-primary">Ver documentação</a>
          </div>
          <div class="code-block">
            <pre><code>await axios.post('https://api.{{ strtolower($setting->gateway_name) }}.com/v2/pix/payment', {
 amount: 500,
 external_id: '123456',
 description: 'Descrição',
 creditParty: {
  name: 'Monkey D. Luffy',
  keyType: 'EMAIL',
  key: 'redacted@example.invalid',
  taxId: '99999999999'
 },
 postbackUrl: 'https://linkdoseuwebhook.com',
}, {
 headers: {
  'Content-Type': 'application/json',
  'Authorization': `Bearer ${token}`
 }
});</code></pre>
          </div>
        </div>
      </div>
    </section>

    <!-- App Section -->
    <section class="app-section">
      <div class="container">
        <div class="app-content">
          <h2>Baixe nosso App</h2>
          <p>Monitore suas vendas pelo seu aparelho celular</p>
          <p>Disponibilizamos um aplicativo exclusivo para dispositivos Android e iOS.</p>
          <div class="app-buttons">
            <a href="#" class="app-button">
              <span>📱</span>
              <span>Download para Android</span>
            </a>
            <a href="#" class="app-button">
              <span>🍎</span>
              <span>Download para iOS</span>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq" id="faq">
      <div class="container">
        <div class="faq-content">
          <h2>Alguma dúvida?</h2>
          <p>Estamos aqui para ajudar!</p>
          <p>Nossa equipe de suporte está disponível 24/7 para responder suas dúvidas e resolver qualquer problema. Entre em contato e teremos prazer em ajudar você.</p>
          <a href="https://api.whatsapp.com/send?phone=55{{ $setting->contato }}&text=Ol%C3%A1!%20Vim%20pelo%20site%20da%20{{ $setting->gateway_name }}." class="btn btn-primary">Falar com a equipe de suporte</a>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-section">
            <h3>Plataforma</h3>
            <a href="#taxas">Taxas</a>
            <a href="#faq">FAQ</a>
            <a href="#">Ajuda</a>
          </div>
          <div class="footer-section">
            <h3>Empresa</h3>
            <a href="#">Contato</a>
            <a href="#">Denúncias</a>
          </div>
          <div class="footer-section">
            <h3>Sobre</h3>
            <a href="/termos-de-uso" target="_blank">Termos de Uso</a>
            <a href="#">Política de Privacidade</a>
            <a href="#">Política de Segurança</a>
            <a href="#">PLD</a>
            <a href="#">Manual KYC</a>
            <a href="#">Politica de Compilance</a>
            <a href="#">Politica de Segurança Cybernética</a>
            <a href="#">Politica antifraude</a>
          </div>
        </div>
        <div class="footer-bottom">
          <p>{{ $setting->gateway_name }} - Todos os direitos reservados</p>
        </div>
      </div>
    </footer>

    <script src="{{ asset('LandingPage/js/webflow.js') }}" type="text/javascript"></script>
    <script>
      function redirectCadastrar() {
        window.location.href = '/register';
      }
    </script>
  </body>
</html>
