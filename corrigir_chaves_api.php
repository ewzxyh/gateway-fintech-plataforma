<?php

/**
 * Script para corrigir usuários sem chaves de API
 * 
 * Este script identifica e corrige usuários que não possuem chaves de API configuradas
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\UsersKey;
use Illuminate\Support\Str;

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correção de Chaves de API - Gateway</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
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

        .user-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .user-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .user-card h4 {
            margin: 0 0 10px 0;
            color: #495057;
        }

        .user-card p {
            margin: 5px 0;
            font-size: 14px;
        }

        .fixed {
            background: #d4edda;
            border-color: #c3e6cb;
        }

        .missing {
            background: #f8d7da;
            border-color: #f5c6cb;
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
        <h1>🔧 Correção de Chaves de API - Gateway</h1>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_users'])) {
            echo '<div class="test-section">';
            echo '<h2>🔧 Executando Correção de Chaves de API</h2>';

            try {
                // Buscar usuários sem chaves de API
                $usersWithoutKeys = User::whereDoesntHave('chaves')
                    ->whereNotNull('username')
                    ->get();

                echo '<p><span class="info">ℹ️ Usuários encontrados sem chaves:</span> ' . $usersWithoutKeys->count() . '</p>';

                $fixedCount = 0;
                $errorCount = 0;

                foreach ($usersWithoutKeys as $user) {
                    try {
                        // Gerar chaves de API
                        $token = Str::uuid()->toString();
                        $secret = Str::uuid()->toString();

                        // Criar registro na tabela users_key
                        UsersKey::create([
                            'user_id' => $user->username,
                            'token' => $token,
                            'secret' => $secret,
                            'status' => 1
                        ]);

                        // Atualizar cliente_id na tabela users
                        $user->update(['cliente_id' => $token]);

                        echo '<p class="success">✅ ' . htmlspecialchars($user->username) . ' - Chaves criadas</p>';
                        $fixedCount++;
                    } catch (Exception $e) {
                        echo '<p class="error">❌ ' . htmlspecialchars($user->username) . ' - Erro: ' . $e->getMessage() . '</p>';
                        $errorCount++;
                    }
                }

                echo '<div class="log-entry">';
                echo 'Correção concluída!<br>';
                echo 'Usuários corrigidos: ' . $fixedCount . '<br>';
                echo 'Erros: ' . $errorCount . '<br>';
                echo 'Total processados: ' . $usersWithoutKeys->count();
                echo '</div>';
            } catch (Exception $e) {
                echo '<p class="error">❌ Erro geral: ' . $e->getMessage() . '</p>';
            }

            echo '</div>';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_users'])) {
            echo '<div class="test-section">';
            echo '<h2>🔍 Verificação de Usuários</h2>';

            try {
                // Buscar todos os usuários
                $allUsers = User::whereNotNull('username')->get();
                $usersWithKeys = User::whereHas('chaves')->get();
                $usersWithoutKeys = User::whereDoesntHave('chaves')->whereNotNull('username')->get();

                echo '<p><span class="info">ℹ️ Total de usuários:</span> ' . $allUsers->count() . '</p>';
                echo '<p><span class="success">✅ Usuários com chaves:</span> ' . $usersWithKeys->count() . '</p>';
                echo '<p><span class="error">❌ Usuários sem chaves:</span> ' . $usersWithoutKeys->count() . '</p>';

                if ($usersWithoutKeys->count() > 0) {
                    echo '<h3>👥 Usuários sem chaves de API:</h3>';
                    echo '<div class="user-list">';

                    foreach ($usersWithoutKeys as $user) {
                        echo '<div class="user-card missing">';
                        echo '<h4>' . htmlspecialchars($user->name ?? $user->username) . '</h4>';
                        echo '<p><strong>Username:</strong> ' . htmlspecialchars($user->username) . '</p>';
                        echo '<p><strong>Email:</strong> ' . htmlspecialchars($user->email ?? 'N/A') . '</p>';
                        echo '<p><strong>Status:</strong> ' . ($user->status ? 'Ativo' : 'Inativo') . '</p>';
                        echo '<p><strong>Cliente ID:</strong> ' . htmlspecialchars($user->cliente_id ?? 'N/A') . '</p>';
                        echo '</div>';
                    }

                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<p class="error">❌ Erro: ' . $e->getMessage() . '</p>';
            }

            echo '</div>';
        }
        ?>

        <div class="test-section">
            <h2>🔍 Verificar Usuários</h2>
            <p>Clique no botão abaixo para verificar quais usuários não possuem chaves de API configuradas.</p>
            <form method="POST">
                <button type="submit" name="check_users">🔍 Verificar Usuários</button>
            </form>
        </div>

        <div class="test-section">
            <h2>🔧 Corrigir Usuários</h2>
            <p><strong>⚠️ ATENÇÃO:</strong> Este botão irá criar chaves de API para todos os usuários que não possuem.</p>
            <form method="POST" onsubmit="return confirm('Tem certeza que deseja criar chaves de API para todos os usuários sem chaves?')">
                <button type="submit" name="fix_users" class="danger">🔧 Corrigir Todos os Usuários</button>
            </form>
        </div>

        <div class="test-section">
            <h2>📋 O que este script faz:</h2>
            <ol>
                <li><strong>Identifica usuários</strong> sem chaves de API na tabela <code>users_key</code></li>
                <li><strong>Gera automaticamente</strong> token e secret únicos para cada usuário</li>
                <li><strong>Atualiza</strong> o campo <code>cliente_id</code> na tabela <code>users</code></li>
                <li><strong>Registra logs</strong> de todas as operações realizadas</li>
            </ol>
        </div>

        <div class="test-section">
            <h2>🔧 Como funciona a correção:</h2>
            <ol>
                <li><strong>Busca usuários</strong> que não possuem registro na tabela <code>users_key</code></li>
                <li><strong>Gera UUID</strong> para token e secret</li>
                <li><strong>Cria registro</strong> na tabela <code>users_key</code></li>
                <li><strong>Atualiza</strong> <code>cliente_id</code> na tabela <code>users</code></li>
                <li><strong>Registra</strong> operação nos logs</li>
            </ol>
        </div>

        <div class="test-section">
            <h2>📱 Teste no App</h2>
            <p>Após executar a correção:</p>
            <ol>
                <li>Abra o GatewayApp</li>
                <li>Faça login com seu username</li>
                <li>O erro "Usuário sem chaves de API configuradas" não deve mais aparecer</li>
                <li>O login deve funcionar normalmente</li>
            </ol>
        </div>

        <div class="test-section">
            <h2>🔍 Logs</h2>
            <p>Para monitorar as operações:</p>
            <pre style="background: #f8f9fa; padding: 10px; border-radius: 5px;">
tail -f storage/logs/laravel.log | grep -E '\[API\]|\[CHAVES\]'
            </pre>
        </div>
    </div>
</body>

</html>