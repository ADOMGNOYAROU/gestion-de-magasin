<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Historique des Ventes</h1>
                <div>
                    <a href="<?php echo e(route('rapports.ventes.form')); ?>" class="btn btn-outline-primary me-2 <?php echo e(hideIfCannot('manage-rapports')); ?>">
                        <i class="fas fa-chart-line"></i> Rapport Ventes
                    </a>
                    <a href="<?php echo e(route('ventes.create')); ?>" class="btn btn-success">
                        <i class="fas fa-cash-register"></i> Nouvelle Vente
                    </a>
                </div>
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

            <!-- Filtres de recherche -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('ventes.index')); ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Rechercher produit, boutique..." 
                                       value="<?php echo e($search ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_debut" class="form-control" 
                                       placeholder="Date début" value="<?php echo e($date_debut ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_fin" class="form-control" 
                                       placeholder="Date fin" value="<?php echo e($date_fin ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                                <?php if($search || $date_debut || $date_fin): ?>
                                    <a href="<?php echo e(route('ventes.index')); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Effacer
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des ventes -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Boutique</th>
                                    <th>Quantité</th>
                                    <th>Prix Unitaire</th>
                                    <th>Total</th>
                                    <th>Bénéfice</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $ventes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($vente->date_vente->format('d/m/Y')); ?></td>
                                        <td>
                                            <?php $__currentLoopData = $vente->venteProduits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <strong><?php echo e($vp->produit->nom); ?></strong> (<?php echo e($vp->quantite); ?>)
                                                <?php if(!$loop->last): ?><br><?php endif; ?>
                                                <br><small class="text-muted"><?php echo e($vp->produit->categorie); ?></small>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($vente->boutique->nom); ?></span>
                                            <br><small class="text-muted"><?php echo e($vente->boutique->magasin->nom); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning"><?php echo e($vente->total_produits); ?></span>
                                        </td>
                                        <td><?php echo e($vente->total_produits > 0 ? number_format($vente->montant_total / $vente->total_produits, 0, ',', ' ') : 0); ?> FCFA</td>
                                        <td>
                                            <strong class="text-primary"><?php echo e(number_format($vente->montant_total, 0, ',', ' ')); ?> FCFA</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-success"><?php echo e(number_format($vente->benefice_total, 0, ',', ' ')); ?> FCFA</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('ventes.show', $vente->id)); ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="<?php echo e(route('ventes.destroy', $vente->id)); ?>" 
                                                      method="POST" style="display: inline-block;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Annuler la vente" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir annuler cette vente? Le stock sera restauré.')">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucune vente trouvée</p>
                                            <a href="<?php echo e(route('ventes.create')); ?>" class="btn btn-success">
                                                Enregistrer la première vente
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if($ventes->hasPages()): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <?php echo e($ventes->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Ventes</h5>
                            <h3><?php echo e($ventes->total()); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Chiffre d'Affaires</h5>
                            <h3><?php echo e(number_format($ventes->sum('montant_total'), 0, ',', ' ')); ?> FCFA</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Bénéfice Total</h5>
                            <h3><?php echo e(number_format($ventes->sum(function($vente) { return $vente->benefice_total; }), 0, ',', ' ')); ?> FCFA</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Marge Moyenne</h5>
                            <h3><?php echo e($ventes->sum('montant_total') > 0 ? round(($ventes->sum(function($vente) { return $vente->benefice_total; }) / $ventes->sum('montant_total')) * 100, 1) : 0); ?>%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/ventes/index.blade.php ENDPATH**/ ?>