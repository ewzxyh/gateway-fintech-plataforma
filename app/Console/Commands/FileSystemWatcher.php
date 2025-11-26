<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FileSystemWatcher extends Command
{
    protected $signature = 'security:watch-files {--daemon : Executar como daemon}';
    protected $description = 'Monitor de sistema de arquivos';

    private $watchedDirectories = [
        'storage/app/public/uploads',
        'public/uploads',
        'storage/app/public/documents',
        'storage/app/public/avatars',
        'storage/app/public/temp'
    ];

    private $isRunning = true;

    public function handle()
    {
        $this->info('👁️ Iniciando Monitor de Sistema de Arquivos...');
        
        if (!extension_loaded('inotify')) {
            $this->error('❌ Extensão inotify não está disponível. Instalando fallback...');
            $this->runFallbackMonitoring();
            return;
        }

        if ($this->option('daemon')) {
            $this->runAsDaemon();
        } else {
            $this->runContinuous();
        }
    }

    private function runAsDaemon()
    {
        $this->info('🔄 Executando como daemon com inotify...');
        
        $inotify = inotify_init();
        
        // Configurar watch para cada diretório
        $watchDescriptors = [];
        foreach ($this->watchedDirectories as $dir) {
            $fullPath = base_path($dir);
            if (File::exists($fullPath)) {
                $wd = inotify_add_watch($inotify, $fullPath, IN_CREATE | IN_MOVED_TO | IN_MODIFY);
                $watchDescriptors[$wd] = $fullPath;
                $this->info("👀 Monitorando: {$fullPath}");
            }
        }

        // Configurar stream para não bloquear
        stream_set_blocking($inotify, 0);

        while ($this->isRunning) {
            $events = inotify_read($inotify);
            
            if ($events) {
                foreach ($events as $event) {
                    $this->handleFileEvent($event, $watchDescriptors);
                }
            }
            
            usleep(100000); // 100ms
        }

        inotify_rm_watch($inotify, $wd);
        fclose($inotify);
    }

    private function runContinuous()
    {
        $this->info('🔍 Executando monitoramento contínuo (fallback)...');
        $this->runFallbackMonitoring();
    }

    private function runFallbackMonitoring()
    {
        $this->info('Pressione Ctrl+C para parar');
        
        $lastCheck = [];
        
        while ($this->isRunning) {
            foreach ($this->watchedDirectories as $dir) {
                $fullPath = base_path($dir);
                
                if (!File::exists($fullPath)) {
                    continue;
                }

                $currentFiles = $this->getDirectoryFiles($fullPath);
                $lastFiles = $lastCheck[$fullPath] ?? [];
                
                // Verificar novos arquivos
                $newFiles = array_diff($currentFiles, $lastFiles);
                
                foreach ($newFiles as $file) {
                    $this->analyzeNewFile($file);
                }
                
                $lastCheck[$fullPath] = $currentFiles;
            }
            
            sleep(2); // Verifica a cada 2 segundos
        }
    }

    private function handleFileEvent($event, $watchDescriptors)
    {
        $mask = $event['mask'];
        $name = $event['name'];
        $wd = $event['wd'];
        
        $directory = $watchDescriptors[$wd] ?? null;
        if (!$directory) {
            return;
        }
        
        $fullPath = $directory . '/' . $name;
        
        // Verificar se é criação ou movimento de arquivo
        if ($mask & IN_CREATE || $mask & IN_MOVED_TO) {
            $this->analyzeNewFile($fullPath);
        }
        
        // Verificar se é modificação de arquivo existente
        if ($mask & IN_MODIFY) {
            $this->analyzeModifiedFile($fullPath);
        }
    }

    private function getDirectoryFiles($directory)
    {
        $files = [];
        
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
            );
            
            foreach ($iterator as $file) {
                $files[] = $file->getPathname();
            }
        } catch (\Exception $e) {
            Log::error('Erro ao listar arquivos do diretório: ' . $e->getMessage(), [
                'directory' => $directory
            ]);
        }
        
        return $files;
    }

    private function analyzeNewFile($filePath)
    {
        if (!File::exists($filePath)) {
            return;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Verificar arquivos PHP imediatamente
        if (in_array($extension, ['php', 'php3', 'php4', 'php5', 'phtml', 'inc', 'pht', 'phtm'])) {
            $this->analyzePhpFile($filePath);
        }
        
        // Verificar arquivos suspeitos (imagens/documentos que podem conter PHP)
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'txt'])) {
            $this->analyzeSuspiciousFile($filePath);
        }
    }

    private function analyzeModifiedFile($filePath)
    {
        // Aguardar um pouco para o arquivo ser completamente escrito
        sleep(1);
        
        $this->analyzeNewFile($filePath);
    }

    private function analyzePhpFile($filePath)
    {
        try {
            $content = File::get($filePath);
            $fileName = basename($filePath);
            
            // Verificar padrões de webshell
            if ($this->isWebshell($content)) {
                $this->handleWebshell($filePath, $content, 'PHP_WEBSHELL');
            } elseif ($this->isSuspiciousPhp($content)) {
                $this->handleWebshell($filePath, $content, 'SUSPICIOUS_PHP');
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao analisar arquivo PHP: ' . $e->getMessage(), [
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
                
                $this->handleWebshell($filePath, $content, 'DISGUISED_PHP');
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao analisar arquivo suspeito: ' . $e->getMessage(), [
                'file' => $filePath
            ]);
        }
    }

    private function isWebshell($content)
    {
        $webshellPatterns = [
            '/eval\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/system\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/shell_exec\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/exec\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/passthru\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/proc_open\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/popen\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/file_get_contents\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/file_put_contents\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/fwrite\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/fopen\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/curl_exec\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/base64_decode\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/assert\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/create_function\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/call_user_func\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/call_user_func_array\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/preg_replace.*\/e.*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/array_map.*assert.*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/array_walk.*assert.*\$_(GET|POST|REQUEST|COOKIE)/i',
            '/c99shell/i',
            '/r57shell/i',
            '/webshell/i',
            '/backdoor/i',
            '/hack/i',
            '/exploit/i'
        ];

        foreach ($webshellPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    private function isSuspiciousPhp($content)
    {
        // Arquivo PHP muito pequeno (suspeito)
        if (strlen($content) < 100 && strpos($content, '<?php') !== false) {
            return true;
        }
        
        // Apenas código PHP sem HTML (suspeito)
        if (preg_match('/^<\?php.*\?>$/s', trim($content))) {
            return true;
        }
        
        // Muitas funções perigosas
        $dangerousFunctions = ['eval', 'system', 'shell_exec', 'exec', 'passthru', 'proc_open', 'popen'];
        $count = 0;
        
        foreach ($dangerousFunctions as $func) {
            $count += substr_count($content, $func);
        }
        
        if ($count > 2) {
            return true;
        }
        
        return false;
    }

    private function handleWebshell($filePath, $content, $threatType)
    {
        $fileName = basename($filePath);
        $fileSize = strlen($content);
        $fileHash = md5($content);
        
        // Log crítico
        Log::critical('🚨 WEBSHELL DETECTADO EM TEMPO REAL', [
            'file' => $filePath,
            'filename' => $fileName,
            'threat_type' => $threatType,
            'file_size' => $fileSize,
            'file_hash' => $fileHash,
            'timestamp' => now(),
            'content_preview' => substr($content, 0, 200) . '...'
        ]);
        
        // Salvar backup para análise
        $this->saveMaliciousFileBackup($filePath, $content, $threatType);
        
        // Remover arquivo imediatamente
        $this->removeMaliciousFile($filePath);
        
        // Notificar
        $this->warn("🚨 WEBSHELL REMOVIDO EM TEMPO REAL: {$fileName} ({$threatType})");
        
        // Enviar alerta
        $this->sendRealTimeAlert($filePath, $threatType, $fileName);
    }

    private function saveMaliciousFileBackup($filePath, $content, $threatType)
    {
        $backupDir = storage_path('logs/malicious_files_realtime');
        
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        
        $timestamp = now()->format('Y-m-d_H-i-s-u');
        $fileName = basename($filePath);
        $backupFile = "{$backupDir}/{$timestamp}_{$threatType}_{$fileName}";
        
        File::put($backupFile, $content);
        
        // Metadados
        $metadata = [
            'original_path' => $filePath,
            'threat_type' => $threatType,
            'detected_at' => now()->toISOString(),
            'file_size' => strlen($content),
            'file_hash' => md5($content),
            'content_preview' => substr($content, 0, 500),
            'detection_method' => 'realtime_filesystem_watcher'
        ];
        
        File::put($backupFile . '.meta.json', json_encode($metadata, JSON_PRETTY_PRINT));
    }

    private function removeMaliciousFile($filePath)
    {
        try {
            if (File::exists($filePath)) {
                File::delete($filePath);
                
                Log::info('Arquivo malicioso removido em tempo real', [
                    'file' => $filePath
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao remover arquivo malicioso: ' . $e->getMessage(), [
                'file' => $filePath
            ]);
        }
    }

    private function sendRealTimeAlert($filePath, $threatType, $fileName)
    {
        try {
            // Log do alerta
            Log::warning('🚨 ALERTA EM TEMPO REAL - Webshell detectado e removido', [
                'file' => $filePath,
                'threat_type' => $threatType,
                'filename' => $fileName,
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao enviar alerta em tempo real: ' . $e->getMessage());
        }
    }
}
