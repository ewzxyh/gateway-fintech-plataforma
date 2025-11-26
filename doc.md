---
title: "{{GATEWAY_NAME}} Gateway API"
excerpt: "Gateway Completo de Pagamentos - Solução robusta com múltiplos adquirentes e sistema de taxas flexível"
category: "API Reference"
order: 1
---

# {{GATEWAY_NAME}} Gateway API

> **Gateway Completo de Pagamentos** - Solução robusta com múltiplos adquirentes e sistema de taxas flexível

## Visão Geral

O {{GATEWAY_NAME}} Gateway é uma solução completa para integração de pagamentos, oferecendo múltiplos adquirentes, sistema de taxas flexível, controle de IPs e webhooks em tempo real.

### Características Principais

- ✅ **Gateway Unificado**: Uma única integração para todos os pagamentos
- ✅ **Sistema de Taxas Flexível**: Taxas personalizadas por usuário
- ✅ **PIX IN/OUT**: Depósitos e saques instantâneos
- ✅ **Sistema de Splits**: Distribuição automática de pagamentos
- ✅ **Controle de IP**: Segurança avançada para saques
- ✅ **Webhooks**: Notificações em tempo real
- ✅ **Relatórios**: Dashboard completo de transações
- ✅ **Sistema de Níveis**: Diferentes tipos de usuários
- ✅ **Alta Disponibilidade**: Sistema redundante com múltiplos processadores

## Autenticação

Todas as requisições devem incluir suas credenciais de API:

```http
POST {{APP_URL}}/api/wallet/deposit/payment
Content-Type: application/json
```

### Parâmetros de Autenticação

| Parâmetro | Tipo | Obrigatório | Descrição |
|-----------|------|-------------|-----------|
| `token` | string | Sim | Seu token de API |
| `secret` | string | Sim | Sua chave secreta |

---

## Endpoints

### 💰 PIX IN (Depósito)

Cria uma transação de depósito PIX e retorna o QR Code para pagamento.

```http
POST /api/wallet/deposit/payment
```

#### Corpo da Requisição

```json
{
  "token": "seu_token_aqui",
  "secret": "sua_chave_secreta",
  "amount": 100.00,
  "debtor_name": "João Silva",
  "email": "redacted@example.invalid",
  "debtor_document_number": "12345678901",
  "phone": "11999999999",
  "method_pay": "pix",
  "postback": "https://seusite.com/webhook",
  "split_email": "redacted@example.invalid",
  "split_username": "@admin",
  "split_percentage": 10.0
}
```

#### Parâmetros

| Parâmetro | Tipo | Obrigatório | Descrição |
|-----------|------|-------------|-----------|
| `token` | string | Sim | Token de autenticação |
| `secret` | string | Sim | Chave secreta |
| `amount` | decimal | Sim | Valor do depósito (mín: R$ 1,00) |
| `debtor_name` | string | Sim | Nome do devedor/cliente |
| `email` | string | Sim | Email do cliente |
| `debtor_document_number` | string | Não | CPF/CNPJ do cliente |
| `phone` | string | Sim | Telefone do cliente |
| `method_pay` | string | Sim | Método de pagamento (ex: "pix") |
| `postback` | string | Sim | URL do webhook para notificações |
| `split_email` | string | Não | Email do destinatário do split |
| `split_username` | string | Não | Username para split (ex: @admin) |
| `split_percentage` | decimal | Não | Percentual do split (0-100) |

#### Resposta de Sucesso

```json
{
  "success": true,
  "data": {
    "idTransaction": "dep_1234567890",
    "status": "pending",
    "amount": 100.00,
    "qr_code": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
    "qr_code_text": "00020126580014br.gov.bcb.pix...",
    "expires_at": "2025-01-15T15:30:00Z",
    "created_at": "2025-01-15T15:00:00Z"
  }
}
```

---

### 💸 PIX OUT (Saque)

Processa um saque PIX para a conta do cliente.

```http
POST {{APP_URL}}/api/wallet/saque/payment
```

#### Corpo da Requisição

```json
{
  "token": "seu_token_aqui",
  "secret": "sua_chave_secreta",
  "amount": 50.00,
  "pixKey": "11999999999",
  "pixKeyType": "phone",
  "baasPostbackUrl": "web"
}
```

#### Parâmetros

