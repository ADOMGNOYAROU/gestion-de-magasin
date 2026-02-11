@php
    $type = $type ?? 'info';
    $dismissible = $dismissible ?? true;
    $icon = $icon ?? true;
@endphp

@php
    $iconMap = [
        'success' => 'fas fa-check-circle',
        'danger' => 'fas fa-exclamation-triangle',
        'warning' => 'fas fa-exclamation-circle',
        'info' => 'fas fa-info-circle'
    ];
    
    $iconClass = $iconMap[$type] ?? $iconMap['info'];
@endphp

<div class="alert alert-{{ $type }} alert-dismissible fade show {{ $dismissible ? '' : 'pe-3' }}" role="alert">
    @if($icon)
        <i class="{{ $iconClass }} me-2"></i>
    @endif
    
    {{ $slot }}
    
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    @endif
</div>
