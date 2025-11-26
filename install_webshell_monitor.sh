#!/bin/bash

# Instalação do Monitor de Arquivos
# EXEMPLO Gateway

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

PROJECT_DIR="/home/gateway-demo/htdocs/demo.gateway.shop"
SERVICE_NAME="gateway-webshell-monitor"
SERVICE_FILE="/etc/systemd/system/${SERVICE_NAME}.service"

echo -e "${BLUE}🛡️  EXEMPLO Gateway - Instalação do Sistema de Monitoramento de Webshells${NC}"
echo ""

# Função para verificar se é root
check_root() {
    if [ "$EUID" -ne 0 ]; then
        echo -e "${RED}❌ Este script deve ser executado como root${NC}"
        echo "Use: sudo $0"
        exit 1
    fi
}

# Função para criar diretórios necessários
create_directories() {
    echo -e "${BLUE}📁 Criando diretórios necessários...${NC}"
    
    mkdir -p "$PROJECT_DIR/storage/logs/malicious_files_realtime"
    mkdir -p "$PROJECT_DIR/storage/logs/malicious_uploads"
    mkdir -p "$PROJECT_DIR/storage/logs/malicious_files"
    
    chmod 755 "$PROJECT_DIR/storage/logs"
    chmod 755 "$PROJECT_DIR/storage/logs/malicious_files_realtime"
    chmod 755 "$PROJECT_DIR/storage/logs/malicious_uploads"
    chmod 755 "$PROJECT_DIR/storage/logs/malicious_files"
    
    echo -e "${GREEN}✅ Diretórios criados${NC}"
}

# Função para configurar permissões
setup_permissions() {
    echo -e "${BLUE}🔐 Configurando permissões...${NC}"
    
    # Tornar script executável
    chmod +x "$PROJECT_DIR/webshell_monitor.sh"
    
    # Configurar permissões dos diretórios de upload
    chmod 755 "$PROJECT_DIR/storage/app/public/uploads" 2>/dev/null || true
    chmod 755 "$PROJECT_DIR/public/uploads" 2>/dev/null || true
    chmod 755 "$PROJECT_DIR/storage/app/public/documents" 2>/dev/null || true
    chmod 755 "$PROJECT_DIR/storage/app/public/avatars" 2>/dev/null || true
    
    echo -e "${GREEN}✅ Permissões configuradas${NC}"
}

# Função para criar serviço systemd
create_systemd_service() {
    echo -e "${BLUE}⚙️  Criando serviço systemd...${NC}"
    
    cat > "$SERVICE_FILE" << EOF
[Unit]
Description=EXEMPLO Gateway Webshell Monitor
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=$PROJECT_DIR
ExecStart=$PROJECT_DIR/webshell_monitor.sh start
ExecStop=$PROJECT_DIR/webshell_monitor.sh stop
Restart=always
RestartSec=10
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
EOF

    # Recarregar systemd
    systemctl daemon-reload
    
    # Habilitar serviço
    systemctl enable "$SERVICE_NAME"
    
    echo -e "${GREEN}✅ Serviço systemd criado e habilitado${NC}"
}

# Função para criar cron job
create_cron_job() {
    echo -e "${BLUE}⏰ Configurando cron job...${NC}"
    
    # Criar script de limpeza
    cat > "$PROJECT_DIR/cleanup_logs.sh" << 'EOF'
#!/bin/bash
# Script de limpeza automática de logs antigos

PROJECT_DIR="/home/gateway-demo/htdocs/demo.gateway.shop"
LOG_DIR="$PROJECT_DIR/storage/logs"

# Limpar logs de webshells mais antigos que 30 dias
find "$LOG_DIR/malicious_files_realtime" -type f -mtime +30 -delete 2>/dev/null || true
find "$LOG_DIR/malicious_uploads" -type f -mtime +30 -delete 2>/dev/null || true
find "$LOG_DIR/malicious_files" -type f -mtime +30 -delete 2>/dev/null || true

# Limpar logs do Laravel mais antigos que 7 dias
find "$LOG_DIR" -name "*.log" -mtime +7 -delete 2>/dev/null || true

# Log da limpeza
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Limpeza automática de logs executada" >> "$LOG_DIR/cleanup.log"
EOF

    chmod +x "$PROJECT_DIR/cleanup_logs.sh"
    
    # Adicionar cron job (executa diariamente às 2:00)
    (crontab -l 2>/dev/null; echo "0 2 * * * $PROJECT_DIR/cleanup_logs.sh") | crontab -
    
    echo -e "${GREEN}✅ Cron job configurado${NC}"
}

# Função para criar .htaccess de segurança
create_htaccess_security() {
    echo -e "${BLUE}🔒 Configurando .htaccess de segurança...${NC}"
    
    # .htaccess para pasta uploads
    cat > "$PROJECT_DIR/storage/app/public/uploads/.htaccess" << 'EOF'
# Arquivo de segurança para pasta uploads
# Bloquear execução de PHP em uploads

<Files "*.php">
    Order Deny,Allow
    Deny from all
</Files>

<FilesMatch "\.(php|php3|php4|php5|phtml|pl|py|jsp|asp|sh|cgi)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# Permitir apenas tipos de arquivo seguros
<FilesMatch "\.(jpg|jpeg|png|gif|webp|svg|pdf|doc|docx|xls|xlsx|txt|zip|rar)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Bloquear acesso direto a arquivos PHP
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} \.php$
RewriteRule ^(.*)$ - [F,L]

