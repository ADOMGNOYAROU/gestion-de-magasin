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
                                    <label for="produit_id" class="form-label">Produit <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['produit_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="produit_id" name="produit_id" required>
                                        <option value="">Sélectionner un produit</option>
                                        <?php $__currentLoopData = $produits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($produit->id); ?>" 
                                                    <?php echo e(old('produit_id') == $produit->id ? 'selected' : ''); ?>

                                                    data-categorie="<?php echo e($produit->categorie); ?>">
                                                <?php echo e($produit->nom); ?> - <?php echo e($produit->categorie); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['produit_id'];
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
                        </div>

                        <div class="row">
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantite" class="form-label">Quantité à Transférer <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['quantite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="quantite" name="quantite" value="<?php echo e(old('quantite')); ?>" 
                                           min="1" required>
                                    <?php $__errorArgs = ['quantite'];
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
                                    <label class="form-label">Stock Disponible</label>
                                    <div class="form-control bg-light" id="stock-info">
                                        <span id="stock-disponible">-</span>
                                        <small class="text-muted" id="stock-alerte"></small>
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
                                <i class="fas fa-exchange-alt"></i> Effectuer le transfert
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
    const produitSelect = document.getElementById('produit_id');
    const magasinSelect = document.getElementById('magasin_id');
    const boutiqueSelect = document.getElementById('boutique_id');
    const quantiteInput = document.getElementById('quantite');
    const stockDisponible = document.getElementById('stock-disponible');
    const stockAlerte = document.getElementById('stock-alerte');
    const infoProduit = document.getElementById('info-produit');
    const validationMessage = document.getElementById('validation-message');
    const validationText = document.getElementById('validation-text');
    const submitBtn = document.getElementById('submit-btn');

    let currentStock = 0;

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
        
        updateStockInfo();
    }

    function updateStockInfo() {
        const produitId = produitSelect.value;
        const magasinId = magasinSelect.value;
        
        if (produitId && magasinId) {
            fetch(`/api/stock-disponible?produit_id=${produitId}&magasin_id=${magasinId}`)
                .then(response => response.json())
                .then(stock => {
                    currentStock = stock.quantite;
                    stockDisponible.textContent = `${stock.quantite} unités`;
                    
                    if (stock.en_alerte) {
                        stockAlerte.textContent = '⚠️ Stock en alerte';
                        stockAlerte.className = 'text-danger';
                    } else {
                        stockAlerte.textContent = '';
                    }
                    
                    updateValidation();
                    updateInfoProduit();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    stockDisponible.textContent = 'Erreur';
                });
        } else {
            stockDisponible.textContent = '-';
            stockAlerte.textContent = '';
            currentStock = 0;
            updateValidation();
        }
    }

    function updateInfoProduit() {
        const selectedOption = produitSelect.options[produitSelect.selectedIndex];
        if (selectedOption.value && magasinSelect.value) {
            const categorie = selectedOption.dataset.categorie;
            const magasinOption = magasinSelect.options[magasinSelect.selectedIndex];
            
            infoProduit.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <strong>Catégorie :</strong> ${categorie}
                    </div>
                    <div class="col-md-4">
                        <strong>Stock disponible :</strong> ${currentStock} unités
                    </div>
                    <div class="col-md-4">
                        <strong>Magasin :</strong> ${magasinOption.text}
                    </div>
                </div>
            `;
        } else {
            infoProduit.innerHTML = '<p class="mb-0">Sélectionnez un produit et un magasin pour voir les informations</p>';
        }
    }

    function updateValidation() {
        const quantite = parseInt(quantiteInput.value) || 0;
        
        if (quantite === 0) {
            validationMessage.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }
        
        validationMessage.style.display = 'block';
        
        if (quantite > currentStock) {
            validationMessage.className = 'alert alert-danger';
            validationText.textContent = `❌ Quantité (${quantite}) supérieure au stock disponible (${currentStock})`;
            submitBtn.disabled = true;
        } else if (quantite === currentStock) {
            validationMessage.className = 'alert alert-warning';
            validationText.textContent = `⚠️ Vous allez transférer tout le stock disponible (${quantite} unités)`;
            submitBtn.disabled = false;
        } else {
            validationMessage.className = 'alert alert-success';
            validationText.textContent = `✅ Transfert possible : ${quantite} unités sur ${currentStock} disponibles`;
            submitBtn.disabled = false;
        }
    }

    // Écouteurs d'événements
    produitSelect.addEventListener('change', updateStockInfo);
    magasinSelect.addEventListener('change', function() {
        updateBoutiques();
        updateStockInfo();
    });
    quantiteInput.addEventListener('input', updateValidation);

    // Initialisation
    updateValidation();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/transferts/create.blade.php ENDPATH**/ ?>