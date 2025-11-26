@extends('layouts.obrigado')

@section('title', 'Pagamento Confirmado')

@section('content')
<div class="container py-5">
  @if($order && $order->id === 999)
    <!-- Preview Mode -->
    <div class="alert alert-info text-center mb-4">
      <h5><i class="fas fa-eye me-2"></i>Modo Preview</h5>
      <p class="mb-0">Esta é uma visualização da página de obrigado padrão com dados de exemplo.</p>
    </div>
  @endif
  
  @if(!isset($order) || !$order || $order->status !== 'pago')
    <div class="alert alert-warning text-center">
      <h4>Pedido não encontrado ou pagamento pendente</h4>
      <p>Por favor, verifique o status do seu pedido.</p>
      <a href="{{ url()->previous() }}" class="btn btn-primary">Voltar</a>
    </div>
  @else
    <!-- Cabeçalho de Sucesso -->
    <div class="text-center mb-5 success-header">
      <div class="success-icon mb-4">
        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="60" cy="60" r="60" fill="#28a745"/>
          <path d="M35 60L52 77L85 44" stroke="white" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <h1 class="display-4 fw-bold text-success mb-2">Pagamento Confirmado!</h1>
      <p class="lead text-muted">Obrigado pela sua compra, {{ e($order->name ?? 'Cliente') }}</p>
    </div>

    <!-- Resumo da Compra -->
    <div class="row justify-content-center mb-4">
      <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-receipt"></i> Resumo da Compra</h5>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <p class="mb-2"><strong>Produto:</strong></p>
                <p class="text-muted">{{ e($checkout->produto_name) }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-2"><strong>Valor Total:</strong></p>
                <p class="text-success fs-4 fw-bold">R$ {{ number_format($order->valor_total ?? 0, 2, ',', '.') }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-md-6 mb-3">
                <p class="mb-1"><strong>ID da Transação:</strong></p>
                <p class="text-muted small">{{ e($order->idTransaction) }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <p class="mb-1"><strong>Data da Compra:</strong></p>
                <p class="text-muted">{{ $order && $order->created_at ? $order->created_at->format('d/m/Y H:i') : '' }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <p class="mb-1"><strong>Email:</strong></p>
                <p class="text-muted">{{ e($order->email) }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <p class="mb-1"><strong>CPF:</strong></p>
                <p class="text-muted">{{ e($order->cpf) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Próximos Passos -->
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-list-check"></i> Próximos Passos</h5>
          </div>
          <div class="card-body">
            @if($checkout->produto_tipo === 'digital')
              <div class="alert alert-info mb-3">
                <i class="bi bi-envelope-check"></i>
                <strong>Produto Digital:</strong> Você receberá um email em <strong>{{ e($order->email) }}</strong> com instruções de acesso ao produto.
              </div>
            @else
              <div class="alert alert-info mb-3">
                <i class="bi bi-truck"></i>
                <strong>Produto Físico:</strong> Seu produto será enviado em até <strong>2 dias úteis</strong> para o endereço cadastrado.
              </div>
            @endif

            @if(!empty($checkout->periodo_garantia))
              <div class="alert alert-success">
                <i class="bi bi-shield-check"></i>
                <strong>Garantia:</strong> Este produto possui garantia de <strong>{{ e($checkout->periodo_garantia) }}</strong>.
              </div>
            @endif

            <p class="text-muted mb-0">
              <i class="bi bi-info-circle"></i> 
              Um email de confirmação foi enviado para {{ e($order->email) }} com todos os detalhes da sua compra.
            </p>
          </div>
        </div>

        <!-- Comprovantes -->
        <div class="card shadow-sm mb-4">
          <div class="card-body text-center">
            <h5 class="mb-3">Comprovante de Pagamento</h5>
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
              <button class="btn btn-outline-primary" onclick="downloadComprovante()">
                <i class="bi bi-download"></i> Baixar Comprovante
              </button>
              <button class="btn btn-outline-secondary" onclick="enviarPorEmail()">
                <i class="bi bi-envelope"></i> Enviar por Email
              </button>
            </div>
          </div>
        </div>

        <!-- Suporte -->
        @if(!empty($checkout->whatsapp_suporte) || !empty($checkout->email_suporte))
          <div class="card shadow-sm">
            <div class="card-body text-center">
              <h5 class="mb-3">Precisa de Ajuda?</h5>
              <p class="text-muted mb-3">Nossa equipe está pronta para atendê-lo</p>
              <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                @if(!empty($checkout->whatsapp_suporte))
                  <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $checkout->whatsapp_suporte) }}?text=Olá, preciso de ajuda com o pedido {{ e($order->id) }}" 
                    class="btn btn-success" target="_blank">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                  </a>
                @endif
                @if(!empty($checkout->email_suporte))
                  <a href="mailto:{{ e($checkout->email_suporte) }}?subject=Dúvida sobre pedido {{ e($order->id) }}" 
                    class="btn btn-outline-primary">
                    <i class="bi bi-envelope"></i> Email
                  </a>
                @endif
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  @endif
</div>
@endsection

@section('styles')
<style>
  .success-header {
    animation: fadeInDown 0.8s ease-out;
  }

  .success-icon {
    animation: scaleIn 0.6s ease-out;
  }

  @keyframes fadeInDown {
    from {
      opacity: 0;
      transform: translateY(-30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes scaleIn {
    from {
      opacity: 0;
      transform: scale(0.5);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: fadeInUp 0.8s ease-out;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .btn {
    transition: all 0.3s ease;
  }

  .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  }

  .alert {
    border-left: 4px solid;
  }

  .alert-info {
    border-left-color: #0dcaf0;
  }

  .alert-success {
    border-left-color: #28a745;
  }
</style>
@endsection

@section('scripts')
<script>
  // Dados do pedido para JavaScript
  const orderData = {
    id: '{{ e($order->id ?? '') }}',
    idTransaction: '{{ e($order->idTransaction ?? '') }}',
    name: '{{ e($order->name ?? '') }}',
    email: '{{ e($order->email ?? '') }}',
    valor_total: '{{ $order->valor_total ?? 0 }}',
    produto_name: '{{ e($checkout->produto_name ?? '') }}',
    created_at: '{{ $order && $order->created_at ? $order->created_at->format('d/m/Y H:i') : '' }}'
  };

  // Função para download de comprovante
  function downloadComprovante() {
    // Criar conteúdo do comprovante
    const comprovanteContent = `
      ========================================
      COMPROVANTE DE PAGAMENTO
      ========================================
      
      Produto: ${orderData.produto_name}
      Valor: R$ ${parseFloat(orderData.valor_total).toFixed(2).replace('.', ',')}
      
      Cliente: ${orderData.name}
      Email: ${orderData.email}
      
      ID Transação: ${orderData.idTransaction}
      Data: ${orderData.created_at}
      
      Status: PAGO
      
      ========================================
      Obrigado pela sua compra!
      ========================================
    `;

    // Criar blob e fazer download
    const blob = new Blob([comprovanteContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `comprovante-${orderData.idTransaction}.txt`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);

    // Tracking
    trackEvent('download_comprovante', {
      order_id: orderData.id,
      transaction_id: orderData.idTransaction
    });
  }

  // Função para enviar por email
  function enviarPorEmail() {
    fetch('/api/enviar-comprovante', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        order_id: orderData.id,
        email: orderData.email
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Comprovante enviado com sucesso para ' + orderData.email);
        trackEvent('email_comprovante', {
          order_id: orderData.id
        });
      } else {
        alert('Erro ao enviar comprovante. Tente novamente.');
      }
    })
    .catch(error => {
      console.error('Erro:', error);
      alert('Erro ao enviar comprovante. Tente novamente.');
    });
  }

  // Função de tracking genérica
  function trackEvent(eventName, params = {}) {
    // Facebook Pixel
    if (typeof fbq !== 'undefined') {
      fbq('track', 'Purchase', {
        value: parseFloat(orderData.valor_total),
        currency: 'BRL',
        content_name: orderData.produto_name,
        content_ids: [orderData.id],
        content_type: 'product'
      });
    }

    // Google Analytics
    if (typeof gtag !== 'undefined') {
      gtag('event', eventName, {
        ...params,
        value: parseFloat(orderData.valor_total),
        currency: 'BRL',
        transaction_id: orderData.idTransaction
      });
    }

    // Google Analytics 4 - Purchase Event
    if (typeof gtag !== 'undefined' && eventName === 'purchase') {
      gtag('event', 'purchase', {
        transaction_id: orderData.idTransaction,
        value: parseFloat(orderData.valor_total),
        currency: 'BRL',
        items: [{
          item_id: orderData.id,
          item_name: orderData.produto_name,
          price: parseFloat(orderData.valor_total),
          quantity: 1
        }]
      });
    }
  }

  // Tracking de conversão ao carregar a página
  document.addEventListener('DOMContentLoaded', function() {
    @if(isset($order) && $order->status === 'pago')
      // Facebook Pixel - Purchase
      trackEvent('purchase', {
        order_id: orderData.id,
        transaction_id: orderData.idTransaction
      });

      // Enviar email de confirmação automático
      setTimeout(function() {
        fetch('/api/enviar-confirmacao', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            order_id: orderData.id
          })
        });
      }, 1000);
    @endif
  });
</script>
@endsection
