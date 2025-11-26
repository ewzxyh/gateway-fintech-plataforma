<?php

/**
 * Teste do Callback Unificado PrimePay7
 * 
 * Este arquivo demonstra como o sistema detecta automaticamente
 * se uma transação é um depósito ou saque baseado no conteúdo.
 */

// Simular diferentes tipos de callbacks da PrimePay7
$testCallbacks = [
    // Depósito detectado por transaction_type
    'deposit_by_type' => [
        'transaction_type' => 'deposit',
        'status' => 'PAID',
        'transaction_id' => 'dep_123456',
        'amount' => 100.00,
        'description' => 'Depósito via PIX'
    ],

    // Saque detectado por event
    'withdraw_by_event' => [
        'event' => 'withdraw.completed',
        'status' => 'COMPLETED',
        'transaction_id' => 'wd_789012',
        'amount' => 50.00,
        'description' => 'Saque via PIX'
    ],

    // Depósito detectado por direction
    'deposit_by_direction' => [
        'direction' => 'in',
        'status' => 'APPROVED',
        'transaction_id' => 'dep_345678',
        'amount' => 200.00,
        'description' => 'Transferência recebida'
    ],

    // Saque detectado por amount negativo
    'withdraw_by_amount' => [
        'status' => 'PROCESSING',
        'transaction_id' => 'wd_901234',
        'amount' => -75.00,
        'description' => 'Transferência enviada'
    ],

    // Depósito detectado por description
    'deposit_by_description' => [
        'status' => 'PAID',
        'transaction_id' => 'dep_567890',
        'amount' => 150.00,
        'description' => 'Pagamento recebido via PIX'
    ],

    // Saque detectado por category
    'withdraw_by_category' => [
        'status' => 'COMPLETED',
        'transaction_id' => 'wd_123456',
        'amount' => 80.00,
        'category' => 'withdrawal',
        'description' => 'Saque solicitado'
    ]
];

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Callback Unificado PrimePay7</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }

        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .test-case {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #e2e8f0;
        }

        .test-title {
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .test-payload {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            overflow-x: auto;
        }

        .detection-result {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .detection-deposit {
            background: #e6fffa;
            border: 1px solid #81e6d9;
            color: #234e52;
        }

        .detection-withdraw {
            background: #fef5e7;
            border: 1px solid #f6ad55;
            color: #744210;
        }

        .endpoint-info {
            background: #e6fffa;
            border: 1px solid #81e6d9;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .method {
            background: #38a169;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }

        .method.post {
            background: #3182ce;
        }

        h1 {
            color: #2d3748;
        }

        h2 {
            color: #4a5568;
        }

        h3 {
            color: #718096;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Teste do Callback Unificado PrimePay7</h1>
            <p>Demonstração da detecção automática de tipo de transação</p>
        </div>

        <div class="endpoint-info">
            <h3>🎯 Endpoint Unificado</h3>
            <p><span class="method post">POST</span> <code>https://app.gateway.shop/api/primepay7/callback</code></p>
            <p>Este endpoint detecta automaticamente se é um depósito ou saque baseado no conteúdo da requisição.</p>
        </div>

        <?php foreach ($testCallbacks as $testName => $payload): ?>
            <div class="test-section">
                <div class="test-case">
                    <div class="test-title">Teste: <?= ucfirst(str_replace('_', ' ', $testName)) ?></div>

                    <div class="test-payload">
                        <?= json_encode($payload, JSON_PRETTY_PRINT) ?>
                    </div>

                    <?php
                    // Simular a lógica de detecção
                    $detectedType = detectTransactionType($payload);
                    $resultClass = $detectedType === 'deposit' ? 'detection-deposit' : 'detection-withdraw';
                    $resultText = $detectedType === 'deposit' ? '✅ DEPÓSITO DETECTADO' : '💸 SAQUE DETECTADO';
                    ?>

                    <div class="detection-result <?= $resultClass ?>">
                        <?= $resultText ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="test-section">
            <h2>🔍 Como Funciona a Detecção</h2>
            <p>O sistema analisa os seguintes campos na seguinte ordem de prioridade:</p>
            <ol>
                <li><strong>transaction_type</strong> - Campo específico do tipo</li>
                <li><strong>type</strong> - Tipo da transação</li>
                <li><strong>event</strong> - Tipo do evento</li>
                <li><strong>status</strong> - Status da transação</li>
                <li><strong>direction/flow</strong> - Direção da transação</li>
                <li><strong>amount</strong> - Valor (positivo = depósito, negativo = saque)</li>
                <li><strong>description</strong> - Palavras-chave na descrição</li>
                <li><strong>category</strong> - Categoria da transação</li>
                <li><strong>operation</strong> - Tipo de operação</li>
                <li><strong>transaction_id</strong> - Consulta no banco de dados</li>
            </ol>
        </div>

        <div class="test-section">
            <h2>📋 Configuração no Painel PrimePay7</h2>
            <p>Configure apenas este URL no painel da PrimePay7:</p>
            <div class="test-payload">
                https://app.gateway.shop/api/primepay7/callback
            </div>
            <p>O sistema automaticamente:</p>
            <ul>
                <li>Detecta se é depósito ou saque</li>
                <li>Processa a transação corretamente</li>
                <li>Atualiza o status no banco de dados</li>
                <li>Credita/debita o saldo do usuário</li>
                <li>Envia notificações se configurado</li>
            </ul>
        </div>
    </div>
</body>

</html>

<?php
/**
 * Função que simula a lógica de detecção do controller
 */
function detectTransactionType($data)
{
    // Verificar campos específicos primeiro
    if (isset($data['transaction_type'])) {
        return strtolower($data['transaction_type']) === 'deposit' ? 'deposit' : 'withdraw';
    }

    if (isset($data['type'])) {
        return strtolower($data['type']) === 'deposit' ? 'deposit' : 'withdraw';
    }

    if (isset($data['event'])) {
        $event = strtolower($data['event']);
        if (strpos($event, 'deposit') !== false || strpos($event, 'received') !== false) {
            return 'deposit';
        }
        if (strpos($event, 'withdraw') !== false || strpos($event, 'sent') !== false) {
            return 'withdraw';
        }
    }

    // Verificar por campos de direção
    if (isset($data['direction'])) {
        return strtolower($data['direction']) === 'in' ? 'deposit' : 'withdraw';
    }

    if (isset($data['flow'])) {
        return strtolower($data['flow']) === 'in' ? 'deposit' : 'withdraw';
    }

    // Verificar por campos de valor
    if (isset($data['amount'])) {
        $amount = (float) $data['amount'];
        if ($amount > 0) {
            return 'deposit';
        } elseif ($amount < 0) {
            return 'withdraw';
        }
    }

    // Verificar por campos de descrição
    if (isset($data['description'])) {
        $description = strtolower($data['description']);
        $depositKeywords = ['deposit', 'received', 'incoming', 'credit', 'payment_received'];
        $withdrawKeywords = ['withdraw', 'sent', 'outgoing', 'debit', 'payment_sent'];

        foreach ($depositKeywords as $keyword) {
            if (strpos($description, $keyword) !== false) {
                return 'deposit';
            }
        }
        foreach ($withdrawKeywords as $keyword) {
            if (strpos($description, $keyword) !== false) {
                return 'withdraw';
            }
        }
    }

    // Verificar por campos de categoria
    if (isset($data['category'])) {
        $category = strtolower($data['category']);
        if (strpos($category, 'deposit') !== false || strpos($category, 'credit') !== false) {
            return 'deposit';
        }
        if (strpos($category, 'withdraw') !== false || strpos($category, 'debit') !== false) {
            return 'withdraw';
        }
    }

    // Se não conseguir detectar, retornar null
    return null;
}
?>