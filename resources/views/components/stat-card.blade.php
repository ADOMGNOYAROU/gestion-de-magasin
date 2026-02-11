@php
    $title = $title ?? 'Statistiques';
    $value = $value ?? '0';
    $icon = $icon ?? 'fas fa-chart-bar';
    $color = $color ?? 'primary';
    $trend = $trend ?? null;
    $trendValue = $trendValue ?? null;
    $footer = $footer ?? null;
@endphp

@php
    $colorClasses = [
        'primary' => 'text-white bg-primary',
        'secondary' => 'text-white bg-secondary',
        'success' => 'text-white bg-success',
        'danger' => 'text-white bg-danger',
        'warning' => 'text-white bg-warning',
        'info' => 'text-white bg-info',
        'light' => 'text-dark bg-light',
        'dark' => 'text-white bg-dark'
    ];
    
    $cardClass = $colorClasses[$color] ?? $colorClasses['primary'];
    
    $trendIcons = [
        'up' => 'fas fa-arrow-up',
        'down' => 'fas fa-arrow-down',
        'neutral' => 'fas fa-minus'
    ];
    
    $trendColors = [
        'up' => 'text-success',
        'down' => 'text-danger',
        'neutral' => 'text-muted'
    ];
@endphp

<div class="card {{ $attributes->merge(['class' => 'border-left-primary shadow h-100 py-2']) }}">
    <div class="card-body">
        <div class="row no-gutters align-items-center">
            <div class="col mr-2">
                <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase mb-1">
                    {{ $title }}
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ $value }}
                </div>
                
                @if($trend && $trendValue)
                    <div class="mt-2">
                        <span class="{{ $trendColors[$trend] ?? 'text-muted' }} small">
                            <i class="{{ $trendIcons[$trend] ?? 'fas fa-minus' }}"></i>
                            {{ $trendValue }}
                        </span>
                    </div>
                @endif
            </div>
            
            <div class="col-auto">
                <div class="{{ $cardClass }} rounded-circle p-3">
                    <i class="{{ $icon }} fa-2x"></i>
                </div>
            </div>
        </div>
        
        @if($footer)
            <div class="mt-3">
                <small class="text-muted">{{ $footer }}</small>
            </div>
        @endif
    </div>
</div>
