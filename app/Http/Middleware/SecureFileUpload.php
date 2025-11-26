<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecureFileUpload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se há uploads de arquivo
        if ($request->hasFile('*')) {
            foreach ($request->allFiles() as $fieldName => $files) {
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $this->validateFile($file, $request, $fieldName);
                    }
                } else {
                    $this->validateFile($files, $request, $fieldName);
                }
            }
        }

        return $next($request);
    }

    private function validateFile($file, Request $request, string $fieldName): void
    {
        // Verificar extensões perigosas
        $dangerousExtensions = [
            'php', 'php3', 'php4', 'php5', 'phtml', 'pl', 'py', 'jsp', 'asp', 'sh', 'cgi',
            'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'war'
        ];
        
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (in_array($extension, $dangerousExtensions)) {
            Log::critical('Tentativa de upload de arquivo perigoso bloqueada', [
                'extension' => $extension,
                'field_name' => $fieldName,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'file_size' => $file->getSize()
            ]);
            
            abort(403, 'Tipo de arquivo não permitido: ' . $extension);
        }

        // Verificar MIME types perigosos
        $dangerousMimes = [
            'application/x-php',
            'text/x-php',
            'application/x-executable',
            'application/x-msdownload',
            'application/x-msdos-program',
            'application/x-winexe',
            'application/x-javascript',
            'text/javascript'
        ];
        
        $mimeType = $file->getMimeType();
        
        if (in_array($mimeType, $dangerousMimes)) {
            Log::critical('Tentativa de upload com MIME type perigoso bloqueada', [
                'mime_type' => $mimeType,
                'extension' => $extension,
                'field_name' => $fieldName,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id()
            ]);
            
            abort(403, 'Tipo MIME não permitido: ' . $mimeType);
        }

        // Verificar tamanho máximo (10MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            Log::warning('Tentativa de upload de arquivo muito grande', [
                'file_size' => $file->getSize(),
                'field_name' => $fieldName,
                'ip' => $request->ip(),
                'user_id' => auth()->id()
            ]);
            
            abort(413, 'Arquivo muito grande. Máximo permitido: 10MB');
        }

        // Verificar conteúdo do arquivo
        $fileContent = file_get_contents($file->getRealPath());
        
        // Procurar por código malicioso
        $maliciousPatterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/eval\(/i',
            '/base64_decode\(/i',
            '/system\(/i',
            '/exec\(/i',
            '/shell_exec\(/i'
        ];
        
        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $fileContent)) {
                Log::critical('Conteúdo malicioso detectado no upload', [
                    'pattern' => $pattern,
                    'extension' => $extension,
                    'mime_type' => $mimeType,
                    'field_name' => $fieldName,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'user_id' => auth()->id(),
                    'file_size' => $file->getSize()
                ]);
                
                abort(403, 'Conteúdo malicioso detectado no arquivo');
            }
        }
    }
}


