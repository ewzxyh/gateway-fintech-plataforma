<x-app-layout :route="'[ADMIN] Ajustes Landing Page'">
  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-danger">
            <div class="card-body p-5">
              <div class="d-flex align-items-center mb-3">
                <div class="icon-circle bg-danger text-white me-3" style="width: 64px; height: 64px;">
                  <i class="fa-solid fa-browser fa-lg"></i>
                </div>
                <div>
                  <h1 class="display-6 fw-bold mb-1">Configuração da Landing Page</h1>
                  <p class="text-muted mb-0">Configure as 6 seções da página de destino do sistema</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Formulário -->
      <form method="POST" action="{{ route('admin.landing.update') }}" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div class="row g-4">
          <!-- Coluna Esquerda -->
          <div class="col-lg-6">
            
            <!-- Seção 1 -->
            <div class="card border-primary border-2 mb-4">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                  <i class="fa-solid fa-1 me-2"></i>
                  Seção 1
                </h5>
              </div>
              <div class="card-body p-4">
                <x-text-field name="section1_title" label="Título" :value="$landing->section1_title" />
                <x-text-field name="section1_description" label="Descrição" :value="$landing->section1_description" />
                <x-image-upload id="section1_image" name="section1_image" label="Imagem" :value="asset($landing->section1_image)" />
              </div>
            </div>

            <!-- Seção 2 -->
            <div class="card border-success border-2 mb-4">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                  <i class="fa-solid fa-2 me-2"></i>
                  Seção 2
                </h5>
              </div>
              <div class="card-body p-4">
                <x-text-field name="section2_title" label="Título" :value="$landing->section2_title" />
                <x-text-field name="section2_description" label="Descrição" :value="$landing->section2_description" />
                
                <div class="alert alert-info mb-3">
                  <i class="fa-solid fa-info-circle me-2"></i>
                  <strong>3 Imagens:</strong> Configure as três imagens desta seção
                </div>

                <x-image-upload id="section2_image1" name="section2_image1" label="Imagem 1" :value="asset($landing->section2_image1)" />
                <x-image-upload id="section2_image2" name="section2_image2" label="Imagem 2" :value="asset($landing->section2_image2)" />
                <x-image-upload id="section2_image3" name="section2_image3" label="Imagem 3" :value="asset($landing->section2_image3)" />
              </div>
            </div>

            <!-- Seção 6 -->
            <div class="card border-secondary border-2 mb-4">
              <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                  <i class="fa-solid fa-6 me-2"></i>
                  Seção 6
                </h5>
              </div>
              <div class="card-body p-4">
                <x-text-field name="section6_title" label="Título" :value="$landing->section6_title" />
                <x-text-field name="section6_description" label="Descrição" :value="$landing->section6_description" />
                <x-image-upload id="section6_image" name="section6_image" label="Imagem" :value="asset($landing->section6_image)" />
              </div>
            </div>

          </div>

          <!-- Coluna Direita -->
          <div class="col-lg-6">
            
            <!-- Seção 3 -->
            <div class="card border-warning border-2 mb-4">
              <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                  <i class="fa-solid fa-3 me-2"></i>
                  Seção 3
                </h5>
              </div>
              <div class="card-body p-4">
                <x-text-field name="section3_title" label="Título" :value="$landing->section3_title" />

                <div class="alert alert-info mb-3">
                  <i class="fa-solid fa-info-circle me-2"></i>
                  <strong>3 Items:</strong> Configure os três itens desta seção
                </div>

                <!-- Item 1 -->
                <div class="card bg-light mb-3">
                  <div class="card-body p-3">
                    <h6 class="fw-bold mb-3">
                      <i class="fa-solid fa-circle-1 text-primary me-2"></i>
                      Item 1
                    </h6>
                    <x-image-upload id="section3_item1_image" name="section3_item1_image" label="Imagem" :value="asset($landing->section3_item1_image)" />
                    <x-text-field name="section3_item1_title" label="Título" :value="$landing->section3_item1_title" />
                    <x-text-field name="section3_item1_description" label="Descrição" :value="$landing->section3_item1_description" />
                  </div>
                </div>

                <!-- Item 2 -->
                <div class="card bg-light mb-3">
                  <div class="card-body p-3">
                    <h6 class="fw-bold mb-3">
                      <i class="fa-solid fa-circle-2 text-success me-2"></i>
                      Item 2
                    </h6>
                    <x-image-upload id="section3_item2_image" name="section3_item2_image" label="Imagem" :value="asset($landing->section3_item2_image)" />
                    <x-text-field name="section3_item2_title" label="Título" :value="$landing->section3_item2_title" />
                    <x-text-field name="section3_item2_description" label="Descrição" :value="$landing->section3_item2_description" />
                  </div>
                </div>

                <!-- Item 3 -->
                <div class="card bg-light mb-3">
                  <div class="card-body p-3">
                    <h6 class="fw-bold mb-3">
                      <i class="fa-solid fa-circle-3 text-warning me-2"></i>
                      Item 3
                    </h6>
                    <x-image-upload id="section3_item3_image" name="section3_item3_image" label="Imagem" :value="asset($landing->section3_item3_image)" />
                    <x-text-field name="section3_item3_title" label="Título" :value="$landing->section3_item3_title" />
                    <x-text-field name="section3_item3_description" label="Descrição" :value="$landing->section3_item3_description" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Seção 4 -->
            <div class="card border-danger border-2 mb-4">
              <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                  <i class="fa-solid fa-4 me-2"></i>
                  Seção 4
                </h5>
              </div>
              <div class="card-body p-4">
                <x-image-upload id="section4_image" name="section4_image" label="Imagem" :value="asset($landing->section4_image)" />
                <x-text-field name="section4_title" label="Título" :value="$landing->section4_title" />
                <x-text-field name="section4_description" label="Descrição" :value="$landing->section4_description" />
                <x-text-field name="section4_link" label="Link" :value="$landing->section4_link" />
                
                <small class="text-muted">
                  <i class="fa-solid fa-link me-1"></i>
                  Insira a URL completa para o link do botão desta seção
                </small>
              </div>
            </div>

            <!-- Seção 5 -->
            <div class="card border-info border-2 mb-4">
              <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                  <i class="fa-solid fa-5 me-2"></i>
                  Seção 5
                </h5>
              </div>
              <div class="card-body p-4">
                <x-text-field name="section5_title" label="Título" :value="$landing->section5_title" />
                <x-text-field name="section5_description" label="Descrição" :value="$landing->section5_description" />
                <x-image-upload id="section5_image" name="section5_image" label="Imagem" :value="asset($landing->section5_image)" />
              </div>
            </div>

          </div>
        </div>

        <!-- Botão Salvar -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card border-success">
              <div class="card-body p-4 text-center">
                <button type="submit" class="btn btn-success btn-lg px-5">
                  <i class="fa-solid fa-save me-2"></i>
                  Salvar Todas as Alterações
                </button>
              </div>
            </div>
          </div>
        </div>

      </form>

    </div>
  </div>
</x-app-layout>

