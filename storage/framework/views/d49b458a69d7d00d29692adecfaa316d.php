<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails de la Boutique</h1>
                <div>
                    <a href="<?php echo e(route('boutiques.edit', $boutique)); ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="<?php echo e(route('boutiques.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nom :</strong> <?php echo e($boutique->nom); ?></p>
                            <p><strong>Adresse :</strong> <?php echo e($boutique->adresse ?? 'Non spécifiée'); ?></p>
                            <p><strong>Téléphone :</strong> <?php echo e($boutique->telephone ?? 'Non spécifié'); ?></p>
                            <p><strong>Email :</strong> <?php echo e($boutique->email ?? 'Non spécifié'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Magasin :</strong> <?php echo e($boutique->magasin->nom ?? 'Non spécifié'); ?></p>
                            <p><strong>Responsable :</strong> <?php echo e($boutique->responsable->name ?? 'Non spécifié'); ?></p>
                            <p><strong>Date de création :</strong> <?php echo e($boutique->created_at->format('d/m/Y H:i')); ?></p>
                            <p><strong>Dernière mise à jour :</strong> <?php echo e($boutique->updated_at->format('d/m/Y H:i')); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Stock de la boutique</h6>
                </div>
                <div class="card-body">
                    <?php if($boutique->stockBoutiques->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité en stock</th>
                                        <th>Seuil d'alerte</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $boutique->stockBoutiques; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($stock->produit->nom ?? 'Produit inconnu'); ?></td>
                                            <td><?php echo e($stock->quantite); ?></td>
                                            <td><?php echo e($stock->seuil_alerte ?? 'Non défini'); ?></td>
                                            <td>
                                                <?php if(isset($stock->seuil_alerte) && $stock->quantite <= $stock->seuil_alerte): ?>
                                                    <span class="badge bg-danger">Stock faible</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">En stock</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Aucun produit enregistré dans le stock de cette boutique.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/boutiques/show.blade.php ENDPATH**/ ?>