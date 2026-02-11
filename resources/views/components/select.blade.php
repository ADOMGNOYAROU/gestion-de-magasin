@php
    $label = $label ?? null;
    $name = $name ?? null;
    $id = $id ?? $name ?? 'select-' . uniqid();
    $required = $required ?? false;
    $disabled = $disabled ?? false;
    $multiple = $multiple ?? false;
    $help = $help ?? null;
    $error = $error ?? null;
    $value = $value ?? null;
    $class = $class ?? '';
    $icon = $icon ?? null;
    $groupClass = $groupClass ?? '';
    $placeholder = $placeholder ?? 'Sélectionner...';
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
        
        <select id="{{ $id }}" 
                name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
                class="form-select {{ $error ? 'is-invalid' : '' }} {{ $class }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($multiple) multiple @endif
                {{ $attributes }}>
            
            @if(!$multiple && $placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            
            {{ $slot }}
        </select>
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
