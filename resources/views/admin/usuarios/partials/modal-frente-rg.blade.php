@props(['user'])
<!-- Modal Foto Frente RG -->
<div class="modal fade" id="fotoFrenteModal{{ $user->id }}" tabindex="-1" aria-labelledby="fotoFrenteLabel{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="fotoFrenteLabel{{ $user->id }}">Foto de Frente RG</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="text-center modal-body">
        @if($user->foto_rg_frente)
          <img src="{{ asset($user->foto_rg_frente) }}" alt="Foto de Frente RG" class="img-fluid">
        @else
          <p class="text-muted">Imagem não disponível</p>
        @endif
      </div>
    </div>
  </div>
</div>
