<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirmação de Compra - {{ $checkout->produto_name ?? 'Gateway' }}</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
      color: #333;
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
      background-color: #f4f4f4;
    }

    .container {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .header {
      text-align: center;
      border-bottom: 2px solid #007bff;
      padding-bottom: 20px;
      margin-bottom: 30px;
    }

    .logo {
      font-size: 24px;
      font-weight: bold;
      color: #007bff;
      margin-bottom: 10px;
    }

    .success-icon {
      font-size: 48px;
      color: #28a745;
      margin-bottom: 20px;
    }

    .title {
      font-size: 24px;
      color: #333;
      margin-bottom: 10px;
    }

    .subtitle {
      font-size: 16px;
      color: #666;
    }

    .content {
      margin-bottom: 30px;
    }

    .order-info {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 20px;
      margin: 20px 0;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      padding: 5px 0;
      border-bottom: 1px solid #e9ecef;
    }

    .info-row:last-child {
      border-bottom: none;
      font-weight: bold;
      font-size: 18px;
      color: #007bff;
    }

    .info-label {
      font-weight: 500;
      color: #666;
    }

    .info-value {
      color: #333;
    }

    .product-section {
      background-color: #fff;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 20px;
      margin: 20px 0;
    }

    .product-title {
      font-size: 18px;
      font-weight: bold;
      color: #333;
      margin-bottom: 10px;
    }

    .product-description {
      color: #666;
      margin-bottom: 15px;
    }

    .next-steps {
      background-color: #e7f3ff;
      border: 1px solid #b3d9ff;
      border-radius: 8px;
      padding: 20px;
      margin: 20px 0;
    }

    .next-steps h3 {
      color: #007bff;
      margin-top: 0;
    }

    .next-steps ul {
      margin: 10px 0;
      padding-left: 20px;
    }

    .next-steps li {
      margin-bottom: 8px;
    }

    .support-section {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 20px;
      margin: 20px 0;
      text-align: center;
    }

    .support-title {
      font-size: 18px;
      font-weight: bold;
      color: #333;
      margin-bottom: 15px;
    }

    .support-info {
      margin-bottom: 10px;
    }

    .support-link {
      color: #007bff;
      text-decoration: none;
      font-weight: 500;
    }

    .support-link:hover {
      text-decoration: underline;
    }

    .footer {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #dee2e6;
      color: #6c757d;
      font-size: 14px;
    }

    .status-badge {
      display: inline-block;
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .status-paid {
      background-color: #d4edda;
      color: #155724;
    }

    .status-pending {
      background-color: #fff3cd;
      color: #856404;
    }

    .status-cancelled {
      background-color: #f8d7da;
      color: #721c24;
    }

    @media (max-width: 600px) {
      .container {
        padding: 20px;
        margin: 10px;
      }

      .info-row {
        flex-direction: column;
      }

      .info-label {
        margin-bottom: 5px;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <div class="success-icon">✅</div>
      <div class="logo">{{ $checkout->produto_name ?? 'Gateway' }}</div>
      <h1 class="title">Compra Confirmada!</h1>
      <p class="subtitle">Obrigado pela sua compra. Aqui estão os detalhes do seu pedido.</p>
    </div>

    <!-- Status do Pedido -->
    <div class="content">
      <div style="text-align: center; margin-bottom: 20px;">
        <span class="status-badge status-{{ $order->status === 'pago' ? 'paid' : ($order->status === 'aguardando' ? 'pending' : 'cancelled') }}">
          {{ $order->status === 'pago' ? 'Pago' : ($order->status === 'aguardando' ? 'Aguardando Pagamento' : 'Cancelado') }}
        </span>
      </div>
    </div>

    <!-- Informações do Pedido -->
    <div class="order-info">
      <h3 style="margin-top: 0; color: #333;">Detalhes do Pedido</h3>

      <div class="info-row">
        <span class="info-label">ID do Pedido:</span>
        <span class="info-value">#{{ $order->id }}</span>
      </div>

      <div class="info-row">
        <span class="info-label">ID da Transação:</span>
        <span class="info-value">{{ $order->idTransaction }}</span>
      </div>

      <div class="info-row">
        <span class="info-label">Data da Compra:</span>
        <span class="info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
      </div>

      <div class="info-row">
        <span class="info-label">Método de Pagamento:</span>
        <span class="info-value">{{ ucfirst($order->metodo ?? 'Não informado') }}</span>
      </div>

      <div class="info-row">
        <span class="info-label">Valor Total:</span>
        <span class="info-value">R$ {{ number_format($order->valor_total, 2, ',', '.') }}</span>
      </div>
    </div>

    <!-- Informações do Cliente -->
    <div class="order-info">
      <h3 style="margin-top: 0; color: #333;">Dados do Cliente</h3>

      <div class="info-row">
        <span class="info-label">Nome:</span>
        <span class="info-value">{{ $order->name }}</span>
      </div>

      <div class="info-row">
        <span class="info-label">Email:</span>
        <span class="info-value">{{ $order->email }}</span>
      </div>

      <div class="info-row">
        <span class="info-label">CPF:</span>
        <span class="info-value">{{ $order->cpf }}</span>
      </div>

      @if($order->telefone)
      <div class="info-row">
        <span class="info-label">Telefone:</span>
        <span class="info-value">{{ $order->telefone }}</span>
      </div>
      @endif
    </div>

    <!-- Informações do Produto -->
    <div class="product-section">
      <h3 class="product-title">{{ $checkout->produto_name ?? 'Produto' }}</h3>
      @if($checkout->produto_descricao)
      <p class="product-description">{{ $checkout->produto_descricao }}</p>
      @endif

      @if($checkout->produto_tipo === 'digital')
      <div style="background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin-top: 15px;">
        <strong>📱 Produto Digital:</strong> Você receberá o acesso ao produto em breve por email.
      </div>
      @elseif($checkout->produto_tipo === 'fisico')
      <div style="background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin-top: 15px;">
        <strong>📦 Produto Físico:</strong> Seu produto será enviado para o endereço informado.
      </div>
      @endif
    </div>

    <!-- Próximos Passos -->
    <div class="next-steps">
      <h3>Próximos Passos</h3>
      <ul>
        @if($order->status === 'pago')
        <li>✅ Pagamento confirmado com sucesso</li>
        <li>📧 Você receberá o acesso ao produto por email</li>
        <li>📱 Verifique sua caixa de spam caso não receba</li>
        @elseif($order->status === 'aguardando')
        <li>⏳ Aguardando confirmação do pagamento</li>
        <li>📧 Você receberá um email quando o pagamento for confirmado</li>
        <li>💳 Verifique se o pagamento foi processado</li>
        @else
        <li>❌ Pagamento não foi processado</li>
        <li>🔄 Tente realizar uma nova compra</li>
        <li>📞 Entre em contato conosco se precisar de ajuda</li>
        @endif
      </ul>
    </div>

    <!-- Suporte -->
    <div class="support-section">
      <h3 class="support-title">Precisa de Ajuda?</h3>

      @if($checkout->whatsapp_suporte)
      <div class="support-info">
        <strong>WhatsApp:</strong>
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $checkout->whatsapp_suporte) }}" class="support-link">
          {{ $checkout->whatsapp_suporte }}
        </a>
      </div>
      @endif

      @if($checkout->email_suporte)
      <div class="support-info">
        <strong>Email:</strong>
        <a href="mailto:{{ $checkout->email_suporte }}" class="support-link">
          {{ $checkout->email_suporte }}
        </a>
      </div>
      @endif

      @if($checkout->periodo_garantia)
      <div class="support-info">
        <strong>Garantia:</strong> {{ $checkout->periodo_garantia }}
      </div>
      @endif
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>Este é um email automático do sistema {{ $checkout->produto_name ?? 'Gateway' }}.</p>
      <p>Guarde este email como comprovante da sua compra.</p>
      <p>Para suporte, entre em contato através dos canais informados acima.</p>
    </div>
  </div>
</body>

</html>