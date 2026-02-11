@php
    $label = $label ?? null;
    $name = $name ?? null;
    $id = $id ?? $name ?? 'textarea-' . uniqid();
    $placeholder = $placeholder ?? null;
    $required = $required ?? false;
    $disabled = $disabled ?? false;
    $readonly = $readonly ?? false;
    $help = $help ?? null;
    $error = $error ?? null;
    $value = $value ?? null;
    $class = $class ?? '';
    $rows = $rows ?? 3;
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
    
    <textarea id="{{ $id }}" 
              name="{{ $name }}" 
              class="form-control {{ $error ? 'is-invalid' : '' }} {{ $class }}"
              placeholder="{{ $placeholder }}"
              rows="{{ $rows }}"
              @if($required) required @endif
              @if($disabled) disabled @endif
              @if($readonly) readonly @endif
              {{ $attributes }}>{{ $value }}</textarea>
    
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
