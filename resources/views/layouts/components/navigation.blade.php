<style>
  /* Estilos principais para navegação */
  .nav-link {
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
  }
  
  .nav-link:hover {
    color: #ffc107;
  }
  
  .nav-link.active {
    color: #ffc107;
    background-color: rgba(255, 193, 7, 0.1);
    border-radius: 4px;
  }
  
  .breadcrumb {
    background: transparent !important;
  }
  
  .breadcrumb-item + .breadcrumb-item::before {
    color: #fff;
  }
  
  .breadcrumb-item a {
    color: #fff !important;
    text-decoration: none;
  }
  
  .breadcrumb-item.active {
    color: #ffc107 !important;
  }
  
  /* Estilos responsivos */
  .main-content {
    margin-left: 280px; /* Ajuste baseado na largura do sidebar */
    padding: 20px;
    min-height: 100vh;
  }
  
  @media (max-width: 768px) {
    .main-content {
      margin-left: 0;
      padding: 10px;
    }
  }
  
  /* Estilos para botões */
  .btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
  }
  
  .btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
    color: #000;
  }
  
  /* Estilos para cards */
  .card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .card.bg-dark {
    background-color: #1e1e1e !important;
  }
  
  .card.border-secondary {
    border-color: #495057 !important;
  }
  
  /* Estilos para tabelas */
  .table-dark {
    color: #fff;
    background-color: #212529;
  }
  
  .table-dark th,
  .table-dark td {
    border-color: #495057;
  }
  
  /* Estilos para formulários */
  .form-control.bg-dark {
    background-color: #2d2d2d !important;
    border-color: #495057;
    color: #fff;
  }
  
  .form-control:focus {
    background-color: #2d2d2d;
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    color: #fff;
  }
  
  /* Estilos para progress bar */
  .progress {
    background-color: rgba(255, 255, 255, 0.1);
    height: 8px;
    border-radius: 4px;
  }
  
  .progress-bar {
    background-color: #ffc107;
    transition: width 0.3s ease;
  }
  
  /* Utilitários */
  .text-warning {
    color: #ffc107 !important;
  }
  
  .text-success {
    color: #28a745 !important;
  }
  
  .text-danger {
    color: #dc3545 !important;
  }
  
  .text-info {
    color: #17a2b8 !important;
  }
  
  .text-muted {
    color: #6c757d !important;
  }
  
  /* Animações */
  .nav-item {
    transition: all 0.3s ease;
  }
  
  .nav-item:hover {
    transform: translateX(2px);
  }
  
  /* Estilos para ícones */
  .fa-solid, .fas, .material-icons {
    color: inherit;
  }
  
  /* Estilos para dropdowns */
  .dropdown-menu {
    background-color: #2d2d2d;
    border-color: #495057;
  }
  
  .dropdown-item {
    color: #fff;
  }
  
  .dropdown-item:hover {
    background-color: #495057;
    color: #ffc107;
  }
  
  .dropdown-item.active {
    background-color: #ffc107;
    color: #000;
  }
</style>

<script>
  // Função para inicializar navegação
  document.addEventListener('DOMContentLoaded', function() {
    // Ativa links baseados no caminho atual
    const currentPath = window.location.pathname;
    
    document.querySelectorAll('.nav-link').forEach(link => {
      const href = link.getAttribute('href');
      if (href && href !== '#' && href !== '') {
        if (currentPath === href || currentPath.includes(href)) {
          link.classList.add('active');
        }
      }
    });
    
    // Adiciona efeitos de hover para navegação
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('mouseenter', function() {
        this.style.transform = 'translateX(2px)';
      });
      
      link.addEventListener('mouseleave', function() {
        this.style.transform = 'translateX(0)';
      });
    });
    
    // Função para fechar menus móveis quando um link é clicado
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
      link.addEventListener('click', function() {
        const navbarCollapse = document.querySelector('.navbar-collapse');
        if (navbarCollapse && navbarCollapse.classList.contains('show')) {
          const bsCollapse = new bootstrap.Collapse(navbarCollapse);
          bsCollapse.hide();
        }
      });
    });
  });
</script>
