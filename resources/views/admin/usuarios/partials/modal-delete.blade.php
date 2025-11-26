@props([
  'user'
])



<div class="modal fade glassmorphism-delete-modal" id="deleteModal-{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel-{{ $user->id }}">
          <i class="fas fa-trash-alt me-2"></i>Confirmar Exclusão
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Você tem certeza que deseja excluir o usuário <span class="text-highlight">{{ $user->username }}</span>?</p>
      </div>
      <div class="modal-footer gap-2">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i>Cancelar
        </button>
        <form method="POST" action="{{ route('admin.usuarios.delete', ['id'=> $user->id]) }}" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-sm btn-danger">
            <i class="fas fa-trash-alt me-1"></i>Excluir
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
