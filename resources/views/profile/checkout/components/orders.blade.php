@props([
  'checkout' => []
])

<div class="card">
  <div class="flex justify-between align-middle card-header">
   Pedidos
  </div>
  <div class="card-body">
    <table id="table-checkout-pedidos" class="table">
      <thead>
       <tr>
        <th scope="col">ID do Pedido</th>
        <th scope="col">Nome do cliente</th>
        <th scope="col">CPF do cliente</th>
        <th scope="col">Telefone do cliente</th>
        <th scope="col">Email do cliente</th>
        <th scope="col">Endereço</th>
        <th scope="col">Valor da compra</th>
        <th scope="col">Status</th>
       </tr>
      </thead>
      <tbody>
        {{-- {{dd($checkout->bumps)}} --}}
        @foreach($checkout->orders as $order)
        <tr class="order-row" data-order-id="{{ $order->id }}" style="cursor: pointer;">
          <td>
            <span class="badge bg-primary">{{ $order->id }}</span>
          </td>
          <td>{{ $order->name }}</td>
          <td>{{ $order->cpf }}</td>
          <td>{{ $order->telefone }}</td>
          <td>{{ $order->email }}</td>
          <td>
          @if($checkout->produto_tipo == 'fisico')
            {{ $order->endereco.', Nº'.$order->numero.' '.$order->bairro.', '.$order->cidade.'-'.$order->estado.'CEP: '.$order->cep }}</td>
          @else
            ---
          @endif
          <td>{{"R$".number_format($order->valor_total, '2',',','.') }}</td>
          <td>
            @if($order->status == 'pago')
              <span class="badge text-bg-success">Pago ✅</span>
            @elseif($order->status == 'cancelado')
              <span class="badge text-bg-danger">Cancelado ❌</span>
            @elseif($order->status == 'pendente')
              <span class="badge text-bg-warning">Pendente ⏳</span>
            @elseif($order->status == 'gerado')
              <span class="badge text-bg-info">Aguardando Pagamento 🕒</span>
            @elseif($order->status == 'reembolsado')
              <span class="badge text-bg-secondary">Reembolsado 💰</span>
            @elseif($order->status == 'disputado')
              <span class="badge text-bg-warning">Disputado ⚠️</span>
            @elseif($order->status == 'chargeback')
              <span class="badge text-bg-danger">Chargeback 🔴</span>
            @elseif($order->status == 'disputa_ganha')
              <span class="badge text-bg-success">Disputa Ganha ✅</span>
            @elseif($order->status == 'disputa_perdida')
              <span class="badge text-bg-danger">Disputa Perdida ❌</span>
            @elseif($order->status == 'fraudulento')
              <span class="badge text-bg-dark">Fraudulento 🚫</span>
            @elseif($order->status == 'med')
              <span class="badge text-bg-dark">MED 🔍</span>
            @else
              <span class="badge text-bg-secondary">{{ ucfirst($order->status) }}</span>
            @endif
          </td>

        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
  </div>
