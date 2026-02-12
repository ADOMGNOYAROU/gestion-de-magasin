

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    Détails de la Vente #<?php echo e($vente->numero_ticket); ?>

                </h1>
                <div>
                    <a href="<?php echo e(route('ventes.recu', $vente)); ?>" class="btn btn-success" target="_blank">
                        <i class="fas fa-print"></i> Reçu
                    </a>
                    <a href="<?php echo e(route('ventes.index')); ?>" class="btn btn-secondary ms-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Informations de la Vente</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>N° Ticket :</strong></td>
                                    <td><strong><?php echo e($vente->numero_ticket); ?></strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Date :</strong></td>
                                    <td><?php echo e($vente->date_vente->format('d/m/Y H:i')); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status :</strong></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($vente->status === 'terminee' ? 'success' : ($vente->status === 'annulee' ? 'danger' : 'warning')); ?>">
                                            <?php echo e($vente->status === 'terminee' ? 'Terminée' : ($vente->status === 'annulee' ? 'Annulée' : 'En cours')); ?>

                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Vendeur :</strong></td>
                                    <td><?php echo e($vente->user->name); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Boutique :</strong></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e($vente->boutique->nom); ?></span>
                                        <br><small class="text-muted"><?php echo e($vente->boutique->adresse); ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Magasin :</strong></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo e($vente->boutique->magasin->nom); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Méthode de paiement :</strong></td>
                                    <td><?php echo e($vente->paymentMethod->nom); ?></td>
                                </tr>
                                <?php if($vente->montant_recu > 0): ?>
                                <tr>
                                    <td><strong>Montant reçu :</strong></td>
                                    <td><?php echo e(number_format($vente->montant_recu, 0)); ?> FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Monnaie :</strong></td>
                                    <td><?php echo e(number_format($vente->monnaie, 0)); ?> FCFA</td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <!-- Produits vendus -->
                    <div class="card mt-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Produits Vendus (<?php echo e($vente->venteProduits->count()); ?> article(s))</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Produit</th>
                                            <th class="text-center">Qté</th>
                                            <th class="text-end">Prix Unit.</th>
                                            <th class="text-end">Remise</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $vente->venteProduits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($produit->produit->nom); ?></strong>
                                                <br><small class="text-muted"><?php echo e($produit->produit->categorie); ?></small>
                                            </td>
                                            <td class="text-center"><?php echo e($produit->quantite); ?></td>
                                            <td class="text-end"><?php echo e(number_format($produit->prix_unitaire, 0)); ?> FCFA</td>
                                            <td class="text-end"><?php echo e(number_format($produit->remise, 0)); ?> FCFA</td>
                                            <td class="text-end"><strong><?php echo e(number_format($produit->sous_total, 0)); ?> FCFA</strong></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Résumé Financier</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nombre d'articles :</strong></td>
                                    <td><span class="badge bg-info"><?php echo e($vente->totalProduits); ?></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Total HT :</strong></td>
                                    <td><?php echo e(number_format($vente->montant_total, 0)); ?> FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Bénéfice Total :</strong></td>
                                    <td>
                                        <strong class="text-success"><?php echo e(number_format($vente->benefice_total, 0)); ?> FCFA</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Marge moyenne :</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo e($vente->montant_total > 0 ? round(($vente->benefice_total / $vente->montant_total) * 100, 1) : 0); ?>%
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6>Actions</h6>
                                    <?php if($vente->status === 'terminee'): ?>
                                    <div class="btn-group" role="group">
                                        <form action="<?php echo e(route('ventes.destroy', $vente->id)); ?>"
                                              method="POST" style="display: inline-block;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-outline-warning btn-sm"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler cette vente? Le stock sera automatiquement restauré.')">
                                                <i class="fas fa-undo"></i> Annuler
                                            </button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="<?php echo e(route('ventes.recu', $vente)); ?>" class="btn btn-outline-success btn-sm" target="_blank">
                                        <i class="fas fa-receipt"></i> Reçu
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
                                <strong>ID Vente :</strong> #<?php echo e($vente->id); ?><br>
                                <strong>Créé le :</strong> <?php echo e($vente->created_at->format('d/m/Y H:i:s')); ?><br>
                                <strong>Modifié le :</strong> <?php echo e($vente->updated_at->format('d/m/Y H:i:s')); ?><br>
                                <?php if($vente->sessionCaisse): ?>
                                <strong>Session caisse :</strong> #<?php echo e($vente->sessionCaisse->id); ?>

                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/ventes/show.blade.php ENDPATH**/ ?>