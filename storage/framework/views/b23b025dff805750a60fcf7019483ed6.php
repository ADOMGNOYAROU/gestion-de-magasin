

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestion des Produits</h1>
                <div>
                    <a href="<?php echo e(route('rapports.stock.pdf')); ?>" class="btn btn-outline-danger me-2 <?php echo e(hideIfCannot('manage-rapports')); ?>">
                        <i class="fas fa-warehouse"></i> Rapport Stock
                    </a>
                    <a href="<?php echo e(route('produits.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Produit
                    </a>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filtre de recherche -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('produits.index')); ?>">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Rechercher par nom ou catégorie..." 
                                       value="<?php echo e($search ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                                <?php if($search ?? null): ?>
                                    <a href="<?php echo e(route('produits.index')); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Effacer
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des produits -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Prix Achat</th>
                                    <th>Prix Vente</th>
                                    <th>Marge</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $produits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($produit->id); ?></td>
                                        <td>
                                            <strong><?php echo e($produit->nom); ?></strong>
                                            <?php if($produit->description): ?>
                                                <br><small class="text-muted"><?php echo e(Str::limit($produit->description, 50)); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($produit->categorie); ?></span>
                                        </td>
                                        <td><?php echo e(number_format($produit->prix_achat, 0, ',', ' ')); ?> FCFA</td>
                                        <td><?php echo e(number_format($produit->prix_vente, 0, ',', ' ')); ?> FCFA</td>
                                        <td>
                                            <span class="badge bg-<?php echo e($produit->marge_percentage > 30 ? 'success' : ($produit->marge_percentage > 15 ? 'warning' : 'danger')); ?>">
                                                <?php echo e($produit->marge_percentage); ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo e($produit->statut == 'actif' ? 'success' : 'secondary'); ?>">
                                                <?php echo e(ucfirst($produit->statut)); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('produits.show', $produit->id)); ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('produits.edit', $produit->id)); ?>" 
                                                   class="btn btn-sm btn-outline-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="<?php echo e(route('produits.destroy', $produit->id)); ?>" 
                                                      method="POST" style="display: inline-block;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Supprimer" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucun produit trouvé</p>
                                            <a href="<?php echo e(route('produits.create')); ?>" class="btn btn-primary">
                                                Créer le premier produit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if($produits->hasPages()): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <?php echo e($produits->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/produits/index.blade.php ENDPATH**/ ?>