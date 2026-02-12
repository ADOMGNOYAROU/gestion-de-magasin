<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Nouvelle Entrée de Stock</h1>
                <a href="<?php echo e(route('entrees-stock.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('entrees-stock.store')); ?>">
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

                                                    data-prix="<?php echo e($produit->prix_achat); ?>"
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
                                    <label for="magasin_id" class="form-label">Magasin <span class="text-danger">*</span></label>
                                    <?php if(isset($magasin)): ?>
                                        <!-- Gestionnaire : magasin prédéfini -->
                                        <input type="text" class="form-control" value="<?php echo e($magasin->nom); ?>" readonly>
                                        <input type="hidden" name="magasin_id" value="<?php echo e($magasin->id); ?>">
                                    <?php else: ?>
                                        <!-- Admin : choix du magasin -->
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
                                            <?php $__currentLoopData = $magasins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($m->id); ?>" 
                                                        <?php echo e(old('magasin_id') == $m->id ? 'selected' : ''); ?>>
                                                    <?php echo e($m->nom); ?> - <?php echo e($m->localisation); ?>

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
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Source <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="fournisseur_id" class="form-label">Fournisseur</label>
                                            <select class="form-select <?php $__errorArgs = ['fournisseur_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    id="fournisseur_id" name="fournisseur_id">
                                                <option value="">Sélectionner un fournisseur</option>
                                                <?php $__currentLoopData = $fournisseurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fournisseur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($fournisseur->id); ?>" 
                                                            <?php echo e(old('fournisseur_id') == $fournisseur->id ? 'selected' : ''); ?>>
                                                        <?php echo e($fournisseur->nom); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php $__errorArgs = ['fournisseur_id'];
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
                                        <div class="col-md-6">
                                            <label for="partenaire_id" class="form-label">Partenaire</label>
                                            <select class="form-select <?php $__errorArgs = ['partenaire_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    id="partenaire_id" name="partenaire_id">
                                                <option value="">Sélectionner un partenaire</option>
                                                <?php $__currentLoopData = $partenaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partenaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($partenaire->id); ?>" 
                                                            <?php echo e(old('partenaire_id') == $partenaire->id ? 'selected' : ''); ?>>
                                                        <?php echo e($partenaire->nom); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php $__errorArgs = ['partenaire_id'];
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
                                    <small class="text-muted">Sélectionnez un fournisseur OU un partenaire</small>
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantite" class="form-label">Quantité <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['quantite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="quantite" name="quantite" value="<?php echo e(old('quantite')); ?>" 
                                           min="1" max="2147483647" required>
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prix_unitaire" class="form-label">Prix Unitaire (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['prix_unitaire'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="prix_unitaire" name="prix_unitaire" value="<?php echo e(old('prix_unitaire')); ?>" 
                                           step="0.01" min="0" required>
                                    <?php $__errorArgs = ['prix_unitaire'];
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Total</label>
                                    <div class="form-control bg-light">
                                        <span id="total-calculé">0 FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du produit sélectionné -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Informations du produit</h6>
                                    <div id="info-produit">
                                        <p class="mb-0">Sélectionnez un produit pour voir ses informations</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="<?php echo e(route('entrees-stock.index')); ?>" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Enregistrer l'entrée
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
    const quantiteInput = document.getElementById('quantite');
    const prixUnitaireInput = document.getElementById('prix_unitaire');
    const totalCalcule = document.getElementById('total-calculé');
    const infoProduit = document.getElementById('info-produit');

    function calculerTotal() {
        const quantite = parseFloat(quantiteInput.value) || 0;
        const prixUnitaire = parseFloat(prixUnitaireInput.value) || 0;
        const total = quantite * prixUnitaire;
        totalCalcule.textContent = `${total.toFixed(2)} FCFA`;
    }

    function afficherInfoProduit() {
        const selectedOption = produitSelect.options[produitSelect.selectedIndex];
        if (selectedOption.value) {
            const prix = selectedOption.dataset.prix;
            const categorie = selectedOption.dataset.categorie;
            
            infoProduit.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <strong>Catégorie :</strong> ${categorie}
                    </div>
                    <div class="col-md-4">
                        <strong>Prix d'achat standard :</strong> ${parseFloat(prix).toFixed(2)} FCFA
                    </div>
                    <div class="col-md-4">
                        <strong>Écart prix :</strong> 
                        <span id="ecart-prix" class="badge">
                            ${prixUnitaireInput.value ? (prixUnitaireInput.value - prix).toFixed(2) + ' FCFA' : '0 FCFA'}
                        </span>
                    </div>
                </div>
            `;
            
            // Mettre à jour la classe du badge selon l'écart
            const ecartPrixSpan = document.getElementById('ecart-prix');
            if (prixUnitaireInput.value && prixUnitaireInput.value > prix) {
                ecartPrixSpan.className = 'badge bg-danger';
            } else if (prixUnitaireInput.value && prixUnitaireInput.value < prix) {
                ecartPrixSpan.className = 'badge bg-success';
            } else {
                ecartPrixSpan.className = 'badge bg-secondary';
            }
            
            // Suggérer le prix standard si non renseigné
            if (!prixUnitaireInput.value) {
                prixUnitaireInput.value = prix;
                calculerTotal();
            }
        } else {
            infoProduit.innerHTML = '<p class="mb-0">Sélectionnez un produit pour voir ses informations</p>';
        }
    }

    produitSelect.addEventListener('change', function() {
        afficherInfoProduit();
        calculerTotal();
    });

    quantiteInput.addEventListener('input', calculerTotal);
    prixUnitaireInput.addEventListener('input', function() {
        calculerTotal();
        afficherInfoProduit(); // Pour mettre à jour l'écart
    });

    // Initialisation
    afficherInfoProduit();
    calculerTotal();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/entrees-stock/create.blade.php ENDPATH**/ ?>