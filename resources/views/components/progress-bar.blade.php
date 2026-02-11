@php
    $title = $title ?? 'Progression';
    $value = $value ?? 0;
    $max = $max ?? 100;
    $showPercentage = $showPercentage ?? true;
    $animated = $animated ?? true;
    $color = $color ?? 'primary';
    $size = $size ?? 'md'; // sm, md, lg
    $striped = $striped ?? false;
    $label = $label ?? null;
@endphp

@php
    $colorClasses = [
        'primary' => 'bg-primary',
        'secondary' => 'bg-secondary',
        'success' => 'bg-success',
        'danger' => 'bg-danger',
        'warning' => 'bg-warning',
        'info' => 'bg-info',
        'light' => 'bg-light',
        'dark' => 'bg-dark'
    ];
    
    $sizeClasses = [
        'sm' => 'progress-sm',
        'md' => '',
        'lg' => 'progress-lg'
    ];
    
    $progressClass = $colorClasses[$color] ?? $colorClasses['primary'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    
    $percentage = $max > 0 ? min(($value / $max) * 100, 100) : 0;
@endphp

@if($label)
    <div class="d-flex justify-content-between mb-1">
        <span class="fw-semibold">{{ $label }}</span>
        @if($showPercentage)
            <span class="text-muted">{{ number_format($percentage, 1) }}%</span>
        @endif
    </div>
@endif

<div class="progress {{ $sizeClass }}" role="progressbar" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="{{ $max }}">
    <div class="progress-bar {{ $progressClass }} {{ $striped ? 'progress-bar-striped' : '' }} {{ $animated ? 'progress-bar-animated' : '' }}" 
         style="width: {{ $percentage }}%"
         @if($showPercentage && !$label) 
         title="{{ number_format($percentage, 1) }}%"
         @endif>
        @if($showPercentage && !$label && $percentage > 10)
            {{ number_format($percentage, 0) }}%
        @endif
    </div>
</div>

@if($value > $max)
    <small class="text-warning">
        <i class="fas fa-exclamation-triangle me-1"></i>
        La valeur ({{ $value }}) dépasse le maximum ({{ $max }})
    </small>
@endif
