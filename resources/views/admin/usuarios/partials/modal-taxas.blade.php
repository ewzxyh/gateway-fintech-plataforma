@props([
  'user'
])


<div class="modal fade glassmorphism-taxas-modal" id="taxasModal-{{ $user->id }}" data-user-name="{{ $user->username }}" tabindex="-1" aria-labelledby="taxasModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="taxasModalLabel-{{ $user->id }}">
          <i class="fas fa-dollar-sign me-2" style="color: #ffc107;"></i>Configuração de Taxas - {{ $user->username }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <!-- Informações do usuário -->
        <div class="taxa-card">
          <div class="row">
            <div class="col-md-6">
              <div class="taxa-title">Usuário:</div>
              <div class="text-white">{{ $user->username }}</div>
            </div>
            <div class="col-md-6">
              <div class="taxa-title">CPF/CNPJ:</div>
              <div class="text-white">{{ formatarCpfOuCnpj($user->cpf_cnpj) }}</div>
            </div>
          </div>
        </div>

        <form id="taxasForm-{{ $user->id }}">
          @csrf
          
          <!-- Configurações de Depósito -->
          <div class="taxa-card">
            <div class="taxa-title mb-3">
              <i class="fas fa-arrow-down me-2" style="color: #28a745;"></i>Configurações de Depósito
            </div>
            
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="taxa_percentual_deposito-{{ $user->id }}" class="form-label">
                  <i class="fas fa-percentage me-2"></i>Taxa Percentual Depósito (%)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="taxa_percentual_deposito-{{ $user->id }}" 
                    name="taxa_percentual_deposito" 
                    step="0.01" 
                    min="0" 
                    max="100"
                    placeholder="2.00"
                    value="2.00">
                <small class="">Taxa percentual aplicada sobre o valor do depósito</small>
              </div>
              
              <div class="col-md-4 mb-3">
                <label for="taxa_fixa_deposito-{{ $user->id }}" class="form-label">
                  <i class="fas fa-coins me-2"></i>Taxa Fixa Depósito (R$)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="taxa_fixa_deposito-{{ $user->id }}" 
                    name="taxa_fixa_deposito" 
                    step="0.01" 
                    min="0"
                    placeholder="0.50"
                    value="0.50">
                <small class="">Taxa fixa aplicada em todos os depósitos</small>
              </div>
              
              <div class="col-md-4 mb-3">
                <label for="valor_minimo_deposito-{{ $user->id }}" class="form-label">
                  <i class="fas fa-arrow-up me-2"></i>Valor Mínimo de Depósito (R$)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="valor_minimo_deposito-{{ $user->id }}" 
                    name="valor_minimo_deposito" 
                    step="0.01" 
                    min="0"
                    placeholder="1.00"
                    value="1.00">
                <small class="">Valor mínimo que o usuário pode depositar</small>
              </div>
            </div>
          </div>

          <!-- Configurações de Saque PIX -->
          <div class="taxa-card">
            <div class="taxa-title mb-3">
              <i class="fas fa-arrow-up me-2" style="color: #dc3545;"></i>Configurações de Saque
            </div>
            
            <!-- Explicação do cálculo -->
            <div class="alert alert-info mb-3" style="background: rgba(23, 162, 184, 0.2); border: 1px solid rgba(23, 162, 184, 0.3);">
              <h6><i class="fas fa-info-circle me-2"></i>Como funciona o cálculo de taxa de saque PIX:</h6>
              <ul class="mb-0 small">
                <li><strong>Taxa percentual PIX:</strong> 2.00% do valor</li>
                <li><strong>Taxa mínima PIX:</strong> R$ 0.80</li>
                <li><strong>Taxa fixa PIX:</strong> R$ 0.20 (sempre somada)</li>
                <li><strong>Taxa aplicada:</strong> Maior valor entre taxa percentual e taxa mínima + taxa fixa</li>
                <li><strong>Exemplo:</strong> R$ 2,00 → 2% = R$ 0,04 < R$ 0,80 → Taxa = R$ 0,80 + R$ 0.20 = R$ 1,00</li>
              </ul>
            </div>
            
            <div class="row">
              <div class="col-md-3 mb-3">
                <label for="taxa_percentual_pix-{{ $user->id }}" class="form-label">
                  <i class="fas fa-percentage me-2"></i>Taxa Percentual PIX (%)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="taxa_percentual_pix-{{ $user->id }}" 
                    name="taxa_percentual_pix" 
                    step="0.01" 
                    min="0" 
                    max="100"
                    placeholder="2.00"
                    value="2.00">
                <small class="">Taxa percentual aplicada sobre o valor do saque PIX</small>
              </div>
              
              <div class="col-md-3 mb-3">
                <label for="taxa_minima_pix-{{ $user->id }}" class="form-label">
                  <i class="fas fa-arrow-down me-2"></i>Taxa Mínima PIX (R$)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="taxa_minima_pix-{{ $user->id }}" 
                    name="taxa_minima_pix" 
                    step="0.01" 
                    min="0"
                    placeholder="0.80"
                    value="0.80">
                <small class="">Valor mínimo de taxa para saques PIX</small>
              </div>
              
              <div class="col-md-3 mb-3">
                <label for="taxa_fixa_pix-{{ $user->id }}" class="form-label">
                  <i class="fas fa-plus me-2"></i>Taxa Fixa PIX (R$)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="taxa_fixa_pix-{{ $user->id }}" 
                    name="taxa_fixa_pix" 
                    step="0.01" 
                    min="0"
                    placeholder="0.20"
                    value="0.20">
                <small class="">Taxa fixa adicional aplicada sobre saques PIX</small>
              </div>
              
              <div class="col-md-3 mb-3">
                <label for="valor_minimo_saque-{{ $user->id }}" class="form-label">
                  <i class="fas fa-arrow-up me-2"></i>Valor Mínimo de Saque (R$)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="valor_minimo_saque-{{ $user->id }}" 
                    name="valor_minimo_saque" 
                    step="0.01" 
                    min="0"
                    placeholder="1.00"
                    value="1.00">
                <small class="">Valor mínimo que o usuário pode sacar</small>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="limite_mensal_pf-{{ $user->id }}" class="form-label">
                  <i class="fas fa-calendar-alt me-2"></i>Limite Mensal Pessoa Física (R$)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="limite_mensal_pf-{{ $user->id }}" 
                    name="limite_mensal_pf" 
                    step="0.01" 
                    min="0"
                    placeholder="50000.00"
                    value="50000.00">
                <small class="">Limite máximo de saques por mês para pessoa física</small>
              </div>
              
              <div class="col-md-4 mb-3">
                <label for="taxa_saque_api-{{ $user->id }}" class="form-label">
                  <i class="fas fa-code me-2"></i>Taxa Saque via API (%)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="taxa_saque_api-{{ $user->id }}" 
                    name="taxa_saque_api" 
                    step="0.01" 
                    min="0" 
                    max="100"
                    placeholder="2.00"
                    value="2.00">
                <small class="">Taxa percentual para saques realizados via API externa</small>
              </div>
              
              <div class="col-md-4 mb-3">
                <label for="taxa_saque_crypto-{{ $user->id }}" class="form-label">
                  <i class="fab fa-bitcoin me-2"></i>Taxa Saque Criptomoedas (%)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="taxa_saque_crypto-{{ $user->id }}" 
                    name="taxa_saque_crypto" 
                    step="0.01" 
                    min="0" 
                    max="100"
                    placeholder="2.00"
                    value="2.00">
                <small class="">Taxa percentual para saques em criptomoedas</small>
              </div>
            </div>
          </div>

          <!-- Sistema de Taxas Flexível para Depósitos -->
          <div class="taxa-card">
            <div class="taxa-title mb-3">
              <i class="fas fa-sliders-h me-2" style="color: #6f42c1;"></i>Sistema de Taxas Flexível para Depósitos
            </div>
            
            <div class="row mb-3">
              <div class="col-12">
                <div class="form-check form-switch">
                  <input class="form-check-input" 
                      type="checkbox" 
                      id="sistema_flexivel_ativo-{{ $user->id }}" 
                      name="sistema_flexivel_ativo"
                      value="1">
                  <label class="form-check-label text-white" for="sistema_flexivel_ativo-{{ $user->id }}">
                    <strong>Ativar Sistema de Taxas Flexível</strong>
                  </label>
                </div>
                <small class="">Quando ativado, o sistema aplicará taxas diferentes baseadas no valor do depósito</small>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="valor_minimo_flexivel-{{ $user->id }}" class="form-label">
                  <i class="fas fa-arrow-down me-2"></i>Valor Mínimo (R$)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="valor_minimo_flexivel-{{ $user->id }}" 
                    name="valor_minimo_flexivel" 
                    step="0.01" 
                    min="0"
                    placeholder="15.00"
                    value="15.00">
                <small class="">Depósitos abaixo deste valor terão taxa fixa</small>
              </div>
              
              <div class="col-md-4 mb-3">
                <label for="taxa_fixa_baixos-{{ $user->id }}" class="form-label">
                  <i class="fas fa-coins me-2"></i>Taxa Fixa para Valores Baixos (R$)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="taxa_fixa_baixos-{{ $user->id }}" 
                    name="taxa_fixa_baixos" 
                    step="0.01" 
                    min="0"
                    placeholder="0.80"
                    value="0.80">
                <small class="">Taxa fixa para depósitos abaixo do valor mínimo</small>
              </div>
              
              <div class="col-md-4 mb-3">
                <label for="taxa_percentual_altos-{{ $user->id }}" class="form-label">
                  <i class="fas fa-percentage me-2"></i>Taxa Percentual para Valores Altos (%)
                </label>
                <input type="number" 
                    class="form-control" 
                    id="taxa_percentual_altos-{{ $user->id }}" 
                    name="taxa_percentual_altos" 
                    step="0.01" 
                    min="0" 
                    max="100"
                    placeholder="2.00"
                    value="2.00">
                <small class="">Taxa percentual para depósitos acima do valor mínimo</small>
              </div>
            </div>
            
            <div class="alert alert-warning" style="background: rgba(255, 193, 7, 0.2); border: 1px solid rgba(255, 193, 7, 0.3);">
              <h6><i class="fas fa-lightbulb me-2"></i>Como funciona:</h6>
              <ul class="mb-0 small">
                <li><strong>Depósitos abaixo de R$ 15.00:</strong> Taxa fixa de R$ 0.80</li>
                <li><strong>Depósitos acima de R$ 15.00:</strong> Taxa percentual de 2.00%</li>
              </ul>
            </div>
          </div>

          <!-- Observações -->
          <div class="taxa-card">
            <div class="taxa-title mb-3">
              <i class="fas fa-sticky-note me-2"></i>Observações
            </div>
            <div class="row">
              <div class="col-12">
                <textarea class="form-control" 
                     id="observacoes-{{ $user->id }}" 
                     name="observacoes" 
                     rows="3" 
                     placeholder="Observações sobre as taxas configuradas..."></textarea>
              </div>
            </div>
          </div>

          <!-- Histórico de alterações -->
          <div class="taxa-card">
            <div class="taxa-title mb-3">
              <i class="fas fa-history me-2"></i>Histórico de Alterações
            </div>
            <div class="text-center py-3">
              <i class="fas fa-info-circle me-2"></i>
              Nenhuma alteração registrada ainda.
            </div>
          </div>
        </form>
      </div>
      
      <div class="modal-footer gap-2">
        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i>Cancelar
        </button>
        <button type="button" class="btn btn-sm btn-info" onclick="desativarTaxasPersonalizadas({{ $user->id }})" id="btn-desativar-{{ $user->id }}" style="display: none;">
          <i class="fas fa-globe me-1"></i>Usar Taxas Globais
        </button>
        <button type="button" class="btn btn-sm btn-success" onclick="salvarTaxas({{ $user->id }})">
          <i class="fas fa-save me-1"></i>Salvar Taxas
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Carregar taxas do usuário quando o modal abrir
function carregarTaxas(userId) {
  fetch(`/admin/usuario/${userId}/taxas`, {
    method: 'GET',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      preencherFormulario(userId, data.taxas);
      atualizarIndicadorPersonalizado(userId, data.taxas.usando_personalizadas);
    } else {
      console.error('Erro ao carregar taxas:', data.message);
    }
  })
  .catch(error => {
    console.error('Erro na requisição:', error);
  });
}

