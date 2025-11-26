/**
 * Integração PrimePay7 com 3DS para Checkout EXEMPLO
 * Baseado na documentação oficial: https://bank.primepay7.com/docs/sales/create-sale
 */

class PrimePay7Integration {
    constructor() {
        this.publicKey = null;
        this.moduleName = null;
        this.threeDSSettings = null;
        this.iframeId = null;
        
        // Bind methods
        this.init = this.init.bind(this);
        this.setPublicKey = this.setPublicKey.bind(this);
        this.load3DSSettings = this.load3DSSettings.bind(this);
        this.prepareThreeDS = this.prepareThreeDS.bind(this);
        this.encryptCard = this.encryptCard.bind(this);
        this.finishThreeDS = this.finishThreeDS.bind(this);
        this.convertToCents = this.convertToCents.bind(this);
    }

    /**
     * Inicializa a integração PrimePay7
     * @param {string} publicKey - Chave pública da PrimePay7
     */
    async init(publicKey) {
        console.log('🚀 Inicializando PrimePay7 Integration...');
        
        try {
            this.publicKey = publicKey;
            this.moduleName = window["ShieldHelper"].getModuleName();
            
            // Configurar chave pública
            await window[this.moduleName].setPublicKey(this.publicKey);
            console.log('✅ Chave pública configurada');
            
            // Carregar configurações 3DS
            await this.load3DSSettings();
            
            console.log('✅ PrimePay7 Integration inicializada com sucesso');
            return true;
            
        } catch (error) {
            console.error('❌ Erro ao inicializar PrimePay7:', error);
            throw error;
        }
    }

