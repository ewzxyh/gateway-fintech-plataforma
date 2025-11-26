/**
 * Validação de CPF e CNPJ com identificação automática
 * Autor: Sistema EXEMPLO
 */

class CpfCnpjValidator {
    constructor() {
        this.cpfCnpjInput = null;
        this.tipoDocumento = null;
        this.contratoSocialField = null;
        this.init();
    }

    init() {
        this.cpfCnpjInput = document.getElementById('cpf-cnpj');
        this.contratoSocialField = document.getElementById('contrato-social-field');
        
        if (this.cpfCnpjInput) {
            this.cpfCnpjInput.addEventListener('input', (e) => this.handleInput(e));
            this.cpfCnpjInput.addEventListener('blur', (e) => this.validateDocument(e));
        }
    }

    handleInput(event) {
        let value = event.target.value.replace(/\D/g, ''); // Remove caracteres não numéricos
        
        // Identifica se é CPF ou CNPJ baseado no tamanho
        if (value.length <= 11) {
            this.tipoDocumento = 'cpf';
            this.formatCpf(value);
        } else if (value.length <= 14) {
            this.tipoDocumento = 'cnpj';
            this.formatCnpj(value);
        }
        
        // Mostra/oculta campo de contrato social
        this.toggleContratoSocialField();
    }

    formatCpf(value) {
        // Formata CPF: 000.000.000-00
        let formatted = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        if (value.length <= 3) {
            formatted = value;
        } else if (value.length <= 6) {
            formatted = value.replace(/(\d{3})(\d+)/, '$1.$2');
        } else if (value.length <= 9) {
            formatted = value.replace(/(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
        }
        this.cpfCnpjInput.value = formatted;
    }

    formatCnpj(value) {
        // Formata CNPJ: 00.000.000/0000-00
        let formatted = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
        if (value.length <= 2) {
            formatted = value;
        } else if (value.length <= 5) {
            formatted = value.replace(/(\d{2})(\d+)/, '$1.$2');
        } else if (value.length <= 8) {
            formatted = value.replace(/(\d{2})(\d{3})(\d+)/, '$1.$2.$3');
        } else if (value.length <= 12) {
            formatted = value.replace(/(\d{2})(\d{3})(\d{3})(\d+)/, '$1.$2.$3/$4');
        }
        this.cpfCnpjInput.value = formatted;
    }

    toggleContratoSocialField() {
        if (this.contratoSocialField) {
            if (this.tipoDocumento === 'cnpj') {
                this.contratoSocialField.style.display = 'block';
                this.contratoSocialField.querySelector('input').required = true;
            } else {
                this.contratoSocialField.style.display = 'none';
                this.contratoSocialField.querySelector('input').required = false;
            }
        }
    }

    validateDocument(event) {
        let value = event.target.value.replace(/\D/g, '');
        let isValid = false;
        let message = '';

        if (value.length === 11) {
            isValid = this.validateCpf(value);
            message = isValid ? 'CPF válido' : 'CPF inválido';
        } else if (value.length === 14) {
            isValid = this.validateCnpj(value);
            message = isValid ? 'CNPJ válido' : 'CNPJ inválido';
        } else if (value.length > 0) {
            message = 'Documento incompleto';
        }

        this.showValidationMessage(message, isValid);
    }

    validateCpf(cpf) {
        // Remove caracteres não numéricos
        cpf = cpf.replace(/\D/g, '');
        
        // Verifica se tem 11 dígitos
        if (cpf.length !== 11) return false;
        
        // Verifica se todos os dígitos são iguais
        if (/^(\d)\1{10}$/.test(cpf)) return false;
        
        // Validação do primeiro dígito verificador
        let sum = 0;
        for (let i = 0; i < 9; i++) {
            sum += parseInt(cpf.charAt(i)) * (10 - i);
        }
        let remainder = sum % 11;
        let firstDigit = remainder < 2 ? 0 : 11 - remainder;
        
        if (parseInt(cpf.charAt(9)) !== firstDigit) return false;
        
        // Validação do segundo dígito verificador
        sum = 0;
        for (let i = 0; i < 10; i++) {
            sum += parseInt(cpf.charAt(i)) * (11 - i);
        }
        remainder = sum % 11;
        let secondDigit = remainder < 2 ? 0 : 11 - remainder;
        
        return parseInt(cpf.charAt(10)) === secondDigit;
    }

    validateCnpj(cnpj) {
        // Remove caracteres não numéricos
        cnpj = cnpj.replace(/\D/g, '');
        
        // Verifica se tem 14 dígitos
        if (cnpj.length !== 14) return false;
        
        // Verifica se todos os dígitos são iguais
        if (/^(\d)\1{13}$/.test(cnpj)) return false;
        
        // Validação do primeiro dígito verificador
        let sum = 0;
        let weight = 5;
        for (let i = 0; i < 12; i++) {
            sum += parseInt(cnpj.charAt(i)) * weight;
            weight = weight === 2 ? 9 : weight - 1;
        }
        let remainder = sum % 11;
        let firstDigit = remainder < 2 ? 0 : 11 - remainder;
        
        if (parseInt(cnpj.charAt(12)) !== firstDigit) return false;
        
        // Validação do segundo dígito verificador
        sum = 0;
        weight = 6;
        for (let i = 0; i < 13; i++) {
            sum += parseInt(cnpj.charAt(i)) * weight;
            weight = weight === 2 ? 9 : weight - 1;
        }
        remainder = sum % 11;
        let secondDigit = remainder < 2 ? 0 : 11 - remainder;
        
        return parseInt(cnpj.charAt(13)) === secondDigit;
    }

    showValidationMessage(message, isValid) {
        // Remove mensagem anterior
        const existingMessage = this.cpfCnpjInput.parentNode.querySelector('.validation-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        // Cria nova mensagem
        const messageElement = document.createElement('small');
        messageElement.className = `validation-message ${isValid ? 'text-success' : 'text-danger'}`;
        messageElement.textContent = message;
        
        this.cpfCnpjInput.parentNode.appendChild(messageElement);
    }

    getTipoDocumento() {
        return this.tipoDocumento;
    }

    getDocumentoLimpo() {
        return this.cpfCnpjInput ? this.cpfCnpjInput.value.replace(/\D/g, '') : '';
    }
}

// Inicializa quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    window.cpfCnpjValidator = new CpfCnpjValidator();
});
