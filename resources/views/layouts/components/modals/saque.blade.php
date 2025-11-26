<!-- Modal Saque PIX -->
<div class="modal fade" id="saquepix" tabindex="-1" aria-labelledby="saquepixLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-semibold" id="saquepixLabel">
          <i class="fa-brands fa-pix me-2"></i>Saque PIX
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="saqueForm" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="row g-3">

            <!-- Saldo Disponível -->
            <div class="col-12">
              <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Disponível para Saque:</strong> R$ {{ number_format(auth()->user()->saldo, 2, ',', '.') }}
              </div>
            </div>

            <!-- Valor -->
            <div class="col-12">
              <label for="valor" class="form-label">Valor do Saque</label>
              <div class="input-group">
                <span class="input-group-text">R$</span>
                <input type="text" 
                    class="form-control"
                    id="valor"
                    max="{{ auth()->user()->saldo }}"
                    name="valor"
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

            <!-- Tipo de Chave -->
            <div class="col-12">
              <label for="tipo_chave" class="form-label">Tipo de Chave PIX</label>
              <select id="tipo_chave" name="tipo_chave" class="form-select">
                <option value="cpf">CPF</option>
                <option value="cnpj">CNPJ</option>
                <option value="email">E-mail</option>
                <option value="telefone">Telefone</option>
                <option value="aleatoria">Chave Aleatória</option>
              </select>
            </div>

            <!-- Chave PIX -->
            <div class="col-12">
              <label for="chave" class="form-label">Chave PIX</label>
              <input type="text" class="form-control" id="chave" name="chave" placeholder="Digite sua chave PIX" required>
            </div>

            <!-- PIN de Segurança -->
            @if(auth()->user()->pin_active)
            <div class="col-12">
              <label for="pin_saque" class="form-label">
                <i class="fas fa-lock text-warning me-1"></i>PIN de Segurança
                <span class="badge bg-warning text-dark ms-1">Obrigatório</span>
              </label>
              <input type="password" 
                  class="form-control" 
                  id="pin_saque" 
                  name="pin_saque" 
                  placeholder="Digite seu PIN de 6 dígitos" 
                  maxlength="6"
                  pattern="[0-9]{6}"
                  required>
              <div class="form-text">
                <i class="fas fa-info-circle"></i> Seu PIN é necessário para confirmar esta operação de saque.
              </div>
            </div>
            @else
            <div class="col-12">
              <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-3"></i>
                <div>
                  <strong>PIN de Segurança Necessário!</strong><br>
                  Para realizar saques via web, você precisa cadastrar um PIN de segurança primeiro.
                  <div class="mt-2">
                    <a href="/my-profile?tab=security" class="btn btn-warning btn-sm">
                      <i class="fas fa-cog me-1"></i>Configurar PIN
                    </a>
                  </div>
                </div>
              </div>
            </div>
            @endif

            <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->email }}">
          </div>
        </div>
        <div class="modal-footer gap-3">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Cancelar
          </button>
          <button id="btnSolicitarSaque" type="submit" class="btn btn-success">
            <i class="fas fa-check me-1"></i>Solicitar Saque
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    let btnSolicitarSaque = document.getElementById("btnSolicitarSaque");
    let inputValor = document.getElementById("valor");
    let inputChave = document.getElementById("chave");
    let inputPin = document.getElementById("pin_saque");
    let valorLiquidoInput = document.getElementById("valorLiquido");
    let containerValorLiquido = document.getElementById("containerValorLiquido");

    // Desabilita o botão inicialmente
    if (btnSolicitarSaque) {
      btnSolicitarSaque.setAttribute("disabled", true);
      btnSolicitarSaque.classList.add("btn-secondary");
      
      // Validação inicial
      validarCampos();

      function validarCampos() {
        let valorFormatado = inputValor.value;
        let valorNumerico = converterParaNumerico(valorFormatado);
        let valorPreenchido = valorFormatado && parseFloat(valorNumerico) > 0;
        let chavePreenchida = inputChave.value.trim().length > 0;
        let pinPreenchido = true; // Assume que não precisa de PIN por padrão
        
        // Verificar se o usuário tem PIN ativo
        let pinAtivo = {{ auth()->user()->pin_active ? 'true' : 'false' }};
        
        // Se o campo PIN existe (usuário tem PIN ativo), verifica se está preenchido
        if (pinAtivo && inputPin) {
          pinPreenchido = inputPin.value.trim().length === 6 && /^[0-9]{6}$/.test(inputPin.value);
        } else if (pinAtivo && !inputPin) {
          // Se deveria ter PIN mas não tem o campo, não permitir
          pinPreenchido = false;
        }

        // Habilitar botão se todos os campos obrigatórios estiverem preenchidos
        if (valorPreenchido && chavePreenchida && pinPreenchido) {
          btnSolicitarSaque.removeAttribute("disabled");
          btnSolicitarSaque.classList.remove("btn-secondary");
          btnSolicitarSaque.classList.add("btn-success");
        } else {
          btnSolicitarSaque.setAttribute("disabled", true);
          btnSolicitarSaque.classList.remove("btn-success");
          btnSolicitarSaque.classList.add("btn-secondary");
        }
      }

      function calcularValorLiquido() {
        let maxValue = parseFloat(inputValor.max) || 0;
        let valorFormatado = inputValor.value;
        let valorNumerico = converterParaNumerico(valorFormatado);
        let currentValue = parseFloat(valorNumerico) || 0;

        if (currentValue > maxValue) {
          inputValor.value = maxValue;
          currentValue = maxValue;
        }

        if (currentValue <= 0 || isNaN(currentValue)) {
          containerValorLiquido.style.display ="none";
          return;
        }

        // Cliente sempre recebe o valor solicitado
        let valorLiquido = currentValue;
        
        // Calcular taxa total para exibição
        let taxasPersonalizadasAtivas = {{ auth()->user()->taxas_personalizadas_ativas ? 'true' : 'false' }};
        
        let tx_cash_out, taxa_fixa_pix, taxa_minima;
        
        if (taxasPersonalizadasAtivas) {
          // Usar taxas personalizadas do usuário
          tx_cash_out = parseFloat("{{ auth()->user()->taxa_percentual_pix ?? 0 }}") || 0;
          taxa_fixa_pix = parseFloat("{{ auth()->user()->taxa_fixa_pix ?? 0 }}") || 0;
          taxa_minima = parseFloat("{{ auth()->user()->taxa_minima_pix ?? 0 }}") || 0;
        } else {
          // Usar taxas globais
          tx_cash_out = parseFloat("{{ \App\Helpers\Helper::getSetting()->taxa_cash_out_padrao }}") || 0;
          taxa_fixa_pix = parseFloat("{{ \App\Helpers\Helper::getSetting()->taxa_fixa_pix ?? 0.00 }}") || 0;
          taxa_minima = parseFloat("{{ \App\Helpers\Helper::getSetting()->baseline }}") || 0;
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

      // Formatação automática de moeda
      inputValor.addEventListener("input", function(e) {
        let valor = e.target.value;
        let valorFormatado = formatarMoeda(valor);
        e.target.value = valorFormatado;
        
        calcularValorLiquido();
        validarCampos();
      });

      // Formatação ao colar texto
      inputValor.addEventListener("paste", function(e) {
        setTimeout(() => {
          let valor = e.target.value;
          let valorFormatado = formatarMoeda(valor);
          e.target.value = valorFormatado;
          
          calcularValorLiquido();
          validarCampos();
        }, 10);
      });

      // Formatação ao perder o foco (caso o usuário tenha digitado manualmente)
      inputValor.addEventListener("blur", function(e) {
        let valor = e.target.value;
        if (valor && valor !== '0,00') {
          let valorFormatado = formatarMoeda(valor);
          e.target.value = valorFormatado;
          
          calcularValorLiquido();
          validarCampos();
        }
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
    }
  });

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
</script>

<script>
  document.getElementById('saqueForm').addEventListener('submit', function(event) {
    event.preventDefault();
    var saldo = {{ auth()->user()->saldo }};
    var valorFormatado = document.getElementById('valor').value;
    var valorNumerico = converterParaNumerico(valorFormatado);
    var valor = parseFloat(valorNumerico);
    var valorError = document.getElementById('valorError');
    var pinAtivo = {{ auth()->user()->pin_active ? 'true' : 'false' }};
    var inputPin = document.getElementById('pin_saque');

    // Validar se o valor é válido
    if (!valor || valor <= 0 || isNaN(valor)) {
      showToast('error', 'Por favor, informe um valor válido para o saque.');
      return;
    }

    // Verifica se o usuário tem PIN ativo
    if (pinAtivo) {
      // Se tem PIN ativo, verifica se foi digitado
      if (!inputPin || !inputPin.value || inputPin.value.trim().length !== 6 || !/^[0-9]{6}$/.test(inputPin.value)) {
        showToast('error',"Por favor, digite seu PIN de 6 dígitos no campo 'PIN de Segurança (Obrigatório para saques)'");
        return;
      }
    } else {
      // Se não tem PIN ativo, mostra mensagem informativa
      showToast('warning',"Para realizar saques via web, você precisa cadastrar um PIN de segurança primeiro. Acesse seu perfil para configurar o PIN.");
      setTimeout(() => {
        window.location.href = '/my-profile?tab=security';
      }, 3000);
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

    requestPayment();
    async function requestPayment() {
      var token ="{{ auth()->user()->chaves->token }}";
      var secret ="{{ auth()->user()->chaves->secret }}";
      var amount = valorNumerico;
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

      // Adicionar PIN se o usuário tem PIN ativo
      if (pinAtivo && inputPin && inputPin.value) {
        payload.pin = inputPin.value;
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
        // Tratar diferentes tipos de erro
        if (data.requires_pin_setup) {
          showToast('warning', data.message);
          // Opcional: redirecionar para página de configuração do PIN
          setTimeout(() => {
            window.location.href = '/my-profile?tab=security';
          }, 3000);
        } else {
          showToast('warning', data.message);
        }
      }
    }
  });
</script>