| Parâmetro | Tipo | Obrigatório | Descrição |
|-----------|------|-------------|-----------|
| `token` | string | Sim | Token de autenticação |
| `secret` | string | Sim | Chave secreta |
| `amount` | decimal | Sim | Valor do saque |
| `pixKey` | string | Sim | Chave PIX de destino |
| `pixKeyType` | string | Sim | Tipo da chave: `cpf`, `cnpj`, `email`, `phone`, `random` |
| `description` | string | Não | Descrição da transação |

#### Resposta de Sucesso

```json
{
  "success": true,
  "data": {
    "idTransaction": "out_1234567890",
    "status": "processing",
    "amount": 50.00,
    "pixKey": "11999999999",
    "created_at": "2025-01-15T15:00:00Z"
  }
}
```

---

### 📊 Status da Transação

Consulta o status de uma transação específica.

```http
POST /api/status
```

#### Corpo da Requisição

```json
{
  "idTransaction": "dep_1234567890"
}
```

#### Resposta

```json
{
  "success": true,
  "data": {
    "idTransaction": "dep_1234567890",
    "status": "paid",
    "amount": 100.00,
    "paid_at": "2025-01-15T15:05:00Z",
    "created_at": "2025-01-15T15:00:00Z"
  }
}
```

---

## Webhooks

O PlayGame envia notificações em tempo real para sua URL de webhook quando o status de uma transação muda.

### Configuração

Configure sua URL de webhook no campo `postback` ao criar uma transação:

```json
{
  "postback": "https://seudominio.com/webhook/playgame"
}
```

### Payload do Webhook

#### PIX IN (Depósito Pago)

```json
{
  "idTransaction": "dep_1234567890",
  "status": "paid",
  "typeTransaction": "PIX",
  "amount": 100.00,
  "debtor_name": "João Silva",
  "email": "redacted@example.invalid",
  "debtor_document_number": "12345678901",
  "phone": "11999999999",
  "created_at": "2025-01-15T15:00:00Z",
  "paid_at": "2025-01-15T15:05:00Z",
  "split_processed": true,
  "split_amount": 10.00,
  "split_recipient": "redacted@example.invalid"
}
```

#### PIX OUT (Saque Processado)

```json
{
  "idTransaction": "out_1234567890",
  "status": "completed",
  "typeTransaction": "PIX",
  "amount": 50.00,
  "pixKey": "11999999999",
  "externalId": "EXT_REF_1234567890",
  "created_at": "2025-01-15T15:00:00Z",
  "completed_at": "2025-01-15T15:10:00Z"
}
```

### Códigos de Status

| Status | Descrição |
|--------|-----------|
| `pending` | Aguardando pagamento |
| `paid` | Pago com sucesso |
| `processing` | Processando |
| `completed` | Concluído |
| `failed` | Falhou |
| `cancelled` | Cancelado |

---

## Sistema de Splits

O PlayGame suporta distribuição automática de pagamentos entre múltiplos destinatários.

### Como Funciona

1. **Configuração**: Adicione `split_email` e `split_percentage` na requisição
2. **Processamento**: Após confirmação do pagamento, o split é processado automaticamente
3. **Notificação**: O webhook inclui informações sobre o split processado

### Exemplo de Cálculo

```json
{
  "amount": 1000.00,
  "split_percentage": 15.0,
  "split_amount": 150.00,
  "net_amount": 850.00
}
```

### Tipos de Split Suportados

- **Percentual**: Baseado em percentual do valor total
- **Valor Fixo**: Valor específico em reais
- **Parceiro**: Para afiliados e parceiros
- **Afiliado**: Sistema de comissões

---

## Controle de IP

Para maior segurança, configure IPs permitidos para operações de saque.

### Configuração

1. Acesse seu perfil em `/my-profile`
2. Vá para a aba "Credenciais"
3. Adicione os IPs permitidos na seção "IP's Permitidos"

### Formatos Suportados

- **IP Único**: `192.168.1.1`
- **Range CIDR**: `192.168.1.0/24`
- **Wildcard**: `192.168.1.*`

### Comportamento

- **PIX IN**: Sem restrição de IP
- **PIX OUT**: Apenas IPs autorizados podem realizar saques

---

## Códigos de Erro

| Código | Descrição |
|--------|-----------|
| `400` | Dados inválidos |
| `401` | Não autorizado |
| `403` | IP não autorizado |
| `404` | Transação não encontrada |
| `500` | Erro interno do servidor |

---

## Limites e Taxas

### Limites por Transação

- **Depósito Mínimo**: R$ 1,00
- **Depósito Máximo**: R$ 50.000,00
- **Saque Mínimo**: R$ 10,00
- **Saque Máximo**: R$ 20.000,00

