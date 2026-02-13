

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails du Transfert</h1>
                <div>
                    <a href="<?php echo e(route('transferts.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Informations du Transfert</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID Transfert :</strong></td>
                                    <td>#<?php echo e($transfert->id); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Date :</strong></td>
                                    <td><?php echo e($transfert->date->format('d/m/Y')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Produit :</strong></td>
                                    <td>
                                        <strong><?php echo e($transfert->produit->nom); ?></strong>
                                        <br><small class="text-muted"><?php echo e($transfert->produit->categorie); ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Magasin Source :</strong></td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-minus"></i> <?php echo e($transfert->magasin->nom); ?>

                                        </span>
                                        <br><small class="text-muted"><?php echo e($transfert->magasin->localisation); ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Boutique Destination :</strong></td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-plus"></i> <?php echo e($transfert->boutique->nom); ?>

                                        </span>
                                        <br><small class="text-muted"><?php echo e($transfert->boutique->localisation); ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Quantité Transférée :</strong></td>
                                    <td>
                                        <span class="badge bg-info fs-6"><?php echo e($transfert->quantite); ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Impact sur les Stocks</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning py-2">
                                <i class="fas fa-minus-circle"></i>
                                <strong>Magasin Source</strong><br>
                                Stock diminué de <?php echo e($transfert->quantite); ?> unités
                            </div>
                            
                            <div class="alert alert-success py-2">
                                <i class="fas fa-plus-circle"></i>
                                <strong>Boutique Destination</strong><br>
                                Stock augmenté de <?php echo e($transfert->quantite); ?> unités
                            </div>

                            <hr>

                            <h6>Stocks Actuels</h6>
                            
                            <div class="mb-2">
                                <small class="text-muted">Stock Magasin :</small><br>
                                <strong><?php echo e($transfert->produit->stockTotalMagasin ?? 0); ?> unités</strong>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">Stock Boutique :</small><br>
                                <strong><?php echo e($transfert->produit->stockTotalBoutique ?? 0); ?> unités</strong>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Statut</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle"></i>
                                <strong>Transfert Effectué</strong><br>
                                <small>Le transfert a été complété avec succès</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Actions</h6>
                            <div class="btn-group" role="group">
                                <form action="<?php echo e(route('transferts.destroy', $transfert->id)); ?>" 
                                      method="POST" style="display: inline-block;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-warning" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir annuler ce transfert? Les stocks seront automatiquement restaurés.')">
                                        <i class="fas fa-undo"></i> Annuler le transfert
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div>
                            <a href="<?php echo e(route('produits.show', $transfert->produit->id)); ?>" class="btn btn-outline-info">
                                <i class="fas fa-box"></i> Voir le produit
                            </a>
                            <a href="<?php echo e(route('transferts.index')); ?>" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-list"></i> Voir tous les transferts
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations système -->
            <div class="card mt-3">
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Informations système :</strong><br>
                        <strong>Créé le :</strong> <?php echo e($transfert->created_at->format('d/m/Y H:i:s')); ?><br>
                        <strong>Modifié le :</strong> <?php echo e($transfert->updated_at->format('d/m/Y H:i:s')); ?>

                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/transferts/show.blade.php ENDPATH**/ ?>