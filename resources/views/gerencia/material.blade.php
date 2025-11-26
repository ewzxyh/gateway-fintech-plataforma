
@php
  $setting = \App\Helpers\Helper::getSetting();
  $porcentagem = $setting->gerente_percentage;
@endphp
<x-app-layout :route="'[GERENCIA] Material de divulgação'">
  <style>
    .table-responsive {
  overflow: visible !important;
}
  </style>

  <div class="main-content app-content">
    <div class="container-fluid">
      <div class="mb-3 row justify-content-between align-items-">
        <div style="display:flex;align-item:center;justify-content:flex-start;" class="mb-5 col-12 col-md-4 mb-md-0 justify-content-start align-items-center">
          <h1 class="mb-0 display-5">Material de divulgação</h1>
        </div>
      </div>

      <!-- Start:: row-1 -->
      <div class="row">
        <div class="mb-4 col-12">
          <div class="border-4 card card-raised card-border-color">
            <div class="px-4 card-body">
              <div class="mb-2 d-flex justify-content-between align-items-center">
                <div class="me-2">
                  <div class="display-6">Material de divulgação</div>
                  <div class="card-text">Aqui está disponível todo material necessário para divulgação</div>
                </div>
                <div class="text-white icon-circle bg-info card-color"><i class="text-xl fa-solid fa-folder-open"></i></div>
              </div>
            </div>
          </div>
        </div>
        <div class="mb-4 col-12">
          <div class="border-4 card card-raised card-border-color">
            <div class="px-4 card-body">
              <div class="mb-2 d-flex justify-content-between align-items-center">
                <div class="me-2">
                  <div class="display-6"><button id="shareButton" class="justify-center w-4 btn btn-primary align-center"><i class="text-sm fa-solid fa-share-from-square"></i></button> {{env('APP_URL')."/register?ref=".auth()->user()->code_ref}}</div>
                  <div class="card-text">Seu link de compartilhamento</div>
                </div>
                <div class="text-white icon-circle bg-info card-color"><i class="text-xl fa-solid fa-share"></i></div>
              </div>
            </div>
          </div>
        </div>
        <div class="mb-3">

          @foreach ($apoios as $apoio)
          <div class="accordion" id="material-{{$apoio->id}}">
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading-{{$apoio->id}}">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{$apoio->id}}" aria-expanded="true" aria-controls="collapse-{{$apoio->id}}">
                  {{$apoio->titulo}}
                </button>
              </h2>
              <div id="collapse-{{$apoio->id}}" class="accordion-collapse collapse" aria-labelledby="heading-{{$apoio->id}}" data-bs-parent="#material-{{$apoio->id}}">
                <div class="accordion-body row">
                  <div class="col-md-3">
                    <img src="{{ $apoio->imagem }}" style="width: auto; height: 120px;" alt="Imagem de apoio">
                  </div>
                  <div class="col-md-9">
                    <textarea readonly class="text-area" style="width: 100%; min-height: 120px;">{{ $apoio->descricao }}</textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
        </div>
      </div>
    </div>
  </div>
  <script>
    document.getElementById('shareButton').addEventListener('click', async () => {
      if (navigator.share) {
        try {
          await navigator.share({
            title:"{{ env('APP_NAME') }}",
            text: 'Somos o maior ecossistema de soluções integradas para negócios digitais no Brasil, com ferramentas completas que simplificam e escalam operações de qualquer tamanho.',
            url:"{{env('APP_URL')."/register?ref=".auth()->user()->code_ref}}"
          });
          console.log('Compartilhado com sucesso!');
        } catch (err) {
          console.error('Erro ao compartilhar:', err);
        }
      } else {
        console.log('O compartilhamento não é suportado neste navegador.');
      }
    });
  </script>
</x-app-layout>
