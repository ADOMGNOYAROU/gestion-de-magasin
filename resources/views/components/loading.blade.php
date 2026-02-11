@php
    $text = $text ?? 'Chargement...';
    $size = $size ?? 'md';
    $overlay = $overlay ?? false;
    $centered = $centered ?? true;
@endphp

@php
    $sizeClasses = [
        'sm' => 'spinner-border-sm',
        'md' => '',
        'lg' => 'spinner-border-lg'
    ];
    
    $spinnerClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

@if($overlay)
    <div class="loading-overlay d-flex justify-content-center align-items-center position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50" style="z-index: 9999;">
@endif

<div class="d-flex justify-content-center align-items-center {{ $centered ? '' : '' }}" @if($overlay) style="min-height: 200px;" @endif>
    <div class="text-center">
        <div class="spinner-border text-primary {{ $spinnerClass }}" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
        @if($text)
            <div class="mt-3 text-muted">
                <small>{{ $text }}</small>
            </div>
        @endif
    </div>
</div>

@if($overlay)
    </div>
@endif

@if($overlay)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cacher automatiquement après 2 secondes (optionnel)
    setTimeout(function() {
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }, 2000);
});
</script>
@endif
