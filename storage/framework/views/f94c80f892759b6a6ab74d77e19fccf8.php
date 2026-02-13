

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Nouveau Transfert de Stock</h1>
                <a href="<?php echo e(route('transferts.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('transferts.store')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="magasin_id" class="form-label">Magasin Source <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['magasin_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="magasin_id" name="magasin_id" required>
                                        <option value="">Sélectionner un magasin</option>
                                        <?php $__currentLoopData = $magasins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $magasin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($magasin->id); ?>" 
                                                    <?php echo e(old('magasin_id') == $magasin->id ? 'selected' : ''); ?>>
                                                <?php echo e($magasin->nom); ?> - <?php echo e($magasin->localisation); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['magasin_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="boutique_id" class="form-label">Boutique Destination <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['boutique_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="boutique_id" name="boutique_id" required disabled>
                                        <option value="">Sélectionnez d'abord un magasin</option>
                                    </select>
                                    <?php $__errorArgs = ['boutique_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="date" name="date" value="<?php echo e(old('date', now()->format('Y-m-d'))); ?>" required>
                                    <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Sélection des produits -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-list"></i> Produits à transférer</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="produits-table">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" id="select-all"></th>
                                                        <th>Produit</th>
                                                        <th>Catégorie</th>
                                                        <th>Stock Disponible</th>
                                                        <th>Quantité à Transférer</th>
                                                        <th>Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="produits-tbody">
                                                    <!-- Les produits seront chargés dynamiquement -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du produit et validation -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" id="info-produit">
                                    <h6><i class="fas fa-info-circle"></i> Informations</h6>
                                    <p class="mb-0">Sélectionnez un produit et un magasin pour voir les informations de stock</p>
                                </div>
                            </div>
                        </div>

                        <!-- Validation en temps réel -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert" id="validation-message" style="display: none;">
                                    <h6><i class="fas fa-exclamation-triangle"></i> Validation</h6>
                                    <p class="mb-0" id="validation-text"></p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="<?php echo e(route('transferts.index')); ?>" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                <i class="fas fa-exchange-alt"></i> Effectuer les transferts
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const magasinSelect = document.getElementById('magasin_id');
    const boutiqueSelect = document.getElementById('boutique_id');
    const dateInput = document.getElementById('date');
    const produitsTbody = document.getElementById('produits-tbody');
    const selectAllCheckbox = document.getElementById('select-all');
    const validationMessage = document.getElementById('validation-message');
    const validationText = document.getElementById('validation-text');
    const submitBtn = document.getElementById('submit-btn');

    let produitsData = [];

    function updateBoutiques() {
        const magasinId = magasinSelect.value;
        
        if (magasinId) {
            fetch(`/api/boutiques-par-magasin?magasin_id=${magasinId}`)
                .then(response => response.json())
                .then(boutiques => {
                    boutiqueSelect.innerHTML = '<option value="">Sélectionner une boutique</option>';
                    boutiques.forEach(boutique => {
                        boutiqueSelect.innerHTML += `<option value="${boutique.id}">${boutique.nom}</option>`;
                    });
                    boutiqueSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
        } else {
            boutiqueSelect.innerHTML = '<option value="">Sélectionnez d\'abord un magasin</option>';
            boutiqueSelect.disabled = true;
        }
        
        loadProduitsTable();
    }

    function loadProduitsTable() {
        const magasinId = magasinSelect.value;
        
        if (magasinId) {
            fetch(`/api/produits-avec-stock?magasin_id=${magasinId}`)
                .then(response => response.json())
                .then(data => {
                    produitsData = data;
                    renderProduitsTable();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    produitsTbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erreur de chargement</td></tr>';
                });
        } else {
            produitsTbody.innerHTML = '<tr><td colspan="6" class="text-center">Sélectionnez un magasin</td></tr>';
            produitsData = [];
        }
        updateValidation();
    }

    function renderProduitsTable() {
        if (produitsData.length === 0) {
            produitsTbody.innerHTML = '<tr><td colspan="6" class="text-center">Aucun produit disponible</td></tr>';
            return;
        }

        produitsTbody.innerHTML = '';
        produitsData.forEach(produit => {
            const row = document.createElement('tr');
            const stock = produit.stock || 0;
            const isEnAlerte = stock <= (produit.seuil_alerte || 0);
            
            row.innerHTML = `
                <td><input type="checkbox" class="produit-checkbox" value="${produit.id}"></td>
                <td>${produit.nom}</td>
                <td>${produit.categorie}</td>
                <td><span class="stock-disponible ${isEnAlerte ? 'text-danger' : ''}">${stock}</span></td>
                <td>
                    <input type="number" class="form-control quantite-input" 
                           name="quantite[${produit.id}]" min="0" max="${stock}" value="0" disabled>
                </td>
                <td>
                    <span class="badge ${stock > 0 ? 'bg-success' : 'bg-secondary'}">${stock > 0 ? 'Disponible' : 'Épuisé'}</span>
                </td>
            `;
            produitsTbody.appendChild(row);
        });

        // Attach events
        document.querySelectorAll('.produit-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const quantiteInput = this.closest('tr').querySelector('.quantite-input');
                quantiteInput.disabled = !this.checked;
                if (!this.checked) quantiteInput.value = 0;
                updateSelectAll();
                updateValidation();
            });
        });

        document.querySelectorAll('.quantite-input').forEach(input => {
            input.addEventListener('input', updateValidation);
        });
    }

    function updateSelectAll() {
        const checkboxes = document.querySelectorAll('.produit-checkbox');
        const checkedBoxes = document.querySelectorAll('.produit-checkbox:checked');
        selectAllCheckbox.checked = checkboxes.length > 0 && checkedBoxes.length === checkboxes.length;
        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < checkboxes.length;
    }

    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.produit-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            const quantiteInput = checkbox.closest('tr').querySelector('.quantite-input');
            quantiteInput.disabled = !this.checked;
            if (!this.checked) quantiteInput.value = 0;
        });
        updateValidation();
    });

    function updateValidation() {
        const selectedProducts = document.querySelectorAll('.produit-checkbox:checked');
        let hasValidSelection = false;
        let errors = [];

        selectedProducts.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const quantiteInput = row.querySelector('.quantite-input');
            const quantite = parseInt(quantiteInput.value) || 0;
            const stockDisponible = parseInt(row.querySelector('.stock-disponible').textContent) || 0;

            if (quantite > 0) {
                if (quantite > stockDisponible) {
                    errors.push(`Quantité (${quantite}) > stock disponible (${stockDisponible}) pour ${row.cells[1].textContent}`);
                } else {
                    hasValidSelection = true;
                }
            }
        });

        if (errors.length > 0) {
            validationMessage.className = 'alert alert-danger';
            validationText.textContent = errors.join('; ');
            submitBtn.disabled = true;
        } else if (hasValidSelection) {
            validationMessage.className = 'alert alert-success';
            validationText.textContent = `${selectedProducts.length} produit(s) sélectionné(s) pour le transfert`;
            submitBtn.disabled = false;
        } else {
            validationMessage.style.display = 'none';
            submitBtn.disabled = true;
        }
    }

    // Écouteurs d'événements
    magasinSelect.addEventListener('change', updateBoutiques);
    boutiqueSelect.addEventListener('change', updateValidation);
    dateInput.addEventListener('change', updateValidation);

    // Initialisation
    updateValidation();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/transferts/create.blade.php ENDPATH**/ ?>