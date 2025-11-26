<!-- Modal Adicionar Saldo -->
<div class="modal fade" id="addsaldo" tabindex="-1" aria-labelledby="addsaldoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-semibold" id="addsaldoLabel">
          <i class="fas fa-plus-circle me-2"></i>Adicionar Saldo
        </h5>
        <button id="btnDepositar" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="depositForm" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="valor_deposito" class="form-label">Valor do Depósito</label>
            <div class="input-group">
              <span class="input-group-text">R$</span>
              <input type="text" class="form-control" id="valor_deposito" name="valor" placeholder="0,00" required>
            </div>
            <div class="form-text">
              <i class="fas fa-info-circle"></i> Informe o valor que deseja depositar
            </div>
          </div>
        </div>
        <div class="modal-footer gap-3">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Cancelar
          </button>
          <button id="btn-depositar" type="submit" class="btn btn-success">
            <i class="fas fa-qrcode me-1"></i>Gerar QR Code
          </button>
        </div>
      </form>

      <div id="data-qrcode" class="p-4 text-center" style="display: none;">
        <img id="pix-qr-code" width="200" height="200" class="mb-3 rounded" />
        <div class="mb-3">
          <label class="form-label fw-semibold">Código PIX Copia e Cola</label>
          <input id="pix-copia-e-cola" class="form-control text-center" readonly />
        </div>
        <button class="btn btn-primary w-100" onclick="copiarTexto()">
          <i class="fas fa-copy me-2"></i>Copiar Código PIX
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  function copiarTexto() {
    var input = document.getElementById("pix-copia-e-cola");
    input.select();
    document.execCommand("copy");
    showToast("success","Chave Pix copiada!");
  }

  // Helper para formatação de moeda brasileira
  function formatarMoeda(valor) {
    // Remove tudo que não é dígito
    let apenasNumeros = valor.replace(/\D/g, '');
    
    // Se não há números, retorna vazio
    if (!apenasNumeros) return '';
    
    // Converte para número e divide por 100 para obter os centavos
    let numero = parseInt(apenasNumeros) / 100;
    
    // Formata no padrão brasileiro
    return numero.toLocaleString('pt-BR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  // Helper para converter formato brasileiro para numérico
  function converterParaNumerico(valorFormatado) {
    // Remove pontos (separadores de milhares) e substitui vírgula por ponto
    return valorFormatado.replace(/\./g, '').replace(',', '.');
  }

  // Aplicar formatação automática no input de valor
  document.addEventListener('DOMContentLoaded', function() {
    const valorInput = document.getElementById('valor_deposito');
    
    if (valorInput) {
      // Formatação durante a digitação
      valorInput.addEventListener('input', function(e) {
        let valor = e.target.value;
        let valorFormatado = formatarMoeda(valor);
        e.target.value = valorFormatado;
      });

      // Formatação ao colar texto
      valorInput.addEventListener('paste', function(e) {
        setTimeout(() => {
          let valor = e.target.value;
          let valorFormatado = formatarMoeda(valor);
          e.target.value = valorFormatado;
        }, 10);
      });

      // Formatação ao perder o foco (caso o usuário tenha digitado manualmente)
      valorInput.addEventListener('blur', function(e) {
        let valor = e.target.value;
        if (valor && valor !== '0,00') {
          let valorFormatado = formatarMoeda(valor);
          e.target.value = valorFormatado;
        }
      });
    }
  });
</script>

<script>
  document.getElementById('depositForm').addEventListener('submit', function(event) {
    event.preventDefault();
    let btnDepositar = document.getElementById('btn-depositar');
    btnDepositar.setAttribute('disabled', true);
    var paymentCode;
    var transactionId;
    generateQRCode();
    async function generateQRCode() {
      var name ="{{ auth()->user()->name }}";
      var cpf ="{{ auth()->user()->cpf_cnpj }}";
      var email ="{{ auth()->user()->email }}";
      var amountFormatted = document.getElementById('valor_deposito').value;
      var amount = converterParaNumerico(amountFormatted);
      
      // Validar se o valor é válido
      if (!amount || parseFloat(amount) <= 0) {
        alert('Por favor, informe um valor válido para o depósito.');
        btnDepositar.removeAttribute('disabled');
        return;
      }
      
      // Verificar se o CPF está preenchido
      if (!cpf || cpf.trim() === '') {
        alert('Erro: CPF/CNPJ não preenchido. Por favor, complete seu perfil antes de fazer depósitos.');
        btnDepositar.removeAttribute('disabled');
        return;
      }
      var apiUrl ="{{ env('APP_URL') }}/api/wallet/deposit/payment";
      var token ="{{ auth()->user()->chaves->token }}";
      var secret ="{{ auth()->user()->chaves->secret }}";
      var phone ="{{ auth()->user()->telefone }}";
      var payload = {
        "token": token,
        "secret": secret,
        "amount": parseFloat(amount),
        "debtor_name": name,
        "email": email,
        "debtor_document_number": cpf,
        "phone": phone,
        "method_pay":"pix",
        "postback":"web"
      };
      try {
        const response = await fetch(apiUrl, {
          method:"POST",
          headers: {
            "Content-Type":"application/json",
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();
        console.log('Resposta padronizada:', data);
        
        // Agora todas as respostas seguem o mesmo padrão!
        if (data.status === 'success' && data.qr_code_image_url) {
          console.log('Processando dados do PIX...');
          paymentCode = data.qrcode || data.qr_code; // Código PIX para copia e cola
          transactionId = data.idTransaction || data.transaction_id;

          console.log('Dados do PIX:', {
            paymentCode: paymentCode,
            transactionId: transactionId,
            amount: data.amount || amount
          });

          // Configurar QR Code e código PIX
          document.getElementById('pix-qr-code').src = data.qr_code_image_url;
          document.getElementById('pix-copia-e-cola').value = paymentCode;
          
          // Adicionar valor do depósito acima do QR Code
          let valorElement = document.getElementById('valor-deposito-display');
          if (!valorElement) {
            valorElement = document.createElement('div');
            valorElement.id = 'valor-deposito-display';
            valorElement.className = 'mb-3 text-center';
            valorElement.style.fontSize = '1.2rem';
            valorElement.style.fontWeight = 'bold';
            valorElement.style.color = '#28a745';
            document.getElementById('data-qrcode').insertBefore(valorElement, document.getElementById('pix-qr-code'));
          }
          valorElement.innerHTML = `Valor do Depósito: R$ ${parseFloat(data.amount || amount).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
          
          console.log('QR code configurado:', {
            src: data.qr_code_image_url,
            paymentCode: paymentCode,
            amount: data.amount || amount
          });

          console.log('Ocultando formulário e exibindo modal...');
          document.getElementById('depositForm').style.display = 'none';
          let pixcontainer = document.getElementById('data-qrcode');
          pixcontainer.style.display = 'flex';
          pixcontainer.style.flexDirection ="column";
          pixcontainer.style.alignItems ="center";
          pixcontainer.style.justifyContent ="center";
          pixcontainer.style.gap = 5;

          console.log('Modal PIX exibido com sucesso!');
          // Inicia a verificação do pagamento a cada 5 segundos
          setInterval(checkPaymentStatus, 5000);
        } else {
          btnDepositar.removeAttribute('disabled');
          console.error("Erro na solicitação:", data.message);
          alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
      } catch (error) {
        btnDepositar.setAttribute('disabled', false);
        console.error("Erro na solicitação:", error);
      }
    }

    async function checkPaymentStatus() {
      var apiUrl ="{{env('APP_URL')}}/api/status";
      var payload = {
        "idTransaction": transactionId
      };

      try {
        const response = await fetch(apiUrl, {
          method:"POST",
          headers: {
            "Content-Type":"application/json",
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.status ==="PAID_OUT") {
          clearInterval(checkPaymentStatus); // Para a verificação quando o pagamento for confirmado

          showToast('success',"Saldo adcionado com sucesso!")
          setTimeout(() => {
            window.location.reload();
          }, 3000)
        } else if (data.status ==="WAITING_FOR_APPROVAL") {
          console.log("Aguardando aprovação...");
        }
      } catch (error) {
        console.error("Erro na verificação do pagamento:", error);
      }
    }
  })
</script>
