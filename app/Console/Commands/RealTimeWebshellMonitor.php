<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class RealTimeWebshellMonitor extends Command
{
    protected $signature = 'security:realtime-monitor {--daemon : Executar como daemon}';
    protected $description = 'Monitor de arquivos suspeitos';

    private $monitoredPaths = [
        'storage/app/public/uploads',
        'public/uploads',
        'storage/app/public/documents',
        'storage/app/public/avatars',
        'storage/app/public/temp'
    ];

    private $suspiciousPatterns = [
        // Padrões de webshells comuns
        '/<\?php\s+.*?(eval|system|shell_exec|exec|passthru|proc_open|popen|file_get_contents|file_put_contents|fwrite|fopen|curl_exec|base64_decode).*?\?>/is',
        '/<\?php\s+.*?\$_GET.*?\$_POST.*?\$_REQUEST.*?\?>/is',
        '/<\?php\s+.*?assert\s*\(.*?\?>/is',
        '/<\?php\s+.*?create_function\s*\(.*?\?>/is',
        '/<\?=\s*\$_(GET|POST|REQUEST|COOKIE)\[.*?\]\s*\?>/is',
        '/<\?php\s+.*?call_user_func.*?\?>/is',
        '/<\?php\s+.*?call_user_func_array.*?\?>/is',
        '/<\?php\s+.*?preg_replace.*?\/e.*?\?>/is',
        '/<\?php\s+.*?array_map.*?assert.*?\?>/is',
        '/<\?php\s+.*?array_walk.*?assert.*?\?>/is',
        
        // Padrões específicos de webshells conhecidos
        '/c99shell/i',
        '/r57shell/i',
        '/webshell/i',
        '/shell\.php/i',
        '/backdoor/i',
        '/hack/i',
        '/exploit/i',
        
        // Padrões de obfuscação
        '/base64_decode\s*\(\s*["\'].*?["\']\s*\)/is',
        '/gzinflate\s*\(\s*base64_decode/is',
        '/str_rot13\s*\(\s*base64_decode/is',
        '/eval\s*\(\s*base64_decode/is',
        '/eval\s*\(\s*gzinflate/is',
        
        // Padrões de upload malicioso
        '/move_uploaded_file.*?\.php/i',
        '/copy.*?\.php/i',
        '/rename.*?\.php/i',
        
        // Padrões de acesso a arquivos do sistema
        '/\/etc\/passwd/i',
        '/\/etc\/shadow/i',
        '/\/proc\/version/i',
        '/\/proc\/cpuinfo/i',
        '/\/proc\/meminfo/i',
        '/\/proc\/mounts/i',
        '/\/proc\/self\/status/i',
        
        // Padrões de rede maliciosa
        '/fsockopen\s*\(\s*["\']\d+\.\d+\.\d+\.\d+["\']/i',
        '/socket_create/i',
        '/curl_init/i',
        '/file_get_contents\s*\(\s*["\']http/i',
        
        // Padrões de manipulação de arquivos críticos
        '/chmod\s*\(\s*["\']777["\']/i',
        '/chown/i',
        '/unlink\s*\(\s*["\']\.htaccess["\']/i',
        '/rm\s+-rf/i',
        '/rmdir/i',
        
        // Padrões de bypass de segurança
        '/ini_set\s*\(\s*["\']disable_functions["\']/i',
        '/ini_set\s*\(\s*["\']safe_mode["\']/i',
        '/putenv/i',
        '/dl\s*\(/i',
        
        // Padrões de mineração de criptomoedas
        '/stratum/i',
        '/mining/i',
        '/cryptocurrency/i',
        '/bitcoin/i',
        '/monero/i',
        '/ethereum/i'
    ];

    private $isRunning = true;
    private $lastCheck = [];

    public function handle()
    {
        $this->info('🚨 Iniciando Monitor de Webshells em Tempo Real...');
        
        if ($this->option('daemon')) {
            $this->runAsDaemon();
        } else {
            $this->runContinuous();
        }
    }

    private function runAsDaemon()
    {
        $this->info('🔄 Executando como daemon...');
        
        while ($this->isRunning) {
            $this->scanForWebshells();
            sleep(5); // Verifica a cada 5 segundos
        }
    }

    private function runContinuous()
    {
        $this->info('🔍 Executando monitoramento contínuo...');
        $this->info('Pressione Ctrl+C para parar');
        
        // Configurar handler para parada graciosa
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, [$this, 'stopMonitoring']);
            pcntl_signal(SIGTERM, [$this, 'stopMonitoring']);
        }

        while ($this->isRunning) {
            $this->scanForWebshells();
            
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }
            
            sleep(10); // Verifica a cada 10 segundos
        }
    }

    public function stopMonitoring()
    {
        $this->info('🛑 Parando monitoramento...');
        $this->isRunning = false;
    }

    private function scanForWebshells()
    {
        foreach ($this->monitoredPaths as $path) {
            $fullPath = base_path($path);
            
            if (!File::exists($fullPath)) {
                continue;
            }

            $this->scanDirectory($fullPath);
        }
    }

    private function scanDirectory($directory)
    {
        try {
            $files = File::allFiles($directory);
            
            foreach ($files as $file) {
                $filePath = $file->getPathname();
                $extension = strtolower($file->getExtension());
                
                // Verificar apenas arquivos PHP e suspeitos
                if (in_array($extension, ['php', 'php3', 'php4', 'php5', 'phtml', 'inc', 'pht', 'phtm'])) {
                    $this->analyzeFile($filePath);
                }
                
                // Verificar arquivos com extensões de imagem/documento que podem conter PHP
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'txt'])) {
                    $this->analyzeSuspiciousFile($filePath);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao escanear diretório: ' . $e->getMessage(), [
                'directory' => $directory
            ]);
        }
    }

    private function analyzeFile($filePath)
    {
        try {
            $content = File::get($filePath);
            $fileName = basename($filePath);
            
            // Verificar se é um arquivo suspeito
            if ($this->isSuspiciousContent($content)) {
                $this->handleSuspiciousFile($filePath, $content, 'PHP_SUSPICIOUS');
            }
            
            // Verificar se é um webshell conhecido
            if ($this->isWebshell($content)) {
                $this->handleSuspiciousFile($filePath, $content, 'WEBSHELL_DETECTED');
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao analisar arquivo: ' . $e->getMessage(), [
                'file' => $filePath
            ]);
        }
    }

    private function analyzeSuspiciousFile($filePath)
    {
        try {
            $content = File::get($filePath);
            $fileName = basename($filePath);
            
            // Verificar se arquivo de imagem/documento contém PHP
            if (strpos($content, '<?php') !== false || 
                strpos($content, '<?=') !== false || 
                strpos($content, '<script') !== false) {
                
                $this->handleSuspiciousFile($filePath, $content, 'DISGUISED_PHP');
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao analisar arquivo suspeito: ' . $e->getMessage(), [
                'file' => $filePath
            ]);
        }
    }

    private function isSuspiciousContent($content)
    {
        // Verificar tamanho do arquivo (arquivos PHP muito pequenos são suspeitos)
        if (strlen($content) < 100 && strpos($content, '<?php') !== false) {
            return true;
        }
        
        // Verificar se contém apenas código PHP sem HTML
        if (preg_match('/^<\?php.*\?>$/s', trim($content))) {
            return true;
        }
        
        return false;
    }

    private function isWebshell($content)
    {
        foreach ($this->suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }

    private function handleSuspiciousFile($filePath, $content, $threatType)
    {
        $fileName = basename($filePath);
        $fileSize = filesize($filePath);
        $fileHash = md5($content);
        
        // Log crítico da detecção
        Log::critical('🚨 WEBSHELL DETECTADO E REMOVIDO', [
            'file' => $filePath,
            'filename' => $fileName,
            'threat_type' => $threatType,
            'file_size' => $fileSize,
            'file_hash' => $fileHash,
            'timestamp' => now(),
            'content_preview' => substr($content, 0, 200) . '...'
        ]);
        
        // Salvar backup do arquivo malicioso para análise
        $this->saveMaliciousFileBackup($filePath, $content, $threatType);
        
        // Remover arquivo imediatamente
        $this->removeMaliciousFile($filePath);
        
        // Enviar alerta
        $this->sendSecurityAlert($filePath, $threatType, $fileName);
        
        // Notificar no console
        $this->warn("🚨 WEBSHELL REMOVIDO: {$fileName} ({$threatType})");
    }

    private function saveMaliciousFileBackup($filePath, $content, $threatType)
    {
        $backupDir = storage_path('logs/malicious_files');
        
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        
        $timestamp = now()->format('Y-m-d_H-i-s');
        $fileName = basename($filePath);
        $backupFile = "{$backupDir}/{$timestamp}_{$threatType}_{$fileName}";
        
        File::put($backupFile, $content);
        
        // Criar arquivo de metadados
        $metadata = [
            'original_path' => $filePath,
            'threat_type' => $threatType,
            'detected_at' => now()->toISOString(),
            'file_size' => strlen($content),
            'file_hash' => md5($content),
            'content_preview' => substr($content, 0, 500)
        ];
        
        File::put($backupFile . '.meta.json', json_encode($metadata, JSON_PRETTY_PRINT));
    }

    private function removeMaliciousFile($filePath)
    {
        try {
            if (File::exists($filePath)) {
                File::delete($filePath);
                
                Log::info('Arquivo malicioso removido com sucesso', [
                    'file' => $filePath
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao remover arquivo malicioso: ' . $e->getMessage(), [
                'file' => $filePath
            ]);
        }
    }

    private function sendSecurityAlert($filePath, $threatType, $fileName)
    {
        try {
            // Enviar email de alerta (se configurado)
            if (config('mail.mailers.smtp.host')) {
                Mail::raw("
🚨 ALERTA DE SEGURANÇA - WEBSHELL DETECTADO

Arquivo: {$fileName}
Caminho: {$filePath}
Tipo de Ameaça: {$threatType}
Data/Hora: " . now()->format('d/m/Y H:i:s') . "

O arquivo foi automaticamente removido pelo sistema de monitoramento.

Ação recomendada: Verificar logs de acesso e investigar origem do upload.
                ", function ($message) {
                    $message->to(config('mail.from.address'))
                            ->subject('🚨 ALERTA: Webshell Detectado - ' . config('app.name'));
                });
            }
            
            // Log do alerta
            Log::warning('Alerta de segurança enviado', [
                'file' => $filePath,
                'threat_type' => $threatType
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao enviar alerta de segurança: ' . $e->getMessage());
        }
    }
}