# Headers de segurança
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "no-referrer-when-downgrade"
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self'; media-src 'self';"
</IfModule>
EOF

    # .htaccess para pasta public/uploads
    if [ -d "$PROJECT_DIR/public/uploads" ]; then
        cp "$PROJECT_DIR/storage/app/public/uploads/.htaccess" "$PROJECT_DIR/public/uploads/.htaccess"
    fi
    
    echo -e "${GREEN}✅ .htaccess de segurança configurado${NC}"
}

# Função para testar o sistema
test_system() {
    echo -e "${BLUE}🧪 Testando sistema...${NC}"
    
    # Testar comando de monitoramento
    if [ -x "$PROJECT_DIR/webshell_monitor.sh" ]; then
        echo -e "${GREEN}✅ Script de monitoramento executável${NC}"
    else
        echo -e "${RED}❌ Script de monitoramento não é executável${NC}"
    fi
    
    # Testar serviço systemd
    if systemctl is-enabled "$SERVICE_NAME" >/dev/null 2>&1; then
        echo -e "${GREEN}✅ Serviço systemd habilitado${NC}"
    else
        echo -e "${RED}❌ Serviço systemd não está habilitado${NC}"
    fi
    
    # Verificar diretórios
    if [ -d "$PROJECT_DIR/storage/logs/malicious_files_realtime" ]; then
        echo -e "${GREEN}✅ Diretórios de backup criados${NC}"
    else
        echo -e "${RED}❌ Diretórios de backup não foram criados${NC}"
    fi
}

# Função para iniciar o serviço
start_service() {
    echo -e "${BLUE}🚀 Iniciando serviço de monitoramento...${NC}"
    
    systemctl start "$SERVICE_NAME"
    
    sleep 2
    
    if systemctl is-active "$SERVICE_NAME" >/dev/null 2>&1; then
        echo -e "${GREEN}✅ Serviço iniciado com sucesso${NC}"
        echo -e "${BLUE}📊 Status: $(systemctl is-active $SERVICE_NAME)${NC}"
    else
        echo -e "${RED}❌ Erro ao iniciar serviço${NC}"
        echo -e "${YELLOW}Verifique os logs: journalctl -u $SERVICE_NAME${NC}"
    fi
}

# Função para mostrar informações
show_info() {
    echo ""
    echo -e "${BLUE}📋 INFORMAÇÕES DO SISTEMA${NC}"
    echo "=================================="
    echo -e "📁 Diretório do projeto: ${GREEN}$PROJECT_DIR${NC}"
    echo -e "🔧 Serviço systemd: ${GREEN}$SERVICE_NAME${NC}"
    echo -e "📊 Logs: ${GREEN}$PROJECT_DIR/storage/logs/${NC}"
    echo ""
    echo -e "${BLUE}📋 COMANDOS ÚTEIS${NC}"
    echo "=================================="
    echo -e "Iniciar monitor: ${GREEN}systemctl start $SERVICE_NAME${NC}"
    echo -e "Parar monitor: ${GREEN}systemctl stop $SERVICE_NAME${NC}"
    echo -e "Status: ${GREEN}systemctl status $SERVICE_NAME${NC}"
    echo -e "Logs: ${GREEN}journalctl -u $SERVICE_NAME -f${NC}"
    echo -e "Script manual: ${GREEN}$PROJECT_DIR/webshell_monitor.sh status${NC}"
    echo ""
    echo -e "${BLUE}📋 DIRETÓRIOS MONITORADOS${NC}"
    echo "=================================="
    echo -e "• ${GREEN}$PROJECT_DIR/storage/app/public/uploads${NC}"
    echo -e "• ${GREEN}$PROJECT_DIR/public/uploads${NC}"
    echo -e "• ${GREEN}$PROJECT_DIR/storage/app/public/documents${NC}"
    echo -e "• ${GREEN}$PROJECT_DIR/storage/app/public/avatars${NC}"
    echo -e "• ${GREEN}$PROJECT_DIR/storage/app/public/temp${NC}"
}

# Função principal
main() {
    check_root
    
    echo -e "${YELLOW}⚠️  Instalando sistema de monitoramento de webshells...${NC}"
    echo ""
    
    create_directories
    setup_permissions
    create_systemd_service
    create_cron_job
    create_htaccess_security
    test_system
    start_service
    show_info
    
    echo ""
    echo -e "${GREEN}🎉 INSTALAÇÃO CONCLUÍDA COM SUCESSO!${NC}"
    echo ""
    echo -e "${YELLOW}⚠️  IMPORTANTE:${NC}"
    echo -e "• O sistema está monitorando em tempo real"
    echo -e "• Webshells serão detectados e removidos automaticamente"
    echo -e "• Logs são salvos em: $PROJECT_DIR/storage/logs/"
    echo -e "• Use 'systemctl status $SERVICE_NAME' para verificar status"
    echo ""
}

# Executar instalação
main
