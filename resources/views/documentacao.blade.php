<x-app-layout :route="'Documentação API PIX'">
  <link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/line-numbers/prism-line-numbers.css" rel="stylesheet" />

  <div class="main-content app-content">
    <div class="container-fluid">
      @php
      $setting = \App\Helpers\Helper::getSetting();
      @endphp

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-primary">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-primary text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-file-code fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">API {{ $setting->gateway_name ?? 'Gateway' }}</h1>
                  <p class="text-muted mb-0">Documentação técnica para integração de pagamentos PIX, Cartão e Boleto</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PIX IN -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-success text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-download me-2"></i>Depósito (PIX IN)
                <span class="badge bg-light text-dark ms-2">POST</span>
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Gera um pagamento via QrCode para depósito.</p>
              <div class="alert alert-info">
                <strong><i class="fa-solid fa-link me-2"></i>Endpoint:</strong>
                <code>{{ config('app.url') }}/api/wallet/deposit/payment</code>
              </div>

              <!-- Headers -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-lock me-2 text-primary"></i>Cabeçalhos (Headers)
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "Content-Type":"application/json",
  "Accept":"application/json"
}</code></pre>

              <!-- Request Body -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-upload me-2 text-primary"></i>Corpo da Requisição
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "token":"seu_token",
  "secret":"seu_secret",
  "amount": 100.00,
  "debtor_name":"Nome do Cliente",
  "email":"redacted@example.invalid",
  "debtor_document_number":"12345678901",
  "phone":"11999999999",
  "method_pay":"pix",
  "postback":"https://seusite.com/webhook",
  "split_email":"redacted@example.invalid",
  "split_username":"@admin",
  "split_percentage": 10.00
}</code></pre>

              <!-- Parameters -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-info-circle me-2 text-primary"></i>Parâmetros
              </h6>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Parâmetro</th>
                      <th>Tipo</th>
                      <th>Descrição</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><span class="badge bg-light text-dark">token</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Token de autenticação</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">secret</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Chave secreta de autenticação</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">amount</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Valor do depósito em reais</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">debtor_name</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Nome do devedor/cliente</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">email</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Email do cliente</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">debtor_document_number</span></td>
                      <td><span class="badge bg-secondary">Opcional</span></td>
                      <td>CPF/CNPJ do cliente</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">phone</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Telefone do cliente</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">method_pay</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Método de pagamento (ex: "pix")</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">postback</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>URL do webhook para receber notificações de status</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">split_email</span></td>
                      <td><span class="badge bg-secondary">Opcional</span></td>
                      <td>Email para split de pagamento</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">split_username</span></td>
                      <td><span class="badge bg-secondary">Opcional</span></td>
                      <td>Username para split (ex: @admin)</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">split_percentage</span></td>
                      <td><span class="badge bg-secondary">Opcional</span></td>
                      <td>Porcentagem do split (0-100)</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Response -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-check-circle me-2 text-success"></i>Resposta
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "idTransaction":"TX123",
  "qrcode":"código copia e cola",
  "qr_code_image_url":"url da imagem"
}</code></pre>
            </div>
          </div>
        </div>
      </div>

      <!-- Webhook PIX IN -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-warning text-dark">
              <h5 class="mb-0">
                <i class="fa-solid fa-bell me-2"></i>Webhook PIX IN
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Retorno automático na rota <span class="badge bg-light text-dark">postback</span> informada na criação do depósito.</p>

              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-code me-2 text-primary"></i>Exemplo de Retorno
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "idTransaction":"TX123",
  "status":"paid",
  "typeTransaction":"PIX",
  "amount": 100.00,
  "debtor_name":"Nome",
  "email":"redacted@example.invalid",
  "debtor_document_number":"12345678901",
  "phone":"11999999999",
  "created_at":"2025-09-10T17:00:00.000Z",
  "paid_at":"2025-09-10T17:05:00.000Z",
  "split_processed": true,
  "split_amount": 10.00,
  "split_recipient":"redacted@example.invalid"
}</code></pre>
            </div>
          </div>
        </div>
      </div>

      <!-- Webhook PIX OUT -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-warning text-dark">
              <h5 class="mb-0">
                <i class="fa-solid fa-bell me-2"></i>Webhook PIX OUT
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Retorno automático na rota <span class="badge bg-light text-dark">baasPostbackUrl</span> informada na requisição de saque.</p>

              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-code me-2 text-primary"></i>Exemplo de Retorno
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "status":"paid",
  "idTransaction":"TX123",
  "typeTransaction":"PAYMENT",
  "externalId":"EXT_REF_1234567890"
}</code></pre>

              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-info-circle me-2 text-primary"></i>Campos do Webhook
              </h6>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Campo</th>
                      <th>Descrição</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><span class="badge bg-light text-dark">status</span></td>
                      <td>Status do saque (paid, completed, failed)</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">idTransaction</span></td>
                      <td>ID único da transação no sistema</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">typeTransaction</span></td>
                      <td>Tipo da transação (PAYMENT, WITHDRAW)</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">externalId</span></td>
                      <td>Referência externa da transação (mesmo valor enviado em idTransaction)</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PIX OUT -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-danger text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-upload me-2"></i>Saque (PIX OUT)
                <span class="badge bg-light text-dark ms-2">POST</span>
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Realiza um saque para uma chave PIX.</p>
              <div class="alert alert-info">
                <strong><i class="fa-solid fa-link me-2"></i>Endpoint:</strong>
                <code>{{ config('app.url') }}/api/wallet/saque/payment</code>
              </div>

              <!-- Headers -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-lock me-2 text-primary"></i>Cabeçalhos (Headers)
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "Content-Type":"application/json",
  "Accept":"application/json"
}</code></pre>

              <!-- Request Body -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-upload me-2 text-primary"></i>Corpo da Requisição
              </h6>
              <div class="alert alert-warning">
                <strong>pixKeyType:</strong> 'cpf' | 'cnpj' | 'email' | 'phone' | 'random'
              </div>
              <pre class="line-numbers"><code class="language-json">{
  "token":"seu_token",
  "secret":"seu_secret",
  "amount": 100.00,
  "pixKey":"chave_pix",
  "pixKeyType":"cpf",
  "baasPostbackUrl":"https://seusite.com/webhook",
  "idTransaction":"ID_UNICO_TRANSACAO"
}</code></pre>

              <!-- Parameters -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-info-circle me-2 text-primary"></i>Parâmetros
              </h6>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Parâmetro</th>
                      <th>Tipo</th>
                      <th>Descrição</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><span class="badge bg-light text-dark">token</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Token de autenticação</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">secret</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Chave secreta de autenticação</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">amount</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Valor do saque em reais</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">pixKey</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Chave PIX de destino</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">pixKeyType</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Tipo da chave PIX</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">baasPostbackUrl</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>URL do webhook para receber notificações de status</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">idTransaction</span></td>
                      <td><span class="badge bg-secondary">Opcional</span></td>
                      <td>ID único da transação no seu sistema - será usado como externalId</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Response -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-check-circle me-2 text-success"></i>Resposta
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "id":"b522a295-e404...",
  "amount": 100,
  "pixKey":"chave",
  "pixKeyType":"cpf",
  "withdrawStatusId":"PendingProcessing",
  "createdAt":"2025-04-19T20:04:53.166Z",
  "updatedAt":"2025-04-19T20:04:53.166Z"
}</code></pre>

              <!-- External ID -->
              <div class="alert alert-info mt-4">
                <h6 class="alert-heading fw-semibold">
                  <i class="fa-solid fa-link me-2"></i>External ID
                </h6>
                <p class="mb-0">Use o campo <span class="badge bg-light text-dark">idTransaction</span> para enviar um ID único do seu sistema. Este ID será retornado no webhook como <span class="badge bg-light text-dark">externalId</span> para facilitar a reconciliação.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CARTÃO -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-credit-card me-2"></i>Pagamento com Cartão
                <span class="badge bg-light text-dark ms-2">POST</span>
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Processa pagamentos com cartão de crédito ou débito via API.</p>
              <div class="alert alert-info">
                <strong><i class="fa-solid fa-link me-2"></i>Endpoint:</strong>
                <code>{{ config('app.url') }}/api/card/payment</code>
              </div>

              <!-- Headers -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-lock me-2 text-primary"></i>Cabeçalhos (Headers)
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "Content-Type":"application/json",
  "Accept":"application/json"
}</code></pre>

              <!-- Request Body -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-upload me-2 text-primary"></i>Corpo da Requisição
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "amount": 100.00,
  "client_name":"João Silva",
  "client_email":"redacted@example.invalid",
  "client_document":"12345678901",
  "client_phone":"11999999999",
  "installments": 1,
  "card": {
    "number":"4111111111111111",
    "holder_name":"JOÃO SILVA",
    "expiration_month":"12",
    "expiration_year":"2025",
    "cvv":"123"
  },
  "description":"Pagamento via Cartão",
  "return_url":"https://seusite.com/retorno",
  "postback_url":"https://seusite.com/webhook"
}</code></pre>

              <!-- Parameters -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-info-circle me-2 text-primary"></i>Parâmetros
              </h6>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Parâmetro</th>
                      <th>Tipo</th>
                      <th>Descrição</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><span class="badge bg-light text-dark">amount</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Valor do pagamento em reais</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">client_name</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Nome do cliente</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">client_email</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Email do cliente</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">client_document</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>CPF/CNPJ do cliente</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">client_phone</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Telefone do cliente</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">installments</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Número de parcelas (1-12)</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">card.number</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Número do cartão</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">card.holder_name</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Nome do portador</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">card.expiration_month</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Mês de vencimento (1-12)</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">card.expiration_year</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Ano de vencimento</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">card.cvv</span></td>
                      <td><span class="badge bg-danger">Obrigatório</span></td>
                      <td>Código de segurança</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">description</span></td>
                      <td><span class="badge bg-secondary">Opcional</span></td>
                      <td>Descrição da transação</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">return_url</span></td>
                      <td><span class="badge bg-secondary">Opcional</span></td>
                      <td>URL de retorno</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-light text-dark">postback_url</span></td>
                      <td><span class="badge bg-secondary">Opcional</span></td>
                      <td>URL do webhook</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Response -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-check-circle me-2 text-success"></i>Resposta
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "status":"success",
  "data": {
    "transaction_id":"TXN123456789",
    "external_reference":"API_CARD_1234567890_1234567890",
    "status":"processing",
    "amount": 100.00,
    "amount_net": 95.00,
    "fee": 5.00,
    "installments": 1,
    "created_at":"2025-01-15T14:30:00.000Z",
    "return_url":"https://seusite.com/retorno"
  }
}</code></pre>
            </div>
          </div>
        </div>
      </div>

      <!-- Consulta Status Cartão -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-search me-2"></i>Consulta Status do Pagamento
                <span class="badge bg-light text-dark ms-2">GET</span>
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Consulta o status de um pagamento com cartão.</p>
              <div class="alert alert-info">
                <strong><i class="fa-solid fa-link me-2"></i>Endpoint:</strong>
                <code>{{ config('app.url') }}/api/card/payment/{transaction_id}</code>
              </div>

              <!-- Response -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-check-circle me-2 text-success"></i>Resposta
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "status":"success",
  "data": {
    "transaction_id":"TXN123456789",
    "external_reference":"API_CARD_1234567890_1234567890",
    "status":"paid",
    "amount": 100.00,
    "amount_net": 95.00,
    "fee": 5.00,
    "client_name":"João Silva",
    "client_email":"redacted@example.invalid",
    "description":"Pagamento via Cartão",
    "created_at":"2025-01-15T14:30:00.000Z",
    "updated_at":"2025-01-15T14:35:00.000Z"
  }
}</code></pre>
            </div>
          </div>
        </div>
      </div>

      <!-- Webhook Cartão -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-warning text-dark">
              <h5 class="mb-0">
                <i class="fa-solid fa-bell me-2"></i>Webhook Cartão
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Retorno automático na URL informada no campo <span class="badge bg-light text-dark">postback_url</span>.</p>

              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-code me-2 text-primary"></i>Exemplo de Retorno
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "type":"transaction.status_changed",
  "data": {
    "id":"TXN123456789",
    "status":"approved",
    "amount": 10000,
    "installments": 1,
    "created_at":"2025-01-15T14:30:00.000Z",
    "updated_at":"2025-01-15T14:35:00.000Z"
  }
}</code></pre>

              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-info-circle me-2 text-primary"></i>Status do Cartão
              </h6>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Status</th>
                      <th>Descrição</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><span class="badge bg-warning text-dark">pending</span></td>
                      <td>Aguardando processamento</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-info">processing</span></td>
                      <td>Processando</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-success">approved</span></td>
                      <td>Aprovado</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-success">paid</span></td>
                      <td>Pago</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-danger">refused</span></td>
                      <td>Recusado</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-secondary">cancelled</span></td>
                      <td>Cancelado</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-warning text-dark">refunded</span></td>
                      <td>Reembolsado</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- BOLETO -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-secondary text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-barcode me-2"></i>Pagamento com Boleto
                <span class="badge bg-light text-dark ms-2">POST</span>
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Gera um boleto bancário para pagamento.</p>
              <div class="alert alert-info">
                <strong><i class="fa-solid fa-link me-2"></i>Endpoint:</strong>
                <code>{{ config('app.url') }}/api/billet/charge</code>
              </div>

              <!-- Request Body -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-upload me-2 text-primary"></i>Corpo da Requisição
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "amount": 100.00,
  "client_name":"João Silva",
  "client_email":"redacted@example.invalid",
  "client_document":"12345678901",
  "client_phone":"11999999999",
  "description":"Pagamento via Boleto",
  "due_date":"2025-02-15",
  "postback_url":"https://seusite.com/webhook"
}</code></pre>

              <!-- Response -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-check-circle me-2 text-success"></i>Resposta
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "status":"success",
  "data": {
    "transaction_id":"BOL123456789",
    "barcode":"23791234567890123456789012345678901234567890",
    "billet_url":"https://api.exemplo.com/billet/BOL123456789.pdf",
    "due_date":"2025-02-15",
    "amount": 100.00,
    "status":"pending"
  }
}</code></pre>
            </div>
          </div>
        </div>
      </div>

      <!-- Códigos de Status -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-list me-2"></i>Códigos de Status
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-4">Códigos de resposta HTTP e status de transação.</p>

              <!-- HTTP Status Codes -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-code me-2 text-primary"></i>HTTP Status Codes
              </h6>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Código</th>
                      <th>Descrição</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><span class="badge bg-success">200</span></td>
                      <td>Sucesso</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-warning text-dark">400</span></td>
                      <td>Dados inválidos</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-danger">401</span></td>
                      <td>Não autorizado</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-danger">403</span></td>
                      <td>IP não autorizado</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-warning text-dark">422</span></td>
                      <td>Erro de validação</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-danger">500</span></td>
                      <td>Erro interno do servidor</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Transaction Status -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-exchange-alt me-2 text-primary"></i>Status de Transação
              </h6>
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="card bg-light">
                    <div class="card-body">
                      <h6 class="fw-semibold mb-2">
                        <i class="fa-solid fa-download text-success me-2"></i>PIX IN
                      </h6>
                      <p class="mb-0">
                        <span class="badge bg-warning text-dark">pending</span>
                        <i class="fa-solid fa-arrow-right mx-2"></i>
                        <span class="badge bg-success">paid</span>
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-light">
                    <div class="card-body">
                      <h6 class="fw-semibold mb-2">
                        <i class="fa-solid fa-upload text-danger me-2"></i>PIX OUT
                      </h6>
                      <p class="mb-0">
                        <span class="badge bg-warning text-dark">PendingProcessing</span>
                        <i class="fa-solid fa-arrow-right mx-2"></i>
                        <span class="badge bg-success">paid</span>
                        /
                        <span class="badge bg-info">completed</span>
                        /
                        <span class="badge bg-danger">failed</span>
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-light">
                    <div class="card-body">
                      <h6 class="fw-semibold mb-2">
                        <i class="fa-solid fa-credit-card text-primary me-2"></i>CARTÃO
                      </h6>
                      <p class="mb-0">
                        <span class="badge bg-warning text-dark">pending</span>
                        <i class="fa-solid fa-arrow-right mx-2"></i>
                        <span class="badge bg-info">processing</span>
                        <i class="fa-solid fa-arrow-right mx-2"></i>
                        <span class="badge bg-success">paid</span>
                        /
                        <span class="badge bg-danger">refused</span>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CÓDIGOS DE ERRO -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-danger text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-exclamation-triangle me-2"></i>Códigos de Erro HTTP
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Lista completa dos códigos de erro HTTP retornados pela API.</p>

              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Código</th>
                      <th>Status</th>
                      <th>Descrição</th>
                      <th>Solução</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><span class="badge bg-danger">400</span></td>
                      <td>Bad Request</td>
                      <td>Dados inválidos na requisição</td>
                      <td>Verifique os parâmetros obrigatórios</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-warning text-dark">401</span></td>
                      <td>Unauthorized</td>
                      <td>Token/Secret inválidos ou expirados</td>
                      <td>Verifique suas credenciais de API</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-warning text-dark">403</span></td>
                      <td>Forbidden</td>
                      <td>IP não autorizado para saques</td>
                      <td>Configure IPs autorizados no painel</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-info">422</span></td>
                      <td>Unprocessable Entity</td>
                      <td>Erro de validação dos dados</td>
                      <td>Corrija os dados conforme especificação</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-warning text-dark">429</span></td>
                      <td>Too Many Requests</td>
                      <td>Rate limit excedido</td>
                      <td>Aguarde antes de fazer nova requisição</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-secondary">500</span></td>
                      <td>Internal Server Error</td>
                      <td>Erro interno do servidor</td>
                      <td>Entre em contato com o suporte</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-code me-2 text-primary"></i>Exemplo de Resposta de Erro
              </h6>
              <pre class="line-numbers"><code class="language-json">{
  "status": "error",
  "message": "IP não autorizado para realizar saques",
  "client_ip": "191.44.21.53",
  "error_code": "UNAUTHORIZED_IP",
  "timestamp": "2025-01-15T14:30:00.000Z"
}</code></pre>
            </div>
          </div>
        </div>
      </div>

      <!-- LIMITES E RESTRIÇÕES -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-warning text-dark">
              <h5 class="mb-0">
                <i class="fa-solid fa-shield-halved me-2"></i>Limites e Restrições
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Limites de transação e restrições de segurança da API.</p>

              <div class="row">
                <div class="col-md-6">
                  <h6 class="fw-semibold mb-3">
                    <i class="fa-solid fa-coins me-2 text-success"></i>Valores de Transação
                  </h6>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead class="table-light">
                        <tr>
                          <th>Tipo</th>
                          <th>Mínimo</th>
                          <th>Máximo</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><strong>PIX IN</strong></td>
                          <td>R$ 1,00</td>
                          <td>R$ 50.000,00</td>
                        </tr>
                        <tr>
                          <td><strong>PIX OUT</strong></td>
                          <td>R$ 5,00</td>
                          <td>R$ 10.000,00</td>
                        </tr>
                        <tr>
                          <td><strong>Cartão</strong></td>
                          <td>R$ 1,00</td>
                          <td>R$ 5.000,00</td>
                        </tr>
                        <tr>
                          <td><strong>Boleto</strong></td>
                          <td>R$ 5,00</td>
                          <td>R$ 2.000,00</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="col-md-6">
                  <h6 class="fw-semibold mb-3">
                    <i class="fa-solid fa-clock me-2 text-primary"></i>Rate Limiting
                  </h6>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead class="table-light">
                        <tr>
                          <th>Endpoint</th>
                          <th>Limite</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><strong>PIX IN</strong></td>
                          <td>60 req/min</td>
                        </tr>
                        <tr>
                          <td><strong>PIX OUT</strong></td>
                          <td>30 req/min</td>
                        </tr>
                        <tr>
                          <td><strong>Cartão</strong></td>
                          <td>20 req/min</td>
                        </tr>
                        <tr>
                          <td><strong>Consulta</strong></td>
                          <td>100 req/min</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-lock me-2 text-danger"></i>Segurança para Saques
              </h6>
              <div class="alert alert-warning">
                <h6 class="alert-heading fw-semibold">
                  <i class="fa-solid fa-exclamation-triangle me-2"></i>Controle de IP
                </h6>
                <p class="mb-2">Para realizar saques, você deve configurar IPs autorizados:</p>
                <ul class="mb-0">
                  <li>Acesse <strong>Perfil → IPs de Saque</strong></li>
                  <li>Adicione os IPs dos seus servidores</li>
                  <li>Use o IP real do cliente para requisições da API</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- GUIA DE INTEGRAÇÃO -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-book me-2"></i>Guia de Integração
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-3">Exemplos práticos de integração em diferentes linguagens.</p>

              <!-- JavaScript/Node.js -->
              <h6 class="fw-semibold mb-3">
                <i class="fa-brands fa-js-square me-2 text-warning"></i>JavaScript/Node.js
              </h6>
              <pre class="line-numbers"><code class="language-javascript">// PIX IN - Criar Depósito
