<x-app-layout :route="'[ADMIN] Criar transação de saída'">
  
  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-arrow-up fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Criar Transação de Saída</h1>
                  <p class="text-muted mb-0">Retire créditos manualmente da conta de usuários do sistema</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Card de Criação -->
        <div class="col-lg-6 col-12 mb-4">
          <div class="card h-100">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="fa-solid fa-minus-circle text-danger me-2"></i>
                Nova Transação
              </h5>
            </div>
            <div class="card-body p-4 d-flex flex-column">
              <div class="text-center mb-4">
                <div class="icon-circle bg-danger text-white mx-auto mb-3" style="width: 80px; height: 80px;">
                  <i class="fa-solid fa-hand-holding-dollar fa-2x"></i>
                </div>
                <h4 class="fw-bold mb-2">Retirar Crédito</h4>
                <p class="text-muted mb-0">Clique no botão abaixo para criar uma nova saída</p>
              </div>

              <div class="mt-auto">
                <button class="btn btn-danger btn-lg w-100" 
                    data-bs-toggle="modal" 
                    data-bs-target="#addtask"
                    data-saldo="{{ number_format($saldoliquido, 2, ',', '.') }}">
                  <i class="fa-solid fa-minus-circle me-2"></i>
                  Criar Transação de Saída
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Card de Instruções -->
        <div class="col-lg-6 col-12 mb-4">
          <div class="card border-warning h-100">
            <div class="card-header bg-warning text-dark">
              <h5 class="mb-0">
                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                Instruções
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="alert alert-warning d-flex align-items-start mb-4" role="alert">
                <i class="fa-solid fa-info-circle fa-lg me-3 mt-1"></i>
                <div>
                  <h6 class="alert-heading mb-2">Atenção!</h6>
                  <p class="mb-0 small">
                    Esta funcionalidade permite retirar créditos diretamente da conta de um usuário. 
                    O saldo será debitado imediatamente após a confirmação.
                  </p>
                </div>
              </div>

              <h6 class="fw-bold mb-3">
                <i class="fa-solid fa-list-check text-primary me-2"></i>
                Passo a Passo
              </h6>
              <ul class="list-unstyled mb-0">
                <li class="mb-3 d-flex align-items-start">
                  <div class="icon-circle bg-primary text-white me-3" style="width: 32px; height: 32px; font-size: 0.875rem;">
                    <span class="fw-bold">1</span>
                  </div>
                  <div>
                    <strong>Selecione o Usuário</strong>
                    <p class="text-muted mb-0 small">Escolha o usuário que terá o crédito retirado</p>
                  </div>
                </li>
                <li class="mb-3 d-flex align-items-start">
                  <div class="icon-circle bg-primary text-white me-3" style="width: 32px; height: 32px; font-size: 0.875rem;">
                    <span class="fw-bold">2</span>
                  </div>
                  <div>
                    <strong>Informe o Valor</strong>
                    <p class="text-muted mb-0 small">Digite o valor que será debitado</p>
                  </div>
                </li>
                <li class="d-flex align-items-start">
                  <div class="icon-circle bg-primary text-white me-3" style="width: 32px; height: 32px; font-size: 0.875rem;">
                    <span class="fw-bold">3</span>
                  </div>
                  <div>
                    <strong>Confirme a Operação</strong>
                    <p class="text-muted mb-0 small">A descrição será automaticamente definida como <span class="badge bg-secondary">{{ env('APP_NAME') }}</span></p>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Modal de Criação de Saída -->
  <div class="modal fade" id="addtask" tabindex="-1" aria-labelledby="addtaskLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold" id="addtaskLabel">
            <i class="fa-solid fa-minus-circle text-danger me-2"></i>
            Nova Transação de Saída
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <form id="inserir-entrada" method="POST" action="{{ route('admin.transacoes.addsaida') }}">
          @csrf
          <div class="modal-body p-4">
            <div class="row g-3">
              <!-- Alert de Saldo Baixo -->
              @if (isset($saldoBaixo) && $saldoBaixo)
              <div class="col-12">
                <div class="alert alert-danger d-flex align-items-center">
                  <i class="fa-solid fa-exclamation-triangle fa-lg me-3"></i>
                  <div>
                    <strong>Atenção!</strong> Saldo muito baixo para realizar um saque.
                  </div>
                </div>
              </div>
              @endif

              <!-- Selecionar Usuário -->
              <div class="col-12">
                <label for="user_id" class="form-label fw-semibold">
                  <i class="fa-solid fa-user text-primary me-2"></i>
                  Selecionar Usuário
                </label>
                <select class="form-select form-select-lg" id="user_id" name="user_id" required>
                  <option value="">Selecione um usuário</option>
                  @foreach ($users as $user)
                    <option value="{{ $user->user_id }}">
                      {{ $user->username }} | {{ $user->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <!-- Valor -->
              <div class="col-12">
                <label for="valor" class="form-label fw-semibold">
                  <i class="fa-solid fa-dollar-sign text-danger me-2"></i>
                  Valor
                </label>
                <div class="input-group input-group-lg">
                  <span class="input-group-text">
                    <i class="fa-solid fa-brazilian-real-sign"></i>
                  </span>
                  <input type="number" step="0.01" class="form-control" 
                      id="valor-saida" name="valor" 
                      placeholder="0,00" 
                      min="0.01"
                      required>
                </div>
                <div id="valorError" class="mt-2 alert alert-danger d-none">
                  <i class="fa-solid fa-exclamation-triangle me-2"></i>
                  Saldo insuficiente para o valor solicitado.
                </div>
              </div>

              <!-- Informação sobre descrição -->
              <div class="col-12">
                <div class="alert alert-warning mb-0">
                  <i class="fa-solid fa-exclamation-triangle me-2"></i>
                  <strong>Atenção:</strong> O valor será debitado imediatamente. A descrição será definida como <span class="badge bg-dark">{{ env('APP_NAME') }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fa-solid fa-times me-2"></i>Cancelar
            </button>
            <button type="submit" class="btn btn-danger" 
                @if (isset($count) && $count > 0) disabled @endif>
              <i class="fa-solid fa-check me-2"></i>Retirar Crédito
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // ======================================================
      // REFERÊNCIAS DOS ELEMENTOS
      // ======================================================
      const modal = document.getElementById('addtask');
      const form = document.getElementById('inserir-entrada');
      const valorInput = document.getElementById('valor-saida');
      const valorError = document.getElementById('valorError');
      const userSelect = document.getElementById('user_id');

      // ======================================================
      // VALIDAÇÃO DO FORMULÁRIO
      // ======================================================
      if (form) {
        form.addEventListener('submit', function(e) {
          const valor = parseFloat(valorInput.value);
          
          // Valida se o valor é positivo
          if (isNaN(valor) || valor <= 0) {
            e.preventDefault();
            valorError.classList.remove('d-none');
            valorInput.focus();
            return false;
          }
          
          // Confirma a operação (mais crítico em saídas)
          const userName = userSelect.options[userSelect.selectedIndex].text;
          const confirmMsg = `⚠️ ATENÇÃO!\n\nConfirmar RETIRADA de R$ ${valor.toFixed(2).replace('.', ',')} de ${userName}?\n\nEsta ação não pode ser desfeita automaticamente.`;
          
          if (!confirm(confirmMsg)) {
            e.preventDefault();
            return false;
          }
        });
      }

      // ======================================================
      // LIMPAR ERROS AO DIGITAR
      // ======================================================
      if (valorInput) {
        valorInput.addEventListener('input', function() {
          if (this.value && parseFloat(this.value) > 0) {
            valorError.classList.add('d-none');
          }
        });
      }

      // ======================================================
      // RESET DO FORMULÁRIO AO FECHAR O MODAL
      // ======================================================
      if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
          if (form) {
            form.reset();
          }
          if (valorError) {
            valorError.classList.add('d-none');
          }
        });
      }

      // ======================================================
      // FORMATAÇÃO DO CAMPO DE VALOR
      // ======================================================
      if (valorInput) {
        valorInput.addEventListener('blur', function() {
          if (this.value) {
            const valor = parseFloat(this.value);
            if (!isNaN(valor)) {
              this.value = valor.toFixed(2);
            }
          }
        });
      }
    });
  </script>

</x-app-layout>