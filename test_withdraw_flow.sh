#!/bin/bash

# Script para testar fluxo de saque e notificações
# Uso: ./test_withdraw_flow.sh

echo "🧪 TESTE DE FLUXO DE SAQUE E NOTIFICAÇÕES"
echo "=========================================="

# Configurações
API_URL="https://demo.gateway.shop/api"
TOKEN="82b404ab-fd93-48c6-a034-6eacbaa816b1"
SECRET="REDACTED_SECRET"
USER_ID="admin"

echo "📋 Configurações:"
echo "API URL: $API_URL"
echo "User ID: $USER_ID"
echo "Token: ${TOKEN:0:20}..."
echo ""

# Função para fazer requisição
make_request() {
    local method=$1
    local endpoint=$2
    local data=$3
    
    echo "🔍 Fazendo requisição: $method $endpoint"
    if [ -n "$data" ]; then
        echo "📤 Dados: $data"
        curl -X $method "$API_URL/$endpoint?token=$TOKEN&secret=$SECRET" \
            -H "Content-Type: application/json" \
            -d "$data" \
            -s | jq .
    else
        curl -X $method "$API_URL/$endpoint?token=$TOKEN&secret=$SECRET" \
            -H "Content-Type: application/json" \
            -s | jq .
    fi
    echo ""
}

# 1. Verificar saldo atual
echo "1️⃣ Verificando saldo atual..."
make_request "GET" "balance"

# 2. Registrar token de notificação (simulado)
echo "2️⃣ Registrando token de notificação..."
NOTIFICATION_TOKEN="ExponentPushToken[test-token-$(date +%s)]"
make_request "POST" "notifications/register-token" "{\"token\":\"$NOTIFICATION_TOKEN\",\"platform\":\"expo\",\"device_id\":\"test-device-$(date +%s)\"}"

# 3. Fazer saque
echo "3️⃣ Fazendo saque..."
WITHDRAW_DATA="{
    \"amount\": 5.00,
    \"pix_key\": \"11999999999\",
    \"pixKeyType\": \"phone\",
    \"idTransaction\": \"withdraw_$(date +%s)\",
    \"pin\": \"123456\",
    \"description\": \"Teste de saque via script\"
}"
make_request "POST" "pix/withdraw" "$WITHDRAW_DATA"

# 4. Verificar transações
echo "4️⃣ Verificando transações..."
make_request "GET" "transactions"

# 5. Verificar notificações
echo "5️⃣ Verificando notificações..."
make_request "GET" "notifications"

echo "✅ Teste concluído!"
echo "📋 Verifique os logs em:"
echo "   - storage/logs/app_requests.log"
echo "   - storage/logs/notifications.log"
echo "   - storage/logs/withdraw_flow.log"
echo ""
echo "🔍 Para monitorar em tempo real, execute:"
echo "   ./monitor_app_logs.sh"
