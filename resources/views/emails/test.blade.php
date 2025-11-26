<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teste de Configuração SMTP</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
      color: #333;
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
      background-color: #f4f4f4;
    }

    .container {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .header {
      text-align: center;
      border-bottom: 2px solid #007bff;
      padding-bottom: 20px;
      margin-bottom: 30px;
    }

    .logo {
      font-size: 24px;
      font-weight: bold;
      color: #007bff;
      margin-bottom: 10px;
    }

    .success-icon {
      font-size: 48px;
      color: #28a745;
      margin-bottom: 20px;
    }

    .content {
      text-align: center;
    }

    .title {
      font-size: 20px;
      color: #28a745;
      margin-bottom: 15px;
    }

    .message {
      font-size: 16px;
      margin-bottom: 20px;
      color: #666;
    }

    .info-box {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 5px;
      padding: 15px;
      margin: 20px 0;
    }

    .info-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
    }

    .info-label {
      font-weight: bold;
      color: #495057;
    }

    .info-value {
      color: #6c757d;
    }

    .footer {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #dee2e6;
      color: #6c757d;
      font-size: 14px;
    }

    .button {
      display: inline-block;
      background-color: #007bff;
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 5px;
      margin: 20px 0;
      font-weight: bold;
    }

    .button:hover {
      background-color: #0056b3;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <div class="logo">Gateway</div>
      <div class="success-icon">✅</div>
    </div>

    <div class="content">
      <div class="title">Configuração SMTP Funcionando!</div>

      <div class="message">
        Parabéns! Sua configuração de SMTP está funcionando perfeitamente.
        Este é um email de teste para confirmar que o sistema de envio de emails
        está configurado corretamente.
      </div>

      <div class="info-box">
        <div class="info-item">
          <span class="info-label">Data/Hora:</span>
          <span class="info-value">{{ $timestamp }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Servidor:</span>
          <span class="info-value">{{ $server }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Status:</span>
          <span class="info-value" style="color: #28a745; font-weight: bold;">✅ Sucesso</span>
        </div>
      </div>

      <div class="message">
        Agora você pode usar o sistema de recuperação de senha e outros recursos
        que dependem do envio de emails.
      </div>

      <a href="{{ config('app.url') }}" class="button">
        Acessar Gateway
      </a>
    </div>

    <div class="footer">
      <p>Este é um email automático do sistema Gateway.</p>
      <p>Se você não solicitou este teste, pode ignorar este email.</p>
    </div>
  </div>
</body>

</html>