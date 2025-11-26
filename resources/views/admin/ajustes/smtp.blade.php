<x-app-layout :route="'[ADMIN] Configurações SMTP'">
  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-primary">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-primary text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-envelope fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Configurações SMTP</h1>
                  <p class="text-muted mb-0">Configure o sistema de envio de emails para recuperação de senha e notificações</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Alertas -->
      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      <!-- Configurações SMTP -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-cog me-2"></i>Configurações de Email
              </h5>
            </div>
            <div class="card-body p-4">
              <form method="POST" action="{{ route('admin.smtp.update') }}">
                @csrf

                <div class="row g-3">
                  <!-- Mailer -->
                  <div class="col-md-6">
                    <label for="MAIL_MAILER" class="form-label">
                      <i class="fa-solid fa-server me-1"></i>Driver de Email
                    </label>
                    <select class="form-select" id="MAIL_MAILER" name="MAIL_MAILER" required>
                      <option value="log" {{ $config['MAIL_MAILER'] == 'log' ? 'selected' : '' }}>Log (Desenvolvimento)</option>
                      <option value="smtp" {{ $config['MAIL_MAILER'] == 'smtp' ? 'selected' : '' }} selected>SMTP</option>
                      <option value="sendmail" {{ $config['MAIL_MAILER'] == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                      <option value="mailgun" {{ $config['MAIL_MAILER'] == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                      <option value="ses" {{ $config['MAIL_MAILER'] == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                      <option value="postmark" {{ $config['MAIL_MAILER'] == 'postmark' ? 'selected' : '' }}>Postmark</option>
                      <option value="resend" {{ $config['MAIL_MAILER'] == 'resend' ? 'selected' : '' }}>Resend</option>
                    </select>
                    <div class="form-text">Escolha o driver de email que deseja usar</div>
                  </div>

                  <!-- Host SMTP -->
                  <div class="col-md-6">
                    <label for="MAIL_HOST" class="form-label">
                      <i class="fa-solid fa-globe me-1"></i>Host SMTP
                    </label>
                    <input type="text" class="form-control" id="MAIL_HOST" name="MAIL_HOST"
                      value="{{ $config['MAIL_HOST'] ?? 'smtp.hostinger.com' }}" placeholder="smtp.hostinger.com">
                    <div class="form-text">Servidor SMTP da Hostinger: smtp.hostinger.com</div>
                  </div>

                  <!-- Porta -->
                  <div class="col-md-6">
                    <label for="MAIL_PORT" class="form-label">
                      <i class="fa-solid fa-plug me-1"></i>Porta
                    </label>
                    <input type="number" class="form-control" id="MAIL_PORT" name="MAIL_PORT"
                      value="{{ $config['MAIL_PORT'] ?? '587' }}" placeholder="587">
                    <div class="form-text">587 (TLS/STARTTLS) ou 465 (SSL) - Hostinger</div>
                  </div>

                  <!-- Username -->
                  <div class="col-md-6">
                    <label for="MAIL_USERNAME" class="form-label">
                      <i class="fa-solid fa-user me-1"></i>Usuário
                    </label>
                    <input type="text" class="form-control" id="MAIL_USERNAME" name="MAIL_USERNAME"
                      value="{{ $config['MAIL_USERNAME'] ?? 'redacted@example.invalid' }}" placeholder="redacted@example.invalid">
                    <div class="form-text">Email completo (ex: redacted@example.invalid)</div>
                  </div>

                  <!-- Password -->
                  <div class="col-md-6">
                    <label for="MAIL_PASSWORD" class="form-label">
                      <i class="fa-solid fa-lock me-1"></i>Senha
                    </label>
                    <div class="input-group">
                      <input type="password" class="form-control" id="MAIL_PASSWORD" name="MAIL_PASSWORD"
                        value="{{ $config['MAIL_PASSWORD'] }}" placeholder="Sua senha">
                      <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                        <i class="fa-solid fa-eye" id="password-icon"></i>
                      </button>
                    </div>
                    <div class="form-text">Senha definida no painel da Hostinger</div>
                  </div>

                  <!-- Encryption -->
                  <div class="col-md-6">
                    <label for="MAIL_ENCRYPTION" class="form-label">
                      <i class="fa-solid fa-shield-halved me-1"></i>Criptografia
                    </label>
                    <select class="form-select" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION">
                      <option value="tls" {{ $config['MAIL_ENCRYPTION'] == 'tls' ? 'selected' : '' }}>TLS</option>
                      <option value="ssl" {{ $config['MAIL_ENCRYPTION'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                      <option value="null" {{ $config['MAIL_ENCRYPTION'] == 'null' ? 'selected' : '' }}>Nenhuma</option>
                    </select>
                    <div class="form-text">Tipo de criptografia</div>
                  </div>

                  <!-- From Address -->
                  <div class="col-md-6">
                    <label for="MAIL_FROM_ADDRESS" class="form-label">
                      <i class="fa-solid fa-at me-1"></i>Email Remetente
                    </label>
                    <input type="email" class="form-control" id="MAIL_FROM_ADDRESS" name="MAIL_FROM_ADDRESS"
                      value="{{ $config['MAIL_FROM_ADDRESS'] ?? 'redacted@example.invalid' }}" required>
                    <div class="form-text">Email que aparecerá como remetente</div>
                  </div>

                  <!-- From Name -->
                  <div class="col-md-6">
                    <label for="MAIL_FROM_NAME" class="form-label">
                      <i class="fa-solid fa-signature me-1"></i>Nome Remetente
                    </label>
                    <input type="text" class="form-control" id="MAIL_FROM_NAME" name="MAIL_FROM_NAME"
                      value="{{ $config['MAIL_FROM_NAME'] ?? 'Gateway' }}" required>
                    <div class="form-text">Nome que aparecerá como remetente</div>
                  </div>
                </div>

                <div class="mt-4">
                  <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-2"></i>Salvar Configurações
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Teste de Email -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-success text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-paper-plane me-2"></i>Teste de Envio
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Teste se as configurações estão funcionando enviando um email de teste.</p>

              <form method="POST" action="{{ route('admin.smtp.test') }}">
                @csrf
                <div class="row g-3">
                  <div class="col-md-8">
                    <label for="test_email" class="form-label">
                      <i class="fa-solid fa-envelope me-1"></i>Email para Teste
                    </label>
                    <input type="email" class="form-control" id="test_email" name="test_email"
                      placeholder="redacted@example.invalid" required>
                    <div class="form-text">Digite um email válido para receber o teste</div>
                  </div>
                  <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                      <i class="fa-solid fa-paper-plane me-2"></i>Enviar Teste
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Informações Úteis -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-info-circle me-2"></i>Configurações Recomendadas
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-3">
                <div class="col-md-6">
                  <h6 class="fw-bold text-primary">Hostinger (Recomendado)</h6>
                  <ul class="list-unstyled">
                    <li><strong>Host:</strong> smtp.hostinger.com</li>
                    <li><strong>Porta:</strong> 587 (TLS) ou 465 (SSL)</li>
                    <li><strong>Criptografia:</strong> TLS ou SSL</li>
                    <li><strong>Usuário:</strong> redacted@example.invalid</li>
                    <li><strong>Senha:</strong> Senha do painel Hostinger</li>
                  </ul>
                </div>
                <div class="col-md-6">
                  <h6 class="fw-bold text-primary">Outras Opções</h6>
                  <ul class="list-unstyled">
                    <li><strong>Gmail:</strong> smtp.gmail.com:587</li>
                    <li><strong>Outlook:</strong> smtp-mail.outlook.com:587</li>
                    <li><strong>Yahoo:</strong> smtp.mail.yahoo.com:587</li>
                    <li><strong>SendGrid:</strong> smtp.sendgrid.net:587</li>
                  </ul>
                </div>
              </div>

              <div class="alert alert-info mt-3">
                <i class="fa-solid fa-info-circle me-2"></i>
                <strong>Hostinger:</strong> Use a senha que você definiu para o email no painel da Hostinger.
                Não é necessário senha de aplicativo como no Gmail.
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <style>
    /* Garantir que os campos SMTP sempre sejam visíveis */
    #MAIL_HOST,
    #MAIL_PORT,
    #MAIL_USERNAME,
    #MAIL_PASSWORD,
    #MAIL_ENCRYPTION {
      display: block !important;
      visibility: visible !important;
    }

    .col-md-6:has(#MAIL_HOST),
    .col-md-6:has(#MAIL_PORT),
    .col-md-6:has(#MAIL_USERNAME),
    .col-md-6:has(#MAIL_PASSWORD),
    .col-md-6:has(#MAIL_ENCRYPTION) {
      display: block !important;
      visibility: visible !important;
    }
  </style>

  <script>
    // Toggle password visibility
    function togglePassword() {
      const passwordInput = document.getElementById('MAIL_PASSWORD');
      const passwordIcon = document.getElementById('password-icon');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
      }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Garantir que todos os campos SMTP estejam sempre visíveis
      const smtpFields = ['MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_ENCRYPTION'];

      smtpFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
          field.closest('.col-md-6').style.display = 'block';
          field.closest('.col-md-6').style.visibility = 'visible';
        }
      });

      console.log('Formulário SMTP carregado - todos os campos editáveis');
    });
  </script>
</x-app-layout>