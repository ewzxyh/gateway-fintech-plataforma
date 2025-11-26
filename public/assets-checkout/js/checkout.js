// Função helper para redirecionar para página de obrigado
function redirectToThankYou(transactionId) {
    let redirectUrl = `/obrigado?transaction_id=${transactionId}`;
    
    // Usar URL configurada no checkout se disponível
    if (window.thank_you_url && window.thank_you_url !== '') {
        redirectUrl = window.thank_you_url.replace('ORDER_ID', transactionId);
        // Se não tem transaction_id na URL, adicionar
        if (!redirectUrl.includes('transaction_id=') && !redirectUrl.includes('order_id=')) {
            const separator = redirectUrl.includes('?') ? '&' : '?';
            redirectUrl += `${separator}transaction_id=${transactionId}`;
        }
    }
    
    console.log('🎯 Redirecionando para:', redirectUrl);
    window.location.href = redirectUrl;
}

document.addEventListener('DOMContentLoaded', function () {
    function applyInputMasks() {
      const telefone = document.querySelectorAll('input[name="telefone"]');
      const cpf = document.querySelectorAll('input[name="cpf"]');
      const cep = document.querySelectorAll('input[name="cep"]');
      const name = document.querySelectorAll('input[name="name"]');

      if (telefone.length) Inputmask({"mask": "(99) 99999-9999"}).mask(telefone);
      if (cpf.length) Inputmask({"mask": "999.999.999-99"}).mask(cpf);
      if (cep.length) Inputmask({"mask": "99999-999"}).mask(cep);
      if (name.length) {
          Inputmask.remove(name); // remove qualquer máscara anterior
          Inputmask({
              regex: "^[A-Za-zÀ-ÿ'\\-\\s]+$",
              placeholder: ''
          }).mask(name);
      }
    }

    applyInputMasks();

    // Adicionar máscara para número do cartão
    const cardNumber = document.querySelectorAll('input[name="card_number"]');
    if (cardNumber.length) Inputmask({"mask": "9999 9999 9999 9999"}).mask(cardNumber);

    // Adicionar máscara para CVV
    const cardCvv = document.querySelectorAll('input[name="card_cvv"]');
    if (cardCvv.length) Inputmask({"mask": "9999"}).mask(cardCvv);

    // Controlar exibição das seções de pagamento
    function showPaymentMethod(method) {
        // Esconder todas as seções
        document.querySelectorAll('.chk-flag-option').forEach(section => {
            section.classList.add('d-none');
            section.classList.remove('selected');
        });

        // Mostrar a seção selecionada
        if (method === 'pix') {
            const pixSection = document.getElementById('pix-payment-0');
            if (pixSection) {
                pixSection.classList.remove('d-none');
                pixSection.classList.add('selected');
            }
        } else if (method === 'card') {
            const cardSection = document.getElementById('card-payment-0');
            if (cardSection) {
                cardSection.classList.remove('d-none');
                cardSection.classList.add('selected');
                // Aplicar máscaras quando a seção do cartão for exibida
                setTimeout(applyCardMasks, 100);
            }
        } else if (method === 'billet') {
            const billetSection = document.getElementById('billet-payment-0');
            if (billetSection) {
                billetSection.classList.remove('d-none');
                billetSection.classList.add('selected');
            }
        }
    }

    // Selecionar PIX por padrão
    document.addEventListener('DOMContentLoaded', function() {
        const pixRadio = document.getElementById('pix');
        if (pixRadio) {
            pixRadio.checked = true;
            showPaymentMethod('pix');
        }
    });

    // Adicionar event listeners para os botões de método de pagamento
    document.querySelectorAll('input[name="metodo"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                showPaymentMethod(this.value);
            }
        });
    });

    let currentStep = 1;
    const totalSteps = document.querySelectorAll('.step-content').length;

    function showStep(step) {
        document.querySelectorAll('.step-content').forEach(function (el) {
            el.classList.add('d-none');
        });
        // Verificar se elemento existe antes de acessar classList
        const currentStepElement = document.querySelector('.step-content[data-step="' + step + '"]');
        if (currentStepElement) {
            currentStepElement.classList.remove('d-none');
        }

        document.querySelectorAll('.guide').forEach(g => g.classList.remove('ativo', 'current'));
        
        // Verificar se elementos existem antes de acessar classList
        if (step === 1) {
            const contactEl = document.getElementById('contact_data');
            if (contactEl) contactEl.classList.add('ativo', 'current');
        }
        if (step === 2) {
            const deliveryEl = document.getElementById('delivery_data');
            if (deliveryEl) deliveryEl.classList.add('ativo', 'current');
        }
        if (step === 3) {
            const paymentEl = document.getElementById('payment_data');
            if (paymentEl) paymentEl.classList.add('ativo', 'current');
        }
    }

    function validateStep(step) {
      let isValid = true;
      const stepContent = document.querySelector(`.step-content[data-step="${step}"]`);
      const requiredFields = stepContent.querySelectorAll('[required]');

      requiredFields.forEach(field => {
          const parent = field.closest('.mb-3');
          if (!parent) return;

          // Remove mensagens anteriores
          const existingError = parent.querySelector('.text-danger.dynamic-error');
          if (existingError) existingError.remove();

          const inputMask = field.inputmask;
          const isMasked = inputMask ? inputMask.isComplete() : true;

          // Verifica se está vazio ou incompleto
          if (!field.value.trim() || !isMasked) {
              isValid = false;

              // Estilização do input
              field.classList.add('is-invalid');

              // Mensagem de erro
              const error = document.createElement('span');
              error.classList.add('text-danger', 'dynamic-error');
              error.innerText = !field.value.trim() ? 'Campo obrigatório' : 'Preencha corretamente';
              parent.appendChild(error);
          } else {
              field.classList.remove('is-invalid');
          }
      });

      // Validação específica para campos do cartão (apenas se cartão estiver selecionado)
      const selectedMethod = document.querySelector('input[name="metodo"]:checked');
      if (selectedMethod && selectedMethod.value === 'card') {
          const cardFields = [
              { id: 'card-brand', name: 'Bandeira' },
              { id: 'card-number', name: 'Número do cartão' },
              { id: 'card-exp-month', name: 'Mês' },
              { id: 'card-exp-year', name: 'Ano' },
              { id: 'card-cvv', name: 'CVV' },
              { id: 'card-holder-name', name: 'Nome no cartão' }
          ];

          cardFields.forEach(fieldInfo => {
              const field = document.getElementById(fieldInfo.id);
              if (field) {
                  const parent = field.closest('.mb-3');
                  if (!parent) return;

                  // Remove mensagens anteriores
                  const existingError = parent.querySelector('.text-danger.dynamic-error');
                  if (existingError) existingError.remove();

                  // Verifica se está vazio
                  if (!field.value.trim()) {
                      isValid = false;
                      field.classList.add('is-invalid');

                      // Mensagem de erro
                      const error = document.createElement('span');
                      error.classList.add('text-danger', 'dynamic-error');
                      error.innerText = `${fieldInfo.name} é obrigatório`;
                      parent.appendChild(error);
                  } else {
                      field.classList.remove('is-invalid');
                  }
              }
          });
      }

      return isValid;
  }
  
  document.querySelectorAll('[required]').forEach(field => {
    field.addEventListener('input', function () {
        const isMasked = field.inputmask ? field.inputmask.isComplete() : true;

        const parent = field.closest('.mb-3');
        if (!parent) return;

        const existingError = parent.querySelector('.text-danger.dynamic-error');
        if (existingError) existingError.remove();

        if (field.value.trim() && isMasked) {
            field.classList.remove('is-invalid');
        }
    });
});

// Event listeners para campos do cartão
const cardFields = ['card-brand', 'card-number', 'card-exp-month', 'card-exp-year', 'card-cvv', 'card-holder-name'];

// Função para aplicar máscaras quando a seção do cartão for exibida
function applyCardMasks() {
    const cardNumber = document.getElementById('card-number');
    const cardCvv = document.getElementById('card-cvv');
    
    if (cardNumber && !cardNumber.inputmask) {
        Inputmask({"mask": "9999 9999 9999 9999"}).mask(cardNumber);
    }
    
    if (cardCvv && !cardCvv.inputmask) {
        Inputmask({"mask": "9999"}).mask(cardCvv);
    }
}

cardFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
        field.addEventListener('input', function () {
            const parent = field.closest('.mb-3');
            if (!parent) return;

            const existingError = parent.querySelector('.text-danger.dynamic-error');
            if (existingError) existingError.remove();

            if (field.value.trim()) {
                field.classList.remove('is-invalid');
            }
        });
    }
});



    document.querySelectorAll('.next-step').forEach(btn => {
        btn.addEventListener('click', function () {
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            }
        });
    });

    document.querySelectorAll('.prev-step').forEach(btn => {
        btn.addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        })
    });

    const btnInicial = document.querySelector('#for_add button[type="button"]');
    btnInicial.addEventListener('click', function (e) {
        e.preventDefault();

        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
        }
    });

    showStep(currentStep);

    let tempo = window.tempo;
    const twoMinutes =  tempo * 60;
    const display = document.getElementById('countdown_text');
    startCountdown(twoMinutes, display);

    // 1. Pegue o valor vindo do backend Blade
    let hexColor = window.checkout_color_default; // Ex: "#FF5733"

    // 2. Função para converter HEX para RGBA
    function hexToRgba(hex, alpha = 0.4) {
        hex = hex.replace('#', '');

        if (hex.length === 3) {
            hex = hex.split('').map(h => h + h).join('');
        }

        const bigint = parseInt(hex, 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;

        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    // 3. Converta e aplique no CSS root
    const rgbaColor = hexToRgba(hexColor, 0.1); // Define sua opacidade aqui
    const rgbaColor2 = hexToRgba(hexColor, 0.8);
    document.documentElement.style.setProperty('--color-default-opacity', rgbaColor);
    document.documentElement.style.setProperty('--color-default-opacity2', rgbaColor2);


});

function startCountdown(duration, display) {
    let timer = duration, minutes, seconds;

    const interval = setInterval(() => {
        minutes = String(Math.floor(timer / 60)).padStart(2, '0');
        seconds = String(timer % 60).padStart(2, '0');

        display.textContent = `${minutes}:${seconds}`;

        if (--timer < 0) {
            clearInterval(interval);
            display.textContent = "00:00";
            let containerCountdown = document.getElementById('texto-contador');
            containerCountdown.style.color = "white";
            containerCountdown.innerText = "Seu tempo acabou! Você precisa finalizar sua compra agora para ganhar o desconto extra."
        }
    }, 1000);
}

function applyViaCep() {
    const cepInput = document.querySelector('input[name="cep"]');
	if(cepInput){
      cepInput.addEventListener('input', function () {
          let cep = cepInput.value.replace(/\D/g, '');
          if (cep.length === 8) {
              fetchAddressByCEP(cep);
          }
      });

      cepInput.addEventListener('blur', function () {
          let cep = cepInput.value.replace(/\D/g, '');
          if (cep.length === 8) {
              fetchAddressByCEP(cep);
          }
      });
    }
}

