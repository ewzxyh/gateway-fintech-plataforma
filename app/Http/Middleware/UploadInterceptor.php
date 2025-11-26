<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class UploadInterceptor
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasFile('file') || $request->hasFile('image') || $request->hasFile('avatar') || $request->hasFile('document')) {
            $files = $request->allFiles();

            foreach ($files as $inputName => $file) {
                if (is_array($file)) {
                    foreach ($file as $f) {
                        $this->interceptFile($f, $request);
                    }
                } else {
                    $this->interceptFile($file, $request);
                }
            }
        }

        return $next($request);
    }

    protected function interceptFile($file, Request $request)
    {
        try {
            $filename = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();
            
            // Verificar se é arquivo PHP
            if (in_array($extension, ['php', 'php3', 'php4', 'php5', 'phtml', 'inc', 'pht', 'phtm'])) {
                $this->handleSuspiciousUpload($file, $request, 'PHP_FILE_UPLOAD', $filename);
                abort(403, 'Tipo de arquivo não permitido: ' . $extension);
            }
            
            // Verificar conteúdo do arquivo
            $fileContent = file_get_contents($file->getRealPath());
            
            // Verificar padrões de webshell
            if ($this->isWebshell($fileContent)) {
                $this->handleSuspiciousUpload($file, $request, 'WEBSHELL_DETECTED', $filename);
                abort(403, 'Arquivo malicioso detectado e bloqueado!');
            }
            
            // Verificar PHP disfarçado em arquivos não-PHP
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'txt'])) {
                if (strpos($fileContent, '<?php') !== false || 
                    strpos($fileContent, '<?=') !== false || 
                    strpos($fileContent, '<script') !== false) {
                    
                    $this->handleSuspiciousUpload($file, $request, 'DISGUISED_PHP', $filename);
                    abort(403, 'Arquivo malicioso detectado e bloqueado!');
                }
            }
            
            // Verificar arquivo muito pequeno (suspeito)
            if ($fileSize < 100 && strpos($fileContent, '<?php') !== false) {
                $this->handleSuspiciousUpload($file, $request, 'SUSPICIOUS_SMALL_FILE', $filename);
                abort(403, 'Arquivo suspeito detectado!');
            }
            
        } catch (\Exception $e) {
            Log::error('Erro no interceptor de upload: ' . $e->getMessage(), [
                'file' => $filename ?? 'unknown',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }
    }

    protected function isWebshell($content)
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
            '/exploit/i',
            '/stratum/i',
            '/mining/i',
            '/cryptocurrency/i',
            '/bitcoin/i',
            '/monero/i',
            '/ethereum/i'
        ];

        foreach ($webshellPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    protected function handleSuspiciousUpload($file, Request $request, $threatType, $filename)
    {
        $fileContent = file_get_contents($file->getRealPath());
        $fileSize = $file->getSize();
        $fileHash = md5($fileContent);
        
        // Log crítico
        Log::critical('🚨 UPLOAD MALICIOSO INTERCEPTADO', [
            'filename' => $filename,
            'threat_type' => $threatType,
            'file_size' => $fileSize,
            'file_hash' => $fileHash,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'timestamp' => now(),
            'content_preview' => substr($fileContent, 0, 200) . '...'
        ]);
        
        // Salvar backup do arquivo malicioso
        $this->saveMaliciousFileBackup($file, $fileContent, $threatType, $filename);
        
        // Enviar alerta
        $this->sendUploadAlert($filename, $threatType, $request);
    }

    protected function saveMaliciousFileBackup($file, $content, $threatType, $filename)
    {
        $backupDir = storage_path('logs/malicious_uploads');
        
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        
        $timestamp = now()->format('Y-m-d_H-i-s-u');
        $backupFile = "{$backupDir}/{$timestamp}_{$threatType}_{$filename}";
        
        File::put($backupFile, $content);
        
        // Metadados
        $metadata = [
            'original_filename' => $filename,
            'threat_type' => $threatType,
            'intercepted_at' => now()->toISOString(),
            'file_size' => strlen($content),
            'file_hash' => md5($content),
            'content_preview' => substr($content, 0, 500),
            'detection_method' => 'upload_interceptor'
        ];
        
        File::put($backupFile . '.meta.json', json_encode($metadata, JSON_PRETTY_PRINT));
    }

    protected function sendUploadAlert($filename, $threatType, Request $request)
    {
        try {
            Log::warning('🚨 ALERTA DE UPLOAD MALICIOSO', [
                'filename' => $filename,
                'threat_type' => $threatType,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao enviar alerta de upload: ' . $e->getMessage());
        }
    }
}
