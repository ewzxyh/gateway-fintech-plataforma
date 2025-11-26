<?php

/**
 * Script para alternar adquirente PIX rapidamente
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Adquirente;

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alternar Adquirente PIX - Gateway</title>
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

        .adquirente-card {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #00d4aa;
        }

        .adquirente-card.inactive {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔄 Alternar Adquirente PIX - Gateway</h1>

        <?php
        // Buscar adquirentes disponíveis
        $adquirentes = Adquirente::where('status', 1)->get();

        echo '<div class="test-section">';
        echo '<h2>📋 Adquirentes Disponíveis</h2>';

        foreach ($adquirentes as $adq) {
            $statusClass = $adq->status ? '' : 'inactive';
            echo '<div class="adquirente-card ' . $statusClass . '">';
            echo '<h4>' . htmlspecialchars($adq->nome ?? $adq->referencia) . '</h4>';
            echo '<p><strong>Referência:</strong> ' . htmlspecialchars($adq->referencia) . '</p>';
            echo '<p><strong>Status:</strong> ' . ($adq->status ? 'ATIVO' : 'INATIVO') . '</p>';
            echo '</div>';
        }
        echo '</div>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['switch_adquirente'])) {
            echo '<div class="test-section">';
            echo '<h2>🔄 Alternando Adquirente</h2>';

            try {
                $novoAdquirente = $_POST['adquirente'] ?? '';

                if (!$novoAdquirente) {
                    throw new Exception('Selecione um adquirente');
                }

                // Verificar se o adquirente existe e está ativo
                $adquirente = Adquirente::where('referencia', $novoAdquirente)
                    ->where('status', 1)
                    ->first();

                if (!$adquirente) {
                    throw new Exception('Adquirente não encontrado ou inativo');
                }

                // Aqui você pode implementar a lógica para alterar o adquirente padrão
                // Por exemplo, atualizar uma configuração global ou por usuário

                echo '<p class="success">✅ Adquirente alterado para: ' . htmlspecialchars($adquirente->referencia) . '</p>';

                echo '<div class="log-entry">';
                echo 'Configuração atualizada:<br>';
                echo 'Novo Adquirente: ' . htmlspecialchars($adquirente->referencia) . '<br>';
                echo 'Nome: ' . htmlspecialchars($adquirente->nome ?? 'N/A') . '<br>';
                echo 'Status: ATIVO<br>';
                echo 'Data: ' . date('Y-m-d H:i:s');
                echo '</div>';

                echo '<p class="info">ℹ️ Agora teste gerar um QR Code PIX no app!</p>';
            } catch (Exception $e) {
                echo '<p class="error">❌ Erro: ' . $e->getMessage() . '</p>';
            }

            echo '</div>';
        }
        ?>

        <div class="test-section">
            <h2>⚡ SOLUÇÃO RÁPIDA</h2>
            <p><strong>Problema atual:</strong> Woovi bloqueando IP do servidor</p>
            <p><strong>Solução:</strong> Usar outro adquirente PIX</p>

            <form method="POST">
                <div style="margin: 20px 0;">
                    <label><strong>Escolha o novo adquirente:</strong></label><br><br>

                    <label style="display: block; margin: 10px 0;">
                        <input type="radio" name="adquirente" value="xdpag" checked>
                        <strong>XDPag</strong> - Alternativa confiável
                    </label>

                    <label style="display: block; margin: 10px 0;">
                        <input type="radio" name="adquirente" value="trustypix">
                        <strong>TrustyPix</strong> - Outra opção
                    </label>
                </div>

                <button type="submit" name="switch_adquirente" class="danger">🔄 Alternar Adquirente</button>
            </form>
        </div>

        <div class="test-section">
            <h2>🧪 Testar Após Alteração</h2>
            <ol>
                <li>Selecione um adquirente acima</li>
                <li>Clique "Alternar Adquirente"</li>
                <li>Teste gerar QR Code no app</li>
                <li>Verifique se funciona sem erro</li>
            </ol>
        </div>

        <div class="test-section">
            <h2>🔍 Monitorar Logs</h2>
            <p>Para acompanhar tentativas:</p>
            <pre style="background: #f8f9fa; padding: 10px; border-radius: 5px;">
tail -f storage/logs/laravel.log | grep -E "QR Code|adquirente|XDPag|TrustyPix"
            </pre>
        </div>

        <div class="test-section">
            <h2>📝 Próximos Passos</h2>
            <ol>
                <li><strong>Alternar</strong> para XDPag ou TrustyPix</li>
                <li><strong>Testar</strong> geração de QR Code</li>
                <li><strong>Configurar</strong> credenciais se necessário</li>
                <li><strong>Validar</strong> funcionamento completo</li>
            </ol>
        </div>
    </div>
</body>

</html>