function fetchAddressByCEP(cep) {
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(res => res.json())
        .then(data => {
            if (!data.erro) {
                document.querySelector('input[name="endereco"]').value = data.logradouro || '';
                document.querySelector('input[name="bairro"]').value = data.bairro || '';
                document.querySelector('input[name="cidade"]').value = data.localidade || '';
                document.querySelector('input[name="estado"]').value = data.uf || '';
            } else {
                showCepError('CEP não encontrado.');
            }
        })
        .catch(() => {
            showCepError('Erro ao consultar CEP.');
        });
}

function showCepError(message) {
    const cepInput = document.querySelector('input[name="cep"]');
    let errorSpan = cepInput.parentElement.querySelector('.text-danger');
    if (!errorSpan) {
        errorSpan = document.createElement('span');
        errorSpan.className = 'text-danger';
        cepInput.parentElement.appendChild(errorSpan);
    }
    errorSpan.textContent = message;
}
if(window.endereco_active){
  applyViaCep();
}
function reorderCheckoutSteps() {
    const $product = $('.produto-reorder-item');
    const $steps = $('.steps-reorder-item');
    const $containerGrid1 = $('#container-grid1');

    if ($(window).width() < 992) {
        if (!$('.produto-reorder-item.mobile-inserted').length) {
            const $clone = $product.clone(true);
            $clone.addClass('mobile-inserted');
            $clone.insertBefore($steps); // Insere acima dos steps
            $product.hide(); // Oculta o original
        }
    } else {
        const $inserted = $('.produto-reorder-item.mobile-inserted');
        if ($inserted.length) {
            $inserted.remove();
            $product.show(); // Mostra novamente no lugar original
        }
    }
}


// Chamada inicial e no redimensionamento
window.addEventListener('load', reorderCheckoutSteps);
window.addEventListener('resize', reorderCheckoutSteps);


