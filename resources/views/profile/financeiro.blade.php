@php
use App\Helpers\Helper;
Helper::calculaSaldoLiquido(auth()->user()->user_id);
$setting = Helper::getSetting();

auth()->user()->fresh();

@endphp
<x-app-layout :route="'Financeiro'">



  <div class="main-content app-content">
    <div class="container-fluid">
      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12 mb-4">
          <div class="card border-primary">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-primary text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-wallet fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Dashboard Financeiro</h1>
                  <p class="text-muted mb-0">Gerencie seus saldos, depósitos e saques</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Saldo Cards -->
      <div class="row g-3 mb-4">
        <!-- Saldo Total -->
        <div class="col-lg-6 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fas fa-wallet"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Saldo Total</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.5rem;">R$ {{ number_format(auth()->user()->saldo + auth()->user()->valor_saque_pendente ?? 0, 2, ',', '.') }}</h4>
                  <div class="d-inline-flex align-items-center mt-1" style="font-size: 0.7rem;">
                    <i class="fa-solid fa-chart-line text-success me-1"></i>
                    <span class="text-success">Disponível + Pendente</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Disponível para Saque -->
        <div class="col-lg-6 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Disponível para Saque</p>
                  <h4 class="fw-bold mb-0 text-info text-truncate" style="font-size: 1.5rem;">R$ {{ number_format(auth()->user()->saldo ?? 0, 2, ',', '.') }}</h4>
                  <div class="d-inline-flex align-items-center mt-1" style="font-size: 0.7rem;">
                    <i class="fa-solid fa-money-bill-wave text-info me-1"></i>
                    <span class="text-info">Líquido para Saque</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Cards -->
      <div class="row g-3 mb-4">
        <!-- Depósito -->
        <div class="col-lg-6 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle-modern bg-gradient-primary flex-shrink-0">
                  <i class="fas fa-plus"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <h5 class="text-primary fw-bold mb-0" style="font-size: 1.1rem;">Depósito</h5>
                  <p class="text-muted small mb-0" style="font-size: 0.75rem;">Adicionar Saldo</p>
                  <div class="d-inline-flex align-items-center mt-1" style="font-size: 0.7rem;">
                    <i class="fa-brands fa-pix text-primary me-1"></i>
                    <span class="text-primary">PIX Rápido e Seguro</span>
                  </div>
                </div>
              </div>
              <button class="btn btn-primary w-100"
                data-bs-toggle="modal"
                data-bs-target="#addsaldo">
                <i class="fas fa-credit-card me-2"></i>
                Depositar Agora
              </button>
            </div>
          </div>
        </div>

        <!-- Saque -->
        <div class="col-lg-6 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fas fa-arrow-up"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <h5 class="text-success fw-bold mb-0" style="font-size: 1.1rem;">Saque</h5>
                  <p class="text-muted small mb-0" style="font-size: 0.75rem;">Retirar Saldo</p>
                  <div class="d-inline-flex align-items-center mt-1" style="font-size: 0.7rem;">
                    <i class="fa-solid fa-money-bill-wave text-success me-1"></i>
                    <span class="text-success">PIX/Crypto Seguro</span>
                  </div>
                </div>
              </div>
              <button class="btn btn-success w-100"
                data-bs-toggle="modal"
                data-bs-target="{{ is_null($networks) ? '#saquepix' : '#modalSelMoeda' }}"
                data-saldo="{{ $saldoliquido }}">
                <i class="fas fa-money-bill-wave me-2"></i>
                Sacar Agora
              </button>
            </div>
          </div>
        </div>
      </div>


      <!-- Modal Selecionar Modalidade -->
      <div class="modal fade" id="modalSelMoeda" tabindex="-1" aria-labelledby="modalSelMoedaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-semibold" id="modalSelMoedaLabel">
                <i class="fas fa-exchange-alt me-2"></i>Selecione a Modalidade de Saque
              </h5>
              <button id="btn-c-mod" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
              <div class="row g-3">
                <div class="col-6">
                  <button type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#saquepix"
                    onclick="document.getElementById('btn-c-mod').click()"
                    class="btn btn-outline-primary w-100 d-flex flex-column align-items-center justify-content-center"
                    style="height: 140px;">
                    <i class="fa-brands fa-pix fa-3x mb-2"></i>
                    <span class="fw-semibold">PIX</span>
                  </button>
                </div>
                <div class="col-6">
                  <button type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#saquecrypto"
                    onclick="document.getElementById('btn-c-mod').click()"
                    class="btn btn-outline-primary w-100 d-flex flex-column align-items-center justify-content-center"
                    style="height: 140px;">
                    <i class="fab fa-bitcoin fa-3x mb-2"></i>
                    <span class="fw-semibold">CRYPTO</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>




      <!-- Modal Saque Crypto -->
      <div class="modal fade" id="saquecrypto" tabindex="-1" aria-labelledby="saquecryptoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-semibold" id="saquecryptoLabel">
                <i class="fab fa-bitcoin me-2"></i>Saque Crypto
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saqueFormCrypto" method="POST">
              @csrf
              <div class="modal-body p-4">
                <div class="row g-3">

                  <!-- Alertas -->
                  @if($saldoBaixo)
                  <div class="col-12">
                    <div class="alert alert-danger mb-0">
                      <i class="fas fa-exclamation-circle me-2"></i>
                      <strong>Saldo muito baixo para realizar um saque.</strong>
                    </div>
                  </div>
                  @endif

                  @if($retiradasPendentes)
                  <div class="col-12">
                    <div class="alert alert-warning mb-0">
                      <i class="fas fa-clock me-2"></i>
                      <strong>Já existe um saque em processamento. Aguarde a conclusão.</strong>
                    </div>
                  </div>
                  @endif

                  <!-- Saldo Disponível -->
                  <div class="col-12">
                    <div class="alert alert-info mb-0">
                      <i class="fas fa-info-circle me-2"></i>
                      <strong>Disponível para Saque:</strong> R$ {{ number_format(auth()->user()->saldo, 2, ',', '.') }}
                    </div>
                  </div>

                  <!-- Valor -->
                  <div class="col-12">
                    <label for="valor_saque" class="form-label">Valor do Saque</label>
                    <div class="input-group">
                      <span class="input-group-text">R$</span>
                      <input type="number"
                          step="0.01"
                          class="form-control"
                          id="valor_saque"
                          max="{{ auth()->user()->saldo }}"
                          name="valor_saque"
                          placeholder="0,00"
                          required>
                    </div>
                    <div id="containerValorLiquido" class="alert alert-success mt-2" style="display: none;">
                      <small id="valorLiquido"></small>
                    </div>
                    <div id="valorError" class="text-danger small mt-2" style="display: none;">
                      <i class="fas fa-exclamation-triangle me-1"></i>Saldo insuficiente para o valor solicitado.
                    </div>
                  </div>
                  @if(!is_null($networks))
                  <div class="col-12 multistep">
                    <!-- STEP 1: Escolha da Network -->
                    <div id="step-networks" class="row g-3">
                      @foreach($networks as $network)
                      <div class="col-md-3">
                        <div class="card card-network text-center h-100"
                          data-id="{{ $network['_id'] }}"
                          data-data="{{ json_encode($network) }}">
                          <div class="card-body">
                            <h6 class="fw-bold mb-1">{{ $network['name'] }}</h6>
                            <p class="text-muted small mb-0">{{ $network['chain'] }} • {{ $network['symbol'] }}</p>
                          </div>
                        </div>
                      </div>
                      @endforeach
                    </div>

                    <!-- STEP 2: Escolha da Moeda -->
                    <div id="step-cryptos" class="d-none">
                      <h6 class="fw-semibold mb-3">Escolha a moeda:</h6>
                      <ul class="list-group mb-3" id="crypto-list"></ul>
                      <button type="button" class="btn btn-secondary" id="btn-back">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                      </button>
                    </div>

                    <!-- STEP 3: Endereço + PIN -->
                    <div id="step-final" class="d-none">
                      <div class="mb-3">
                        <label for="wallet" class="form-label">Endereço da Carteira</label>
                        <input type="text" class="form-control" id="wallet" placeholder="Cole o endereço da sua carteira">
                      </div>

                      <!-- PIN de Segurança -->
                      @if(auth()->user()->pin_active)
                      <div class="mb-3">
                        <label for="pin_saque_crypto" class="form-label">
                          <i class="fas fa-lock text-warning me-1"></i>PIN de Segurança
                          <span class="badge bg-warning text-dark ms-1">Obrigatório</span>
                        </label>
                        <input type="password" 
                            class="form-control" 
                            id="pin_saque_crypto" 
                            name="pin_saque_crypto" 
                            placeholder="Digite seu PIN de 6 dígitos" 
                            maxlength="6"
                            pattern="[0-9]{6}"
                            required>
                        <div class="form-text">
                          <i class="fas fa-info-circle"></i> Seu PIN é necessário para confirmar esta operação de saque.
                        </div>
                      </div>
                      @endif

                      <input id="tipo_chave" name="tipo_chave" type="text" value="crypto" hidden />
                      <input type="hidden" id="user_id" name="user_id" value="{{ $email }}">

                      <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="btn-back-crypto">
                          <i class="fas fa-arrow-left me-1"></i>Voltar
                        </button>
                        <button type="submit" class="btn btn-success">
                          <i class="fas fa-check me-1"></i>Solicitar Saque
                        </button>
                      </div>
                    </div>
                  </div>
                  @endif
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Informações sobre Saque -->
      <div class="row">
        <div class="col-12">
          <div class="card border-warning">
            <div class="card-body p-4" style="background: rgba(255, 193, 7, 0.05);">
              <h6 class="text-warning mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>Informações de Saque
                @if($taxasPersonalizadas)
                  <span class="badge bg-warning text-dark ms-2">Personalizadas</span>
                @endif
              </h6>
              <div class="row g-3">
                <div class="col-md-3">
                  <div class="small text-muted mb-1">Taxa PIX</div>
                  <div class="fw-bold">{{ number_format($taxa_cash_out, 2, ',', '.') }}% {{ $taxa_fixa_padrao_cash_out > 0 ? '+ R$ '.number_format($taxa_fixa_padrao_cash_out, 2, ',', '.') : '' }}</div>
                </div>
                <div class="col-md-3">
                  <div class="small text-muted mb-1">Limite Mensal PF</div>
                  @if(isset($limite_mensal_pf) && (float)$limite_mensal_pf > 0)
                  <div class="fw-bold">R$ {{ number_format($limite_mensal_pf, 2, ',', '.') }}</div>
                  @else
                  <div class="fw-bold text-success">Sem limite</div>
                  @endif
                </div>
                <div class="col-md-3">
                  <div class="small text-muted mb-1">Saques por mês</div>
                  <div class="fw-bold">{{ $limite_saques_mes }}</div>
                </div>
                <div class="col-md-3">
                  <div class="small text-muted mb-1">Limite PJ</div>
                  <div class="fw-bold text-success">Sem limite</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    window.selectedNetwork = null;
    window.selectedCrypto = null;
    document.addEventListener("DOMContentLoaded", function() {
      let selectedNetwork = null;
      let selectedCrypto = null;
      const stepNetworks = document.getElementById("step-networks");
      const stepCryptos = document.getElementById("step-cryptos");
      const stepFinal = document.getElementById("step-final");
      const cryptoList = document.getElementById("crypto-list");

      // Seleção de network
      document.querySelectorAll(".card-network").forEach(card => {
        card.addEventListener("click", function() {
          // Resetar seleção
          document.querySelectorAll(".card-network").forEach(c => c.classList.remove("active"));
          this.classList.add("active");

          selectedNetwork = this.dataset.id;
        
          // Carregar as cryptos dinamicamente
          cryptoList.innerHTML ="";
          let networkData = @json($networks);
          let selNet = networkData.find(n => n._id === selectedNetwork);
          let cryptos = selNet.cryptocurrencies;
          window.selectedNetwork = selNet;

          cryptos.forEach(c => {
            let li = document.createElement("li");
            li.className ="list-group-item list-group-item-action crypto-item";
            li.dataset.crypto = c.cryptocurrency._id;
            li.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
              <span><strong>${c.cryptocurrency.name}</strong> <small>(${c.cryptocurrency.symbol})</small></span>
              <span class="badge bg-light text-dark border">Min: ${c.minWithdraw}</span>
            </div>`;
            cryptoList.appendChild(li);

            li.addEventListener("click", function() {
              document.querySelectorAll(".crypto-item").forEach(el => el.classList.remove("active"));
              this.classList.add("active");
              selectedCrypto = this.dataset.crypto;
              window.selectedCrypto = c;

              // Avança para step final
              stepCryptos.classList.add("d-none");
              stepFinal.classList.remove("d-none");
            });
          });

          // Mostrar Step 2
          stepNetworks.classList.add("d-none");
          stepCryptos.classList.remove("d-none");
        });
      });

      // Botão Voltar
      document.getElementById("btn-back").addEventListener("click", () => {
        stepCryptos.classList.add("d-none");
        stepNetworks.classList.remove("d-none");
        selectedNetwork = null;
      });

      document.getElementById("btn-back-crypto").addEventListener("click", () => {
        stepFinal.classList.add("d-none");
        stepCryptos.classList.remove("d-none");
        selectedCrypto = null;
      });
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      let selectedNetwork = null;
      let selectedCrypto = null;

      // Seleção de network
      document.querySelectorAll(".btn-select-network").forEach(btn => {
        btn.addEventListener("click", function() {
          let id = this.dataset.id;

          // Resetar todos os cards
          document.querySelectorAll(".card-select").forEach(card => {
            card.classList.remove("border-primary","shadow");
          });

          // Marcar card selecionado
          let selectedCard = this.closest(".card-select");
          selectedCard.classList.add("border-primary","shadow");

          // Esconder todas as listas
          document.querySelectorAll(".crypto-wrapper").forEach(list => {
            list.classList.add("d-none");
          });

          // Mostrar apenas a lista da network escolhida
          document.getElementById("crypto-list-" + id).classList.remove("d-none");

          selectedNetwork = id;
          selectedCrypto = null;
          console.log("Network selecionada:", selectedNetwork);
        });
      });

      // Seleção de crypto
      document.querySelectorAll(".crypto-item").forEach(item => {
        item.addEventListener("click", function() {
          let networkId = this.dataset.network;

          // Resetar todos os itens da lista dessa network
          document.querySelectorAll("#crypto-list-" + networkId +" .crypto-item")
            .forEach(el => el.classList.remove("active"));

          // Marcar item selecionado
          this.classList.add("active");

          selectedCrypto = this.dataset.crypto;
          console.log("Crypto selecionada:", selectedCrypto);
        });
      });
    });
  </script>

  <script>
    function copiarTexto() {
      var input = document.getElementById("pix-copia-e-cola");
      input.select();
      document.execCommand("copy");
      showToast("success","Chave Pix copiada!");
    }
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
        var amount = document.getElementById('valor_deposito').value;
        
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

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      let btnSolicitarSaque = document.getElementById("btnSolicitarSaque");
      let inputValor = document.getElementById("valor");
      let inputChave = document.getElementById("chave");
      let inputPin = document.getElementById("pin_saque");
      let valorLiquidoInput = document.getElementById("valorLiquido");
      let containerValorLiquido = document.getElementById("containerValorLiquido");

      // Desabilita o botão inicialmente
      btnSolicitarSaque.setAttribute("disabled", true);
      btnSolicitarSaque.classList.add("btn-secondary");
      
      // Validação inicial
      validarCampos();

      function validarCampos() {
        let valorPreenchido = inputValor.value && parseFloat(inputValor.value) > 0;
        let chavePreenchida = inputChave.value.trim().length > 0;
        let pinPreenchido = true; // Assume que não precisa de PIN por padrão
        
        // Se o campo PIN existe (usuário tem PIN ativo), verifica se está preenchido
        if (inputPin) {
          pinPreenchido = inputPin.value.trim().length === 6 && /^[0-9]{6}$/.test(inputPin.value);
        }

        // Habilitar botão se todos os campos obrigatórios estiverem preenchidos
        if (valorPreenchido && chavePreenchida && pinPreenchido) {
          btnSolicitarSaque.removeAttribute("disabled");
          btnSolicitarSaque.classList.remove("btn-secondary");
          btnSolicitarSaque.classList.add("btn-primary");
        } else {
          btnSolicitarSaque.setAttribute("disabled", true);
          btnSolicitarSaque.classList.remove("btn-primary");
          btnSolicitarSaque.classList.add("btn-secondary");
        }
      }

      function calcularValorLiquido() {
        let maxValue = parseFloat(inputValor.max) || 0;
        let currentValue = parseFloat(inputValor.value) || 0;

        if (currentValue > maxValue) {
          inputValor.value = maxValue;
          currentValue = maxValue;
        }

        if (currentValue <= 0 || isNaN(currentValue)) {
          containerValorLiquido.style.display ="none";
          return;
        }

        // NOVA LÓGICA: Cliente sempre recebe o valor solicitado
        let valorLiquido = currentValue; // Cliente recebe exatamente o que solicitou
        
        // Calcular taxa total para exibição
        // Verificar se o usuário tem taxas personalizadas ativas
        let taxasPersonalizadasAtivas = {{ auth()->user()->taxas_personalizadas_ativas ? 'true' : 'false' }};
        
        let tx_cash_out, taxa_fixa_pix, taxa_minima;
        
        if (taxasPersonalizadasAtivas) {
          // Usar taxas personalizadas do usuário
          tx_cash_out = parseFloat("{{ auth()->user()->taxa_percentual_pix ?? 0 }}") || 0;
          taxa_fixa_pix = parseFloat("{{ auth()->user()->taxa_fixa_pix ?? 0 }}") || 0;
          taxa_minima = parseFloat("{{ auth()->user()->taxa_minima_pix ?? 0 }}") || 0;
        } else {
          // Usar taxas globais
          tx_cash_out = parseFloat("{{ $setting->taxa_cash_out_padrao }}") || 0;
          taxa_fixa_pix = parseFloat("{{ $setting->taxa_fixa_pix ?? 0.00 }}") || 0;
          taxa_minima = parseFloat("{{ $setting->baseline }}") || 0;
        }
        
        let taxa_percentual = (currentValue * tx_cash_out / 100);
        let taxa_principal = Math.max(taxa_percentual, taxa_minima);
        let taxa_total = taxa_principal + taxa_fixa_pix;
        let valor_total_descontar = currentValue + taxa_total;

        // Verificar se há saldo suficiente
        if (valor_total_descontar > maxValue) {
          containerValorLiquido.style.display ="block";
          valorLiquidoInput.innerHTML = '<span style="color: red;">Saldo insuficiente! Necessário: ' + 
            valor_total_descontar.toLocaleString("pt-BR", {
              style:"currency",
              currency:"BRL"
            }) + '</span>';
        } else {
          containerValorLiquido.style.display ="block";
          let saldoRestante = maxValue - valor_total_descontar;
          valorLiquidoInput.innerHTML ="Valor líquido a receber:" +
            valorLiquido.toLocaleString("pt-BR", {
              style:"currency",
              currency:"BRL"
            }) +" (Taxa:" + taxa_total.toLocaleString("pt-BR", {
              style:"currency",
              currency:"BRL"
            }) +") - Saldo restante:" + saldoRestante.toLocaleString("pt-BR", {
              style:"currency",
              currency:"BRL"
            });
        }
      }

      inputValor.addEventListener("input", function() {
        calcularValorLiquido();
        validarCampos();
      });

      inputChave.addEventListener("input", validarCampos);
      
      // Adicionar listener para o campo PIN se existir
      if (inputPin) {
        inputPin.addEventListener("input", function() {
          // Limitar a 6 dígitos
          if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
          }
          // Permitir apenas números
          this.value = this.value.replace(/[^0-9]/g, '');
          
          // Feedback visual
          if (this.value.length === 6) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
          } else if (this.value.length > 0) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
          } else {
            this.classList.remove('is-valid', 'is-invalid');
          }
          
          validarCampos();
        });
        
        inputPin.addEventListener("paste", function(e) {
          e.preventDefault();
          let paste = (e.clipboardData || window.clipboardData).getData('text');
          let numbers = paste.replace(/[^0-9]/g, '').slice(0, 6);
          this.value = numbers;
          
          // Feedback visual
          if (this.value.length === 6) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
          } else if (this.value.length > 0) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
          } else {
            this.classList.remove('is-valid', 'is-invalid');
          }
          
          validarCampos();
        });
        
        inputPin.addEventListener("focus", function() {
          this.classList.remove('is-valid', 'is-invalid');
        });
      }
    });
  </script>

  <script>
    document.getElementById('saqueForm').addEventListener('submit', function(event) {
      event.preventDefault();
      var saldo ="{{ $saldoliquido }}"; // Corrigido para usar PHP para obter o saldo
      var valor = parseFloat(document.getElementById('valor').value);
      var valorError = document.getElementById('valorError');
      var pinAtivo = {{ auth()->user()->pin_active ? 'true' : 'false' }};
      var inputPin = document.getElementById('pin_saque');

      // Verifica se o usuário tem PIN ativo
      if (pinAtivo) {
        // Se tem PIN ativo, verifica se foi digitado
        if (!inputPin || !inputPin.value || inputPin.value.trim().length !== 6 || !/^[0-9]{6}$/.test(inputPin.value)) {
          showToast('error',"Por favor, digite seu PIN de 6 dígitos no campo 'PIN de Segurança (Obrigatório para saques)'");
          return;
        }
      }

      // Verifica se o saldo é zero ou se o valor solicitado é maior que o saldo
      if (saldo <= 0) {
        showToast('warning',"Saldo insuficiente!")
        event.preventDefault(); // Evita o envio do formulário
        return;
      } else if (valor > saldo) {
        showToast('success',"Saldo insuficiente!")
        event.preventDefault(); // Evita o envio do formulário
        return;
      }

      requestPayment();
      async function requestPayment() {
        var token ="{{ auth()->user()->chaves->token }}";
        var secret ="{{ auth()->user()->chaves->secret }}";
        var amount = document.getElementById('valor').value;
        var pixKey = document.getElementById('chave').value;
        var pixKeyType = document.getElementById('tipo_chave').value;
        var apiUrl ="{{env('APP_URL')}}/api/pixout";

        if (parseFloat(valor) > parseFloat(saldo)) {
          valor = saldo;
        }

        var payload = {
          token,
          secret,
          amount,
          pixKey,
          pixKeyType,
          baasPostbackUrl: 'web'
        }

        const response = await fetch(apiUrl, {
          method:"POST",
          headers: {
            "Content-Type":"application/json",
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.id) {
          showToast('success',"Saque solicitado com sucesso.")
          setTimeout(() => {
            window.location.reload();
          }, 3000)
        } else {
          showToast('warning', data.message);
        }
      }



    });




    document.getElementById('saqueFormCrypto').addEventListener('submit', function(event) {
      event.preventDefault();
      var saldo ="{{ $saldoliquido }}"; // Corrigido para usar PHP para obter o saldo
      var valor = parseFloat(document.getElementById('valor').value);
      var valorError = document.getElementById('valorError');
      var pinAtivo = {{ auth()->user()->pin_active ? 'true' : 'false' }};

      // Verifica se o usuário tem PIN ativo
      if (!pinAtivo) {
        showToast('error',"PIN obrigatório para saques! Acesse Meu Perfil > Segurança para configurar.");
        event.preventDefault();
        return;
      }

      // Verifica se o saldo é zero ou se o valor solicitado é maior que o saldo
      if (saldo <= 0) {
        showToast('warning',"Saldo insuficiente!")
        event.preventDefault(); // Evita o envio do formulário
        return;
      } else if (valor > saldo) {
        showToast('success',"Saldo insuficiente!")
        event.preventDefault(); // Evita o envio do formulário
        return;
      }

      requestPaymentCrypto();
      async function requestPaymentCrypto() {
        var token ="{{ auth()->user()->chaves->token }}";
        var secret ="{{ auth()->user()->chaves->secret }}";
        var amount = document.getElementById('valor_saque').value;
        var pixKey = document.getElementById('wallet').value;
        var pixKeyType ="crypto";
        var apiUrl ="{{env('APP_URL')}}/api/pixout";

        if (parseFloat(valor) > parseFloat(saldo)) {
          valor = saldo;
        }

        let blockchainNetwork = window.selectedNetwork;
        blockchainNetwork['cryptocurrencies'] = [];
        let cryptocurrency = window.selectedCrypto.cryptocurrency;

        var payload = {
          token,
          secret,
          amount,
          pixKey,
          pixKeyType,
          baasPostbackUrl: 'web',
          blockchainNetwork,
          cryptocurrency,
        }

        console.log(payload)
        const response = await fetch(apiUrl, {
          method:"POST",
          headers: {
            "Content-Type":"application/json",
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.id) {
          showToast('success',"Saque solicitado com sucesso.")
          setTimeout(() => {
            window.location.reload();
          }, 3000)
        } else {
          showToast('warning', data.message);
        }
      }



    });
  </script>


</x-app-layout>