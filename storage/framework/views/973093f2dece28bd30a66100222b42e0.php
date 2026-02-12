<?php $__env->startSection('title', 'Gestion des Caisses'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Accueil</a></li>
    <li class="breadcrumb-item active">Gestion des Caisses</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header'); ?>
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">Gestion des Caisses</h1>
        <p class="text-muted mb-0">Ouvrir et fermer les sessions de caisse des vendeurs</p>
    </div>
    <div>
        <a href="<?php echo e(route('pos.open')); ?>" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Ouvrir une caisse
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4">
    <!-- Test simple -->
    <div class="col-12">
        <div class="alert alert-info">
            <h5>Test de fonctionnement</h5>
            <p>Interface de gestion des caisses chargée avec succès !</p>
            <ul>
                <li>Utilisateur: <?php echo e(Auth::user()->name); ?></li>
                <li>Rôle: <?php echo e(Auth::user()->role); ?></li>
                <li>Boutiques disponibles: <?php echo e($boutiques->count() ?? 0); ?></li>
                <li>Sessions actives: <?php echo e($sessionsActives->count() ?? 0); ?></li>
            </ul>
        </div>
    </div>

    <!-- Boutiques -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Boutiques</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php $__empty_1 = true; $__currentLoopData = $boutiques ?? collect(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $boutique): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo e($boutique->nom); ?></strong><br>
                            <small class="text-muted"><?php echo e($boutique->magasin->nom ?? 'Aucun magasin'); ?></small>
                        </div>
                        <span class="badge bg-secondary">vendeurs</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="list-group-item text-center text-muted">
                        Aucune boutique trouvée
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions de caisse actives -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sessions de caisse actives</h5>
                <a href="<?php echo e(route('pos.close')); ?>" class="btn btn-warning btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Fermer une caisse
                </a>
            </div>
            <div class="card-body">
                <?php if(($sessionsActives ?? collect())->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Vendeur</th>
                                <th>Boutique</th>
                                <th>Ouverture</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $sessionsActives; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($session->vendeur->name); ?></strong><br>
                                    <small class="text-muted"><?php echo e($session->vendeur->email); ?></small>
                                </td>
                                <td><?php echo e($session->boutique->nom); ?></td>
                                <td><?php echo e($session->date_ouverture->format('d/m/Y H:i')); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e($session->status === 'ouverte' ? 'success' : 'warning'); ?>">
                                        <?php echo e(ucfirst($session->status)); ?>

                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('pos.close', ['vendeur_id' => $session->vendeur_id])); ?>"
                                       class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-sign-out-alt me-1"></i>Fermer
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-cash-register fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune session de caisse active</h5>
                    <p class="text-muted">Toutes les caisses sont fermées</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/pos/admin.blade.php ENDPATH**/ ?>