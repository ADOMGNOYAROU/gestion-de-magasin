@php
    $title = $title ?? 'Recherche';
    $placeholder = $placeholder ?? 'Rechercher...';
    $name = $name ?? 'search';
    $value = $value ?? request($name);
    $method = $method ?? 'GET';
    $action = $action ?? request()->url();
    $class = $class ?? '';
    $icon = $icon ?? 'fas fa-search';
@endphp

<form method="{{ $method }}" action="{{ $action }}" class="search-form {{ $class }}">
    @csrf
    <div class="input-group">
        <input type="text" 
               class="form-control" 
               name="{{ $name }}" 
               value="{{ $value }}"
               placeholder="{{ $placeholder }}"
               aria-label="{{ $title }}">
        
        @if($method === 'GET')
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endif
        
        <button class="btn btn-outline-secondary" type="submit">
            <i class="{{ $icon }}"></i>
            <span class="d-none d-md-inline ms-1">{{ $title }}</span>
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        const searchInput = searchForm.querySelector('input[type="text"]');
        
        // Recherche automatique après 500ms d'inactivité
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            searchTimeout = setTimeout(function() {
                // Soumettre uniquement si la méthode est GET
                if ('{{ $method }}' === 'GET') {
                    searchForm.submit();
                }
            }, 500);
        });
        
        // Annuler la recherche avec Escape
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                clearTimeout(searchTimeout);
            }
        });
    }
});
</script>
