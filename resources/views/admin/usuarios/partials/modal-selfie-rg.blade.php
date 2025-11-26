@props(['user'])
<!-- Modal Selfie RG -->
<div class="modal fade" id="selfieModal{{ $user->id }}" tabindex="-1" aria-labelledby="selfieLabel{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="selfieLabel{{ $user->id }}">Selfie com RG</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="text-center modal-body">
        @if($user->selfie_rg)
          <img src="{{ asset($user->selfie_rg) }}" alt="Selfie com RG" class="img-fluid">
        @else
          <p class="text-muted">Imagem não disponível</p>
        @endif
      </div>
    </div>
  </div>
</div>
