@props(['user'])
<!-- Modal Foto Verso RG -->
<div class="modal fade" id="fotoVersoModal{{ $user->id }}" tabindex="-1" aria-labelledby="fotoVersoLabel{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="fotoVersoLabel{{ $user->id }}">Foto de Verso RG</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="text-center modal-body">
        @if($user->foto_rg_verso)
          <img src="{{ asset($user->foto_rg_verso) }}" alt="Foto de Verso RG" class="img-fluid">
        @else
          <p class="text-muted">Imagem não disponível</p>
        @endif
      </div>
    </div>
  </div>
</div>
