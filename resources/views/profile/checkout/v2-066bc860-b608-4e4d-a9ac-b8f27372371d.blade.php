<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $checkout->produto_name }} - Checkout</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0b6856;
            --secondary-color: #0f7864;
            --accent-color: #00d47f;
            --text-color: #000000;
            --background-color: #ffffff;
        }
        
        body {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }
        
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .checkout-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 8px;
            padding: 1rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(11, 104, 86, 0.1);
        }
        
        .price-highlight {
            color: var(--accent-color);
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .testimonial-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .trust-badge {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .trust-badge i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .checkout-container {
                padding: 1rem;
            }
            
            .product-card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <!-- Header -->
        <div class="checkout-header">
            <h1>{{ $checkout->produto_name }}</h1>
            <p class="mb-0">Finalize sua compra com segurança</p>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Produto Principal -->
                <div class="product-card">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            @if($checkout->produto_image)
                                <img src="{{ env('APP_URL') }}/{{ $checkout->produto_image }}" 
                                     alt="{{ $checkout->produto_name }}" 
                                     class="img-fluid rounded">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-box fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h2>{{ $checkout->produto_name }}</h2>
                            <p class="text-muted">{{ $checkout->produto_descricao }}</p>
                            
                            <div class="d-flex align-items-center gap-3 mb-3">
                                @if($checkout->produto_de_valor)
                                    <span class="text-decoration-line-through text-muted">R$ {{ number_format($checkout->produto_de_valor, 2, ',', '.') }}</span>
                                @endif
                                <span class="price-highlight">R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}</span>
                            </div>
                            
                            @if($checkout->periodo_garantia)
                                <div class="alert alert-success">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    Garantia de {{ $checkout->periodo_garantia }} dias
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Formulário de Dados -->
                <div class="product-card">
                    <h4 class="mb-4">
                        <i class="fas fa-user me-2"></i>
                        Seus Dados
                    </h4>
                    
                    <form id="checkout-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CPF</label>
                                <input type="text" class="form-control" name="cpf" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefone</label>
                                <input type="tel" class="form-control" name="telefone" required>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-credit-card me-2"></i>
                                Finalizar Compra - R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Depoimentos -->
                @if($checkout->checkout_depoimentos_nome)
                <div class="product-card">
                    <h5 class="mb-3">
                        <i class="fas fa-comments me-2"></i>
                        Depoimentos
                    </h5>
                    
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                {{ substr($checkout->checkout_depoimentos_nome, 0, 1) }}
                            </div>
                            <strong>{{ $checkout->checkout_depoimentos_nome }}</strong>
                        </div>
                        <p class="mb-0">"{{ $checkout->checkout_depoimentos_depoimento }}"</p>
                    </div>
                </div>
                @endif
                
                <!-- Selos de Confiança -->
                <div class="product-card">
                    <h5 class="mb-3">
                        <i class="fas fa-shield-alt me-2"></i>
                        Compra Segura
                    </h5>
                    
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="trust-badge">
                                <i class="fas fa-lock"></i>
                                <div class="small">Seguro</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="trust-badge">
                                <i class="fas fa-truck"></i>
                                <div class="small">Entrega</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="trust-badge">
                                <i class="fas fa-undo"></i>
                                <div class="small">Garantia</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informações de Suporte -->
                @if($checkout->whatsapp_suporte || $checkout->email_suporte)
                <div class="product-card">
                    <h5 class="mb-3">
                        <i class="fas fa-headset me-2"></i>
                        Suporte
                    </h5>
                    
                    @if($checkout->whatsapp_suporte)
                    <div class="mb-2">
                        <a href="https://wa.me/{{ $checkout->whatsapp_suporte }}" class="text-decoration-none">
                            <i class="fab fa-whatsapp me-2"></i>
                            WhatsApp
                        </a>
                    </div>
                    @endif
                    
                    @if($checkout->email_suporte)
                    <div>
                        <a href="mailto:{{ $checkout->email_suporte }}" class="text-decoration-none">
                            <i class="fas fa-envelope me-2"></i>
                            {{ $checkout->email_suporte }}
                        </a>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Formulário de checkout
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Aqui você pode implementar a lógica de pagamento
            alert('Redirecionando para pagamento...');
        });
        
        // Máscara para CPF
        document.querySelector('input[name="cpf"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });
        
        // Máscara para telefone
        document.querySelector('input[name="telefone"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            value = value.replace(/(\d{4})-\d{4}(\d)/, '$1-$2');
            e.target.value = value;
        });
    </script>
</body>
</html>