<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MonitorUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:monitor-uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitora uploads suspeitos e arquivos maliciosos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Iniciando monitoramento de uploads...');
        
        $uploadPaths = [
            public_path('uploads'),
            storage_path('app/public/uploads'),
            storage_path('app/public'),
        ];
        
        $suspiciousFiles = [];
        $maliciousFiles = [];
        
        foreach ($uploadPaths as $path) {
            if (File::exists($path)) {
                $this->scanDirectory($path, $suspiciousFiles, $maliciousFiles);
            }
        }
        
        if (!empty($suspiciousFiles)) {
            $this->warn('⚠️  Arquivos suspeitos encontrados:');
            foreach ($suspiciousFiles as $file) {
                $this->line("   - {$file}");
            }
        }
        
        if (!empty($maliciousFiles)) {
            $this->error('🚨 Arquivos maliciosos encontrados:');
            foreach ($maliciousFiles as $file) {
                $this->line("   - {$file}");
                $this->removeMaliciousFile($file);
            }
        }
        
        if (empty($suspiciousFiles) && empty($maliciousFiles)) {
            $this->info('✅ Nenhum arquivo suspeito encontrado.');
        }
        
        $this->info('✅ Monitoramento concluído.');
    }
    
    private function scanDirectory($path, &$suspiciousFiles, &$maliciousFiles)
    {
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            $filePath = $file->getPathname();
            $extension = strtolower($file->getExtension());
            $filename = $file->getFilename();
            
            // Verificar extensões suspeitas
            $suspiciousExtensions = [
                'php', 'php3', 'php4', 'php5', 'phtml', 'pl', 'py', 'jsp', 'asp', 'sh', 'cgi',
                'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'war'
            ];
            
            if (in_array($extension, $suspiciousExtensions)) {
                $suspiciousFiles[] = $filePath;
                
                // Verificar conteúdo para determinar se é malicioso
                if ($this->isMaliciousFile($filePath)) {
                    $maliciousFiles[] = $filePath;
                }
            }
            
            // Verificar nomes suspeitos
            $suspiciousNames = [
                'shell', 'backdoor', 'webshell', 'uploader', 'admin', 'config',
                'wp-config', 'settings', 'c99', 'r57', 'b374k'
            ];
            
            foreach ($suspiciousNames as $suspiciousName) {
                if (strpos(strtolower($filename), $suspiciousName) !== false) {
                    $suspiciousFiles[] = $filePath;
                    break;
                }
            }
        }
    }
    
    private function isMaliciousFile($filePath)
    {
        try {
            $content = File::get($filePath);
            
            $maliciousPatterns = [
                '/<\?php/i',
                '/<\?=/i',
                '/<script/i',
                '/javascript:/i',
                '/vbscript:/i',
                '/eval\(/i',
                '/base64_decode\(/i',
                '/system\(/i',
                '/exec\(/i',
                '/shell_exec\(/i',
                '/passthru\(/i',
                '/popen\(/i',
                '/proc_open\(/i',
                '/file_get_contents\(/i',
                '/file_put_contents\(/i',
                '/fopen\(/i',
                '/fwrite\(/i',
                '/curl_exec\(/i',
                '/fsockopen\(/i',
                '/socket_create\(/i',
                '/stream_socket_client\(/i'
            ];
            
            foreach ($maliciousPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Erro ao verificar arquivo malicioso', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    private function removeMaliciousFile($filePath)
    {
        try {
            File::delete($filePath);
            $this->error("   🗑️  Arquivo removido: {$filePath}");
            
            Log::critical('Arquivo malicioso removido automaticamente', [
                'file' => $filePath,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            $this->error("   ❌ Erro ao remover arquivo: {$filePath}");
            Log::error('Erro ao remover arquivo malicioso', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
        }
    }
}


