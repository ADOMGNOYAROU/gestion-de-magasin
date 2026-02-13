<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestion des Fournisseurs</h1>
                <a href="<?php echo e(route('fournisseurs.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter un Fournisseur
                </a>
            </div>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreurs :</strong>
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Liste des Fournisseurs</h6>
                </div>
                <div class="card-body">
                    <?php if($fournisseurs->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Contact</th>
                                        <th>Adresse</th>
                                        <th>Téléphone</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $fournisseurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fournisseur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($fournisseur->nom); ?></td>
                                            <td><?php echo e($fournisseur->contact_personne ?: '-'); ?></td>
                                            <td><?php echo e($fournisseur->adresse ?: '-'); ?></td>
                                            <td><?php echo e($fournisseur->telephone ?: '-'); ?></td>
                                            <td><?php echo e($fournisseur->email ?: '-'); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('fournisseurs.edit', $fournisseur)); ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="<?php echo e(route('fournisseurs.destroy', $fournisseur)); ?>" method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <?php echo e($fournisseurs->links()); ?>

                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun fournisseur trouvé</h5>
                            <p class="text-muted">Commencez par ajouter votre premier fournisseur.</p>
                            <a href="<?php echo e(route('fournisseurs.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ajouter un Fournisseur
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/fournisseurs/index.blade.php ENDPATH**/ ?>