

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails de l'Entrée de Stock</h1>
                <div>
                    <a href="<?php echo e(route('entrees-stock.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Informations de l'Entrée</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID Entrée :</strong></td>
                                    <td>#<?php echo e($entree->id); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Date :</strong></td>
                                    <td><?php echo e($entree->date->format('d/m/Y')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Produit :</strong></td>
                                    <td>
                                        <?php if($entree->produit): ?>
                                            <strong><?php echo e($entree->produit->nom); ?></strong>
                                            <?php if($entree->produit->categorie): ?>
                                                <br><small class="text-muted"><?php echo e($entree->produit->categorie); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-danger">Produit introuvable</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Magasin :</strong></td>
                                    <td>
                                        <?php if($entree->magasin): ?>
                                            <span class="badge bg-info"><?php echo e($entree->magasin->nom); ?></span>
                                            <?php if($entree->magasin->localisation): ?>
                                                <br><small class="text-muted"><?php echo e($entree->magasin->localisation); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Magasin inconnu</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Source :</strong></td>
                                    <td>
                                        <?php if($entree->fournisseur): ?>
                                            <div class="alert alert-primary py-2">
                                                <i class="fas fa-truck"></i>
                                                <strong>Fournisseur :</strong> <?php echo e($entree->fournisseur->nom ?? 'Non spécifié'); ?>

                                                <?php if($entree->fournisseur): ?>
                                                    <br><small>
                                                        <?php if($entree->fournisseur->contact): ?><?php echo e($entree->fournisseur->contact); ?> - <?php endif; ?>
                                                        <?php echo e($entree->fournisseur->telephone ?? ''); ?>

                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning py-2">
                                                <i class="fas fa-handshake"></i>
                                                <strong>Partenaire :</strong> <?php echo e($entree->partenaire->nom ?? 'Non spécifié'); ?>

                                                <?php if($entree->partenaire): ?>
                                                    <br><small>
                                                        <?php if($entree->partenaire->contact): ?><?php echo e($entree->partenaire->contact); ?> - <?php endif; ?>
                                                        <?php echo e($entree->partenaire->telephone ?? ''); ?>

                                                    </small>
                                                    <?php if($entree->partenaire->type_accord): ?>
                                                        <br><small><strong>Type d'accord :</strong> <?php echo e($entree->partenaire->type_accord); ?></small>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Détails Financiers</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Quantité :</strong></td>
                                    <td>
                                        <span class="badge bg-success fs-6"><?php echo e($entree->quantite); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Prix Unitaire :</strong></td>
                                    <td><?php echo e(number_format($entree->prix_unitaire, 0, ',', ' ')); ?> FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Total :</strong></td>
                                    <td>
                                        <strong class="text-primary"><?php echo e(number_format($entree->quantite * $entree->prix_unitaire, 0, ',', ' ')); ?> FCFA</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Prix Standard :</strong></td>
                                    <td>
                                        <?php if($entree->produit && isset($entree->produit->prix_achat)): ?>
                                            <?php echo e(number_format($entree->produit->prix_achat, 0, ',', ' ')); ?> FCFA
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Écart :</strong></td>
                                    <td>
                                        <?php
                                            $ecartClass = 'secondary';
                                            $ecart = 0;
                                            
                                            if ($entree->produit && isset($entree->produit->prix_achat)) {
                                                $ecart = $entree->prix_unitaire - $entree->produit->prix_achat;
                                                $ecartClass = $ecart > 0 
                                                    ? 'danger' 
                                                    : ($ecart < 0 ? 'success' : 'secondary');
                                            }
                                        ?>
                                        <span class="badge bg-<?php echo e($ecartClass); ?>">
                                            <?php if($entree->produit && isset($entree->produit->prix_achat)): ?>
                                                <?php echo e(number_format($ecart, 0, ',', ' ')); ?> FCFA
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Impact sur le Stock</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success">
                                <i class="fas fa-arrow-up"></i>
                                <strong>Stock augmenté de <?php echo e($entree->quantite); ?> unités</strong>
                            </div>
                            
                            <?php if($entree->produit && isset($entree->produit->stockTotalMagasin)): ?>
                                <small class="text-muted">
                                    <strong>Stock total actuel :</strong> <?php echo e($entree->produit->stockTotalMagasin); ?> unités
                                </small>
                            <?php endif; ?>
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
                                <form action="<?php echo e(route('entrees-stock.destroy', $entree->id)); ?>" 
                                      method="POST" style="display: inline-block;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée? Le stock sera automatiquement mis à jour.')">
                                        <i class="fas fa-trash"></i> Supprimer l'entrée
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div>
                            <?php if($entree->produit): ?>
                                <a href="<?php echo e(route('produits.show', $entree->produit->id)); ?>" class="btn btn-outline-info">
                                    <i class="fas fa-box"></i> Voir le produit
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('entrees-stock.index')); ?>" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-list"></i> Voir toutes les entrées
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
                        <strong>Créé le :</strong> <?php echo e($entree->created_at->format('d/m/Y H:i:s')); ?><br>
                        <strong>Modifié le :</strong> <?php echo e($entree->updated_at->format('d/m/Y H:i:s')); ?>

                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/entrees-stock/show.blade.php ENDPATH**/ ?>