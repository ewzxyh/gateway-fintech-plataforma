// Checkout V2 - Sistema de Pagamento Integrado
// Baseado no checkout.js do V1 para manter compatibilidade

document.addEventListener('DOMContentLoaded', function () {
    // Configurações globais
    window.checkoutV2 = {
        currentStep: 1,
        totalSteps: 2,
        selectedPaymentMethod: 'pix',
        checkoutData: {},
        transactionId: null,
        paymentCode: null
    };

    // Aplicar máscaras nos campos
    applyInputMasks();
    
    // Inicializar sistema de pagamento
    initializePaymentSystem();
    
    // Configurar event listeners
    setupEventListeners();
});

// Aplicar máscaras nos campos de entrada
function applyInputMasks() {
    const telefone = document.querySelectorAll('input[name="userPhone"]');
    const cpf = document.querySelectorAll('input[name="document"]');
    const cep = document.querySelectorAll('input[name="zipCode"]');
    const name = document.querySelectorAll('input[name="name"]');

    if (telefone.length) {
        telefone.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 2) value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
                if (value.length > 8) value = value.slice(0, 10) + '-' + value.slice(10);
                e.target.value = value.slice(0, 15);
            });
        });
    }
    
    if (cpf.length) {
        cpf.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                e.target.value = value.slice(0, 14);
            });
        });
    }
    
    if (cep.length) {
        cep.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
                e.target.value = value.slice(0, 9);
            });
        });
    }
}

// Inicializar sistema de pagamento
function initializePaymentSystem() {
    // Criar seção de métodos de pagamento se não existir
    createPaymentMethodsSection();
    
    // Configurar dados do checkout
    setupCheckoutData();
}

// Criar seção de métodos de pagamento
function createPaymentMethodsSection() {
    const form = document.querySelector('form[data-v-24d558e9]');
    if (!form) return;

    // Verificar se já existe seção de pagamento
    const existingPaymentSection = document.querySelector('.payment-methods-section');
    if (existingPaymentSection) return;

    // Criar seção de métodos de pagamento
    const paymentSection = document.createElement('div');
    paymentSection.className = 'payment-methods-section';
    paymentSection.innerHTML = `
        <h2 data-v-24d558e9="">
            <span data-v-24d558e9="" class="orderNumber">3</span> Forma de Pagamento
        </h2>
        
        <div class="payment-methods">
            <div class="payment-method">
                <input type="radio" name="payment_method" id="pix_method" value="pix" checked>
                <label for="pix_method">
                    <i class="fa-brands fa-pix"></i>
                    <span>PIX</span>
                </label>
            </div>
            <div class="payment-method">
                <input type="radio" name="payment_method" id="card_method" value="card">
                <label for="card_method">
                    <i class="fa-solid fa-credit-card"></i>
                    <span>Cartão</span>
                </label>
            </div>
        </div>
        
        <!-- Seção PIX -->
        <div id="pix-payment-section" class="payment-section active">
            <div class="pix-info">
                <h4>Pague no PIX</h4>
                <h3>
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgZmlsbD0iIzAwZDNjNyIgdmlld0JveD0iMCAwIDE2IDE2Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNNi41IDBhLjUuNSAwIDAgMCAwIDFIN3YxLjA3QTcuMDAxIDcuMDAxIDAgMCAwIDggMTZhNyA3IDAgMCAwIDUuMjktMTEuNTg0LjUzMS41MzEgMCAwIDAgLjAxMy0uMDEybC4zNTQtLjM1NC4zNTMuMzU0YS41LjUgMCAxIDAgLjcwNy0uNzA3bC0xLjQxNC0xLjQxNWEuNS41IDAgMSAwLS43MDcuNzA3bC4zNTQuMzU0LS4zNTQuMzU0YS43MTcuNzE3IDAgMCAwLS4wMTIuMDEyQTYuOTczIDYuOTczIDAgMCAwIDkgMi4wNzFWMWguNWEuNS41IDAgMCAwIDAtMWgtM3ptMiA1LjZWOWEuNS41IDAgMCAxLS41LjVINC41YS41LjUgMCAwIDEgMC0xaDNWNS42YS41LjUgMCAxIDEgMSAxeiIgLz4KICAgICAgICAgICAgICAgICAgICAgICAgPC9zdmc+">
                    Imediato
                </h3>
                <p>Ao selecionar a opção PIX o código para pagamento estará disponível.</p>
            </div>
        </div>
        
        <!-- Seção Cartão -->
        <div id="card-payment-section" class="payment-section">
            <div class="card-form">
                <h4>Dados do Cartão</h4>
                <div class="form-group">
                    <label>Número do Cartão</label>
                    <input type="text" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19">
                </div>
                <div class="form-group">
                    <label>Nome no Cartão</label>
                    <input type="text" name="card_holder_name" placeholder="Nome como está no cartão">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Validade</label>
                        <div class="expiry-inputs">
                            <select name="card_exp_month">
                                <option value="">Mês</option>
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                            <select name="card_exp_year">
                                <option value="">Ano</option>
                                ${generateYearOptions()}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>CVV</label>
                        <input type="text" name="card_cvv" placeholder="000" maxlength="4">
                    </div>
                </div>
                <div class="form-group">
                    <label>Parcelamento</label>
                    <select name="installments">
                        <option value="1">1x de R$ ${formatCurrency(window.checkoutV2.checkoutData.total)}</option>
                        <option value="2">2x de R$ ${formatCurrency(window.checkoutV2.checkoutData.total / 2)}</option>
                        <option value="3">3x de R$ ${formatCurrency(window.checkoutV2.checkoutData.total / 3)}</option>
                        <option value="6">6x de R$ ${formatCurrency(window.checkoutV2.checkoutData.total / 6)}</option>
                        <option value="12">12x de R$ ${formatCurrency(window.checkoutV2.checkoutData.total / 12)}</option>
                    </select>
                </div>
            </div>
        </div>
    `;

    // Inserir antes do botão de compra
    const submitButton = form.querySelector('button[onclick="generateQRCode()"]');
    if (submitButton) {
        form.insertBefore(paymentSection, submitButton);
    }

    // Adicionar estilos CSS
    addPaymentStyles();
}