</div>
<script>
  // Aguardar o jQuery estar disponível
  function waitForJQuery(callback) {
    if (typeof $ !== 'undefined') {
      callback();
    } else {
      setTimeout(function() {
        waitForJQuery(callback);
      }, 100);
    }
  }

  waitForJQuery(function() {
    document.addEventListener("DOMContentLoaded", function() {
      var table = null; // Declarar variável no escopo correto
      
      // Verificar se DataTable está disponível
      if (typeof $.fn.DataTable === 'undefined') {
        console.warn('DataTable não está disponível, usando tabela simples');
        inicializarTabelaSimples();
      } else {
        console.log('DataTable disponível, inicializando...');
        // Inicializar DataTable
        try {
          table = $("#table-checkout-pedidos").DataTable({
            responsive: true,
            language: {
              url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            }
          });
          console.log('DataTable inicializado com sucesso:', table);
        } catch (error) {
          console.error('Erro ao inicializar DataTable:', error);
          console.log('Usando tabela simples como fallback');
          inicializarTabelaSimples();
        }
      }
      
      // Função para inicializar tabela simples (sem DataTable)
      function inicializarTabelaSimples() {
        console.log('Inicializando tabela simples...');
        // Adicionar estilos básicos para a tabela
        $('#table-checkout-pedidos').addClass('table table-striped table-hover');
        console.log('Tabela simples inicializada');
      }
      
      // Função para adicionar eventos de clique
      function adicionarEventosClique() {
        console.log('Adicionando eventos de clique...');
        
        // Método 1: Evento direto no tbody (sempre funciona)
        $('#table-checkout-pedidos tbody').on('click', 'tr.order-row', function(e) {
          console.log('Clique detectado via evento direto no tbody');
          processarCliqueLinha(this, e);
        });
        
        // Método 2: Evento usando DataTable API (se disponível)
        if (typeof $.fn.DataTable !== 'undefined' && typeof table !== 'undefined') {
          try {
            table.on('click', 'tr.order-row', function(e) {
              console.log('Clique detectado via DataTable API');
              processarCliqueLinha(this, e);
            });
          } catch (error) {
            console.warn('Erro ao adicionar evento DataTable:', error);
          }
        }
        
        // Método 3: Evento direto nas linhas (fallback)
        $('#table-checkout-pedidos tbody tr.order-row').on('click', function(e) {
          console.log('Clique detectado via evento direto nas linhas');
          processarCliqueLinha(this, e);
        });
        
        console.log('Eventos de clique adicionados');
      }

      // Função para processar clique na linha
      function processarCliqueLinha(elemento, evento) {
        if (evento) {
          evento.preventDefault();
          evento.stopPropagation();
        }
        
        var orderId = $(elemento).data('order-id');
        console.log('=== CLIQUE EM LINHA DETECTADO ===');
        console.log('Elemento clicado:', elemento);
        console.log('OrderId encontrado:', orderId);
        console.log('Classes da linha:', $(elemento).attr('class'));
        console.log('Data attributes:', $(elemento).data());
        
        if (orderId) {
          console.log('Chamando carregarDetalhesPedido com ID:', orderId);
          carregarDetalhesPedido(orderId);
        } else {
          console.error('OrderId não encontrado na linha clicada');
          console.log('Tentando encontrar orderId de outras formas...');
          
          // Tentar encontrar o ID de outras formas
          var badgeText = $(elemento).find('.badge').text();
          console.log('Texto do badge:', badgeText);
          
          if (badgeText && !isNaN(badgeText)) {
            console.log('Usando ID do badge:', badgeText);
            carregarDetalhesPedido(badgeText);
          } else {
            alert('Erro: Não foi possível identificar o ID do pedido');
          }
        }
      }
      
      // Adicionar eventos de clique nas linhas da tabela
      adicionarEventosClique();
      
      // Verificar se há linhas na tabela
      var rowCount = $('#table-checkout-pedidos tbody tr').length;
      console.log('Número de linhas na tabela:', rowCount);
      
      if (rowCount === 0) {
        console.warn('Nenhuma linha encontrada na tabela de pedidos');
      }
    });
  });

  // Função para carregar detalhes do pedido
  function carregarDetalhesPedido(orderId) {
    console.log('=== INICIANDO CARREGAMENTO DE DETALHES ===');
    console.log('OrderId recebido:', orderId);
    console.log('Tipo do orderId:', typeof orderId);
    
    // Verificar se o orderId é válido
    if (!orderId || orderId === 'undefined' || orderId === 'null') {
      console.error('OrderId inválido:', orderId);
      alert('Erro: ID do pedido inválido');
      return;
    }
    
    // Construir URL
    var url = '/produtos/pedido/detalhes/' + orderId;
    console.log('URL da requisição:', url);
    
    // Verificar CSRF token
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    console.log('CSRF Token:', csrfToken ? 'Encontrado' : 'Não encontrado');
    
    $.ajax({
      url: url,
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': csrfToken
      },
      beforeSend: function() {
        console.log('Enviando requisição AJAX...');
      },
      success: function(response) {
        console.log('=== RESPOSTA RECEBIDA ===');
        console.log('Resposta completa:', response);
        console.log('Tipo da resposta:', typeof response);
        
        if (response && response.success) {
          console.log('Dados do pedido:', response.order);
          preencherModalPedido(response.order);
          
          // Tentar abrir o modal usando Bootstrap 5
          try {
            const modalElement = document.getElementById('detalhesPedidoModal');
            if (modalElement) {
              const modal = new bootstrap.Modal(modalElement);
              modal.show();
              console.log('✅ Modal aberto com sucesso!');
            } else {
              console.error('❌ Elemento modal não encontrado');
              alert('Erro: Modal não encontrado');
            }
          } catch (error) {
            console.error('❌ Erro ao abrir modal:', error);
            // Fallback para jQuery modal (Bootstrap 4)
            try {
              $('#detalhesPedidoModal').modal('show');
              console.log('✅ Modal aberto via jQuery (fallback)');
            } catch (fallbackError) {
              console.error('❌ Erro no fallback:', fallbackError);
              alert('Erro ao abrir modal: ' + error.message);
            }
          }
        } else {
          console.error('❌ Resposta não indica sucesso:', response);
          alert('Erro ao carregar detalhes: ' + (response.message || 'Erro desconhecido'));
        }
      },
      error: function(xhr, status, error) {
        console.error('=== ERRO NA REQUISIÇÃO AJAX ===');
        console.error('Status HTTP:', xhr.status);
        console.error('Status:', status);
        console.error('Error:', error);
        console.error('Response Text:', xhr.responseText);
        console.error('Response Headers:', xhr.getAllResponseHeaders());
        
        var errorMessage = 'Erro ao carregar detalhes do pedido';
        
        if (xhr.status === 404) {
          errorMessage = 'Pedido não encontrado (404)';
        } else if (xhr.status === 403) {
          errorMessage = 'Acesso negado (403)';
        } else if (xhr.status === 500) {
          errorMessage = 'Erro interno do servidor (500)';
        }
        
        alert(errorMessage + '. Verifique o console para mais detalhes.');
      }
    });
  }

  // Função para preencher o modal com os dados do pedido
  function preencherModalPedido(dados) {
    // Resumo
    $('#modal-pedido-valor').text('R$ ' + parseFloat(dados.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2}));
    $('#modal-pedido-produto').text(dados.produto_name || 'N/A');
    $('#modal-pedido-cliente').text(dados.name || 'N/A');
    
    // Detectar método de pagamento correto para o resumo
    function detectarMetodoPagamento(dados) {
      // Padrões de IDs para pagamentos via CARTÃO
      const padroesCartao = [
        // Stripe
        'pi_', 'ch_', 'pm_', 'src_',
        // Rede
        'rede_', 'rd_', 'rde_',
        // PrimePay
        'pp_', 'prime_', 'primepay_',
        // PagarMe
        'pg_', 'pagar_', 'pagarme_',
        // Cielo
        'cielo_', 'cl_',
        // Stone
        'stone_', 'st_',
        // GetNet
        'getnet_', 'gn_',
        // Mercado Pago
        'mp_', 'mercadopago_',
        // Outros padrões comuns de cartão
        'card_', 'credit_', 'debit_', 'visa_', 'master_', 'amex_'
      ];
      
      // Padrões de IDs para pagamentos via PIX
      const padroesPix = [
        // Padrões comuns de PIX
        'dep_', 'pix_', 'pixup_', 'xdpag_', 'bspay_',
        // Outros adquirentes PIX
        'pixpay_', 'pixgo_', 'pixnow_', 'pixpro_', 'pixflow_', 'pixnet_', 'pixmax_',
        // Padrões genéricos
        'qr_', 'qrcode_', 'instant_', 'immediate_'
      ];
      
      let isCardPayment = false;
      let isPixPayment = false;
      
      if (dados.idTransaction) {
        const idLower = dados.idTransaction.toLowerCase();
        
        // Verificar padrões de cartão
        for (const padrao of padroesCartao) {
          if (idLower.startsWith(padrao)) {
            isCardPayment = true;
            break;
          }
        }
        
        // Verificar padrões de PIX
        for (const padrao of padroesPix) {
          if (idLower.startsWith(padrao)) {
            isPixPayment = true;
            break;
          }
        }
        
        // Verificar palavras-chave PIX
        if (!isPixPayment && (
          idLower.includes('pix') ||
          idLower.includes('qr') ||
          idLower.includes('instant')
        )) {
          isPixPayment = true;
        }
      }
      
      // Fallbacks baseados em outros dados
      if (!isCardPayment && !isPixPayment) {
        if (dados.credit_card && dados.credit_card !== null) {
          isCardPayment = true;
        } else if (dados.metodo === 'pix') {
          isPixPayment = true;
        } else if (dados.metodo === 'card') {
          isCardPayment = true;
        }
      }
      
      return { isCardPayment, isPixPayment };
    }
    
    const { isCardPayment, isPixPayment } = detectarMetodoPagamento(dados);
    
    let metodoTexto = 'N/A';
    if (isCardPayment) {
      metodoTexto = 'Cartão';
    } else if (isPixPayment) {
      metodoTexto = 'PIX';
    } else if (dados.metodo === 'boleto') {
      metodoTexto = 'Boleto';
    } else {
      metodoTexto = dados.metodo || 'N/A';
    }
    
    $('#modal-pedido-metodo').text(metodoTexto);
    
    // Status
    const statusText = getStatusTextPedido(dados.status);
    const statusClass = getStatusClassPedido(dados.status);
    $('#modal-pedido-status').text(statusText).attr('class', 'badge ' + statusClass);
    
    // Dados do pedido
    $('#modal-pedido-id').text(dados.id);
    $('#modal-pedido-id-transacao').text(dados.idTransaction || 'N/A');
    $('#modal-pedido-data').text(dados.created_at);
    $('#modal-pedido-valor-detalhes').text('R$ ' + parseFloat(dados.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2}));
    
    // Informações do cliente
    $('#modal-pedido-nome').text(dados.name || 'N/A');
    $('#modal-pedido-cpf').text(dados.cpf || 'N/A');
    $('#modal-pedido-telefone').text(dados.telefone || 'N/A');
    $('#modal-pedido-email').text(dados.email || 'N/A');
    
    // Endereço (se produto físico)
    if (dados.produto_tipo === 'fisico') {
      $('#modal-pedido-endereco').text(
        dados.endereco + ', Nº' + dados.numero + ' ' + dados.bairro + ', ' + 
        dados.cidade + '-' + dados.estado + ' CEP: ' + dados.cep
      );
    } else {
      $('#modal-pedido-endereco').text('Produto Digital');
    }
    
    // Dados do pagamento - melhorar detecção do método
    console.log('Processando dados de pagamento:', dados);
    console.log('Método original:', dados.metodo_original);
    console.log('Método corrigido:', dados.metodo);
    console.log('Debug da detecção:', dados.detection_debug);
    
    // Usar a variável isCardPayment já declarada acima
    console.log('É pagamento via cartão?', isCardPayment);
    console.log('É pagamento via PIX?', isPixPayment);
    console.log('Método:', dados.metodo);
    console.log('ID Transação:', dados.idTransaction);
    console.log('Credit Card:', dados.credit_card);
    
    if (isCardPayment && dados.credit_card) {
      try {
        const cardData = JSON.parse(dados.credit_card);
        console.log('Dados do cartão:', cardData);
        $('#modal-pedido-dados-pagamento').html(`
          <div class="row">
            <div class="col-md-6">
              <strong>Bandeira:</strong> ${cardData.brand?.toUpperCase() || 'N/A'}
            </div>
            <div class="col-md-6">
              <strong>Número:</strong> **** **** **** ${cardData.number?.slice(-4) || '****'}
            </div>
            <div class="col-md-6">
              <strong>Portador:</strong> ${cardData.holder_name || 'N/A'}
            </div>
            <div class="col-md-6">
              <strong>Validade:</strong> ${cardData.expiration_month || '**'}/${cardData.expiration_year || '****'}
            </div>
          </div>
        `);
      } catch (error) {
        console.error('Erro ao processar dados do cartão:', error);
        $('#modal-pedido-dados-pagamento').html(`
          <div class="text-center">
            <i class="fas fa-credit-card fa-2x text-muted mb-2"></i>
            <p class="text-muted">Pagamento via Cartão</p>
            <small class="text-muted">ID: ${dados.idTransaction || 'N/A'}</small>
          </div>
        `);
      }
    } else if (isCardPayment) {
      // Pagamento via cartão mas sem dados detalhados
      $('#modal-pedido-dados-pagamento').html(`
        <div class="text-center">
          <i class="fas fa-credit-card fa-2x text-muted mb-2"></i>
          <p class="text-muted">Pagamento via Cartão</p>
          <small class="text-muted">ID: ${dados.idTransaction || 'N/A'}</small>
        </div>
      `);
    } else if (isPixPayment) {
      // Pagamento via PIX
      $('#modal-pedido-dados-pagamento').html(`
        <div class="text-center">
          <i class="fas fa-qrcode fa-2x text-muted mb-2"></i>
          <p class="text-muted">Pagamento via PIX</p>
          <small class="text-muted">ID: ${dados.idTransaction || 'N/A'}</small>
        </div>
      `);
    } else {
      // Pagamento via Boleto
      $('#modal-pedido-dados-pagamento').html(`
        <div class="text-center">
          <i class="fas fa-barcode fa-2x text-muted mb-2"></i>
          <p class="text-muted">Pagamento via Boleto</p>
          ${dados.idTransaction ? `<small class="text-muted">ID: ${dados.idTransaction}</small>` : ''}
        </div>
      `);
    }
    
    // Histórico de eventos
    $('#modal-pedido-historico').empty();
    if (dados.eventos && dados.eventos.length > 0) {
      dados.eventos.forEach(function(evento) {
        const statusText = getStatusTextPedido(evento.status);
        const statusClass = getStatusClassPedido(evento.status);
        $('#modal-pedido-historico').append(`
          <div class="d-flex align-items-center mb-2">
            <span class="badge me-2 ${statusClass}">${statusText}</span>
            <small class="text-muted">${evento.data || 'N/A'}</small>
          </div>
        `);
      });
    } else {
      $('#modal-pedido-historico').html('<p class="text-muted">Nenhum evento registrado</p>');
    }
  }

  // Funções auxiliares para status
  function getStatusTextPedido(status) {
    const statusMap = {
      'pago': 'PAGO',
      'cancelado': 'CANCELADO',
      'pendente': 'PENDENTE',
      'gerado': 'AGUARDANDO PAGAMENTO',
      'reembolsado': 'REEMBOLSADO',
      'disputado': 'DISPUTADO',
      'chargeback': 'CHARGEBACK',
      'disputa_ganha': 'DISPUTA GANHA',
      'disputa_perdida': 'DISPUTA PERDIDA',
      'fraudulento': 'FRAUDULENTO',
      'med': 'MED'
    };
    return statusMap[status] || status.toUpperCase();
  }

  function getStatusClassPedido(status) {
    const classMap = {
      'pago': 'text-bg-success',
      'cancelado': 'text-bg-danger',
      'pendente': 'text-bg-warning',
      'gerado': 'text-bg-info',
      'reembolsado': 'text-bg-secondary',
      'disputado': 'text-bg-warning',
      'chargeback': 'text-bg-danger',
      'disputa_ganha': 'text-bg-success',
      'disputa_perdida': 'text-bg-danger',
      'fraudulento': 'text-bg-dark',
      'med': 'text-bg-dark'
    };
    return classMap[status] || 'text-bg-secondary';
  }

  </script>

