@props([
  'label',
  'info',
  'icon',
  'color' => 'success'
  ])

<div class="col-xxl-3 col-xl-4 col-md-6">
  <div class="card card-hover shadow-sm">
    <div class="card-body p-3">
      <div class="d-flex align-items-center gap-3">
        <div class="icon-circle-modern bg-gradient-{{ $color }} flex-shrink-0">
          <i class="fa-solid {{ $icon }}"></i>
        </div>
        <div class="flex-grow-1 min-w-0">
          <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $label }}</p>
          <h4 class="fw-bold mb-0 text-{{ $color }} text-truncate" style="font-size: 1.35rem;">{{ $info }}</h4>
        </div>
      </div>
    </div>
  </div>
</div>