$(document).ready(function () {
    const produtoValor = window.produto_valor;

    // Inicializa carrinho se não existir
    if (!localStorage.getItem('cart')) {
        const cart = {
            items: {
                produto: {
                    id: 'produto',
                    qtd: 1,
                    valor: produtoValor
                }
            },
            bumps: []
        };
        localStorage.setItem('cart', JSON.stringify(cart));
    }

    function getCart() {
        const cart = localStorage.getItem('cart');
        return cart ? JSON.parse(cart) : { items: { produto: { qtd: 1, valor: 0 } }, bumps: [] };
    }

    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
    }

    function atualizarDisplay() {
        const cart = getCart();
        const produtoQtd = cart.items.produto.qtd;
        const produtoTotal = produtoQtd * cart.items.produto.valor;

        let bumpsTotal = 0;
        const bumpsIdsUnicos = new Set();

        cart.bumps.forEach(b => {
            if (!bumpsIdsUnicos.has(b.id)) {
                bumpsTotal += parseFloat(b.valor_por);
                bumpsIdsUnicos.add(b.id);
            }
        });

        const total = produtoTotal + bumpsTotal;

        $('#checkout-total').text(total.toLocaleString('pt-br', { minimumFractionDigits: 2 }));
        $('.subtotal-value').text(total.toLocaleString('pt-br', { minimumFractionDigits: 2 }));
        $('.valor_total').text(total.toLocaleString('pt-br', { minimumFractionDigits: 2 }));
        $('.number-qtd').text(produtoQtd);
        $('.qtde').text(produtoQtd);
    }

    function restaurarBumps() {
        const cart = getCart();
        $('.ob-preview-content').empty();

        cart.bumps.forEach(bump => {
            $('.ob-preview-content').append(bump.html);

            // Aguarda o DOM estar completamente carregado
            setTimeout(() => {
                const originalContainer = $(`.container-item[data-id="${bump.id}"]`);
                if (originalContainer.length > 0) {
                    originalContainer.addClass('container-item-in-cart');
                    originalContainer.find('.btn-add-bump').addClass('d-none');
                    originalContainer.find('.ob-purchased').addClass('d-flex');
                }
            }, 50);
        });
    }

    // Botão de adicionar produto
    $(document).on('click', '.btn-add', function () {
        const cart = getCart();
        cart.items.produto.qtd += 1;
        saveCart(cart);
        atualizarDisplay();
    });

    $(document).on('click', '.btn-sub', function () {
        const cart = getCart();
        if (cart.items.produto.qtd > 1) {
            cart.items.produto.qtd -= 1;
            saveCart(cart);
            atualizarDisplay();
        }
    });

    // Adicionar bump
    $(document).on('click', '.btn-add-bump', function () {
        const container = $(this).closest('.container-item');
        const bumpId = container.data('id');
        const cart = getCart();

        // 🚫 Impede duplicação: Verifica se o bump com esse ID já está no carrinho
        const bumpExistente = cart.bumps.find(b => b.id === bumpId);
        if (bumpExistente) {
            return; // Já existe, não adiciona novamente
        }

        const image = container.find('img').attr('src');
        const nome = container.find('h6').first().text();
        const descricao = container.find('p').eq(0).text();
        const precoPor = container.find('.text-success').text();
        const precoDe = container.find('.text-danger').text();

        const valorPor = parseFloat(precoPor.replace(/[^\d,]/g, '').replace(',', '.'));
        const valorDe = parseFloat(precoDe.replace(/[^\d,]/g, '').replace(',', '.'));

        const desconto = valorDe - valorPor;
        const descontoFormatado = desconto.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

        const obHtml = `
            <div class="ob-info container-item-in-cart" id="ob-${bumpId}" data-id="${bumpId}" data-valor-de="${valorDe}" data-valor-por="${valorPor}">
                <img class="ob-photo" src="${image}" alt="Product Photo">
                <div class="ob-text">
                    <div class="ob-title">${nome}</div>
                    <div class="ob-description">${descricao}</div>
                    <div class="ob-price-container">
                        <span>1x de</span>
                        <span class="ob-price">${precoPor}</span>
                        <div class="ob-saved">${descontoFormatado} OFF</div>
                    </div>
                </div>
                <a class="ob-trash" style="cursor: pointer;"></a>
            </div>
        `;

        $('.ob-preview-content').append(obHtml);

        // Marca visualmente o bump como adicionado
        container.addClass('container-item-in-cart');
        container.find('.btn-add-bump').addClass('d-none');
        container.find('.ob-purchased').addClass('d-flex');

        // Adiciona o bump ao carrinho
        cart.bumps.push({
            id: bumpId,
            valor_de: valorDe,
            valor_por: valorPor,
            html: obHtml
        });

        saveCart(cart);
        atualizarDisplay();
        atualizaDesconto();
    });


    // Remover bump
    $(document).on('click', '.ob-trash', function () {
        const bumpItem = $(this).closest('.ob-info');
        const bumpId = bumpItem.data('id');

        bumpItem.remove();

        // Reexibe botão de adicionar e remove classe do item original
        const originalContainer = $(`.container-item[data-id="${bumpId}"]`);
        originalContainer.removeClass('container-item-in-cart');
        originalContainer.find('.btn-add-bump').removeClass('d-none');
        originalContainer.find('.ob-purchased').removeClass('d-flex');

        // Atualiza o carrinho removendo o bump do array
        const cart = getCart();
        cart.bumps = cart.bumps.filter(b => b.id !== bumpId);
        saveCart(cart);
        // Atualiza o total
        atualizarDisplay();
        atualizaDesconto();
    });


    function salvarBumpsLocalStorage() {
        const cart = getCart();
        cart.bumps = [];

        $('.ob-info').each(function () {
            cart.bumps.push({
                id: $(this).data('id'),
                valor_de: $(this).data('valor-de'),  // Salvando valor original
                valor_por: $(this).data('valor-por'), // Salvando valor com desconto
                html: $(this).prop('outerHTML')
            });
        });

        saveCart(cart); // Atualiza no localStorage
    }

    function atualizaDesconto(){
        let bumps = JSON.parse(localStorage.getItem('cart'))['bumps'];
        let per = 0;
        let total_disc = 0;
        bumps.map((item)=>{
            let de = parseFloat(item?.valor_de);
            let por = parseFloat(item?.valor_por);
            if (de > 0 && por < de) {
                per = per + ((de - por) / de) * 100;
                total_disc += (de - por);
            }
        })
        $('#discount_pix_span').text(total_disc.toLocaleString('pt-br', { minimumFractionDigits: 2 }));
        $('#discount_card_span').text(total_disc.toLocaleString('pt-br', { minimumFractionDigits: 2 }));
    }
    atualizaDesconto();

    let bumps = window.bumps || [];
        let per = 0;
        let total_disc = 0;
        bumps.map((item)=>{
            let de = parseFloat(item?.valor_de);
            let por = parseFloat(item?.valor_por);
            if (de > 0 && por < de) {
                per = per + ((de - por) / de) * 100;
                total_disc += (de - por);
            }
        })

        $('.chk-flag-option-discount').text("ATÉ "+per.toFixed(0)+'% OFF');
        $('.percent-discount').text("ATÉ "+per.toFixed(0)+'% OFF');
    // Inicializa a interface
    restaurarBumps();
    atualizarDisplay();

    // ========================================
    // FUNÇÕES AUXILIARES PRIMEPAY7
    // ========================================
    
    // Função para mostrar mensagem de sucesso
    function mostrarSucesso(total) {
        Swal.fire({
            title: '<h4>Seu pagamento foi confirmado</h4>',
            html: `
                <img src="/assets-checkout/img/order_confirmed.png" width="200px" height="auto"></br>
                <h5 style="font-weight:bold;" class="text-success mb-3 text-bold">Obrigado pela sua compra.</h5>
            `,
            icon: 'success',
            showCloseButton: true,
            showCancelButton: false,
            showConfirmButton: false,
            didClose: () => {
                if(window.redirect_tankyou){
                    window.location.href = window.redirect_tankyou;
                } else {
                    window.location.reload();
                }
            }
        });

        // Disparar evento de compra para Meta/Facebook Pixel
        if(typeof fbq !== 'undefined'){
            fbq('track', 'Purchase', {value: total, currency: 'BRL'});
        }
    }

    // Função para verificar o status do pagamento (polling)
    async function checkPaymentStatus(orderId, total, maxAttempts = 30, interval = 2000) {
        let attempts = 0;
        
        const checkStatus = async () => {
            try {
                attempts++;
                console.log(`🔍 Verificando status (tentativa ${attempts}/${maxAttempts})...`);
                
                const response = await fetch('/checkout/transaction/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ order_id: orderId })
                });

                const result = await response.json();
                
                if (result.status === 'success') {
                    const orderStatus = result.order_status;
                    console.log('📊 Status atual:', orderStatus);
                    
                    // Verificar se o status mudou
                    if (orderStatus === 'approved' || orderStatus === 'paid') {
                        Swal.close();
                        mostrarSucesso(total);
                        return true; // Finalizar polling
                    } else if (orderStatus === 'refused' || orderStatus === 'cancelled') {
                        Swal.close();
                        Swal.fire({
                            title: 'Pagamento Recusado',
                            text: 'Seu pagamento foi recusado pela operadora. Por favor, verifique os dados do cartão e tente novamente.',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                        return true; // Finalizar polling
                    } else if (orderStatus === 'processing' || orderStatus === 'pending') {
                        // Ainda processando, continuar polling
                        if (attempts >= maxAttempts) {
                            // Timeout - exibir mensagem
                            Swal.close();
                            Swal.fire({
                                title: 'Pagamento em Análise',
                                text: 'Seu pagamento está sendo processado. Você receberá uma confirmação em breve.',
                                icon: 'info',
                                confirmButtonText: 'Ok',
                                didClose: () => {
                                    if(window.redirect_tankyou){
                                        window.location.href = window.redirect_tankyou;
                                    } else {
                                        window.location.reload();
                                    }
                                }
                            });
                            return true; // Finalizar polling
                        }
                        // Continuar polling
                        return false;
                    }
                }
                
                // Se não conseguiu verificar, continuar tentando
                if (attempts >= maxAttempts) {
                    Swal.close();
                    Swal.fire({
                        title: 'Não foi possível confirmar',
                        text: 'Não conseguimos confirmar o status do seu pagamento. Por favor, entre em contato conosco.',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                    return true;
                }
                
                return false; // Continuar polling
                
            } catch (error) {
                console.error('❌ Erro ao verificar status:', error);
                
                if (attempts >= maxAttempts) {
                    Swal.close();
                    Swal.fire({
                        title: 'Erro ao verificar status',
                        text: 'Não foi possível verificar o status do pagamento. Por favor, entre em contato conosco.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    return true;
                }
                
                return false; // Continuar polling
            }
        };
        
        // Loop de polling
        while (true) {
            const shouldStop = await checkStatus();
            if (shouldStop) break;
            
            // Aguardar intervalo antes da próxima verificação
            await new Promise(resolve => setTimeout(resolve, interval));
        }
    }

    // ========================================
    // FUNÇÃO PARA PROCESSAR COM STRIPE
    // ========================================
    async function processarComStripe(dados, cardData, total) {
        try {
            console.log('🚀 Iniciando processamento Stripe...');
            
            // Mostrar loading
            Swal.fire({
                title: 'Processando pagamento...',
                text: 'Aguarde enquanto processamos seu pagamento',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Preparar dados para envio
            const dadosEnvio = {
                ...dados,
                amount: total,
                metodo: 'card',
                credit_card: JSON.stringify(cardData),
                installment: JSON.stringify({ installments: 1 }),
                _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            };
            
            console.log('📤 Enviando dados para Stripe:', dadosEnvio);
            
            // Enviar para o backend
            const response = await fetch('/checkout/gerarpedido', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': dadosEnvio._token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(dadosEnvio)
            });
            
            const result = await response.json();
            console.log('📥 Resposta do Stripe:', result);
            
            if (result.status === 'success') {
                const paymentIntentId = result.data.idTransaction;
                console.log('✅ PaymentIntent criado:', paymentIntentId);
                
                // Iniciar polling para verificar status
                await checkStripePaymentStatus(paymentIntentId, total);
            } else {
                throw new Error(result.message || 'Erro ao processar pagamento');
            }
            
        } catch (error) {
            console.error('❌ Erro no processamento Stripe:', error);
            
            let errorMessage = 'Erro ao processar pagamento. Tente novamente.';
            
            if (error.message) {
                errorMessage = error.message;
            } else if (error.response && error.response.data && error.response.data.message) {
                errorMessage = error.response.data.message;
            }
            
            // Mensagens específicas para erros comuns
            if (errorMessage.includes('Cartão de teste não suportado')) {
                errorMessage = 'Cartão não suportado para testes. Use um cartão de teste válido do Stripe.';
            } else if (errorMessage.includes('Erro ao processar dados do cartão')) {
                errorMessage = 'Erro ao processar dados do cartão. Verifique os dados e tente novamente.';
            } else if (errorMessage.includes('Erro ao confirmar pagamento')) {
                errorMessage = 'Erro ao confirmar pagamento. Tente novamente.';
            }
            
            Swal.fire({
                title: 'Erro no pagamento',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'Tentar novamente',
                showCloseButton: true
            });
        }
    }

    // ========================================
    // FUNÇÃO PARA VERIFICAR STATUS DO STRIPE
    // ========================================
    async function checkStripePaymentStatus(paymentIntentId, total, maxAttempts = 30, interval = 3000) {
        let attempts = 0;
        
        const checkStatus = async () => {
            try {
                attempts++;
                console.log(`🔍 Verificando status Stripe (tentativa ${attempts}/${maxAttempts}):`, paymentIntentId);
                
                const response = await fetch(`/api/stripe/status?payment_intent_id=${paymentIntentId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                console.log('📊 Status Stripe:', result);
                console.log('🔍 Debug - result.status:', result.status);
                console.log('🔍 Debug - result.data:', result.data);
                
                if (result.status === 'success') {
                    const { stripe_status, system_status, is_final, message, order_status } = result.data;
                    
                    console.log('📊 Status recebido:', { stripe_status, order_status, is_final });
                    console.log('🔍 Estrutura completa result.data:', result.data);
                    
                    // Verificar se é status final (aprovado ou cancelado)
                    console.log('🔍 Verificando condições:', { 
                        'stripe_status === succeeded': stripe_status === 'succeeded',
                        'order_status === approved': order_status === 'approved',
                        'stripe_status': stripe_status,
                        'order_status': order_status,
                        'typeof stripe_status': typeof stripe_status,
                        'typeof order_status': typeof order_status
                    });
                    
                    if (stripe_status === 'succeeded' || order_status === 'approved') {
                        // Pagamento aprovado - parar polling
                        console.log('✅ Pagamento Stripe aprovado!');
                        console.log('🚀 Iniciando redirecionamento para obrigado...');
                        Swal.close();
                        
                        // Tracking do Facebook Pixel
                        let produto = JSON.parse(localStorage.getItem('cart'));
                        if (typeof fbq !== 'undefined' && fbq) {
                            fbq('track', 'Purchase', {value: produto?.items?.produto?.valor, currency: 'BRL'});
                        }
                        
                        // Salvar dados do carrinho
                        const produtoValor = window.produto_valor;
                        const cart = {
                            items: {
                                produto: {
                                    id: 'produto',
                                    qtd: 1,
                                    valor: produtoValor
                                }
                            },
                            bumps: []
                        };
                        localStorage.setItem('cart', JSON.stringify(cart));
                        atualizarDisplay();
                        atualizaDesconto();
                        
                        // Redirecionar para página de obrigado
                        console.log('🎯 Redirecionando para:', `/obrigado?transaction_id=${paymentIntentId}`);
                        
                        // Usar URL configurada no checkout se disponível
                        let redirectUrl = `/obrigado?transaction_id=${paymentIntentId}`;
                        if (window.thank_you_url && window.thank_you_url !== '') {
                            redirectUrl = window.thank_you_url.replace('ORDER_ID', paymentIntentId);
                            // Se não tem transaction_id na URL, adicionar
                            if (!redirectUrl.includes('transaction_id=') && !redirectUrl.includes('order_id=')) {
                                const separator = redirectUrl.includes('?') ? '&' : '?';
                                redirectUrl += `${separator}transaction_id=${paymentIntentId}`;
                            }
                        }
                        
                        console.log('🎯 URL final de redirecionamento:', redirectUrl);
                        redirectToThankYou(paymentIntentId);
                        console.log('✅ Redirecionamento executado!');
                        return; // Parar polling
                        
                    } else if (stripe_status === 'canceled' || order_status === 'cancelled') {
                        // Pagamento cancelado - parar polling
                        console.log('❌ Pagamento Stripe cancelado');
                        Swal.close();
                        Swal.fire({
                            title: 'Pagamento cancelado',
                            text: 'Seu pagamento foi cancelado. Tente novamente.',
                            icon: 'warning',
                            confirmButtonText: 'Tentar novamente'
                        });
                        return; // Parar polling
                        
                    } else if (stripe_status === 'requires_payment_method' || stripe_status === 'requires_confirmation' || stripe_status === 'requires_action') {
                        // Status que requer ação do usuário - parar polling e mostrar erro
                        console.log('⚠️ Pagamento requer ação:', stripe_status);
                        Swal.close();
                        Swal.fire({
                            title: 'Ação necessária',
                            text: 'Seu pagamento requer uma ação adicional. Tente novamente.',
                            icon: 'warning',
                            confirmButtonText: 'Tentar novamente'
                        });
                        return; // Parar polling
                        
                    } else if (is_final || stripe_status === 'processing') {
                        // Outros status finais ou processando - parar polling
                        console.log('⚠️ Status final:', stripe_status);
                        Swal.close();
                        
                        if (stripe_status === 'processing') {
                            Swal.fire({
                                title: 'Processando pagamento',
                                text: 'Seu pagamento está sendo processado. Você receberá uma confirmação em breve.',
                                icon: 'info',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Status do pagamento',
                                text: message || 'Status inesperado do pagamento.',
                                icon: 'info',
                                confirmButtonText: 'OK'
                            });
                        }
                        return; // Parar polling
                        
                    } else {
                        // Status não final - continuar polling
                        console.log('⏳ Status não final, continuando polling...', stripe_status);
                        
                        // Atualizar mensagem do SweetAlert
                        Swal.update({
                            title: 'Processando pagamento...',
                            text: message || 'Aguardando confirmação do pagamento...'
                        });
                        
                        // Continuar polling se não excedeu tentativas
                        if (attempts < maxAttempts) {
                            setTimeout(checkStatus, interval);
                        } else {
                            // Timeout - parar polling
                            Swal.close();
                            Swal.fire({
                                title: 'Timeout',
                                text: 'O pagamento está demorando mais que o esperado. Verifique seu email ou entre em contato.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                } else {
                    // Erro na verificação
                    console.error('❌ Erro ao verificar status:', result);
                    
                    if (attempts < maxAttempts) {
                        console.log('🔄 Tentando novamente em', interval/1000, 'segundos...');
                        setTimeout(checkStatus, interval);
                    } else {
                        Swal.close();
                        Swal.fire({
                            title: 'Erro na verificação',
                            text: 'Não foi possível verificar o status do pagamento. Entre em contato com o suporte.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            footer: 'Se o problema persistir, entre em contato conosco.'
                        });
                    }
                }
                
            } catch (error) {
                console.error('❌ Erro no polling Stripe:', error);
                
                if (attempts < maxAttempts) {
                    console.log('🔄 Erro de rede, tentando novamente em', interval/1000, 'segundos...');
                    setTimeout(checkStatus, interval);
                } else {
                    Swal.close();
                    Swal.fire({
                        title: 'Erro de conexão',
                        text: 'Erro ao verificar status do pagamento. Verifique sua conexão e tente novamente.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        footer: 'Se o problema persistir, entre em contato conosco.'
                    });
                }
            }
        };
        
        // Iniciar polling
        checkStatus();
    }

    // ========================================
    // FUNÇÃO PARA PROCESSAR COM PRIMEPAY7
    // ========================================
    // Função para processar pagamento com PagarMe
    async function processarComPagarMe(dados, cardData, total) {
        try {
            console.log('🚀 Iniciando processamento PagarMe...');
            
            // Mostrar loading
            Swal.fire({
                title: 'Processando pagamento...',
                text: 'Aguarde enquanto processamos seu pagamento',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Preparar dados para envio
            const dadosEnvio = {
                ...dados,
                amount: total,
                metodo: 'card',
                credit_card: JSON.stringify(cardData),
                installment: JSON.stringify({ installments: 1 }),
                _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            };
            
            console.log('📤 Enviando dados para PagarMe:', dadosEnvio);
            
            // Enviar para o backend
            const response = await fetch('/checkout/gerarpedido', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': dadosEnvio._token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(dadosEnvio)
            });
            
            const result = await response.json();
            console.log('📥 Resposta do PagarMe/Rede:', result);
            
            if (result.status === 'success') {
                // Verificar o status REAL da transação (result.data.status)
                const transactionStatus = result.data?.status;
                
                console.log('✅ Transação criada:', {
                    idTransaction: result.data?.idTransaction,
                    status: transactionStatus
                });
                
                // Se o status é "pending", aguardar confirmação via webhook
                if (transactionStatus === 'pending' || transactionStatus === 'processing') {
                    console.log('⏳ Aguardando confirmação da transação...');
                    
                    Swal.fire({
                        title: 'Processando pagamento...',
                        html: `
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Carregando...</span>
                            </div>
                            <p class="mt-3">Aguarde enquanto confirmamos seu pagamento...</p>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });
                    
                    // Fazer polling para verificar o status
                    let checkCount = 0;
                    const maxChecks = 60; // 5 minutos (5 segundos * 60)
                    
                    const checkInterval = setInterval(async () => {
                        checkCount++;
                        
                        try {
                            const statusResponse = await fetch('/checkout/transaction/status', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                                },
                                body: JSON.stringify({
                                    order_id: result.data?.order_id,
                                    idTransaction: result.data?.idTransaction
                                })
                            });
                            
                            const statusResult = await statusResponse.json();
                            console.log('🔄 Status verificado:', statusResult);
                            
                            if (statusResult.status === 'pago' || statusResult.status === 'paid' || statusResult.status === 'approved') {
                                clearInterval(checkInterval);
                                
                                Swal.fire({
                                    title: 'Pagamento confirmado!',
                                    text: 'Seu pagamento foi confirmado com sucesso.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    if (result.data && result.data.idTransaction) {
                                        window.location.href = `/obrigado?transaction=${result.data.idTransaction}`;
                                    } else {
                                        window.location.reload();
                                    }
                                });
                            } else if (statusResult.status === 'cancelado' || statusResult.status === 'refused' || statusResult.status === 'failed') {
                                clearInterval(checkInterval);
                                
                                Swal.fire({
                                    title: 'Pagamento não autorizado',
                                    text: 'Seu pagamento não foi autorizado. Verifique os dados do cartão e tente novamente.',
                                    icon: 'error',
                                    confirmButtonText: 'Tentar novamente'
                                });
                            } else if (checkCount >= maxChecks) {
                                clearInterval(checkInterval);
                                
                                Swal.fire({
                                    title: 'Processamento em andamento',
                                    text: 'Seu pagamento ainda está sendo processado. Você receberá uma notificação quando for confirmado.',
                                    icon: 'info',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        } catch (error) {
                            console.error('Erro ao verificar status:', error);
                        }
                    }, 5000); // Verificar a cada 5 segundos
                    
                } else if (transactionStatus === 'pago' || transactionStatus === 'paid' || transactionStatus === 'approved') {
                    // Pagamento JÁ aprovado na primeira resposta
                    console.log('✅ Pagamento aprovado imediatamente!');
                    
                    Swal.fire({
                        title: 'Pagamento processado!',
                        text: 'Seu pagamento foi processado com sucesso.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (result.data && result.data.idTransaction) {
                            window.location.href = `/obrigado?transaction=${result.data.idTransaction}`;
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    // Status desconhecido - mostrar erro
                    console.log('⚠️ Status desconhecido:', transactionStatus);
                    
                    Swal.fire({
                        title: 'Status desconhecido',
                        text: 'Não foi possível confirmar o status do pagamento. Tente novamente.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            } else {
                Swal.fire({
                    title: 'Erro no pagamento',
                    text: result.message || 'Ocorreu um erro ao processar o pagamento.',
                    icon: 'error',
                    confirmButtonText: 'Tentar novamente'
                });
            }
            
        } catch (error) {
            console.error('❌ Erro ao processar PagarMe:', error);
            Swal.fire({
                title: 'Erro de conexão',
                text: 'Não foi possível conectar ao servidor. Tente novamente.',
                icon: 'error',
                confirmButtonText: 'Tentar novamente'
            });
        }
    }

    async function processarComRede(dados, cardNumber, cardExpMonth, cardExpYear, cardCvv, cardHolderName, total) {
        try {
            console.log('💳 Iniciando processamento com Rede e.Rede...');
            
            // Preparar dados do cartão para Rede
            const cardData = {
                number: cardNumber.replace(/\s/g, ''),
                holderName: cardHolderName,
                expirationMonth: parseInt(cardExpMonth),
                expirationYear: parseInt(cardExpYear),
                cvv: cardCvv
            };

            console.log('🔐 Dados do cartão preparados para Rede:', {
                number: cardData.number.replace(/\d(?=\d{4})/g, "*"),
                holderName: cardData.holderName,
                expirationMonth: cardData.expirationMonth,
                expirationYear: cardData.expirationYear,
                cvv: "***"
            });

            // Preparar dados do pagamento para Rede
            const paymentData = {
                amount: total,
                installments: 1,
                items: [{
                    title: dados.description || 'Pagamento',
                    quantity: 1,
                    unitPrice: total,
                    tangible: false
                }],
                customer: {
                    name: dados.name || 'Cliente',
                    email: dados.email || 'redacted@example.invalid',
                    document: {
                        type: 'cpf',
                        number: dados.cpf || '08355037120'
                    }
                },
                card: cardData,
                returnURL: window.location.origin + '/obrigado',
                postbackURL: window.location.origin + '/api/rede/webhook'
            };

            console.log('📦 Dados preparados para Rede:', paymentData);

            // Enviar direto para o backend que vai processar com Rede
            const response = await fetch('/api/card/payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    ...paymentData,
                    metodo: 'card_rede',
                    adquirente: 'rede'
                })
            });

            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }

            const result = await response.json();
            console.log('✅ Resposta da Rede:', result);

            if (result.status === 'success') {
                // Verificar o status REAL da transação (result.data.status)
                const transactionStatus = result.data?.status;
                
                console.log('✅ Transação Rede criada:', {
                    idTransaction: result.data?.idTransaction,
                    status: transactionStatus
                });
                
                // Se o status é "pending", aguardar confirmação via webhook
                if (transactionStatus === 'pending' || transactionStatus === 'processing') {
                    console.log('⏳ Aguardando confirmação da transação Rede...');
                    
                    Swal.fire({
                        title: 'Processando pagamento...',
                        html: `
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Carregando...</span>
                            </div>
                            <p class="mt-3">Aguarde enquanto confirmamos seu pagamento...</p>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });
                    
                    // Fazer polling para verificar o status
                    let checkCount = 0;
                    const maxChecks = 60; // 5 minutos (5 segundos * 60)
                    
                    const checkInterval = setInterval(async () => {
                        checkCount++;
                        
                        try {
                            const statusResponse = await fetch('/checkout/transaction/status', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                                },
                                body: JSON.stringify({
                                    order_id: result.data?.order_id,
                                    idTransaction: result.data?.idTransaction
                                })
                            });
                            
                            const statusResult = await statusResponse.json();
                            console.log('🔄 Status Rede verificado:', statusResult);
                            
                            if (statusResult.status === 'pago' || statusResult.status === 'paid' || statusResult.status === 'approved') {
                                clearInterval(checkInterval);
                                
                                Swal.fire({
                                    title: 'Pagamento confirmado!',
                                    text: 'Seu pagamento foi confirmado com sucesso.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    if (result.data && result.data.idTransaction) {
                                        window.location.href = `/obrigado?transaction=${result.data.idTransaction}`;
                                    } else {
                                        window.location.reload();
                                    }
                                });
                            } else if (statusResult.status === 'cancelado' || statusResult.status === 'refused' || statusResult.status === 'failed') {
                                clearInterval(checkInterval);
                                
                                Swal.fire({
                                    title: 'Pagamento não autorizado',
                                    text: 'Seu pagamento não foi autorizado. Verifique os dados do cartão e tente novamente.',
                                    icon: 'error',
                                    confirmButtonText: 'Tentar novamente'
                                });
                            } else if (checkCount >= maxChecks) {
                                clearInterval(checkInterval);
                                
                                Swal.fire({
                                    title: 'Processamento em andamento',
                                    text: 'Seu pagamento ainda está sendo processado. Você receberá uma notificação quando for confirmado.',
                                    icon: 'info',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        } catch (error) {
                            console.error('Erro ao verificar status Rede:', error);
                        }
                    }, 5000); // Verificar a cada 5 segundos
                    
                } else if (transactionStatus === 'pago' || transactionStatus === 'paid' || transactionStatus === 'approved') {
                    // Pagamento JÁ aprovado na primeira resposta
                    console.log('✅ Pagamento Rede aprovado imediatamente!');
                    
                    Swal.fire({
                        title: 'Pagamento processado!',
                        text: 'Seu pagamento foi processado com sucesso.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (result.data && result.data.idTransaction) {
                            window.location.href = `/obrigado?transaction=${result.data.idTransaction}`;
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    // Status desconhecido - mostrar erro
                    console.log('⚠️ Status Rede desconhecido:', transactionStatus);
                    
                    Swal.fire({
                        title: 'Status desconhecido',
                        text: 'Não foi possível confirmar o status do pagamento. Tente novamente.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            } else if (result.status === 'approved') {
                // Redirecionar para página de sucesso (caso raro de aprovação imediata)
                window.location.href = '/obrigado?transaction_id=' + result.transaction_id;
            } else if (result.status === 'pending' || result.status === 'processing') {
                // Mostrar mensagem de processamento
                showMessage('Pagamento em processamento. Aguarde...', 'info');
            } else {
                throw new Error(result.message || 'Erro no processamento do pagamento');
            }

        } catch (error) {
            console.error('❌ Erro ao processar com Rede:', error);
            showMessage('Erro ao processar pagamento: ' + error.message, 'error');
        }
    }

    async function processarComPrimePay7(dados, cardNumber, cardExpMonth, cardExpYear, cardCvv, cardHolderName, total) {
        try {
            console.log('💳 Iniciando processamento com PrimePay7...');
            
            // Mostrar loading
            Swal.fire({
                title: 'Processando pagamento...',
                text: 'Aguarde enquanto processamos seu pagamento',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Tokenizar o cartão usando PrimePay7
            console.log('🔐 Tokenizando cartão...');
            
            const cardData = {
                number: cardNumber.replace(/\s/g, ''), // Remover espaços
                holderName: cardHolderName,
                expMonth: parseInt(cardExpMonth),
                expYear: parseInt(cardExpYear),
                cvv: cardCvv.replace(/_/g, '') // Remover underscores do placeholder da máscara
            };
            
            console.log('📦 Dados do cartão para tokenização:', {
                ...cardData,
                number: cardData.number.replace(/\d(?=\d{4})/g, '*'),
                cvv: '***'
            });
            
            let cardHash = null;
            
            // Tentar tokenizar se PrimePay7 estiver disponível
            if (typeof PrimePay7 !== 'undefined' && typeof PrimePay7.encrypt === 'function') {
                try {
                    cardHash = await PrimePay7.encrypt(cardData);
                    console.log('✅ Cartão tokenizado com sucesso');
                    console.log('🔑 Hash do cartão:', cardHash.substring(0, 20) + '...');
                } catch (error) {
                    console.warn('⚠️ Erro ao tokenizar cartão, enviando dados diretos:', error);
                }
            } else {
                console.warn('⚠️ PrimePay7.encrypt não disponível, enviando dados diretos do cartão');
            }

            // Preparar dados do cartão para PrimePay7
            const paymentData = {
                checkout_id: dados['checkout_id'],
                name: dados['name'],
                email: dados['email'],
                cpf: dados['cpf'],
                telefone: dados['telefone'],
                valor_total: total,
                quantidade: dados['quantidade'],
                order_bumps: dados['order_bumps'],
                metodo: 'card',
                installments: 1, // Por enquanto fixo em 1x, depois pode adicionar seleção de parcelas
                card: cardHash ? {
                    hash: cardHash // Enviar apenas o hash se tokenizado
                } : {
                    // Caso falhe a tokenização, enviar dados completos
                    number: cardData.number,
                    holderName: cardData.holderName,
                    expirationMonth: cardData.expMonth,
                    expirationYear: cardData.expYear,
                    cvv: cardData.cvv
                }
            };

            console.log('📦 Dados preparados para PrimePay7:', paymentData);

            // Enviar direto para o backend que vai processar com PrimePay7
            const response = await fetch('/checkout/gerarpedido', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(paymentData)
            });

            const result = await response.json();
            console.log('📨 Resposta do servidor:', result);

            Swal.close();

            // Verificar se a requisição foi bem-sucedida (HTTP 200)
            if (result.status === 'success' && result.data) {
                const orderId = result.data.order_id;
                const transactionStatus = result.data.status;
                
                console.log('✅ Transação criada:', {
                    order_id: orderId,
                    transaction_id: result.data.id,
                    status: transactionStatus
                });

                // IMPORTANTE: Verificar o status REAL da transação (result.data.status)
                // "processing" ou "pending" = aguardar confirmação
                if (transactionStatus === 'processing' || transactionStatus === 'pending') {
                    console.log('⏳ Aguardando confirmação da transação...');
                    
                    // Mostrar tela de aguardo
                    Swal.fire({
                        title: 'Processando pagamento...',
                        html: `
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Carregando...</span>
                            </div>
                            <p class="mt-3">Aguarde enquanto confirmamos seu pagamento...</p>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });

                    // Fazer polling para verificar o status
                    await checkPaymentStatus(orderId, total);
                    
                } else if (transactionStatus === 'paid' || transactionStatus === 'approved') {
                    // Pagamento JÁ aprovado na primeira resposta (raro, mas possível)
                    console.log('✅ Pagamento aprovado imediatamente!');
                    mostrarSucesso(total);
                } else if (transactionStatus === 'refused' || transactionStatus === 'cancelled') {
                    // Pagamento JÁ recusado na primeira resposta
                    console.log('❌ Pagamento recusado imediatamente');
                    Swal.fire({
                        title: 'Pagamento Recusado',
                        text: 'Seu pagamento foi recusado pela operadora. Por favor, verifique os dados do cartão e tente novamente.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    // Status desconhecido - aguardar polling
                    console.log('⚠️ Status desconhecido, iniciando polling:', transactionStatus);
                    Swal.fire({
                        title: 'Processando pagamento...',
                        html: `
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Carregando...</span>
                            </div>
                            <p class="mt-3">Aguarde enquanto confirmamos seu pagamento...</p>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });
                    await checkPaymentStatus(orderId, total);
                }
            } else {
                // Erro na requisição HTTP ou erro retornado pelo servidor
                Swal.fire({
                    title: 'Erro no pagamento',
                    text: result.message || 'Não foi possível processar o pagamento. Tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }

        } catch (error) {
            console.error('❌ Erro ao processar com PrimePay7:', error);
            Swal.close();
            Swal.fire({
                title: 'Erro',
                text: 'Erro ao processar pagamento. Tente novamente.',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        }
    }

    $('#form-paid').on('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const dados = {};

        formData.forEach((value, key) => {
            dados[key] = value;
        });

        let checkout_id = window.checkout_id;
        let carrinho = JSON.parse(localStorage.getItem('cart'));
        let produto = carrinho?.items?.produto;
        let orderbumps = carrinho?.bumps;

        let qtd_produtos = produto?.qtd;
        let valor_produto = window.produto_valor;
        let total = valor_produto * qtd_produtos;

        let order_bumps = [];
        orderbumps?.forEach((i)=>{
            total += i?.valor_por;
            order_bumps.push(i?.id);
        })

        dados['quantidade'] = qtd_produtos;
        dados['valor_total'] = total;
        dados['checkout_id'] = checkout_id;
        dados['order_bumps'] = JSON.stringify(order_bumps);
       // dados['metodo'] = 

      const email = dados['email'];
	  const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!regexEmail.test(email)) {
        Swal.fire({
                    title: `Digite um email válido para continuar!'`,
                    icon: 'warning',
                    confirmButtonText: "Ok",
                    showCloseButton: true,
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                return;
      }
      
       let regexTelefone = /^[1-9]{2}9?[0-9]{8}$/;
       let telefone = dados['telefone'].replace(/[^\d]/g, '');
       console.log('Telefone: ', telefone);
       if(!regexTelefone.test(dados['telefone'].replace(/[^\d]/g, ''))){
            Swal.fire({
                    title: `Digite um número de telefone válido para continuar!'`,
                    icon: 'warning',
                    confirmButtonText: "Ok",
                    showCloseButton: true,
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                return;
       }

       let regexName = /^[ ]*([A-Za-zÀ-ÿ]+[ ]+)+[A-Za-zÀ-ÿ]+[ ]*$/;
       if(!regexName.test(dados['name'])){
         Swal.fire({
                    title: `Digite seu nome verdadeiro para continuar!'`,
                    icon: 'warning',
                    confirmButtonText: "Ok",
                    showCloseButton: true,
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                return;
       }

       function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, ''); // Remove pontos, traços, espaços

        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
            return false; // Tamanho inválido ou dígitos todos iguais
        }

        // Valida o primeiro dígito verificador
        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(cpf.charAt(i)) * (10 - i);
        }

        let digito1 = 11 - (soma % 11);
        digito1 = (digito1 >= 10) ? 0 : digito1;

        if (parseInt(cpf.charAt(9)) !== digito1) {
            return false;
        }

        // Valida o segundo dígito verificador
        soma = 0;
        for (let i = 0; i < 10; i++) {
            soma += parseInt(cpf.charAt(i)) * (11 - i);
        }

        let digito2 = 11 - (soma % 11);
        digito2 = (digito2 >= 10) ? 0 : digito2;

        if (parseInt(cpf.charAt(10)) !== digito2) {
            return false;
        }

        return true;
    }

    if(!validarCPF(dados['cpf'])){
        Swal.fire({
                    title: `Digite um CPF válido para continuar!'`,
                    icon: 'warning',
                    confirmButtonText: "Ok",
                    showCloseButton: true,
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                return;
    }

        if(!dados['metodo']){
            Swal.fire({
                    title: `Selecione um método de pagamento para continuar!'`,
                    icon: 'warning',
                    confirmButtonText: "Ok",
                    showCloseButton: true,
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                return;
        }
      
      console.log(dados);
      
      // 🔧 FILTRAR DADOS BASEADO NO MÉTODO DE PAGAMENTO
      // Remover dados de cartão quando método for PIX ou Boleto
      if (dados['metodo'] === 'pix' || dados['metodo'] === 'boleto') {
          // Remover campos de cartão que não devem ser enviados
          delete dados['card_brand'];
          delete dados['card_number'];
          delete dados['card_exp_month'];
          delete dados['card_exp_year'];
          delete dados['card_cvv'];
          delete dados['card_holder_name'];
          
          console.log('🧹 Dados de cartão removidos para método:', dados['metodo']);
      }
      
       if (dados['metodo'] === 'card') {
            // Validar campos do cartão
            let cardBrand = document.getElementById('card-brand').value;
            const cardNumber = document.getElementById('card-number').value;
            const cardExpMonth = document.getElementById('card-exp-month').value;
            const cardExpYear = document.getElementById('card-exp-year').value;
            const cardCvv = document.getElementById('card-cvv').value;
            const cardHolderName = document.getElementById('card-holder-name').value;

            if (!cardNumber || !cardExpMonth || !cardExpYear || !cardCvv || !cardHolderName) {
                Swal.fire({
                    title: 'Preencha todos os dados do cartão',
                    icon: 'warning',
                    confirmButtonText: "Ok"
                });
                return;
            }

            // 🔍 VERIFICAR SE DEVE USAR PRIMEPAY7
            // Para cartões, usar a adquirente específica de cartão/boleto
            const adquirenteCard = window.defaultAdquirenteCard || window.defaultAdquirante;
            console.log('🔍 Verificando adquirente para cartão:', adquirenteCard);
            
            if (adquirenteCard === 'primepay7') {
                console.log('✅ Usando PrimePay7 para processar pagamento com cartão');
                processarComPrimePay7(dados, cardNumber, cardExpMonth, cardExpYear, cardCvv, cardHolderName, total);
                return; // Sair da função e usar o fluxo PrimePay7
            } else if (adquirenteCard === 'rede') {
                console.log('✅ Usando Rede e.Rede para processar pagamento com cartão');
                processarComRede(dados, cardNumber, cardExpMonth, cardExpYear, cardCvv, cardHolderName, total);
                return; // Sair da função e usar o fluxo Rede
            }
            
            console.log('ℹ️ Usando EFI para processar pagamento com cartão');

            // Validar número do cartão (remover espaços)
            const cleanCardNumber = cardNumber.replace(/\s/g, '');
            if (cleanCardNumber.length < 13 || cleanCardNumber.length > 19) {
                Swal.fire({
                    title: 'Número do cartão inválido',
                    icon: 'warning',
                    confirmButtonText: "Ok"
                });
                return;
            }

            // Validar CVV (remover underscores antes de validar)
            const cleanCvv = cardCvv.replace(/_/g, '');
            if (cleanCvv.length < 3 || cleanCvv.length > 4) {
                Swal.fire({
                    title: 'CVV inválido',
                    text: 'O CVV deve ter 3 ou 4 dígitos',
                    icon: 'warning',
                    confirmButtonText: "Ok"
                });
                return;
            }

            // Função de fallback para detectar bandeira do cartão
            function detectCardBrand(cardNumber) {
                const number = cardNumber.replace(/\s/g, '');
                
                // Visa
                if (/^4/.test(number)) {
                    return 'visa';
                }
                // Mastercard
                if (/^5[1-5]/.test(number) || /^2[2-7]/.test(number)) {
                    return 'mastercard';
                }
                // American Express
                if (/^3[47]/.test(number)) {
                    return 'amex';
                }
                // Elo
                if (/^(4011|4312|4389|4514|4576|5041|5066|5067|5090|6277|6362|6363|6504|6505|6516|6550)/.test(number)) {
                    return 'elo';
                }
                // Hipercard
                if (/^(384100|384140|384160|606282)/.test(number)) {
                    return 'hipercard';
                }
                // Discover
                if (/^6(?:011|5)/.test(number)) {
                    return 'discover';
                }
                // JCB
                if (/^35/.test(number)) {
                    return 'jcb';
                }
                // Diners
                if (/^3(?:0[0-5]|[68])/.test(number)) {
                    return 'diners';
                }
                
                // Se não detectar, usar visa como padrão (compatibilidade)
                return 'visa';
            }

            // Se a bandeira não foi detectada automaticamente, detectar agora
            if (!cardBrand || cardBrand === '') {
                cardBrand = detectCardBrand(cleanCardNumber);
                document.getElementById('card-brand').value = cardBrand;
                console.log('🔍 Bandeira detectada via fallback:', cardBrand);
            }

            const cardData = {
                number: cleanCardNumber,
                brand: cardBrand.toLowerCase(),
                expiration_month: cardExpMonth,
                expiration_year: cardExpYear,
                cvv: cardCvv.replace(/_/g, ''), // Remover underscores do placeholder da máscara
                holder_name: cardHolderName
            };

            dados['credit_card'] = JSON.stringify(cardData);

            console.log('🔍 Dados do cartão preparados:', cardData);
            console.log('🔍 window.checkoutefi disponível?', typeof window.checkoutefi);
            
            // Determinar qual adquirente usar baseado na configuração
            const adquirenteAtual = window.defaultAdquirenteCard || 'pagarme';
            console.log('🔍 Adquirente detectado:', adquirenteAtual);
            
            // Se for Stripe, processar com Stripe
            if (adquirenteAtual === 'stripe') {
                console.log('✅ Processando pagamento com Stripe...');
                await processarComStripe(dados, cardData, total);
                return;
            }
            
            // Se for PagarMe, processar diretamente sem tokenização
            if (adquirenteAtual === 'pagarme') {
                console.log('✅ Processando pagamento com PagarMe...');
                await processarComPagarMe(dados, cardData, total);
                return;
            }
            
            // Se for EFI, verificar se está disponível
            if (adquirenteAtual === 'efi') {
                if (!window.checkoutefi || typeof window.checkoutefi.getPaymentToken !== 'function') {
                    console.error('❌ EFI Checkout não está inicializado!');
                    Swal.fire({
                        title: 'Erro de inicialização',
                        text: 'Sistema de pagamento não está pronto. Tente recarregar a página.',
                        icon: 'error',
                        confirmButtonText: 'Recarregar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                    return;
                }
            }

            console.log('✅ Iniciando geração de token com EFI...');
            
            window.checkoutefi.getPaymentToken(
                {
                    brand: cardData.brand,
                    number: cardData.number,
                    cvv: cardData.cvv,
                    expiration_month: cardData.expiration_month,
                    expiration_year: cardData.expiration_year,
                    reuse: false
                },
                function (error, response) {
                    if (error) {
                        console.error('❌ Erro ao gerar token:', error);
                        Swal.fire('Erro ao gerar token', error.message || 'Verifique os dados e tente novamente.', 'error');
                    } else {
                        console.log('✅ Token gerado com sucesso:', response);
                        
                        let cobrar_taxa = 0;
                        cobrar_taxa += Number(window.taxas_cartao_fixa);
                        cobrar_taxa += Number(window.taxas_cartao_porcentagem) * total /100;
                        cobrar_taxa += total;
                        
                        window.checkoutefi.getInstallments(
                            Number(cobrar_taxa.toFixed(2)) * 100, // valor total da cobrança em centavos
                            cardData.brand,
                            function (error, responseInstallments) {
                                if (error) {
                                    Swal.fire('Erro ao buscar parcelas', error.message || 'Tente novamente mais tarde.', 'error');
                                } else {
                                    const installments = responseInstallments?.data?.installments;

                                    if (!installments || !Array.isArray(installments)) {
                                        Swal.fire('Erro', 'Parcelas não encontradas.', 'error');
                                        return;
                                    }

                                    const options = installments.map((parcela, index) => {
                                        const texto = `${parcela.installment}x de R$ ${parcela.currency}`;
                                        return `<option value="${index}">${texto}</option>`;
                                    }).join('');

                                    Swal.fire({
                                        title: 'Escolha o número de parcelas',
                                        html: `
                                        <div class="mb-3">
                                            <span class="form-label" >Parcelas</span>
                                            <select type="text" id="select-parcela" class="form-control" aria-label="Selecione a quantidade de parcelas" aria-describedby="parcelas-cartao">
                                               ${options}
                                            </select>
                                        </div>
                                        `,
                                        confirmButtonText: 'Confirmar Pagamento',
                                        preConfirm: () => {
                                            const index = document.getElementById('select-parcela').value;
                                            const parcela = installments[index];
                                            dados['installment'] = JSON.stringify(parcela);
                                            dados['payment_token'] = response?.data?.payment_token;

                                            // 🔧 FILTRAR DADOS BASEADO NO MÉTODO DE PAGAMENTO (para EFI)
                                            if (dados['metodo'] === 'pix' || dados['metodo'] === 'boleto') {
                                                delete dados['card_brand'];
                                                delete dados['card_number'];
                                                delete dados['card_exp_month'];
                                                delete dados['card_exp_year'];
                                                delete dados['card_cvv'];
                                                delete dados['card_holder_name'];
                                                console.log('🧹 Dados de cartão removidos para EFI método:', dados['metodo']);
                                            }

                                            fetch("/checkout/cliente/pedido/gerar", {
                                                method: "POST",
                                                headers: {
                                                    'Content-Type': "application/json",
                                                    'Accept': "application/json",
                                                    'Authorization': 'Bearer '+btoa(dados['token']+':'+dados['secret']),
                                                    'X-CSRF-TOKEN': dados['_token']
                                                },
                                                body: JSON.stringify(dados)
                                            })
                                            .then((res)=>res.json())
                                            .then((res)=>{
                                                if(res.status === 'success'){
                                                    
                                                    Swal.fire({
                                                        title: `<h4>Seu pagamento foi confirmado</h4>`,
                                                        html: `
                                                            <img src="/assets-checkout/img/order_confirmed.png" width="200px" height="auto"></br>
                                                            <h5 style="font-weight:bold;" class="text-success mb-3 text-bold">Obrigado pela sua compra.</h5>
                                                        `,
                                                        showCloseButton: true,
                                                        showCancelButton: false,
                                                        showConfirmButton: false,
                                                        didClose: () => {
                                                            if(window.redirect_tankyou){
                                                                window.location.href = window.redirect_tankyou;
                                                            } else {
                                                                window.location.reload();
                                                            }
                                                        }

                                                    });

                                                    if(typeof fbq !== 'undefined' && fbq){
                                                        let produto = JSON.parse(localStorage.getItem('cart'));
                                                        fbq('track', 'Purchase', {value: produto?.items?.produto?.valor, currency: 'BRL'});

                                                    }

                                                    const produtoValor = window.produto_valor;

                                                    const cart = {
                                                        items: {
                                                            produto: {
                                                                id: 'produto',
                                                                qtd: 1,
                                                                valor: produtoValor
                                                            }
                                                        },
                                                        bumps: []
                                                    };

                                                    localStorage.setItem('cart', JSON.stringify(cart));

                                                     atualizarDisplay();
                                                    atualizaDesconto();
                                                } else {
                                                    Swal.fire({
                                                        title: `${res?.message ?? 'Houve um erro. Tente novamente mais tarde!'}`,
                                                        icon: 'warning',
                                                        confirmButtonText: "Ok",
                                                        showCloseButton: true,
                                                        showCancelButton: false,
                                                        showConfirmButton: false,
                                                        allowOutsideClick: false,
                                                        allowEscapeKey: false
                                                    });
                                                }

                                            })
                                        }
                                    });
                                }
                            }
                        );
                    }
                }
            );

    } else {
        // 🔧 FILTRAR DADOS BASEADO NO MÉTODO DE PAGAMENTO (para PIX/Boleto)
        if (dados['metodo'] === 'pix' || dados['metodo'] === 'boleto') {
            delete dados['card_brand'];
            delete dados['card_number'];
            delete dados['card_exp_month'];
            delete dados['card_exp_year'];
            delete dados['card_cvv'];
            delete dados['card_holder_name'];
            console.log('🧹 Dados de cartão removidos para método:', dados['metodo']);
        }
        
        fetch("/checkout/cliente/pedido/gerar", {
            method: "POST",
            headers: {
                'Content-Type': "application/json",
                'Accept': "application/json",
                'Authorization': 'Bearer '+btoa(dados['token']+':'+dados['secret']),
                'X-CSRF-TOKEN': dados['_token']
            },
            body: JSON.stringify(dados)
        })
        .then((res)=>res.json())
        .then((res)=>{
            if(res.status === 'success'){
                if(res?.data?.barcode){
                    Swal.fire({
                        title: `<h6>Copie o código de barras ou se preferir faça o download do boleto!<h6>`,
                        html: `
                        <h4 style="font-weight:bold;" class="text-success mb-3 text-bold">Valor: ${res.valor_text}</h4>
                        <input class="form-control" readonly id="input-barcode" value="${res?.data?.barcode}">
                            <div id="container-alert"> </div>
                            <div class="d-flex align-items-center justify-content-center gap-4">
                                <button onclick="copiarBarcode()" class="btn btn-info btn-sm my-3"><i class="fa-solid fa-copy"></i>&nbsp;Copiar</button>
                                <button onclick="downloadBoleto('${res?.data?.download}')" class="btn btn-info btn-sm my-3"><i class="fa-solid fa-download"></i>&nbsp;Download</button>
                            </div>
                            `,
                        showCloseButton: true,
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                } else {
                    Swal.fire({
                        title: `<h6>Escaneie o QrCode ou copie o código abaixo para realizar o pagamento<h6>`,
                        html: `    
                          <img src="${res?.data?.qr_code_image_url}" width="250px" height="250px"></br>
                          <h5 style="font-weight:bold;" class="text-success mb-3 text-bold">Valor: ${res.valor_text}</h5>
                          <input id="pix-copia-e-cola" class="form-control" readonly id="input-copia-e-cola" value="${res?.data?.qrcode}">
                              <div id="container-alert"> </div>
                          <button onclick="copiarChavePix()" class="btn btn-info btn-sm my-3"><i class="fa-solid fa-copy"></i>&nbsp;Copiar</button>
    
                              `,
                        showCloseButton: true,
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });


                    let intervalId;

                const checkStatus = () => {
                    let idTransaction = res?.data?.idTransaction;

                    fetch("/checkout/cliente/pedido/status", {
                        method: "POST",
                        headers: {
                            'Content-Type': "application/json",
                            'Accept': "application/json",
                            'X-CSRF-TOKEN': dados['_token']
                        },
                        body: JSON.stringify({ idTransaction })
                    })
                    .then((res) => res.json())
                    .then(res => {
                        if (res?.status === 'pago') {
                            Swal.close();

                            Swal.fire({
                                title: `<h4>Seu pagamento foi confirmado</h4>`,
                                html: `
                                    <img src="/assets-checkout/img/order_confirmed.png" width="200px" height="auto"></br>
                                    <h5 style="font-weight:bold;" class="text-success mb-3 text-bold">Obrigado pela sua compra.</h5>
                                `,
                                showCloseButton: true,
                                showCancelButton: false,
                                showConfirmButton: false,
                                didClose: () => {
                                    if(window.redirect_tankyou){
                                        window.location.href = window.redirect_tankyou;
                                    } else {
                                        window.location.reload();
                                    }
                                }

                            });
                            clearInterval(intervalId);

                            let produto = JSON.parse(localStorage.getItem('cart'));
                            if(typeof fbq !== 'undefined' && fbq){
                                fbq('track', 'Purchase', {value: produto?.items?.produto?.valor, currency: 'BRL'});
                            }

                            const produtoValor = window.produto_valor;

                            const cart = {
                                items: {
                                    produto: {
                                        id: 'produto',
                                        qtd: 1,
                                        valor: produtoValor
                                    }
                                },
                                bumps: []
                            };
                            localStorage.setItem('cart', JSON.stringify(cart));
                            atualizarDisplay();
                            atualizaDesconto();
                        }
                    });
                };

                intervalId = setInterval(checkStatus, 5000);
                }

                
            } else {
                Swal.fire({
                    title: `${res?.message ?? 'Houve um erro. Tente novamente mais tarde!'}`,
                    icon: 'warning',
                    confirmButtonText: "Ok",
                    showCloseButton: true,
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }

        })
    }
    });
});

function copiarChavePix() {
   var input = document.getElementById("pix-copia-e-cola");

   // Garante que o valor do input será copiado
   navigator.clipboard.writeText(input.value)
   .then(() => {
     let message = `
<div class="alert alert-success my-3" role="alert" style="font-weight:bold;">
  <i class="fa-brands fa-pix"></i>&nbsp;Chave PIX copiada com sucesso!
</div>`;
     document.getElementById('container-alert').innerHTML = message;
   })
   .catch(err => {
   });
}

function copiarBarcode() {
   var input = document.getElementById("input-barcode");

   // Garante que o valor do input será copiado
   navigator.clipboard.writeText(input.value)
   .then(() => {
     let message = `
<div class="alert alert-success my-3" role="alert" style="font-weight:bold;">
  <i class="fa-solid fa-barcode"></i>&nbsp;Código de barras copiado com sucesso!
</div>`;
     document.getElementById('container-alert').innerHTML = message;
   })
   .catch(err => {
   });
}

function downloadBoleto(externalUrl) {
    const produto = "{{ $checkout->produto_name }}".replace(/\s+/g, '_').toLowerCase();
    const url = `/download-boleto?url=${encodeURIComponent(externalUrl.replace('?sandbox=true', ''))}`;
    const link = document.createElement('a');
    link.href = url;
    link.download = `boleto-${produto}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

document.addEventListener('DOMContentLoaded', function () {
    localStorage.clear();
    
    // ===== INTEGRAÇÃO PRIMEPAY7 COM CARTÃO E 3DS =====
    
    // Instanciar integração PrimePay7
    let PrimePay7CardIntegration = null;
    
    // Instanciar integração Rede
    let RedeCardIntegration = null;

    // Função para inicializar PrimePay7
    async function initPrimePay7Card() {
      try {
        // Buscar chave pública da configuração
        const publicKey = window.primePay7PublicKey || 'pk_live_YOUR_PUBLIC_KEY';
        
        if (!publicKey || publicKey === 'pk_live_YOUR_PUBLIC_KEY') {
          console.warn('⚠️ Chave pública PrimePay7 não configurada');
          return false;
        }
        
        // Verificar se ShieldHelper está disponível
        if (typeof window.ShallHelper === 'undefined') {
          console.error('❌ ShieldHelper não encontrado. Certifique-se de incluir o script do PrimePay7');
          return false;
        }
        
        PrimePay7CardIntegration = new window.PrimePay7Integration();
        await PrimePay7CardIntegration.init(publicKey);
        
        console.log('🔐 PrimePay7 Card Integration inicializada');
        return true;
        
      } catch (error) {
        console.error('❌ Erro ao inicializar PrimePay<｜tool▁sep｜>:', error);
        return false;
      }
    }
    
    // Função para inicializar Rede
    async function initRedeCard() {
      try {
        // Buscar configurações da Rede
        const redeConfig = window.redeConfig || {};
        
        if (!redeConfig.pv || !redeConfig.token) {
          console.warn('⚠️ Configurações da Rede não encontradas');
          return false;
        }

        console.log('🔐 Rede Card Integration inicializada');
        return true;
      } catch (error) {
        console.error('❌ Erro ao inicializar Rede:', error);
        return false;
      }
    }

    // Função para processar pagamento com cartão Rede
    async function processCardPaymentRede(dados) {
        try {
            if (!RedeCardIntegration) {
                const initialized = await initRedeCard();
                if (!initialized) {
                    throw new Error('Rede não foi inicializada');
                }
                RedeCardIntegration = true; // Marcar como inicializada
            }

            // Preparar dados do pagamento para Rede
            const paymentData = {
                amount: dados.total,
                installments: 1,
                items: [{
                    title: dados.description || 'Pagamento',
                    quantity: 1,
                    unitPrice: dados.total,
                    tangible: false
                }],
                customer: {
                    name: dados.name || 'Cliente',
                    email: dados.email || 'redacted@example.invalid',
                    document: {
                        type: 'cpf',
                        number: dados.cpf || '08355037120'
                    }
                },
                card: {
                    number: dados.cardNumber?.replace(/\s/g, ''),
                    holderName: dados.cardHolderName,
                    expirationMonth: parseInt(dados.cardExpMonth),
                    expirationYear: parseInt(dados.cardExpYear),
                    cvv: dados.cardCvv
                },
                returnURL: window.location.origin + '/obrigado',
                postbackURL: window.location.origin + '/api/rede/webhook'
            };

            console.log('💳 Processando pagamento com Rede...', paymentData);

            // Enviar para o backend
            const response = await fetch('/api/card/payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    ...paymentData,
                    metodo: 'card_rede',
                    adquirente: 'rede'
                })
            });

            const result = await response.json();
            console.log('✅ Resposta da Rede:', result);

            return result;

        } catch (error) {
            console.error('❌ Erro ao processar pagamento Rede:', error);
            throw error;
        }
    }

    // Função para processar pagamento com cartão PrimePay7
    async function processCardPaymentPrimePay7(dados) {
      try {
        if (!PrimePay7CardIntegration) {
          await initPrimePay7Card();
        }
        
        if (!PrimePay7CardIntegration) {
          throw new Error('PrimePay7 não foi inicializada');
        }
        
        // Preparar dados do cartão
        const cardData = {
          number: dados.card_number ? dados.card_number.replace(/\s/g, '') : null,
          holderName: dados.card_holder_name || null,
          expMonth: dados.card_exp_month ? parseInt(dados.card_exp_month) : null,
          expYear: dados.card_exp_year ? parseInt(dados.card_exp_year) : null,
          cvv: dados.card_cvv || null
        };
        
        // Preparar dados do pagamento
        const paymentData = {
          amount: parseFloat(dados.valor_total),
          installments: parseInt(dados.installments) || 1,
          currency: 'BRL',
          cardData: cardData,
          customer: {
            name: dados.name,
            email: dados.email,
            document: dados.cpf ? dados.cpf.replace(/\D/g, '') : '',
            phone: dados.telefone ? dados.telefone.replace(/\D/g, '') : ''
          },
          items: [{
            name: dados.product_name || 'Produto',
            value: parseFloat(dados.valor_total) * 100, // valor em centavos
            amount: 1
          }]
        };
        
        console.log('💳 Processando pagamento com PrimePay7...', paymentData);
        
        // Processar pagamento completo
        const payload = await PrimePay7CardIntegration.processPayment(paymentData);
        
        // Adicionar dados específicos do checkout
        payload.checkout_id = dados.checkout_id;
        payload.metodo = 'card_primepay7'; // Identificar como PrimePay7
        
        return payload;
        
      } catch (error) {
        console.error('❌ Erro ao processar pagamento PrimePay7:', error);
        
        Swal.fire({
          title: 'Erro no Pagamento',
          text: error.message || 'Erro ao processar pagamento com cartão',
          icon: 'error',
          confirmButtonText: 'OK'
        });
        
        throw error;
      }
    }
    
    // Intercepitar clique no botão de pagamento para detectar método
    document.addEventListener('click', async function(e) {
      if (e.target.closest('#submit-payment') || e.target.closest('.checkout-btn')) {
        e.preventDefault();
        
        // Verificar se é método cartão
        const metodoSelecionado = document.querySelector('input[name="metodo"]:checked');
        if (!metodoSelecionado || metodoSelecionado.value !== 'card') {
          return; // Continuar com fluxo normal se não for cartão
        }
        
        // Verificar se deve usar PrimePay7 ou Rede
        const usarPrimePay7 = document.querySelector('#use-primepay7')?.checked || window.defaultAdquirante === 'primepay7';
        const usarRede = document.querySelector('#use-rede')?.checked || window.defaultAdquirante === 'rede';
        
        console.log('🔍 Verificando adquirente:', {
            usarPrimePay7,
            usarRede,
            defaultAdquirante: window.defaultAdquirante
        });

        if (usarPrimePay7) {
          try {
            // Preparar dados do formulário
            const dados = {
              checkout_id: document.querySelector('#checkout_id')?.value,
              name: document.querySelector('input[name="name"]')?.value,
              email: document.querySelector('input[name="email"]')?.value,
              cpf: document.querySelector('input[name="cpf"]')?.value,
              telefone: document.querySelector('input[name="telefone"]')?.value,
              card_number: document.querySelector('input[name="card_number"]')?.value,
              card_holder_name: document.querySelector('input[name="card_holder_name"]')?.value,
              card_exp_month: document.querySelector('select[name="card_exp_month"]')?.value,
              card_exp_year: document.querySelector('select[name="card_exp_year"]')?.value,
              card_cvv: document.querySelector('input[name="card_cvv"]')?.value,
              installments: document.querySelector('#select-parcela')?.value || 1,
              valor_total: document.querySelector('#valor_total')?.value || document.querySelector('input[name="valor_total"]')?.value,
              product_name: document.querySelector('#product_name')?.value || 'Produto'
            };
            
            // Processar com PrimePay7
            const payload = await processCardPaymentPrimePay7(dados);
            
            // Enviar para o servidor
            $.ajax({
              type: 'POST',
              url: '/checkout/gerarpedido',
              data: payload,
              success: function (response) {
                console.log('Resposta do servidor:', response);
                
                if (response.status === 'success') {
                  // Verificar se é 3DS REDIRECT
                  if (response.threeDS && response.threeDS.redirectUrl) {
                    // O redirecionamento já é feito automaticamente pelo finishThreeDS
                    console.log('🔄 Redirecionando para 3DS...');
                  } else {
                    Swal.fire('Sucesso!', `Pagamento processado!\nPedido: ${response.data?.id}`, 'success').then(function () {
                      if (response.things?.id) {
                        window.location.href = `/checkout/success?transaction_id=${response.data.id}`;
                      }
                    });
                  }
                } else {
                  Swal.fire('Erro', response.message || 'Verifique e tente novamente.', 'error');
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
                console.error('Erro no servidor:', jqXHR.responseJSON);
                Swal.fire('Erro', jqXHR.responseJSON?.message || 'Verifique e tente novamente.', 'error');
              }
            });
            
            return; // Sair da função para não executar o fluxo padrão
            
          } catch (error) {
            console.error('Erro no processamento PrimePay7:', error);
            // Continuar com o fluxo padrão em caso de erro
          }
        } else if (usarRede) {
          try {
            // Preparar dados do formulário
            const dados = {
              checkout_id: document.querySelector('#checkout_id')?.value,
              name: document.querySelector('input[name="name"]')?.value,
              email: document.querySelector('input[name="email"]')?.value,
              telefone: document.querySelector('input[name="telefone"]')?.value,
              cpf: document.querySelector('input[name="cpf"]')?.value,
              cardNumber: document.querySelector('input[name="card_number"]')?.value,
              cardExpMonth: document.querySelector('input[name="card_exp_month"]')?.value,
              cardExpYear: document.querySelector('input[name="card_exp_year"]')?.value,
              cardCvv: document.querySelector('input[name="card_cvv"]')?.value,
              cardHolderName: document.querySelector('input[name="card_holder_name"]')?.value,
              total: parseFloat(document.querySelector('#total')?.textContent?.replace(/[^\d,]/g, '').replace(',', '.') || '0'),
              description: document.querySelector('#description')?.textContent || 'Pagamento'
            };

            console.log('📋 Dados preparados para Rede:', dados);

            // Processar com Rede
            const payload = await processCardPaymentRede(dados);
            
            if (payload.status === 'success' || payload.status === 'approved') {
              // Redirecionar para página de sucesso
              window.location.href = '/obrigado?transaction_id=' + payload.transaction_id;
            } else if (payload.status === 'pending' || payload.status === 'processing') {
              // Mostrar mensagem de processamento
              Swal.fire({
                title: 'Pagamento em processamento',
                text: 'Seu pagamento está sendo processado. Aguarde...',
                icon: 'info',
                allowOutsideClick: false
              });
            } else {
              throw new Error(payload.message || 'Erro no processamento do pagamento');
            }
          } catch (error) {
            console.error('Erro no processamento Rede:', error);
            // Continuar com o fluxo padrão em caso de erro
          }
        }
        
        // Continuar com fluxo padrão se não usar PrimePay7 ou Rede ou houver erro
      }
    });
    
    console.log('🚀 Integração PrimePay7 e Rede carregada no checkout');
});

// ===============================
// 🎯 SISTEMA DE DETECÇÃO DE BANDEIRA
// ===============================

class CardBrandDetector {
    constructor() {
        this.brands = {
            visa: {
                prefix: /^4/,
                pattern: /^4[0-9]*$/,
                name: 'Visa',
                icon: window.location.origin + '/assets-checkout/img/visa.svg'
            },
            mastercard: {
                prefix: /^5[1-5]/,
                pattern: /^5[1-5][0-9]*$/,
                name: 'Mastercard',
                icon: window.location.origin + '/assets-checkout/img/mastercard.svg'
            },
            americanexpress: {
                prefix: /^3[47]/,
                pattern: /^3[47][0-9]*$/,
                name: 'American Express',
                icon: window.location.origin + '/assets-checkout/img/amex.svg'
            },
            hiper: {
                prefix: /^637/,
                pattern: /^637[0-9]*$/,
                name: 'Hiper',
                icon: window.location.origin + '/assets-checkout/img/discover.svg'
            },
            hipercard: {
                prefix: /^(606282|384140|384141|384142|384143|384144|384145|384146|384147|384148|384149)/,
                pattern: /^(606282|384140|384141|384142|384143|384144|384145|384146|384147|384148|384149)[0-9]*$/,
                name: 'Hipercard',
                icon: window.location.origin + '/assets-checkout/img/discover.svg'
            },
            elo: {
                prefix: /^4(0[1-9]|1[1-7])/,
                pattern: /^4(0[1-9]|1[1-7])[0-9]*$/,
                name: 'Elo',
                icon: window.location.origin + '/assets-checkout/img/discover.svg'
            },
            discover: {
                prefix: /^6(011|5[0-9]{2})/,
                pattern: /^6(011|5[0-9]{2})[0-9]*$/,
                name: 'Discover',
                icon: window.location.origin + '/assets-checkout/img/discover.svg'
            }
        };
        
        console.log('🎯 [BRAND DETECTOR] Sistema inicializado');
    }
    
    detectBrand(cardNumber) {
        const cleanNumber = cardNumber.replace(/\s/g, '').replace(/[^\d]/g, '');
        
            // Detecção imediata por prefixo (1 dígito ou mais)
        if (cleanNumber.length >= 1) {
            // Ordem específica para evitar conflitos (Visa antes de Elo)
            const specificBrands = ['visa', 'mastercard', 'americanexpress', 'hipercard', 'hiper', 'discover', 'elo'];
            
            for (const brand of specificBrands) {
                const config = this.brands[brand];
                
                // Primeiro teste: prefixo exato para detecção imediata
                if (config && config.prefix.test(cleanNumber)) {
                    console.log('✅ [BRAND DETECTOR] DETECTADO IMEDIATO:', brand.toUpperCase(), 'prefixo:', cleanNumber);
                    this.displayBrand(brand, config);
                    return;
                }
            }
            
            // Segundo teste: padrão geral para confirmação
            if (cleanNumber.length >= 4) {
                for (const brand of specificBrands) {
                    const config = this.brands[brand];
                    if (config && config.pattern.test(cleanNumber)) {
                        console.log('✅ [BRAND DETECTOR] DETECTADO CONFIRMADO:', brand.toUpperCase(), 'número:', cleanNumber);
                        this.displayBrand(brand, config);
                        return;
                    }
                }
            }
            
            // Se tem muitos dígitos mas não identificou
            if (cleanNumber.length >= 13) {
                console.log('❓ [BRAND DETECTOR] Não identificado:', cleanNumber);
                this.displayPlaceholder();
            }
        }
        
        // Limpar display se número for removido
        if (cleanNumber.length === 0) {
            this.displayDefault();
        }
    }
    
    displayBrand(brandType, config) {
        const brandDisplay = document.getElementById('card-brand-display');
        const brandIcon = document.getElementById('brand-icon');
        const hiddenInput = document.getElementById('card-brand');
        
        if (!brandDisplay || !brandIcon || !hiddenInput) {
            console.log('❌ [BRAND DETECTOR] Elementos não encontrados');
            return;
        }
        
        // Atualizar input oculto
        hiddenInput.value = brandType;
        
        // Atualizar visual
        brandDisplay.className = 'card-brand-indicator detected';
        
        // Mostrar ícone da bandeira detectada
        brandIcon.src = config.icon;
        brandIcon.style.display = 'block';
        brandIcon.className = 'brand-icon show';
        brandIcon.alt = config.name;
        
        console.log('✅ [BRAND DETECTOR] Exibindo:', config.name);
    }
    
    displayPlaceholder() {
        const brandDisplay = document.getElementById('card-brand-display');
        const brandIcon = document.getElementById('brand-icon');
        const hiddenInput = document.getElementById('card-brand');
        
        if (!brandDisplay || !brandIcon || !hiddenInput) return;
        
        // Limpar input oculto
        hiddenInput.value = '';
        
        // Mostrar placeholder para bandeiras não identificadas
        brandDisplay.className = 'card-brand-indicator placeholder';
        brandIcon.src = window.location.origin + '/assets-checkout/img/placeholder.svg';
        brandIcon.style.display = 'block';
        brandIcon.className = 'brand-icon show';
        brandIcon.alt = 'Bandeira não identificada';
        
        console.log('❓ [BRAND DETECTOR] Exibindo placeholder');
    }
    
    displayDefault() {
        const brandDisplay = document.getElementById('card-brand-display');
        const brandIcon = document.getElementById('brand-icon');
        const hiddenInput = document.getElementById('card-brand');
        
        if (!brandDisplay || !brandIcon || !hiddenInput) return;
        
        // Limpar input oculto
        hiddenInput.value = '';
        
        // Estado padrão - esconder ícone
        brandDisplay.className = 'card-brand-indicator';
        brandIcon.style.display = 'none';
        brandIcon.className = 'brand-icon';
    }
}

// Inicializar detector global
let brandDetector = null;

// Modificar a função showPaymentMethod existente
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 [BRAND DETECTOR] DOM carregado, configurando...');
    
    // Aguardar outros scripts carregarem
    setTimeout(() => {
        try {
            brandDetector = new CardBrandDetector();
            
            // Interceptar eventos de método de pagamento
            document.querySelectorAll('input[name="metodo"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'card') {
                        console.log('💳 [BRAND DETECTOR] Método cartão selecionado');
                        
                        // Aguardar DOM atualizar
                        setTimeout(() => {
                            const cardInput = document.getElementById('card-number');
                            console.log('🔍 [BRAND DETECTOR] Procurando campo card-number:', cardInput);
                            
                            if (cardInput) {
                                console.log('✅ [BRAND DETECTOR] Campo encontrado, valor atual:', cardInput.value);
                                
                                // Se já tem valor, detectar imediatamente
                                if (cardInput.value && cardInput.value.length >= 4) {
                                    console.log('🧪 [BRAND DETECTOR] Detectando valor existente:', cardInput.value);
                                    brandDetector.detectBrand(cardInput.value);
                                }
                                
                                // Adicionar listener para novos inputs
                                cardInput.addEventListener('input', (e) => {
                                    console.log('🔍 [BRAND DETECTOR] Input detectado:', e.target.value, 'Tamanho:', e.target.value.length);
                                    brandDetector.detectBrand(e.target.value);
                                });
                                
                                // NÃO testar automaticamente - apenas aguardar input do usuário
                            } else {
                                console.log('❌ [BRAND DETECTOR] Campo card-number NÃO encontrado!');
                                
                                // Tentar encontrar todos os inputs de cartão
                                const allInputs = document.querySelectorAll('input');
                                console.log('📋 [BRAND DETECTOR] Todos os inputs encontrados:', allInputs.length);
                                allInputs.forEach((input, index) => {
                                    if (input.id && input.id.includes('card') || 
                                        input.name && input.name.includes('card') || 
                                        input.placeholder && input.placeholder.includes('cartão')) {
                                        console.log(`📋 Input ${index}:`, {
                                            id: input.id,
                                            name: input.name, 
                                            placeholder: input.placeholder,
                                            value: input.value
                                        });
                                    }
                                });
                            }
                        }, 300);
                    } else if (this.value === 'pix') {
                        console.log('💜 [BRAND DETECTOR] Método PIX selecionado');
                        // Limpar detecção quando muda para PIX
                        if (brandDetector) {
                            brandDetector.displayDefault();
                        }
                    }
                });
            });
            
            console.log('✅ [BRAND DETECTOR] Sistema configurado com sucesso!');
            
        } catch (error) {
            console.error('💥 [BRAND DETECTOR] Erro ao configurar:', error);
        }
    }, 500);
});