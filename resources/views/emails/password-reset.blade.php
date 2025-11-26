<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperação de Senha - Gateway</title>
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

    .content {
      text-align: center;
    }

    .title {
      font-size: 20px;
      color: #333;
      margin-bottom: 15px;
    }

    .message {
      font-size: 16px;
      margin-bottom: 20px;
      color: #666;
      text-align: left;
    }

    .button {
      display: inline-block;
      background-color: #007bff;
      color: white;
      padding: 15px 30px;
      text-decoration: none;
      border-radius: 5px;
      margin: 20px 0;
      font-weight: bold;
      font-size: 16px;
    }

    .button:hover {
      background-color: #0056b3;
    }

    .info-box {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 5px;
      padding: 15px;
      margin: 20px 0;
      text-align: left;
    }

    .warning {
      background-color: #fff3cd;
      border: 1px solid #ffeaa7;
      color: #856404;
      padding: 15px;
      border-radius: 5px;
      margin: 20px 0;
    }

    .footer {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #dee2e6;
      color: #6c757d;
      font-size: 14px;
    }

    .code {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 3px;
      padding: 10px;
      font-family: monospace;
      font-size: 14px;
      word-break: break-all;
      margin: 10px 0;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <div class="logo">Gateway</div>
      <h1>Recuperação de Senha</h1>
    </div>

    <div class="content">
      <div class="title">Olá!</div>

      <div class="message">
        Você está recebendo este email porque recebemos uma solicitação de redefinição de senha para sua conta.
      </div>

      <div class="message">
        Clique no botão abaixo para redefinir sua senha:
      </div>

      <a href="{{ $actionUrl }}" class="button">
        Redefinir Senha
      </a>

      <div class="warning">
        <strong>⚠️ Importante:</strong> Este link de redefinição de senha expirará em {{ $count }} minutos.
      </div>

      <div class="info-box">
        <strong>Se você não conseguir clicar no botão acima, copie e cole o link abaixo no seu navegador:</strong>
        <div class="code">{{ $actionUrl }}</div>
      </div>

      <div class="message">
        Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária.
        Sua senha permanecerá inalterada.
      </div>

      <div class="message">
        <strong>Dicas de segurança:</strong>
        <ul style="text-align: left; margin: 10px 0;">
          <li>Use uma senha forte com pelo menos 8 caracteres</li>
          <li>Combine letras maiúsculas, minúsculas, números e símbolos</li>
          <li>Não compartilhe sua senha com ninguém</li>
          <li>Ative a autenticação de dois fatores se disponível</li>
        </ul>
      </div>
    </div>

    <div class="footer">
      <p>Este é um email automático do sistema Gateway.</p>
      <p>Se você não solicitou esta redefinição, pode ignorar este email.</p>
      <p>Para suporte, entre em contato conosco através do painel administrativo.</p>
    </div>
  </div>
</body>

</html>