const axios = require('axios');

async function criarDeposito() {
  try {
    const response = await axios.post('{{ config("app.url") }}/api/wallet/deposit/payment', {
      token: 'seu_token',
      secret: 'seu_secret',
      amount: 100.00,
      debtor_name: 'João Silva',
      email: 'redacted@example.invalid',
      debtor_document_number: '12345678901',
      phone: '11999999999',
      method_pay: 'pix',
      postback: 'https://seusite.com/webhook'
    }, {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });
    
    console.log('QR Code:', response.data.qrcode);
    console.log('ID Transação:', response.data.idTransaction);
  } catch (error) {
    console.error('Erro:', error.response.data);
  }
}

// PIX OUT - Realizar Saque
async function realizarSaque() {
  try {
    const response = await axios.post('{{ config("app.url") }}/api/wallet/saque/payment', {
      token: 'seu_token',
      secret: 'seu_secret',
      amount: 50.00,
      pixKey: '17865551746',
      pixKeyType: 'cpf',
      baasPostbackUrl: 'https://seusite.com/webhook',
      idTransaction: 'SAQUE_' + Date.now()
    });
    
    console.log('Saque criado:', response.data);
  } catch (error) {
    console.error('Erro:', error.response.data);
  }
}</code></pre>

              <!-- PHP -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-brands fa-php me-2 text-primary"></i>PHP
              </h6>
              <pre class="line-numbers"><code class="language-php">&lt;?php
