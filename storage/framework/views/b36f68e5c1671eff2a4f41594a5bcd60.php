

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Créer un Nouveau Produit</h1>
                <a href="<?php echo e(route('produits.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('produits.store')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="nom" name="nom" value="<?php echo e(old('nom')); ?>" required>
                                    <?php $__errorArgs = ['nom'];
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
                                    <label for="categorie" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['categorie'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="categorie" name="categorie" required>
                                        <option value="">Sélectionner une catégorie</option>
                                        <option value="Électronique" <?php echo e(old('categorie') == 'Électronique' ? 'selected' : ''); ?>>Électronique</option>
                                        <option value="Vêtements" <?php echo e(old('categorie') == 'Vêtements' ? 'selected' : ''); ?>>Vêtements</option>
                                        <option value="Alimentation" <?php echo e(old('categorie') == 'Alimentation' ? 'selected' : ''); ?>>Alimentation</option>
                                        <option value="Maison" <?php echo e(old('categorie') == 'Maison' ? 'selected' : ''); ?>>Maison</option>
                                        <option value="Beauté" <?php echo e(old('categorie') == 'Beauté' ? 'selected' : ''); ?>>Beauté</option>
                                        <option value="Sport" <?php echo e(old('categorie') == 'Sport' ? 'selected' : ''); ?>>Sport</option>
                                        <option value="Livres" <?php echo e(old('categorie') == 'Livres' ? 'selected' : ''); ?>>Livres</option>
                                        <option value="Autre" <?php echo e(old('categorie') == 'Autre' ? 'selected' : ''); ?>>Autre</option>
                                    </select>
                                    <?php $__errorArgs = ['categorie'];
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

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="description" name="description" rows="3"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
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

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prix_achat" class="form-label">Prix d'achat (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['prix_achat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="prix_achat" name="prix_achat" value="<?php echo e(old('prix_achat')); ?>" 
                                           step="0.01" min="0" required>
                                    <?php $__errorArgs = ['prix_achat'];
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
                                    <label for="prix_vente" class="form-label">Prix de vente (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['prix_vente'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="prix_vente" name="prix_vente" value="<?php echo e(old('prix_vente')); ?>" 
                                           step="0.01" min="0" required>
                                    <?php $__errorArgs = ['prix_vente'];
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
                                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                    <select class="form-select <?php $__errorArgs = ['statut'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="statut" name="statut" required>
                                        <option value="actif" <?php echo e(old('statut') == 'actif' ? 'selected' : ''); ?>>Actif</option>
                                        <option value="inactif" <?php echo e(old('statut') == 'inactif' ? 'selected' : ''); ?>>Inactif</option>
                                    </select>
                                    <?php $__errorArgs = ['statut'];
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

                        <!-- Calcul de la marge en temps réel -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Marge estimée :</strong> 
                                    <span id="marge-estimee">0 FCFA (0%)</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="<?php echo e(route('produits.index')); ?>" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
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
    const prixAchat = document.getElementById('prix_achat');
    const prixVente = document.getElementById('prix_vente');
    const margeEstimee = document.getElementById('marge-estimee');

    function calculerMarge() {
        const achat = parseFloat(prixAchat.value) || 0;
        const vente = parseFloat(prixVente.value) || 0;
        
        if (achat > 0 && vente > 0) {
            const marge = vente - achat;
            const pourcentage = ((marge / achat) * 100).toFixed(1);
            margeEstimee.textContent = `${marge.toFixed(2)} FCFA (${pourcentage}%)`;
        } else {
            margeEstimee.textContent = '0 FCFA (0%)';
        }
    }

    prixAchat.addEventListener('input', calculerMarge);
    prixVente.addEventListener('input', calculerMarge);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/produits/create.blade.php ENDPATH**/ ?>