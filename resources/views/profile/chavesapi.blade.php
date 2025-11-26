<x-app-layout :route="'Token API PIX'">

  <div class="main-content app-content">
    <div class="container-fluid">
      
      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-primary">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-primary text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-key fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Chaves API</h1>
                  <p class="text-muted mb-0">Gerencie suas credenciais de autenticação</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Recursos do Gateway -->
        <div class="col-lg-8 col-md-12 mb-4">
          <div class="card h-100">
            <div class="card-body p-4">
              <h4 class="fw-semibold mb-4">
                <i class="fa-solid fa-rocket text-primary me-2"></i>
                Recursos do Gateway {{ env('APP_NAME') }}
              </h4>
              <p class="text-muted mb-3">
                Nossa API foi projetada com tecnologia de última geração para garantir alto desempenho,
                segurança robusta e escalabilidade real. Com uma arquitetura moderna e otimizada,
                possibilita o processamento de transações de forma rápida e confiável, assegurando a
                melhor experiência tanto para lojistas quanto para clientes finais.
              </p>
              <p class="text-muted mb-3">
                Disponibilizamos um painel de controle completo e personalizável, que oferece análises
                detalhadas de vendas e ferramentas avançadas para gestão financeira, facilitando a
                tomada de decisões estratégicas.
              </p>
              <p class="text-muted mb-3">
                A integração com as principais plataformas de e-commerce é simples e direta,
                proporcionando uma jornada fluida e sem barreiras. Além disso, a conexão nativa com as
                adquirentes otimiza o fluxo de pagamentos, reduzindo etapas intermediárias e aumentando
                a eficiência operacional.
              </p>
              <p class="text-muted mb-0">
                Seja para expandir sua operação ou modernizar sua infraestrutura de pagamentos, nossa
                API representa a solução definitiva para quem busca inovação, segurança e controle
                total.
              </p>
            </div>
          </div>
        </div>

        <!-- Credenciais API -->
        <div class="col-lg-4 col-md-12 mb-4">
          <div class="card h-100">
            <div class="card-body p-4 d-flex flex-column">
              <h4 class="fw-semibold mb-4">
                <i class="fa-solid fa-plug text-success me-2"></i>
                Integração com o Gateway
              </h4>

              <!-- Token -->
              <div class="mb-4">
                <label class="form-label fw-semibold mb-2">
                  <i class="fa-solid fa-lock text-primary me-1"></i>Token
                </label>
                <div class="input-group">
                  <button id="btn-show-key-token" class="btn btn-outline-secondary" onclick="mostrarToken()" type="button" title="Mostrar/Ocultar">
                    <i class="fa-solid fa-eye"></i>
                  </button>
                  <input type="text" id="token" class="form-control font-monospace" value="***********************" readonly>
                  <button class="btn btn-outline-primary" onclick="copiarToken()" type="button" title="Copiar">
                    <i class="fa-solid fa-copy"></i>
                  </button>
                </div>
              </div>

              <!-- Secret -->
              <div class="mb-4">
                <label class="form-label fw-semibold mb-2">
                  <i class="fa-solid fa-shield-halved text-warning me-1"></i>Secret
                </label>
                <div class="input-group">
                  <button id="btn-show-key-secret" class="btn btn-outline-secondary" onclick="mostrarSecret()" type="button" title="Mostrar/Ocultar">
                    <i class="fa-solid fa-eye"></i>
                  </button>
                  <input type="text" id="secret" class="form-control font-monospace" value="***********************" readonly>
                  <button class="btn btn-outline-primary" onclick="copiarSecret()" type="button" title="Copiar">
                    <i class="fa-solid fa-copy"></i>
                  </button>
                </div>
              </div>

              <!-- Hidden Inputs -->
              <input id="chave-secret" value="{{ $secret }}" type="hidden" />
              <input id="chave-token" value="{{ $token }}" type="hidden" />

              <!-- Endpoint -->
              <div class="mt-auto">
                <label class="form-label fw-semibold mb-2">
                  <i class="fa-solid fa-link text-info me-1"></i>API Endpoint
                  <small class="text-muted">(Clique para copiar)</small>
                </label>
                <input type="text" id="endpoint" class="form-control font-monospace" 
                  value="{{ env('APP_URL').'/api/' }}" 
                  readonly 
                  onclick="copyToClipboard()"
                  title="Clique para copiar"
                  style="cursor: pointer;">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Copiar Endpoint
    function copyToClipboard() {
      const endpointInput = document.getElementById("endpoint");
      navigator.clipboard.writeText(endpointInput.value)
        .then(() => {
          showToast('success', 'Endpoint copiado com sucesso!');
        })
        .catch(err => {
          console.error("Erro ao copiar endpoint:", err);
          showToast('error', 'Falha ao copiar o endpoint.');
        });
    }

    // Copiar Token
    function copiarToken() {
      const tokenValue = document.getElementById("chave-token").value;
      navigator.clipboard.writeText(tokenValue)
        .then(() => {
          showToast('success',"Token copiado com sucesso!");
        })
        .catch(err => {
          console.error("Erro ao copiar token:", err);
          showToast('error', 'Falha ao copiar o token.');
        });
    }

    // Copiar Secret
    function copiarSecret() {
      const secretValue = document.getElementById("chave-secret").value;
      navigator.clipboard.writeText(secretValue)
        .then(() => {
          showToast('success',"Secret copiado com sucesso!");
        })
        .catch(err => {
          console.error("Erro ao copiar secret:", err);
          showToast('error', 'Falha ao copiar o secret.');
        });
    }

    // Mostrar/Ocultar Token
    function mostrarToken() {
      const tokenInput = document.getElementById("token");
      const btnIcon = document.querySelector('#btn-show-key-token i');
      const tokenValue = document.getElementById("chave-token").value;

      if (tokenInput.value.includes("*")) {
        tokenInput.value = tokenValue;
        btnIcon.classList.remove('fa-eye');
        btnIcon.classList.add('fa-eye-slash');
      } else {
        tokenInput.value = '***********************';
        btnIcon.classList.remove('fa-eye-slash');
        btnIcon.classList.add('fa-eye');
      }
    }

    // Mostrar/Ocultar Secret
    function mostrarSecret() {
      const secretInput = document.getElementById("secret");
      const btnIcon = document.querySelector('#btn-show-key-secret i');
      const secretValue = document.getElementById("chave-secret").value;

      if (secretInput.value.includes("*")) {
        secretInput.value = secretValue;
        btnIcon.classList.remove('fa-eye');
        btnIcon.classList.add('fa-eye-slash');
      } else {
        secretInput.value = '***********************';
        btnIcon.classList.remove('fa-eye-slash');
        btnIcon.classList.add('fa-eye');
      }
    }
  </script>
</x-app-layout>