// Preencher formulário com as taxas carregadas
function preencherFormulario(userId, taxas) {
  const form = document.getElementById('taxasForm-' + userId);
  
  // Configurações de Depósito
  if (taxas.configuracoes_deposito) {
    setValue('taxa_percentual_deposito', taxas.configuracoes_deposito.taxa_percentual_deposito);
    setValue('taxa_fixa_deposito', taxas.configuracoes_deposito.taxa_fixa_deposito);
    setValue('valor_minimo_deposito', taxas.configuracoes_deposito.valor_minimo_deposito);
  }
  
  // Configurações de Saque
  if (taxas.configuracoes_saque) {
    setValue('taxa_percentual_pix', taxas.configuracoes_saque.taxa_percentual_pix);
    setValue('taxa_minima_pix', taxas.configuracoes_saque.taxa_minima_pix);
    setValue('taxa_fixa_pix', taxas.configuracoes_saque.taxa_fixa_pix);
    setValue('valor_minimo_saque', taxas.configuracoes_saque.valor_minimo_saque);
    setValue('limite_mensal_pf', taxas.configuracoes_saque.limite_mensal_pf);
    setValue('taxa_saque_api', taxas.configuracoes_saque.taxa_saque_api);
    setValue('taxa_saque_crypto', taxas.configuracoes_saque.taxa_saque_crypto);
  }
  
  // Sistema Flexível
  if (taxas.sistema_flexivel) {
    setValue('sistema_flexivel_ativo', taxas.sistema_flexivel.sistema_flexivel_ativo);
    setValue('valor_minimo_flexivel', taxas.sistema_flexivel.valor_minimo_flexivel);
    setValue('taxa_fixa_baixos', taxas.sistema_flexivel.taxa_fixa_baixos);
    setValue('taxa_percentual_altos', taxas.sistema_flexivel.taxa_percentual_altos);
  }
  
  // Observações
  setValue('observacoes', taxas.observacoes || '');
  
  function setValue(fieldName, value) {
    const field = form.querySelector(`[name="${fieldName}"]`);
    if (field) {
      if (field.type === 'checkbox') {
        field.checked = Boolean(value);
      } else {
        field.value = value || '';
      }
    }
  }
}

