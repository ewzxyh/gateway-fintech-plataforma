<?php

/**
 * Script para configurar Woovi
 * 
 * Este script ajuda a configurar o Woovi com uma API key válida
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Woovi;

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração Woovi - Gateway</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #00d4aa;
            border-bottom: 3px solid #00d4aa;
            padding-bottom: 10px;
        }

        .test-section {
            margin: 20px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #00d4aa;
            border-radius: 5px;
        }

        .success {
            color: #28a745;
            font-weight: bold;
        }

        .error {
            color: #dc3545;
            font-weight: bold;
        }

        .info {
            color: #007bff;
            font-weight: bold;
        }

        .warning {
            color: #ffc107;
            font-weight: bold;
        }

        .log-entry {
            background: #1a1a1a;
            color: #00d4aa;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-x: auto;
        }

        .form-group {
            margin: 15px 0;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            background: #00d4aa;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin: 10px 5px;
        }

        button:hover {
            background: #00b894;
        }

        .config-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔧 Configuração Woovi - Gateway</h1>

        <?php
        // Verificar configuração atual
        $woovi = Woovi::first();

        if ($woovi) {
            echo '<div class="test-section">';
            echo '<h2>📋 Configuração Atual do Woovi</h2>';
            echo '<div class="config-info">';
            echo '<p><strong>API Key:</strong> ' . ($woovi->api_key ? 'CONFIGURADA' : 'NÃO CONFIGURADA') . '</p>';
            echo '<p><strong>Status:</strong> ' . ($woovi->status ? 'ATIVO' : 'INATIVO') . '</p>';
            echo '<p><strong>Sandbox:</strong> ' . ($woovi->sandbox ? 'SIM' : 'NÃO') . '</p>';
            echo '<p><strong>URL:</strong> ' . $woovi->getApiUrl() . '</p>';
            echo '<p><strong>Webhook Secret:</strong> ' . ($woovi->webhook_secret ? 'CONFIGURADO' : 'NÃO CONFIGURADO') . '</p>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="test-section">';
            echo '<h2>⚠️ Woovi Não Configurado</h2>';
            echo '<p class="error">Nenhuma configuração do Woovi encontrada no banco de dados.</p>';
            echo '</div>';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_config'])) {
            echo '<div class="test-section">';
            echo '<h2>🔧 Atualizando Configuração</h2>';

            try {
                $apiKey = $_POST['api_key'] ?? '';
                $webhookSecret = $_POST['webhook_secret'] ?? '';
                $sandbox = isset($_POST['sandbox']) ? 1 : 0;
                $status = isset($_POST['status']) ? 1 : 0;

                if (!$apiKey) {
                    throw new Exception('API Key é obrigatória');
                }

                if (!$woovi) {
                    // Criar nova configuração
                    $woovi = Woovi::create([
                        'api_key' => $apiKey,
                        'webhook_secret' => $webhookSecret,
                        'sandbox' => $sandbox,
                        'status' => $status,
                        'taxa_adquirente_entradas' => 0,
                        'taxa_adquirente_saidas' => 0
                    ]);
                    echo '<p class="success">✅ Configuração do Woovi criada com sucesso!</p>';
                } else {
                    // Atualizar configuração existente
                    $woovi->update([
                        'api_key' => $apiKey,
                        'webhook_secret' => $webhookSecret,
                        'sandbox' => $sandbox,
                        'status' => $status
                    ]);
                    echo '<p class="success">✅ Configuração do Woovi atualizada com sucesso!</p>';
                }

                echo '<div class="log-entry">';
                echo 'Configuração salva:<br>';
                echo 'API Key: ' . ($apiKey ? 'CONFIGURADA' : 'NÃO CONFIGURADA') . '<br>';
                echo 'Webhook Secret: ' . ($webhookSecret ? 'CONFIGURADO' : 'NÃO CONFIGURADO') . '<br>';
                echo 'Sandbox: ' . ($sandbox ? 'SIM' : 'NÃO') . '<br>';
                echo 'Status: ' . ($status ? 'ATIVO' : 'INATIVO') . '<br>';
                echo 'URL: ' . $woovi->getApiUrl();
                echo '</div>';
            } catch (Exception $e) {
                echo '<p class="error">❌ Erro: ' . $e->getMessage() . '</p>';
            }

            echo '</div>';
        }
        ?>

        <div class="test-section">
            <h2>🔧 Configurar Woovi</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="api_key">API Key:</label>
                    <input type="text" id="api_key" name="api_key" required placeholder="Digite sua API Key do Woovi">
                </div>

                <div class="form-group">
                    <label for="webhook_secret">Webhook Secret (opcional):</label>
                    <input type="text" id="webhook_secret" name="webhook_secret" placeholder="Digite o Webhook Secret">
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="sandbox" <?php echo ($woovi && $woovi->sandbox) ? 'checked' : ''; ?>>
                        Usar Sandbox (ambiente de teste)
                    </label>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="status" <?php echo ($woovi && $woovi->status) ? 'checked' : ''; ?>>
                        Ativar Woovi
                    </label>
                </div>

                <button type="submit" name="update_config">💾 Salvar Configuração</button>
            </form>
        </div>

        <div class="test-section">
            <h2>📝 Como Obter API Key do Woovi</h2>
            <ol>
                <li><strong>Acesse:</strong> <a href="https://woovi.com" target="_blank">https://woovi.com</a></li>
                <li><strong>Faça login</strong> na sua conta</li>
                <li><strong>Vá para:</strong> Configurações > API</li>
                <li><strong>Copie a API Key</strong> gerada</li>
                <li><strong>Cole no campo acima</strong> e salve</li>
            </ol>
        </div>

        <div class="test-section">
            <h2>🧪 Testar Configuração</h2>
            <p>Após configurar a API Key:</p>
            <ol>
                <li>Salve a configuração</li>
                <li>Teste gerar um QR Code PIX no app</li>
                <li>Verifique os logs para confirmar funcionamento</li>
            </ol>
        </div>

        <div class="test-section">
            <h2>🔍 Logs de Erro</h2>
            <p>Para monitorar erros do Woovi:</p>
            <pre style="background: #f8f9fa; padding: 10px; border-radius: 5px;">
tail -f storage/logs/laravel.log | grep -E "\[WOOVI\]|WooviService"
            </pre>
        </div>

        <div class="test-section">
            <h2>⚠️ Problema Atual</h2>
            <p><strong>Erro identificado:</strong> "appID inválido"</p>
            <p><strong>Causa:</strong> API Key do Woovi não configurada ou inválida</p>
            <p><strong>Solução:</strong> Configure uma API Key válida usando o formulário acima</p>
        </div>
    </div>
</body>

</html>