// PIX IN - Criar Depósito
function criarDeposito() {
    $url = '{{ config("app.url") }}/api/wallet/deposit/payment';
    
    $data = [
        'token' => 'seu_token',
        'secret' => 'seu_secret',
        'amount' => 100.00,
        'debtor_name' => 'João Silva',
        'email' => 'redacted@example.invalid',
        'debtor_document_number' => '12345678901',
        'phone' => '11999999999',
        'method_pay' => 'pix',
        'postback' => 'https://seusite.com/webhook'
    ];
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    if ($result === FALSE) {
        throw new Exception('Erro na requisição');
    }
    
    return json_decode($result, true);
}

// PIX OUT - Realizar Saque
function realizarSaque() {
    $url = '{{ config("app.url") }}/api/wallet/saque/payment';
    
    $data = [
        'token' => 'seu_token',
        'secret' => 'seu_secret',
        'amount' => 50.00,
        'pixKey' => '17865551746',
        'pixKeyType' => 'cpf',
        'baasPostbackUrl' => 'https://seusite.com/webhook',
        'idTransaction' => 'SAQUE_' . time()
    ];
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    if ($result === FALSE) {
        throw new Exception('Erro na requisição');
    }
    
    return json_decode($result, true);
}
?></code></pre>

              <!-- Python -->
              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-brands fa-python me-2 text-success"></i>Python
              </h6>
              <pre class="line-numbers"><code class="language-python">import requests