<script>
// Script adicional para garantir que o modal funcione corretamente
document.addEventListener('DOMContentLoaded', function() {
  console.log('Script de inicialização do modal carregado');
  
  // Verificar se o Bootstrap está disponível
  if (typeof bootstrap !== 'undefined') {
    console.log('Bootstrap 5 disponível');
  } else {
    console.warn('Bootstrap 5 não disponível, usando jQuery');
  }
  
  // Verificar se o jQuery está disponível
  if (typeof $ !== 'undefined') {
    console.log('jQuery disponível');
  } else {
    console.warn('jQuery não disponível');
  }
  
  // Verificar se o modal existe
  const modalElement = document.getElementById('detalhesPedidoModal');
  if (modalElement) {
    console.log('Elemento modal encontrado');
  } else {
    console.error('Elemento modal não encontrado!');
  }
  
  // Adicionar listener para debug
  document.addEventListener('click', function(e) {
    if (e.target.closest('.order-row')) {
      console.log('Clique detectado em linha de pedido');
    }
  });
});

</script>

<!-- Modal Detalhes do Pedido -->
<div class="modal fade" id="detalhesPedidoModal" tabindex="-1" aria-labelledby="detalhesPedidoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="detalhesPedidoModalLabel">
          <i class="fas fa-shopping-cart me-2"></i>Detalhes do Pedido
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body bg-light">
        <!-- Cards de Resumo -->
        <div class="row g-3 mb-4">
          <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm bg-success text-white h-100">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                      <i class="fas fa-dollar-sign fa-lg"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <small class="d-block opacity-75">VALOR TOTAL</small>
                    <h4 class="mb-0 fw-bold" id="modal-pedido-valor">R$ 0,00</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm bg-info text-white h-100">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                      <i class="fas fa-box fa-lg"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <small class="d-block opacity-75">PRODUTO</small>
                    <h6 class="mb-0 fw-bold" id="modal-pedido-produto">N/A</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm bg-warning text-white h-100">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                      <i class="fas fa-user fa-lg"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <small class="d-block opacity-75">CLIENTE</small>
                    <h6 class="mb-0 fw-bold" id="modal-pedido-cliente">N/A</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm bg-secondary text-white h-100">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                      <i class="fas fa-credit-card fa-lg"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <small class="d-block opacity-75">MÉTODO</small>
                    <h6 class="mb-0 fw-bold" id="modal-pedido-metodo">N/A</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Status -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card shadow-sm">
              <div class="card-body text-center">
                <h6 class="card-title text-muted mb-2">STATUS</h6>
                <span class="badge fs-6" id="modal-pedido-status">N/A</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Informações Detalhadas -->
        <div class="row g-4">
          <!-- Informações do Pedido -->
          <div class="col-lg-6">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white">
                <h6 class="card-title mb-0">
                  <i class="fas fa-receipt me-2 text-primary"></i>Dados do Pedido
                </h6>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-12">
                    <div class="d-flex justify-content-between">
                      <span class="text-muted">ID do Pedido:</span>
                      <strong id="modal-pedido-id">N/A</strong>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-flex justify-content-between">
                      <span class="text-muted">ID Transação:</span>
                      <strong id="modal-pedido-id-transacao">N/A</strong>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-flex justify-content-between">
                      <span class="text-muted">Data:</span>
                      <strong id="modal-pedido-data">N/A</strong>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-flex justify-content-between">
                      <span class="text-muted">Valor:</span>
                      <strong id="modal-pedido-valor-detalhes">R$ 0,00</strong>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Informações do Cliente -->
          <div class="col-lg-6">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white">
                <h6 class="card-title mb-0">
                  <i class="fas fa-user me-2 text-primary"></i>Informações do Cliente
                </h6>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-12">
                    <div class="d-flex justify-content-between">
                      <span class="text-muted">Nome:</span>
                      <strong id="modal-pedido-nome">N/A</strong>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-flex justify-content-between">
                      <span class="text-muted">CPF:</span>
                      <strong id="modal-pedido-cpf">N/A</strong>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-flex justify-content-between">
                      <span class="text-muted">Telefone:</span>
                      <strong id="modal-pedido-telefone">N/A</strong>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-flex justify-content-between">
                      <span class="text-muted">Email:</span>
                      <strong id="modal-pedido-email">N/A</strong>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-flex justify-content-between">
                      <span class="text-muted">Endereço:</span>
                      <strong id="modal-pedido-endereco">N/A</strong>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Dados do Pagamento -->
          <div class="col-lg-6">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white">
                <h6 class="card-title mb-0">
                  <i class="fas fa-credit-card me-2 text-primary"></i>Dados do Pagamento
                </h6>
              </div>
              <div class="card-body">
                <div id="modal-pedido-dados-pagamento">
                  <!-- Conteúdo será preenchido via JavaScript -->
                </div>
              </div>
            </div>
          </div>

          <!-- Histórico -->
          <div class="col-lg-6">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-white">
                <h6 class="card-title mb-0">
                  <i class="fas fa-history me-2 text-primary"></i>Histórico de Eventos
                </h6>
              </div>
              <div class="card-body">
                <div id="modal-pedido-historico">
                  <!-- Conteúdo será preenchido via JavaScript -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-white">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i>Fechar
        </button>
      </div>
    </div>
  </div>
</div>
