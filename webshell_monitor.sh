#!/bin/bash

# Monitor de arquivos suspeitos
# EXEMPLO Gateway

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

PROJECT_DIR="/home/gateway-demo/htdocs/demo.gateway.shop"
LOG_FILE="$PROJECT_DIR/storage/logs/webshell_monitor.log"
PID_FILE="$PROJECT_DIR/storage/logs/webshell_monitor.pid"
MONITOR_DIRS=(
    "$PROJECT_DIR/storage/app/public/uploads"
    "$PROJECT_DIR/public/uploads"
    "$PROJECT_DIR/storage/app/public/documents"
    "$PROJECT_DIR/storage/app/public/avatars"
    "$PROJECT_DIR/storage/app/public/temp"
)

# Função para log
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Função para verificar se o processo já está rodando
check_running() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p "$PID" > /dev/null 2>&1; then
            echo -e "${YELLOW}⚠️  Monitor já está rodando (PID: $PID)${NC}"
            return 0
        else
            rm -f "$PID_FILE"
        fi
    fi
    return 1
}

# Função para parar o monitor
stop_monitor() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p "$PID" > /dev/null 2>&1; then
            kill "$PID"
            rm -f "$PID_FILE"
            log_message "🛑 Monitor parado (PID: $PID)"
            echo -e "${GREEN}✅ Monitor parado com sucesso${NC}"
        else
            rm -f "$PID_FILE"
            echo -e "${YELLOW}⚠️  Monitor não estava rodando${NC}"
        fi
    else
        echo -e "${YELLOW}⚠️  Monitor não está rodando${NC}"
    fi
}

# Função para analisar arquivo suspeito
analyze_file() {
    local file="$1"
    local filename=$(basename "$file")
    local extension="${filename##*.}"
    
    # Verificar se é arquivo PHP
    if [[ "$extension" =~ ^(php|php3|php4|php5|phtml|inc|pht|phtm)$ ]]; then
        analyze_php_file "$file"
    # Verificar arquivos suspeitos (imagens/documentos com PHP)
    elif [[ "$extension" =~ ^(jpg|jpeg|png|gif|webp|pdf|doc|docx|txt)$ ]]; then
        analyze_suspicious_file "$file"
    fi
}

# Função para analisar arquivo PHP
analyze_php_file() {
    local file="$1"
    local filename=$(basename "$file")
    
    # Padrões de webshell
    local webshell_patterns=(
        "eval.*\$_GET"
        "eval.*\$_POST"
        "eval.*\$_REQUEST"
        "system.*\$_GET"
        "system.*\$_POST"
        "shell_exec.*\$_GET"
        "exec.*\$_POST"
        "passthru.*\$_REQUEST"
        "proc_open.*\$_GET"
        "popen.*\$_POST"
        "file_get_contents.*\$_GET"
        "file_put_contents.*\$_POST"
        "fwrite.*\$_REQUEST"
        "fopen.*\$_GET"
        "curl_exec.*\$_POST"
        "base64_decode.*\$_GET"
        "assert.*\$_POST"
        "create_function.*\$_GET"
        "call_user_func.*\$_POST"
        "c99shell"
        "r57shell"
        "webshell"
        "backdoor"
        "hack"
        "exploit"
    )
    
    for pattern in "${webshell_patterns[@]}"; do
        if grep -qi "$pattern" "$file" 2>/dev/null; then
            handle_webshell "$file" "PHP_WEBSHELL" "$pattern"
            return
        fi
    done
    
    # Verificar arquivo PHP suspeito (muito pequeno)
    local file_size=$(stat -c%s "$file" 2>/dev/null || echo "0")
    if [ "$file_size" -lt 100 ] && grep -q "<?php" "$file" 2>/dev/null; then
        handle_webshell "$file" "SUSPICIOUS_PHP" "small_php_file"
    fi
}

# Função para analisar arquivo suspeito
analyze_suspicious_file() {
    local file="$1"
    local filename=$(basename "$file")
    
    # Verificar se contém PHP disfarçado
    if grep -q "<?php\|<?=\|<script" "$file" 2>/dev/null; then
        handle_webshell "$file" "DISGUISED_PHP" "php_in_non_php_file"
    fi
}

# Função para lidar com webshell detectado
handle_webshell() {
    local file="$1"
    local threat_type="$2"
    local pattern="$3"
    local filename=$(basename "$file")
    local file_size=$(stat -c%s "$file" 2>/dev/null || echo "0")
    local file_hash=$(md5sum "$file" 2>/dev/null | cut -d' ' -f1)
    
    # Log crítico
    log_message "🚨 WEBSHELL DETECTADO: $filename ($threat_type) - Padrão: $pattern"
    log_message "📁 Arquivo: $file"
    log_message "📊 Tamanho: $file_size bytes"
    log_message "🔐 Hash: $file_hash"
    
    # Salvar backup
    save_backup "$file" "$threat_type"
    
    # Remover arquivo
    remove_file "$file"
    
    # Enviar alerta
    send_alert "$file" "$threat_type" "$filename"
}

