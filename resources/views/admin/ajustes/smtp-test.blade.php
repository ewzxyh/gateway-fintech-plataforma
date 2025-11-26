<x-app-layout :route="'[ADMIN] Teste SMTP'">
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
                  <h1 class="display-6 fw-bold mb-1">Teste SMTP</h1>
                  <p class="text-muted mb-0">Página de teste para verificar se o SMTP está funcionando</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

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
                      <option value="log">Log (Desenvolvimento)</option>
                      <option value="smtp" selected>SMTP</option>
                    </select>
                    <div class="form-text">Escolha o driver de email que deseja usar</div>
                  </div>

                  <!-- Host SMTP -->
                  <div class="col-md-6">
                    <label for="MAIL_HOST" class="form-label">
                      <i class="fa-solid fa-globe me-1"></i>Host SMTP
                    </label>
                    <input type="text" class="form-control" id="MAIL_HOST" name="MAIL_HOST"
                      value="smtp.hostinger.com" placeholder="smtp.hostinger.com">
                    <div class="form-text">Ex: smtp.hostinger.com</div>
                  </div>

                  <!-- Porta -->
                  <div class="col-md-6">
                    <label for="MAIL_PORT" class="form-label">
                      <i class="fa-solid fa-plug me-1"></i>Porta
                    </label>
                    <input type="number" class="form-control" id="MAIL_PORT" name="MAIL_PORT"
                      value="587" placeholder="587">
                    <div class="form-text">587 (TLS) ou 465 (SSL)</div>
                  </div>

                  <!-- Username -->
                  <div class="col-md-6">
                    <label for="MAIL_USERNAME" class="form-label">
                      <i class="fa-solid fa-user me-1"></i>Usuário
                    </label>
                    <input type="text" class="form-control" id="MAIL_USERNAME" name="MAIL_USERNAME"
                      value="redacted@example.invalid" placeholder="redacted@example.invalid">
                    <div class="form-text">Email completo do servidor SMTP</div>
                  </div>

                  <!-- Password -->
                  <div class="col-md-6">
                    <label for="MAIL_PASSWORD" class="form-label">
                      <i class="fa-solid fa-lock me-1"></i>Senha
                    </label>
                    <input type="password" class="form-control" id="MAIL_PASSWORD" name="MAIL_PASSWORD"
                      placeholder="Digite sua senha">
                    <div class="form-text">Senha do email</div>
                  </div>

                  <!-- Encryption -->
                  <div class="col-md-6">
                    <label for="MAIL_ENCRYPTION" class="form-label">
                      <i class="fa-solid fa-shield-halved me-1"></i>Criptografia
                    </label>
                    <select class="form-select" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION">
                      <option value="tls" selected>TLS</option>
                      <option value="ssl">SSL</option>
                      <option value="null">Nenhuma</option>
                    </select>
                    <div class="form-text">Tipo de criptografia</div>
                  </div>

                  <!-- From Address -->
                  <div class="col-md-6">
                    <label for="MAIL_FROM_ADDRESS" class="form-label">
                      <i class="fa-solid fa-at me-1"></i>Email Remetente
                    </label>
                    <input type="email" class="form-control" id="MAIL_FROM_ADDRESS" name="MAIL_FROM_ADDRESS"
                      value="redacted@example.invalid" required>
                    <div class="form-text">Email que aparecerá como remetente</div>
                  </div>

                  <!-- From Name -->
                  <div class="col-md-6">
                    <label for="MAIL_FROM_NAME" class="form-label">
                      <i class="fa-solid fa-signature me-1"></i>Nome Remetente
                    </label>
                    <input type="text" class="form-control" id="MAIL_FROM_NAME" name="MAIL_FROM_NAME"
                      value="Gateway" required>
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

    </div>
  </div>
</x-app-layout>