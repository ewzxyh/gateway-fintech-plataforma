<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout V2 - {{ $produto->name_produto ?? 'Produto' }}</title>
    
    <!-- Fonts -->
    <link href="[REDACTED_BASIC_AUTH_URL]" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #48bb78;
            --danger-color: #ef4444;
            --warning-color: #ed8936;
            --dark-color: #2d3748;
            --light-color: #f7fafc;
            --border-color: #e2e8f0;
            --text-color: #2d3748;
            --text-muted: #718096;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
            --border-radius-lg: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Header com Timer */
        .checkout-header {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            color: white;
            padding: 1rem 2rem;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-lg);
        }

        .checkout-header .timer-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .checkout-header .timer-icon {
            font-size: 1.5rem;
            color: var(--danger-color);
        }

        .checkout-header .timer-text {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }

        .checkout-header .progress-bar {
            background: rgba(255, 255, 255, 0.2);
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .checkout-header .progress-fill {
            background: var(--success-color);
            height: 100%;
            width: 85%;
            transition: width 0.3s ease;
        }

        /* Container Principal */
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
            min-height: calc(100vh - 120px);
        }

        /* Coluna Principal (Esquerda) */
        .checkout-main {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            height: fit-content;
        }

        /* Coluna Sidebar (Direita) */
        .checkout-sidebar {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-lg);
            height: fit-content;
            position: sticky;
            top: 120px;
        }

        /* Componentes */
        .checkout-component {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background: white;
        }

        .checkout-component:last-child {
            margin-bottom: 0;
        }

        /* Header do Produto */
        .product-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .product-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .product-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        /* Informações do Produto */
        .product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--light-color);
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }

        .product-image {
            width: 80px;
            height: 80px;
            border-radius: var(--border-radius);
            object-fit: cover;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        .product-details h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--success-color);
        }

        /* Formulários */
        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h2 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .form-section .step-number {
            background: var(--primary-color);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Seção de Pagamento */
        .payment-section {
            background: var(--light-color);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }

        .payment-methods {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .payment-method {
            flex: 1;
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
        }

        .payment-method.active {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
        }

        .payment-method i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .payment-method h4 {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .payment-method p {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Botão de Compra */
        .checkout-button {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--success-color) 0%, #38a169 100%);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.125rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .checkout-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .checkout-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Sidebar */
        .sidebar-header {
            background: var(--success-color);
            color: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .sidebar-header h3 {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
        }

        .order-summary {
            margin-bottom: 1.5rem;
        }

        .order-summary h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--text-color);
        }

        .order-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--light-color);
            border-radius: var(--border-radius);
            margin-bottom: 0.75rem;
        }

        .order-item img {
            width: 50px;
            height: 50px;
            border-radius: var(--border-radius);
            object-fit: cover;
        }

        .order-item-info h5 {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .order-item-info p {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .order-total {
            border-top: 1px solid var(--border-color);
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .order-total h3 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--success-color);
            margin-bottom: 0.5rem;
        }

        .order-total p {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Depoimentos */
        .testimonials {
            margin-bottom: 1.5rem;
        }

        .testimonials h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .testimonial {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--light-color);
            border-radius: var(--border-radius);
            margin-bottom: 0.75rem;
        }

        .testimonial-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        .testimonial-content h5 {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .testimonial-content p {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-style: italic;
        }

        /* Selos de Confiança */
        .trust-badges {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .trust-badge {
            text-align: center;
            padding: 0.75rem 0.5rem;
            background: var(--light-color);
            border-radius: var(--border-radius);
        }

        .trust-badge i {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .trust-badge p {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-color);
        }

        /* Footer */
        .checkout-footer {
            text-align: center;
            padding: 1.5rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .checkout-footer .security-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .checkout-footer .security-info span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .checkout-footer .security-info i {
            color: var(--success-color);
        }

        /* Responsividade */
        @media (max-width: 1024px) {
            .checkout-container {
                grid-template-columns: 1fr;
                gap: 1rem;
                padding: 1rem;
            }

            .checkout-sidebar {
                position: static;
                order: -1;
            }

            .checkout-main {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .checkout-header {
                padding: 1rem;
            }

            .checkout-header .timer-text {
                font-size: 1.25rem;
            }

            .checkout-header .timer-container {
                flex-direction: column;
                gap: 0.5rem;
            }

            .checkout-container {
                padding: 0.5rem;
            }

            .checkout-main,
            .checkout-sidebar {
                padding: 1rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .payment-methods {
                flex-direction: column;
            }

            .trust-badges {
                grid-template-columns: repeat(2, 1fr);
            }

            .product-info {
                flex-direction: column;
                text-align: center;
            }

            .product-image {
                width: 100px;
                height: 100px;
            }

            .form-section h2 {
                font-size: 1.125rem;
            }

            .form-section .step-number {
                width: 28px;
                height: 28px;
                font-size: 0.75rem;
            }

            .checkout-button {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
            }

            .order-item {
                flex-direction: column;
                text-align: center;
            }

            .testimonial {
                flex-direction: column;
                text-align: center;
            }

            .security-info {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .checkout-header {
                padding: 0.75rem;
            }

            .checkout-header .timer-text {
                font-size: 1rem;
            }

            .checkout-main,
            .checkout-sidebar {
                padding: 0.75rem;
            }

            .product-header h1 {
                font-size: 1.5rem;
            }

            .product-header p {
                font-size: 1rem;
            }

            .form-section h2 {
                font-size: 1rem;
            }

            .trust-badges {
                grid-template-columns: 1fr;
            }

            .payment-method {
                padding: 0.75rem;
            }

            .payment-method i {
                font-size: 1.25rem;
            }

            .checkout-button {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }
        }

        /* Animações */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .checkout-component {
            animation: fadeInUp 0.6s ease-out;
        }

        .checkout-component:nth-child(2) { animation-delay: 0.1s; }
        .checkout-component:nth-child(3) { animation-delay: 0.2s; }
        .checkout-component:nth-child(4) { animation-delay: 0.3s; }
        .checkout-component:nth-child(5) { animation-delay: 0.4s; }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Header com Timer -->
    <header class="checkout-header">
        <div class="timer-container">
            <i class="fas fa-clock timer-icon"></i>
            <span class="timer-text" id="countdown">00:05:00</span>
        </div>
        <p>Sua compra está reservada, você está adquirindo um produto físico.</p>
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
    </header>

    <!-- Container Principal -->
    <div class="checkout-container">
        <!-- Coluna Principal (Esquerda) -->
        <div class="checkout-main">
            <!-- Header do Produto -->
            <div class="product-header">
                <h1>Finalize sua Compra</h1>
                <p>Complete os dados abaixo para finalizar sua compra com segurança</p>
            </div>

            <!-- Informações do Produto -->
            <div class="product-info">
                @if(!empty($produto->logo_produto))
                    <img src="{{ env('APP_URL').'/'.$produto->logo_produto }}" alt="{{ $produto->name_produto }}" class="product-image">
                @else
                    <div class="product-image"></div>
                @endif
                <div class="product-details">
                    <h3>{{ $produto->name_produto ?? 'Nome do Produto' }}</h3>
                    <div class="product-price">R$ {{ number_format((float)($produto->valor ?? 0), 2, ',', '.') }}</div>
                </div>
            </div>

            <!-- Formulário de Dados Pessoais -->
            <div class="form-section">
                <h2>
                    <span class="step-number">1</span>
                    Seus Dados
                </h2>
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="redacted@example.invalid" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="cpf">CPF</label>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
                    </div>
                    <div class="form-group">
                        <label for="telefone">Celular</label>
                        <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" required>
                    </div>
                </div>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.5rem;">
                    <a href="#" style="color: var(--primary-color);">Por que pedimos esses dados?</a>
                </p>
            </div>

            <!-- Formulário de Entrega -->
            <div class="form-section">
                <h2>
                    <span class="step-number">2</span>
                    Dados de Entrega
                </h2>
                <div class="form-group">
                    <label for="cep">CEP</label>
                    <input type="text" id="cep" name="cep" placeholder="00000-000" required>
                </div>
                <div class="form-group">
                    <label for="endereco">Endereço</label>
                    <input type="text" id="endereco" name="endereco" placeholder="Rua, número, complemento" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade" required>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <input type="text" id="estado" name="estado" required>
                    </div>
                </div>
            </div>

            <!-- Seção de Pagamento -->
            <div class="form-section">
                <h2>
                    <span class="step-number">3</span>
                    Pagamento
                </h2>
                <div class="payment-section">
                    <div class="payment-methods">
                        <div class="payment-method active" data-method="pix">
                            <i class="fas fa-qrcode"></i>
                            <h4>PIX</h4>
                            <p>Pagamento instantâneo</p>
                        </div>
                        <div class="payment-method" data-method="cartao">
                            <i class="fas fa-credit-card"></i>
                            <h4>Cartão</h4>
                            <p>Crédito ou débito</p>
                        </div>
                        <div class="payment-method" data-method="boleto">
                            <i class="fas fa-barcode"></i>
                            <h4>Boleto</h4>
                            <p>Pagamento à vista</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botão de Finalizar Compra -->
            <button class="checkout-button" id="finalizar-compra">
                <i class="fas fa-lock"></i>
                Finalizar Compra - R$ {{ number_format((float)($produto->valor ?? 0), 2, ',', '.') }}
            </button>
        </div>

        <!-- Coluna Sidebar (Direita) -->
        <div class="checkout-sidebar">
            <!-- Header da Sidebar -->
            <div class="sidebar-header">
                <h3><i class="fas fa-shield-alt"></i> Compra Segura</h3>
            </div>

            <!-- Resumo do Pedido -->
            <div class="order-summary">
                <h4>Resumo do Pedido</h4>
                <div class="order-item">
                    @if(!empty($produto->logo_produto))
                        <img src="{{ env('APP_URL').'/'.$produto->logo_produto }}" alt="{{ $produto->name_produto }}">
                    @else
                        <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: var(--border-radius);"></div>
                    @endif
                    <div class="order-item-info">
                        <h5>{{ $produto->name_produto ?? 'Nome do Produto' }}</h5>
                        <p>Precisa de ajuda? Veja o contato do vendedor</p>
                    </div>
                </div>
                <div class="order-total">
                    <h3>Total: R$ {{ number_format((float)($produto->valor ?? 0), 2, ',', '.') }}</h3>
                    <p>1x de R$ {{ number_format((float)($produto->valor ?? 0), 2, ',', '.') }} ou R$ {{ number_format((float)($produto->valor ?? 0), 2, ',', '.') }} à vista</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Renovação atual</p>
                </div>
            </div>

            <!-- Depoimentos -->
            <div class="testimonials">
                <h4>Depoimentos</h4>
                <div class="testimonial">
                    <div class="testimonial-avatar">M</div>
                    <div class="testimonial-content">
                        <h5>Maria Silva</h5>
                        <p>"Produto excelente! Entrega rápida e qualidade impecável."</p>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-avatar">J</div>
                    <div class="testimonial-content">
                        <h5>João Santos</h5>
                        <p>"Superou minhas expectativas. Recomendo!"</p>
                    </div>
                </div>
            </div>

            <!-- Selos de Confiança -->
            <div class="trust-badges">
                <div class="trust-badge">
                    <i class="fas fa-shield-alt"></i>
                    <p>Seguro</p>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-truck"></i>
                    <p>Entrega</p>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-undo"></i>
                    <p>Garantia</p>
                </div>
            </div>

            <!-- Informações Legais -->
            <div style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 1rem;">
                <p>Sixx Global é uma instituição de pagamento para o comércio eletrônico regulada pelo Banco Central do Brasil e protegida pela Política de privacidade e Termos de serviço. Para reclamações sobre serviços financeiros, você também pode entrar em contato com os Termos de Compra.</p>
            </div>

            <!-- Área para Depoimentos Adicionais -->
            <div style="text-align: center; padding: 1rem; border: 2px dashed var(--border-color); border-radius: var(--border-radius); color: var(--text-muted); font-size: 0.875rem;">
                <i class="fas fa-comments"></i>
                <p style="margin-top: 0.5rem;">Arraste depoimentos para esta área</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="checkout-footer">
        <div class="security-info">
            <span><i class="fas fa-lock"></i> Compra Segura</span>
            <span><i class="fas fa-shield-alt"></i> Dados Protegidos</span>
        </div>
        <p>© 2024 - Todos os direitos reservados.</p>
    </footer>

    <!-- Scripts -->
    <script>
        // Timer Countdown
        function startCountdown(duration) {
            const display = document.getElementById('countdown');
            let timer = duration, minutes, seconds;

            function updateDisplay() {
                minutes = Math.floor(timer / 60);
                seconds = timer % 60;

                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;

                display.textContent = '00:' + minutes + ':' + seconds;

                if (--timer < 0) {
                    timer = duration;
                }
            }

            setInterval(updateDisplay, 1000);
            updateDisplay();
        }

        // Iniciar timer de 5 minutos
        window.onload = function() {
            const fiveMinutes = 60 * 5;
            startCountdown(fiveMinutes);
        };

        // Máscaras de Input
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 3) value = value.slice(0, 3) + '.' + value.slice(3);
            if (value.length > 7) value = value.slice(0, 7) + '.' + value.slice(7);
            if (value.length > 11) value = value.slice(0, 11) + '-' + value.slice(11);
            e.target.value = value.slice(0, 14);
        });

        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
            if (value.length > 8) value = value.slice(0, 10) + '-' + value.slice(10);
            e.target.value = value.slice(0, 15);
        });

        document.getElementById('cep').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) value = value.slice(0, 5) + '-' + value.slice(5);
            e.target.value = value.slice(0, 9);
        });

        // Busca CEP
        document.getElementById('cep').addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('endereco').value = data.logradouro;
                            document.getElementById('cidade').value = data.localidade;
                            document.getElementById('estado').value = data.uf;
                        }
                    })
                    .catch(error => console.error('Erro ao buscar CEP:', error));
            }
        });

        // Seleção de Método de Pagamento
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Finalizar Compra
        document.getElementById('finalizar-compra').addEventListener('click', function() {
            const button = this;
            const originalText = button.innerHTML;
            
            // Validação básica
            const requiredFields = ['nome', 'email', 'cpf', 'telefone', 'cep', 'endereco', 'cidade', 'estado'];
            let isValid = true;
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--danger-color)';
                } else {
                    field.style.borderColor = 'var(--border-color)';
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos Obrigatórios',
                    text: 'Por favor, preencha todos os campos obrigatórios.',
                });
                return;
            }

            // Simular processamento
            button.innerHTML = '<span class="spinner"></span> Processando...';
            button.disabled = true;

            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Compra Finalizada!',
                    text: 'Sua compra foi processada com sucesso.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Redirecionar para página de obrigado
                    window.location.href = '/obrigado';
                });
            }, 2000);
        });
    </script>
</body>
</html>
