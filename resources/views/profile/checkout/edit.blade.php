<x-app-layout :route="'Check-out'">
@php
$adquirente = \App\Models\Adquirente::where('status', 1)->first();
$adquirencia = $adquirente ? $adquirente->referencia : null;
$meios = $adquirencia == 'efi';

$metodos = json_decode($checkout->methods) ?? [];

@endphp

  <div class="main-content app-content">
    <div class="container-fluid">
      <!-- Page Header -->
      <div class="row mt-4 mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h1 class="display-5 mb-1">Editar Produto</h1>
              <p class="text-muted mb-0">{{ $checkout->produto_name }}</p>
            </div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='/produtos'">
                <i class="fa-solid fa-chevron-left me-1"></i>Voltar
              </button>
              <button type="submit" form="form-checkout-completo" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Salvar alterações
              </button>
            </div>
          </div>
        </div>
      </div>

      <form id="form-checkout-completo" method="POST" action="{{ route('profile.checkout.produto.editar', ['id' => $checkout->id ]) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs">
                  <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#geral"
                    type="button" role="tab" aria-controls="geral" aria-selected="true">
                      <i class="fa-solid fa-info-circle me-1"></i>Geral
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#configuracoes"
                    type="button" role="tab" aria-controls="configuracoes" aria-selected="false">
                      <i class="fa-solid fa-cog me-1"></i>Configurações
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#checkouts"
                    type="button" role="tab" aria-controls="checkouts" aria-selected="false">
                      <i class="fa-solid fa-paint-brush me-1"></i>Checkouts V1
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#checkouts-builder"
                    type="button" role="tab" aria-controls="checkouts-builder" aria-selected="false">
                      <i class="fa-solid fa-shopping-cart me-1"></i>Checkouts
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#orderbumps"
                    type="button" role="tab" aria-controls="orderbumps" aria-selected="false">
                      <i class="fa-solid fa-plus-circle me-1"></i>Order Bump
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#links"
                    type="button" role="tab" aria-controls="links" aria-selected="false">
                      <i class="fa-solid fa-link me-1"></i>Links
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#ads"
                    type="button" role="tab" aria-controls="ads" aria-selected="false">
                      <i class="fa-solid fa-bullhorn me-1"></i>ADS
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#orders"
                    type="button" role="tab" aria-controls="orders" aria-selected="false">
                      <i class="fa-solid fa-shopping-cart me-1"></i>Pedidos
                    </a>
                  </li>
                </ul>
              </div>


            <!-- Tab panes -->
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane show active" id="geral" role="tabpanel" aria-labelledby="geral-tab">
                <div class="card-body p-4">
                  <div class="row g-4">
                    <!-- Coluna Esquerda -->
                    <div class="col-lg-6">
                      <div class="mb-4">
                        <label for="produto_name" class="form-label fw-semibold">
                          <i class="fa-solid fa-tag text-primary me-2"></i>Nome do Produto
                        </label>
                        <input autofocus type="text" class="form-control @error('produto_name') is-invalid @enderror" name="produto_name" value="{{ $checkout->produto_name }}" placeholder="Digite o nome do produto">
                        @error('produto_name')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                      
                      <div class="mb-4">
                        <label for="produto_valor" class="form-label fw-semibold">
                          <i class="fa-solid fa-dollar-sign text-success me-2"></i>Valor do Produto
                        </label>
                        <div class="input-group">
                          <span class="input-group-text">R$</span>
                          <input type="number" step="0.01" min="0.01" class="form-control @error('produto_valor') is-invalid @enderror" name="produto_valor" value="{{ $checkout->produto_valor }}" placeholder="0.00"
                            oninput="this.value = this.value.replace(/[^0-9.,]/g, '').replace(/,/g, '.')">
                        </div>
                        @error('produto_valor')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                      
                      <div class="mb-4">
                        <label for="produto_descricao" class="form-label fw-semibold">
                          <i class="fa-solid fa-align-left text-info me-2"></i>Descrição do Produto
                        </label>
                        <textarea class="form-control @error('produto_descricao') is-invalid @enderror" name="produto_descricao" rows="4" placeholder="Descreva seu produto...">{{ $checkout->produto_descricao }}</textarea>
                        @error('produto_descricao')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="mb-4">
                        <label class="form-label fw-semibold" for="produto_categoria">
                          <i class="fa-solid fa-list text-warning me-2"></i>Categoria do Produto
                        </label>
                        <select class="form-select @error('produto_categoria') is-invalid @enderror" name="produto_categoria" id="produto_categoria">
                          <option value="0" {{ $checkout->produto_categoria =="0" ?"selected" :"" }}>Selecione a Categoria</option>
                            <option value="1"> {{ $checkout->produto_categoria =="1" ?"selected" :"" }}Administração e Negócios</option>
                            <option value="2"> {{ $checkout->produto_categoria =="2" ?"selected" :"" }}Animais de Estimação</option>
                            <option value="3"> {{ $checkout->produto_categoria =="3" ?"selected" :"" }}Arquitetura e Engenharia</option>
                            <option value="4"> {{ $checkout->produto_categoria =="4" ?"selected" :"" }}Artes e Música</option>
                            <option value="5"> {{ $checkout->produto_categoria =="5" ?"selected" :"" }}Auto-ajuda e Desenvolvimento Pessoal</option>
                            <option value="6"> {{ $checkout->produto_categoria =="6" ?"selected" :"" }}Automóveis</option>
                            <option value="7"> {{ $checkout->produto_categoria =="7" ?"selected" :"" }}Blogs e Redes Sociais</option>
                            <option value="8"> {{ $checkout->produto_categoria =="8" ?"selected" :"" }}Casa e Jardinagem</option>
                            <option value="9"> {{ $checkout->produto_categoria =="9" ?"selected" :"" }}Culinária, Gastronomia, Receitas</option>
                            <option value="10" {{ $checkout->produto_categoria =="10" ?"selected" :"" }}>Design e Templates PSD, PPT ou HTML</option>
                            <option value="11" {{ $checkout->produto_categoria =="11" ?"selected" :"" }}>Edição de Áudio, Vídeo ou Imagens</option>
                            <option value="12" {{ $checkout->produto_categoria =="12" ?"selected" :"" }}>Educacional, Cursos Técnicos e Profissionalizantes</option>
                            <option value="13" {{ $checkout->produto_categoria =="13" ?"selected" :"" }}>Entretenimento, Lazer e Diversão</option>
                            <option value="14" {{ $checkout->produto_categoria =="14" ?"selected" :"" }}>Esportes e Fitness</option>
                            <option value="15" {{ $checkout->produto_categoria =="15" ?"selected" :"" }}>Filmes e Cinema</option>
                            <option value="16" {{ $checkout->produto_categoria =="16" ?"selected" :"" }}>Finanças</option>
                            <option value="17" {{ $checkout->produto_categoria =="17" ?"selected" :"" }}>Geral</option>
                            <option value="18" {{ $checkout->produto_categoria =="18" ?"selected" :"" }}>Histórias em Quadrinhos</option>
                            <option value="19" {{ $checkout->produto_categoria =="19" ?"selected" :"" }}>Idiomas</option>
                            <option value="20" {{ $checkout->produto_categoria =="20" ?"selected" :"" }}>Informática</option>
                            <option value="21" {{ $checkout->produto_categoria =="21" ?"selected" :"" }}>Internet Marketing</option>
                            <option value="22" {{ $checkout->produto_categoria =="22" ?"selected" :"" }}>Investimentos e Finanças</option>
                            <option value="23" {{ $checkout->produto_categoria =="23" ?"selected" :"" }}>Jogos de Cartas, Poker, Loterias</option>
                            <option value="24" {{ $checkout->produto_categoria =="24" ?"selected" :"" }}>Jogos de Computador, Jogos Online</option>
                            <option value="25" {{ $checkout->produto_categoria =="25" ?"selected" :"" }}>Jurídico</option>
                            <option value="26" {{ $checkout->produto_categoria =="26" ?"selected" :"" }}>Literatura e Poesia</option>
                            <option value="27" {{ $checkout->produto_categoria =="27" ?"selected" :"" }}>Marketing de Rede</option>
                            <option value="28" {{ $checkout->produto_categoria =="28" ?"selected" :"" }}>Marketing e Comunicação</option>
                            <option value="29" {{ $checkout->produto_categoria =="29" ?"selected" :"" }}>Plantas, Meio Ambiente</option>
                            <option value="30" {{ $checkout->produto_categoria =="30" ?"selected" :"" }}>Moda e vestuário</option>
                            <option value="31" {{ $checkout->produto_categoria =="31" ?"selected" :"" }}>Música, Bandas e Shows</option>
                            <option value="32" {{ $checkout->produto_categoria =="32" ?"selected" :"" }}>Paquera, Sedução e Relacionamentos</option>
                            <option value="33" {{ $checkout->produto_categoria =="33" ?"selected" :"" }}>Pessoas com deficiência</option>
                            <option value="34" {{ $checkout->produto_categoria =="34" ?"selected" :"" }}>Plugins, Widgets e Extensões</option>
                            <option value="35" {{ $checkout->produto_categoria =="35" ?"selected" :"" }}>Produtividade e Organização Pessoal</option>
                            <option value="36" {{ $checkout->produto_categoria =="36" ?"selected" :"" }}>Produtos infantis</option>
                            <option value="37" {{ $checkout->produto_categoria =="37" ?"selected" :"" }}>Relatórios, Artigos e Pesquisas</option>
                            <option value="38" {{ $checkout->produto_categoria =="38" ?"selected" :"" }}>Religião e Crenças</option>
                            <option value="39" {{ $checkout->produto_categoria =="39" ?"selected" :"" }}>Renda Extra</option>
                            <option value="40" {{ $checkout->produto_categoria =="40" ?"selected" :"" }}>Romances, Dramas, Estórias e Contos</option>
                            <option value="41" {{ $checkout->produto_categoria =="41" ?"selected" :"" }}>Saúde, Bem-estar e Beleza</option>
                            <option value="42" {{ $checkout->produto_categoria =="42" ?"selected" :"" }}>Scripts</option>
                            <option value="43" {{ $checkout->produto_categoria =="43" ?"selected" :"" }}>Segurança do Trabalho</option>
                            <option value="44" {{ $checkout->produto_categoria =="44" ?"selected" :"" }}>Sexologia e Sexualidade</option>
                            <option value="45" {{ $checkout->produto_categoria =="45" ?"selected" :"" }}>Snippets (Trechos de Vídeo)</option>
                            <option value="46" {{ $checkout->produto_categoria =="46" ?"selected" :"" }}>Turismo</option>
                        </select>
                        @error('produto_categoria')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                      
                      <div class="mb-4">
                        <label for="produto_tipo" class="form-label fw-semibold">
                          <i class="fa-solid fa-box text-primary me-2"></i>Tipo do Produto
                        </label>
                        <select class="form-select @error('produto_tipo') is-invalid @enderror" name="produto_tipo" value="{{ $checkout->produto_tipo }}" readonly>
                          <option value="info" {{ $checkout->produto_tipo == 'info' ? 'selected' : '' }}>Info Produto</option>
                          <option value="fisico" {{ $checkout->produto_tipo == 'fisico' ? 'selected' : '' }}>Produto Físico</option>
                        </select>
                        @error('produto_tipo')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                      
                      <div class="mb-4">
                        <label for="produto_tipo_cob" class="form-label fw-semibold">
                          <i class="fa-solid fa-sync text-success me-2"></i>Tipo de Cobrança
                        </label>
                        <select class="form-select @error('produto_tipo_cob') is-invalid @enderror" name="produto_tipo_cob" value="{{ $checkout->produto_tipo_cob }}" readonly>
                          <option value="unico">Único</option>
                          <option value="recorrente">Recorrente</option>
                        </select>
                        @error('produto_tipo_cob')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                      
                      <div class="mb-4">
                        <label for="url_pagina_vendas" class="form-label fw-semibold">
                          <i class="fa-solid fa-check-circle text-success me-2"></i>Página de Obrigado
                        </label>
                        
                        <!-- Botão Gerador de Upsell -->
                        <div class="mb-3">
                          <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#upsellGeneratorModal">
                            <i class="fa-solid fa-magic-wand-sparkles me-1"></i>Gerador de Upsell
                          </button>
                        </div>
                        
                        <!-- Opção para usar página padrão -->
                        <div class="mb-2">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="use_default_thank_you" 
                                {{ empty($checkout->url_pagina_vendas) || str_contains($checkout->url_pagina_vendas, '/obrigado?order_id=') ? 'checked' : '' }}>
                            <label class="form-check-label" for="use_default_thank_you">
                              <i class="fas fa-check-circle text-success me-1"></i>
                              Usar página de obrigado padrão do sistema
                            </label>
                          </div>
                          <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            A página padrão inclui confirmação de pagamento, resumo da compra, próximos passos e suporte.
                          </small>
                        </div>
                        
                        <!-- Campo de URL personalizada -->
                        <div id="custom_url_section" style="{{ empty($checkout->url_pagina_vendas) || str_contains($checkout->url_pagina_vendas, '/obrigado?order_id=') ? 'display: none;' : '' }}">
                          <input type="text" class="form-control @error('url_pagina_vendas') is-invalid @enderror" 
                              name="url_pagina_vendas" id="url_pagina_vendas" 
                              value="{{ $checkout->url_pagina_vendas }}" 
                              placeholder="https://exemplo.com/obrigado">
                          <small class="text-muted d-block mt-1">
                            <i class="fas fa-link me-1"></i>
                            Digite a URL completa da sua página de obrigado personalizada
                          </small>
                        </div>
                        
                        <!-- Campo hidden para página padrão -->
                        <input type="hidden" name="url_pagina_vendas_default" id="url_pagina_vendas_default" 
                            value="{{ url('/obrigado?order_id=ORDER_ID') }}">
                        
                        @error('url_pagina_vendas')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                    </div>
                    
                    <!-- Coluna Direita -->
                    <div class="col-lg-6">
                      <div class="mb-4">
                        <label class="form-label fw-semibold">
                          <i class="fa-solid fa-image text-primary me-2"></i>Imagem do Produto
                        </label>
                        <x-image-upload id="produto_image" name="produto_image" label="" :height="'200px'" :value="$checkout->produto_image" />
                        @error('produto_image')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                      
                      <div class="mb-4">
                        <label for="periodo_garantia" class="form-label fw-semibold">
                          <i class="fa-solid fa-shield-alt text-success me-2"></i>Período de Garantia
                        </label>
                        <div class="input-group">
                          <input type="number" value="7" class="form-control @error('periodo_garantia') is-invalid @enderror" name="periodo_garantia" value="{{ $checkout->periodo_garantia }}" placeholder="7">
                          <span class="input-group-text">dias</span>
                        </div>
                        @error('periodo_garantia')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                      
                      <div class="mb-4">
                        <label for="whatsapp_suporte" class="form-label fw-semibold">
                          <i class="fa-brands fa-whatsapp text-success me-2"></i>WhatsApp de Suporte
                        </label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="fa-brands fa-whatsapp"></i>
                          </span>
                          <input type="text" class="form-control @error('whatsapp_suporte') is-invalid @enderror" name="whatsapp_suporte" value="{{ $checkout->whatsapp_suporte }}" placeholder="(00) 00000-0000">
                        </div>
                        @error('whatsapp_suporte')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                      
                      <div class="mb-4">
                        <label for="email_suporte" class="form-label fw-semibold">
                          <i class="fa-solid fa-envelope text-info me-2"></i>Email de Suporte
                        </label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="fa-solid fa-envelope"></i>
                          </span>
                          <input type="email" class="form-control @error('email_suporte') is-invalid @enderror" name="email_suporte" value="{{ $checkout->email_suporte }}" placeholder="redacted@example.invalid">
                        </div>
                        @error('email_suporte')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                      
                      <div class="mb-4">
                        <label for="descricao_extra" class="form-label fw-semibold">
                          <i class="fa-solid fa-truck text-warning me-2"></i>Entregável e Forma de Entrega
                        </label>
                        <textarea class="form-control @error('descricao_extra') is-invalid @enderror" name="descricao_extra" rows="4" placeholder="Descreva o que será entregue e como...">{{ $checkout->descricao_extra }}</textarea>
                        @error('descricao_extra')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            <!-- Nova Aba Configurações -->
            <div class="tab-pane" id="configuracoes" role="tabpanel" aria-labelledby="configuracoes-tab">
              <div class="card-body p-4">
                <div class="row">
                  <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                      <div>
                        <h5 class="mb-1 fw-bold">
                          <i class="fa-solid fa-cog text-primary me-2"></i>Configurações de Pagamento
                        </h5>
                        <p class="text-muted mb-0 small">Configure as opções de pagamentos aceitos</p>
                      </div>
                    </div>
                    
                    <!-- Seção de Métodos de Pagamento -->
                    <div class="mb-4">
                      <label class="form-label fw-semibold">
                        <i class="fa-solid fa-credit-card text-info me-2"></i>Métodos de Pagamento <span class="text-danger">*</span>
                      </label>
                      
                      <div class="row g-3" id="payment-methods-container">
                        <!-- PIX Card -->
                        <div class="col-md-4">
                          <div class="card payment-method-card {{ in_array('pix', $metodos) ? 'border-primary' : 'border-light' }}" 
                               style="cursor: pointer; transition: all 0.3s ease;">
                            <div class="card-body text-center p-4">
                              <div class="mb-3">
                                <i class="fa-brands fa-pix" style="font-size: 2.5rem;"></i>
                              </div>
                              <h6 class="card-title fw-bold mb-2">PIX</h6>
                              <p class="text-muted small mb-3">Valor líquido: R$ 0,00</p>
                              
                              <input type="checkbox" class="btn-check" id="method_pix" name="methods[]" value="pix" autocomplete="off" 
                                     @if(in_array('pix', $metodos)) checked @endif>
                              <label class="btn {{ in_array('pix', $metodos) ? 'btn-primary' : 'btn-outline-primary' }} btn-sm" 
                                     for="method_pix">
                                {{ in_array('pix', $metodos) ? 'Método padrão' : 'Selecionar' }}
                              </label>
                            </div>
                          </div>
                        </div>

                        <!-- Cartão Card -->
                        <div class="col-md-4">
                          <div class="card payment-method-card {{ in_array('card', $metodos) ? 'border-primary' : 'border-light' }}" 
                               style="cursor: pointer; transition: all 0.3s ease;">
                            <div class="card-body text-center p-4">
                              <div class="mb-3">
                                <i class="fa-solid fa-credit-card" style="font-size: 2.5rem;"></i>
                              </div>
                              <h6 class="card-title fw-bold mb-2">Cartão</h6>
                              <p class="text-muted small mb-3">Valor líquido: R$ 0,00</p>
                              
                              <input type="checkbox" class="btn-check" id="method_cartao" name="methods[]" value="card" autocomplete="off" 
                                     @if(in_array('card', $metodos)) checked @endif>
                              <label class="btn {{ in_array('card', $metodos) ? 'btn-primary' : 'btn-outline-primary' }} btn-sm" 
                                     for="method_cartao">
                                {{ in_array('card', $metodos) ? 'Método padrão' : 'Selecionar' }}
                              </label>
                            </div>
                          </div>
                        </div>

                        <!-- Boleto Card -->
                        <div class="col-md-4">
                          <div class="card payment-method-card {{ in_array('billet', $metodos) ? 'border-primary' : 'border-light' }}" 
                               style="cursor: pointer; transition: all 0.3s ease;">
                            <div class="card-body text-center p-4">
                              <div class="mb-3">
                                <i class="fa-solid fa-barcode" style="font-size: 2.5rem;"></i>
                              </div>
                              <h6 class="card-title fw-bold mb-2">Boleto</h6>
                              <p class="text-muted small mb-3">Valor líquido: R$ 0,00</p>
                              
                              <input type="checkbox" class="btn-check" id="method_boleto" name="methods[]" value="billet" autocomplete="off" 
                                     @if(in_array('billet', $metodos)) checked @endif>
                              <label class="btn {{ in_array('billet', $metodos) ? 'btn-primary' : 'btn-outline-primary' }} btn-sm" 
                                     for="method_boleto">
                                {{ in_array('billet', $metodos) ? 'Método padrão' : 'Selecionar' }}
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <small class="text-muted d-block mt-3">
                        <i class="fa-solid fa-info-circle me-1"></i>Selecione os métodos de pagamento que estarão disponíveis para seus clientes.
                      </small>
                      <div id="payment-validation-message" class="mt-2" style="display: none;">
                        <small class="text-danger">
                          <i class="fa-solid fa-exclamation-triangle me-1"></i>
                          É obrigatório selecionar pelo menos um método de pagamento.
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane" id="checkouts" role="tabpanel" aria-labelledby="checkouts-tab">
              <div class="card-body p-0">
                @include('profile.checkout.components.checkout', ['checkout' => $checkout])
              </div>
            </div>


            <!-- Nova Aba Checkouts Builder -->
            <div class="tab-pane" id="checkouts-builder" role="tabpanel" aria-labelledby="checkouts-builder-tab">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <div>
                    <h5 class="mb-1 fw-bold">
                      <i class="fa-solid fa-shopping-cart text-primary me-2"></i>Gerenciador de Checkouts
                    </h5>
                    <p class="text-muted mb-0 small">Gerencie e personalize seus checkouts</p>
                  </div>
                  <button type="button" class="btn btn-primary" onclick="window.open('/checkout-builder/{{ $checkout->id_unico }}', '_blank')">
                    <i class="fa-solid fa-plus me-1"></i>Adicionar Checkout
                  </button>
                </div>
                
                <!-- Barra de Pesquisa -->
                <div class="row mb-4">
                  <div class="col-md-8">
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="fa-solid fa-search"></i>
                      </span>
                      <input type="text" class="form-control" placeholder="Pesquisar..." id="searchCheckouts">
                    </div>
                  </div>
                </div>

                <!-- Lista de Checkouts - DataTable Simples -->
                <div class="card">
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table id="checkoutsTable" class="table table-hover mb-0">
                        <thead class="table-light">
                          <tr>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Visitas</th>
                            <th>Oferta</th>
                            <th class="text-center">Ações</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                  <i class="fas fa-shopping-cart text-primary" style="font-size: 16px;"></i>
                                </div>
                                <div>
                                  <strong>Checkout Principal</strong>
                                  <br>
                                  <span class="badge bg-success">Padrão</span>
                                </div>
                              </div>
                            </td>
                            <td>
                              <span class="fw-bold text-success">R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}</span>
                            </td>
                            <td>
                              <span class="fw-semibold">{{ $checkout->visitas ?? 0 }}</span>
                              <br>
                              <small class="text-muted">visualizações</small>
                            </td>
                            <td>
                              <span class="badge bg-primary">{{ $checkout->produto_name }}</span>
                            </td>
                            <td class="text-center">
                              <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openCheckoutBuilder('{{ $checkout->id_unico }}')" title="Personalizar">
                                  <i class="fa-solid fa-wand-magic-sparkles"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="duplicateCheckout('{{ $checkout->id_unico }}')" title="Duplicar">
                                  <i class="fa-solid fa-copy"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="configureCheckout('{{ $checkout->id_unico }}')" title="Configurações">
                                  <i class="fa-solid fa-cog"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCheckout('{{ $checkout->id_unico }}')" title="Deletar">
                                  <i class="fa-solid fa-trash"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <!-- Mensagem quando não há checkouts -->
                <div id="noCheckoutsMessage" class="text-center py-5" style="display: none;">
                  <div class="text-muted">
                    <i class="fa-solid fa-shopping-cart fa-3x mb-3 d-block"></i>
                    <h5>Nenhum checkout encontrado</h5>
                    <p class="mb-0">Clique em "Adicionar Checkout" para criar seu primeiro checkout personalizado.</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane" id="orderbumps" role="tabpanel" aria-labelledby="orderbumps-tab">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <div>
                    <h5 class="mb-1 fw-bold">
                      <i class="fa-solid fa-plus-circle text-primary me-2"></i>Order Bumps
                    </h5>
                    <p class="text-muted mb-0 small">Adicione ofertas complementares ao checkout</p>
                  </div>
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderbump">
                    <i class="fa-solid fa-plus me-1"></i>Adicionar
                  </button>
                </div>
                
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead class="table-light">
                     <tr>
                      <th scope="col">Imagem</th>
                      <th scope="col">Nome</th>
                      <th scope="col">Descrição</th>
                      <th scope="col">Valor De</th>
                      <th scope="col">Valor Por</th>
                      <th scope="col">Status</th>
                      <th scope="col" class="text-end">Ações</th>
                     </tr>
                    </thead>
                    <tbody>
                      @forelse($checkout->allBumps as $bump)
                      <tr>
                        <td>
                          <img class="rounded" style="width:48px;height:48px;object-fit:cover;" src="{{ $bump->image }}" alt="{{ $bump->nome }}">
                        </td>
                        <td class="fw-semibold">{{ $bump->nome }}</td>
                        <td>
                          <small class="text-muted">{{ Str::limit($bump->descricao, 50) }}</small>
                        </td>
                        <td><span class="text-muted text-decoration-line-through">R$ {{ number_format($bump->valor_de, 2, ',', '.') }}</span></td>
                        <td><span class="text-success fw-bold">R$ {{ number_format($bump->valor_por, 2, ',', '.') }}</span></td>
                        <td>
                          @if($bump->ativo == 1)
                            <span class="badge bg-success">Ativo</span>
                          @else
                            <span class="badge bg-secondary">Inativo</span>
                          @endif
                        </td>
                        <td class="text-end">
                          <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-orderbump"
                              data-id="{{ $bump->id }}"
                              data-nome="{{ $bump->nome }}"
                              data-descricao="{{ $bump->descricao }}"
                              data-valor-de="{{ $bump->valor_de }}"
                              data-valor-por="{{ $bump->valor_por }}"
                              data-ativo="{{ $bump->ativo }}"
                              data-image="{{ $bump->image }}"
                              data-bs-toggle="modal"
                              data-bs-target="#editOrderbump"
                              title="Editar">
                              <i class="fa-solid fa-pencil"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-orderbump"
                              data-id="{{ $bump->id }}"
                              data-nome="{{ $bump->nome }}"
                              data-bs-toggle="modal"
                              data-bs-target="#removeOrderbump"
                              title="Remover">
                              <i class="fa-solid fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="7" class="text-center py-5">
                          <div class="text-muted">
                            <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                            <h5>Nenhum Order Bump cadastrado</h5>
                            <p class="mb-0">Clique em"Adicionar" para criar sua primeira oferta.</p>
                          </div>
                        </td>
                      </tr>
                      @endforelse
                    </tbody>
                   </table>
                  </div>
                </div>
            </div>
            <div class="tab-pane" id="links" role="tabpanel" aria-labelledby="links-tab">
              <div class="card-body p-4">
                <div class="mb-4">
                  <h5 class="mb-1 fw-bold">
                    <i class="fa-solid fa-link text-primary me-2"></i>Link do Checkout
                  </h5>
                  <p class="text-muted mb-0 small">Compartilhe este link para receber pagamentos</p>
                </div>
                
                <div class="card bg-light mb-4">
                  <div class="card-body p-4">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Produto</small>
                        <strong>{{ $checkout->produto_name }}</strong>
                      </div>
                      <div class="col-md-2">
                        <small class="text-muted d-block mb-1">Tipo</small>
                        <span class="badge bg-primary">{{ ucfirst($checkout->produto_tipo) }}</span>
                      </div>
                      <div class="col-md-2">
                        <small class="text-muted d-block mb-1">Cobrança</small>
                        <span class="badge bg-primary">{{ ucfirst($checkout->produto_tipo_cob) }}</span>
                      </div>
                      <div class="col-md-2">
                        <small class="text-muted d-block mb-1">Valor</small>
                        <strong class="text-success">R$ {{ number_format($checkout->produto_valor, 2, ',', '.') }}</strong>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Checkout V1 -->
                <div class="card border-primary mb-3">
                  <div class="card-header bg-primary text-white">
                    <i class="fa-solid fa-paint-brush me-2"></i>
                    <strong>Checkout V1</strong> (Padrão/Nativo)
                  </div>
                  <div class="card-body p-4">
                    <label class="form-label fw-semibold mb-3">
                      <i class="fa-solid fa-external-link-alt text-primary me-2"></i>URL do Checkout V1
                    </label>
                    <div class="input-group input-group-lg">
                      <input type="text" id="link-checkout-v1" class="form-control" value="{{ env('APP_URL').'/checkout/produto/v1/'.$checkout->id_unico }}" readonly>
                      <button type="button" class="btn btn-primary" onclick="copiarUrlV1()" title="Copiar link V1">
                        <i class="fa-solid fa-copy me-1"></i>Copiar
                      </button>
                      <a href="{{ env('APP_URL').'/checkout/produto/v1/'.$checkout->id_unico }}" target="_blank" class="btn btn-success" title="Abrir V1 em nova aba">
                        <i class="fa-solid fa-external-link-alt me-1"></i>Abrir
                      </a>
                    </div>
                    <small class="text-muted d-block mt-2">
                      <i class="fa-solid fa-info-circle me-1"></i>Checkout padrão com layout nativo
                    </small>
                  </div>
                </div>
                
                <!-- Checkout V2 -->
                <div class="card border-success mb-3">
                  <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fa-solid fa-wand-magic-sparkles me-2"></i>
                    <strong>Checkout V2</strong> (Gerado pelo Builder)
                  </div>
                  <div class="card-body p-4">
                    <label class="form-label fw-semibold mb-3">
                      <i class="fa-solid fa-external-link-alt me-2" style="color: #667eea;"></i>URL do Checkout V2
                    </label>
                    <div class="input-group input-group-lg">
                      <input type="text" id="link-checkout-v2" class="form-control" value="{{ env('APP_URL').'/checkout/produto/v2/'.$checkout->id_unico }}" readonly>
                      <button type="button" class="btn btn-primary" onclick="copiarUrlV2()" title="Copiar link V2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fa-solid fa-copy me-1"></i>Copiar
                      </button>
                      <a href="{{ env('APP_URL').'/checkout/produto/v2/'.$checkout->id_unico }}" target="_blank" class="btn btn-success" title="Abrir V2 em nova aba">
                        <i class="fa-solid fa-external-link-alt me-1"></i>Abrir
                      </a>
                    </div>
                    <small class="text-muted d-block mt-2">
                      <i class="fa-solid fa-info-circle me-1"></i>Checkout gerado automaticamente pelo builder com layout personalizado
                    </small>
                  </div>
                </div>
                
              </div>
            </div>
            <div class="tab-pane" id="ads" role="tabpanel" aria-labelledby="ads-tab">
              <div class="card-body p-4">
                <div class="mb-4">
                  <h5 class="mb-1 fw-bold">
                    <i class="fa-solid fa-bullhorn text-primary me-2"></i>Pixels de Rastreamento
                  </h5>
                  <p class="text-muted mb-0 small">Configure os pixels das plataformas de anúncios para rastrear conversões</p>
                </div>
                
                <div class="row g-4">
                  <!-- Meta/Facebook Ads -->
                  <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                      <div class="card-body p-4 text-center">
                        <div class="mb-3">
                          <img src="/build/assets/images/meta_ads.png" width="120px" height="auto" alt="Meta Ads">
                        </div>
                        <label for="checkout_ads_meta" class="form-label fw-semibold">
                          <i class="fa-brands fa-facebook me-1"></i>Facebook Pixel ID
                        </label>
                        <input type="text" class="form-control @error('checkout_ads_meta') is-invalid @enderror" value="{{$checkout->checkout_ads_meta}}" name="checkout_ads_meta" placeholder="Digite o Pixel ID">
                        @error('checkout_ads_meta')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                        <small class="text-muted d-block mt-2">
                          <i class="fa-solid fa-info-circle me-1"></i>Configure o pixel do Facebook/Instagram
                        </small>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Google Ads -->
                  <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                      <div class="card-body p-4 text-center">
                        <div class="mb-3">
                          <img src="/build/assets/images/google_ads.png" width="80px" height="auto" alt="Google Ads">
                        </div>
                        <label for="checkout_ads_google" class="form-label fw-semibold">
                          <i class="fa-brands fa-google me-1"></i>Google Tag ID
                        </label>
                        <input type="text" class="form-control @error('checkout_ads_google') is-invalid @enderror" name="checkout_ads_google" value="{{$checkout->checkout_ads_google}}" placeholder="Digite o Google Tag ID">
                        @error('checkout_ads_google')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                        <small class="text-muted d-block mt-2">
                          <i class="fa-solid fa-info-circle me-1"></i>Configure o Google Ads/Analytics
                        </small>
                      </div>
                    </div>
                  </div>
                  
                  <!-- TikTok Ads -->
                  <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                      <div class="card-body p-4 text-center">
                        <div class="mb-3">
                          <img src="/build/assets/images/tiktok_ads.png" width="90px" height="auto" alt="TikTok Ads">
                        </div>
                        <label for="checkout_ads_tiktok" class="form-label fw-semibold">
                          <i class="fa-brands fa-tiktok me-1"></i>TikTok Pixel ID
                        </label>
                        <input type="text" class="form-control @error('checkout_ads_tiktok') is-invalid @enderror" name="checkout_ads_tiktok" value="{{$checkout->checkout_ads_tiktok}}" placeholder="Digite o TikTok Pixel ID">
                        @error('checkout_ads_tiktok')
                        <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                        <small class="text-muted d-block mt-2">
                          <i class="fa-solid fa-info-circle me-1"></i>Configure o pixel do TikTok
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="tab-pane" id="orders" role="tabpanel" aria-labelledby="orders-tab">
              <div class="card-body p-0">
                @include('profile.checkout.components.orders', ['checkout' => $checkout])
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Modal: Adicionar Order Bump -->
  <form id="form-orderbump" class="row" method="POST" action="{{ route('checkout.orderbumps.create', [ 'id' => $checkout->id ]) }}" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="addOrderbump" tabindex="-1" data-bs-backdrop="static" aria-labelledby="addOrderbumpLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fw-bold" id="addOrderbumpLabel">
              <i class="fa-solid fa-plus-circle me-2"></i>Adicionar Order Bump
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="row g-3">
              <div class="col-12">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" name="active" id="active" checked>
                  <label class="form-check-label fw-semibold" for="active">
                    <i class="fa-solid fa-toggle-on text-success me-1"></i>Order Bump Ativo
                  </label>
                </div>
              </div>
              
              <div class="col-12">
                <label for="nome" class="form-label fw-semibold">
                  <i class="fa-solid fa-tag text-primary me-1"></i>Nome
                </label>
                <input type="text" class="form-control @error('nome') is-invalid @enderror" name="nome" value="" placeholder="Digite o nome da oferta" required>
                @error('nome')
                <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                @enderror
              </div>
              
              <div class="col-12">
                <label for="descricao" class="form-label fw-semibold">
                  <i class="fa-solid fa-align-left text-info me-1"></i>Descrição
                </label>
                <textarea style="min-height: 100px" class="form-control @error('descricao') is-invalid @enderror" name="descricao" placeholder="Descreva a oferta..." required></textarea>
                @error('descricao')
                <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                @enderror
              </div>
              
              <div class="col-md-6">
                <label for="valor_de" class="form-label fw-semibold">
                  <i class="fa-solid fa-dollar-sign text-warning me-1"></i>Valor Normal
                </label>
                <div class="input-group">
                  <span class="input-group-text">R$</span>
                  <input type="text" class="form-control @error('valor_de') is-invalid @enderror" name="valor_de" value="" placeholder="0.00" required>
                </div>
                @error('valor_de')
                <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                @enderror
              </div>
              
              <div class="col-md-6">
                <label for="valor_por" class="form-label fw-semibold">
                  <i class="fa-solid fa-tag text-success me-1"></i>Valor Promocional
                </label>
                <div class="input-group">
                  <span class="input-group-text">R$</span>
                  <input type="text" class="form-control @error('valor_por') is-invalid @enderror" name="valor_por" value="" placeholder="0.00" required>
                </div>
                @error('valor_por')
                <small class="text-danger d-block mt-1"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</small>
                @enderror
              </div>
              
              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="fa-solid fa-image text-primary me-1"></i>Imagem
                </label>
                <x-image-upload id="image" name="image" label="" :height="'150px'" :value="null" />
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              <i class="fa-solid fa-times me-1"></i>Cancelar
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fa-solid fa-save me-1"></i>Salvar
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>


