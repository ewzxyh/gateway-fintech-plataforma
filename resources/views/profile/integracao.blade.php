<x-app-layout :route="'Integrações'">
  <div class="main-content app-content">
    <div class="container-fluid">
      <!-- Page Header -->
      <div class="row mt-4 mb-5">
        <div class="col-12">
          <div class="card border-primary">
            <div class="card-body text-center p-5">
              <div class="icon-circle bg-primary text-white mb-3 mx-auto" style="width: 80px; height: 80px;">
                <i class="fa-solid fa-link fa-2x"></i>
              </div>
              <h1 class="display-6 fw-bold mb-3">Integrações</h1>
              <p class="text-muted mb-0">Conecte suas ferramentas favoritas e automatize seus processos de negócio</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Cards de Integração -->
      <div class="row justify-content-center">
        <!-- Card UTMFY -->
        <div class="col-12 col-md-6 col-lg-4 mb-4">
          <div class="card h-100">
            <div class="card-body d-flex flex-column p-4">
              <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-3">
                  <div class="icon-circle bg-primary text-white me-3">
                    <i class="fa-solid fa-link"></i>
                  </div>
                  <div>
                    <h5 class="mb-0 fw-bold">UTMFY</h5>
                    <small class="text-muted">URL Tracking & Analytics</small>
                  </div>
                </div>
                @if(auth()->user()->integracao_utmfy)
                <div class="alert alert-success py-2 mb-0">
                  <small class="d-flex align-items-center">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    <span class="text-truncate">Token: {{ auth()->user()->integracao_utmfy }}</span>
                  </small>
                </div>
                @else
                <div class="alert alert-info py-2 mb-0">
                  <small>
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Nenhum token configurado
                  </small>
                </div>
                @endif
              </div>

              <div class="d-grid mt-3">
                @php
                $utmfy = auth()->user()->integracao_utmfy;
                $title = is_null($utmfy) || $utmfy == '' ?"Adicionar" :"Alterar";
                @endphp
                <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#utmfyModal">
                  <i class="fa-solid fa-{{ is_null($utmfy) || $utmfy == '' ? 'plus' : 'pencil' }} me-2"></i>{{ $title }} Token
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Nova Integração -->
        <div class="col-12 col-md-6 col-lg-4 mb-4">
          <div class="card h-100">
            <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
              <div class="icon-circle bg-secondary text-white mb-3 mx-auto" style="width: 64px; height: 64px;">
                <i class="fa-solid fa-plus fa-lg"></i>
              </div>
              <h5 class="fw-semibold mb-3">Nova Integração</h5>
              <p class="text-muted mb-3">Em breve, novas integrações estarão disponíveis</p>
              <button class="btn btn-outline-secondary" disabled>
                <i class="fa-solid fa-clock me-2"></i>Em Desenvolvimento
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal UTMFY Token -->
  <div class="modal fade" id="utmfyModal" tabindex="-1" aria-labelledby="utmfyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          @php
            $utmfy = auth()->user()->integracao_utmfy;
            $title = is_null($utmfy) || $utmfy == '' ?"Adicionar" :"Alterar";
          @endphp
          <h5 class="modal-title fw-semibold" id="utmfyLabel">
            <i class="fa-solid fa-link me-2"></i>UTMFY Token - {{ $title }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('integracoes.utmfy.edit') }}">
          @csrf
          <div class="modal-body p-4">
            <div class="mb-3">
              <label for="integracao_utmfy" class="form-label">API Token</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fa-solid fa-key"></i>
                </span>
                <input type="text" 
                    class="form-control" 
                    id="integracao_utmfy" 
                    name="integracao_utmfy" 
                    placeholder="Digite seu API Token" 
                    value="{{ $utmfy }}" 
                    required>
              </div>
              <div class="form-text">
                <i class="fa-solid fa-info-circle"></i> Obtenha seu token de API no painel do UTMFY
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fa-solid fa-check me-1"></i>{{ $title }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

</x-app-layout>