    /**
     * Carrega as configurações 3DS da PrimePay7
     */
    async load3DSSettings() {
        console.log('🔐 Carregando configurações 3DS...');
        
        try {
            const response = await fetch(`https://api.primepay7.com/api/v1/js/get3dsSettings?publicKey=${this.publicKey}`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                },
            });

            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }

            this.threeDSSettings = await response.json();
            console.log('✅ Configurações 3DS carregadas:', this.threeDSSettings);

            // Configurar interface baseada nas settings
            this.configureUI(this.threeDSSettings);
            
            return this.threeDSSettings;
            
        } catch (error) {
            console.error('❌ Erro ao carregar configurações 3DS:', error);
            throw error;
        }
    }

    /**
     * Configura a interface baseada nas configurações 3DS
     * @param {Object} settings - Configurações recebidas da API
     */
    configureUI(settings) {
        console.log('🎨 Configurando interface baseada nas settings...');

        // Esconder formulário se necessário
        if (settings.hideCardForm) {
            console.log('📱 Escondendo formulário do cartão (hideCardForm = true)');
            const cardForm = document.getElementById('card-form');
            if (cardForm) {
                cardForm.style.display = 'none';
            }
        }

        // Configurar iframe para IFRAME 3DS
        if (settings.threeDSSecurityType === 'IFRAME') {
            console.log('🖼️ Configurando iframe para autenticação 3DS');
            this.setupIframe(settings);
        }

        // Configurar listener para validação do iframe
        if (settings.threeDSSecurityType === 'IFRAME') {
            this.setupIframeValidation();
        }
    }

    /**
     * Configura iframe para autenticação 3DS
     * @param {Object} settings - Configurações 3DS
     */
    setupIframe(settings) {
        try {
            this.iframeId = window["ShieldHelper"].getIframeId();
            
            // Criar iframe dinamicamente
            const iframe = document.createElement("iframe");
            iframe.id = this.iframeId;
            iframe.src = settings.iframeUrl;
            iframe.width = "100%";
            iframe.height = "400px";
            iframe.style.border = "none";
            iframe.style.borderRadius = "8px";

            // Inserir no container do formulário
            const container = document.getElementById('payment-form-container');
            if (container) {
                container.appendChild(iframe);
                console.log('✅ Iframe 3DS criado e inserido');
            } else {
                console.warn('⚠️ Container de pagamento não encontrado');
            }
            
        } catch (error) {
            console.error('❌ Erro ao configurar iframe:', error);
        }
    }

    /**
     * Configura listener para validação do iframe
     */
    setupIframeValidation() {
        try {
            window["ShieldHelper"].subscribeIframeFormValidation((data) => {
                console.log('🔍 Validação do iframe:', data);
                
                // Atualizar UI baseada na validação
                const submitButton = document.getElementById('submit-payment');
                if (submitButton) {
                    submitButton.disabled = data.hasError;
                    
                    if (data.hasError) {
                        submitButton.classList.add('disabled');
                        submitButton.title = 'Corrija os dados do cartão';
                    } else {
                        submitButton.classList.remove('disabled');
                        submitButton.title = 'Finalizar pagamento';
                    }
                }
            });
            
        } catch (error) {
            console.error('❌ Erro ao configurar validação iframe:', error);
        }
    }

    /**
     * Prepara parâmetros 3DS antes da criptografia
     * @param {Object} params - Parâmetros: {amount, installments, currency}
     */
    async prepareThreeDS(params) {
        console.log('⚙️ Preparando parâmetros 3DS...', params);
        
        try {
            const {amount, installments = 1, currency = 'BRL'} = params;
            
            // Converter valor para centavos
            const amountInCents = this.convertToCents(amount, currency);
            
            await window[this.moduleName].prepareThreeDS({
                amount: amountInCents,
                installments: installments,
            });
            
            console.log('✅ Parâmetros 3DS preparados');
            return true;
            
        } catch (error) {
            console.error('❌ Erro ao preparar 3DS:', error);
            throw error;
        }
    }

    /**
     * Criptografa dados do cartão
     * @param {Object} cardData - Dados do cartão ou null para formulário externo
     */
    async encryptCard(cardData = null) {
        console.log('🔒 Criptografando dados do cartão...');
        
        try {
            if (this.threeDSSettings && this.threeDSSettings.hideCardForm) {
                // Formulário fora do nosso controle
                console.log('📱 Usando dados null (formulário externo)');
                cardData = {
                    number: null,
                    holderName: null,
                    expMonth: null,
                    expYear: null,
                    cvv: null,
                };
            }

            const token = await window[this.moduleName].encrypt(cardData);
            console.log('✅ Token gerado:', token);
            return token;
            
        } catch (error) {
            console.error('❌ Erro ao criptografar cartão:', error);
            throw error;
        }
    }

    /**
     * Finaliza processo 3DS
     * @param {Object} transaction - Dados da transação do servidor
     * @param {Object} options - Opções (disableRedirect)
     */
    async finishThreeDS(transaction, options = {}) {
        console.log('🏁 Finalizando processo 3DS...', transaction);
        
        try {
            const result = await window["ShieldHelper"].finishThreeDS(transaction, options);
            console.log('✅ Processo 3DS finalizado');
            return result;
            
        } catch (error) {
            console.error('❌ Erro ao finalizar 3DS:', error);
            throw error;
        }
    }

    /**
     * Converte valor decimal para centavos
     * @param {number} amount - Valor decimal
     * @param {string} currency - Moeda
     */
    convertToCents(amount, currency = 'BRL') {
        return parseInt((amount * 100).toFixed(0));
    }

    /**
     * Processa pagamento completo com cartão
     * @param {Object} paymentData - Dados do pagamento
     */
    async processPayment(paymentData) {
        console.log('💳 Processando pagamento completo...', paymentData);
        
        try {
            // 1. Preparar 3DS
            await this.prepareThreeDS({
                amount: paymentData.amount,
                installments: paymentData.installments,
                currency: paymentData.currency || 'BRL'
            });

            // 2. Criptografar cartão
            const token = await this.encryptCard(paymentData.cardData);

            // 3. Preparar payload para API
            const payload = {
                paymentMethod: 'credit_card',
                amount: this.convertToCents(paymentData.amount, paymentData.currency || 'BRL'),
                currency: paymentData.currency || 'BRL',
                installments: paymentData.installments || 1,
                items: paymentData.items || [],
                card: {
                    hash: token
                },
                customer: paymentData.customer || {}
            };

            // Adicionar returnURL se necessário (REDIRECT 3DS)
            if (this.threeDSSettings.threeDSSecurityType === 'REDIRECT') {
                payload.returnURL = window.location.origin + '/checkout/callback';
            }

            console.log('📤 Payload final:', payload);
            return payload;
            
        } catch (error) {
            console.error('❌ Erro ao processar pagamento:', error);
            throw error;
        }
    }
}

// Instância global
window.PrimePay7Integration = PrimePay7Integration;

// Função helper para facilitar uso
window.initPrimePay7Card = async function(publicKey) {
    const integration = new PrimePay7Integration();
    await integration.init(publicKey);
    return integration;
};

console.log('🚀 PrimePay7 Integration carregada!');
