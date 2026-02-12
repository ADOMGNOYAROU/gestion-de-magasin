<?php $__env->startSection('title', 'Ouvrir une caisse'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Accueil</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('pos.index')); ?>">Gestion des Caisses</a></li>
    <li class="breadcrumb-item active">Ouvrir une caisse</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header'); ?>
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">Ouvrir une caisse</h1>
        <p class="text-muted mb-0">Sélectionner un vendeur et ouvrir sa session de caisse</p>
    </div>
    <div>
        <a href="<?php echo e(route('pos.index')); ?>" class="btn btn-secondary">
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
                <h5 class="mb-0">Sélection du vendeur</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('pos.store_open')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label for="vendeur_id" class="form-label">Vendeur</label>
                        <select class="form-select <?php $__errorArgs = ['vendeur_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="vendeur_id" name="vendeur_id" required>
                            <option value="">-- Sélectionner un vendeur --</option>
                            <?php $__currentLoopData = $vendeurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendeur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($vendeur->id); ?>"
                                    <?php echo e(old('vendeur_id') == $vendeur->id ? 'selected' : ''); ?>>
                                <?php echo e($vendeur->name); ?> - <?php echo e($vendeur->boutique ? $vendeur->boutique->nom : 'Aucune boutique'); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['vendeur_id'];
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
                        <label for="montant_initial" class="form-label">Montant initial en caisse</label>
                        <input type="number" class="form-control <?php $__errorArgs = ['montant_initial'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="montant_initial" name="montant_initial"
                               value="<?php echo e(old('montant_initial', 0)); ?>" min="0" step="0.01" required>
                        <div class="form-text">Montant d'argent présent dans la caisse au début de la session</div>
                        <?php $__errorArgs = ['montant_initial'];
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
                        <label for="notes" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Notes sur l'ouverture de la caisse..."><?php echo e(old('notes')); ?></textarea>
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

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-play me-2"></i>Ouvrir la caisse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/pos/admin_open.blade.php ENDPATH**/ ?>