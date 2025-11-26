

@php
  $setting = \App\Helpers\Helper::getSetting();
  $porcentagem = $setting->gerente_percentage;
@endphp
<x-app-layout :route="'[GERENCIA] Clientes'">
  <style>
    .table-responsive {
  overflow: visible !important;
}

/* Correção para modais travados */
.modal {
  z-index: 9999 !important;
}

.modal-backdrop {
  z-index: 9998 !important;
}

.modal-dialog {
  z-index: 10000 !important;
}

/* Garantir que o modal seja clicável */
.modal-content {
  position: relative;
  z-index: 10001 !important;
}

/* Corrigir backdrop que trava a tela */
.modal-backdrop.show {
  opacity: 0.5 !important;
  z-index: 9998 !important;
}

body.modal-open {
  overflow: hidden !important;
}

/* Estilo para os botões de ação */
.btn-group .btn {
  margin-right: 2px;
}

.btn-group .btn:last-child {
  margin-right: 0;
}

/* Garantir que os botões sejam clicáveis */
.btn {
  pointer-events: auto !important;
  cursor: pointer !important;
}
  </style>

  <div class="main-content app-content">
    <div class="container-fluid">

      <div class="mb-3 row justify-content-between align-items-">
        <div style="display:flex;align-item:center;justify-content:flex-start;" class="mb-5 col-12 col-md-4 mb-md-0 justify-content-start align-items-center">
          <h1 class="mb-0 display-5">Dashboard</h1>
        </div>
      </div>
      <!-- Start:: row-1 -->
      <div class="row g-3">
        <div class="col-12">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fa-solid fa-share"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Seu link de compartilhamento</p>
                  <div class="d-flex align-items-center gap-2 mt-1">
                    <h6 class="fw-bold mb-0 text-truncate" style="font-size: 0.95rem;">{{env('APP_URL')."/register?ref=".auth()->user()->code_ref}}</h6>
                    <button id="shareButton" class="btn btn-primary btn-sm">
                      <i class="fa-solid fa-share-from-square"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-info flex-shrink-0">
                  <i class="fa-solid fa-people-group"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Indicações</p>
                  <h4 class="fw-bold mb-0 text-info text-truncate" style="font-size: 1.35rem;">{{ (clone $users)->where('indicador_ref', auth()->user()->code_ref)->count() }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-primary flex-shrink-0">
                  <i class="fa-solid fa-people-group"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Clientes</p>
                  <h4 class="fw-bold mb-0 text-primary text-truncate" style="font-size: 1.35rem;">{{ (clone $users)->count() }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-success flex-shrink-0">
                  <i class="fa-solid fa-dollar-sign"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Minha comissão</p>
                  <h4 class="fw-bold mb-0 text-success text-truncate" style="font-size: 1.35rem;">R$ {{ number_format(((float) $comissoes * $porcentagem / 100), '2',',','.') }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xxl-3 col-md-6">
          <div class="card card-hover shadow-sm">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle-modern bg-gradient-warning flex-shrink-0">
                  <i class="fa-solid fa-users"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Clientes (total)</p>
                  <h4 class="fw-bold mb-0 text-warning text-truncate" style="font-size: 1.35rem;">{{ $usersTotal }}</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- End:: row-1 --}}

      <div class="mb-3 row" style="min-height:70vh;">
        <div class="col-xl-12">
          <div class="card card-raised">
            <div class="bg-white card-header">
              <h6>Meus clientes</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="table-listar-usuarios" class="table text-nowrap">
                  <thead>
                    <tr>
                      <th scope="col">Status</th>
                      <th scope="col">Nome</th>
                      <th scope="col">Email</th>
                      <th scope="col">Contato</th>
                      <th scope="col">Faturamento</th>
                      <th scope="col">Comissões</th>
                      <th scope="col">Data de Cadastro</th>
                       @if (auth()->user()->gerente_aprovar)
                        <th scope="col"></th>
                      @endif
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($users as $user)
                    <tr>
                      <td>
                        @if($user->status == 0)
                          <span class="text-white badge text-bg-danger">Pendente de doc</span>
                        @elseif($user->status == 1)
                          <span class="text-white badge text-bg-success">Aprovado</span>
                        @elseif($user->status == 5)
                          <span class="text-white badge text-bg-warning">Aguardando aprovação</span>
                        @endif
                      </td>
                      <td>{{ $user->name }}</td>
                      <td>{{ $user->email }}</td>
                      <td>
                        <div class="btn-group" role="group" aria-label="Basic outlined example">
                          <a href="tel:{{$user->telefone}}" target="_blank">
                            <button type="button" class="btn btn-outline-info"><i class="fa-solid fa-phone"></i></button>
                          </a>
                          @php
                            $hora = now()->hour; // Usa a hora atual do servidor (Carbon instance)
                            if ($hora >= 5 && $hora < 12) {
                              $saudacao = 'Bom dia';
                            } elseif ($hora >= 12 && $hora < 18) {
                              $saudacao = 'Boa tarde';
                            } else {
                              $saudacao = 'Boa noite';
                            }

                            $gerente_name = explode(' ',auth()->user()->name)[0];
                            $mensagem = $saudacao . ' ' . $user->name.'. Meu nome é '.$gerente_name.', sou seu gerente na '.$setting->gateway_name.'!';
                            $mensagemEncode = urlencode($mensagem);
                          @endphp

                          <a href="https://api.whatsapp.com/send?phone=55{{ $user->telefone }}&text={{ $mensagemEncode }}" target="_blank">
                            <button type="button" class="btn btn-outline-success"><i class="fa-brands fa-whatsapp"></i></button>
                          </a>
                        </div>
                      </td>
                      <td>R$ {{ number_format($user->depositos->where('status','PAID_OUT')->sum('amount'), 2, ',', '.') }}</td>

                      <td>
                        R$ {{ number_format($user->comissoes->sum('comission_value'), '2', ',','.') }}
                      </td>
                      <td>{{ $user->created_at->format('d/m/Y \à\s H:i:s') }}</td>
                      @if (auth()->user()->gerente_aprovar)
                        <td>
                          <div class="btn-group" role="group">
                            <!-- Botão Visualizar Usuário -->
                            <a href="{{ route('gerencia.detalhes', $user->id) }}" 
                              class="btn btn-sm btn-outline-info" 
                              title="Visualizar Usuário">
                              <i class="fa-solid fa-eye"></i>
                            </a>
                            
                            <!-- Botão Aprovar/Reprovar -->
                            @if ($user->status == 1)
                              <button type="button" 
                                  class="btn btn-sm btn-outline-warning" 
                                  data-bs-toggle="modal"
                                  data-bs-target="#statusModal-{{ $user->id }}"
                                  data-id="{{ $user->id }}"
                                  data-name="{{ $user->name }}"
                                  data-email="{{ $user->email }}"
                                  data-saldo="{{ $user->saldo }}"
                                  data-permission="{{ $user->permission }}"
                                  title="Enviar p/ Análise">
                                <i class="fa-solid fa-user"></i>
                              </button>
                            @else
                              <button type="button" 
                                  class="btn btn-sm btn-outline-success" 
                                  data-bs-toggle="modal"
                                  data-bs-target="#statusModal-{{ $user->id }}"
                                  data-id="{{ $user->id }}"
                                  data-name="{{ $user->name }}"
                                  data-email="{{ $user->email }}"
                                  data-saldo="{{ $user->saldo }}"
                                  data-permission="{{ $user->permission }}"
                                  title="Aprovar Usuário">
                                <i class="fa-solid fa-user-plus"></i>
                              </button>
                            @endif
                          </div>
                        </td>

                      <!-- Modal confirmar envio de senha -->
                    {{-- <div class="modal fade" id="resetarSenhaModal-{{ $user->id }}" tabindex="-1" aria-labelledby="resetarSenhaModalLabel-{{ $user->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="resetarSenhaModalLabel-{{ $user->id }}">Resetar senha</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <p>Deseja enviar uma nova senha para o usuário <span class="text-success">{{ $user->name }}</span>?</p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancelar</button>
                            <form method="POST" action="">
                              @csrf
                              <button type="submit" class="btn btn-warning">Resetar</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div> --}}

                    <div class="modal fade" id="statusModal-{{ $user->id }}" tabindex="-1" aria-labelledby="statusModalLabel-{{ $user->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="statusLabel-{{ $user->id }}">{{ $user->status == 1 ? 'Marcar como Em analise' : 'Aprovar cliente' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <p>Deseja {{ $user->status == 1 ? 'colocar em analise' : 'aprovar cliente' }} o cliente <span class="text-success">{{ $user->name }}</span>?</p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancelar</button>
                            <form method="POST" action="{{ route('gerencia.mudarstatus') }}">
                                  <input id="id" name="id" value="{{$user->id}}" hidden />
                                  <input id="tipo" name="tipo" value="status" hidden />
                                  @csrf
                                  @if ($user->status == 1)
                                  <button type="submit" name="reavaliar" value="true" class="btn btn-warning">
                                     <i class="fa-solid fa-user text-warning"></i>&nbsp;
                                    Colocar em Análise
                                    </button>
                                  @else
                                  <button type="submit" name="aprovar" value="true" class="btn btn-success">
                                     <i class="fa-solid fa-user text-success"></i>&nbsp;
                                    Aprovar Usuário
                                    </button>
                                  @endif
                                </form>
                          </div>
                        </div>
                      </div>
                    </div>

                      @endif

                    </tr>


                    @endforeach
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
    $("#table-listar-usuarios").DataTable({
      responsive: true,
      info:false,
      ordering: false,
      lengthChange: false,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
      },
      dom: '<"top"f>rt<"bottom"p><"clear">',
        initComplete: function() {
          // Muda o placeholder do input de busca
          $('#table-listar-usuarios_filter input[type="search"]').attr('placeholder', 'Pesquisar');
        }
    });

    // Solução completa para modais travados
    function limparModalBackdrop() {
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open');
      $('body').css({
        'overflow': '',
        'padding-right': ''
      });
    }

    // Monitorar quando modal abre
    $('.modal').on('show.bs.modal', function () {
      // Limpar qualquer backdrop anterior
      limparModalBackdrop();
    });

    // Monitorar quando modal fecha
    $('.modal').on('hidden.bs.modal', function () {
      limparModalBackdrop();
    });

    // Limpar backdrop ao fechar modal com ESC
    $(document).on('keydown', function(e) {
      if (e.keyCode === 27) { // ESC key
        limparModalBackdrop();
      }
    });

    // Limpar backdrop ao clicar fora do modal
    $(document).on('click', '.modal-backdrop', function() {
      limparModalBackdrop();
    });

    // Monitorar cliques nos botões de fechar
    $(document).on('click', '[data-bs-dismiss="modal"]', function() {
      setTimeout(function() {
        limparModalBackdrop();
      }, 100);
    });

    // Verificar periodicamente se há backdrop órfão
    setInterval(function() {
      if ($('.modal-backdrop').length > 0 && $('.modal.show').length === 0) {
        limparModalBackdrop();
      }
    }, 500);

  });
  </script>
  <script>

    function gerarChaveSecret(id){
      let chave = generateUUIDv4();
      document.getElementById(`e-secret-${id}`).value = chave;
    }

    function gerarChaveToken(id){
      let chave = generateUUIDv4();
      document.getElementById(`e-token-${id}`).value = chave;
    }

    function generateUUIDv4() {
      return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
        (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
      );
    }

  </script>
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