// Atualizar indicador visual de taxas personalizadas
function atualizarIndicadorPersonalizado(userId, usandoPersonalizadas) {
  const modal = document.getElementById('taxasModal-' + userId);
  const titulo = modal.querySelector('.modal-title');
  const btnDesativar = document.getElementById('btn-desativar-' + userId);
  
  // Obter o nome do usuário do atributo data ou do título original
  let nomeUsuario = modal.getAttribute('data-user-name');
  
  if (!nomeUsuario) {
    // Tentar extrair do título original
    const textoOriginal = titulo.textContent || titulo.innerText || '';
    const match = textoOriginal.match(/Configuração de Taxas - ([^-]+?)(?:\s+(?:PERSONALIZADAS|GLOBAIS).*)?$/);
    if (match && match[1]) {
      nomeUsuario = match[1].trim();
    } else {
      // Fallback: usar nome padrão
      nomeUsuario = 'Usuário';
    }
  }
  
  if (usandoPersonalizadas) {
    titulo.innerHTML = `<i class="fas fa-dollar-sign me-2" style="color: #ffc107;"></i>Configuração de Taxas - ${nomeUsuario} <span class="badge bg-warning ms-2">PERSONALIZADAS</span>`;
    if (btnDesativar) btnDesativar.style.display = 'inline-block';
  } else {
    titulo.innerHTML = `<i class="fas fa-dollar-sign me-2" style="color: #ffc107;"></i>Configuração de Taxas - ${nomeUsuario} <span class="badge bg-info ms-2">GLOBAIS</span>`;
    if (btnDesativar) btnDesativar.style.display = 'none';
  }
}

