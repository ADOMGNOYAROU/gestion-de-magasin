@php
    $title = $title ?? 'Card Title';
    $header = $header ?? null;
    $footer = $footer ?? null;
    $color = $color ?? 'default';
    $collapsible = $collapsible ?? false;
    $collapsed = $collapsed ?? false;
    $id = $id ?? 'card-' . uniqid();
@endphp

@php
    $colorClasses = [
        'primary' => 'text-white bg-primary',
        'secondary' => 'text-white bg-secondary',
        'success' => 'text-white bg-success',
        'danger' => 'text-white bg-danger',
        'warning' => 'text-white bg-warning',
        'info' => 'text-white bg-info',
        'light' => 'bg-light',
        'dark' => 'text-white bg-dark',
        'default' => ''
    ];
    
    $headerClass = $colorClasses[$color] ?? $colorClasses['default'];
@endphp

<div class="card {{ $attributes->merge(['class' => 'shadow']) }}">
    @if($header || $collapsible)
        <div class="card-header {{ $headerClass }}" @if($collapsible) id="{{ $id }}-header" @endif>
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    @if($collapsible)
                        <button class="btn btn-link text-decoration-none p-0 {{ $color !== 'default' && $color !== 'light' ? 'text-white' : '' }}" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#{{ $id }}-collapse" 
                                aria-expanded="{{ !$collapsed ? 'true' : 'false' }}"
                                aria-controls="{{ $id }}-collapse">
                            <i class="fas fa-chevron-{{ $collapsed ? 'right' : 'down' }} me-2"></i>
                        </button>
                    @endif
                    
                    @if($header)
                        {{ $header }}
                    @else
                        {{ $title }}
                    @endif
                </h5>
                
                @if(isset($actions))
                    <div class="card-actions">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <div class="card-body {{ $collapsible && $collapsed ? 'collapse' : '' }}" 
         @if($collapsible) id="{{ $id }}-collapse" 
         data-bs-parent="#{{ $id }}-header" @endif>
        @if(!$header && !$collapsible)
            <h5 class="card-title">{{ $title }}</h5>
        @endif
        
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="card-footer {{ $color === 'light' ? 'text-muted' : '' }}">
            {{ $footer }}
        </div>
    @endif
</div>

@if($collapsible)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const collapseElement = document.getElementById('{{ $id }}-collapse');
    const chevronIcon = document.querySelector('#{{ $id }}-header .fa-chevron-right, #{{ $id }}-header .fa-chevron-down');
    
    if (collapseElement && chevronIcon) {
        collapseElement.addEventListener('show.bs.collapse', function () {
            chevronIcon.classList.remove('fa-chevron-right');
            chevronIcon.classList.add('fa-chevron-down');
        });
        
        collapseElement.addEventListener('hide.bs.collapse', function () {
            chevronIcon.classList.remove('fa-chevron-down');
            chevronIcon.classList.add('fa-chevron-right');
        });
    }
});
</script>
@endif
