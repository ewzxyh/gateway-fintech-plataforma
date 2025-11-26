@props(['user'])
<!-- Modal Contrato Social -->
<div class="modal fade" id="contratoSocialModal{{ $user->id }}" tabindex="-1" aria-labelledby="contratoSocialLabel{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contratoSocialLabel{{ $user->id }}">Contrato Social</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @if(pathinfo($user->contrato_social, PATHINFO_EXTENSION) === 'pdf')
          <div class="text-center">
            <div class="mb-3">
              <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
              <h5>Contrato Social (PDF)</h5>
              <p class="text-muted">Clique no botão abaixo para visualizar o documento</p>
            </div>
            <a href="{{ asset($user->contrato_social) }}" target="_blank" class="btn btn-danger btn-lg">
              <i class="fas fa-external-link-alt me-2"></i>Abrir PDF
            </a>
            <div class="mt-3">
              <a href="{{ asset($user->contrato_social) }}" download class="btn btn-outline-primary">
                <i class="fas fa-download me-2"></i>Download
              </a>
            </div>
          </div>
        @else
          <div class="text-center">
            <img src="{{ asset($user->contrato_social) }}" alt="Contrato Social" class="img-fluid">
            <div class="mt-3">
              <a href="{{ asset($user->contrato_social) }}" download class="btn btn-outline-primary">
                <i class="fas fa-download me-2"></i>Download
              </a>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
