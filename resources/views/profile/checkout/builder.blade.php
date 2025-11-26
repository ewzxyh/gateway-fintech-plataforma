<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Builder - {{ $checkout->produto_name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>
        :root {
            --primary-color: #0b6856;
            --secondary-color: #0f7864;
            --success-color: #00d47f;
            --dark-bg: #1a1a1a;
            --sidebar-bg: #2d2d2d;
            --card-bg: #ffffff;
            --text-primary: #000000;
            --text-secondary: #666666;
            --border-color: #e0e0e0;
        }

        body {
            background-color: var(--dark-bg);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Header */
        .builder-header {
            background-color: var(--dark-bg);
            padding: 1rem 2rem;
            border-bottom: 2px solid var(--primary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .logo {
            color: var(--success-color);
            font-size: 1.5rem;
            font-weight: bold;
        }

        .save-btn {
            background-color: var(--success-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .save-btn:hover {
            background-color: var(--secondary-color);
        }

        /* Main Layout */
        .builder-container {
            display: flex;
            margin-top: 80px;
            height: calc(100vh - 80px);
        }

        /* Preview Area - Two Columns */
        .preview-area {
            flex: 1;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            padding: 2rem;
            overflow-y: auto;
        }

        .preview-container {
            background-color: #f8f9fa;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .checkout-columns {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
        }

        @media (max-width: 992px) {
            .checkout-columns {
                grid-template-columns: 1fr;
            }
        }

        /* Drag Zones */
        .drag-zone {
            background-color: #f8f9fa;
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            color: var(--text-secondary);
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .drag-zone.drag-over {
            border-color: var(--success-color);
            background-color: rgba(0, 212, 127, 0.1);
        }

        .drag-zone-title {
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        /* Checkout Components */
        .checkout-component {
            margin-bottom: 1rem;
            padding: 1.5rem;
            border-radius: 8px;
            background: white;
            border: 1px solid var(--border-color);
            position: relative;
            transition: all 0.3s ease;
            cursor: move;
        }

        .checkout-component:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--success-color);
        }

        .component-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .checkout-component:hover .component-controls {
            opacity: 1;
        }

        .component-control-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .component-control-btn.delete {
            background-color: #dc3545;
        }

        /* Product Component */
        .product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .product-image {
            width: 80px;
            height: 80px;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--text-secondary);
        }

        .product-details h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
        }

        .product-price {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Form Components */
        .form-section {
            width: 100%;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .section-icon {
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .section-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.95rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Payment Component */
        .payment-method {
            background: #f8f9fa;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background: rgba(11, 104, 86, 0.05);
        }

        .payment-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .payment-info h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
        }

        .payment-benefits {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .payment-benefits li {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .benefit-icon {
            color: var(--success-color);
            margin-right: 0.25rem;
        }

        /* Button Component */
        .checkout-button {
            width: 100%;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .checkout-button:hover {
            background-color: var(--secondary-color);
        }

        /* Order Summary */
        .order-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-total {
            font-weight: 600;
            font-size: 1.1rem;
            padding-top: 1rem;
            margin-top: 0.5rem;
            border-top: 2px solid var(--border-color);
        }

        /* Security Badge */
        .security-badge {
            background: #e8f5e9;
            color: var(--primary-color);
            padding: 0.75rem;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .legal-text {
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* Sidebar */
        .sidebar {
            width: 350px;
            background-color: var(--sidebar-bg);
            padding: 2rem;
            overflow-y: auto;
            border-left: 1px solid #444;
        }

        .sidebar-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .sidebar-tab {
            flex: 1;
            background: transparent;
            border: 1px solid #444;
            color: #aaa;
            padding: 0.75rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sidebar-tab.active {
            background: var(--success-color);
            color: white;
            border-color: var(--success-color);
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        .components-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .component-btn {
            background: #3a3a3a;
            border: 1px solid #555;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            cursor: move;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .component-btn:hover {
            background: #4a4a4a;
            border-color: var(--success-color);
        }

        .component-icon {
            font-size: 1.5rem;
        }

        .component-name {
            font-size: 0.85rem;
        }

        /* Color Pickers */
        .color-picker-group {
            margin-bottom: 1.5rem;
        }

        .color-picker-group label {
            display: block;
            color: #aaa;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .color-picker-group input[type="color"] {
            width: 100%;
            height: 40px;
            border: 1px solid #555;
            border-radius: 6px;
            cursor: pointer;
        }

        .compra-segura-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 1rem;
        }

        .compra-segura-box h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
        }

        .oferta-limitada-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #856404;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="builder-header">
        <div class="logo">
            <i class="fas fa-rocket"></i> Checkout Builder
        </div>
        <button class="save-btn" onclick="saveCheckout()">
            <i class="fas fa-save"></i> Salvar
        </button>
    </div>

    <!-- Main Container -->
    <div class="builder-container">
        <!-- Preview Area -->
        <div class="preview-area">
            <div class="preview-container">
                <div class="checkout-columns">
                    <!-- Left Column -->
                    <div class="left-column">
                        <div class="drag-zone-title">
                            <i class="fas fa-arrow-down"></i> Coluna Principal
                        </div>
                        <div class="drag-zone" id="left-drag-zone">
                            <i class="fas fa-plus"></i> Arraste componentes aqui
                        </div>
                    </div>

                    <!-- Right Column (Sidebar) -->
                    <div class="right-column">
                        <div class="drag-zone-title">
                            <i class="fas fa-arrow-down"></i> Sidebar
                        </div>

                        <!-- Compra Segura (Fixed) -->
                        <div class="checkout-component" data-component="compra-segura" style="cursor: default;">
                            <div class="compra-segura-box">
                                <h4><i class="fas fa-shield-alt"></i> Compra segura</h4>
                            </div>
                        </div>

                        <!-- Resumo do Pedido (Fixed) -->
                        <div class="checkout-component" data-component="order-summary" style="cursor: default;">
                            <div class="order-summary">
                                <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem;">Resumo do pedido</h3>

                                <div class="summary-item">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-box" style="color: var(--text-secondary);"></i>
                                        <span>{{ $checkout->produto_name }}</span>
                                    </div>
                                    <span>R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}</span>
                                </div>

                                <div class="summary-total">
                                    <span>Total</span>
                                    <span>1 X de R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}</span>
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.5rem;">
                                    ou R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }} à vista
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem;">
                                    Renovação atual
                                </div>
                            </div>
                        </div>

                        <!-- Drag Zone for Testimonials -->
                        <div class="drag-zone" id="right-drag-zone">
                            <i class="fas fa-plus"></i> Arraste depoimentos para esta área
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-tabs">
                <button class="sidebar-tab active" data-tab="components" onclick="switchTab('components')">
                    Componentes
                </button>
                <button class="sidebar-tab" data-tab="settings" onclick="switchTab('settings')">
                    Configurações
                </button>
            </div>

            <!-- Components Tab -->
            <div id="components-panel" class="tab-panel active">
                <h3 style="color: #aaa; font-size: 0.9rem; margin-bottom: 1rem;">Componentes Principais</h3>
                <div class="components-grid">
                    <button class="component-btn" draggable="true" data-component="product-info" data-target="left">
                        <i class="fas fa-box component-icon"></i>
                        <span class="component-name">Produto</span>
                    </button>
                    <button class="component-btn" draggable="true" data-component="customer-data" data-target="left">
                        <i class="fas fa-user component-icon"></i>
                        <span class="component-name">Dados</span>
                    </button>
                    <button class="component-btn" draggable="true" data-component="payment" data-target="left">
                        <i class="fas fa-credit-card component-icon"></i>
                        <span class="component-name">Pagamento</span>
                    </button>
                    <button class="component-btn" draggable="true" data-component="oferta-limitada" data-target="left">
                        <i class="fas fa-ticket-alt component-icon"></i>
                        <span class="component-name">Cupom</span>
                    </button>
                    <button class="component-btn" draggable="true" data-component="oferta-especial" data-target="left">
                        <i class="fas fa-gift component-icon"></i>
                        <span class="component-name">OrderBump</span>
                    </button>
                    <button class="component-btn" draggable="true" data-component="resumo-pedido" data-target="left">
                        <i class="fas fa-receipt component-icon"></i>
                        <span class="component-name">Resumo</span>
                    </button>
                    <button class="component-btn" draggable="true" data-component="checkout-button" data-target="left">
                        <i class="fas fa-check-circle component-icon"></i>
                        <span class="component-name">Botão Pagar</span>
                    </button>
                    <button class="component-btn" draggable="true" data-component="security" data-target="left">
                        <i class="fas fa-shield-alt component-icon"></i>
                        <span class="component-name">Segurança</span>
                    </button>
                </div>

                <h3 style="color: #aaa; font-size: 0.9rem; margin: 1.5rem 0 1rem 0;">Componentes Sidebar</h3>
                <div class="components-grid">
                    <button class="component-btn" draggable="true" data-component="testimonial" data-target="right">
                        <i class="fas fa-quote-left component-icon"></i>
                        <span class="component-name">Depoimento</span>
                    </button>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings-panel" class="tab-panel">
                <h3 style="color: #aaa; font-size: 0.9rem; margin-bottom: 1rem;">Cores do Tema</h3>

                <div class="color-picker-group">
                    <label for="primary-color">Cor Primária</label>
                    <input type="color" id="primary-color" value="#0b6856">
                </div>

                <div class="color-picker-group">
                    <label for="secondary-color">Cor Secundária</label>
                    <input type="color" id="secondary-color" value="#0f7864">
                </div>

                <div class="color-picker-group">
                    <label for="button-color">Cor do Botão</label>
                    <input type="color" id="button-color" value="#0b6856">
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Initialize Sortable for drag and drop
        const leftZone = document.getElementById('left-drag-zone');
        const rightZone = document.getElementById('right-drag-zone');

        // Sortable for left column
        new Sortable(leftZone, {
            group: {
                name: 'left-column',
                pull: true,
                put: ['components']
            },
            animation: 150,
            ghostClass: 'drag-ghost',
            onAdd: function(evt) {
                const componentType = evt.item.dataset.component;
                const newComponent = createComponent(componentType);
                evt.item.replaceWith(newComponent);
            }
        });

        // Sortable for right column
        new Sortable(rightZone, {
            group: {
                name: 'right-column',
                pull: true,
                put: ['components']
            },
            animation: 150,
            ghostClass: 'drag-ghost',
            onAdd: function(evt) {
                const componentType = evt.item.dataset.component;
                const newComponent = createComponent(componentType);
                evt.item.replaceWith(newComponent);
            }
        });

        // Make component buttons draggable
        const componentBtns = document.querySelectorAll('.component-btn');
        componentBtns.forEach(btn => {
            btn.addEventListener('dragstart', function(e) {
                e.dataTransfer.effectAllowed = 'copy';
                e.dataTransfer.setData('component-type', this.dataset.component);
            });
        });

        // Drag zones event listeners
        [leftZone, rightZone].forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });

            zone.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });

            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                const componentType = e.dataTransfer.getData('component-type');
                if (componentType) {
                    const newComponent = createComponent(componentType);
                    this.appendChild(newComponent);
                }
            });
        });

        // Create component based on type
        function createComponent(type) {
            const div = document.createElement('div');
            div.className = 'checkout-component';
            div.dataset.component = type;

            let content = '';

            switch (type) {
                case 'product-info':
                    content = `
                        <div class="component-controls">
                            <button class="component-control-btn delete" onclick="deleteComponent(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                        </div>
                        <div class="product-info">
                            <div class="product-image">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="product-details">
                                <h3>{{ $checkout->produto_name }}</h3>
                                <div class="product-price">
                                    1 X de R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}
                                </div>
                                <div class="product-price">
                                    ou R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }} à vista
                                </div>
                            </div>
                        </div>
                    `;
                    break;

                case 'customer-data':
                    content = `
                        <div class="component-controls">
                            <button class="component-control-btn delete" onclick="deleteComponent(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-user section-icon"></i>
                                <h3 class="section-title">Seus dados</h3>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Nome completo</label>
                                <input type="text" class="form-control" placeholder="Nome do comprador">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="redacted@example.invalid">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">CPF</label>
                                    <input type="text" class="form-control" placeholder="304.761.160-23">
                            </div>
                                <div class="form-group">
                                    <label class="form-label">Celular</label>
                                    <input type="text" class="form-control" placeholder="+55 (99) 99999-9999">
                            </div>
                            </div>
                            
                            <a href="#" style="color: var(--primary-color); text-decoration: none; font-size: 0.9rem;">
                                Por que pedimos esses dados?
                            </a>
                            </div>
                        `;
                    break;

                case 'payment':
                    content = `
                        <div class="component-controls">
                            <button class="component-control-btn delete" onclick="deleteComponent(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                            </div>
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-credit-card section-icon"></i>
                                <h3 class="section-title">Pagamento</h3>
                                </div>
                            
                            <div class="payment-method selected">
                                <div class="payment-icon">
                                    <i class="fas fa-qrcode"></i>
                                </div>
                                <div class="payment-info">
                                    <h4>PIX</h4>
                                    <ul class="payment-benefits">
                                        <li><i class="fas fa-check benefit-icon"></i> Liberação imediata</li>
                                        <li><i class="fas fa-check benefit-icon"></i> É simples, só usar o aplicativo de seu banco para pagar Pix</li>
                                    </ul>
                            </div>
                    </div>
                            </div>
                        `;
                    break;

                case 'oferta-limitada':
                    content = `
                        <div class="component-controls">
                            <button class="component-control-btn delete" onclick="deleteComponent(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="alert alert-warning">
                            <strong><i class="fas fa-ticket-alt me-2"></i>Oferta limitada</strong>
                            <div class="mt-2">
                                <input type="text" class="form-control mb-2" placeholder="DIGITE O CUPOM DE DESCONTO">
                                <button class="btn btn-success w-100">Aplicar</button>
                            </div>
                        </div>
                    `;
                    break;

                case 'oferta-especial':
                    content = `
                        <div class="component-controls">
                            <button class="component-control-btn delete" onclick="deleteComponent(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div style="border: 2px dashed var(--border-color); border-radius: 8px; padding: 1.5rem; margin: 1rem 0;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="oferta-especial">
                                <label class="form-check-label fw-bold" for="oferta-especial">
                                    SIM, EU ACEITO ESSA OFERTA ESPECIAL!
                                </label>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Nome do seu produto</strong>
                                    <div class="text-muted small">Adicione à compra</div>
                                </div>
                                <div class="text-end">
                                    <div class="text-decoration-line-through text-muted small">R$ 0,00</div>
                                    <div class="text-success fw-bold">-INFINITY% OFF<br>R$ 25,00</div>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-primary mt-2 w-100">Adicionar Produto</button>
                        </div>
                    `;
                    break;

                case 'resumo-pedido':
                    content = `
                        <div class="component-controls">
                            <button class="component-control-btn delete" onclick="deleteComponent(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div style="background: white; border-radius: 8px; padding: 1.5rem; margin: 1rem 0;">
                            <h6 class="mb-3">Resumo do pedido</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-box me-2"></i>{{ $checkout->produto_name }}</span>
                                <span>R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total</span>
                                <span>R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    `;
                    break;

                case 'checkout-button':
                    content = `
                            <div class="component-controls">
                            <button class="component-control-btn delete" onclick="deleteComponent(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                            </div>
                        <button class="checkout-button">
                            <i class="fas fa-qrcode"></i> Pagar com Pix
                        </button>
                    `;
                    break;

                case 'security':
                    content = `
                    <div class="component-controls">
                        <button class="component-control-btn delete" onclick="deleteComponent(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                        <div style="text-align: center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 1rem;">
                                <img src="/storage/images/favicon_light.png" style="width: 20px; height: 20px;">
                                <span style="color: var(--text-secondary); font-size: 0.9rem;">
                                    Gateway está processando este pagamento para o vendedor
                                </span>
                            </div>
                            
                            <div class="security-badge" style="margin-bottom: 1rem;">
                                <i class="fas fa-shield-alt"></i>
                                <span>Compra 100% segura</span>
                            </div>
                            
                            <div class="legal-text">
                                Este site é protegido pelo reCAPTCHA do Google<br>
                                <strong>Política de privacidade</strong> e <strong>Termos de serviço</strong><br>
                                * Parcelamento com acréscimo<br><br>
                                Ao continuar, você concorda com os <strong>Termos de Compra</strong>
                                </div>
                            </div>
                        `;
                    break;

                case 'testimonial':
                    content = `
                        <div class="component-controls">
                            <button class="component-control-btn delete" onclick="deleteComponent(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                                    </div>
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <div style="width: 40px; height: 40px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                    A
                                </div>
                                <strong>Cliente Satisfeito</strong>
                            </div>
                            <p style="margin: 0; font-size: 0.9rem; color: var(--text-secondary);">
                                "Excelente produto! Recomendo muito."
                            </p>
                            </div>
                        `;
                    break;
            }

            div.innerHTML = content;
            return div;
        }

        // Delete component
        function deleteComponent(btn) {
            if (confirm('Deseja remover este componente?')) {
                btn.closest('.checkout-component').remove();
            }
        }

        // Switch tabs
        function switchTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.sidebar-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');

            // Update tab panels
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            document.getElementById(tabName + '-panel').classList.add('active');
        }

        // Color pickers
        document.getElementById('primary-color').addEventListener('change', function(e) {
            document.documentElement.style.setProperty('--primary-color', e.target.value);
        });

        document.getElementById('secondary-color').addEventListener('change', function(e) {
            document.documentElement.style.setProperty('--secondary-color', e.target.value);
        });

        document.getElementById('button-color').addEventListener('change', function(e) {
            document.querySelectorAll('.checkout-button').forEach(btn => {
                btn.style.backgroundColor = e.target.value;
            });
        });

        // Save checkout
        function saveCheckout() {
            const leftComponents = [];
            const rightComponents = [];

            // Get left column components
            document.querySelectorAll('#left-drag-zone .checkout-component').forEach(comp => {
                leftComponents.push(comp.dataset.component);
            });

            // Get right column components
            document.querySelectorAll('#right-drag-zone .checkout-component').forEach(comp => {
                rightComponents.push(comp.dataset.component);
            });

            const checkoutData = {
                layout: {
                    type: 'two-column'
                },
                settings: {
                    'primary-color': document.getElementById('primary-color').value,
                    'secondary-color': document.getElementById('secondary-color').value,
                    'button-color': document.getElementById('button-color').value
                },
                components: {
                    left: leftComponents,
                    right: rightComponents
                }
            };

            // Send to server
            fetch('/checkout-builder/{{ $checkout->id_unico }}/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(checkoutData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Checkout salvo com sucesso!');
                    } else {
                        alert('❌ Erro ao salvar checkout');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ Erro ao salvar checkout');
                });
        }

        // Show notification
        function showNotification(message, type) {
            alert(message);
        }
    </script>
</body>

</html>