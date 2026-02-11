@php
    $title = $title ?? 'Actions';
    $size = $size ?? 'sm';
    $vertical = $vertical ?? false;
    $class = $class ?? '';
@endphp

<div class="btn-group {{ $vertical ? 'btn-group-vertical' : '' }} {{ $class }}" role="group">
    {{ $slot }}
</div>

@if(isset($tooltip))
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips si nécessaire
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endif
