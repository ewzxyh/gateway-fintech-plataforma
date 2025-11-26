@props([
  'user'
])



<div class="modal fade glassmorphism-modal" id="visModal-{{ $user->id }}" tabindex="-1" aria-labelledby="visModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="visModalLabel-{{ $user->id }}">Visualizar usuário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">

            <div class="row gy-4">
                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Usuario:</label>
                    <p>{{ $user->user_id }}</p>
                  </div>

                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Nome:</label>
                    <p>{{ $user->username }}</p>
                  </div>
                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Email:</label>
                    <p>{{ $user->email }}</p>
                  </div>
                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Razão Social:</label>
                    <p>{{ $user->razao_social }}</p>
                  </div>
                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Nome Fantasia:</label>
                    <p>{{ $user->razao_social }}</p>
                  </div>
                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">CPF/CNPJ:</label>
                    <p>{{ $user->cpf_cnpj }}</p>
                  </div>
                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">CPF</label>
                    <p>{{ $user->cpf }}</p>
                  </div>
                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Data de Nascimento:</label>
                    <p>{{ $user->data_nascimento }}</p>
                  </div>
                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Telefone:</label>
                    <p>{{ $user->telefone }}</p>
                  </div>
                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Status:</label>
                    <p class="{{ $user->status == 0 ? 'bg-warning-transparent text-warning' : ($user->status == 1 ? 'bg-success-transparent text-success' : ($user->status == 3 ? 'bg-danger-transparent text-danger' : ($user->status == 5 ? 'bg-warning-transparent text-warning' : 'bg-secondary text-dark'))) }} p-2 rounded">
                      {{ $user->status == 0 ? 'Pendente' : ($user->status == 1 ? 'Aprovado' : ($user->status == 3 ? 'Banido' : ($user->status == 5 ? 'Aguardando aprovação' : 'Status Desconhecido'))) }}
                    </p>
                  </div>

                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Token:</label>
                    <p >
                      {{ $user->chaves->token ?? '' }}
                    </p>
                  </div>

                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Secret:</label>
                    <p >
                      {{ $user->chaves->secret ?? '' }}
                    </p>
                  </div>

                  <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label">Data de Cadastro:</label>
                    <p>{{ \Carbon\Carbon::parse($user->data_cadastro)->format('d/m/Y H:i') }}</p>
                  </div>

                  <!-- Espaçamento adicional -->
                  <div class="col-12">
                    <div class="mb-3"></div>
                  </div>

                  <!-- Botões de Ação alinhados -->
                  <div class="col-12 col-sm-6 col-md-4">
                    <form method="POST" action="{{ route('admin.usuarios.mudarstatus') }}">
                      <input id="id" name="id" value="{{$user->id}}" hidden />
                      <input id="tipo" name="tipo" value="status" hidden />
                      @csrf
                      @if ($user->status == 1)
                      <button type="submit" name="reavaliar" class="btn btn-primary  btn-analysis-fixed w-100">
                        <i class="fas fa-file-alt me-2"></i>Enviar p/ Análise
                      </button>
                      @else
                      <button type="submit" name="aprovar" class="btn btn-primary btn-approve w-100" style="color:red">
                        <i class="fas fa-check me-2"></i>Aprovar Usuário
                      </button>
                      @endif
                    </form>
                  </div>
                  <div class="col-12 col-sm-6 col-md-4">
                    <form method="POST" action="{{ route('admin.usuarios.mudarstatus') }}">
                      <input id="id" name="id" value="{{$user->id}}" hidden />
                      <input id="tipo" name="tipo" value="banido" hidden />
                      @csrf
                      @if ($user->banido == 0)
                      <button type="submit" name="banir" class="btn btn-danger btn-ban-fixed w-100">
                        <i class="fas fa-ban me-2"></i>Banir Usuário
                      </button>
                      @else
                      <button type="submit" name="desbanir" class="btn btn-warning w-100" style="background-color: yellow !important">
                        <i class="fas fa-unlock me-2"></i>Desbanir Usuário
                      </button>
                      @endif
                    </form>
                  </div>
            </div>
            <!-- Contêiner para as fotos -->
            <div class="mt-4 row gy-4">
                  <div class="col-12">
                    <div class="card-title">
                      FOTOS DE DOCUMENTAÇÃO
                    </div>
                  </div>
                  @if($user->foto_rg_frente)
                  <div class="text-center col-12 col-sm-6 col-md-4">
                    <img src="{{ asset($user->foto_rg_frente) }}" alt="Foto de Frente RG" class="img-thumbnail" width="150" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#fotoFrenteModal{{ $user->id }}">
                  </div>
                  @endif
                  @if($user->foto_rg_verso)
                  <div class="text-center col-12 col-sm-6 col-md-4">
                    <img src="{{ asset($user->foto_rg_verso) }}" alt="Foto de Verso RG" class="img-thumbnail" width="150" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#fotoVersoModal{{ $user->id }}">
                  </div>
                  @endif
                  @if($user->selfie_rg)
                  <div class="text-center col-12 col-sm-6 col-md-4">
                    <img src="{{ asset($user->selfie_rg) }}" alt="Selfie RG" class="img-thumbnail" width="150" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#selfieModal{{ $user->id }}">
                  </div>
                  @endif
                  @if($user->contrato_social)
                  <div class="text-center col-12 col-sm-6 col-md-4">
                    <div class="position-relative">
                      @if(pathinfo($user->contrato_social, PATHINFO_EXTENSION) === 'pdf')
                        <div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; background-color: #f8f9fa; border: 2px dashed #dee2e6; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#contratoSocialModal{{ $user->id }}">
                          <div class="text-center">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                            <div class="small text-muted">Contrato Social</div>
                            <div class="small text-muted">(PDF)</div>
                          </div>
                        </div>
                      @else
                        <img src="{{ asset($user->contrato_social) }}" alt="Contrato Social" class="img-thumbnail" width="150" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#contratoSocialModal{{ $user->id }}">
                      @endif
                    </div>
                  </div>
                  @endif
            </div>
            <!-- Modais para exibir as fotos maiores -->

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Incluir modais das fotos -->
@include('admin.usuarios.partials.modal-frente-rg', ['user' => $user])
@include('admin.usuarios.partials.modal-verso-rg', ['user' => $user])
@include('admin.usuarios.partials.modal-selfie-rg', ['user' => $user])

<!-- Incluir modal do contrato social -->
@if($user->contrato_social)
  @include('admin.usuarios.partials.modal-contrato-social', ['user' => $user])
@endif