// Adicionar estilos CSS para os métodos de pagamento
function addPaymentStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .payment-methods-section {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .payment-methods {
            display: flex;
            gap: 15px;
            margin: 15px 0;
        }
        
        .payment-method {
            flex: 1;
        }
        
        .payment-method input[type="radio"] {
            display: none;
        }
        
        .payment-method label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .payment-method input[type="radio"]:checked + label {
            border-color: #007bff;
            background: #e3f2fd;
            color: #007bff;
        }
        
        .payment-section {
            display: none;
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: white;
        }
        
        .payment-section.active {
            display: block;
        }
        
        .pix-info h4 {
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .pix-info h3 {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .card-form .form-group {
            margin-bottom: 15px;
        }
        
        .card-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        
        .card-form input,
        .card-form select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .expiry-inputs {
            display: flex;
            gap: 10px;
        }
        
        .expiry-inputs select {
            flex: 1;
        }
        
        .qr-code-container {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .qr-code-container h4 {
            color: #28a745;
            margin-bottom: 15px;
        }
        
        .qr-code-container img {
            max-width: 256px;
            height: auto;
        }
        
        .pix-code {
            margin: 15px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            font-family: monospace;
            word-break: break-all;
        }
        
        .copy-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }
        
        .copy-button:hover {
            background: #0056b3;
        }
    `;
    document.head.appendChild(style);
}

// Configurar dados do checkout
function setupCheckoutData() {
    const form = document.querySelector('form[data-v-24d558e9]');
    if (!form) return;

    // Extrair dados do produto
    const valorInput = document.getElementById('valuedeposit');
    const clientIdInput = document.getElementById('clientId');
    const apiUrlInput = document.getElementById('apiUrl');

    window.checkoutV2.checkoutData = {
        total: parseFloat(valorInput?.value?.replace(/[^\d,]/g, '').replace(',', '.') || '0'),
        clientId: clientIdInput?.value || '',
        apiUrl: apiUrlInput?.value || '',
        productName: 'Produto',
        productId: '1'
    };

    console.log('Dados do checkout configurados:', window.checkoutV2.checkoutData);
}

// Configurar event listeners
function setupEventListeners() {
    // Event listener para mudança de método de pagamento
    document.addEventListener('change', function(e) {
        if (e.target.name === 'payment_method') {
            changePaymentMethod(e.target.value);
        }
    });

    // Event listener para o botão de compra
    document.addEventListener('click', function(e) {
        if (e.target.closest('button[onclick="generateQRCode()"]')) {
            e.preventDefault();
            processPayment();
        }
    });

    // Aplicar máscara no número do cartão
    document.addEventListener('input', function(e) {
        if (e.target.name === 'card_number') {
            applyCardNumberMask(e.target);
        }
        if (e.target.name === 'card_cvv') {
            applyCvvMask(e.target);
        }
    });
}

// Mudar método de pagamento
function changePaymentMethod(method) {
    window.checkoutV2.selectedPaymentMethod = method;
    
    // Esconder todas as seções
    document.querySelectorAll('.payment-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Mostrar seção selecionada
    const targetSection = document.getElementById(`${method}-payment-section`);
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    console.log('Método de pagamento alterado para:', method);
}

// Aplicar máscara no número do cartão
function applyCardNumberMask(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/(\d{4})(\d{4})(\d{4})(\d{4})/, '$1 $2 $3 $4');
    input.value = value.slice(0, 19);
}

// Aplicar máscara no CVV
function applyCvvMask(input) {
    let value = input.value.replace(/\D/g, '');
    input.value = value.slice(0, 4);
}

// Gerar opções de ano para o cartão
function generateYearOptions() {
    const currentYear = new Date().getFullYear();
    let options = '';
    for (let i = 0; i < 10; i++) {
        const year = currentYear + i;
        options += `<option value="${year}">${year}</option>`;
    }
    return options;
}

// Formatar moeda
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

// Processar pagamento
async function processPayment() {
    const method = window.checkoutV2.selectedPaymentMethod;
    
    // Validar dados do formulário
    if (!validateForm()) {
        return;
    }
    
    // Coletar dados do formulário
    const formData = collectFormData();
    
    // Mostrar loading
    showLoading(true);
    
    try {
        if (method === 'pix') {
            await processPixPayment(formData);
        } else if (method === 'card') {
            await processCardPayment(formData);
        }
    } catch (error) {
        console.error('Erro ao processar pagamento:', error);
        showError('Erro ao processar pagamento. Tente novamente.');
    } finally {
        showLoading(false);
    }
}

// Validar formulário
function validateForm() {
    const name = document.querySelector('input[name="name"]')?.value;
    const email = document.querySelector('input[name="yourEmail"]')?.value;
    const document = document.querySelector('input[name="document"]')?.value;
    
    if (!name || !email || !document) {
        showError('Por favor, preencha todos os campos obrigatórios.');
        return false;
    }
    
    if (window.checkoutV2.selectedPaymentMethod === 'card') {
        const cardNumber = document.querySelector('input[name="card_number"]')?.value;
        const cardHolder = document.querySelector('input[name="card_holder_name"]')?.value;
        const expMonth = document.querySelector('select[name="card_exp_month"]')?.value;
        const expYear = document.querySelector('select[name="card_exp_year"]')?.value;
        const cvv = document.querySelector('input[name="card_cvv"]')?.value;
        
        if (!cardNumber || !cardHolder || !expMonth || !expYear || !cvv) {
            showError('Por favor, preencha todos os dados do cartão.');
            return false;
        }
    }
    
    return true;
}

// Coletar dados do formulário
function collectFormData() {
    const form = document.querySelector('form[data-v-24d558e9]');
    const formData = new FormData(form);
    
    return {
        name: formData.get('name'),
        email: formData.get('yourEmail'),
        document: formData.get('document'),
        phone: formData.get('userPhone'),
        zipCode: formData.get('zipCode'),
        address: formData.get('address'),
        city: formData.get('city'),
        state: formData.get('state'),
        payment_method: window.checkoutV2.selectedPaymentMethod,
        total: window.checkoutV2.checkoutData.total,
        clientId: window.checkoutV2.checkoutData.clientId,
        apiUrl: window.checkoutV2.checkoutData.apiUrl
    };
}

// Processar pagamento PIX
async function processPixPayment(formData) {
    console.log('Processando pagamento PIX...');
    
    const payload = {
        "api-key": formData.clientId,
        "requestNumber": generateRequestNumber(),
        "dueDate": getDueDate(),
        "amount": formData.total,
        "client": {
            "name": formData.name,
            "document": formData.document.replace(/\D/g, ''),
            "email": formData.email
        }
    };
    
    try {
        const response = await fetch(formData.apiUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        console.log('Resposta PIX:', data);
        
        if (data.paymentCode) {
            window.checkoutV2.paymentCode = data.paymentCode;
            window.checkoutV2.transactionId = data.idTransaction;
            
            showPixPayment(data);
            startPaymentStatusCheck();
        } else {
            Swal.fire({
                title: 'Erro!',
                text: data.message || 'Erro ao gerar PIX',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            throw new Error(data.message || 'Erro ao gerar PIX');
        }
    } catch (error) {
        console.error('Erro no pagamento PIX:', error);
        throw error;
    }
}

// Processar pagamento com cartão
async function processCardPayment(formData) {
    console.log('Processando pagamento com cartão...');
    
    // Preparar dados do cartão
    const cardData = {
        number: formData.card_number?.replace(/\s/g, ''),
        holder_name: formData.card_holder_name,
        expiration_month: formData.card_exp_month,
        expiration_year: formData.card_exp_year,
        cvv: formData.card_cvv
    };
    
    // Determinar adquirente baseado na configuração
    const adquirente = window.defaultAdquirenteCard || 'pagarme';
    
    if (adquirente === 'stripe') {
        await processWithStripe(formData, cardData);
    } else if (adquirente === 'pagarme') {
        await processWithPagarMe(formData, cardData);
    } else {
        // Usar API padrão
        await processWithDefaultAPI(formData, cardData);
    }
}

// Processar com Stripe
async function processWithStripe(formData, cardData) {
    console.log('Processando com Stripe...');
    
    const payload = {
        checkout_id: window.checkoutV2.checkoutData.productId,
        name: formData.name,
        email: formData.email,
        cpf: formData.document.replace(/\D/g, ''),
        telefone: formData.phone,
        card_number: cardData.number,
        card_holder_name: cardData.holder_name,
        card_exp_month: cardData.expiration_month,
        card_exp_year: cardData.expiration_year,
        card_cvv: cardData.cvv,
        installments: formData.installments || 1,
        valor_total: formData.total,
        product_name: formData.name
    };
    
    try {
        const response = await fetch('/checkout/gerarpedido', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                title: 'Pagamento processado!',
                text: 'Seu pagamento foi processado com sucesso.',
                icon: 'success',
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                redirectToThankYou();
            });
        } else {
            Swal.fire({
                title: 'Erro!',
                text: data.message || 'Erro no pagamento',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            throw new Error(data.message || 'Erro no pagamento');
        }
    } catch (error) {
        console.error('Erro no Stripe:', error);
        throw error;
    }
}

// Processar com PagarMe
async function processWithPagarMe(formData, cardData) {
    console.log('Processando com PagarMe...');
    
    const payload = {
        checkout_id: window.checkoutV2.checkoutData.productId,
        name: formData.name,
        email: formData.email,
        cpf: formData.document.replace(/\D/g, ''),
        telefone: formData.phone,
        card_number: cardData.number,
        card_holder_name: cardData.holder_name,
        card_exp_month: cardData.expiration_month,
        card_exp_year: cardData.expiration_year,
        card_cvv: cardData.cvv,
        installments: formData.installments || 1,
        valor_total: formData.total,
        product_name: formData.name
    };
    
    try {
        const response = await fetch('/checkout/gerarpedido', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                title: 'Pagamento processado!',
                text: 'Seu pagamento foi processado com sucesso.',
                icon: 'success',
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                redirectToThankYou();
            });
        } else {
            Swal.fire({
                title: 'Erro!',
                text: data.message || 'Erro no pagamento',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            throw new Error(data.message || 'Erro no pagamento');
        }
    } catch (error) {
        console.error('Erro no PagarMe:', error);
        throw error;
    }
}

// Processar com API padrão
async function processWithDefaultAPI(formData, cardData) {
    console.log('Processando com API padrão...');
    
    const payload = {
        "api-key": formData.clientId,
        "requestNumber": generateRequestNumber(),
        "dueDate": getDueDate(),
        "amount": formData.total,
        "client": {
            "name": formData.name,
            "document": formData.document.replace(/\D/g, ''),
            "email": formData.email
        },
        "card": {
            "number": cardData.number,
            "holderName": cardData.holder_name,
            "expirationMonth": parseInt(cardData.expiration_month),
            "expirationYear": parseInt(cardData.expiration_year),
            "cvv": cardData.cvv
        }
    };
    
    try {
        const response = await fetch(formData.apiUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                title: 'Pagamento processado!',
                text: 'Seu pagamento foi processado com sucesso.',
                icon: 'success',
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                redirectToThankYou();
            });
        } else {
            Swal.fire({
                title: 'Erro!',
                text: data.message || 'Erro no pagamento',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            throw new Error(data.message || 'Erro no pagamento');
        }
    } catch (error) {
        console.error('Erro na API padrão:', error);
        throw error;
    }
}

// Mostrar pagamento PIX
function showPixPayment(data) {
    // Esconder formulário
    document.querySelectorAll('.properties').forEach(element => {
        element.style.display = 'none';
    });
    
    // Mostrar QR Code
    document.querySelectorAll('.box1').forEach(element => {
        element.style.display = 'block';
    });
    
    // Atualizar código PIX
    const qrCodeText = document.getElementById('qr-code-text');
    if (qrCodeText) {
        qrCodeText.textContent = data.paymentCode;
    }
    
    // Gerar QR Code
    if (typeof QRCode !== 'undefined') {
        const qrCodeElement = document.getElementById('qrcode');
        if (qrCodeElement) {
            qrCodeElement.innerHTML = '';
            new QRCode(qrCodeElement, {
                text: data.paymentCode,
                width: 256,
                height: 256
            });
            qrCodeElement.style.display = 'block';
        }
    }
    
    // Adicionar botão de copiar
    addCopyButton(data.paymentCode);
}

// Adicionar botão de copiar
function addCopyButton(paymentCode) {
    const qrCodeContainer = document.querySelector('.box1');
    if (!qrCodeContainer) return;
    
    const copyButton = document.createElement('button');
    copyButton.className = 'copy-button';
    copyButton.innerHTML = '<i class="fa-solid fa-copy"></i> Copiar Código PIX';
    copyButton.onclick = () => copyPixCode(paymentCode);
    
    qrCodeContainer.appendChild(copyButton);
}

// Copiar código PIX
function copyPixCode(paymentCode) {
    navigator.clipboard.writeText(paymentCode).then(() => {
        Swal.fire({
            title: 'Código PIX copiado!',
            text: 'O código foi copiado para a área de transferência.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }).catch(() => {
        Swal.fire({
            title: 'Erro!',
            text: 'Erro ao copiar código PIX',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

// Iniciar verificação de status do pagamento
function startPaymentStatusCheck() {
    const interval = setInterval(async () => {
        try {
            const status = await checkPaymentStatus();
            if (status === 'PAID_OUT') {
                clearInterval(interval);
                Swal.fire({
                    title: 'Pagamento confirmado!',
                    text: 'Seu pagamento foi processado com sucesso.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    redirectToThankYou();
                });
            }
        } catch (error) {
            console.error('Erro ao verificar status:', error);
        }
    }, 2000);
}

// Verificar status do pagamento
async function checkPaymentStatus() {
    const payload = {
        "api-key": window.checkoutV2.checkoutData.clientId,
        "idTransaction": window.checkoutV2.transactionId
    };
    
    const apiUrl = window.checkoutV2.checkoutData.apiUrl.replace('/v1/gateway/', '/v1/webhook/');
    
    try {
        const response = await fetch(apiUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        return data.status;
    } catch (error) {
        console.error('Erro ao verificar status:', error);
        throw error;
    }
}

// Gerar número de requisição
function generateRequestNumber() {
    return Math.floor(Math.random() * 1000000).toString();
}

// Obter data de vencimento
function getDueDate() {
    const date = new Date();
    date.setDate(date.getDate() + 1);
    return date.toISOString().split('T')[0];
}

// Mostrar loading
function showLoading(show) {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.style.display = show ? 'block' : 'none';
    }
}

// Mostrar erro
function showError(message) {
    Swal.fire({
        title: 'Erro!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}

// Mostrar sucesso
function showSuccess(message) {
    Swal.fire({
        title: 'Sucesso!',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK'
    });
}

// Redirecionar para página de agradecimento
function redirectToThankYou() {
    // Usar URL de redirecionamento se disponível
    const redirectUrl = window.redirect_tankyou || '/obrigado';
    window.location.href = redirectUrl;
}

// Substituir função original generateQRCode
window.generateQRCode = function() {
    processPayment();
};
