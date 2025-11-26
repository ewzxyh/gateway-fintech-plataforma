@php
$setting = \App\Helpers\Helper::getSetting();
@endphp
<style>
  }');
    --bg-overlay: linear-gradient(135deg, rgba(245, 247, 250, 0.8), rgba(195, 207, 226, 0.8));
    --card-bg: rgba(255, 255, 255, 0.9);
    --card-border: rgba(255, 255, 255, 0.3);
  }
  
  [data-theme="dark"] {
    /* Tema Noturno - Preto Profundo */
    --bg-primary: linear-gradient(135deg, #000000 0%, #0a0a0a 50%, #111111 100%);
    --bg-secondary: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    --glass-bg: rgba(255, 255, 255, 0.03);
    --glass-border: rgba(255, 255, 255, 0.08);
    --text-primary: #ffffff;
    --text-secondary: rgba(255, 255, 255, 0.85);
    --text-muted: rgba(255, 255, 255, 0.5);
    --input-bg: rgba(255, 255, 255, 0.02);
    --input-border: rgba(255, 255, 255, 0.1);
    --input-focus: rgba(99, 102, 241, 0.6);
    --button-primary: linear-gradient(135deg, #333333 0%, #555555 100%);
    --shadow-light: rgba(255, 255, 255, 0.05);
    --shadow-dark: rgba(0, 0, 0, 0.8);
    --bg-image: none;
    --bg-overlay: linear-gradient(135deg, rgba(0, 0, 0, 0.95), rgba(17, 17, 17, 0.9));
    --card-bg: rgba(255, 255, 255, 0.02);
    --card-border: rgba(255, 255, 255, 0.08);
  }
  
  * {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  }
  
  body {
    background: var(--bg-primary);
    background-image: var(--bg-image);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
  }
  
  body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--bg-overlay);
    z-index: -1;
  }
  
  /* Adobe CC Glassmorphism Card */
  .adobe-glass-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(60px) saturate(200%);
    -webkit-backdrop-filter: blur(60px) saturate(200%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 24px;
    box-shadow: 
      0 25px 80px rgba(0, 0, 0, 0.15),
      0 12px 40px rgba(0, 0, 0, 0.08),
      inset 0 2px 0 rgba(255, 255, 255, 0.3),
      inset 0 -1px 0 rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  .adobe-glass-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, 
      rgba(255, 255, 255, 0.4) 0%, 
      transparent 30%, 
      transparent 70%,
      rgba(255, 255, 255, 0.2) 100%);
    opacity: 0.6;
    pointer-events: none;
  }
  
  .adobe-glass-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, 
      transparent, 
      rgba(255, 255, 255, 0.8), 
      transparent);
    opacity: 0.7;
  }
  
  .adobe-glass-card:hover {
    transform: translateY(-8px) scale(1.02);
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 
      0 40px 120px rgba(0, 0, 0, 0.25),
      0 20px 60px rgba(0, 0, 0, 0.12),
      inset 0 2px 0 rgba(255, 255, 255, 0.4),
      inset 0 -1px 0 rgba(0, 0, 0, 0.05);
  }
  
  [data-theme="dark"] .adobe-glass-card {
    background: rgba(255, 255, 255, 0.02);
    border-color: rgba(255, 255, 255, 0.1);
    box-shadow: 
      0 25px 80px rgba(0, 0, 0, 0.9),
      0 12px 40px rgba(0, 0, 0, 0.7),
      inset 0 2px 0 rgba(255, 255, 255, 0.05),
      inset 0 -1px 0 rgba(0, 0, 0, 0.8);
  }
  
  [data-theme="dark"] .adobe-glass-card:hover {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.15);
    box-shadow: 
      0 40px 120px rgba(0, 0, 0, 0.95),
      0 20px 60px rgba(0, 0, 0, 0.8),
      inset 0 2px 0 rgba(255, 255, 255, 0.08),
      inset 0 -1px 0 rgba(0, 0, 0, 0.9);
  }
  
  /* Card Body */
  .adobe-card-body {
    padding: 2rem;
    position: relative;
    z-index: 2;
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    margin: 8px;
  }
  
  /* Typography */
  .adobe-title {
    color: var(--text-primary);
    font-weight: 700;
    margin-bottom: 1.5rem;
    font-size: 1.4rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, var(--text-primary), rgba(255, 255, 255, 0.8));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.02em;
  }
  
  .adobe-subtitle {
    color: var(--text-secondary);
    font-weight: 400;
    text-shadow: 0 1px 4px var(--shadow-dark);
  }
  
  .adobe-text {
    color: var(--text-primary);
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  }
  
  .adobe-text-muted {
    color: var(--text-secondary);
    font-weight: 500;
    opacity: 0.9;
  }
  
  /* Enhanced Modal */
  .modal-content {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 28px;
    backdrop-filter: blur(50px) saturate(180%);
    -webkit-backdrop-filter: blur(50px) saturate(180%);
    box-shadow: 
      0 30px 100px rgba(0, 0, 0, 0.3),
      0 10px 40px rgba(0, 0, 0, 0.15),
      inset 0 2px 0 rgba(255, 255, 255, 0.3),
      inset 0 -2px 0 rgba(0, 0, 0, 0.1);
    overflow: hidden;
    position: relative;
  }

  .modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, 
      transparent, 
      rgba(255, 255, 255, 0.8), 
      transparent);
  }

  .modal-header, .modal-body, .modal-footer {
    border: none;
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(10px);
    margin: 8px;
    border-radius: 16px;
  }

  .modal-title {
    color: var(--text-primary);
    font-weight: 700;
    font-size: 1.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  /* Enhanced Buttons */
  .btn {
    backdrop-filter: blur(15px);
    border-radius: 16px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .btn:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
  }

  .btn-primary {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.3), rgba(13, 110, 253, 0.1));
    border-color: rgba(13, 110, 253, 0.4);
  }
  
  /* Glassmorphism para tabelas */
  .table {
    background: rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(5px);
    border-radius: 12px;
    overflow: hidden;
    color: var(--text-primary);
  }
  
  .table th {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: none;
    padding: 1rem;
    font-weight: 700;
    color: var(--text-secondary);
  }
  
  .table td {
    background: rgba(255, 255, 255, 0.02);
    border: none;
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .table tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
    transform: scale(1.01);
    transition: all 0.3s ease;
  }
  
  [data-theme="dark"] .table th {
    background: rgba(255, 255, 255, 0.03);
  }
  
  [data-theme="dark"] .table td {
    background: rgba(255, 255, 255, 0.01);
    border-bottom-color: rgba(255, 255, 255, 0.08);
  }
  
  [data-theme="dark"] .table tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
  }
  
  /* Main Content */
  .main-content {
    position: relative;
    z-index: 1;
  }
  
  /* Form Controls */
  .form-control, .form-select {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    color: var(--text-primary);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    transition: all 0.4s ease;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .form-control:focus, .form-select:focus {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.3);
    box-shadow: 
      0 0 0 0.2rem rgba(255, 255, 255, 0.1),
      inset 0 2px 4px rgba(0, 0, 0, 0.05);
    transform: scale(1.02);
  }
  
  /* Glassmorphism Badges */
  .badge {
    background: rgba(255, 255, 255, 0.15) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
  }

  .badge.bg-success {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.3), rgba(40, 167, 69, 0.1)) !important;
    border-color: rgba(40, 167, 69, 0.4);
  }

  .badge.bg-light {
    background: linear-gradient(135deg, rgba(248, 249, 250, 0.3), rgba(248, 249, 250, 0.1)) !important;
    border-color: rgba(248, 249, 250, 0.4);
  }
  
  /* Icon Circles */
  .icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transition: all 0.3s ease;
  }
  
  .icon-circle:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
  }
  
  .card-color {
    position: relative;
    overflow: hidden;
  }
  
  .card-color::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transform: rotate(45deg);
    transition: all 0.6s ease;
    opacity: 0;
  }
  
  .card-color:hover::before {
    opacity: 1;
    animation: shimmer 1.5s ease-in-out;
  }
  
  @keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
  }
  
  /* Typography Enhancements */
  .caption {
    font-size: 0.75rem;
    font-weight: 500;
    letter-spacing: 0.5px;
  }
  
  .fw-500 {
    font-weight: 500;
  }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="[REDACTED_BASIC_AUTH_URL]" rel="stylesheet">