// Desativar taxas personalizadas
function desativarTaxasPersonalizadas(userId) {
  if (!confirm('Tem certeza que deseja desativar as taxas personalizadas? O usuário voltará a usar as taxas globais.')) {
    return;
  }
  
  const btnDesativar = document.getElementById('btn-desativar-' + userId);
  const textoOriginal = btnDesativar.innerHTML;
  btnDesativar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Desativando...';
  btnDesativar.disabled = true;
  
  fetch(`/admin/usuario/${userId}/taxas`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Mostrar sucesso
      const alertDiv = document.createElement('div');
      alertDiv.className = 'alert alert-info alert-dismissible fade show';
      alertDiv.innerHTML = `
        <i class="fas fa-info-circle me-2"></i>${data.message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      
      const modalBody = document.querySelector(`#taxasModal-${userId} .modal-body`);
      modalBody.insertBefore(alertDiv, modalBody.firstChild);
      
      // Atualizar indicador e recarregar taxas
      atualizarIndicadorPersonalizado(userId, false);
      carregarTaxas(userId);
      
      // Remover alerta após 5 segundos
      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.remove();
        }
      }, 5000);
      
    } else {
      alert('Erro: ' + (data.message || 'Erro desconhecido'));
    }
  })
  .catch(error => {
    console.error('Erro na requisição:', error);
    alert('Erro ao desativar taxas personalizadas. Tente novamente.');
  })
  .finally(() => {
    // Restaurar botão
    btnDesativar.innerHTML = textoOriginal;
    btnDesativar.disabled = false;
  });
}

