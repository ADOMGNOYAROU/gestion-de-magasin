@php
    $title = $title ?? 'Confirmation';
    $message = $message ?? 'Êtes-vous sûr de vouloir continuer ?';
    $confirmText = $confirmText ?? 'Confirmer';
    $cancelText = $cancelText ?? 'Annuler';
    $confirmClass = $confirmClass ?? 'btn-primary';
    $size = $size ?? 'md';
@endphp

<!-- Modal de Confirmation -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-{{ $size }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">
                    <i class="fas fa-question-circle text-warning me-2"></i>
                    {{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">{{ $message }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> {{ $cancelText }}
                </button>
                <button type="button" class="btn {{ $confirmClass }}" id="confirmButton">
                    <i class="fas fa-check me-1"></i> {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    window.showConfirmModal = function(options) {
        options = options || {};
        
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const confirmButton = document.getElementById('confirmButton');
        
        // Mettre à jour le contenu si nécessaire
        if (options.title) {
            document.querySelector('#confirmModalLabel').innerHTML = 
                '<i class="fas fa-question-circle text-warning me-2"></i>' + options.title;
        }
        
        if (options.message) {
            document.querySelector('#confirmModal .modal-body p').textContent = options.message;
        }
        
        if (options.confirmText) {
            confirmButton.innerHTML = '<i class="fas fa-check me-1"></i>' + options.confirmText;
        }
        
        // Configurer l'action de confirmation
        confirmButton.onclick = function() {
            if (options.onConfirm) {
                options.onConfirm();
            }
            modal.hide();
        };
        
        modal.show();
    };
});
</script>
