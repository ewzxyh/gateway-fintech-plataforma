<style>
/* Estilos para o Checkout Builder Drag & Drop */
.checkout-builder {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.builder-sidebar {
  /* background: #f8f9fa; */
  border-right: 1px solid #e9ecef;
  height: 100vh;
  overflow-y: auto;
}

.builder-main {
  /* background: #ffffff; */;
  height: 100vh;
  overflow-y: auto;
}

.builder-preview {
  /* /* background: #ffffff; */; */
  border: 1px solid #e9ecef;
  border-radius: 8px;
  min-height: 600px;
  position: relative;
}

/* Componentes arrastáveis */
.draggable-component {
  /* /* background: #ffffff; */; */
  border: 2px dashed #dee2e6;
  border-radius: 8px;
  padding: 16px;
  margin: 8px 0;
  cursor: grab;
  transition: all 0.3s ease;
  user-select: none;
}

.draggable-component:hover {
  border-color: #007bff;
  background: #f8f9ff;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.draggable-component.dragging {
  opacity: 0.5;
  transform: rotate(5deg);
}

/* Área de drop */
.drop-zone {
  min-height: 100px;
  border: 2px dashed #dee2e6;
  border-radius: 8px;
  padding: 16px;
  margin: 8px 0;
  transition: all 0.3s ease;
  position: relative;
}

.drop-zone.drag-over {
  border-color: #28a745;
  background: #f8fff8;
}

.drop-zone.empty::before {
  content:"Arraste componentes aqui";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #6c757d;
  font-size: 14px;
  pointer-events: none;
}

/* Componentes no preview */
.preview-component {
  /* background: #ffffff; */;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 16px;
  margin: 8px 0;
  position: relative;
  transition: all 0.3s ease;
  /* Garantir que controles funcionem */
  isolation: isolate;
  cursor: move; /* Indica que pode ser arrastado */
}

.preview-component:hover {
  border-color: #007bff;
  box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
}

.preview-component.selected {
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

/* Componente sendo arrastado */
.preview-component.dragging-preview {
  opacity: 0.5;
  cursor: grabbing;
  transform: rotate(2deg);
}

/* Indicador de onde o componente será solto */
.preview-component.drag-over-preview {
  border-top: 3px solid #28a745;
  padding-top: 20px;
  background: rgba(40, 167, 69, 0.05);
}

/* Adicionar uma linha indicadora visual */
.preview-component.drag-over-preview::before {
  content: '';
  position: absolute;
  top: -3px;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #28a745, #20c997);
  box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
  animation: pulse-line 1s ease-in-out infinite;
}

@keyframes pulse-line {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}

/* Garantir que o conteúdo do componente não bloqueie os controles */
.preview-component > *:not(.component-controls) {
  position: relative;
  z-index: 1;
  pointer-events: auto;
}

/* Controles de componente */
.component-controls {
  position: absolute;
  top: -10px;
  right: -10px;
  display: flex;
  gap: 8px;
  opacity: 0;
  transition: all 0.3s ease;
  z-index: 1000;
  pointer-events: auto;
}

.preview-component:hover .component-controls {
  opacity: 1;
}

/* Botões individuais dos controles */
.component-control-btn {
  background: #007bff;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  border: none;
}

.component-control-btn:hover {
  transform: scale(1.15);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.component-control-btn.btn-edit {
  background: #007bff;
}

.component-control-btn.btn-edit:hover {
  background: #0056b3;
}

.component-control-btn.btn-delete {
  background: #dc3545;
}

.component-control-btn.btn-delete:hover {
  background: #bd2130;
}

.component-control-btn.btn-move {
  background: #6c757d;
  cursor: move;
}

.component-control-btn.btn-move:hover {
  background: #5a6268;
}

.component-control-btn i {
  pointer-events: none;
  font-size: 14px;
}

/* Tooltip nos botões */
.component-control-btn[title]:hover::after {
  content: attr(title);
  position: absolute;
  bottom: -30px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  white-space: nowrap;
  z-index: 10000;
  pointer-events: none;
}

.component-control-btn[title]:hover::before {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  border: 4px solid transparent;
  border-bottom-color: rgba(0, 0, 0, 0.8);
  pointer-events: none;
  z-index: 10001;
}

/* Forçar visibilidade e funcionalidade dos controles */
.component-controls:active {
  transform: scale(0.95);
}

/* Garantir que controles fiquem acima de tudo, incluindo overlays de drag */
.preview-component.dragging .component-controls {
  z-index: 1001;
  opacity: 1;
}

/* Feedback visual adicional */
.component-controls::before {
  content: '';
  position: absolute;
  inset: -4px;
  border-radius: 50%;
  background: rgba(0, 123, 255, 0.1);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.component-controls:hover::before {
  opacity: 1;
}

/* Painel de propriedades */
.properties-panel {
  /* background: #ffffff; */;
  border-left: 1px solid #e9ecef;
  height: 100vh;
  overflow-y: auto;
}

.property-group {
  margin-bottom: 24px;
}

.property-group h6 {
  color: #495057;
  font-weight: 600;
  margin-bottom: 12px;
  padding-bottom: 8px;
  border-bottom: 1px solid #e9ecef;
}

.property-item {
  margin-bottom: 16px;
}

.property-item label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #495057;
  margin-bottom: 6px;
}

.property-item input,
.property-item select,
.property-item textarea {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 6px;
  font-size: 14px;
  transition: border-color 0.3s ease;
}

.property-item input:focus,
.property-item select:focus,
.property-item textarea:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

/* Color picker customizado */
.color-picker-group {
  display: flex;
  align-items: center;
  gap: 12px;
}

.color-picker {
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.color-picker:hover {
  transform: scale(1.1);
}

.color-value {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 6px;
  font-family: monospace;
  font-size: 14px;
}

/* Tabs de customização */
.customization-tabs {
  border-bottom: 1px solid #e9ecef;
  margin-bottom: 24px;
}

.customization-tabs .nav-link {
  border: none;
  border-bottom: 2px solid transparent;
  color: #6c757d;
  font-weight: 500;
  padding: 12px 16px;
  transition: all 0.3s ease;
}

.customization-tabs .nav-link:hover {
  color: #007bff;
  border-bottom-color: #007bff;
}

.customization-tabs .nav-link.active {
  color: #007bff;
  border-bottom-color: #007bff;
  background: none;
}

/* Botões de ação */
.action-buttons {
  position: fixed;
  bottom: 24px;
  right: 24px;
  display: flex;
  gap: 12px;
  z-index: 1000;
}

.btn-builder {
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-primary-builder {
  background: #007bff;
  color: white;
}

.btn-primary-builder:hover {
  background: #0056b3;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 123, 255, 0.3);
}

.btn-success-builder {
  background: #28a745;
  color: white;
}

.btn-success-builder:hover {
  background: #1e7e34;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
}

/* Responsividade */
@media (max-width: 768px) {
  .builder-sidebar,
  .properties-panel {
    position: fixed;
    top: 0;
    left: -100%;
    width: 80%;
    height: 100vh;
    z-index: 1000;
    transition: left 0.3s ease;
  }
  
  .builder-sidebar.open,
  .properties-panel.open {
    left: 0;
  }
  
  .action-buttons {
    bottom: 16px;
    right: 16px;
    flex-direction: column;
  }
}

/* Animações */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.slide-in {
  animation: slideIn 0.3s ease;
}

/* Temas pré-definidos */
.theme-preview {
  width: 60px;
  height: 40px;
  border-radius: 6px;
  border: 2px solid #e9ecef;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.theme-preview:hover {
  border-color: #007bff;
  transform: scale(1.1);
}

.theme-preview.selected {
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
}

.theme-preview::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 30%;
  background: var(--primary-color, #007bff);
}

.theme-preview::after {
  content: '';
  position: absolute;
  top: 30%;
  left: 0;
  right: 0;
  height: 70%;
  background: var(--secondary-color, #ffffff);
}
</style>

<div class="checkout-builder">
  <div class="row g-0">
    <!-- Sidebar com componentes -->
    <div class="col-md-3 builder-sidebar">
      <div class="p-3">
        <h5 class="mb-3">Componentes</h5>
        
        <!-- Componentes disponíveis -->
        <div class="draggable-component" draggable="true" data-component="header">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-window-maximize text-primary me-2"></i>
            <span>Header</span>
          </div>
        </div>
        
        <div class="draggable-component" draggable="true" data-component="product">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-box text-primary me-2"></i>
            <span>Produto</span>
          </div>
        </div>
        
        <div class="draggable-component" draggable="true" data-component="pricing">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-tag text-primary me-2"></i>
            <span>Preços</span>
          </div>
        </div>
        
        <div class="draggable-component" draggable="true" data-component="form">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-form text-primary me-2"></i>
            <span>Formulário</span>
          </div>
        </div>
        
        <div class="draggable-component" draggable="true" data-component="testimonials">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-comments text-primary me-2"></i>
            <span>Depoimentos</span>
          </div>
        </div>
        
        <div class="draggable-component" draggable="true" data-component="trust-badges">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-shield-check text-primary me-2"></i>
            <span>Selos de Confiança</span>
          </div>
        </div>
        
        <div class="draggable-component" draggable="true" data-component="timer">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-clock text-primary me-2"></i>
            <span>Timer</span>
          </div>
        </div>
        
        <div class="draggable-component" draggable="true" data-component="footer">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-window-minimize text-primary me-2"></i>
            <span>Footer</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Área principal de preview -->
    <div class="col-md-6 builder-main">
      <div class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5>Preview do Checkout</h5>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleMobilePreview()">
              <i class="fa-solid fa-mobile-screen"></i> Mobile
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleDesktopPreview()">
              <i class="fa-solid fa-desktop"></i> Desktop
            </button>
          </div>
        </div>
        
        <div class="builder-preview" id="checkout-preview">
          <!-- Componentes serão inseridos aqui via drag & drop -->
          <div class="drop-zone empty" id="main-drop-zone">
            <!-- Área principal de drop -->
          </div>
        </div>
      </div>
    </div>
    
    <!-- Painel de propriedades -->
    <div class="col-md-3 properties-panel">
      <div class="p-3">
        <h5 class="mb-3">Propriedades</h5>
        
        <!-- Tabs de customização -->
        <ul class="nav nav-tabs customization-tabs" id="customizationTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="theme-tab" data-bs-toggle="tab" data-bs-target="#theme" type="button" role="tab">
              <i class="fa-solid fa-palette me-1"></i> Tema
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="layout-tab" data-bs-toggle="tab" data-bs-target="#layout" type="button" role="tab">
              <i class="fa-solid fa-th-large me-1"></i> Layout
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="component-tab" data-bs-toggle="tab" data-bs-target="#component" type="button" role="tab">
              <i class="fa-solid fa-cog me-1"></i> Componente
            </button>
          </li>
        </ul>
        
        <div class="tab-content" id="customizationTabContent">
          <!-- Tab Tema -->
          <div class="tab-pane fade show active" id="theme" role="tabpanel">
            <div class="property-group">
              <h6>Tema Pré-definido</h6>
              <div class="d-flex gap-2 flex-wrap">
                <div class="theme-preview" data-theme="default" style="--primary-color: #007bff; --secondary-color: #ffffff;"></div>
                <div class="theme-preview" data-theme="green" style="--primary-color: #28a745; --secondary-color: #ffffff;"></div>
                <div class="theme-preview" data-theme="purple" style="--primary-color: #6f42c1; --secondary-color: #ffffff;"></div>
                <div class="theme-preview" data-theme="orange" style="--primary-color: #fd7e14; --secondary-color: #ffffff;"></div>
                <div class="theme-preview" data-theme="dark" style="--primary-color: #343a40; --secondary-color: #ffffff;"></div>
              </div>
            </div>
            
            <div class="property-group">
              <h6>Cores Personalizadas</h6>
              
              <div class="property-item">
                <label>Cor Primária</label>
                <div class="color-picker-group">
                  <input type="color" class="color-picker" id="primary-color" value="{{ $checkout->primary_color ?? '#007bff' }}">
                  <input type="text" class="color-value" id="primary-color-value" value="{{ $checkout->primary_color ?? '#007bff' }}">
                </div>
              </div>
              
              <div class="property-item">
                <label>Cor Secundária</label>
                <div class="color-picker-group">
                  <input type="color" class="color-picker" id="secondary-color" value="{{ $checkout->secondary_color ?? '#ffffff' }}">
                  <input type="text" class="color-value" id="secondary-color-value" value="{{ $checkout->secondary_color ?? '#ffffff' }}">
                </div>
              </div>
              
              <div class="property-item">
                <label>Cor de Destaque</label>
                <div class="color-picker-group">
                  <input type="color" class="color-picker" id="accent-color" value="{{ $checkout->accent_color ?? '#ff6b35' }}">
                  <input type="text" class="color-value" id="accent-color-value" value="{{ $checkout->accent_color ?? '#ff6b35' }}">
                </div>
              </div>
              
              <div class="property-item">
                <label>Cor do Texto</label>
                <div class="color-picker-group">
                  <input type="color" class="color-picker" id="text-color" value="{{ $checkout->text_color ?? '#333333' }}">
                  <input type="text" class="color-value" id="text-color-value" value="{{ $checkout->text_color ?? '#333333' }}">
                </div>
              </div>
              
              <div class="property-item">
                <label>Cor de Fundo</label>
                <div class="color-picker-group">
                  <input type="color" class="color-picker" id="background-color" value="{{ $checkout->background_color ?? '#f5f5f5' }}">
                  <input type="text" class="color-value" id="background-color-value" value="{{ $checkout->background_color ?? '#f5f5f5' }}">
                </div>
              </div>
            </div>
            
            <div class="property-group">
              <h6>Tipografia</h6>
              
              <div class="property-item">
                <label>Família da Fonte</label>
                <select id="font-family">
                  <option value="Inter" {{ ($checkout->font_family ?? 'Inter') === 'Inter' ? 'selected' : '' }}>Inter</option>
                  <option value="Roboto" {{ ($checkout->font_family ?? 'Inter') === 'Roboto' ? 'selected' : '' }}>Roboto</option>
                  <option value="Open Sans" {{ ($checkout->font_family ?? 'Inter') === 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                  <option value="Lato" {{ ($checkout->font_family ?? 'Inter') === 'Lato' ? 'selected' : '' }}>Lato</option>
                  <option value="Poppins" {{ ($checkout->font_family ?? 'Inter') === 'Poppins' ? 'selected' : '' }}>Poppins</option>
                </select>
              </div>
              
              <div class="property-item">
                <label>Tamanho Base</label>
                <input type="text" id="font-size-base" value="{{ $checkout->font_size_base ?? '16px' }}" placeholder="16px">
              </div>
            </div>
            
            <div class="property-group">
              <h6>Espaçamento</h6>
              
              <div class="property-item">
                <label>Border Radius</label>
                <input type="text" id="border-radius" value="{{ $checkout->border_radius ?? '8px' }}" placeholder="8px">
              </div>
              
              <div class="property-item">
                <label>Padding Pequeno</label>
                <input type="text" id="padding-small" value="{{ $checkout->padding_small ?? '8px' }}" placeholder="8px">
              </div>
              
              <div class="property-item">
                <label>Padding Médio</label>
                <input type="text" id="padding-medium" value="{{ $checkout->padding_medium ?? '16px' }}" placeholder="16px">
              </div>
              
              <div class="property-item">
                <label>Padding Grande</label>
                <input type="text" id="padding-large" value="{{ $checkout->padding_large ?? '24px' }}" placeholder="24px">
              </div>
            </div>
          </div>
          
          <!-- Tab Layout -->
          <div class="tab-pane fade" id="layout" role="tabpanel">
            <div class="property-group">
              <h6>Tipo de Layout</h6>
              <div class="property-item">
                <select id="layout-type">
                  <option value="single-column" {{ ($checkout->layout_type ?? 'single-column') === 'single-column' ? 'selected' : '' }}>Coluna Única</option>
                  <option value="two-column" {{ ($checkout->layout_type ?? 'single-column') === 'two-column' ? 'selected' : '' }}>Duas Colunas</option>
                  <option value="sidebar" {{ ($checkout->layout_type ?? 'single-column') === 'sidebar' ? 'selected' : '' }}>Com Sidebar</option>
                </select>
              </div>
            </div>
            
            <div class="property-group">
              <h6>Sidebar</h6>
              <div class="property-item">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="sidebar-enabled" {{ ($checkout->sidebar_enabled ?? false) ? 'checked' : '' }}>
                  <label class="form-check-label" for="sidebar-enabled">Habilitar Sidebar</label>
                </div>
              </div>
              
              <div class="property-item">
                <label>Posição da Sidebar</label>
                <select id="sidebar-position">
                  <option value="left" {{ ($checkout->sidebar_position ?? 'right') === 'left' ? 'selected' : '' }}>Esquerda</option>
                  <option value="right" {{ ($checkout->sidebar_position ?? 'right') === 'right' ? 'selected' : '' }}>Direita</option>
                </select>
              </div>
              
              <div class="property-item">
                <label>Largura da Sidebar (px)</label>
                <input type="number" id="sidebar-width" value="{{ $checkout->sidebar_width ?? 300 }}" min="200" max="500">
              </div>
            </div>
            
            <div class="property-group">
              <h6>Animações</h6>
              <div class="property-item">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="animations-enabled" {{ ($checkout->animations_enabled ?? true) ? 'checked' : '' }}>
                  <label class="form-check-label" for="animations-enabled">Habilitar Animações</label>
                </div>
              </div>
              
              <div class="property-item">
                <label>Duração da Animação</label>
                <input type="text" id="animation-duration" value="{{ $checkout->animation_duration ?? '0.3s' }}" placeholder="0.3s">
              </div>
              
              <div class="property-item">
                <label>Easing</label>
                <select id="animation-easing">
                  <option value="ease" {{ ($checkout->animation_easing ?? 'ease-in-out') === 'ease' ? 'selected' : '' }}>Ease</option>
                  <option value="ease-in" {{ ($checkout->animation_easing ?? 'ease-in-out') === 'ease-in' ? 'selected' : '' }}>Ease In</option>
                  <option value="ease-out" {{ ($checkout->animation_easing ?? 'ease-in-out') === 'ease-out' ? 'selected' : '' }}>Ease Out</option>
                  <option value="ease-in-out" {{ ($checkout->animation_easing ?? 'ease-in-out') === 'ease-in-out' ? 'selected' : '' }}>Ease In Out</option>
                  <option value="linear" {{ ($checkout->animation_easing ?? 'ease-in-out') === 'linear' ? 'selected' : '' }}>Linear</option>
                </select>
              </div>
            </div>
          </div>
          
          <!-- Tab Componente -->
          <div class="tab-pane fade" id="component" role="tabpanel">
            <div class="property-group">
              <h6>Componente Selecionado</h6>
              <p class="text-muted">Selecione um componente no preview para editar suas propriedades.</p>
            </div>
            
            <div id="component-properties">
              <!-- Propriedades específicas do componente serão carregadas aqui -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Botões de ação -->
  <div class="action-buttons">
    <button type="button" class="btn-builder btn-primary-builder" onclick="previewCheckout()">
      <i class="fa-solid fa-eye me-2"></i> Preview
    </button>
    <button type="button" class="btn-builder btn-success-builder" onclick="saveCheckout()">
      <i class="fa-solid fa-save me-2"></i> Salvar
    </button>
  </div>
</div>

<script>
// Sistema de Drag & Drop
let draggedElement = null;
let selectedComponent = null;
let draggedPreviewComponent = null; // Para arrastar componentes dentro do preview

// Inicializar drag & drop
document.addEventListener('DOMContentLoaded', function() {
  initializeDragAndDrop();
  initializeColorPickers();
  initializeThemeSelection();
  loadExistingLayout();
  initializeComponentControls();
});

// Event listener global para garantir que os controles sempre funcionem
function initializeComponentControls() {
  // Usar event delegation para capturar cliques em todos os controles
  document.addEventListener('click', function(e) {
    const control = e.target.closest('.component-controls');
    if (control) {
      e.preventDefault();
      e.stopPropagation();
      
      const componentId = control.dataset.componentId || control.parentElement.id;
      if (componentId) {
        selectComponent(componentId);
        console.log('Controle clicado via delegation:', componentId);
      }
    }
  }, true); // useCapture = true para maior prioridade
  
  // Reforçar z-index e pointer-events dos controles existentes
  setInterval(() => {
    document.querySelectorAll('.component-controls').forEach(control => {
      control.style.zIndex = '9999';
      control.style.pointerEvents = 'all';
    });
  }, 500);
}

function initializeDragAndDrop() {
  // Componentes arrastáveis
  const draggableComponents = document.querySelectorAll('.draggable-component');
  draggableComponents.forEach(component => {
    component.addEventListener('dragstart', handleDragStart);
    component.addEventListener('dragend', handleDragEnd);
  });
  
  // Áreas de drop
  const dropZones = document.querySelectorAll('.drop-zone');
  dropZones.forEach(zone => {
    zone.addEventListener('dragover', handleDragOver);
    zone.addEventListener('drop', handleDrop);
    zone.addEventListener('dragenter', handleDragEnter);
    zone.addEventListener('dragleave', handleDragLeave);
  });
}

function handleDragStart(e) {
  draggedElement = e.target;
  e.target.classList.add('dragging');
  e.dataTransfer.effectAllowed = 'move';
  e.dataTransfer.setData('text/html', e.target.outerHTML);
}

function handleDragEnd(e) {
  e.target.classList.remove('dragging');
  draggedElement = null;
}

function handleDragOver(e) {
  e.preventDefault();
  e.dataTransfer.dropEffect = 'move';
}

function handleDragEnter(e) {
  e.preventDefault();
  e.target.classList.add('drag-over');
}

function handleDragLeave(e) {
  e.target.classList.remove('drag-over');
}

function handleDrop(e) {
  e.preventDefault();
  e.target.classList.remove('drag-over');
  
  if (e.target.classList.contains('drop-zone')) {
    const componentType = draggedElement.dataset.component;
    const componentHTML = e.dataTransfer.getData('text/html');
    
    // Criar componente no preview
    createPreviewComponent(componentType, e.target);
    
    // Remover classe empty se existir
    e.target.classList.remove('empty');
  }
}

function createPreviewComponent(type, dropZone) {
  const componentId = 'component_' + Date.now();
  const componentHTML = generateComponentHTML(type, componentId);
  
  const componentElement = document.createElement('div');
  componentElement.className = 'preview-component';
  componentElement.id = componentId;
  componentElement.innerHTML = componentHTML;
  
  // Criar controles usando DOM (NÃO usar innerHTML += pois destrói event listeners)
  const controlsDiv = document.createElement('div');
  controlsDiv.className = 'component-controls';
  controlsDiv.setAttribute('data-component-id', componentId);
  
  // Botão de arrastar (move)
  const moveBtn = document.createElement('button');
  moveBtn.className = 'component-control-btn btn-move';
  moveBtn.setAttribute('title', 'Arrastar para reordenar');
  moveBtn.innerHTML = '<i class="fa-solid fa-grip-vertical" style="pointer-events: none;"></i>';
  moveBtn.addEventListener('mousedown', (e) => {
    // O drag será iniciado pelo atributo draggable do componente
    console.log('🖐️ Iniciando drag do componente:', componentId);
  });
  
  // Botão de editar
  const editBtn = document.createElement('button');
  editBtn.className = 'component-control-btn btn-edit';
  editBtn.setAttribute('title', 'Editar propriedades');
  editBtn.innerHTML = '<i class="fa-solid fa-cog" style="pointer-events: none;"></i>';
  editBtn.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    selectComponent(componentId);
    console.log('✅ Botão editar clicado:', componentId);
  }, true);
  
  // Botão de deletar
  const deleteBtn = document.createElement('button');
  deleteBtn.className = 'component-control-btn btn-delete';
  deleteBtn.setAttribute('title', 'Remover componente');
  deleteBtn.innerHTML = '<i class="fa-solid fa-trash" style="pointer-events: none;"></i>';
  deleteBtn.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    removeComponent(componentId);
    console.log('🗑️ Botão deletar clicado:', componentId);
  }, true);
  
  // Adicionar botões aos controles
  controlsDiv.appendChild(moveBtn);
  controlsDiv.appendChild(editBtn);
  controlsDiv.appendChild(deleteBtn);
  
  // Adicionar controles ao componente usando appendChild (preserva eventos)
  componentElement.appendChild(controlsDiv);
  
  // Tornar o componente arrastável para reordenar
  componentElement.setAttribute('draggable', 'true');
  componentElement.addEventListener('dragstart', handleComponentDragStart);
  componentElement.addEventListener('dragend', handleComponentDragEnd);
  componentElement.addEventListener('dragover', handleComponentDragOver);
  componentElement.addEventListener('drop', handleComponentDrop);
  
  // Event listener no componente (menor prioridade)
  componentElement.addEventListener('click', (e) => {
    // Só seleciona se não clicou nos controles
    if (!e.target.closest('.component-controls')) {
      selectComponent(componentId);
    }
  });
  
  // Inserir no drop zone
  if (dropZone.classList.contains('empty')) {
    dropZone.innerHTML = '';
    dropZone.classList.remove('empty');
  }
  dropZone.appendChild(componentElement);
  
  // Aplicar estilos atuais
  applyCurrentStyles(componentElement);
  
  console.log('🎯 Componente criado:', componentId, 'Controles adicionados:', controlsDiv);
}

function generateComponentHTML(type, id) {
  const components = {
    'header': `
      <div class="component-header">
        <h2>Header do Checkout</h2>
        <p>Área para logo e título do produto</p>
      </div>
    `,
    'product': `
      <div class="component-product">
        <img src="/assets/images/product-placeholder.jpg" alt="Produto" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
        <h3>{{ $checkout->produto_name ?? 'Nome do Produto' }}</h3>
        <p>{{ $checkout->produto_descricao ?? 'Descrição do produto' }}</p>
      </div>
    `,
    'pricing': `
      <div class="component-pricing">
        <div class="price-display">
          <span class="price-old">R$ {{ number_format($checkout->produto_de_valor ?? 0, 2, ',', '.') }}</span>
          <span class="price-current">R$ {{ number_format($checkout->produto_valor ?? 0, 2, ',', '.') }}</span>
        </div>
        <div class="price-installments">ou 12x de R$ {{ number_format(($checkout->produto_valor ?? 0) / 12, 2, ',', '.') }}</div>
      </div>
    `,
    'form': `
      <div class="component-form">
        <h4>Dados para Pagamento</h4>
        <form>
          <div class="form-group">
            <label>Nome Completo</label>
            <input type="text" placeholder="Digite seu nome">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" placeholder="Digite seu email">
          </div>
          <div class="form-group">
            <label>CPF</label>
            <input type="text" placeholder="000.000.000-00">
          </div>
        </form>
      </div>
    `,
    'testimonials': `
      <div class="component-testimonials">
        <h4>Depoimentos</h4>
        <div class="testimonial-item">
          <img src="/assets/images/avatar_default.png" alt="Cliente" style="width: 50px; height: 50px; border-radius: 50%;">
          <div>
            <strong>João Silva</strong>
            <p>"Produto excelente, recomendo!"</p>
          </div>
        </div>
      </div>
    `,
    'trust-badges': `
      <div class="component-trust-badges">
        <div class="trust-badge">
          <i class="fa-solid fa-shield-check"></i>
          <span>Compra Segura</span>
        </div>
        <div class="trust-badge">
          <i class="fa-solid fa-truck"></i>
          <span>Entrega Rápida</span>
        </div>
        <div class="trust-badge">
          <i class="fa-solid fa-undo"></i>
          <span>Garantia</span>
        </div>
      </div>
    `,
    'timer': `
      <div class="component-timer">
        <div class="timer-display">
          <span class="timer-text">Oferta termina em:</span>
          <div class="timer-countdown">
            <span class="timer-hours">02</span>:
            <span class="timer-minutes">30</span>:
            <span class="timer-seconds">45</span>
          </div>
        </div>
      </div>
    `,
    'footer': `
      <div class="component-footer">
        <p>© 2025 {{ env('APP_NAME') }}. Todos os direitos reservados.</p>
        <div class="footer-links">
          <a href="#">Termos de Uso</a>
          <a href="#">Política de Privacidade</a>
          <a href="#">Contato</a>
        </div>
      </div>
    `
  };
  
  return components[type] || '<div>Componente não encontrado</div>';
}

function selectComponent(componentId) {
  console.log('🎯 selectComponent chamada para:', componentId);
  
  // Remover seleção anterior
  document.querySelectorAll('.preview-component').forEach(comp => {
    comp.classList.remove('selected');
    // Resetar opacidade dos controles
    const controls = comp.querySelector('.component-controls');
    if (controls) {
      controls.style.opacity = '0';
    }
  });
  
  // Selecionar novo componente
  const component = document.getElementById(componentId);
  console.log('📦 Componente encontrado:', component);
  
  if (component) {
    component.classList.add('selected');
    selectedComponent = componentId;
    
    console.log('✅ Componente marcado como selected');
    
    // Forçar controles visíveis no componente selecionado
    const controls = component.querySelector('.component-controls');
    if (controls) {
      controls.style.opacity = '1';
      controls.style.pointerEvents = 'all';
      controls.style.zIndex = '9999';
      console.log('🔧 Controles tornados visíveis');
    }
    
    // Carregar propriedades do componente
    try {
      loadComponentProperties(componentId);
      console.log('📝 Propriedades carregadas');
    } catch (error) {
      console.error('❌ Erro ao carregar propriedades:', error);
    }
    
    // Ativar aba de componente no painel de propriedades
    const componentTab = document.querySelector('#component-tab') || document.querySelector('[data-bs-target="#component"]');
    console.log('📑 Aba de componente encontrada:', componentTab);
    if (componentTab) {
      // Tentar usar Bootstrap Tab API primeiro
      try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
          const tab = new bootstrap.Tab(componentTab);
          tab.show();
          console.log('🖱️ Aba ativada via Bootstrap Tab API');
        } else {
          // Fallback: click direto
          componentTab.click();
          console.log('🖱️ Aba ativada via click() - fallback');
        }
      } catch (error) {
        console.warn('⚠️ Erro ao ativar aba, tentando fallback:', error);
        componentTab.click();
      }
    } else {
      console.warn('⚠️ Aba #component não encontrada!');
      console.log('🔍 Tentando buscar todas as abas disponíveis...');
      const allTabs = document.querySelectorAll('[data-bs-toggle="tab"]');
      console.log('📑 Abas encontradas:', allTabs);
    }
  } else {
    console.error('❌ Componente não encontrado com ID:', componentId);
  }
}

function loadComponentProperties(componentId) {
  console.log('📂 loadComponentProperties para:', componentId);
  
  const component = document.getElementById(componentId);
  if (!component) {
    console.error('❌ Componente não encontrado para carregar propriedades:', componentId);
    return;
  }
  
  console.log('📦 Componente HTML:', component);
  console.log('🔍 Classes do componente:', component.className);
  console.log('🔍 Conteúdo do componente:', component.innerHTML.substring(0, 200));
  
  // Tentar encontrar o elemento com classe component-*
  const componentElement = component.querySelector('[class*="component-"]');
  console.log('🎨 Elemento com classe component-*:', componentElement);
  
  if (!componentElement) {
    console.warn('⚠️ Não foi possível encontrar elemento com classe component-*');
    document.getElementById('component-properties').innerHTML = `
      <div class="alert alert-warning">
        <i class="fa-solid fa-exclamation-triangle me-2"></i>
        Não foi possível carregar as propriedades deste componente.
      </div>
    `;
    return;
  }
  
  const componentType = componentElement.className.split(' ')[0].replace('component-', '');
  console.log('🏷️ Tipo do componente detectado:', componentType);
  
  const propertiesHTML = generateComponentProperties(componentType);
  const propertiesContainer = document.getElementById('component-properties');
  
  if (propertiesContainer) {
    propertiesContainer.innerHTML = propertiesHTML;
    console.log('✅ Propriedades inseridas no DOM');
  } else {
    console.error('❌ Container #component-properties não encontrado!');
  }
}

function generateComponentProperties(type) {
  const properties = {
    'header': `
      <div class="property-group">
        <h6>Propriedades do Header</h6>
        <div class="property-item">
          <label>Título</label>
          <input type="text" id="header-title" value="{{ $checkout->produto_name ?? 'Nome do Produto' }}">
        </div>
        <div class="property-item">
          <label>Subtítulo</label>
          <input type="text" id="header-subtitle" value="Descrição do produto">
        </div>
        <div class="property-item">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="header-logo-enabled">
            <label class="form-check-label" for="header-logo-enabled">Mostrar Logo</label>
          </div>
        </div>
      </div>
    `,
    'product': `
      <div class="property-group">
        <h6>Propriedades do Produto</h6>
        <div class="property-item">
          <label>Nome do Produto</label>
          <input type="text" id="product-name" value="{{ $checkout->produto_name ?? 'Nome do Produto' }}">
        </div>
        <div class="property-item">
          <label>Descrição</label>
          <textarea id="product-description" rows="3">{{ $checkout->produto_descricao ?? 'Descrição do produto' }}</textarea>
        </div>
        <div class="property-item">
          <label>Imagem do Produto</label>
          <input type="file" id="product-image" accept="image/*">
        </div>
      </div>
    `,
    'pricing': `
      <div class="property-group">
        <h6>Propriedades de Preço</h6>
        <div class="property-item">
          <label>Preço Atual</label>
          <input type="number" id="price-current" value="{{ $checkout->produto_valor ?? 0 }}" step="0.01">
        </div>
        <div class="property-item">
          <label>Preço Original</label>
          <input type="number" id="price-original" value="{{ $checkout->produto_de_valor ?? 0 }}" step="0.01">
        </div>
        <div class="property-item">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="price-installments-enabled">
            <label class="form-check-label" for="price-installments-enabled">Mostrar Parcelamento</label>
          </div>
        </div>
      </div>
    `
  };
  
  return properties[type] || '<div class="property-group"><h6>Propriedades</h6><p class="text-muted">Nenhuma propriedade específica disponível para este componente.</p></div>';
}

function initializeColorPickers() {
  const colorPickers = document.querySelectorAll('.color-picker');
  colorPickers.forEach(picker => {
    picker.addEventListener('change', function() {
      const valueInput = document.getElementById(this.id + '-value');
      if (valueInput) {
        valueInput.value = this.value;
        applyColorToPreview(this.id.replace('-color', ''), this.value);
      }
    });
  });
  
  const colorValues = document.querySelectorAll('.color-value');
  colorValues.forEach(valueInput => {
    valueInput.addEventListener('input', function() {
      const picker = document.getElementById(this.id.replace('-value', ''));
      if (picker && /^#[0-9A-F]{6}$/i.test(this.value)) {
        picker.value = this.value;
        const colorName = this.id.replace('-color-value', '');
        applyColorToPreview(colorName, this.value);
      }
    });
  });
}

function applyColorToPreview(colorName, colorValue) {
  const preview = document.getElementById('checkout-preview');
  if (preview) {
    preview.style.setProperty(`--${colorName}-color`, colorValue);
  }
}

function initializeThemeSelection() {
  const themePreviews = document.querySelectorAll('.theme-preview');
  themePreviews.forEach(preview => {
    preview.addEventListener('click', function() {
      // Remover seleção anterior
      themePreviews.forEach(p => p.classList.remove('selected'));
      
      // Selecionar tema
      this.classList.add('selected');
      
      // Aplicar tema
      applyTheme(this.dataset.theme);
    });
  });
}

function applyTheme(themeName) {
  const themes = {
    'default': {
      primary: '#007bff',
      secondary: '#ffffff',
      accent: '#ff6b35',
      text: '#333333',
      background: '#f5f5f5'
    },
    'green': {
      primary: '#28a745',
      secondary: '#ffffff',
      accent: '#20c997',
      text: '#333333',
      background: '#f8fff8'
    },
    'purple': {
      primary: '#6f42c1',
      secondary: '#ffffff',
      accent: '#e83e8c',
      text: '#333333',
      background: '#f8f5ff'
    },
    'orange': {
      primary: '#fd7e14',
      secondary: '#ffffff',
      accent: '#ffc107',
      text: '#333333',
      background: '#fff8f0'
    },
    'dark': {
      primary: '#343a40',
      secondary: '#ffffff',
      accent: '#6c757d',
      text: '#ffffff',
      background: '#212529'
    }
  };
  
  const theme = themes[themeName];
  if (theme) {
    Object.keys(theme).forEach(colorName => {
      const picker = document.getElementById(`${colorName}-color`);
      const value = document.getElementById(`${colorName}-color-value`);
      
      if (picker && value) {
        picker.value = theme[colorName];
        value.value = theme[colorName];
        applyColorToPreview(colorName, theme[colorName]);
      }
    });
  }
}

// ====================================
// Funções para Remover e Reordenar Componentes
// ====================================

function removeComponent(componentId) {
  console.log('🗑️ Removendo componente:', componentId);
  
  // Confirmar antes de remover
  if (confirm('Deseja realmente remover este componente?')) {
    const component = document.getElementById(componentId);
    if (component) {
      // Animação de saída
      component.style.transition = 'all 0.3s ease';
      component.style.opacity = '0';
      component.style.transform = 'scale(0.8)';
      
      setTimeout(() => {
        component.remove();
        console.log('✅ Componente removido:', componentId);
        
        // Limpar seleção se era o componente selecionado
        if (selectedComponent === componentId) {
          selectedComponent = null;
          document.getElementById('component-properties').innerHTML = `
            <div class="text-center text-muted py-4">
              <i class="fa-solid fa-mouse-pointer fa-2x mb-3"></i>
              <p>Selecione um componente no preview para editar</p>
            </div>
          `;
        }
      }, 300);
    }
  }
}

// Drag & Drop para reordenar componentes dentro do preview
function handleComponentDragStart(e) {
  draggedPreviewComponent = e.target;
  e.target.classList.add('dragging-preview');
  e.dataTransfer.effectAllowed = 'move';
  console.log('🎯 Iniciando drag de reordenação:', e.target.id);
}

function handleComponentDragEnd(e) {
  e.target.classList.remove('dragging-preview');
  
  // Remover indicadores visuais de todos os componentes
  document.querySelectorAll('.preview-component').forEach(comp => {
    comp.classList.remove('drag-over-preview');
  });
  
  draggedPreviewComponent = null;
  console.log('✅ Drag de reordenação finalizado');
}

function handleComponentDragOver(e) {
  e.preventDefault();
  e.dataTransfer.dropEffect = 'move';
  
  // Só aplicar se estiver arrastando um componente do preview
  if (draggedPreviewComponent && e.target.classList.contains('preview-component')) {
    e.target.classList.add('drag-over-preview');
  }
}

function handleComponentDrop(e) {
  e.preventDefault();
  e.stopPropagation();
  
  const dropTarget = e.target.closest('.preview-component');
  
  if (dropTarget && draggedPreviewComponent && dropTarget !== draggedPreviewComponent) {
    // Determinar se deve inserir antes ou depois
    const rect = dropTarget.getBoundingClientRect();
    const midpoint = rect.top + rect.height / 2;
    
    if (e.clientY < midpoint) {
      // Inserir antes
      dropTarget.parentNode.insertBefore(draggedPreviewComponent, dropTarget);
      console.log('⬆️ Componente movido para cima de:', dropTarget.id);
    } else {
      // Inserir depois
      dropTarget.parentNode.insertBefore(draggedPreviewComponent, dropTarget.nextSibling);
      console.log('⬇️ Componente movido para baixo de:', dropTarget.id);
    }
  }
  
  // Limpar classes de drag
  dropTarget?.classList.remove('drag-over-preview');
}

function applyCurrentStyles(element) {
  // Aplicar estilos atuais do tema
  const primaryColor = document.getElementById('primary-color')?.value || '#007bff';
  const secondaryColor = document.getElementById('secondary-color')?.value || '#ffffff';
  const accentColor = document.getElementById('accent-color')?.value || '#ff6b35';
  const textColor = document.getElementById('text-color')?.value || '#333333';
  const backgroundColor = document.getElementById('background-color')?.value || '#f5f5f5';
  
  element.style.setProperty('--primary-color', primaryColor);
  element.style.setProperty('--secondary-color', secondaryColor);
  element.style.setProperty('--accent-color', accentColor);
  element.style.setProperty('--text-color', textColor);
  element.style.setProperty('--background-color', backgroundColor);
}

function loadExistingLayout() {
  // Carregar layout existente se houver
  const existingLayout = @json($checkout->component_order ?? []);
  if (existingLayout.length > 0) {
    const dropZone = document.getElementById('main-drop-zone');
    dropZone.classList.remove('empty');
    dropZone.innerHTML = '';
    
    existingLayout.forEach(componentType => {
      createPreviewComponent(componentType, dropZone);
    });
  }
}

function previewCheckout() {
  // Abrir preview em nova aba
  const previewUrl = `/checkout/preview/{{ $checkout->id }}`;
  window.open(previewUrl, '_blank');
}

function saveCheckout() {
  // Coletar dados do layout
  const components = [];
  const dropZone = document.getElementById('main-drop-zone');
  const previewComponents = dropZone.querySelectorAll('.preview-component');
  
  previewComponents.forEach(component => {
    const componentClass = component.querySelector('[class*="component-"]').className.split(' ')[0];
    const componentType = componentClass.replace('component-', '');
    components.push(componentType);
  });
  
  // Coletar configurações de tema
  const themeConfig = {
    primary_color: document.getElementById('primary-color').value,
    secondary_color: document.getElementById('secondary-color').value,
    accent_color: document.getElementById('accent-color').value,
    text_color: document.getElementById('text-color').value,
    background_color: document.getElementById('background-color').value,
    font_family: document.getElementById('font-family').value,
    font_size_base: document.getElementById('font-size-base').value,
    border_radius: document.getElementById('border-radius').value,
    padding_small: document.getElementById('padding-small').value,
    padding_medium: document.getElementById('padding-medium').value,
    padding_large: document.getElementById('padding-large').value,
    layout_type: document.getElementById('layout-type').value,
    sidebar_enabled: document.getElementById('sidebar-enabled').checked,
    sidebar_position: document.getElementById('sidebar-position').value,
    sidebar_width: document.getElementById('sidebar-width').value,
    animations_enabled: document.getElementById('animations-enabled').checked,
    animation_duration: document.getElementById('animation-duration').value,
    animation_easing: document.getElementById('animation-easing').value
  };
  
  // Enviar dados para o servidor
  fetch('/checkout/save-customization', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
      checkout_id: {{ $checkout->id }},
      component_order: components,
      theme_config: themeConfig
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showToast('success', 'Checkout salvo com sucesso!');
    } else {
      showToast('error', 'Erro ao salvar checkout: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Erro:', error);
    showToast('error', 'Erro ao salvar checkout');
  });
}

function toggleMobilePreview() {
  const preview = document.getElementById('checkout-preview');
  preview.style.maxWidth = '375px';
  preview.style.margin = '0 auto';
}

function toggleDesktopPreview() {
  const preview = document.getElementById('checkout-preview');
  preview.style.maxWidth = '100%';
  preview.style.margin = '0';
}

// Função para mostrar toast (assumindo que existe)
function showToast(type, message) {
  // Implementar sistema de toast
  console.log(`${type.toUpperCase()}: ${message}`);
}
</script>