function salvarTaxas(userId) {
  const form = document.getElementById('taxasForm-' + userId);
  const formData = new FormData(form);
  
  // Validações básicas
  if (!validarTaxas(userId)) {
    return;
  }
  
  // Mostrar loading
  const btnSalvar = document.querySelector(`button[onclick="salvarTaxas(${userId})"]`);
  const textoOriginal = btnSalvar.innerHTML;
  btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
  btnSalvar.disabled = true;
  
  // Fazer requisição AJAX real
  fetch(`/admin/usuario/${userId}/taxas`, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Mostrar sucesso
      const alertDiv = document.createElement('div');
      alertDiv.className = 'alert alert-success alert-dismissible fade show';
      alertDiv.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>${data.message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      
      const modalBody = document.querySelector(`#taxasModal-${userId} .modal-body`);
      modalBody.insertBefore(alertDiv, modalBody.firstChild);
      
      // Atualizar indicador
      atualizarIndicadorPersonalizado(userId, data.usando_personalizadas);
      
      // Remover alerta após 5 segundos
      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.remove();
        }
      }, 5000);
      
    } else {
      // Mostrar erro
      alert('Erro: ' + (data.message || 'Erro desconhecido'));
    }
  })
  .catch(error => {
    console.error('Erro na requisição:', error);
    alert('Erro ao salvar taxas. Tente novamente.');
  })
  .finally(() => {
    // Restaurar botão
    btnSalvar.innerHTML = textoOriginal;
    btnSalvar.disabled = false;
  });
}

function validarTaxas(userId) {
  const form = document.getElementById('taxasForm-' + userId);
  const inputs = form.querySelectorAll('input[type="number"]');
  let valido = true;
  
  inputs.forEach(input => {
    const valor = parseFloat(input.value);
    const min = parseFloat(input.getAttribute('min'));
    const max = parseFloat(input.getAttribute('max'));
    
    if (isNaN(valor) || valor < min || (max && valor > max)) {
      input.classList.add('is-invalid');
      valido = false;
    } else {
      input.classList.remove('is-invalid');
    }
  });
  
  if (!valido) {
    alert('Por favor, verifique os valores inseridos. Todos os campos numéricos devem ter valores válidos.');
  }
  
  return valido;
}


// Inicialização do modal
document.addEventListener('DOMContentLoaded', function() {
  // Limpar qualquer campo de exemplo que possa ter sido adicionado dinamicamente
  const camposExemplo = document.querySelectorAll('[id^="valor_exemplo_saque-"], [id^="resultado_calculo-"]');
  camposExemplo.forEach(campo => {
    const row = campo.closest('.row');
    if (row && row.querySelector('[id^="valor_exemplo_saque-"]')) {
      row.remove();
    }
  });
  
  // Adicionar listener para carregar taxas quando o modal abrir
  const modals = document.querySelectorAll('[id^="taxasModal-"]');
  modals.forEach(modal => {
    modal.addEventListener('show.bs.modal', function() {
      const userId = this.id.replace('taxasModal-', '');
      console.log('Modal aberto para usuário:', userId);
      carregarTaxas(userId);
    });
    
    // Limpar título quando o modal for fechado para evitar acúmulo
    modal.addEventListener('hidden.bs.modal', function() {
      const titulo = this.querySelector('.modal-title');
      const nomeUsuario = this.getAttribute('data-user-name');
      if (titulo && nomeUsuario) {
        titulo.innerHTML = `<i class="fas fa-dollar-sign me-2" style="color: #ffc107;"></i>Configuração de Taxas - ${nomeUsuario}`;
      }
    });
  });
});
</script>