<x-app-layout :route="'Produtos'">

  <div class="main-content app-content">
    <div class="container-fluid">
      <!-- Decorative Header Card -->
      <div class="adobe-glass-card mb-4">
        <div class="adobe-card-body text-center py-3">
          <h1 class="adobe-title mb-2">Gestão de Produtos</h1>
          <p class="adobe-text-muted mb-0">Gerencie seus produtos e checkouts de forma eficiente</p>
        </div>
      </div>
      
      <!-- Cards de Métricas -->
      <div class="row mb-4">
        <div class="col-xxl-3 col-md-6 mb-4">
          <div class="adobe-glass-card">
            <div class="adobe-card-body px-4 py-4" style="min-height: 140px">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="me-2">
                  <div class="display-5 adobe-text fw-bold">{{ $produtos->count() }}</div>
                  <div class="adobe-text-muted">Total de Produtos</div>
                </div>
                <div class="text-white icon-circle bg-primary card-color">
                  <i class="fas fa-box text-white"></i>
                </div>
              </div>
              <div class="adobe-text-muted">
                <div class="d-inline-flex align-items-center">
                  <i class="fa-solid fa-shopping-bag text-primary"></i>&nbsp;
                  <div class="caption text-primary fw-500 me-2">Status:</div>
                  <div class="caption text-primary">Cadastrados</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6 mb-4">
          <div class="adobe-glass-card">
            <div class="adobe-card-body px-4 py-4" style="min-height: 140px">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="me-2">
                  <div class="display-5 text-success adobe-text fw-bold">{{ $produtos->where('ativo', 1)->count() }}</div>
                  <div class="adobe-text-muted">Produtos Ativos</div>
                </div>
                <div class="text-white icon-circle bg-success card-color">
                  <i class="fas fa-check-circle text-white"></i>
                </div>
              </div>
              <div class="adobe-text-muted">
                <div class="d-inline-flex align-items-center">
                  <i class="fa-solid fa-chart-line text-success"></i>&nbsp;
                  <div class="caption text-success fw-500 me-2">Status:</div>
                  <div class="caption text-success">Disponíveis</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6 mb-4">
          <div class="adobe-glass-card">
            <div class="adobe-card-body px-4 py-4" style="min-height: 140px">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="me-2">
                  @php
                    $totalVendas = 0;
                    foreach($produtos as $produto) {
                      if(isset($produto->vendas)) {
                        $totalVendas += $produto->vendas->count();
                      }
                    }
                  @endphp
                  <div class="display-5 adobe-text fw-bold">{{ $totalVendas }}</div>
                  <div class="adobe-text-muted">Total de Vendas</div>
                </div>
                <div class="text-white icon-circle bg-info card-color">
                  <i class="fas fa-shopping-cart text-white"></i>
                </div>
              </div>
              <div class="adobe-text-muted">
                <div class="d-inline-flex align-items-center">
                  <i class="fa-solid fa-dollar-sign text-info"></i>&nbsp;
                  <div class="caption text-info fw-500 me-2">Total:</div>
                  <div class="caption text-info">Pedidos</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6 mb-4">
          <div class="adobe-glass-card">
            <div class="adobe-card-body px-4 py-4" style="min-height: 140px">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="me-2">
                  @php
                    $valorTotal = 0;
                    foreach($produtos as $produto) {
                      $valorTotal += (float)($produto->valor ?? 0);
                    }
                  @endphp
                  <div class="display-5 adobe-text fw-bold">R$ {{ number_format($valorTotal, 2, ',', '.') }}</div>
                  <div class="adobe-text-muted">Valor Total</div>
                </div>
                <div class="text-white icon-circle bg-warning card-color">
                  <i class="fas fa-coins text-white"></i>
                </div>
              </div>
              <div class="adobe-text-muted">
                <div class="d-inline-flex align-items-center">
                  <i class="fa-solid fa-calculator text-warning"></i>&nbsp;
                  <div class="caption text-warning fw-500 me-2">Soma:</div>
                  <div class="caption text-warning">Produtos</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-xl-3">
          <div class="adobe-glass-card">
            <div class="adobe-card-body p-0">
              <div class="p-3 d-grid border-bottom border-block-end-dashed">
                <button class="btn btn-primary d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#addtask">
                  <i class="ri-add-circle-line fs-16 align-middle me-1"></i>Criar novo Produto
                </button>
                <div class="modal fade" id="addtask" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content adobe-glass-card">
                      <div class="modal-header" style="background: rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                        <h6 class="modal-title adobe-title d-flex align-items-center">
                          <div class="icon-circle me-2" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.3), rgba(13, 110, 253, 0.1)); width: 32px; height: 32px; border: 1px solid rgba(13, 110, 253, 0.4);">
                            <i class="ri-add-circle-line" style="color: #0d6efd; font-size: 0.9rem;"></i>
                          </div>
                          Criar novo Produto
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1); opacity: 0.8;"></button>
                      </div>
                      <form method="POST" action="{{ route('profile.checkout.create') }}" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="modal-body px-4" style="background: rgba(255, 255, 255, 0.02);">
                          <div class="row gy-4">
                            <div class="col-xl-12">
                              <div class="adobe-glass-card p-3 mb-2">
                                <label for="produto_name" class="form-label adobe-text d-flex align-items-center">
                                  <i class="fas fa-tag text-primary me-2"></i>Nome do Produto
                                </label>
                                <input type="text" class="form-control @error('produto_name') is-invalid @enderror" name="produto_name" placeholder="Digite o nome do produto" required style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff;">
                                @error('produto_name')
                                <span style="color: #ff6b6b;">{{ $message }}</span>
                                @enderror
                              </div>
                            </div>
                            <div class="col-xl-12">
                              <div class="adobe-glass-card p-3 mb-2">
                                <label for="valor_checkout" class="form-label adobe-text d-flex align-items-center">
                                  <i class="fas fa-dollar-sign text-success me-2"></i>Valor
                                </label>
                                <input type="text" class="form-control @error('valor_checkout') is-invalid @enderror" name="valor_checkout" placeholder="0,00" required style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff;">
                                @error('valor_checkout')
                                <span style="color: #ff6b6b;">{{ $message }}</span>
                                @enderror
                              </div>
                            </div>
                            <div class="col-xl-12">
                              <div class="adobe-glass-card p-3 mb-2">
                                <label for="obrigado_page" class="form-label adobe-text d-flex align-items-center">
                                  <i class="fas fa-link text-info me-2"></i>Página de Agradecimento
                                </label>
                                <input type="text" class="form-control @error('obrigado_page') is-invalid @enderror" name="obrigado_page" placeholder="https://exemplo.com/obrigado" required style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff;">
                                @error('obrigado_page')
                                <span style="color: #ff6b6b;">{{ $message }}</span>
                                @enderror
                              </div>
                            </div>
                            <div class="col-xl-12">
                              <div class="adobe-glass-card p-3 mb-2">
                                <label class="form-label adobe-text d-flex align-items-center">
                                  <i class="fas fa-image text-warning me-2"></i>Imagem do produto
                                </label>
                                <x-image-upload id="formFile" name="formFile" label="" :value="null" />
                                @error('formFile')
                                <span style="color: #ff6b6b;">{{ $message }}</span>
                                @enderror
                                <small class="adobe-text-muted">Formatos aceitos: JPG, PNG, GIF (máx. 2MB)</small>
                              </div>
                            </div>
                            <div class="col-xl-12">
                              <div class="adobe-glass-card p-3 mb-2">
                                <label class="form-label adobe-text d-flex align-items-center">
                                  <i class="fas fa-panorama text-purple me-2"></i>Banner do produto
                                </label>
                                <x-image-upload id="bannerFile" name="bannerFile" label="" :value="null" />
                                @error('bannerFile')
                                <span style="color: #ff6b6b;">{{ $message }}</span>
                                @enderror
                                <div class="alert alert-info mt-3" style="background: rgba(13, 202, 240, 0.1); border: 1px solid rgba(13, 202, 240, 0.3); border-radius: 12px; backdrop-filter: blur(10px);">
                                  <p class="adobe-text-muted mb-0"><i class="fas fa-info-circle me-1"></i>Por favor, faça o upload de um banner nas dimensões recomendadas: 1200x400 pixels.</p>
                                </div>
                              </div>
                            </div>
                            <div class="col-xl-12">
                              <div class="adobe-glass-card p-3 mb-2">
                                <label for="status" class="form-label adobe-text d-flex align-items-center">
                                  <i class="fas fa-toggle-on text-success me-2"></i>Status
                                </label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status" required style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff;">
                                  <option value="1" style="background: #1a1a1a; color: #fff;">Ativo</option>
                                  <option value="0" style="background: #1a1a1a; color: #fff;">Inativo</option>
                                </select>
                                @error('status')
                                <span style="color: #ff6b6b;">{{ $message }}</span>
                                @enderror
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer" style="background: rgba(255, 255, 255, 0.05); border-top: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                          <button type="button" class="btn" data-bs-dismiss="modal" style="background: rgba(108, 117, 125, 0.2); border: 1px solid rgba(108, 117, 125, 0.3); color: #fff; backdrop-filter: blur(10px);">
                            <i class="ri-close-line me-1"></i>Cancelar
                          </button>
                          <button type="submit" class="btn" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.8), rgba(13, 110, 253, 0.6)); border: 1px solid rgba(13, 110, 253, 0.4); color: #fff; backdrop-filter: blur(10px);">
                            <i class="ri-add-line me-1"></i>Criar Produto
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-9">
          <div class="adobe-glass-card">
            <div class="adobe-card-body">
              <div class="d-flex align-items-center mb-4">
                <div class="me-3">
                  <span class="avatar avatar-md" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.3), rgba(13, 110, 253, 0.1)); border: 1px solid rgba(13, 110, 253, 0.4); backdrop-filter: blur(15px); border-radius: 16px;">
                    <i class="ri-shopping-bag-line fs-18 adobe-text"></i>
                  </span>
                </div>
                <div class="flex-fill">
                  <h6 class="adobe-title mb-0 d-flex align-items-center"><i class="ri-shopping-bag-line me-2 fs-16 d-inline-block"></i>Lista de Produtos</h6>
                  <p class="adobe-text-muted mb-0">Gerencie todos os seus produtos e checkouts</p>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table text-nowrap table-hover">
                  <thead>
                    <tr>
                      <th>Produto</th>
                      <th>Status</th>
                      <th>Valor</th>
                      <th>Pedidos</th>
                      <th>Link Checkout</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($produtos as $produto)
                    @if(isset($produto))
                    <tr>
                      <th>
                        <div class='d-flex align-items-center'>
                          <span class='avatar avatar-xs me-2 online avatar-rounded'>
                            <img src='{{ $produto->logo_produto }}' alt='img'>
                          </span>{{ $produto->name_produto }}
                        </div>
                      </th>
                      <td><span class='badge {{ $produto->ativo ?"bg-success" :"bg-light text-dark" }}'>{{ $produto->ativo ? 'Ativo' : 'Inativo' }}</span></td>
                      <td>R$ {{ number_format((float)$produto->valor ?? 0, '2', ',', '.') }}</td>
                      <td>
                        @if($produto->vendas->count() == 0)
                        0
                        @else
                        <a href="#" onclick="{{ $pedidos = $produto->vendas }}">
                          {{$produto->vendas->count()}}
                        </a>
                        @endif
                      </td>
                      <td>
                        <a href='{{ $produto->url_checkout }}' class='btn btn-primary' target='_blank'>DIGITAL</a>
                        <a href='{{ str_replace("v1","v2", $produto->url_checkout) }}' class='btn btn-primary' target='_blank'>FÍSICO</a>
                      </td>
                      <td>
                        <div class='hstack gap-2 flex-wrap'>
                          <a href='#' class='btn btn-info btn-edit' data-bs-toggle='modal' data-bs-target='#editModal' data-id='{{ $produto->id }}' data-name='{{ $produto->name_produto }}' data-value='{{ $produto->valor }}' data-thank-you-page='{{ $produto->obrigado_page }}' data-status='{{ $produto->ativo }}'><i class='ri-edit-line'></i></a>
                          <form action="{{ route('profile.checkout.delete', $produto->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Você tem certeza que deseja excluir este produto?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                              <i class="ri-delete-bin-5-line"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>

                    <!-- Modal para Editar Produto -->
                    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h6 class="modal-title adobe-title" id="editModalLabel">
                              <i class="ri-edit-line me-2"></i>Editar Produto
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1); opacity: 0.8;"></button>
                          </div>
                          <form id="editForm" method="POST" action="{{ route('profile.checkout.edit', $produto->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body px-4">
                              <input type="hidden" id="edit_id" name="id">
                              <div class="row gy-4">
                                <div class="col-xl-12">
                                  <label for="edit_valor_checkout" class="form-label adobe-text">Valor</label>
                                  <input type="text" class="form-control" id="edit_valor_checkout" name="valor_checkout" placeholder="0,00">
                                </div>
                                <div class="col-xl-12">
                                  <label for="edit_obrigado_page" class="form-label adobe-text">Página de Agradecimento</label>
                                  <input type="text" class="form-control" id="edit_obrigado_page" name="obrigado_page" placeholder="https://exemplo.com/obrigado">
                                </div>
                                <div class="col-xl-12">
                                  <label for="edit_status" class="form-label adobe-text">Status</label>
                                  <select class="form-control" id="edit_status" name="status" required>
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="ri-close-line me-1"></i>Cancelar
                              </button>
                              <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i>Salvar Alterações
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    @else
                    <tr>
                      <td colspan='5'>Nenhum produto encontrado</td>
                    </tr>
                    @endif
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!--
        @if($pedidos)
        <div class="col-xl-12">
          <div class="card card-raised">
            <div class="card-header justify-content-between">
              <div class="card-title">Pedidos</div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table text-nowrap table-bordered">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Email</th>
                      <th>Telefone</th>
                      <th>Status</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pedidos as $pedido)
                    @if(isset($pedido))
                    <tr>
                      <th>
                        <div class='d-flex align-items-center'>
                          <span class='avatar avatar-xs me-2 online avatar-rounded'>
                            <img src='{{ $produto->logo_produto }}' alt='img'>
                          </span>{{ $pedido->nome }}
                        </div>
                      </th>
                      <td><span class='badge {{ $produto->ativo ?"bg-success" :"bg-light text-dark" }}'>{{ $produto->ativo ? 'Ativo' : 'Inativo' }}</span></td>
                      <td>{{ $pedido->email }}</td>
                      <td>{{ $pedido->phone }}</td>
                      <td>
                        @if($pedido->status =="QRCODE_GERADO")
                        <span class='badge bg-warning-transparent }}'>{{ $pedido->status }}</span>
                        @elseif($pedido->status =="PAGO")
                        <span class='badge bg-success-transparent }}'>{{ $pedido->status }}</span>
                        @else
                        <span class='badge bg-danger-transparent }}'>{{ $pedido->status }}</span>
                        @endif
                      </td>
                      <td>Ações</td>
                    </tr>


                    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h6 class="modal-title" id="editModalLabel">Editar Checkout</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <form id="editForm" method="POST" action="{{ route('profile.checkout.edit', $produto->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body px-4">
                              <input type="hidden" id="edit_id" name="id">
                              <div class="row gy-2">
                                <div class="col-xl-12">
                                  <label for="edit_valor_checkout" class="form-label">Valor</label>
                                  <input type="text" class="form-control" id="edit_valor_checkout" name="valor_checkout" placeholder="Valor">
                                </div>
                                <div class="col-xl-12">
                                  <label for="edit_obrigado_page" class="form-label">Página de Obrigado</label>
                                  <input type="text" class="form-control" id="edit_obrigado_page" name="obrigado_page" placeholder="URL da Página de Obrigado">
                                </div>
                                <div class="col-xl-12">
                                  <label for="status" class="form-label">Status</label>
                                  <select class="form-control" id="edit_status" name="status" required>
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                  </select>
                                </div>

                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                              <button type="submit" class="btn btn-primary">Salvar alterações</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    @else
                    <tr>
                      <td colspan='5'>Nenhum produto encontrado</td>
                    </tr>
                    @endif
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        @endif
      -->
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#checkoutForm').on('submit', function(e) {
        e.preventDefault(); // Evita o refresh

        var formData = new FormData(this);
        $('.text-danger').remove(); // Remove mensagens de erro antigas

        $.ajax({
          url: $(this).attr('action'),
          type: $(this).attr('method'),
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            alert('Checkout criado com sucesso!');
            location.reload(); // Recarrega a página após sucesso
          },
          error: function(xhr) {
            if (xhr.status === 422) {
              var errors = xhr.responseJSON.errors;
              for (var field in errors) {
                $('[name="' + field + '"]').after('<span class="text-danger">' + errors[field][0] + '</span>');
              }
            }
          }
        });
      });
    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Seleciona todos os botões de edição
      document.querySelectorAll(".btn-edit").forEach(button => {
        button.addEventListener("click", function() {
          // Obtém os atributos data-* do botão clicado
          let id = this.getAttribute("data-id");
          let name = this.getAttribute("data-name");
          let value = this.getAttribute("data-value");
          let thankYouPage = this.getAttribute("data-thank-you-page");
          let status = this.getAttribute("data-status");

          // Preenche os campos da modal
          document.getElementById("edit_id").value = id;
          document.getElementById("edit_valor_checkout").value = value;
          document.getElementById("edit_obrigado_page").value = thankYouPage;
          document.getElementById("edit_status").value = status;

          // Atualiza a action do formulário com o ID correto
          let form = document.getElementById("editForm");
          form.action = form.action.replace(/\/\d+$/, '/' + id);
        });
      });
    });
  </script>

  <script>
    function getVendas(pedidos) {
      console.log(pedidos);
    }
  </script>
</x-app-layout>
