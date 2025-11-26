<x-app-layout :route="'Webhook'">

  <div class="main-content app-content">
    <div class="container-fluid">
      
      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-primary">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-primary text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-bell fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Webhooks</h1>
                  <p class="text-muted mb-0">Receba notificações em tempo real sobre eventos</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Informações sobre Webhooks -->
        <div class="col-lg-8 col-md-12 mb-4">
          <div class="card h-100">
            <div class="card-body p-4">
              <h4 class="fw-semibold mb-4">
                <i class="fa-solid fa-circle-info text-primary me-2"></i>
                O que são Webhooks?
              </h4>
              <p class="text-muted mb-3">
                Webhooks são uma maneira poderosa de receber notificações automáticas em tempo real sobre eventos que acontecem em sua conta. Em vez de você precisar consultar nossa API repetidamente para saber se algo mudou, nós enviamos uma requisição HTTP para a URL que você configurar sempre que um evento de seu interesse ocorrer.
              </p>
              <p class="text-muted mb-4">
                Isso torna sua aplicação mais eficiente e reativa, permitindo automatizar processos como atualizar o status de um pedido, liberar o acesso a um produto digital ou notificar sua equipe de vendas.
              </p>
              
              <h5 class="fw-semibold mb-3">
                <i class="fa-solid fa-gear text-success me-2"></i>
                Como configurar:
              </h5>
              <ol class="text-muted ps-4">
                <li class="mb-2">
                  <strong>Informe a URL:</strong> No campo"URL do Webhook", insira o endereço do seu sistema que está preparado para receber as notificações (requisições POST com corpo em JSON).
                </li>
                <li class="mb-2">
                  <strong>Selecione os Eventos:</strong> Ative os eventos para os quais deseja ser notificado. Você pode escolher ser avisado sempre que um Pix for gerado e/ou quando um pagamento for confirmado.
                </li>
                <li>
                  <strong>Salve as Alterações:</strong> Clique em"Salvar Webhook" e nosso sistema começará a enviar as notificações para a URL configurada.
                </li>
              </ol>
            </div>
          </div>
        </div>

        <!-- Configuração do Webhook -->
        <div class="col-lg-4 col-md-12 mb-4">
          <div class="card h-100">
            <div class="card-body p-4 d-flex flex-column">
              <h4 class="fw-semibold mb-4">
                <i class="fa-solid fa-wrench text-warning me-2"></i>
                Configuração
              </h4>
              
              <form method="POST" action="{{ route('webhook.update') }}" class="d-flex flex-column h-100">
                @csrf
                
                <!-- URL do Webhook -->
                <div class="mb-4">
                  <label for="webhook_url" class="form-label fw-semibold mb-2">
                    <i class="fa-solid fa-link text-primary me-1"></i>
                    URL do Webhook
                  </label>
                  <input type="url" 
                    id="webhook_url" 
                    name="webhook_url" 
                    value="{{ auth()->user()->webhook_url }}" 
                    class="form-control" 
                    placeholder="https://seu-site.com/notificacoes" 
                    required>
                  <small class="form-text text-muted">Endpoint que receberá as notificações POST</small>
                </div>

                <!-- Eventos -->
                <div class="mb-4">
                  <label class="form-label fw-semibold mb-3">
                    <i class="fa-solid fa-toggle-on text-success me-1"></i>
                    Eventos a serem notificados
                  </label>
                  
                  <div class="border rounded p-3">
                    <!-- Pix Gerado -->
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                      <div>
                        <label for="pix_gerado" class="fw-medium mb-0" style="cursor: pointer;">
                          <i class="fa-solid fa-qrcode text-info me-2"></i>
                          Pix Gerado
                        </label>
                        <small class="d-block text-muted">Notificar quando QR Code for criado</small>
                      </div>
                      <div class="form-check form-switch">
                        <input class="form-check-input" 
                          type="checkbox" 
                          role="switch" 
                          id="pix_gerado" 
                          value="gerado"
                          style="cursor: pointer; width: 44px; height: 24px;">
                      </div>
                    </div>
                    
                    <!-- Pix Pago -->
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <label for="pix_pago" class="fw-medium mb-0" style="cursor: pointer;">
                          <i class="fa-solid fa-check-circle text-success me-2"></i>
                          Pix Pago
                        </label>
                        <small class="d-block text-muted">Notificar quando pagamento for confirmado</small>
                      </div>
                      <div class="form-check form-switch">
                        <input class="form-check-input" 
                          type="checkbox" 
                          role="switch" 
                          id="pix_pago" 
                          value="pago"
                          style="cursor: pointer; width: 44px; height: 24px;">
                      </div>
                    </div>
                  </div>
                  
                  <input type="hidden" name="webhook_endpoint" id="webhook_endpoint">
                </div>

                <!-- Botão Salvar -->
                <div class="mt-auto">
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="fa-solid fa-floppy-disk me-2"></i>
                    Salvar Webhook
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Passa os eventos já selecionados pelo usuário para o JavaScript
    const initialSelectedEvents = @json(auth()->user()->webhook_endpoint ?? []);
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const checkboxes = document.querySelectorAll('.event-selector input[type="checkbox"]');
      const hiddenInput = document.getElementById('webhook_endpoint');

      function updateHiddenInput() {
        const selectedValues = Array.from(checkboxes)
          .filter(cb => cb.checked)
          .map(cb => cb.value);
        
        hiddenInput.value = JSON.stringify(selectedValues);
      }

      // Inicializa os checkboxes com base nos valores do backend
      checkboxes.forEach(cb => {
        if (initialSelectedEvents.includes(cb.value)) {
          cb.checked = true;
        }
        // Adiciona o listener para futuras alterações
        cb.addEventListener('change', updateHiddenInput);
      });

      // Define o valor inicial do input escondido
      updateHiddenInput();
    });
  </script>
</x-app-layout>
