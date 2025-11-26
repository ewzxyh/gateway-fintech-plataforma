@props([
'user'
])

<div class="modal fade glassmorphism-affiliados-modal" id="afiliadosModal-{{ $user->id }}" data-user-name="{{ $user->username }}" tabindex="-1" aria-labelledby="afiliadosModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 900px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="afiliadosModalLabel-{{ $user->id }}">
          <i class="fas fa-user-tag me-2" style="color: #ffa500;"></i>Configurações de Afiliados - {{ $user->username }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="afiliadosForm-{{ $user->id }}">
          @csrf
          @method('PUT')

          <!-- Seção de Configurações de Affiliate -->
          <div class="mb-4">
            <h6 class="text-warning mb-3">
              <i class="fas fa-cog me-2"></i>Configurações do Sistema de Afiliados
            </h6>
            
            <!-- Checkbox para ativar affiliate -->
            <div class="mb-3 row">
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input affiliate-checkbox" 
                      type="checkbox" 
                      id="isAffiliate-{{ $user->id }}" 
                      name="is_affiliate" 
                      value="1"
                      {{ $user->is_affiliate ? 'checked' : '' }}
                      onchange="toggleAffiliateFields({{ $user->id }})">
                  <label class="form-check-label text-light" for="isAffiliate-{{ $user->id }}">
                    <strong>É Affiliate</strong>
                  </label>
                </div>
                <small class="text-muted">Ativa o sistema de comissões para este usuário</small>
              </div>
              
              <!-- Porcentagem do affiliate -->
              <div class="col-md-6">
                <div id="container-affiliate-percentage-{{$user->id}}" {{ !$user->is_affiliate ? 'style=display:none' : '' }}>
                  <label for="affiliatePercentage-{{ $user->id }}" class="form-label">Porcentagem Affiliate (%)</label>
                  <input type="number" 
                      value="{{ $user->affiliate_percentage ?? 0 }}" 
                      class="form-control affiliate-percentage-input" 
                      id="affiliatePercentage-{{ $user->id }}" 
                      name="affiliate_percentage" 
                      min="0" 
                      max="10" 
                      step="0.01"
                      placeholder="Ex: 0.50">
                  <small class="text-info">
                    <i class="fas fa-info-circle"></i> Split automático será criado para este affiliate
                  </small>
                </div>
              </div>
            </div>

            <!-- Código e link do affiliate -->
            <div id="container-affiliate-info-{{$user->id}}" {{ !$user->is_affiliate ? 'style=display:none' : '' }}>
              <div class="mb-3 row">
                <div class="col-md-6">
                  <label class="form-label">Código Affiliate</label>
                  <input type="text" 
                      value="{{ $user->affiliate_code ?? 'Será gerado automaticamente' }}" 
                      class="form-control" 
                      readonly
                      name="affiliate_code"
                      style="background-color: #1a1a1a; border-color: #ffa500;">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Link de Indicação</label>
                  <input type="text" 
                      value="{{ $user->affiliate_link ?? url('/register') . '?ref=' . ($user->affiliate_code ?? 'CODIGO') }}" 
                      class="form-control" 
                      readonly
                      name="affiliate_link"
                      style="background-color: #1a1a1a; border-color: #ffa500;">
                  <button type="button" class="btn btn-sm btn-outline-warning mt-2" onclick="copiarLink({{ $user->id }})">
                    <i class="fas fa-copy"></i> Copiar Link
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Estatísticas do affiliate -->
          <div id="container-affiliate-stats-{{$user->id}}" {{ !$user->is_affiliate ? 'style=display:none' : '' }}>
            <div class="mb-4">
              <h6 class="text-info mb-3">
                <i class="fas fa-chart-line me-2"></i>Estatísticas do Affiliate
              </h6>
              <div class="row">
                <div class="col-md-6">
                  <div class="card bg-dark border-info">
                    <div class="card-body text-center">
                      <i class="fas fa-users fa-2x mb-2 text-info"></i>
                      <h5 class="text-info">{{ $user->clientesAffiliate ? $user->clientesAffiliate->count() : 0 }}</h5>
                      <p class="mb-0">Referidos</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card bg-dark border-success">
                    <div class="card-body text-center">
                      <i class="fas fa-dollar-sign fa-2x mb-2 text-success"></i>
                      <h5 class="text-success">R$ 0,00</h5>
                      <p class="mb-0">Comissões</p>
                    </div>
                  </div>
                </div>
              </div>
              
              @if($user->clientesAffiliate && $user->clientesAffiliate->count() > 0)
                <div class="mt-3">
                  <h6 class="text-light">Referidos:</h6>
                  <div class="table-responsive">
                    <table class="table table-dark table-sm">
                      <thead>
                        <tr>
                          <th>Nome</th>
                          <th>Email</th>
                          <th>Data Cadastro</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($user->clientesAffiliate as $referido)
                          <tr>
                            <td>{{ $referido->username }}</td>
                            <td>{{ $referido->email }}</td>
                            <td>{{ $referido->created_at->format('d/m/Y') }}</td>
                            <td>
                              <span class="badge {{ $referido->status == 1 ? 'bg-success' : 'bg-warning' }}">
                                {{ $referido->status == 1 ? 'Ativo' : 'Pendente' }}
                              </span>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              @else
                <div class="alert alert-info text-center">
                  <i class="fas fa-info-circle"></i> Ainda não tem referidos.
                </div>
              @endif
            </div>
          </div>

          <!-- Botões de ação -->
          <div class="modal-footer gap-2">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i>Fechar
            </button>
            <button type="button" class="btn btn-sm btn-success" onclick="salvarAfiliados({{ $user->id }})">
              <i class="fas fa-save me-1"></i>Salvar Configurações
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Função para alternar campos de affiliate
function toggleAffiliateFields(userId) {
  const affiliateCheckbox = document.getElementById('isAffiliate-' + userId);
  const percentageContainer = document.getElementById('container-affiliate-percentage-' + userId);
  const infoContainer = document.getElementById('container-affiliate-info-' + userId);
  const statsContainer = document.getElementById('container-affiliate-stats-' + userId);
  const percentageInput = document.getElementById('affiliatePercentage-' + userId);
  
  if (affiliateCheckbox.checked) {
    percentageContainer.style.display = 'block';
    infoContainer.style.display = 'block';
    statsContainer.style.display = 'block';
    percentageInput.required = true;
    percentageInput.focus();
  } else {
    percentageContainer.style.display = 'none';
    infoContainer.style.display = 'none';
    statsContainer.style.display = 'none';
    percentageInput.required = false;
  }
}

// Função para copiar link de indicação
function copiarLink(userId) {
  const linkInput = document.querySelector(`input[name="affiliate_link"]`);
  linkInput.select();
  document.execCommand('copy');
  
  // Mostrar feedback visual
  const btn = event.target.closest('button');
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-check"></i> Copiado!';
  btn.classList.remove('btn-outline-warning');
  btn.classList.add('btn-success');
  
  setTimeout(() => {
    btn.innerHTML = originalText;
    btn.classList.remove('btn-success');
    btn.classList.add('btn-outline-warning');
  }, 2000);
}

// Função para salvar configurações de afiliados
function salvarAfiliados(userId) {
  const form = document.getElementById('afiliadosForm-' + userId);

  if (!validarAfiliados(userId)) {
    return;
  }

  const btnSalvar = document.querySelector(`#afiliadosModal-${userId} button[onclick*="salvarAfiliados"]`);
  const btnOriginal = btnSalvar.innerHTML;
  
  // Mostrar estado de carregamento
  btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
  btnSalvar.disabled = true;

  // Preparar dados do formulário
  const formData = new FormData(form);
  
  fetch(`/admin/usuario/${userId}/afiliados`, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Mostrar sucesso
      const modalBody = document.querySelector(`#afiliadosModal-${userId} .modal-body`);
      
      // Resetar botão
      btnSalvar.innerHTML = btnOriginal;
      btnSalvar.disabled = false;
      
      // Mostrar mensagem de sucesso
      showAlert('success', 'Configurações de afiliados salvas com sucesso!', modalBody);
      
    } else {
      throw new Error(data.message || 'Erro ao salvar configurações de afiliados');
    }
  })
  .catch(error => {
    console.error('Erro ao salvar afiliados:', error);
    alert('Erro ao salvar configurações de afiliados. Tente novamente.');
    
    // Resetar botão
    btnSalvar.innerHTML = btnOriginal;
    btnSalvar.disabled = false;
  });
}