# Função para salvar backup
save_backup() {
    local file="$1"
    local threat_type="$2"
    local backup_dir="$PROJECT_DIR/storage/logs/malicious_files_realtime"
    local timestamp=$(date '+%Y-%m-%d_%H-%M-%S')
    local filename=$(basename "$file")
    
    mkdir -p "$backup_dir"
    
    # Copiar arquivo
    cp "$file" "$backup_dir/${timestamp}_${threat_type}_${filename}"
    
    # Criar metadados
    cat > "$backup_dir/${timestamp}_${threat_type}_${filename}.meta.json" << EOF
{
    "original_path": "$file",
    "threat_type": "$threat_type",
    "detected_at": "$(date -Iseconds)",
    "file_size": $(stat -c%s "$file" 2>/dev/null || echo "0"),
    "file_hash": "$(md5sum "$file" 2>/dev/null | cut -d' ' -f1)",
    "detection_method": "bash_realtime_monitor"
}
EOF
    
    log_message "💾 Backup salvo: $backup_dir/${timestamp}_${threat_type}_${filename}"
}

# Função para remover arquivo
remove_file() {
    local file="$1"
    
    if [ -f "$file" ]; then
        rm -f "$file"
        log_message "🗑️  Arquivo removido: $file"
    fi
}

# Função para enviar alerta
send_alert() {
    local file="$1"
    local threat_type="$2"
    local filename="$3"
    
    log_message "📢 ALERTA ENVIADO: Webshell $filename ($threat_type) detectado e removido"
    
    # Aqui você pode adicionar notificações por email, Slack, etc.
    # Exemplo: curl -X POST "https://hooks.slack.com/..." -d "{\"text\":\"🚨 Webshell detectado: $filename\"}"
}

# Função principal de monitoramento
start_monitor() {
    log_message "🚀 Iniciando monitor de webshells em tempo real..."
    
    # Verificar se já está rodando
    if check_running; then
        exit 1
    fi
    
    # Criar diretórios de monitoramento se não existirem
    for dir in "${MONITOR_DIRS[@]}"; do
        if [ ! -d "$dir" ]; then
            mkdir -p "$dir"
            log_message "📁 Diretório criado: $dir"
        fi
    done
    
    # Salvar PID
    echo $$ > "$PID_FILE"
    
    log_message "✅ Monitor iniciado (PID: $$)"
    echo -e "${GREEN}✅ Monitor de webshells iniciado (PID: $$)${NC}"
    
    # Loop principal de monitoramento
    while true; do
        for dir in "${MONITOR_DIRS[@]}"; do
            if [ -d "$dir" ]; then
                # Encontrar arquivos novos ou modificados nos últimos 5 segundos
                find "$dir" -type f -newermt "5 seconds ago" 2>/dev/null | while read -r file; do
                    analyze_file "$file"
                done
            fi
        done
        
        sleep 2
    done
}

# Função para mostrar status
show_status() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p "$PID" > /dev/null 2>&1; then
            echo -e "${GREEN}✅ Monitor está rodando (PID: $PID)${NC}"
            echo -e "${BLUE}📁 Diretórios monitorados:${NC}"
            for dir in "${MONITOR_DIRS[@]}"; do
                if [ -d "$dir" ]; then
                    echo -e "   - $dir"
                fi
            done
            echo -e "${BLUE}📊 Log: $LOG_FILE${NC}"
        else
            echo -e "${RED}❌ Monitor não está rodando${NC}"
            rm -f "$PID_FILE"
        fi
    else
        echo -e "${RED}❌ Monitor não está rodando${NC}"
    fi
}

# Função para mostrar logs
show_logs() {
    if [ -f "$LOG_FILE" ]; then
        echo -e "${BLUE}📊 Últimas 50 linhas do log:${NC}"
        tail -50 "$LOG_FILE"
    else
        echo -e "${YELLOW}⚠️  Arquivo de log não encontrado${NC}"
    fi
}

# Função para limpar logs antigos
cleanup_logs() {
    local backup_dir="$PROJECT_DIR/storage/logs/malicious_files_realtime"
    
    if [ -d "$backup_dir" ]; then
        # Remover arquivos mais antigos que 30 dias
        find "$backup_dir" -type f -mtime +30 -delete
        log_message "🧹 Logs antigos limpos (arquivos > 30 dias)"
        echo -e "${GREEN}✅ Logs antigos limpos${NC}"
    else
        echo -e "${YELLOW}⚠️  Diretório de backup não encontrado${NC}"
    fi
}

# Menu principal
case "$1" in
    start)
        start_monitor
        ;;
    stop)
        stop_monitor
        ;;
    restart)
        stop_monitor
        sleep 2
        start_monitor
        ;;
    status)
        show_status
        ;;
    logs)
        show_logs
        ;;
    cleanup)
        cleanup_logs
        ;;
    *)
        echo -e "${BLUE}🛡️  EXEMPLO Gateway - Monitor de Webshells${NC}"
        echo ""
        echo "Uso: $0 {start|stop|restart|status|logs|cleanup}"
        echo ""
        echo "Comandos:"
        echo "  start    - Iniciar monitor"
        echo "  stop     - Parar monitor"
        echo "  restart  - Reiniciar monitor"
        echo "  status   - Mostrar status"
        echo "  logs     - Mostrar logs"
        echo "  cleanup  - Limpar logs antigos"
        echo ""
        echo "Exemplos:"
        echo "  $0 start    # Iniciar monitor"
        echo "  $0 status   # Verificar status"
        echo "  $0 logs     # Ver logs"
        ;;
esac
