<?php

/**
 * Script para resolver problema de IP na Woovi
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolver Problema IP Woovi - Gateway</title>
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

        .ip-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
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

        .danger {
            background: #dc3545;
        }

        .danger:hover {
            background: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔧 Resolver Problema IP Woovi - Gateway</h1>

        <div class="test-section">
            <h2>🚨 Problema Identificado</h2>
            <div class="log-entry">
                Erro: "Este 54.233.244.177 não tem permissão para acessar este ambiente"
            </div>
            <p><strong>Causa:</strong> O IP do servidor não está autorizado na conta Woovi</p>
        </div>

        <div class="test-section">
            <h2>📋 Informações do Servidor</h2>
            <div class="ip-info">
                <strong>IP do Servidor:</strong> 54.233.244.177<br>
                <strong>Ambiente:</strong> Sandbox (Teste)<br>
                <strong>API Key:</strong> Configurada (140 caracteres)<br>
                <strong>Status:</strong> Ativo
            </div>
        </div>

        <div class="test-section">
            <h2>🔧 SOLUÇÕES DISPONÍVEIS</h2>

            <h3>1️⃣ Configurar IP na Woovi (Recomendado)</h3>
            <ol>
                <li><strong>Acesse:</strong> <a href="https://woovi.com" target="_blank">https://woovi.com</a></li>
                <li><strong>Faça login</strong> na sua conta</li>
                <li><strong>Vá para:</strong> Configurações > Segurança > IPs Permitidos</li>
                <li><strong>Adicione o IP:</strong> <code>54.233.244.177</code></li>
                <li><strong>Salve</strong> as configurações</li>
            </ol>

            <h3>2️⃣ Usar Ambiente de Produção</h3>
            <ol>
                <li><strong>Configure</strong> para produção na Woovi</li>
                <li><strong>Atualize</strong> a configuração no sistema</li>
                <li><strong>Teste</strong> novamente</li>
            </ol>

            <h3>3️⃣ Configurar Proxy/VPN</h3>
            <ol>
                <li><strong>Configure</strong> um proxy com IP autorizado</li>
                <li><strong>Atualize</strong> as configurações de rede</li>
                <li><strong>Teste</strong> a conexão</li>
            </ol>
        </div>

        <div class="test-section">
            <h2>⚡ SOLUÇÃO RÁPIDA - Alterar para Produção</h2>
            <p>Se você tem uma conta de produção na Woovi:</p>
            <form method="POST">
                <button type="submit" name="switch_to_production" class="danger">🔄 Alterar para Produção</button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['switch_to_production'])) {
            echo '<div class="test-section">';
            echo '<h2>🔄 Alterando para Produção</h2>';

            try {
                $woovi = \App\Models\Woovi::first();
                if ($woovi) {
                    $woovi->update(['sandbox' => 0]);
                    echo '<p class="success">✅ Alterado para ambiente de produção!</p>';
                    echo '<div class="log-entry">';
                    echo 'Configuração atualizada:<br>';
                    echo 'Ambiente: PRODUÇÃO<br>';
                    echo 'URL: https://api.woovi.com<br>';
                    echo 'IP: 54.233.244.177<br>';
                    echo 'Status: Ativo';
                    echo '</div>';
                } else {
                    echo '<p class="error">❌ Configuração Woovi não encontrada</p>';
                }
            } catch (Exception $e) {
                echo '<p class="error">❌ Erro: ' . $e->getMessage() . '</p>';
            }

            echo '</div>';
        }
        ?>

        <div class="test-section">
            <h2>🧪 Testar Após Configuração</h2>
            <p>Após configurar o IP na Woovi:</p>
            <ol>
                <li>Salve as configurações na Woovi</li>
                <li>Teste gerar QR Code no app</li>
                <li>Verifique os logs para confirmar</li>
            </ol>
        </div>

        <div class="test-section">
            <h2>🔍 Monitorar Logs</h2>
            <p>Para acompanhar tentativas:</p>
            <pre style="background: #f8f9fa; padding: 10px; border-radius: 5px;">
tail -f storage/logs/laravel.log | grep -E "WooviService|QR Code|permissão"
            </pre>
        </div>

        <div class="test-section">
            <h2>📞 Suporte Woovi</h2>
            <p>Se precisar de ajuda:</p>
            <ul>
                <li><strong>Email:</strong> redacted@example.invalid</li>
                <li><strong>Documentação:</strong> <a href="https://docs.woovi.com" target="_blank">https://docs.woovi.com</a></li>
                <li><strong>Chat:</strong> Disponível no painel da Woovi</li>
            </ul>
        </div>

        <div class="test-section">
            <h2>⚠️ Alternativas</h2>
            <p>Se não conseguir resolver o problema de IP:</p>
            <ol>
                <li><strong>Use outro adquirente:</strong> XDPag, TrustyPix, etc.</li>
                <li><strong>Configure VPN:</strong> Para mudar o IP do servidor</li>
                <li><strong>Contate suporte:</strong> Para liberação do IP</li>
            </ol>
        </div>
    </div>
</body>

</html>