### Taxas

As taxas são configuradas por usuário e podem variar conforme o adquirente:

- **PIX IN**: 1,5% a 3,5%
- **PIX OUT**: 2,0% a 4,0%

---

## Exemplos de Integração

### JavaScript (Node.js)

```javascript
const axios = require('axios');

// Criar depósito
async function criarDeposito(dados) {
  try {
    const response = await axios.post('https://playgameoficial.com.br/api/wallet/deposit/payment', {
      token: 'seu_token',
      secret: 'sua_chave',
      amount: dados.valor,
      debtor_name: dados.nome,
      email: dados.email,
      debtor_document_number: dados.cpf,
      phone: dados.telefone,
      method_pay: 'pix',
      postback: 'https://seusite.com/webhook'
    });
    
    return response.data;
  } catch (error) {
    console.error('Erro ao criar depósito:', error.response.data);
  }
}
```

### PHP

```php
<?php
// Criar depósito
function criarDeposito($dados) {
    $url = 'https://playgameoficial.com.br/api/wallet/deposit/payment';
    
    $payload = [
        'token' => 'seu_token',
        'secret' => 'sua_chave',
        'amount' => $dados['valor'],
        'debtor_name' => $dados['nome'],
        'email' => $dados['email'],
        'debtor_document_number' => $dados['cpf'],
        'phone' => $dados['telefone'],
        'method_pay' => 'pix',
        'postback' => 'https://seusite.com/webhook'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
?>
```

### Python

```python
import requests
import json

def criar_deposito(dados):
    url = 'https://playgameoficial.com.br/api/wallet/deposit/payment'
    
    payload = {
        'token': 'seu_token',
        'secret': 'sua_chave',
        'amount': dados['valor'],
        'debtor_name': dados['nome'],
        'email': dados['email'],
        'debtor_document_number': dados['cpf'],
        'phone': dados['telefone'],
        'method_pay': 'pix',
        'postback': 'https://seusite.com/webhook'
    }
    
    response = requests.post(url, json=payload)
    return response.json()
```

---

## Sistema de Taxas Flexível

O Gateway oferece um sistema único de taxas flexíveis que permite configuração personalizada por usuário.

### Como Funciona

1. **Taxas Flexíveis**: Valores baixos com taxa fixa, valores altos com taxa percentual
2. **Taxas Personalizadas**: Cada usuário pode ter suas próprias configurações
3. **Priorização**: Taxas personalizadas > Taxas globais > Taxas padrão

### Exemplo de Configuração

```json
{
  "valor_minimo_flexivel": 5.00,
  "taxa_fixa_baixos": 1.20,
  "taxa_percentual_altos": 3.75
}
```

### Cálculo de Taxas

- **Depósito R$ 3,00**: Taxa = R$ 1,20 (taxa fixa)
- **Depósito R$ 10,00**: Taxa = R$ 0,38 (3,75% de R$ 10,00)

### Tipos de Taxas Suportadas

- **Depósito**: Taxa percentual + taxa fixa + valor mínimo
- **Saque Dashboard**: Taxa percentual PIX personalizada
- **Saque API**: Taxa percentual API personalizada
- **Saque Crypto**: Taxa percentual criptomoedas

---

## Gateway Unificado {{GATEWAY_NAME}}

O {{GATEWAY_NAME}} é um gateway unificado que simplifica toda a complexidade dos pagamentos:

### Vantagens do {{GATEWAY_NAME}}

- **Integração Única**: Uma única API para todos os tipos de pagamento
- **Alta Disponibilidade**: Sistema redundante com múltiplos processadores
- **Processamento Instantâneo**: PIX e cartões em tempo real
- **Segurança Avançada**: Criptografia e validações completas
- **Webhooks Automáticos**: Notificações em tempo real
- **Suporte 24/7**: Assistência técnica especializada

### Como Funciona

O {{GATEWAY_NAME}} gerencia automaticamente toda a complexidade por trás dos pagamentos. Você integra uma vez e recebe todos os benefícios de múltiplos processadores sem se preocupar com configurações técnicas.

---

## Suporte

Para dúvidas ou suporte técnico:

- **Email**: suporte@{{GATEWAY_NAME}}.com
- **Documentação**: {{APP_URL}}/documentacao
- **Demo**: {{APP_URL}}

---

**{{GATEWAY_NAME}} Gateway** - Soluções completas em pagamentos
