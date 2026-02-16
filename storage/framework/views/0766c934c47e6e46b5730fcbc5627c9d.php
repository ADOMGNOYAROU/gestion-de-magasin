<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails du Magasin</h1>
                <div>
                    <a href="<?php echo e(route('magasins.edit', $magasin)); ?>" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="<?php echo e(route('magasins.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Informations du magasin</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nom:</label>
                                        <p class="form-control-plaintext"><?php echo e($magasin->nom); ?></p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Localisation:</label>
                                        <p class="form-control-plaintext"><?php echo e($magasin->localisation); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Responsable:</label>
                                <p class="form-control-plaintext">
                                    <?php echo e($magasin->responsable ? $magasin->responsable->name . ' (' . $magasin->responsable->email . ')' : 'N/A'); ?>

                                </p>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Date de création:</label>
                                        <p class="form-control-plaintext"><?php echo e($magasin->created_at->format('d/m/Y H:i')); ?></p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Dernière modification:</label>
                                        <p class="form-control-plaintext"><?php echo e($magasin->updated_at->format('d/m/Y H:i')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Boutiques associées (<?php echo e($magasin->boutiques->count()); ?>)</h5>

                            <?php if($magasin->boutiques->count() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Adresse</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $magasin->boutiques; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $boutique): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($boutique->nom); ?></td>
                                                <td><?php echo e($boutique->adresse); ?></td>
                                                <td>
                                                    <a href="<?php echo e(route('boutiques.show', $boutique)); ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Aucune boutique associée à ce magasin.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Stock du magasin</h5>

                            <?php if($magasin->stockMagasins->count() > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php $__currentLoopData = $magasin->stockMagasins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo e($stock->produit->nom); ?>

                                        <span class="badge bg-primary rounded-pill"><?php echo e($stock->quantite); ?></span>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Aucun stock enregistré.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="text-danger mb-3">Actions dangereuses</h5>
                            <?php if($magasin->boutiques->count() == 0): ?>
                                <form method="POST" action="<?php echo e(route('magasins.destroy', $magasin)); ?>" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce magasin ? Cette action est irréversible.')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash"></i> Supprimer le magasin
                                    </button>
                                </form>
                            <?php else: ?>
                                <p class="text-muted small">Impossible de supprimer ce magasin car il contient des boutiques.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/magasins/show.blade.php ENDPATH**/ ?>