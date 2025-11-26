#!/bin/bash

# Script para monitorar logs do app em tempo real
# Uso: ./monitor_app_logs.sh

echo "🔍 MONITOR DE LOGS DO APP EXEMPLO"
echo "================================="
echo "Pressione Ctrl+C para sair"
echo ""

# Criar diretório de logs se não existir
mkdir -p /home/gateway-demo/htdocs/demo.gateway.shop/storage/logs

# Monitorar logs de requisições do app
echo "📱 Monitorando requisições do app..."
tail -f /home/gateway-demo/htdocs/demo.gateway.shop/storage/logs/app_requests.log 2>/dev/null &
APP_REQUESTS_PID=$!

# Monitorar logs de notificações
echo "📲 Monitorando notificações..."
tail -f /home/gateway-demo/htdocs/demo.gateway.shop/storage/logs/notifications.log 2>/dev/null &
NOTIFICATIONS_PID=$!

# Monitorar logs de saques
echo "💰 Monitorando fluxo de saques..."
tail -f /home/gateway-demo/htdocs/demo.gateway.shop/storage/logs/withdraw_flow.log 2>/dev/null &
WITHDRAW_PID=$!

# Monitorar log principal do Laravel
echo "📋 Monitorando log principal..."
tail -f /home/gateway-demo/htdocs/demo.gateway.shop/storage/logs/laravel.log 2>/dev/null &
LARAVEL_PID=$!

# Função para limpar processos ao sair
cleanup() {
    echo ""
    echo "🛑 Parando monitoramento..."
    kill $APP_REQUESTS_PID 2>/dev/null
    kill $NOTIFICATIONS_PID 2>/dev/null
    kill $WITHDRAW_PID 2>/dev/null
    kill $LARAVEL_PID 2>/dev/null
    exit 0
}

# Capturar Ctrl+C
trap cleanup SIGINT

# Aguardar indefinidamente
wait
