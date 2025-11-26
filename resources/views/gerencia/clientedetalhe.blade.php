<x-app-layout :route="'[GERENCIA] Detalhes do cliente'">
  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Botão Voltar -->
      <div class="mb-3 row">
        <div class="col-12">
          <a href="{{ route('gerencia.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Voltar para Clientes
          </a>
        </div>
      </div>

      <!-- Exibição dos dados -->
      <div class="row">
        <div class="col-12">
          <div class="card card-raised">
            <div class="bg-white card-header justify-content-between">
              <div class="card-title">
                DADOS DO CLIENTE
              </div>
            </div>
            <div class="card-body">
              <!-- Dados do Usuário -->
              <div class="row gy-4">
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Usuario:</label>
                  <p>{{ $usuario->user_id }}</p>
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Nome:</label>
                  <p>{{ $usuario->name }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Email:</label>
                  <p>{{ $usuario->email }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Razão Social:</label>
                  <p>{{ $usuario->razao_social ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Nome Fantasia:</label>
                  <p>{{ $usuario->nome_fantasia ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">CPF/CNPJ:</label>
                  <p>{{ $usuario->cpf_cnpj ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">CPF</label>
                  <p>{{ $usuario->cpf ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Data de Nascimento:</label>
                  <p>{{ $usuario->data_nascimento ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Telefone:</label>
                  <p>{{ $usuario->telefone ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Status:</label>
                  <p>
                    @if($usuario->status == 0)
                      <span class="badge bg-danger">Pendente de documentação</span>
                    @elseif($usuario->status == 1)
                      <span class="badge bg-success">Aprovado</span>
                    @elseif($usuario->status == 5)
                      <span class="badge bg-warning">Aguardando aprovação</span>
                    @endif
                  </p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Data de Cadastro:</label>
                  <p>{{ $usuario->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Endereço -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card card-raised">
            <div class="bg-white card-header justify-content-between">
              <div class="card-title">
                ENDEREÇO
              </div>
            </div>
            <div class="card-body">
              <div class="row gy-4">
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">CEP:</label>
                  <p>{{ $usuario->cep ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Rua:</label>
                  <p>{{ $usuario->rua ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Número:</label>
                  <p>{{ $usuario->numero_residencia ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Bairro:</label>
                  <p>{{ $usuario->bairro ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Cidade:</label>
                  <p>{{ $usuario->cidade ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Estado:</label>
                  <p>{{ $usuario->estado ?? 'Não informado' }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Complemento:</label>
                  <p>{{ $usuario->complemento ?? 'Não informado' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Informações Financeiras -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card card-raised">
            <div class="bg-white card-header justify-content-between">
              <div class="card-title">
                INFORMAÇÕES FINANCEIRAS
              </div>
            </div>
            <div class="card-body">
              <div class="row gy-4">
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Saldo Atual:</label>
                  <p class="text-success fw-bold">R$ {{ number_format($usuario->saldo, 2, ',', '.') }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Total de Transações:</label>
                  <p>R$ {{ number_format($usuario->total_transacoes, 2, ',', '.') }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Volume Transacional:</label>
                  <p>R$ {{ number_format($usuario->volume_transacional, 2, ',', '.') }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Valor Sacado:</label>
                  <p>R$ {{ number_format($usuario->valor_sacado, 2, ',', '.') }}</p>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <label class="form-label">Valor Pago em Taxas:</label>
                  <p>R$ {{ number_format($usuario->valor_pago_taxa, 2, ',', '.') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ações do Gerente -->
      @if (auth()->user()->gerente_aprovar)
      <div class="row mt-4">
        <div class="col-12">
          <div class="card card-raised">
            <div class="bg-white card-header justify-content-between">
              <div class="card-title">
                AÇÕES DISPONÍVEIS
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-12 col-sm-6 col-md-4">
                  <form method="POST" action="{{ route('gerencia.mudarstatus') }}">
                    <input id="id" name="id" value="{{$usuario->id}}" hidden />
                    <input id="tipo" name="tipo" value="status" hidden />
                    @csrf
                    @if ($usuario->status == 1)
                    <button type="submit" name="reavaliar" class="btn btn-warning w-100">
                      <i class="fas fa-user-times me-2"></i>Enviar p/ Análise
                    </button>
                    @else
                    <button type="submit" name="aprovar" class="btn btn-success w-100">
                      <i class="fas fa-check me-2"></i>Aprovar Usuário
                    </button>
                    @endif
                  </form>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                  <form method="POST" action="{{ route('gerencia.mudarstatus') }}">
                    <input id="id" name="id" value="{{$usuario->id}}" hidden />
                    <input id="tipo" name="tipo" value="banido" hidden />
                    @csrf
                    @if ($usuario->banido == 1)
                    <button type="submit" class="btn btn-success w-100">
                      <i class="fas fa-user-check me-2"></i>Desbanir Usuário
                    </button>
                    @else
                    <button type="submit" class="btn btn-danger w-100">
                      <i class="fas fa-user-times me-2"></i>Banir Usuário
                    </button>
                    @endif
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif

      <!-- Taxas do Sistema -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card card-raised">
            <div class="bg-white card-header justify-content-between">
              <div class="card-title">
                TAXAS DO SISTEMA (CONFIGURAÇÃO GLOBAL)
              </div>
            </div>
            <div class="card-body">
              <div class="row gy-4">
                <!-- Taxa Flexível Ativa -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Taxa Flexível Ativa:</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ $setting->taxa_flexivel_ativa ? 'SIM' : 'NÃO' }}</strong>
                  </div>
                </div>

                <!-- Entrada Valores Baixos -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Entrada (Valores Baixos):</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>R$ {{ number_format($setting->taxa_flexivel_fixa_baixo ?? 0, 2, ',', '.') }} fixo</strong>
                  </div>
                </div>

                <!-- Entrada Valores Altos -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Entrada (Valores Altos):</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ number_format($setting->taxa_flexivel_percentual_alto ?? 0, 2) }}%</strong>
                  </div>
                </div>

                <!-- Valor Mínimo -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Valor Mínimo:</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>R$ {{ number_format($setting->taxa_flexivel_valor_minimo ?? 0, 2, ',', '.') }}</strong>
                  </div>
                </div>

                <!-- Saque Dashboard -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Saque via Dashboard:</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ number_format($setting->taxa_saque_api_padrao ?? 0, 2) }}%</strong>
                  </div>
                </div>

                <!-- Saque API -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Saque via API:</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ number_format($setting->taxa_saque_api_padrao ?? 0, 2) }}%</strong>
                  </div>
                </div>

                <!-- Saque Cripto -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Saque Cripto:</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ number_format($setting->taxa_saque_cripto_padrao ?? 0, 2) }}%</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Taxas Específicas do Usuário -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card card-raised">
            <div class="bg-white card-header justify-content-between">
              <div class="card-title">
                TAXAS ESPECÍFICAS DO USUÁRIO
                @if($usuario->taxas_personalizadas_ativas)
                  <span class="badge bg-success ms-2">ATIVAS</span>
                @else
                  <span class="badge bg-secondary ms-2">INATIVAS</span>
                @endif
              </div>
            </div>
            <div class="card-body">
              @if($usuario->taxas_personalizadas_ativas)
                <div class="alert alert-info">
                  <i class="fas fa-info-circle me-2"></i>
                  <strong>Taxas Personalizadas Ativas:</strong> Este usuário está usando essas taxas específicas em vez das taxas do sistema.
                </div>
              @else
                <div class="alert alert-warning">
                  <i class="fas fa-exclamation-triangle me-2"></i>
                  <strong>Taxas Personalizadas Inativas:</strong> Este usuário está usando as taxas do sistema. Edite as taxas abaixo para ativá-las.
                </div>
              @endif
              
              <div class="row gy-4">
                <!-- Taxa Fixa do Usuário -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Taxa Fixa do Usuário:</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>R$ {{ number_format($usuario->taxa_fixa_baixos ?? 0, 2, ',', '.') }}</strong>
                  </div>
                </div>

                <!-- Taxa Percentual do Usuário -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Taxa Percentual do Usuário:</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ number_format($usuario->taxa_percentual ?? 0, 2) }}%</strong>
                  </div>
                </div>

                <!-- Taxa PIX Personalizada -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Taxa PIX Personalizada (%):</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ number_format($usuario->taxa_percentual_pix ?? 0, 2) }}%</strong>
                  </div>
                </div>

                <!-- Taxa Mínima PIX Personalizada -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Taxa Mínima PIX Personalizada:</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>R$ {{ number_format($usuario->taxa_minima_pix ?? 0, 2, ',', '.') }}</strong>
                  </div>
                </div>

                <!-- Taxa Fixa PIX Personalizada -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Taxa Fixa PIX Personalizada:</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>R$ {{ number_format($usuario->taxa_fixa_pix ?? 0, 2, ',', '.') }}</strong>
                  </div>
                </div>

                <!-- Taxa Depósito Personalizada -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Taxa Depósito Personalizada (%):</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ number_format($usuario->taxa_percentual_deposito ?? 0, 2) }}%</strong>
                  </div>
                </div>

                <!-- Taxa Fixa Depósito Personalizada -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Taxa Fixa Depósito Personalizada:</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>R$ {{ number_format($usuario->taxa_fixa_deposito ?? 0, 2, ',', '.') }}</strong>
                  </div>
                </div>

                <!-- Taxa Saque API Personalizada -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Taxa Saque API Personalizada (%):</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ number_format($usuario->taxa_saque_api ?? 0, 2) }}%</strong>
                  </div>
                </div>

                <!-- Taxa Saque Crypto Personalizada -->
                <div class="col-12 col-md-6">
                  <label class="form-label">Taxa Saque Crypto Personalizada (%):</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ number_format($usuario->taxa_saque_crypto ?? 0, 2) }}%</strong>
                  </div>
                </div>

                <!-- Observações -->
                @if($usuario->observacoes_taxas)
                <div class="col-12">
                  <label class="form-label">Observações sobre Taxas:</label>
                  <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ $usuario->observacoes_taxas }}</strong>
                  </div>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Configuração de Taxas -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card card-raised">
            <div class="bg-white card-header justify-content-between">
              <div class="card-title">
                CONFIGURAÇÃO DE TAXAS
              </div>
            </div>
            <div class="card-body">
              <form method="POST" action="{{ route('gerencia.edit', $usuario->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="tipo" value="taxas">
                
                <div class="row gy-4">
                  <!-- Taxa Percentual Geral -->
                  <div class="col-12 col-md-6">
                    <label class="form-label">Taxa Percentual Geral (%):</label>
                    <input type="number" 
                        class="form-control" 
                        name="taxa_percentual" 
                        value="{{ $usuario->taxa_percentual ?? 0 }}" 
                        step="0.01" 
                        min="0" 
                        max="100">
                  </div>

                  <!-- Taxa Fixa Baixos -->
                  <div class="col-12 col-md-6">
                    <label class="form-label">Taxa Fixa Baixos (R$):</label>
                    <input type="number" 
                        class="form-control" 
                        name="taxa_fixa_baixos" 
                        value="{{ $usuario->taxa_fixa_baixos ?? 0 }}" 
                        step="0.01" 
                        min="0">
                  </div>

                  <!-- Taxa Percentual Altos -->
                  <div class="col-12 col-md-6">
                    <label class="form-label">Taxa Percentual Altos (%):</label>
                    <input type="number" 
                        class="form-control" 
                        name="taxa_percentual_altos" 
                        value="{{ $usuario->taxa_percentual_altos ?? 0 }}" 
                        step="0.01" 
                        min="0" 
                        max="100">
                  </div>

                  <!-- Valor Pago Taxa -->
                  <div class="col-12 col-md-6">
                    <label class="form-label">Valor Pago Taxa (R$):</label>
                    <input type="number" 
                        class="form-control" 
                        name="valor_pago_taxa" 
                        value="{{ $usuario->valor_pago_taxa ?? 0 }}" 
                        step="0.01" 
                        min="0">
                  </div>

                  <!-- Taxa PIX -->
                  <div class="col-12 col-md-6">
                    <label class="form-label">Taxa PIX (%):</label>
                    <input type="number" 
                        class="form-control" 
                        name="taxa_percentual_pix" 
                        value="{{ $usuario->taxa_percentual_pix ?? 0 }}" 
                        step="0.01" 
                        min="0" 
                        max="100">
                  </div>

                  <!-- Taxa Mínima PIX -->
                  <div class="col-12 col-md-6">
                    <label class="form-label">Taxa Mínima PIX (R$):</label>
                    <input type="number" 
                        class="form-control" 
                        name="taxa_minima_pix" 
                        value="{{ $usuario->taxa_minima_pix ?? 0 }}" 
                        step="0.01" 
                        min="0">
                  </div>

                  <!-- Taxa Fixa PIX -->
                  <div class="col-12 col-md-6">
                    <label class="form-label">Taxa Fixa PIX (R$):</label>
                    <input type="number" 
                        class="form-control" 
                        name="taxa_fixa_pix" 
                        value="{{ $usuario->taxa_fixa_pix ?? 0 }}" 
                        step="0.01" 
                        min="0">
                  </div>

                  <!-- Taxa Depósito -->
                  <div class="col-12 col-md-6">
                    <label class="form-label">Taxa Depósito (%):</label>
                    <input type="number" 
                        class="form-control" 
                        name="taxa_percentual_deposito" 
                        value="{{ $usuario->taxa_percentual_deposito ?? 0 }}" 
                        step="0.01" 
                        min="0" 
                        max="100">
                  </div>

                  <!-- Taxa Fixa Depósito -->
                  <div class="col-12 col-md-6">
                    <label class="form-label">Taxa Fixa Depósito (R$):</label>
                    <input type="number" 
                        class="form-control" 
                        name="taxa_fixa_deposito" 
                        value="{{ $usuario->taxa_fixa_deposito ?? 0 }}" 
                        step="0.01" 
                        min="0">
                  </div>

                  <!-- Taxa Saque API -->
                  <div class="col-12 col-md-6">
                    <label class="form-label">Taxa Saque API (%):</label>
                    <input type="number" 
                        class="form-control" 
                        name="taxa_saque_api" 
                        value="{{ $usuario->taxa_saque_api ?? 0 }}" 
                        step="0.01" 
                        min="0" 
                        max="100">
                  </div>

                  <!-- Taxa Saque Crypto -->
                  <div class="col-12 col-md-6">
                    <label class="form-label">Taxa Saque Crypto (%):</label>
                    <input type="number" 
                        class="form-control" 
                        name="taxa_saque_crypto" 
                        value="{{ $usuario->taxa_saque_crypto ?? 0 }}" 
                        step="0.01" 
                        min="0" 
                        max="100">
                  </div>

                  <!-- Observações -->
                  <div class="col-12">
                    <label class="form-label">Observações sobre Taxas:</label>
                    <textarea class="form-control" 
                         name="observacoes_taxas" 
                         rows="3" 
                         placeholder="Observações sobre as taxas configuradas...">{{ $usuario->observacoes_taxas ?? '' }}</textarea>
                  </div>
                </div>

              <div class="row mt-4">
                <div class="col-12 col-md-6">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Salvar Configurações de Taxas
                  </button>
                </div>
                @if($usuario->taxas_personalizadas_ativas)
                <div class="col-12 col-md-6">
                  <!-- Botão para desativar taxas personalizadas -->
                  <button type="button" 
                      class="btn btn-outline-warning"
                      onclick="desativarTaxasPersonalizadas()">
                    <i class="fas fa-times me-2"></i>Desativar Taxas Personalizadas
                  </button>
                </div>
                @endif
              </div>
              </form>
              
              <!-- Formulário oculto para desativar taxas personalizadas -->
              <form id="formDesativarTaxas" method="POST" action="{{ route('gerencia.edit', $usuario->id) }}" style="display: none;">
                @csrf
                @method('PUT')
                <input type="hidden" name="tipo" value="desativar_taxas">
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Fotos de Documentação -->
      <div class="row">
        <div class="col-12">
          <div class="card card-raised">
            <div class="bg-white card-header justify-content-between">
              <div class="card-title">
                FOTOS DE DOCUMENTAÇÃO
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                @if($usuario->foto_rg_frente)
                <div class="text-center col-12 col-sm-6 col-md-4">
                  <img src="{{ asset($usuario->foto_rg_frente) }}" alt="Foto de Frente RG" class="img-thumbnail" width="150" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#fotoFrenteModal{{ $usuario->id }}">
                </div>
                @endif
                @if($usuario->foto_rg_verso)
                <div class="text-center col-12 col-sm-6 col-md-4">
                  <img src="{{ asset($usuario->foto_rg_verso) }}" alt="Foto de Verso RG" class="img-thumbnail" width="150" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#fotoVersoModal{{ $usuario->id }}">
                </div>
                @endif
                @if($usuario->selfie_rg)
                <div class="text-center col-12 col-sm-6 col-md-4">
                  <img src="{{ asset($usuario->selfie_rg) }}" alt="Selfie RG" class="img-thumbnail" width="150" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#selfieModal{{ $usuario->id }}">
                </div>
                @endif
                @if($usuario->contrato_social)
                <div class="text-center col-12 col-sm-6 col-md-4">
                  <div class="position-relative">
                    @if(pathinfo($usuario->contrato_social, PATHINFO_EXTENSION) === 'pdf')
                      <div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; background-color: #f8f9fa; border: 2px dashed #dee2e6; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#contratoSocialModal{{ $usuario->id }}">
                        <div class="text-center">
                          <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                          <div class="small text-muted">Contrato Social</div>
                          <div class="small text-muted">(PDF)</div>
                        </div>
                      </div>
                    @else
                      <img src="{{ asset($usuario->contrato_social) }}" alt="Contrato Social" class="img-thumbnail" width="150" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#contratoSocialModal{{ $usuario->id }}">
                    @endif
                  </div>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modais de Documentação -->
      @include('admin.usuarios.partials.modal-frente-rg', ['user' => $usuario])
      @include('admin.usuarios.partials.modal-verso-rg', ['user' => $usuario])
      @include('admin.usuarios.partials.modal-selfie-rg', ['user' => $usuario])
      @include('admin.usuarios.partials.modal-contrato-social', ['user' => $usuario])

    </div>
  </div>
</x-app-layout>

<script>
function desativarTaxasPersonalizadas() {
  if (confirm('Tem certeza que deseja desativar as taxas personalizadas? O usuário voltará a usar as taxas do sistema.')) {
    document.getElementById('formDesativarTaxas').submit();
  }
}
</script>
