<x-app-layout :route="'[ADMIN] Criar transação de entrada'">
  
  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-arrow-down fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Criar Transação de Entrada</h1>
                  <p class="text-muted mb-0">Adicione créditos manualmente para usuários do sistema</p>
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
                <i class="fa-solid fa-plus-circle text-success me-2"></i>
                Nova Transação
              </h5>
            </div>
            <div class="card-body p-4 d-flex flex-column">
              <div class="text-center mb-4">
                <div class="icon-circle bg-success text-white mx-auto mb-3" style="width: 80px; height: 80px;">
                  <i class="fa-solid fa-hand-holding-dollar fa-2x"></i>
                </div>
                <h4 class="fw-bold mb-2">Adicionar Crédito</h4>
                <p class="text-muted mb-0">Clique no botão abaixo para criar uma nova entrada</p>
              </div>

              <div class="mt-auto">
                <button class="btn btn-success btn-lg w-100" 
                    data-bs-toggle="modal" 
                    data-bs-target="#addtask"
                    data-saldo="{{ number_format($saldoliquido, 2, ',', '.') }}">
                  <i class="fa-solid fa-plus-circle me-2"></i>
                  Criar Transação de Entrada
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Card de Instruções -->
        <div class="col-lg-6 col-12 mb-4">
          <div class="card border-info h-100">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-lightbulb me-2"></i>
                Instruções
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
                <i class="fa-solid fa-info-circle fa-lg me-3 mt-1"></i>
                <div>
                  <h6 class="alert-heading mb-2">Como Funciona?</h6>
                  <p class="mb-0 small">
                    Esta funcionalidade permite adicionar créditos diretamente na conta de um usuário. 
                    O saldo será creditado imediatamente após a confirmação.
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
                    <p class="text-muted mb-0 small">Escolha o usuário que receberá o crédito</p>
                  </div>
                </li>
                <li class="mb-3 d-flex align-items-start">
                  <div class="icon-circle bg-primary text-white me-3" style="width: 32px; height: 32px; font-size: 0.875rem;">
                    <span class="fw-bold">2</span>
                  </div>
                  <div>
                    <strong>Informe o Valor</strong>
                    <p class="text-muted mb-0 small">Digite o valor que será creditado</p>
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

  <!-- Modal de Criação de Entrada -->
  <div class="modal fade" id="addtask" tabindex="-1" aria-labelledby="addtaskLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold" id="addtaskLabel">
            <i class="fa-solid fa-plus-circle text-success me-2"></i>
            Nova Transação de Entrada
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <form id="inserir-entrada" method="POST" action="{{ route('admin.transacoes.addentrada') }}">
          @csrf
          <div class="modal-body p-4">
            <div class="row g-3">
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
                  <i class="fa-solid fa-dollar-sign text-success me-2"></i>
                  Valor
                </label>
                <div class="input-group input-group-lg">
                  <span class="input-group-text">
                    <i class="fa-solid fa-brazilian-real-sign"></i>
                  </span>
                  <input type="number" step="0.01" class="form-control" 
                      id="valor-entrada" name="valor" 
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
                <div class="alert alert-info mb-0">
                  <i class="fa-solid fa-info-circle me-2"></i>
                  <strong>Descrição:</strong> Será automaticamente definida como <span class="badge bg-primary">{{ env('APP_NAME') }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fa-solid fa-times me-2"></i>Cancelar
            </button>
            <button type="submit" class="btn btn-success" 
                @if (isset($count) && $count > 0) disabled @endif>
              <i class="fa-solid fa-check me-2"></i>Adicionar Crédito
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      console.log('🔍 [DEBUG] DOMContentLoaded iniciado');
      
      // ======================================================
      // REFERÊNCIAS DOS ELEMENTOS
      // ======================================================
      const modal = document.getElementById('addtask');
      const form = document.getElementById('inserir-entrada');
      // Usar ID único para evitar conflito
      const valorInput = document.getElementById('valor-entrada');
      const valorError = document.getElementById('valorError');
      const userSelect = document.getElementById('user_id');

      console.log('🔍 [DEBUG] Elementos encontrados:', {
        modal: !!modal,
        form: !!form,
        valorInput: !!valorInput,
        valorError: !!valorError,
        userSelect: !!userSelect
      });

      if (valorInput) {
        console.log('🔍 [DEBUG] Elemento valor selecionado:', valorInput);
        console.log('🔍 [DEBUG] Atributos do elemento selecionado:', {
          id: valorInput.id,
          name: valorInput.name,
          type: valorInput.type,
          max: valorInput.max,
          min: valorInput.min,
          step: valorInput.step,
          placeholder: valorInput.placeholder
        });
      }

      // Verificar se há elementos duplicados com mesmo ID
      const todosElementosValor = document.querySelectorAll('#valor');
      console.log('🔍 [DEBUG] Todos os elementos com ID "valor":', todosElementosValor.length);
      todosElementosValor.forEach((el, index) => {
        console.log(`🔍 [DEBUG] Elemento ${index}:`, {
          tagName: el.tagName,
          type: el.type,
          id: el.id,
          name: el.name,
          max: el.max,
          min: el.min,
          step: el.step,
          value: el.value,
          className: el.className
        });
      });

      // ======================================================
      // LOGS DETALHADOS PARA DEBUG DO CAMPO VALOR
      // ======================================================
      if (valorInput) {
        console.log('🔍 [DEBUG] Campo valor encontrado:', valorInput);
        console.log('🔍 [DEBUG] Tipo do campo:', valorInput.type);
        console.log('🔍 [DEBUG] Classes do campo:', valorInput.className);
        console.log('🔍 [DEBUG] Atributos do campo:', {
          id: valorInput.id,
          name: valorInput.name,
          type: valorInput.type,
          step: valorInput.step,
          min: valorInput.min,
          placeholder: valorInput.placeholder,
          required: valorInput.required
        });

        // Log para evento input
        valorInput.addEventListener('input', function(e) {
          console.log('🔍 [DEBUG] INPUT - Valor:', e.target.value, 'Data:', e.data);
          
          // Verificar se o valor mudou após o evento
          setTimeout(() => {
            if (e.target.value !== e.data) {
              console.log('🚨 [ALERTA] Valor alterado externamente! Esperado:', e.data, 'Atual:', e.target.value);
            }
          }, 100);
        });

        // Log para evento keydown
        valorInput.addEventListener('keydown', function(e) {
          console.log('🔍 [DEBUG] KEYDOWN - Tecla:', e.key, 'Valor:', e.target.value);
        });

        // Log para evento keyup
        valorInput.addEventListener('keyup', function(e) {
          console.log('🔍 [DEBUG] KEYUP - Tecla:', e.key, 'Valor:', e.target.value);
        });

        // Log para evento change
        valorInput.addEventListener('change', function(e) {
          console.log('🔍 [DEBUG] Evento CHANGE disparado');
          console.log('🔍 [DEBUG] Valor final:', e.target.value);
        });

        // Log para evento focus
        valorInput.addEventListener('focus', function(e) {
          console.log('🔍 [DEBUG] Campo recebeu foco');
          console.log('🔍 [DEBUG] Valor atual:', e.target.value);
        });

        // Log para evento blur
        valorInput.addEventListener('blur', function(e) {
          console.log('🔍 [DEBUG] Campo perdeu foco');
          console.log('🔍 [DEBUG] Valor final:', e.target.value);
        });

        // Verificar se há máscaras aplicadas
        console.log('🔍 [DEBUG] Verificando máscaras aplicadas...');
        if (typeof Inputmask !== 'undefined') {
          console.log('🔍 [DEBUG] Inputmask está disponível globalmente');
          try {
            const maskData = Inputmask.getData(valorInput);
            console.log('🔍 [DEBUG] Dados da máscara:', maskData);
          } catch (error) {
            console.log('🔍 [DEBUG] Erro ao obter dados da máscara:', error.message);
          }
        } else {
          console.log('🔍 [DEBUG] Inputmask NÃO está disponível');
        }

        if (typeof Cleave !== 'undefined') {
          console.log('🔍 [DEBUG] Cleave está disponível globalmente');
        } else {
          console.log('🔍 [DEBUG] Cleave NÃO está disponível');
        }

        // Proteger o campo contra interferência externa
        console.log('🔍 [DEBUG] Protegendo campo contra interferência externa...');
        
        // Armazenar o valor original para comparação
        let valorOriginal = valorInput.value;
        
        // Monitorar mudanças no valor
        const observer = new MutationObserver(function(mutations) {
          mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
              console.log('🚨 [ALERTA] Valor foi alterado por MutationObserver!');
              console.log('🚨 [ALERTA] Valor anterior:', valorOriginal);
              console.log('🚨 [ALERTA] Valor atual:', valorInput.value);
            }
          });
        });
        
        observer.observe(valorInput, { 
          attributes: true, 
          attributeFilter: ['value'] 
        });
        
        // Remover proteção que estava causando undefined
        console.log('🔍 [DEBUG] Removendo proteção que causava undefined...');
      }

      // ======================================================
      // VALIDAÇÃO DO FORMULÁRIO
      // ======================================================
      if (form) {
        form.addEventListener('submit', function(e) {
          console.log('🔍 [DEBUG] Formulário sendo submetido');
          const valor = parseFloat(valorInput.value);
          console.log('🔍 [DEBUG] Valor para validação:', valor);
          
          // Valida se o valor é positivo
          if (isNaN(valor) || valor <= 0) {
            console.log('🔍 [DEBUG] Valor inválido detectado');
            e.preventDefault();
            valorError.classList.remove('d-none');
            valorInput.focus();
            return false;
          }
          
          // Confirma a operação
          const userName = userSelect.options[userSelect.selectedIndex].text;
          const confirmMsg = `Confirmar adição de R$ ${valor.toFixed(2).replace('.', ',')} para ${userName}?`;
          
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
          console.log('🔍 [DEBUG] Limpando erros - valor:', this.value);
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
          console.log('🔍 [DEBUG] Evento BLUR - formatando valor');
          console.log('🔍 [DEBUG] Valor antes da formatação:', this.value);
          if (this.value) {
            const valor = parseFloat(this.value);
            console.log('🔍 [DEBUG] Valor parseado:', valor);
            if (!isNaN(valor)) {
              this.value = valor.toFixed(2);
              console.log('🔍 [DEBUG] Valor após formatação:', this.value);
            }
          }
        });
      }

      console.log('🔍 [DEBUG] Script de debug carregado completamente');
    });
  </script>

</x-app-layout>