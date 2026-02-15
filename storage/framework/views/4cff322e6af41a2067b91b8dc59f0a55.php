<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-robot"></i> Générer Réapprovisionnement Automatique
                    </h1>
                    <small class="text-muted">Produits en rupture de stock ou sous le seuil d'alerte</small>
                </div>
                <a href="<?php echo e(route('commandes-fournisseurs.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('commandes-fournisseurs.generer-reappro')); ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="seuil_alerte" class="form-label">Seuil d'alerte</label>
                                <input type="number" class="form-control" id="seuil_alerte" name="seuil_alerte" value="<?php echo e($seuil); ?>" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="fournisseur_id" class="form-label">Filtrer par fournisseur</label>
                                <select class="form-control" id="fournisseur_id" name="fournisseur_id">
                                    <option value="">Tous les fournisseurs</option>
                                    <?php $__currentLoopData = $fournisseurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fournisseur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($fournisseur->id); ?>" <?php echo e($fournisseurId == $fournisseur->id ? 'selected' : ''); ?>>
                                            <?php echo e($fournisseur->nom); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Produits à réapprovisionner -->
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-boxes"></i> Produits nécessitant un réapprovisionnement
                        <span class="badge badge-primary"><?php echo e($produitsARappro->count()); ?></span>
                    </h6>
                </div>
                <div class="card-body">
                    <?php if($produitsARappro->isEmpty()): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>Aucun produit ne nécessite de réapprovisionnement</h5>
                            <p class="text-muted">Tous les produits ont un stock suffisant.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Catégorie</th>
                                        <th>Stock Total</th>
                                        <th>Stock par Boutique</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $produitsARappro; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($produit->nom); ?></strong>
                                                <?php if($produit->description): ?>
                                                    <br><small class="text-muted"><?php echo e(Str::limit($produit->description, 50)); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo e($produit->categorie); ?></span>
                                            </td>
                                            <td>
                                                <?php echo e($produit->stockBoutiques->sum('quantite')); ?>

                                            </td>
                                            <td>
                                                <?php $__currentLoopData = $produit->stockBoutiques; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <small class="d-block">
                                                        <?php echo e($stock->boutique->nom ?? 'Boutique inconnue'); ?>: <?php echo e($stock->quantite); ?>

                                                    </small>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('commandes-fournisseurs.create', ['fournisseur_id' => $fournisseurId, 'produit_id' => $produit->id])); ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus"></i> Commander
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/commandes-fournisseurs/generer-reappro.blade.php ENDPATH**/ ?>