// Função para validar configurações de afiliados
function validarAfiliados(userId) {
  const affiliateCheckbox = document.getElementById('isAffiliate-' + userId);
  const percentageInput = document.getElementById('affiliatePercentage-' + userId);
  
  if (affiliateCheckbox.checked) {
    const percentage = parseFloat(percentageInput.value);
    
    if (isNaN(percentage) || percentage < 0 || percentage > 10) {
      alert('Por favor, insira uma porcentagem válida entre 0 e 10.');
      percentageInput.focus();
      return false;
    }
  }
  
  return true;
}

// Função para mostrar alertas
function showAlert(type, message, container) {
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
  alertDiv.innerHTML = `
    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;
  
  container.insertBefore(alertDiv, container.firstChild);
  
  // Remover automaticamente após 5 segundos
  setTimeout(() => {
    if (alertDiv.parentNode) {
      alertDiv.remove();
    }
  }, 5000);
}

// Adicionar listener para aplicar máscaras quando o modal abrir
document.addEventListener('DOMContentLoaded', function() {
  const modals = document.querySelectorAll('[id^="afiliadosModal-"]');
  modals.forEach(modal => {
    modal.addEventListener('shown.bs.modal', function() {
      // Aplicar máscaras aqui se necessário
      const userId = this.id.replace('afiliadosModal-', '');
      
      // Atualizar informações de affiliate se necessário
      // fetch afiliados data aqui se precisar carregar dados adicionais
    });
  });
});
</script>
