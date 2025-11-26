<x-app-layout :route="'[ADMIN] Ajustes de níveis'">

  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-layer-group fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Sistema de Níveis</h1>
                  <p class="text-muted mb-0">Configure os níveis de usuários e seus requisitos de faturamento</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Ativar Sistema de Níveis -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-success">
            <div class="card-header bg-success text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-toggle-on me-2"></i>
                Ativação do Sistema
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <h6 class="fw-bold mb-2">Sistema de Níveis</h6>
                  <p class="text-muted mb-0">Quando ativado, os usuários serão classificados em níveis baseados no faturamento</p>
                </div>
                <div class="form-check form-switch ms-4">
                  <input class="form-check-input" type="checkbox" role="switch" 
                      id="switchCheckDefault" name="niveis_ativo" 
                      {{ ($niveis_ativo ?? false) ? 'checked' : '' }} 
                      value="{{ $niveis_ativo }}"
                      style="width: 3rem; height: 1.5rem; cursor: pointer;">
                  <label class="form-check-label visually-hidden" for="switchCheckDefault">
                    Sistema de níveis ativo
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cards de Níveis -->
      <div class="row">
        <div class="col-12">
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-star me-2"></i>
                Configuração de Níveis
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="container-clone row">
                @foreach ($niveis as $nivel)
                  <div class="mb-4 col-md-6 col-lg-4 col-xl-3 card-nivel" data-id="{{ $nivel->id }}">
                    <div class="card h-100 border-2" style="border-color: {{ $nivel->cor }};">
                      <div class="card-header text-white" style="background-color: {{ $nivel->cor }};">
                        <h6 class="mb-0">
                          <i class="fa-solid fa-trophy me-2"></i>
                          Nível #{{ $nivel->id }}
                        </h6>
                      </div>
                      <div class="card-body p-3">
                        <form class="form-nivel" data-id="{{ $nivel->id }}">
                          <input type="hidden" name="id" value="{{ $nivel->id }}">
                          
                          <div class="mb-3">
                            <label class="form-label fw-semibold">
                              <i class="fa-solid fa-tag text-primary me-1"></i>
                              Nome
                            </label>
                            <input type="text" class="form-control" name="nome" 
                                value="{{ $nivel->nome }}" required>
                          </div>
                          
                          <div class="mb-3">
                            <label class="form-label fw-semibold">
                              <i class="fa-solid fa-dollar-sign text-success me-1"></i>
                              Mínimo (R$)
                            </label>
                            <input type="text" class="form-control" name="minimo" 
                                value="{{ $nivel->id == 1 ? 0 : $nivel->minimo }}" 
                                {{ $nivel->id == 1 ? 'disabled' : 'required' }}>
                            @if($nivel->id == 1)
                              <small class="text-muted">O primeiro nível sempre inicia em R$ 0,00</small>
                            @endif
                          </div>
                          
                          <div class="mb-3">
                            <label class="form-label fw-semibold">
                              <i class="fa-solid fa-coins text-warning me-1"></i>
                              Máximo (R$)
                            </label>
                            <input type="text" class="form-control" name="maximo" 
                                value="{{ $nivel->maximo }}" required>
                          </div>
                          
                          <div class="mb-3">
                            <label class="form-label fw-semibold">
                              <i class="fa-solid fa-palette text-info me-1"></i>
                              Cor
                            </label>
                            <input type="color" class="form-control form-control-color w-100" 
                                name="cor" value="{{ $nivel->cor }}" required>
                          </div>
                          
                          <div class="d-flex gap-2">
                            <button type="button" class="btn btn-danger flex-fill btn-excluir">
                              <i class="fa-solid fa-trash me-1"></i>Excluir
                            </button>
                            <button type="button" class="btn btn-success flex-fill btn-salvar">
                              <i class="fa-solid fa-save me-1"></i>Salvar
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                @endforeach

                {{-- Card modelo para clonagem --}}
                <div id="card-clone" class="mb-4 col-md-6 col-lg-4 col-xl-3 d-none">
                  <div class="card h-100 border-secondary border-2">
                    <div class="card-header bg-secondary text-white">
                      <h6 class="mb-0">
                        <i class="fa-solid fa-trophy me-2"></i>
                        Novo Nível
                      </h6>
                    </div>
                    <div class="card-body p-3">
                      <form class="form-nivel" data-id="">
                        <div class="mb-3">
                          <label class="form-label fw-semibold">
                            <i class="fa-solid fa-tag text-primary me-1"></i>
                            Nome
                          </label>
                          <input type="text" class="form-control" name="nome" required>
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label fw-semibold">
                            <i class="fa-solid fa-dollar-sign text-success me-1"></i>
                            Mínimo (R$)
                          </label>
                          <input type="text" class="form-control" name="minimo" required>
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label fw-semibold">
                            <i class="fa-solid fa-coins text-warning me-1"></i>
                            Máximo (R$)
                          </label>
                          <input type="text" class="form-control" name="maximo" required>
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label fw-semibold">
                            <i class="fa-solid fa-palette text-info me-1"></i>
                            Cor
                          </label>
                          <input type="color" class="form-control form-control-color w-100" 
                              name="cor" value="#6c757d" required>
                        </div>
                        
                        <div class="d-flex gap-2">
                          <button type="button" class="btn btn-danger flex-fill btn-excluir">
                            <i class="fa-solid fa-trash me-1"></i>Excluir
                          </button>
                          <button type="button" class="btn btn-success flex-fill btn-salvar">
                            <i class="fa-solid fa-save me-1"></i>Salvar
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                {{-- Card para adicionar novo nível --}}
                <div id="clone-card" class="mb-4 col-md-6 col-lg-4 col-xl-3">
                  <div class="card h-100 border-primary border-2 border-dashed" 
                     style="cursor: pointer; min-height: 300px;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                      <div class="icon-circle bg-primary text-white mb-3" style="width: 64px; height: 64px;">
                        <i class="fa-solid fa-plus fa-lg"></i>
                      </div>
                      <h5 class="text-primary fw-bold mb-2">Adicionar Nível</h5>
                      <p class="text-muted mb-0">Clique para criar um novo nível</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Modal de Confirmação de Exclusão -->
  <div class="modal fade" id="deleteNivelModal" tabindex="-1" aria-labelledby="deleteNivelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="deleteNivelModalLabel">
            <i class="fa-solid fa-trash-alt me-2"></i>Confirmar Exclusão
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="alert alert-danger d-flex align-items-start">
            <i class="fa-solid fa-exclamation-triangle fa-lg me-3 mt-1"></i>
            <div>
              <h6 class="alert-heading mb-2">Atenção!</h6>
              <p class="mb-0">Você tem certeza que deseja excluir o nível <strong id="nivelNome"></strong>?</p>
            </div>
          </div>
          <p class="text-muted mb-0">
            <i class="fa-solid fa-info-circle me-2"></i>
            Esta ação não pode ser desfeita e todos os usuários deste nível serão reclassificados.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-2"></i>Cancelar
          </button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
            <i class="fa-solid fa-trash-alt me-2"></i>Confirmar Exclusão
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const baseUrl ="/{{env("ADM_ROUTE")}}/ajustes";

    document.addEventListener("DOMContentLoaded", function () {
      // ======================================================
      // REFERÊNCIAS DOS ELEMENTOS
      // ======================================================
      const container = document.querySelector('.container-clone');
      const clonador = document.getElementById('clone-card');
      const modelo = document.getElementById('card-clone');

      // ======================================================
      // CLONAGEM DE CARD (ADICIONAR NOVO NÍVEL)
      // ======================================================
      clonador.addEventListener('click', () => {
        const novo = modelo.cloneNode(true);
        novo.classList.remove('d-none');
        novo.removeAttribute('id');
        novo.classList.add('card-nivel');
        
        // Limpar todos os inputs
        novo.querySelectorAll('input').forEach(input => {
          if (input.type === 'color') {
            input.value = '#6c757d';
          } else {
            input.value = '';
          }
        });
        
        // Inserir antes do card de adicionar
        container.insertBefore(novo, clonador);
        
        // Focar no primeiro input
        novo.querySelector('input[name="nome"]').focus();
      });

      // ======================================================
      // DELEGAÇÃO DE EVENTOS (SALVAR E EXCLUIR)
      // ======================================================
      container.addEventListener('click', function (e) {
        const card = e.target.closest('.card-nivel');
        const form = e.target.closest('.form-nivel');
        
        if (!form || !card) return;

        const id = form.dataset.id;

        // ======================================================
        // BOTÃO EXCLUIR
        // ======================================================
        if (e.target.closest('.btn-excluir')) {
          if (id) {
            // Nível existente - mostrar modal de confirmação
            const nome = form.querySelector('input[name="nome"]').value;
            excluirNivel(id, nome, card);
          } else {
            // Novo nível (não salvo ainda) - remover diretamente
            card.remove();
          }
        }

        // ======================================================
        // BOTÃO SALVAR
        // ======================================================
        if (e.target.closest('.btn-salvar')) {
          const formData = new FormData(form);
          const url = id ? `${baseUrl}/niveis/${id}` : `${baseUrl}/niveis`;
          const method = 'POST';

          if (id) {
            formData.append('_method', 'PUT');
          }

          // Desabilitar botão durante o salvamento
          const btnSalvar = e.target.closest('.btn-salvar');
          const textoOriginal = btnSalvar.innerHTML;
          btnSalvar.disabled = true;
          btnSalvar.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Salvando...';

          fetch(url, {
            method: method,
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              showToast('success', data.message);
              
              // Se for um novo nível, atualizar o ID
              if (!id) {
                form.dataset.id = data.id;
                card.dataset.id = data.id;
                
                // Adicionar input hidden com ID
                const inputHidden = document.createElement('input');
                inputHidden.setAttribute('type', 'hidden');
                inputHidden.setAttribute('name', 'id');
                inputHidden.value = data.id;
                form.appendChild(inputHidden);
                
                // Atualizar o header do card
                const cardHeader = card.querySelector('.card-header h6');
                if (cardHeader) {
                  cardHeader.innerHTML = `<i class="fa-solid fa-trophy me-2"></i>Nível #${data.id}`;
                }
              }
              
              // Atualizar a cor do card
              const cor = form.querySelector('input[name="cor"]').value;
              card.querySelector('.card').style.borderColor = cor;
              card.querySelector('.card-header').style.backgroundColor = cor;
            } else {
              showToast('error', data.message || 'Erro ao salvar');
            }
          })
          .catch(error => {
            console.error('Erro:', error);
            showToast('error', 'Erro ao salvar nível');
          })
          .finally(() => {
            // Reabilitar botão
            btnSalvar.disabled = false;
            btnSalvar.innerHTML = textoOriginal;
          });
        }
      });
    });

    // ======================================================
    // VARIÁVEL GLOBAL PARA ARMAZENAR NÍVEL A SER EXCLUÍDO
    // ======================================================
    let nivelToDelete = null;

    // ======================================================
    // FUNÇÃO PARA EXCLUIR NÍVEL
    // ======================================================
    function excluirNivel(id, nome, card) {
      nivelToDelete = { id: id, nome: nome, card: card };
      document.getElementById('nivelNome').textContent = '"' + nome + '"';
      
      const modal = new bootstrap.Modal(document.getElementById('deleteNivelModal'));
      modal.show();
    }

    // ======================================================
    // CONFIRMAR EXCLUSÃO NO MODAL
    // ======================================================
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
      if (!nivelToDelete) return;
      
      const btn = this;
      const textoOriginal = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Excluindo...';
      
      fetch(`${baseUrl}/niveis/${nivelToDelete.id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
      .then(res => {
        if (res.ok) {
          const modal = bootstrap.Modal.getInstance(document.getElementById('deleteNivelModal'));
          modal.hide();
          
          nivelToDelete.card.remove();
          showToast('success', 'Nível excluído com sucesso');
        } else {
          showToast('error', 'Erro ao excluir nível');
        }
      })
      .catch(error => {
        console.error('Erro:', error);
        showToast('error', 'Erro ao excluir nível');
      })
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = textoOriginal;
        nivelToDelete = null;
      });
    });

    // ======================================================
    // LIMPAR VARIÁVEL AO FECHAR O MODAL
    // ======================================================
    document.getElementById('deleteNivelModal').addEventListener('hidden.bs.modal', function() {
      nivelToDelete = null;
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // ======================================================
      // ATIVAR/DESATIVAR SISTEMA DE NÍVEIS
      // ======================================================
      const active = document.querySelector('[name="niveis_ativo"]');
      const url = baseUrl + '/active-niveis';

      active.addEventListener('change', function () {
        const formData = new FormData();
        formData.append('niveis_ativo', active.checked ? 1 : 0);

        fetch(url, {
          method:"POST",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            showToast('success', data.message);
          } else {
            showToast('error', 'Erro ao salvar configuração');
            // Reverter o switch em caso de erro
            active.checked = !active.checked;
          }
        })
        .catch(error => {
          console.error('Erro:', error);
          showToast('error', 'Erro ao salvar configuração');
          active.checked = !active.checked;
        });
      });
    });
  </script>
</x-app-layout>
