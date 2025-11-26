<x-app-layout :route="'[ADMIN] Ajustes de gerentes'">

<!-- Dropzone CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />

<!-- Dropzone JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-user-tie fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Sistema de Gerentes e Apoio</h1>
                  <p class="text-muted mb-0">Configure o sistema de gerentes e gerencie o material de apoio</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Sistema de Gerentes -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-success">
            <div class="card-header bg-success text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-users-gear me-2"></i>
                Sistema de Gerentes
              </h5>
            </div>
            <div class="card-body p-4">
              <form method="POST" action="{{ route('admin.ajustes.gerais') }}">
                @csrf
                <div class="row g-4 align-items-end">
                  <!-- Switch de Ativação -->
                  <div class="col-lg-6">
                    <div class="card bg-light">
                      <div class="card-body p-4">
                        <div class="form-check form-switch">
                          <input class="form-check-input" 
                            type="checkbox" 
                            role="switch"
                            id="switchCheckDefault"
                            name="gerente_active"
                            value="{{ $gerente_active }}"
                            {{ ($gerente_active ?? false) ? 'checked' : '' }}
                            style="width: 3rem; height: 1.5rem; cursor: pointer;">
                          <label class="form-check-label ms-2 fw-semibold" for="switchCheckDefault">
                            Sistema de Gerente Ativo
                          </label>
                        </div>
                        <small class="text-muted d-block mt-2">
                          <i class="fa-solid fa-info-circle me-1"></i>
                          Ative para habilitar o sistema de comissões para gerentes
                        </small>
                      </div>
                    </div>
                  </div>

                  <!-- Porcentagem -->
                  <div class="col-lg-4">
                    <label for="gerente_percentage" class="form-label fw-semibold">
                      <i class="fa-solid fa-percent text-primary me-2"></i>
                      Porcentagem de Comissão
                    </label>
                    <div class="input-group input-group-lg">
                      <input type="number" 
                        class="form-control" 
                        name="gerente_percentage"
                        id="gerente_percentage"
                        value="{{ $porcentagem }}"
                        step="0.01"
                        min="0"
                        max="100"
                        placeholder="Ex: 5.00"
                        required>
                      <span class="input-group-text">%</span>
                    </div>
                    <small class="text-muted">
                      <i class="fa-solid fa-lightbulb me-1"></i>
                      Porcentagem que o gerente receberá sobre as transações
                    </small>
                  </div>

                  <!-- Botão Salvar -->
                  <div class="col-lg-2">
                    <button type="submit" class="btn btn-success btn-lg w-100">
                      <i class="fa-solid fa-save me-2"></i>Salvar
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Material de Apoio -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="fa-solid fa-book-open me-2"></i>
                Material de Apoio
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="text-muted mb-4">
                <i class="fa-solid fa-info-circle me-2"></i>
                Adicione e gerencie materiais de apoio para os usuários do sistema
              </p>

              <div class="container-clone row g-4">
                @foreach ($apoios as $apoio)
                  <div class="col-lg-4 col-md-6 card-nivel" data-id="{{ $apoio->id }}">
                    <div class="card h-100 border-2">
                      <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                          <i class="fa-solid fa-file-lines me-2"></i>
                          Material #{{ $apoio->id }}
                        </h6>
                      </div>
                      <div class="card-body p-3">
                        <form class="form-nivel" data-id="{{ $apoio->id }}" enctype="multipart/form-data">
                          <input type="hidden" name="id" value="{{ $apoio->id }}">
                          
                          <div class="mb-3">
                            <label class="form-label fw-semibold">
                              <i class="fa-solid fa-heading text-primary me-1"></i>
                              Título
                            </label>
                            <input type="text" class="form-control" 
                              name="titulo" value="{{ $apoio->titulo }}" required>
                          </div>

                          <div class="mb-3">
                            <label class="form-label fw-semibold">
                              <i class="fa-solid fa-align-left text-info me-1"></i>
                              Descrição
                            </label>
                            <textarea class="form-control" 
                              name="descricao" rows="3" required>{{ $apoio->descricao }}</textarea>
                          </div>

                          <div class="mb-3">
                            <x-image-upload 
                              id="imagem-{{$apoio->id}}" 
                              name="imagem" 
                              label="Imagem" 
                              :value="$apoio->imagem" 
                              :height="'130px'" />
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
                <div id="card-clone" class="col-lg-4 col-md-6 d-none">
                  <div class="card h-100 border-secondary border-2">
                    <div class="card-header bg-secondary text-white">
                      <h6 class="mb-0">
                        <i class="fa-solid fa-file-lines me-2"></i>
                        Novo Material
                      </h6>
                    </div>
                    <div class="card-body p-3">
                      <form class="form-nivel" data-id="" enctype="multipart/form-data">
                        <div class="mb-3">
                          <label class="form-label fw-semibold">
                            <i class="fa-solid fa-heading text-primary me-1"></i>
                            Título
                          </label>
                          <input type="text" class="form-control" name="titulo" required>
                        </div>

                        <div class="mb-3">
                          <label class="form-label fw-semibold">
                            <i class="fa-solid fa-align-left text-info me-1"></i>
                            Descrição
                          </label>
                          <textarea class="form-control" name="descricao" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                          <label class="form-label fw-semibold">
                            <i class="fa-solid fa-image text-warning me-1"></i>
                            Imagem
                          </label>
                          <div class="dropzone dz-wrapper" 
                            data-name="imagem" 
                            style="min-height: 130px; border: 2px dashed #dee2e6; border-radius: 0.25rem; padding: 10px;">
                          </div>
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

                {{-- Card para adicionar novo material --}}
                <div id="clone-card" class="col-lg-4 col-md-6">
                  <div class="card h-100 border-primary border-2 border-dashed" 
                     style="cursor: pointer; min-height: 400px;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                      <div class="icon-circle bg-primary text-white mb-3" style="width: 64px; height: 64px;">
                        <i class="fa-solid fa-plus fa-lg"></i>
                      </div>
                      <h5 class="text-primary fw-bold mb-2">Adicionar Material</h5>
                      <p class="text-muted mb-0">Clique para criar um novo material de apoio</p>
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

  <script>
    const baseUrl ="/{{env("ADM_ROUTE")}}/ajustes";

    // ======================================================
    // DESABILITAR AUTO-DISCOVERY DO DROPZONE
    // ======================================================
    Dropzone.autoDiscover = false;

    // ======================================================
    // INICIALIZAR DROPZONES
    // ======================================================
    function initDropzones() {
      document.querySelectorAll('.dropzone:not([data-initialized])').forEach((dzElement) => {
        dzElement.dataset.initialized ="true";

        const dz = new Dropzone(dzElement, {
          url: '/fake',
          autoProcessQueue: false,
          maxFiles: 1,
          addRemoveLinks: true,
          acceptedFiles: 'image/*',
          dictDefaultMessage: '<i class="fa-solid fa-cloud-arrow-up fa-2x mb-2"></i><br>Arraste uma imagem ou clique para selecionar',
          dictRemoveFile: 'Remover',
          dictCancelUpload: 'Cancelar',
          init: function () {
            this.on("addedfile", file => {
              dzElement.dataset.file = file.name;
              dzElement.file = file;
            });

            this.on("removedfile", () => {
              delete dzElement.file;
            });
          }
        });

        // Mostrar imagem antiga se existir
        const value = dzElement.dataset.value;
        if (value) {
          const mockFile = { name:"imagem", size: 12345 };
          dz.emit("addedfile", mockFile);
          dz.emit("thumbnail", mockFile, value);
          dz.emit("complete", mockFile);
          dz.files.push(mockFile);
        }
      });
    }

    // ======================================================
    // DOCUMENT READY
    // ======================================================
    document.addEventListener("DOMContentLoaded", function () {
      // ======================================================
      // REFERÊNCIAS DOS ELEMENTOS
      // ======================================================
      const container = document.querySelector('.container-clone');
      const clonador = document.getElementById('clone-card');
      const modelo = document.getElementById('card-clone');

      // ======================================================
      // INICIALIZAR DROPZONES NA CARGA
      // ======================================================
      initDropzones();

      // ======================================================
      // CLONAGEM DE CARD (ADICIONAR NOVO MATERIAL)
      // ======================================================
      clonador.addEventListener('click', () => {
        const novo = modelo.cloneNode(true);
        novo.classList.remove('d-none');
        novo.removeAttribute('id');
        novo.classList.add('card-nivel');

        // Limpar inputs e textareas
        novo.querySelectorAll('input, textarea').forEach(el => {
          if (el.type !== 'hidden') {
            el.value = '';
          }
        });

        // Reset dropzone
        novo.querySelectorAll('.dropzone').forEach(el => {
          el.innerHTML = '';
          el.removeAttribute('data-initialized');
        });

        container.insertBefore(novo, clonador);
        initDropzones();

        // Focar no primeiro input
        novo.querySelector('input[name="titulo"]')?.focus();
      });

      // ======================================================
      // DELEGAÇÃO DE EVENTOS (SALVAR E EXCLUIR)
      // ======================================================
      container.addEventListener('click', async function (e) {
        const form = e.target.closest('.form-nivel');
        const card = e.target.closest('.card-nivel');

        if (!form) return;

        const id = form.dataset.id;

        // ======================================================
        // BOTÃO SALVAR
        // ======================================================
        if (e.target.closest('.btn-salvar')) {
          const btnSalvar = e.target.closest('.btn-salvar');
          const textoOriginal = btnSalvar.innerHTML;
          
          // Desabilitar botão
          btnSalvar.disabled = true;
          btnSalvar.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Salvando...';

          try {
            const formData = new FormData(form);
            const dz = form.querySelector('.dropzone');
            const file = dz?.file;

            if (file) {
              formData.append("imagem", file);
            }

            const url = id ? `${baseUrl}/apoio/${id}` : `${baseUrl}/apoio`;
            if (id) {
              formData.append('_method', 'PUT');
            }

            const res = await fetch(url, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: formData
            });

            const data = await res.json();
            
            if (data.success) {
              showToast('success', data.message);
              
              // Se for novo material, atualizar o ID
              if (!id) {
                form.dataset.id = data.id;
                card.dataset.id = data.id;

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'id';
                hidden.value = data.id;
                form.appendChild(hidden);

                // Atualizar o header do card
                const cardHeader = card.querySelector('.card-header h6');
                if (cardHeader) {
                  cardHeader.innerHTML = `<i class="fa-solid fa-file-lines me-2"></i>Material #${data.id}`;
                }

                // Mudar border de secondary para border-2
                const cardElement = card.querySelector('.card');
                if (cardElement) {
                  cardElement.classList.remove('border-secondary');
                }
              }
            } else {
              showToast('error', data.message || 'Erro ao salvar');
            }
          } catch (error) {
            console.error('Erro:', error);
            showToast('error', 'Erro ao salvar material');
          } finally {
            // Reabilitar botão
            btnSalvar.disabled = false;
            btnSalvar.innerHTML = textoOriginal;
          }
        }

        // ======================================================
        // BOTÃO EXCLUIR
        // ======================================================
        if (e.target.closest('.btn-excluir')) {
          if (id) {
            // Material existente - confirmar exclusão
            const titulo = form.querySelector('input[name="titulo"]').value;
            if (confirm(`Tem certeza que deseja excluir o material"${titulo}"?\n\nEsta ação não pode ser desfeita.`)) {
              const btnExcluir = e.target.closest('.btn-excluir');
              const textoOriginal = btnExcluir.innerHTML;
              
              btnExcluir.disabled = true;
              btnExcluir.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Excluindo...';

              try {
                const res = await fetch(`${baseUrl}/apoio/${id}`, {
                  method: 'DELETE',
                  headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                  }
                });

                if (res.ok) {
                  card.remove();
                  showToast('success', 'Material excluído com sucesso');
                } else {
                  showToast('error', 'Erro ao excluir material');
                  btnExcluir.disabled = false;
                  btnExcluir.innerHTML = textoOriginal;
                }
              } catch (error) {
                console.error('Erro:', error);
                showToast('error', 'Erro ao excluir material');
                btnExcluir.disabled = false;
                btnExcluir.innerHTML = textoOriginal;
              }
            }
          } else {
            // Novo material (não salvo ainda) - remover diretamente
            card.remove();
          }
        }
      });
    });
  </script>
</x-app-layout>
