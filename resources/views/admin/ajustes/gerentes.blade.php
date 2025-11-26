<x-app-layout :route="'[ADMIN] Ajustes de gerentes'">
<style>
  /* Glassmorphism para cards de gerentes - Idêntico ao Dashboard de Usuários */
  .glassmorphism-user-card {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    box-shadow: 
      0 20px 60px rgba(0, 0, 0, 0.12),
      0 8px 25px rgba(0, 0, 0, 0.08),
      inset 0 1px 0 rgba(255, 255, 255, 0.25);
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  .glassmorphism-user-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, 
      rgba(255, 255, 255, 0.3) 0%, 
      transparent 25%, 
      transparent 75%,
      rgba(255, 255, 255, 0.15) 100%);
    opacity: 0.5;
    pointer-events: none;
  }
  
  .glassmorphism-user-card:hover {
    transform: translateY(-5px);
    box-shadow: 
      0 25px 80px rgba(0, 0, 0, 0.15),
      0 12px 35px rgba(0, 0, 0, 0.1),
      inset 0 1px 0 rgba(255, 255, 255, 0.3);
  }
  
  /* Tema escuro */
  [data-theme="dark"] .glassmorphism-user-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 
      0 20px 60px rgba(0, 0, 0, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.1);
  }

  .glassmorphism-user-card .action-icon {
    backdrop-filter: blur(8px);
    transition: all 0.2s ease;
  }

  .glassmorphism-user-card .action-icon:hover {
    transform: scale(1.1);
    backdrop-filter: blur(12px);
  }

  @media (prefers-color-scheme: dark) {
    .glassmorphism-user-card {
      background: rgba(0, 0, 0, 0.2) !important;
      border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    
    .glassmorphism-user-card .card-footer {
      background: rgba(0, 0, 0, 0.1) !important;
    }
  }

  /* Botões com glassmorphism */
  .glassmorphism-btn {
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 8px !important;
    color: #667eea !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
  }

  .glassmorphism-btn:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    border-color: rgba(102, 126, 234, 0.6) !important;
    color: #764ba2 !important;
    transform: translateY(-2px);
  }

  .glassmorphism-btn-success {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    color: #ffffff !important;
  }

  .glassmorphism-btn-success:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%) !important;
    color: #ffffff !important;
  }

  .glassmorphism-btn-danger {
    background: rgba(231, 76, 60, 0.1) !important;
    backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(231, 76, 60, 0.3) !important;
    border-radius: 8px !important;
    color: #e74c3c !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
  }

  .glassmorphism-btn-danger:hover {
    background: rgba(231, 76, 60, 0.15) !important;
    border-color: rgba(231, 76, 60, 0.6) !important;
    color: #c0392b !important;
    transform: translateY(-2px);
  }

  /* Page Title */
  .page-title-glassmorphism {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    text-shadow: none;
  }
