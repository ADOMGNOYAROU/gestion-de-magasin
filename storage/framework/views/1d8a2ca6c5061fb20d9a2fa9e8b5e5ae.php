<?php $__env->startSection('title', 'Fermer la caisse - ' . $session->vendeur->name); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Accueil</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('pos.index')); ?>">Gestion des Caisses</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('pos.close')); ?>">Fermer une caisse</a></li>
    <li class="breadcrumb-item active"><?php echo e($session->vendeur->name); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header'); ?>
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">Fermer la caisse</h1>
        <p class="text-muted mb-0">Session de <?php echo e($session->vendeur->name); ?> - <?php echo e($session->boutique->nom); ?></p>
    </div>
    <div>
        <a href="<?php echo e(route('pos.close')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Fermeture de caisse</h5>
            </div>
            <div class="card-body">
                <!-- Informations de la session -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-2">Informations de la session</h6>
                            <p class="mb-1"><strong>Vendeur:</strong> <?php echo e($session->vendeur->name); ?></p>
                            <p class="mb-1"><strong>Boutique:</strong> <?php echo e($session->boutique->nom); ?></p>
                            <p class="mb-1"><strong>Ouverture:</strong> <?php echo e($session->date_ouverture->format('d/m/Y H:i')); ?></p>
                            <p class="mb-0"><strong>Montant initial:</strong> <?php echo e(number_format($session->montant_initial, 0, ',', ' ')); ?> FCFA</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-success mb-2">Calculs théoriques</h6>
                            <p class="mb-1"><strong>Montant théorique:</strong> <?php echo e(number_format($session->montant_theorique ?? 0, 0, ',', ' ')); ?> FCFA</p>
                            <p class="mb-1"><strong>Écart:</strong>
                                <span class="text-<?php echo e(($session->ecart ?? 0) >= 0 ? 'success' : 'danger'); ?>">
                                    <?php echo e(number_format($session->ecart ?? 0, 0, ',', ' ')); ?> FCFA
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <form action="<?php echo e(route('pos.store_close')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="session_id" value="<?php echo e($session->id); ?>">

                    <div class="mb-3">
                        <label for="montant_final" class="form-label">Montant final en caisse</label>
                        <input type="number" class="form-control <?php $__errorArgs = ['montant_final'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="montant_final" name="montant_final"
                               value="<?php echo e(old('montant_final')); ?>" min="0" step="0.01" required>
                        <div class="form-text">Montant d'argent compté physiquement dans la caisse</div>
                        <?php $__errorArgs = ['montant_final'];
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

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes de fermeture (optionnel)</label>
                        <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Remarques sur la fermeture de la caisse..."><?php echo e(old('notes')); ?></textarea>
                        <?php $__errorArgs = ['notes'];
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

                    <!-- Résumé -->
                    <div class="alert alert-info">
                        <h6>Résumé de la session:</h6>
                        <p class="mb-0">
                            <strong>Montant initial:</strong> <?php echo e(number_format($session->montant_initial, 0, ',', ' ')); ?> FCFA<br>
                            <strong>Montant théorique:</strong> <?php echo e(number_format($session->montant_theorique ?? 0, 0, ',', ' ')); ?> FCFA<br>
                            <strong>Écart calculé:</strong> <?php echo e(number_format($session->ecart ?? 0, 0, ',', ' ')); ?> FCFA
                        </p>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Fermer la caisse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/pos/admin_close.blade.php ENDPATH**/ ?>