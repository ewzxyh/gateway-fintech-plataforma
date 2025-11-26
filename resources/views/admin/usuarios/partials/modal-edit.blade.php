@props([
'user',
'gerentes'
])


<div class="modal fade glassmorphism-edit-modal" id="editModal-{{ $user->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-lg-down modal-dialog-centered" style="max-width: 1400px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel-{{ $user->id }}">Editar Usuário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form method="POST" action="{{ route('admin.usuarios.edit', ['id' => $user->id]) }}">
          @csrf
          @method('PUT')
          <input type="hidden" id="editUserId" name="id" value="{{ $user->id }}">
          
          <!-- Dados Pessoais -->
          <div class="mb-4">
            <h6 class="text-primary mb-3 border-bottom pb-2">
              <i class="fas fa-user me-2"></i>Dados Pessoais
            </h6>
            <div class="row g-3">
              <div class="col-lg-6 col-md-12">
                <label for="editNome-{{ $user->id }}" class="form-label">Nome</label>
                <input type="text" value="{{ $user->name }}" class="form-control" id="editNome-{{ $user->id }}" name="name">
              </div>
              <div class="col-lg-3 col-md-6">
                <label for="editCpf-{{ $user->cpf_cnpj }}" class="form-label">CPF/CNPJ</label>
                <input type="text" value="{{ $user->cpf_cnpj }}" class="form-control" id="editCpf-{{ $user->cpf_cnpj }}" name="cpf_cnpj">
              </div>
              <div class="col-lg-3 col-md-6">
                <label for="editTelefone-{{ $user->telefone }}" class="form-label">Telefone</label>
                <input type="text" value="{{ $user->telefone }}" class="form-control" id="editTelefone-{{ $user->telefone }}" name="telefone">
              </div>
              <div class="col-lg-4 col-md-6">
                <label for="editDn-{{ $user->data_nascimento }}" class="form-label">Data de Nascimento</label>
                <input type="date" value="{{ $user->data_nascimento }}" class="form-control" id="editDn-{{ $user->data_nascimento }}" name="data_nascimento">
              </div>
              <div class="col-lg-4 col-md-6">
                <label for="editEmail-{{ $user->id }}" class="form-label">Email</label>
                <input type="email" value="{{ $user->email }}" class="form-control" id="editEmail-{{ $user->id }}" name="email">
              </div>
              <div class="col-lg-4 col-md-12">
                <label for="editPermission-{{ $user->id }}" class="form-label">Permissão</label>
                <select
                  onchange="onChangePermission(this, '{{$user->id}}')"
                  class="form-select"
                  value="{{ $user->permission }}"
                  id="editPermission-{{ $user->id }}"
                  data-permission-select
                  data-permission-id="{{ $user->id }}"
                  name="permission">
                  <option value="3" {{ (int) $user->permission == 3 ?"selected" :"" }}>Administrador</option>
                  <option value="5" {{ (int) $user->permission == 5 ?"selected" :"" }}>Gerente</option>
                  <option value="1" {{ (int) $user->permission == 1 ?"selected" :"" }}>Usuário</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Configurações de Gerente -->
          <div class="mb-4 hidden" id="container-gerente-config-{{$user->id}}">
            <h6 class="text-primary mb-3 border-bottom pb-2">
              <i class="fas fa-user-tie me-2"></i>Configurações de Gerente
            </h6>
            <div class="row g-3">
              <div class="col-lg-6 col-md-12 hidden" id="container-percentage-{{$user->id}}">
                <label for="editPorcentagem-{{ $user->id }}" class="form-label">Porcentagem</label>
                <input type="text" value="{{ $user->gerente_percentage }}" class="form-control" id="editPorcentagem-{{ $user->id }}" name="gerente_percentage">
                <small class="text-info">
                  <i class="fas fa-info-circle"></i> Split automático será criado para este gerente
                </small>
              </div>
              
              <div class="col-lg-6 col-md-12 hidden" id="container-gerenteaprovar-{{$user->id}}">
                <label class="form-label">Permissões</label>
                <div class="form-check form-switch">
                  <input
                    class="form-check-input"
                    name="gerente_aprovar"
                    type="checkbox"
                    role="switch"
                    id="switchCheckDefault-{{$user->id}}"
                    value="{{ $user->gerente_aprovar }}"
                    {{ ($user->gerente_aprovar ?? false) ? 'checked' : '' }}>
                  <label class="form-check-label" for="switchCheckDefault-{{$user->id}}">
                    Pode aprovar transações
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Vínculos e Gerenciamento -->
          <div class="mb-4">
            <h6 class="text-primary mb-3 border-bottom pb-2">
              <i class="fas fa-link me-2"></i>Vínculos e Gerenciamento
            </h6>
            <div class="row g-3">
              <div class="col-lg-6 col-md-12" id="container-gerente-{{$user->id}}">
                <label for="editGerente-{{ $user->id }}" class="form-label">Gerente Responsável</label>
                <select class="form-select" id="editGerente-{{ $user->id }}" name="gerente_id">
                  <option value="" {{ $user->gerente_id == NULL ?"selected" :"" }}>Nenhum</option>
                  @foreach ($gerentes as $gerente)
                  <option value="{{ $gerente->id }}" {{ $user->gerente_id == $gerente->id ?"selected" :"" }}>{{ $gerente->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <!-- Credenciais de API -->
          <div class="mb-4" id="container-token-secret-{{$user->id}}">
            <h6 class="text-primary mb-3 border-bottom pb-2">
              <i class="fas fa-key me-2"></i>Credenciais de API
            </h6>
            <div class="row g-3">
              <div class="col-lg-6 col-md-12" id="container-token-{{$user->id}}">
                <label for="e-token-{{ $user->id }}" class="form-label">Token</label>
                <div class="input-group">
                  <input type="text" class="form-control" value="{{ $user->chaves->token ?? '' }}" name="token" id="e-token-{{ $user->id }}" readonly>
                  <button class="btn btn-outline-primary" onclick="gerarChaveToken('{{ $user->id }}')" type="button">
                    <i class="fa-solid fa-sync"></i>
                  </button>
                </div>
              </div>
              
              <div class="col-lg-6 col-md-12" id="container-secret-{{$user->id}}">
                <label for="e-secret-{{ $user->id }}" class="form-label">Secret</label>
                <div class="input-group">
                  <input type="text" class="form-control" value="{{ $user->chaves->secret ?? '' }}" name="secret" id="e-secret-{{ $user->id }}" readonly>
                  <button class="btn btn-outline-primary" onclick="gerarChaveSecret('{{ $user->id }}')" type="button">
                    <i class="fa-solid fa-sync"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Adquirentes -->
          <div class="mb-4">
            <h6 class="text-primary mb-3 border-bottom pb-2">
              <i class="fas fa-building me-2"></i>Adquirentes
            </h6>
            <div class="row g-3">
              <div class="col-lg-6 col-md-12">
                <label for="editAdquirentePix-{{ $user->id }}" class="form-label">
                  <i class="fa-brands fa-pix text-success"></i> Adquirente PIX Específica
                </label>
                <select class="form-select" id="editAdquirentePix-{{ $user->id }}" name="preferred_adquirente">
                  <option value="">Usar adquirente PIX padrão do sistema</option>
                  @foreach ($adquirentes->where('status', 1) as $adquirente)
                    <option value="{{ $adquirente->referencia }}" 
                      {{ $user->preferred_adquirente == $adquirente->referencia ? 'selected' : '' }}>
                      {{ $adquirente->adquirente }}
                      @if($adquirente->is_default)
                        <span class="badge bg-success ms-1">Padrão PIX</span>
                      @endif
                    </option>
                  @endforeach
                </select>
                <small class="form-text text-muted">
                  Adquirente usada para pagamentos via PIX
                </small>
              </div>

              <div class="col-lg-6 col-md-12">
                <label for="editAdquirenteCard-{{ $user->id }}" class="form-label">
                  <i class="fa-solid fa-credit-card text-primary"></i> Adquirente Cartão+Boleto Específica
                </label>
                <select class="form-select" id="editAdquirenteCard-{{ $user->id }}" name="preferred_adquirente_card_billet">
                  <option value="">Usar adquirente Cartão+Boleto padrão do sistema</option>
                  @foreach ($adquirentes->where('status', 1) as $adquirente)
                    <option value="{{ $adquirente->referencia }}" 
                      {{ $user->preferred_adquirente_card_billet == $adquirente->referencia ? 'selected' : '' }}>
                      {{ $adquirente->adquirente }}
                      @if($adquirente->is_default_card_billet)
                        <span class="badge bg-primary ms-1">Padrão Cartão+Boleto</span>
                      @endif
                    </option>
                  @endforeach
                </select>
                <small class="form-text text-muted">
                  Adquirente usada para pagamentos via Cartão de Crédito e Boleto
                </small>
              </div>
            </div>
          </div>

          <!-- Documentos e Fotos -->
          <div class="mb-4">
            <h6 class="text-primary mb-3 border-bottom pb-2">
              <i class="fas fa-camera me-2"></i>Documentos e Fotos
            </h6>
            <div class="row g-3">
              <div class="col-lg-4 col-md-6">
                <x-image-upload id="foto_rg_frente" name="foto_rg_frente" label="Foto RG (Frente)" :value="'/'.$user->foto_rg_frente" />
              </div>
              <div class="col-lg-4 col-md-6">
                <x-image-upload id="foto_rg_verso" name="foto_rg_verso" label="Foto RG (Verso)" :value="'/'.$user->foto_rg_verso" />
              </div>
              <div class="col-lg-4 col-md-6">
                <x-image-upload id="selfie_rg" name="selfie_rg" label="Selfie RG" :value="'/'.$user->selfie_rg" />
              </div>
            </div>
          </div>

          <!-- Botão de Salvar -->
          <div class="d-flex justify-content-end gap-3 pt-3 border-top">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-2"></i>Cancelar
            </button>
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save me-2"></i>Salvar Alterações
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');

    modals.forEach(modal => {
      modal.addEventListener('shown.bs.modal', function() {
        Inputmask({
          "mask":"(99) 99999-9999"
        }).mask(modal.querySelectorAll('input[name="telefone"]'));
        Inputmask({
          mask: ["999.999.999-99","99.999.999/9999-99"],
          keepStatic: true
        }).mask(modal.querySelectorAll('input[name="cpf_cnpj"]'));
        Inputmask({
          "mask":"99999-999"
        }).mask(modal.querySelectorAll('input[name="cep"]'));
      });
    });



  });

  document.addEventListener('DOMContentLoaded', function() {
    // Executa onChangePermission para todos os campos já preenchidos
    document.querySelectorAll('[data-permission-select]').forEach(function(select) {
      const id = select.dataset.permissionId;
      onChangePermission(select, id);
    });
  });

  function onChangePermission(input, id) {
    const containerGerenteConfig = document.getElementById(`container-gerente-config-${id}`);
    const containerPercentage = document.getElementById(`container-percentage-${id}`);
    const containerGerenteAprovar = document.getElementById(`container-gerenteaprovar-${id}`);

    if (input.value ==="5") {
      containerGerenteConfig.classList.remove('hidden');
      containerPercentage.classList.remove('hidden');
      containerGerenteAprovar.classList.remove('hidden');
    } else {
      containerGerenteConfig.classList.add('hidden');
      containerPercentage.classList.add('hidden');
      containerGerenteAprovar.classList.add('hidden');
    }
  };

  // Função para controlar exibição das configurações flexíveis
  function toggleTaxaFlexivel(userId) {
    const checkbox = document.getElementById(`taxa_flexivel_ativa-${userId}`);
    const configDiv = document.getElementById(`config-flexivel-${userId}`);
    
    if (checkbox.checked) {
      configDiv.style.display = 'block';
    } else {
      configDiv.style.display = 'none';
    }
  }


  // Adicionar event listeners para todos os checkboxes de taxa flexível
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="taxa_flexivel_ativa-"]').forEach(function(checkbox) {
      const userId = checkbox.id.split('-').pop();
      checkbox.addEventListener('change', function() {
        toggleTaxaFlexivel(userId);
      });
    });

  });
</script>