<!-- Modal: Editar Order Bump -->
  <form id="form-orderbump-edit" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal fade" id="editOrderbump" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title fw-bold">
              <i class="fa-solid fa-pencil me-2"></i>Editar Order Bump
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body p-4">
            <div class="row g-3">
              <input type="hidden" id="edit-id" name="id">
              <div class="col-12">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" id="edit-active" name="active">
                  <label class="form-check-label fw-semibold" for="edit-active">
                    <i class="fa-solid fa-toggle-on text-success me-1"></i>Order Bump Ativo
                  </label>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="fa-solid fa-tag text-primary me-1"></i>Nome
                </label>
                <input type="text" class="form-control" name="nome" id="edit-nome" required>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="fa-solid fa-align-left text-info me-1"></i>Descrição
                </label>
                <textarea style="min-height: 100px" class="form-control" name="descricao" id="edit-descricao"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa-solid fa-dollar-sign text-warning me-1"></i>Valor Normal
                </label>
                <div class="input-group">
                  <span class="input-group-text">R$</span>
                  <input type="text" class="form-control" name="valor_de" id="edit-valor_de" required>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa-solid fa-tag text-success me-1"></i>Valor Promocional
                </label>
                <div class="input-group">
                  <span class="input-group-text">R$</span>
                  <input type="text" class="form-control" name="valor_por" id="edit-valor_por" required>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              <i class="fa-solid fa-times me-1"></i>Cancelar
            </button>
            <button type="submit" class="btn btn-warning">
              <i class="fa-solid fa-save me-1"></i>Salvar Alterações
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- Modal: Gerador de Upsell -->
  <div class="modal fade" id="upsellGeneratorModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="upsellGeneratorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content shadow-lg">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold" id="upsellGeneratorLabel">
            <i class="fa-solid fa-magic-wand-sparkles me-2"></i>Gerador de Upsell
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-4">
            <!-- Configurações -->
            <div class="col-lg-6">
              <div class="mb-4">
                <label for="upsell_produto_id" class="form-label fw-semibold">
                  <i class="fa-solid fa-box text-primary me-1"></i>Produto upsell
                </label>
                <select class="form-control" id="upsell_produto_id" onchange="updateUpsellPreview()">
                  <option value="">Selecione um produto para upsell</option>
                  @php
                    $produtos = \App\Models\CheckoutBuild::where('user_id', auth()->id())->get();
                  @endphp
                  @if($produtos->count() > 0)
                    @foreach($produtos as $produto)
                      <option value="{{ $produto->id_unico }}">{{ $produto->produto_name }} - R$ {{ number_format($produto->produto_valor, 2, ',', '.') }}</option>
                    @endforeach
                  @else
                    <option value="" disabled>Nenhum produto encontrado</option>
                  @endif
                </select>
                <small class="text-muted">
                  <i class="fa-solid fa-info-circle me-1"></i>Selecione o produto que será oferecido como upsell após o pagamento principal.
                </small>
              </div>
              
              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fa-solid fa-check-circle text-success me-1"></i>Ao aceitar a upsell
                </label>
                <div class="mb-2">
                  <small class="text-muted">Redirecionar para</small>
                </div>
                <input type="url" class="form-control" id="upsell_accept_url" placeholder="Digite a url a redirecionar após o pagamento">
              </div>
              
              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fa-solid fa-times-circle text-danger me-1"></i>Ao recusar a upsell
                </label>
                <div class="mb-2">
                  <small class="text-muted">Redirecionar para</small>
                </div>
                <input type="url" class="form-control" id="upsell_reject_url" placeholder="Digite a url a redirecionar após recusar">
              </div>
              
              <div class="mb-4">
                <label for="upsell_accept_text" class="form-label fw-semibold">
                  <i class="fa-solid fa-check text-success me-1"></i>Texto aceitar upsell
                </label>
                <input type="text" class="form-control" id="upsell_accept_text" value="Sim, eu aceito essa oferta especial!" placeholder="Texto do botão de aceitar" oninput="updateUpsellPreview()">
              </div>
              
              <div class="mb-4">
                <label for="upsell_reject_text" class="form-label fw-semibold">
                  <i class="fa-solid fa-times text-danger me-1"></i>Texto recusar upsell
                </label>
                <input type="text" class="form-control" id="upsell_reject_text" value="Não, eu gostaria de recusar essa oferta!" placeholder="Texto do link de recusar" oninput="updateUpsellPreview()">
              </div>
              
              <div class="mb-4">
                <label for="upsell_button_color" class="form-label fw-semibold">
                  <i class="fa-solid fa-palette text-primary me-1"></i>Cor
                </label>
                <div class="d-flex align-items-center">
                  <input type="color" class="form-control form-control-color" id="upsell_button_color" value="#6200ea" title="Escolher cor do botão" onchange="updateUpsellPreview()">
                  <span class="ms-2 text-muted small">Cor do botão de upsell</span>
                </div>
              </div>
            </div>
            
            <!-- Preview -->
            <div class="col-lg-6">
              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fa-solid fa-eye me-1" id="preview_icon"></i>Prévia
                </label>
                <div class="upsell-preview-container">
                        <div class="upsell-preview-content">
                          <h3 class="upsell-preview-title">Oferta Especial!</h3>
                          <p class="upsell-preview-subtitle">Aproveite esta oferta exclusiva: <strong><span id="preview_produto">Selecione um produto</span></strong></p>
                          
                          <button type="button" class="upsell-preview-btn" id="preview_accept_btn">
                            Sim, eu aceito essa oferta especial!
                          </button>
                          
                          <a href="#" class="upsell-preview-link" id="preview_reject_link">
                            Não, eu gostaria de recusar essa oferta!
                          </a>
                        </div>
                </div>
              </div>
              
              <!-- Script Integration -->
              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fa-solid fa-code text-primary me-1"></i>Integração
                </label>
                <div class="alert alert-info">
                  <small class="fw-semibold d-block mb-2">Adicione o script abaixo antes do <code>&lt;/body&gt;</code> da sua página:</small>
                  <code class="d-block mb-2">&lt;script src="{{ url('/api/upsell/upsell.min.js') }}"&gt;&lt;/script&gt;</code>
                  <small class="text-muted">Posteriormente clique em "Copiar HTML" e adicione ao seu código.</small>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light d-flex justify-content-between">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Fechar
          </button>
          <button type="button" class="btn btn-primary" id="copyUpsellHtml" onclick="copyUpsellHtml()">
            <i class="fa-solid fa-code me-1"></i>Copiar HTML
          </button>
        </div>
      </div>
    </div>
  </div>



  <!-- Modal: Remover Order Bump -->
  <form id="form-orderbump-remove" method="POST">
    @csrf
    @method('DELETE')
    <div class="modal fade" id="removeOrderbump" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title fw-bold">
              <i class="fa-solid fa-trash me-2"></i>Remover Order Bump
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body p-4">
            <div class="alert alert-danger d-flex align-items-center" role="alert">
              <i class="fa-solid fa-exclamation-triangle me-3 fa-2x"></i>
              <div>
                <h6 class="mb-1">Atenção!</h6>
                <p class="mb-0">Deseja realmente remover o Order Bump <strong id="remove-nome"></strong>?</p>
                <small class="text-muted">Esta ação não poderá ser desfeita.</small>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              <i class="fa-solid fa-times me-1"></i>Cancelar
            </button>
            <button type="submit" class="btn btn-danger">
              <i class="fa-solid fa-trash me-1"></i>Remover
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.btn-edit-orderbump');
    const removeButtons = document.querySelectorAll('.btn-remove-orderbump');

    editButtons.forEach(button => {
      button.addEventListener('click', () => {
        const id = button.dataset.id;
        const nome = button.dataset.nome;
        const descricao = button.dataset.descricao;
        const valorDe = button.dataset.valorDe;
        const valorPor = button.dataset.valorPor;
        const ativo = button.dataset.ativo;
        const image = button.dataset.image;

        document.querySelector('#form-orderbump-edit').action = `/produtos/orderbumps/edit/${id}`;
        document.querySelector('#edit-id').value = id;
        document.querySelector('#edit-nome').value = nome;
        document.querySelector('#edit-descricao').value = descricao;
        document.querySelector('#edit-valor_de').value = valorDe;
        document.querySelector('#edit-valor_por').value = valorPor;
        document.querySelector('#edit-active').checked = ativo =="1";

        // Atualize preview da imagem se necessário
      });
    });

    removeButtons.forEach(button => {
      button.addEventListener('click', () => {
        const id = button.dataset.id;
        const nome = button.dataset.nome;

        document.querySelector('#form-orderbump-remove').action = `/produtos/orderbumps/remove/${id}`;
        document.querySelector('#remove-nome').textContent = nome;
      });
    });
  });
