#!/bin/bash

# Script melhorado para monitorar logs do app
# Uso: ./monitor_app_logs.sh

echo "🔍 MONITOR DE LOGS DO APP EXEMPLO - VERSÃO MELHORADA"
echo "=================================================="
echo "Pressione Ctrl+C para sair"
echo ""

LOG_DIR="/home/gateway-demo/htdocs/demo.gateway.shop/storage/logs"
TODAY=$(date +%Y-%m-%d)

# Função para mostrar logs com cores
show_logs() {
    local log_file=$1
    local log_name=$2
    local color=$3
    
    if [ -f "$log_file" ]; then
        echo -e "\033[${color}m📋 $log_name\033[0m"
        echo "----------------------------------------"
        tail -f "$log_file" 2>/dev/null | while read line; do
            # Destacar diferentes tipos de log
            if echo "$line" | grep -q "ERROR\|ERRO"; then
                echo -e "\033[31m❌ $line\033[0m"
            elif echo "$line" | grep -q "SUCCESS\|SUCESSO\|✅"; then
                echo -e "\033[32m✅ $line\033[0m"
            elif echo "$line" | grep -q "WARNING\|AVISO"; then
                echo -e "\033[33m⚠️  $line\033[0m"
            elif echo "$line" | grep -q "API REQUEST"; then
                echo -e "\033[36m🔍 $line\033[0m"
            elif echo "$line" | grep -q "NOTIFICAÇÃO"; then
                echo -e "\033[35m📱 $line\033[0m"
            elif echo "$line" | grep -q "SAQUE"; then
                echo -e "\033[34m💰 $line\033[0m"
            else
                echo "   $line"
            fi
        done &
    else
        echo -e "\033[33m⚠️  $log_name: Arquivo não encontrado\033[0m"
    fi
}

# Função para limpar processos ao sair
cleanup() {
    echo ""
    echo "🛑 Parando monitoramento..."
    pkill -f "tail -f"
    exit 0
}

# Capturar Ctrl+C
trap cleanup SIGINT

echo "📊 Monitorando logs de hoje: $TODAY"
echo ""

# Monitorar logs com cores diferentes
show_logs "$LOG_DIR/app_requests-$TODAY.log" "REQUISIÇÕES DO APP" "36" &
show_logs "$LOG_DIR/notifications-$TODAY.log" "NOTIFICAÇÕES" "35" &
show_logs "$LOG_DIR/withdraw_flow-$TODAY.log" "FLUXO DE SAQUES" "34" &
show_logs "$LOG_DIR/laravel.log" "LOG PRINCIPAL" "37" &

# Aguardar indefinidamente
wait
