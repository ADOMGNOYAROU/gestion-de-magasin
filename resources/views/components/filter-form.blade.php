@php
    $title = $title ?? 'Filtres';
    $method = $method ?? 'GET';
    $action = $action ?? request()->url();
    $reset = $reset ?? true;
    $collapsible = $collapsible ?? false;
    $collapsed = $collapsed ?? false;
@endphp

<div class="card shadow mb-4">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>
                {{ $title }}
            </h6>
            
            @if($collapsible)
                <button class="btn btn-sm btn-outline-secondary" type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#filterCollapse" 
                        aria-expanded="{{ !$collapsed ? 'true' : 'false' }}">
                    <i class="fas fa-chevron-{{ $collapsed ? 'down' : 'up' }}"></i>
                </button>
            @endif
        </div>
    </div>
    
    <div class="card-body {{ $collapsible && $collapsed ? 'collapse' : '' }}" @if($collapsible) id="filterCollapse" @endif>
        <form method="{{ $method }}" action="{{ $action }}" class="filter-form">
            @csrf
            <div class="row">
                {{ $slot }}
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        @if($reset)
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>
                                Réinitialiser
                            </button>
                        @endif
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>
                            Rechercher
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Réinitialiser le formulaire
    const resetButton = document.querySelector('.filter-form button[type="reset"]');
    if (resetButton) {
        resetButton.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            
            // Réinitialiser tous les champs
            form.reset();
            
            // Déclencher la soumission
            form.submit();
        });
    }
});
</script>