</script>


  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    let backgroundColor = document.getElementById('background_color');
    let contadorBG = document.getElementById("countdown_background");
    let contadorDescription = document.getElementById('countdown_description');
    let contadorText = document.getElementById('countdown_text');
    let contadorIcon = document.getElementById('countdown_icon');
    let headerContainer = document.getElementById('headerContainer');
    let headerImage1 = document.getElementById("header_image1");
    let headerImage2 = document.getElementById("header_image2");
    let topbarBackground = document.getElementById('topbar_background');
    let topbarText = document.getElementById('topbar_text');

    let guideCurrent = document.querySelector('.guide.ativo .step-number');

    document.querySelector('[name="checkout_color"]').addEventListener('input', (e) => {
      backgroundColor.style.background = e.target.value;
    });

    document.querySelector('[name="checkout_color_card"]').addEventListener('input', (e) => {
      document.querySelectorAll('.card-bg').forEach(el => {
        el.style.background = e.target.value;
      });
    });


    document.querySelector('[name="checkout_timer_tempo"]').addEventListener('input', (e) => {
      let time = e.target.value;
      if(Number(time) < 10){
        time = '0'+time;
      }
      contadorText.innerText = time+':00';
    });

    document.querySelector('[name="checkout_timer_cor_fundo"]').addEventListener('input', (e) => {
      contadorBG.style.background = e.target.value;
    });

    document.querySelector('[name="checkout_timer_cor_texto"]').addEventListener('input', (e) => {
      contadorText.style.color = e.target.value;
      contadorIcon.style.color = e.target.value;
      contadorDescription.style.color = e.target.value;
    });

    document.querySelector('[name="checkout_timer_texto"]').addEventListener('input', (e) => {
      contadorDescription.innerText = e.target.value;
    });

    document.querySelector('[name="checkout_header_logo"]').addEventListener('input', (e) => {
      const file = e.target.files[0];
      headerImage1.src = URL.createObjectURL(file);
    });

    document.querySelector('[name="checkout_header_logo_active"]').addEventListener('input', (e) => {
      const isChecked = e.target.checked;
      headerImage1.style.display = isChecked ? 'block' : 'none';
      document.querySelector('[name="checkout_header_logo"]').style.display = isChecked ? 'block' : 'none';
    });

    document.querySelector('[name="checkout_header_image"]').addEventListener('input', (e) => {
      const file = e.target.files[0];
      headerImage2.src = URL.createObjectURL(file);

    });

    document.querySelector('[name="checkout_header_image_active"]').addEventListener('input', (e) => {
      const isChecked = e.target.checked;
      headerImage2.style.display = isChecked ? 'block' : 'none';
      document.querySelector('[name="checkout_header_image"]').style.display = isChecked ? 'block' : 'none';
    });

    document.querySelector('[name="checkout_banner_active"]').addEventListener('input', (e) => {
      const isChecked = e.target.checked;
      console.log('isChecked', e.target.checked)
      /* if(isChecked){
        document.getElementById('headerContainer').style.background ="url('{{ $checkout->checkout_banner }}')"
      } */
      headerContainer.style.background = isChecked ?"url('{{ $checkout->checkout_banner }}')" : 'transparent';
    });


    document.querySelector('[name="checkout_banner"]').addEventListener('input', (e) => {
      const file = e.target.files[0];
      headerContainer.style.background = `url(${URL.createObjectURL(file)})`;
    });

    document.querySelector('[name="checkout_banner"]').addEventListener('input', (e) => {
      const file = e.target.files[0];
      headerContainer.style.background = `url(${URL.createObjectURL(file)})`;
    });

    document.querySelector('[name="checkout_topbar_text"]').addEventListener('input', (e) => {
      topbarText.innerText = e.target.value;
    });

    document.querySelector('[name="checkout_topbar_text_color"]').addEventListener('input', (e) => {
      topbarText.style.color = e.target.value;
    });

    document.querySelector('[name="checkout_topbar_color"]').addEventListener('input', (e) => {
      topbarBackground.style.background = e.target.value;
    });

    document.querySelector('[name="checkout_color_default"]').addEventListener('input', (e) => {
      guideCurrent.style.background = e.target.value;
    });

    document.querySelector('[name="checkout_color_default"]').addEventListener('input', (e) => {
      guideCurrent.style.background = e.target.value;
      document.querySelector('.qtde').style.background = e.target.value;

      document.querySelectorAll('.btn-form-checkout').forEach(el => {
        el.style.background = e.target.value;
        el.style.backgroundColor = e.target.value;
      });
    });

    document.querySelector('[name="checkout_timer_active"]').addEventListener('change', (e) => {
      const isChecked = e.target.checked;
      contadorBG.style.display = isChecked ? 'block' : 'none';
      document.querySelectorAll('.timer-scope').forEach(el => {
        el.style.display = isChecked ? 'block' : 'none';
      });
    });

    document.querySelector('[name="checkout_topbar_active"]').addEventListener('change', (e) => {
      const isChecked = e.target.checked;
      topbarBackground.style.display = isChecked ? 'flex' : 'none';
      document.querySelectorAll('.topbar-scope').forEach(el => {
        el.style.display = isChecked ? 'block' : 'none';
      });
    });


  </script>
