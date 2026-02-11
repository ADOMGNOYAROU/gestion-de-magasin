@php
    $label = $label ?? null;
    $name = $name ?? null;
    $id = $id ?? $name ?? 'input-' . uniqid();
    $type = $type ?? 'text';
    $placeholder = $placeholder ?? null;
    $required = $required ?? false;
    $disabled = $disabled ?? false;
    $readonly = $readonly ?? false;
    $help = $help ?? null;
    $error = $error ?? null;
    $value = $value ?? null;
    $class = $class ?? '';
    $icon = $icon ?? null;
    $groupClass = $groupClass ?? '';
@endphp

<div class="form-group mb-3 {{ $groupClass }}">
    @if($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <div class="input-group">
        @if($icon)
            <span class="input-group-text">
                <i class="{{ $icon }}"></i>
            </span>
        @endif
        
        <input type="{{ $type }}" 
               id="{{ $id }}" 
               name="{{ $name }}" 
               class="form-control {{ $error ? 'is-invalid' : '' }} {{ $class }}"
               value="{{ $value }}"
               placeholder="{{ $placeholder }}"
               @if($required) required @endif
               @if($disabled) disabled @endif
               @if($readonly) readonly @endif
               {{ $attributes }}>
    </div>
    
    @if($error)
        <div class="invalid-feedback d-block">
            <i class="fas fa-exclamation-circle me-1"></i>
            {{ $error }}
        </div>
    @endif
    
    @if($help)
        <small class="form-text text-muted">
            <i class="fas fa-info-circle me-1"></i>
            {{ $help }}
        </small>
    @endif
</div>
