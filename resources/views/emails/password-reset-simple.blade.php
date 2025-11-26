<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperação de Senha - Gateway</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
  <div style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
    <div style="text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 20px; margin-bottom: 30px;">
      <h1 style="color: #007bff; margin: 0;">Gateway</h1>
      <h2 style="color: #333; margin: 10px 0;">Recuperação de Senha</h2>
    </div>

    <div style="text-align: center;">
      <h3>Olá!</h3>

      <p>Você está recebendo este email porque recebemos uma solicitação de redefinição de senha para sua conta.</p>

      <p>Clique no botão abaixo para redefinir sua senha:</p>

      <a href="{{ $actionUrl }}" style="display: inline-block; background-color: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; font-size: 16px;">
        Redefinir Senha
      </a>

      <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <strong>⚠️ Importante:</strong> Este link de redefinição de senha expirará em {{ $count }} minutos.
      </div>

      <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 15px; margin: 20px 0;">
        <strong>Se você não conseguir clicar no botão acima, copie e cole o link abaixo no seu navegador:</strong>
        <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 3px; padding: 10px; font-family: monospace; font-size: 14px; word-break: break-all; margin: 10px 0;">{{ $actionUrl }}</div>
      </div>

      <p>Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária. Sua senha permanecerá inalterada.</p>
    </div>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; color: #6c757d; font-size: 14px;">
      <p>Este é um email automático do sistema Gateway.</p>
      <p>Se você não solicitou esta redefinição, pode ignorar este email.</p>
    </div>
  </div>
</body>

</html>