<?php

namespace App\Http\Controllers\Admin\Ajustes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Mail\TestEmail;

class SmtpController extends Controller
{
    public function index()
    {
        // Ler configurações atuais do .env
        $envPath = base_path('.env');
        $envContent = File::exists($envPath) ? File::get($envPath) : '';

        $config = [
            'MAIL_MAILER' => $this->getEnvValue($envContent, 'MAIL_MAILER', 'log'),
            'MAIL_HOST' => $this->getEnvValue($envContent, 'MAIL_HOST', '127.0.0.1'),
            'MAIL_PORT' => $this->getEnvValue($envContent, 'MAIL_PORT', '2525'),
            'MAIL_USERNAME' => $this->getEnvValue($envContent, 'MAIL_USERNAME', ''),
            'MAIL_PASSWORD' => $this->getEnvValue($envContent, 'MAIL_PASSWORD', ''),
            'MAIL_FROM_ADDRESS' => $this->getEnvValue($envContent, 'MAIL_FROM_ADDRESS', 'redacted@example.invalid'),
            'MAIL_FROM_NAME' => $this->getEnvValue($envContent, 'MAIL_FROM_NAME', 'Gateway'),
            'MAIL_ENCRYPTION' => $this->getEnvValue($envContent, 'MAIL_ENCRYPTION', 'tls'),
        ];

        return view("admin.ajustes.smtp", compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'MAIL_MAILER' => 'required|in:smtp,sendmail,mailgun,ses,postmark,resend,log,array',
            'MAIL_HOST' => 'required_if:MAIL_MAILER,smtp|string',
            'MAIL_PORT' => 'required_if:MAIL_MAILER,smtp|integer|min:1|max:65535',
            'MAIL_USERNAME' => 'nullable|string',
            'MAIL_PASSWORD' => 'nullable|string',
            'MAIL_FROM_ADDRESS' => 'required|email',
            'MAIL_FROM_NAME' => 'required|string|max:255',
            'MAIL_ENCRYPTION' => 'nullable|in:tls,ssl,null',
        ]);

        try {
            $envPath = base_path('.env');

            // Fazer backup do .env atual
            $backupPath = base_path('.env.backup.' . date('Y-m-d-H-i-s'));
            if (File::exists($envPath)) {
                File::copy($envPath, $backupPath);
            }

            $envContent = File::exists($envPath) ? File::get($envPath) : '';

            // Atualizar configurações de forma segura
            $envContent = $this->updateEnvValue($envContent, 'MAIL_MAILER', $request->MAIL_MAILER);
            $envContent = $this->updateEnvValue($envContent, 'MAIL_HOST', $request->MAIL_HOST);
            $envContent = $this->updateEnvValue($envContent, 'MAIL_PORT', $request->MAIL_PORT);

            // Garantir que o username seja sempre o email completo
            $username = $request->MAIL_USERNAME;
            if (!str_contains($username, '@')) {
                $username = $request->MAIL_FROM_ADDRESS;
            }
            $envContent = $this->updateEnvValue($envContent, 'MAIL_USERNAME', $username);

            $envContent = $this->updateEnvValue($envContent, 'MAIL_PASSWORD', $request->MAIL_PASSWORD);
            $envContent = $this->updateEnvValue($envContent, 'MAIL_FROM_ADDRESS', $request->MAIL_FROM_ADDRESS);
            $envContent = $this->updateEnvValue($envContent, 'MAIL_FROM_NAME', $request->MAIL_FROM_NAME);
            $envContent = $this->updateEnvValue($envContent, 'MAIL_ENCRYPTION', $request->MAIL_ENCRYPTION);

            // Salvar arquivo .env
            File::put($envPath, $envContent);

            // Limpar cache de configuração de forma segura
            \Artisan::call('config:clear');

            Log::info('Configurações SMTP atualizadas com sucesso');

            return redirect()->back()->with('success', 'Configurações SMTP salvas com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar configurações SMTP: ' . $e->getMessage());

            // Tentar restaurar backup se existir
            if (isset($backupPath) && File::exists($backupPath)) {
                File::copy($backupPath, $envPath);
            }

            return redirect()->back()->with('error', 'Erro ao salvar configurações: ' . $e->getMessage());
        }
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // Configurar temporariamente as configurações de email
            Config::set('mail.mailers.smtp.host', $request->MAIL_HOST ?? config('mail.mailers.smtp.host'));
            Config::set('mail.mailers.smtp.port', $request->MAIL_PORT ?? config('mail.mailers.smtp.port'));
            Config::set('mail.mailers.smtp.username', $request->MAIL_USERNAME ?? config('mail.mailers.smtp.username'));
            Config::set('mail.mailers.smtp.password', $request->MAIL_PASSWORD ?? config('mail.mailers.smtp.password'));
            Config::set('mail.mailers.smtp.encryption', $request->MAIL_ENCRYPTION ?? config('mail.mailers.smtp.encryption'));
            Config::set('mail.from.address', $request->MAIL_FROM_ADDRESS ?? config('mail.from.address'));
            Config::set('mail.from.name', $request->MAIL_FROM_NAME ?? config('mail.from.name'));

            // Enviar email de teste
            Mail::to($request->test_email)->send(new TestEmail());

            Log::info('Email de teste enviado com sucesso para: ' . $request->test_email);

            return redirect()->back()->with('success', 'Email de teste enviado com sucesso para ' . $request->test_email . '!');
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de teste: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao enviar email de teste: ' . $e->getMessage());
        }
    }

    private function getEnvValue($envContent, $key, $default = null)
    {
        if (preg_match("/^{$key}=(.*)$/m", $envContent, $matches)) {
            $value = trim($matches[1]);
            // Remove aspas se existirem
            if (preg_match('/^"(.*)"$/', $value, $quotedMatches)) {
                return $quotedMatches[1];
            }
            return $value;
        }
        return $default;
    }

    private function updateEnvValue($envContent, $key, $value)
    {
        // Escapar aspas duplas no valor
        $escapedValue = str_replace('"', '\"', $value);

        if (preg_match("/^{$key}=.*$/m", $envContent)) {
            return preg_replace("/^{$key}=.*$/m", "{$key}=\"{$escapedValue}\"", $envContent);
        } else {
            return $envContent . "\n{$key}=\"{$escapedValue}\"";
        }
    }
}
