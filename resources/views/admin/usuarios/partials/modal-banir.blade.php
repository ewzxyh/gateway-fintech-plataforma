@props([
  'user'
])


<div class="modal fade glassmorphism-ban-modal" id="banModal-{{ $user->id }}" tabindex="-1" aria-labelledby="banModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="banModalLabel-{{ $user->id }}">
          @if($user->banido == 0)
            <i class="fas fa-ban me-2"></i>Banir usuário
          @else
            <i class="fas fa-unlock me-2"></i>Desbanir usuário
          @endif
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Você tem certeza que deseja {{ $user->banido == 0 ? 'banir' : 'desbanir' }} o usuário <span class="text-highlight">{{ $user->username }}</span>?</p>
      </div>
      <div class="modal-footer gap-2">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i>Cancelar
        </button>
        <form method="POST" action="{{ route('admin.usuarios.mudarstatus') }}" style="display: inline;">
          @csrf
          <input id="id" name="id" value="{{$user->id}}" hidden />
          <input id="tipo" name="tipo" value="banido" hidden />
          @if($user->banido == 0)
            <button type="submit" class="btn btn-sm btn-danger">
              <i class="fas fa-ban me-1"></i>Banir
            </button>
          @else
            <button type="submit" class="btn btn-sm btn-success">
              <i class="fas fa-unlock me-1"></i>Desbanir
            </button>
          @endif
        </form>
      </div>
    </div>
  </div>
</div>
