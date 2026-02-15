<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user"></i> <?php echo e($client->nom_complet); ?>

                    </h1>
                    <small class="text-muted">Client ID: <?php echo e($client->id); ?> • Inscrit le <?php echo e($client->date_inscription->format('d/m/Y')); ?></small>
                </div>
                <div>
                    <a href="<?php echo e(route('clients.edit', $client)); ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <!-- Informations générales -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle"></i> Informations Générales
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Nom complet:</strong></td>
                                            <td><?php echo e($client->nom_complet); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>
                                                <?php if($client->email): ?>
                                                    <?php echo e($client->email); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Non fourni</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Téléphone:</strong></td>
                                            <td>
                                                <?php if($client->telephone): ?>
                                                    <?php echo e($client->telephone); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Non fourni</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date de naissance:</strong></td>
                                            <td>
                                                <?php if($client->date_naissance): ?>
                                                    <?php echo e($client->date_naissance->format('d/m/Y')); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Non fournie</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Âge:</strong></td>
                                            <td>
                                                <?php if($client->age): ?>
                                                    <?php echo e($client->age); ?> ans
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sexe:</strong></td>
                                            <td>
                                                <?php if($client->sexe == 'M'): ?>
                                                    <span class="badge badge-primary">Masculin</span>
                                                <?php elseif($client->sexe == 'F'): ?>
                                                    <span class="badge badge-info">Féminin</span>
                                                <?php else: ?>
                                                    <span class="text-muted">Non spécifié</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Adresse:</strong></td>
                                            <td>
                                                <?php if($client->adresse): ?>
                                                    <?php echo e($client->adresse); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Non fournie</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ville:</strong></td>
                                            <td>
                                                <?php if($client->ville): ?>
                                                    <?php echo e($client->ville); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Non fournie</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Code postal:</strong></td>
                                            <td>
                                                <?php if($client->code_postal): ?>
                                                    <?php echo e($client->code_postal); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Non fourni</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Pays:</strong></td>
                                            <td><?php echo e($client->pays); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut:</strong></td>
                                            <td>
                                                <span class="badge <?php echo e($client->statut == 'actif' ? 'badge-success' : 'badge-secondary'); ?>">
                                                    <?php echo e(ucfirst($client->statut)); ?>

                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dernière vente:</strong></td>
                                            <td>
                                                <?php if($client->derniere_vente): ?>
                                                    <?php echo e($client->derniere_vente->format('d/m/Y')); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">Jamais</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques de fidélité -->
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">
                                <i class="fas fa-star"></i> Programme de Fidélité
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <h2 class="text-success font-weight-bold"><?php echo e(number_format($client->solde_points, 0, ',', ' ')); ?></h2>
                                <p class="text-muted mb-0">Points disponibles</p>
                            </div>

                            <div class="mb-3">
                                <h4 class="text-primary"><?php echo e(number_format($client->total_achats, 0, ',', ' ')); ?> FCFA</h4>
                                <p class="text-muted mb-0">Total des achats</p>
                            </div>

                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-right">
                                        <h5 class="text-info"><?php echo e($client->ventes->count()); ?></h5>
                                        <small class="text-muted">Ventes</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h5 class="text-warning"><?php echo e($client->coupons->where('utilise', false)->count()); ?></h5>
                                    <small class="text-muted">Coupons actifs</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglets pour différentes sections -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="clientTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ventes-tab" data-toggle="tab" href="#ventes" role="tab">
                                <i class="fas fa-shopping-cart"></i> Historique des Achats
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="points-tab" data-toggle="tab" href="#points" role="tab">
                                <i class="fas fa-star"></i> Points de Fidélité
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="coupons-tab" data-toggle="tab" href="#coupons" role="tab">
                                <i class="fas fa-ticket-alt"></i> Coupons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="actions-tab" data-toggle="tab" href="#actions" role="tab">
                                <i class="fas fa-cogs"></i> Actions
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="clientTabsContent">
                        <!-- Onglet Historique des Achats -->
                        <div class="tab-pane fade show active" id="ventes" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Numéro Ticket</th>
                                            <th>Montant</th>
                                            <th>Points Gagnés</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $client->ventes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($vente->date_vente->format('d/m/Y H:i')); ?></td>
                                                <td>
                                                    <a href="<?php echo e(route('ventes.show', $vente)); ?>" class="text-decoration-none">
                                                        <?php echo e($vente->numero_ticket); ?>

                                                    </a>
                                                </td>
                                                <td><?php echo e(number_format($vente->montant_total, 0, ',', ' ')); ?> FCFA</td>
                                                <td>
                                                    <?php echo e($vente->pointsGagnes ?? 0); ?>

                                                </td>
                                                <td>
                                                    <a href="<?php echo e(route('ventes.show', $vente)); ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Voir
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                                    <br>Aucun achat enregistré
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Onglet Points de Fidélité -->
                        <div class="tab-pane fade" id="points" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Points</th>
                                            <th>Solde</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $client->points; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($point->date_transaction->format('d/m/Y H:i')); ?></td>
                                                <td>
                                                    <?php if($point->type_operation == 'gain'): ?>
                                                        <span class="badge badge-success">Gain</span>
                                                    <?php elseif($point->type_operation == 'utilisation'): ?>
                                                        <span class="badge badge-warning">Utilisation</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-info"><?php echo e(ucfirst($point->type_operation)); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($point->description_complete); ?></td>
                                                <td>
                                                    <?php if($point->points_gagnes > 0): ?>
                                                        <span class="text-success">+<?php echo e($point->points_gagnes); ?></span>
                                                    <?php else: ?>
                                                        <span class="text-danger">-<?php echo e($point->points_utilises); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($point->points_net); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="fas fa-star fa-2x mb-2"></i>
                                                    <br>Aucun mouvement de points
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Onglet Coupons -->
                        <div class="tab-pane fade" id="coupons" role="tabpanel">
                            <div class="row">
                                <?php $__empty_1 = true; $__currentLoopData = $client->coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border <?php echo e($coupon->est_valide ? 'border-success' : 'border-secondary'); ?>">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="card-title mb-1"><?php echo e($coupon->code); ?></h6>
                                                        <p class="card-text mb-1"><?php echo e($coupon->description_type); ?></p>
                                                        <small class="text-muted">
                                                            Créé le <?php echo e($coupon->created_at->format('d/m/Y')); ?>

                                                            <?php if($coupon->date_expiration): ?>
                                                                <br>Expire le <?php echo e($coupon->date_expiration->format('d/m/Y')); ?>

                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                    <div class="text-right">
                                                        <?php if($coupon->utilise): ?>
                                                            <span class="badge badge-secondary">Utilisé</span>
                                                        <?php elseif($coupon->est_expire): ?>
                                                            <span class="badge badge-danger">Expiré</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-success">Valide</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="col-12 text-center text-muted py-4">
                                        <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                                        <br>Aucun coupon
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Onglet Actions -->
                        <div class="tab-pane fade" id="actions" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-plus-circle text-success"></i> Ajouter des Points</h6>
                                        </div>
                                        <div class="card-body">
                                            <form action="<?php echo e(route('clients.ajouter_points', $client)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <div class="mb-3">
                                                    <label for="points_ajouter" class="form-label">Nombre de points</label>
                                                    <input type="number" class="form-control" id="points_ajouter" name="points" min="1" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description_ajouter" class="form-label">Description</label>
                                                    <input type="text" class="form-control" id="description_ajouter" name="description" required>
                                                </div>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-plus"></i> Ajouter les points
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-ticket-alt text-primary"></i> Générer un Coupon</h6>
                                        </div>
                                        <div class="card-body">
                                            <form action="<?php echo e(route('clients.generer_coupon', $client)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <div class="mb-3">
                                                    <label for="type_coupon" class="form-label">Type de coupon</label>
                                                    <select class="form-control" id="type_coupon" name="type" required>
                                                        <option value="pourcentage">Pourcentage</option>
                                                        <option value="montant_fixe">Montant fixe</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="valeur_coupon" class="form-label">Valeur</label>
                                                    <input type="number" class="form-control" id="valeur_coupon" name="valeur" min="0.01" step="0.01" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="expiration_coupon" class="form-label">Jours avant expiration</label>
                                                    <input type="number" class="form-control" id="expiration_coupon" name="jours_expiration" min="1" placeholder="Optionnel">
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Générer le coupon
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/clients/show.blade.php ENDPATH**/ ?>