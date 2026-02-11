@php
    $label = $label ?? 'Choisir une option';
    $name = $name ?? null;
    $id = $id ?? $name ?? 'radio-' . uniqid();
    $options = $options ?? [];
    $value = $value ?? null;
    $required = $required ?? false;
    $disabled = $disabled ?? false;
    $inline = $inline ?? false;
    $help = $help ?? null;
    $error = $error ?? null;
    $class = $class ?? '';
    $groupClass = $groupClass ?? '';
@endphp

<div class="form-group mb-3 {{ $groupClass }}">
    @if($label)
        <label class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <div class="{{ $inline ? 'form-check-inline' : '' }}">
        @foreach($options as $optionValue => $optionLabel)
            <div class="form-check {{ $inline ? 'form-check-inline' : '' }}">
                <input class="form-check-input {{ $error ? 'is-invalid' : '' }} {{ $class }}" 
                       type="radio" 
                       name="{{ $name }}" 
                       id="{{ $id }}-{{ $optionValue }}" 
                       value="{{ $optionValue }}"
                       @if($value == $optionValue) checked @endif
                       @if($required) required @endif
                       @if($disabled) disabled @endif>
                       
                <label class="form-check-label" for="{{ $id }}-{{ $optionValue }}">
                    {{ $optionLabel }}
                </label>
            </div>
        @endforeach
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
