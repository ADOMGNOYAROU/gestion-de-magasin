

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestion des Boutiques</h1>
                <a href="<?php echo e(route('boutiques.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Boutique
                </a>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Adresse</th>
                                    <th>Magasin</th>
                                    <th>Vendeur</th>
                                    <th>Date de création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $boutiques; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $boutique): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($boutique->id); ?></td>
                                    <td><?php echo e($boutique->nom); ?></td>
                                    <td><?php echo e($boutique->adresse); ?></td>
                                    <td><?php echo e($boutique->magasin ? $boutique->magasin->nom : 'N/A'); ?></td>
                                    <td><?php echo e($boutique->responsable ? $boutique->responsable->name : 'N/A'); ?></td>
                                    <td><?php echo e($boutique->created_at->format('d/m/Y')); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('boutiques.show', $boutique)); ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('boutiques.edit', $boutique)); ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?php echo e(route('boutiques.destroy', $boutique)); ?>" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette boutique ?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <?php echo e($boutiques->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/boutiques/index.blade.php ENDPATH**/ ?>