import json

# PIX IN - Criar Depósito
def criar_deposito():
    url = '{{ config("app.url") }}/api/wallet/deposit/payment'
    
    data = {
        'token': 'seu_token',
        'secret': 'seu_secret',
        'amount': 100.00,
        'debtor_name': 'João Silva',
        'email': 'redacted@example.invalid',
        'debtor_document_number': '12345678901',
        'phone': '11999999999',
        'method_pay': 'pix',
        'postback': 'https://seusite.com/webhook'
    }
    
    headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
    
    try:
        response = requests.post(url, json=data, headers=headers)
        response.raise_for_status()
        return response.json()
    except requests.exceptions.RequestException as e:
        print(f'Erro: {e}')
        return None

# PIX OUT - Realizar Saque
def realizar_saque():
    url = '{{ config("app.url") }}/api/wallet/saque/payment'
    
    data = {
        'token': 'seu_token',
        'secret': 'seu_secret',
        'amount': 50.00,
        'pixKey': '17865551746',
        'pixKeyType': 'cpf',
        'baasPostbackUrl': 'https://seusite.com/webhook',
        'idTransaction': f'SAQUE_{int(time.time())}'
    }
    
    headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
    
    try:
        response = requests.post(url, json=data, headers=headers)
        response.raise_for_status()
        return response.json()
    except requests.exceptions.RequestException as e:
        print(f'Erro: {e}')
        return None</code></pre>

              <h6 class="fw-semibold mt-4 mb-3">
                <i class="fa-solid fa-lightbulb me-2 text-warning"></i>Dicas Importantes
              </h6>
              <div class="alert alert-info">
                <ul class="mb-0">
                  <li><strong>ID de Transação:</strong> Use sempre IDs únicos para facilitar a reconciliação</li>
                  <li><strong>Webhooks:</strong> Configure URLs válidas para receber notificações</li>
                  <li><strong>Rate Limiting:</strong> Implemente retry com backoff exponencial</li>
                  <li><strong>Validação:</strong> Sempre valide os dados antes de enviar</li>
                  <li><strong>Logs:</strong> Mantenha logs detalhados das transações</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/line-numbers/prism-line-numbers.js"></script>
</x-app-layout>