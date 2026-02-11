@php
    $label = $label ?? null;
    $name = $name ?? null;
    $id = $id ?? $name ?? 'checkbox-' . uniqid();
    $value = $value ?? '1';
    $checked = $checked ?? false;
    $required = $required ?? false;
    $disabled = $disabled ?? false;
    $help = $help ?? null;
    $error = $error ?? null;
    $class = $class ?? '';
    $groupClass = $groupClass ?? '';
    $inline = $inline ?? false;
    $switch = $switch ?? false;
@endphp

<div class="form-group mb-3 {{ $groupClass }}">
    <div class="form-check {{ $inline ? 'form-check-inline' : '' }} {{ $switch ? 'form-switch' : '' }}">
        <input class="form-check-input {{ $error ? 'is-invalid' : '' }} {{ $class }}" 
               type="checkbox" 
               id="{{ $id }}" 
               name="{{ $name }}" 
               value="{{ $value }}"
               @if($checked) checked @endif
               @if($required) required @endif
               @if($disabled) disabled @endif
               {{ $attributes }}>
        
        @if($label)
            <label class="form-check-label" for="{{ $id }}">
                {{ $label }}
                @if($required)
                    <span class="text-danger">*</span>
                @endif
            </label>
        @endif
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
