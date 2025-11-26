#!/bin/bash

# Script para analisar logs e identificar problemas
# Uso: ./analyze_logs.sh

echo "🔍 ANÁLISE DE LOGS DO APP EXEMPLO"
echo "================================"

LOG_DIR="/home/gateway-demo/htdocs/demo.gateway.shop/storage/logs"

# Função para analisar arquivo de log
analyze_log() {
    local log_file=$1
    local log_name=$2
    
    if [ -f "$log_file" ]; then
        echo ""
        echo "📋 $log_name"
        echo "----------------------------------------"
        echo "📊 Estatísticas:"
        echo "   Total de linhas: $(wc -l < "$log_file")"
        echo "   Tamanho: $(du -h "$log_file" | cut -f1)"
        echo "   Última modificação: $(stat -c %y "$log_file")"
        
        echo ""
        echo "🔍 Últimas 10 entradas:"
        tail -10 "$log_file" | while read line; do
            echo "   $line"
        done
        
        echo ""
        echo "❌ Erros encontrados:"
        grep -i "error\|erro\|failed\|falhou" "$log_file" | tail -5 | while read line; do
            echo "   $line"
        done
        
        echo ""
        echo "✅ Sucessos encontrados:"
        grep -i "success\|sucesso\|completed\|concluído" "$log_file" | tail -5 | while read line; do
            echo "   $line"
        done
    else
        echo ""
        echo "❌ $log_name: Arquivo não encontrado"
    fi
}

# Analisar cada tipo de log
analyze_log "$LOG_DIR/app_requests.log" "REQUISIÇÕES DO APP"
analyze_log "$LOG_DIR/notifications.log" "NOTIFICAÇÕES"
analyze_log "$LOG_DIR/withdraw_flow.log" "FLUXO DE SAQUES"
analyze_log "$LOG_DIR/laravel.log" "LOG PRINCIPAL"

echo ""
echo "🔍 RESUMO DE PROBLEMAS POTENCIAIS:"
echo "=================================="

# Verificar problemas comuns
echo ""
echo "📱 Problemas de autenticação:"
grep -i "não autenticado\|unauthorized\|token.*inválido" "$LOG_DIR"/*.log 2>/dev/null | wc -l | xargs echo "   Total:"

echo ""
echo "💰 Problemas de saque:"
grep -i "erro.*saque\|withdraw.*error\|saldo.*insuficiente" "$LOG_DIR"/*.log 2>/dev/null | wc -l | xargs echo "   Total:"

echo ""
echo "📲 Problemas de notificação:"
grep -i "erro.*notificação\|notification.*error\|push.*failed" "$LOG_DIR"/*.log 2>/dev/null | wc -l | xargs echo "   Total:"

echo ""
echo "🌐 Problemas de API:"
grep -i "api.*error\|request.*failed\|timeout" "$LOG_DIR"/*.log 2>/dev/null | wc -l | xargs echo "   Total:"

echo ""
echo "📊 ESTATÍSTICAS GERAIS:"
echo "======================="

# Contar tipos de requisições
echo ""
echo "📱 Tipos de requisições mais comuns:"
grep -o "API REQUEST.*endpoint.*:" "$LOG_DIR/app_requests.log" 2>/dev/null | \
    sed 's/.*endpoint.*: //' | sort | uniq -c | sort -nr | head -10

echo ""
echo "💰 Status de saques:"
grep -o "status.*:" "$LOG_DIR/withdraw_flow.log" 2>/dev/null | \
    sed 's/.*status.*: //' | sort | uniq -c | sort -nr

echo ""
echo "📲 Status de notificações:"
grep -o "success.*:" "$LOG_DIR/notifications.log" 2>/dev/null | \
    sed 's/.*success.*: //' | sort | uniq -c | sort -nr

echo ""
echo "✅ Análise concluída!"
echo "📋 Para monitoramento em tempo real: ./monitor_app_logs.sh"
echo "🧪 Para testar fluxo: ./test_withdraw_flow.sh"
