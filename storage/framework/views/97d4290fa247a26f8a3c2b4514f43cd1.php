

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Historique des Transferts</h1>
                <a href="<?php echo e(route('transferts.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-exchange-alt"></i> Nouveau Transfert
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

            <!-- Filtres de recherche -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('transferts.index')); ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Rechercher produit, magasin..." 
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
                                    <a href="<?php echo e(route('transferts.index')); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Effacer
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des transferts -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Magasin Source</th>
                                    <th>Boutique Dest.</th>
                                    <th>Quantité</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $transferts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transfert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($transfert->date->format('d/m/Y')); ?></td>
                                        <td>
                                            <strong><?php echo e($transfert->produit->nom); ?></strong>
                                            <br><small class="text-muted"><?php echo e($transfert->produit->categorie); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-minus"></i> <?php echo e($transfert->magasin->nom); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fas fa-plus"></i> <?php echo e($transfert->boutique->nom); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($transfert->quantite); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Effectué
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('transferts.show', $transfert->id)); ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="<?php echo e(route('transferts.destroy', $transfert->id)); ?>" 
                                                      method="POST" style="display: inline-block;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Annuler le transfert" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir annuler ce transfert? Les stocks seront restaurés.')">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucun transfert trouvé</p>
                                            <a href="<?php echo e(route('transferts.create')); ?>" class="btn btn-primary">
                                                Créer le premier transfert
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if($transferts->hasPages()): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <?php echo e($transferts->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Transferts</h5>
                            <h3><?php echo e($transferts->total()); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Quantité Totale</h5>
                            <h3><?php echo e(number_format($transferts->sum('quantite'), 0, ',', ' ')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Produits Uniques</h5>
                            <h3><?php echo e($transferts->pluck('produit_id')->unique()->count()); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Boutiques Desservies</h5>
                            <h3><?php echo e($transferts->pluck('boutique_id')->unique()->count()); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/transferts/index.blade.php ENDPATH**/ ?>