</style>

  <div class="main-content app-content">
    <div class="container-fluid">

      <!-- Start::page-header -->
      <div class="mb-3 row">
        <div class="mb-3 md-mb-0 col col-12 col-lg-8 text-start">
          <h1 class="mb-0 display-5 page-title-glassmorphism">Gerentes de contas</h1>
        </div>
        <div class="col col-12 col-lg-4 text-end">
          <!-- Botão removido - agora está no card -->
        </div>
      </div>

      <!-- Cards dos Gerentes -->
      <div class="container px-0 mx-0 row">
        <div class="col-xl-12">
          <div class="row">
            @foreach ($gerentes as $gerente)
              <div class="mb-3 col-12 col-md-6 col-xl-4">
                <div class="relative card card-raised glassmorphism-user-card" style="position: relative; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">
                  <div class="p-3 card-body" style="background: transparent;">
                    <div class="justify-between px-3 mb-3 bg-transparent row align-center">
                      <div class="col-auto text-lg font-bold text-white d-flex align-items-center justify-content-center"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 50px; height: 50px; border-radius: 10px; font-weight: bold; color: white;">
                        {{ strtoupper(substr($gerente->name, 0, 2)) }}
                      </div>
                      <div class="col ps-3" style="line-height: 15px">
                        <h4 class="mb-0 font-bold">{{ $gerente->name }}</h4>
                        <p class="mb-0">{{ $gerente->email }}</p>
                        <p class="flex items-center gap-1 text-sm">
                          <span style="color: gray;">Gerente de Contas</span>
                        </p>
                      </div>
                    </div>

                    <p class="card-text">Status da conta</p>
                    <h2 class="font-bold card-title">
                      @if($gerente->banido == 0)
                        <span style="color: #2ecc71;">Ativo</span>
                      @else
                        <span style="color: #e74c3c;">Inativo</span>
                      @endif
                    </h2>
                  </div>
                  <!-- Ícones de ação no canto superior direito -->
                  <div class="action-icons" style="position: absolute; top: 15px; right: 15px; display: flex; gap: 8px; flex-wrap: wrap; max-width: 120px; justify-content: flex-end;">
                    <!-- Editar -->
                    <button class="action-icon btn btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editModal-{{ $gerente->id }}"
                        data-id="{{ $gerente->id }}"
                        data-name="{{ $gerente->name }}"
                        data-telefone="{{ $gerente->telefone }}"
                        title="Editar"
                        style="background: rgba(46, 204, 113, 0.1); border: 1px solid rgba(46, 204, 113, 0.3); border-radius: 6px; padding: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                      <i class="fa-solid fa-edit" style="color: #2ecc71; font-size: 12px;"></i>
                    </button>

                    <!-- Excluir -->
                    <button class="action-icon btn btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#deleteModal-{{ $gerente->id }}"
                        data-id="{{ $gerente->id }}"
                        title="Excluir"
                        style="background: rgba(231, 76, 60, 0.1); border: 1px solid rgba(231, 76, 60, 0.3); border-radius: 6px; padding: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                      <i class="fa-solid fa-trash" style="color: #e74c3c; font-size: 12px;"></i>
                    </button>
                  </div>

                  <div class="p-2 card-footer" style="background: rgba(255, 255, 255, 0.05); border-top: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0 0 16px 16px;">
                    <div class="row text-center">
                      <div class="col-6">
                        <small class="text-muted">Status</small><br>
                        @if($gerente->banido == 0)
                          <span class="badge bg-success">Ativo</span>
                        @else
                          <span class="badge bg-danger">Inativo</span>
                        @endif
                      </div>
                      <div class="col-6">
                        <small class="text-muted">Telefone</small><br>
                        <span>{{ $gerente->telefone ?? 'N/A' }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
             
             <!-- Card para adicionar novo gerente -->
             <div class="mb-3 col-12 col-md-6 col-xl-4">
               <div class="relative card card-raised glassmorphism-user-card d-flex align-items-center justify-content-center" style="position: relative; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 16px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; min-height: 300px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#adcionarGerente">
                 <div class="text-center">
                   <div class="mb-3">
                     <div class="d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 60px; height: 60px; border-radius: 50%; margin: 0 auto;">
                       <i class="fas fa-plus" style="font-size: 24px; color: white;"></i>
                     </div>
                   </div>
                   <h5 class="mb-2 font-bold">Adicionar Gerente</h5>
                   <p class="text-muted small mb-0">Clique para adicionar um novo gerente</p>
                 </div>
               </div>
             </div>
           </div>
         </div>
       </div>
     </div>
   </div>

   <!-- Modais -->
   @foreach ($gerentes as $gerente)
     <!-- Modal Editar -->
     <div class="modal fade" id="editModal-{{ $gerente->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $gerente->id }}" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
           <div class="modal-header">
             <h5 class="modal-title" id="editModalLabel-{{ $gerente->id }}">Editar Gerente</h5>
             <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
             <form method="POST" action="{{ route('admin.ajustes.gerente.update', ['id' => $gerente->id]) }}">
               @csrf
               @method('PUT')
               <input type="hidden" id="editUserId" name="id">
               <div class="mb-3">
                 <label for="editNome-{{ $gerente->id }}" class="form-label">Nome</label>
                 <input type="text" value="{{ $gerente->name }}" class="form-control" id="editNome-{{ $gerente->id }}" name="name">
               </div>
               <div class="mb-3">
                 <label for="editSenha-{{ $gerente->id }}" class="form-label">Nova senha</label>
                 <input type="text" class="form-control" id="editSenha-{{ $gerente->id }}" name="password">
               </div>
               <div class="mb-3">
                 <label for="editTel-{{ $gerente->id }}" class="form-label">Telefone</label>
                 <input type="telefone" value="{{ $gerente->telefone }}" class="form-control" id="editTel-{{ $gerente->id }}" name="telefone">
               </div>
               <div class="mb-3">
                 <label for="editEmail-{{ $gerente->id }}" class="form-label">Email</label>
                 <input type="email" value="{{ $gerente->email }}" class="form-control" id="editEmail-{{ $gerente->id }}" name="email">
               </div>
               <div class="mb-3">
                 <label for="editStatus-{{ $gerente->id }}" class="form-label">Status</label>
                 <select class="form-control" id="editStatus-{{ $gerente->id }}" name="banido">
                 <option value="0" {{ $gerente->banido == 0 ? 'selected' : '' }}>Ativo</option>
                 <option value="1" {{ $gerente->banido == 1 ? 'selected' : '' }}>Inativo</option>
                 </select>
               </div>

               <button type="submit" class="btn glassmorphism-btn-success">Salvar alterações</button>
             </form>
           </div>
         </div>
       </div>
     </div>

     <!-- Modal Confirmar Exclusão -->
     <div class="modal fade" id="deleteModal-{{ $gerente->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $gerente->id }}" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
           <div class="modal-header">
             <h5 class="modal-title" id="deleteModalLabel-{{ $gerente->id }}">Confirmar Exclusão</h5>
             <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
             <p>Você tem certeza que deseja excluir este gerente?</p>
           </div>
           <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
             <form method="POST" action="{{ url('/admin/usuarios/delete/' . $gerente->id) }}">
               @csrf
               @method('DELETE')
               <button type="submit" class="btn glassmorphism-btn-danger">Excluir</button>
             </form>
           </div>
         </div>
       </div>
     </div>
   @endforeach

   <!-- Modal Adicionar Gerente -->
   <div class="modal fade" id="adcionarGerente" tabindex="-1" aria-labelledby="adcionarGerenteLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
       <div class="modal-content">
         <form method="POST" action="{{ route('admin.ajustes.gerente.add') }}">
           @csrf
           <div class="modal-header">
             <h5 class="modal-title" id="adcionarGerenteLabel">Adicionar gerente</h5>
             <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
             <div class="mb-3">
               <label for="name" class="form-label">Nome</label>
               <input type="text" class="form-control" id="name" name="name">
             </div>
             <div class="mb-3">
               <label for="cpf_cnpj" class="form-label">CPF</label>
               <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj">
             </div>
             <div class="mb-3">
               <label for="email" class="form-label">Email</label>
               <input type="email" class="form-control" id="email" name="email">
             </div>
             <div class="mb-3">
               <label for="telefone" class="form-label">Telefone</label>
               <input type="text" class="form-control" id="telefone" name="telefone">
             </div>
             <div class="mb-3">
               <label for="password" class="form-label">Senha</label>
               <input type="text" class="form-control" id="password" name="password">
             </div>
           </div>
           <input type="text" class="form-control" id="permission" name="permission" value="9" hidden>
           <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
             <button type="submit" class="btn glassmorphism-btn-success">Adicionar</button>
           </div>
         </form>
       </div>
     </div>
   </div>
</x-app-layout>