<script>
  function copiarUrl() {
   var input = document.getElementById("link-checkout-v1") || document.getElementById("link-checkout");

   // Garante que o valor do input será copiado
   navigator.clipboard.writeText(input.value)
    .then(() => {
     showToast('success',"Link copiado.")
     //alert("Chave Pix copiada!");
    })
    .catch(err => {
     console.error("Erro ao copiar", err);
    });
  }
  
  // Nova função para copiar V1
  function copiarUrlV1() {
   var input = document.getElementById("link-checkout-v1");
   
   navigator.clipboard.writeText(input.value)
    .then(() => {
     showToast('success',"✅ Link do Checkout V1 copiado!")
    })
    .catch(err => {
     console.error("Erro ao copiar V1", err);
     showToast('error',"Erro ao copiar link V1")
    });
  }
  
  // Nova função para copiar V2
  function copiarUrlV2() {
   var input = document.getElementById("link-checkout-v2");
   
   navigator.clipboard.writeText(input.value)
    .then(() => {
     showToast('success',"✅ Link do Checkout V2 copiado!")
    })
    .catch(err => {
     console.error("Erro ao copiar V2", err);
     showToast('error',"Erro ao copiar link V2")
    });
  }
  
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const mainTabs = document.querySelectorAll('a[data-bs-toggle="tab"]');

    // Ativa a aba correta com base no hash da URL
    function activateTabsFromHash() {
      const fullHash = window.location.hash;
      const [mainHash, subHash] = fullHash.split('#').filter(Boolean); // remove vazios

      // Ativa aba principal
      const mainTab = document.querySelector('a[data-bs-target="#' + mainHash + '"]');
      if (mainTab) {
        new bootstrap.Tab(mainTab).show();
      }

      // Se aba principal for checkouts
      if (mainHash === 'checkouts') {
        const subId = subHash || 'tema'; // default é"tema" se não houver sub aba
        const subTab = document.querySelector('#checkouts a[data-bs-target="#' + subId + '"]');
        if (subTab) {
          new bootstrap.Tab(subTab).show();
        }
      }
    }

    // Atualiza a URL ao mudar a aba principal
    mainTabs.forEach(tab => {
      tab.addEventListener('shown.bs.tab', function (e) {
        const target = e.target.getAttribute('data-bs-target').replace('#', '');
        if (target === 'checkouts') {
          // Se for checkouts, mantemos o sub aba atual ou padrão 'tema'
          const currentSub = document.querySelector('#checkouts .nav-link.active')?.getAttribute('data-bs-target')?.replace('#', '') || 'tema';
          history.replaceState(null, null, '#' + target + '#' + currentSub);
        } else {
          history.replaceState(null, null, '#' + target);
        }
      });
    });

    // Atualiza a URL ao mudar aba interna de checkouts
    const checkoutSubTabs = document.querySelectorAll('#checkouts a[data-bs-toggle="tab"]');
    checkoutSubTabs.forEach(tab => {
      tab.addEventListener('shown.bs.tab', function (e) {
        const subTarget = e.target.getAttribute('data-bs-target').replace('#', '');
        history.replaceState(null, null, '#checkouts#' + subTarget);
      });
    });

    activateTabsFromHash();
  });
  
  // JavaScript para garantir que os botões de pagamento funcionem corretamente
  document.addEventListener('DOMContentLoaded', function() {
    const paymentButtons = document.querySelectorAll('#configuracoes input[type="checkbox"].btn-check');
    
    paymentButtons.forEach(function(checkbox) {
      // Adicionar evento de mudança
      checkbox.addEventListener('change', function() {
        const label = document.querySelector('label[for="' + this.id + '"]');
        const card = this.closest('.payment-method-card');
        
        if (label && card) {
          if (this.checked) {
            // Ativar método
            label.classList.remove('btn-outline-primary');
            label.classList.add('btn-primary');
            label.textContent = 'Método padrão';
            
            // Destacar card
            card.classList.remove('border-light');
            card.classList.add('border-primary');
            card.style.boxShadow = '0 4px 8px rgba(var(--bs-primary-rgb), 0.2)';
          } else {
            // Desativar método
            label.classList.remove('btn-primary');
            label.classList.add('btn-outline-primary');
            label.textContent = 'Selecionar';
            
            // Remover destaque do card
            card.classList.remove('border-primary');
            card.classList.add('border-light');
            card.style.boxShadow = '';
          }
        }
      });
      
      // Aplicar estado inicial
      const label = document.querySelector('label[for="' + checkbox.id + '"]');
      const card = checkbox.closest('.payment-method-card');
      
      if (label && card && checkbox.checked) {
        label.classList.remove('btn-outline-primary');
        label.classList.add('btn-primary');
        label.textContent = 'Método padrão';
        
        card.classList.remove('border-light');
        card.classList.add('border-primary');
        card.style.boxShadow = '0 4px 8px rgba(var(--bs-primary-rgb), 0.2)';
      }
    });
    
    // Função para validar métodos de pagamento
    function validatePaymentMethods() {
      const paymentMethods = document.querySelectorAll('#configuracoes input[name="methods[]"]:checked');
      const validationMessage = document.getElementById('payment-validation-message');
      const paymentContainer = document.getElementById('payment-methods-container');
      
      if (paymentMethods.length === 0) {
        // Mostrar mensagem de erro
        validationMessage.style.display = 'block';
        paymentContainer.style.borderRadius = '8px';
        paymentContainer.style.padding = '10px';
        paymentContainer.style.backgroundColor = '#fff5f5';
        return false;
      } else {
        // Esconder mensagem de erro
        validationMessage.style.display = 'none';
        paymentContainer.style.borderRadius = '';
        paymentContainer.style.padding = '';
        paymentContainer.style.backgroundColor = '';
        return true;
      }
    }
    
    // Validação obrigatória de métodos de pagamento no submit
    document.getElementById('form-checkout-completo').addEventListener('submit', function(e) {
      if (!validatePaymentMethods()) {
        e.preventDefault(); // Impede o envio do formulário
        
        // Mostrar alerta
        alert('⚠️ Atenção!\n\nÉ obrigatório selecionar pelo menos um método de pagamento (PIX, Cartão ou Boleto) antes de salvar as alterações.');
        
        // Scroll para a seção de métodos de pagamento
        document.querySelector('#configuracoes .row.g-3').scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        });
        
        return false;
      }
    });
    
    // Validação em tempo real quando os checkboxes mudam
    document.querySelectorAll('#configuracoes input[name="methods[]"]').forEach(function(checkbox) {
      checkbox.addEventListener('change', function() {
        validatePaymentMethods();
      });
    });
    
    // Validação inicial
    validatePaymentMethods();
    
    // Adicionar funcionalidade de clique no card
    document.querySelectorAll('.payment-method-card').forEach(function(card) {
      card.addEventListener('click', function(e) {
        // Não executar se clicar no botão
        if (e.target.tagName === 'BUTTON' || e.target.tagName === 'LABEL') {
          return;
        }
        
        const checkbox = card.querySelector('input[type="checkbox"]');
        if (checkbox) {
          checkbox.checked = !checkbox.checked;
          checkbox.dispatchEvent(new Event('change'));
        }
      });
    });
  });
  </script>


  <!-- CSS para os cards de métodos de pagamento e preview do upsell -->
  <style>
    /* Estilos para a tabela de checkouts */
    #checkoutsTable {
      font-size: 0.9rem;
    }
    
    #checkoutsTable .btn-group .btn {
      margin: 0 2px;
      border-radius: 6px;
      transition: all 0.2s ease;
    }
    
    #checkoutsTable .btn-group .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    #checkoutsTable tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    /* Responsividade para mobile */
    @media (max-width: 768px) {
      #checkoutsTable .btn-group {
        flex-direction: column;
        gap: 2px;
      }
      
      #checkoutsTable .btn-group .btn {
        margin: 1px 0;
        font-size: 0.8rem;
        padding: 4px 8px;
      }
    }

    .payment-method-card {
      transition: all 0.3s ease;
      border: 2px solid #e9ecef;
    }
    
    .payment-method-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .payment-method-card.border-primary {
      border-color: var(--bs-primary) !important;
      box-shadow: 0 4px 8px rgba(var(--bs-primary-rgb), 0.2);
    }
    
    .payment-method-card .btn-check:checked + .btn {
      background-color: var(--bs-primary);
      border-color: var(--bs-primary);
    }
    
    /* Adicionar cursor pointer para os cards */
    .payment-method-card {
      cursor: pointer;
    }
    
    /* Efeito de clique no card */
    .payment-method-card:active {
      transform: translateY(0);
    }
    
    /* Ícones dos métodos de pagamento com cor primária */
    .payment-method-card .fa-brands.fa-pix,
    .payment-method-card .fa-solid.fa-credit-card,
    .payment-method-card .fa-solid.fa-barcode {
      color: var(--bs-primary) !important;
    }

    /* CSS para Preview do Upsell */
    .upsell-preview-container {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border: 2px solid #dee2e6;
      border-radius: 12px;
      padding: 2rem;
      min-height: 250px;
      position: relative;
      overflow: hidden;
    }

    .upsell-preview-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--preview-color, var(--bs-primary)), #6f42c1, var(--preview-color, var(--bs-primary)));
      background-size: 200% 100%;
      animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes shimmer {
      0%, 100% { background-position: 200% 0; }
      50% { background-position: -200% 0; }
    }

    .upsell-preview-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      text-align: center;
    }

    .upsell-preview-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--preview-color, #6200ea);
      margin-bottom: 0.5rem;
      text-shadow: 0 1px 2px rgba(0,0,0,0.1);
      transition: color 0.3s ease;
    }

    .upsell-preview-subtitle {
      font-size: 1rem;
      color: var(--preview-color, #6200ea);
      margin-bottom: 1.5rem;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .upsell-preview-btn {
      --bs-btn-color: #fff;
      --bs-btn-bg: #6200ea;
      --bs-btn-border-color: #6200ea;
      --bs-btn-hover-color: #fff;
      --bs-btn-hover-bg: #5300c7;
      --bs-btn-hover-border-color: #4e00bb;
      --bs-btn-focus-shadow-rgb: 122, 38, 237;
      --bs-btn-active-color: #fff;
      --bs-btn-active-bg: #4e00bb;
      --bs-btn-active-border-color: #4a00b0;
      
      background-color: var(--bs-btn-bg);
      border-color: var(--bs-btn-border-color);
      color: var(--bs-btn-color);
      border: 1px solid var(--bs-btn-border-color);
      padding: 12px 24px;
      border-radius: 6px;
      font-size: 16px;
      font-weight: 500;
      min-width: 300px;
      margin-bottom: 1rem;
      transition: all 0.15s ease-in-out;
      cursor: pointer;
    }

    .upsell-preview-btn:hover {
      background-color: var(--bs-btn-hover-bg);
      border-color: var(--bs-btn-hover-border-color);
      color: var(--bs-btn-hover-color);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(98, 0, 234, 0.3);
    }

    .upsell-preview-btn:active {
      background-color: var(--bs-btn-active-bg);
      border-color: var(--bs-btn-active-border-color);
      color: var(--bs-btn-active-color);
      transform: translateY(0);
    }

    .upsell-preview-btn:focus {
      box-shadow: 0 0 0 0.25rem rgba(var(--bs-btn-focus-shadow-rgb), 0.5);
    }

    .upsell-preview-link {
      color: var(--bs-primary);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
    }

    .upsell-preview-link::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--bs-primary);
      transition: width 0.3s ease;
    }

    .upsell-preview-link:hover {
      color: #6f42c1;
      text-decoration: none;
    }

    .upsell-preview-link:hover::after {
      width: 100%;
    }

    /* Responsividade do preview */
    @media (max-width: 768px) {
      .upsell-preview-container {
        padding: 1.5rem;
        min-height: 200px;
      }
      
      .upsell-preview-btn {
        min-width: 240px;
        padding: 12px 24px;
        font-size: 14px;
      }
      
      .upsell-preview-title {
        font-size: 1.25rem;
      }
    }
  </style>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const useDefaultCheckbox = document.getElementById('use_default_thank_you');
    const customUrlSection = document.getElementById('custom_url_section');
    const urlInput = document.getElementById('url_pagina_vendas');
    const defaultUrlInput = document.getElementById('url_pagina_vendas_default');
    
    // Função para alternar entre página padrão e personalizada
    function toggleUrlSection() {
      if (useDefaultCheckbox.checked) {
        // Usar página padrão
        customUrlSection.style.display = 'none';
        urlInput.value = defaultUrlInput.value;
        urlInput.disabled = true;
      } else {
        // Usar URL personalizada
        customUrlSection.style.display = 'block';
        urlInput.disabled = false;
        if (urlInput.value === defaultUrlInput.value) {
          urlInput.value = '';
        }
      }
    }
    
    // Event listener para o checkbox
    useDefaultCheckbox.addEventListener('change', toggleUrlSection);
    
    // Inicializar estado
    toggleUrlSection();
    
    // Preview da página padrão
    const previewButton = document.createElement('button');
    previewButton.type = 'button';
    previewButton.className = 'btn btn-outline-info btn-sm mt-2';
    previewButton.innerHTML = '<i class="fas fa-eye me-1"></i>Visualizar página padrão';
    previewButton.onclick = function() {
      window.open('{{ url("/obrigado?order_id=preview") }}', '_blank');
    };
    
    // Adicionar botão de preview após o checkbox
    useDefaultCheckbox.parentNode.parentNode.appendChild(previewButton);
  });
  </script>

  <!-- JavaScript para Gerador de Upsell -->
  <script>
  function updateUpsellPreview() {
    // Elementos do formulário
    const produtoSelect = document.getElementById('upsell_produto_id');
    const acceptText = document.getElementById('upsell_accept_text');
    const rejectText = document.getElementById('upsell_reject_text');
    const buttonColor = document.getElementById('upsell_button_color');
    
    // Elementos do preview
    const previewProduto = document.getElementById('preview_produto');
    const previewAcceptBtn = document.getElementById('preview_accept_btn');
    const previewRejectLink = document.getElementById('preview_reject_link');
    const previewIcon = document.getElementById('preview_icon');
    
    if (!produtoSelect || !acceptText || !rejectText || !buttonColor) return;
    
    // Atualizar texto do produto selecionado
    if (previewProduto) {
      const selectedOption = produtoSelect.options[produtoSelect.selectedIndex];
      if (selectedOption.value) {
        previewProduto.textContent = selectedOption.text;
      } else {
        previewProduto.textContent = 'Selecione um produto';
      }
    }
    
    // Atualizar texto dos botões
    if (previewAcceptBtn) {
      previewAcceptBtn.textContent = acceptText.value;
    }
    
    if (previewRejectLink) {
      previewRejectLink.textContent = rejectText.value;
    }
    
    // Atualizar cor do botão e elementos
    const selectedColor = buttonColor.value || '#6200ea';
    
    if (previewAcceptBtn) {
      previewAcceptBtn.style.backgroundColor = selectedColor;
      previewAcceptBtn.style.borderColor = selectedColor;
    }
    
    if (previewRejectLink) {
      previewRejectLink.style.color = selectedColor;
    }
    
    if (previewIcon) {
      previewIcon.style.color = selectedColor;
    }
    
    // Atualizar cores dos elementos do preview
    const previewContainer = document.querySelector('.upsell-preview-container');
    const previewTitle = document.querySelector('.upsell-preview-title');
    const previewSubtitle = document.querySelector('.upsell-preview-subtitle');
    
    if (previewContainer) {
      previewContainer.style.setProperty('--preview-color', selectedColor);
    }
    
    if (previewTitle) {
      previewTitle.style.color = selectedColor;
    }
    
    if (previewSubtitle) {
      previewSubtitle.style.color = selectedColor;
    }
  }

  // Função para copiar HTML do upsell
  function copyUpsellHtml() {
    const produtoSelect = document.getElementById('upsell_produto_id');
    const acceptUrl = document.getElementById('upsell_accept_url');
    const rejectUrl = document.getElementById('upsell_reject_url');
    const acceptText = document.getElementById('upsell_accept_text');
    const rejectText = document.getElementById('upsell_reject_text');
    const buttonColor = document.getElementById('upsell_button_color');
    const copyHtmlBtn = document.getElementById('copyUpsellHtml');
    
    if (!produtoSelect || !acceptUrl || !rejectUrl || !acceptText || !rejectText || !buttonColor || !copyHtmlBtn) return;
    
    const produtoId = produtoSelect.value;
    const acceptUrlValue = acceptUrl.value || '#';
    const rejectUrlValue = rejectUrl.value || '#';
    const acceptTextValue = acceptText.value || 'Sim, eu aceito essa oferta especial!';
    const rejectTextValue = rejectText.value || 'Não, eu gostaria de recusar essa oferta!';
    const colorValue = buttonColor.value || '#6200ea';

    const html = `<div id="upsell-preview" style="text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 6px;">
          <button id="preview-accept" style="display: block; width: 100%; padding: 12px; margin-bottom: 10px;
              background-color: ${colorValue}; color: white !important; font-size: 16px; font-weight: bold;
              border: none; border-radius: 4px; cursor: pointer;" data-redirect-to="${acceptUrlValue}" data-produto-id="${produtoId}">${acceptTextValue}</button>
          <a id="preview-recuse" style="display: inline-block; font-size: 14px; color: ${colorValue}; text-decoration: underline; cursor: pointer;" href="${rejectUrlValue}">${rejectTextValue}</a>
        </div>`;
    
    navigator.clipboard.writeText(html).then(() => {
      const originalText = copyHtmlBtn.innerHTML;
      copyHtmlBtn.innerHTML = '<i class="fa-solid fa-check me-1"></i>Copiado!';
      copyHtmlBtn.classList.remove('btn-primary');
      copyHtmlBtn.classList.add('btn-success');
      
      setTimeout(() => {
        copyHtmlBtn.innerHTML = originalText;
        copyHtmlBtn.classList.remove('btn-success');
        copyHtmlBtn.classList.add('btn-primary');
      }, 2000);
    }).catch(err => {
      alert('Erro ao copiar o código. Tente novamente.');
    });
  }

  // Inicializar quando o modal abrir
  document.addEventListener('DOMContentLoaded', function() {
    const upsellModal = document.getElementById('upsellGeneratorModal');
    if (upsellModal) {
      upsellModal.addEventListener('shown.bs.modal', function() {
        // Definir cor padrão
        const buttonColor = document.getElementById('upsell_button_color');
        if (buttonColor) buttonColor.value = '#6200ea';
        
        // Atualizar preview inicial
        setTimeout(updateUpsellPreview, 100);
      });
    }
  });

  // JavaScript para funcionalidades da aba Checkouts Builder
  document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidade de pesquisa simples
    const searchInput = document.getElementById('searchCheckouts');
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#checkoutsTable tbody tr');
        
        tableRows.forEach(row => {
          const text = row.textContent.toLowerCase();
          if (text.includes(searchTerm)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }
  });

  // Funções para ações dos checkouts
  function openCheckoutBuilder(checkoutId) {
    window.open('/checkout-builder/' + checkoutId, '_blank');
  }

  function duplicateCheckout(checkoutId) {
    if (confirm('Deseja duplicar este checkout?')) {
      // Aqui você pode implementar a lógica de duplicação
      showToast('success', 'Checkout duplicado com sucesso!');
    }
  }

  function configureCheckout(checkoutId) {
    // Aqui você pode implementar a lógica de configuração
    showToast('info', 'Abrindo configurações do checkout...');
  }

  function deleteCheckout(checkoutId) {
    if (confirm('Tem certeza que deseja deletar este checkout? Esta ação não pode ser desfeita.')) {
      // Aqui você pode implementar a lógica de exclusão
      showToast('success', 'Checkout deletado com sucesso!');
    }
  }

  // Função para mostrar toast (se não existir)
  function showToast(type, message) {
    // Implementação simples de toast
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
      <i class="fa-solid fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'} me-2"></i>
      ${message}
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
      toast.remove();
    }, 3000);
  }
  </script>

</x-app-layout>
