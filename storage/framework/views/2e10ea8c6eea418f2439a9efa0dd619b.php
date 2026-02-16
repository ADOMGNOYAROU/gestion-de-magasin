<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Stock</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-box {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT DE STOCK</h1>
        <p>Généré le : <?php echo e($dateGeneration); ?></p>
        <p>Par : <?php echo e($user->name); ?> (<?php echo e($user->role); ?>)</p>
        <?php if(isset($magasin)): ?>
            <p>Magasin : <?php echo e($magasin->nom); ?></p>
        <?php elseif(isset($boutique)): ?>
            <p>Boutique : <?php echo e($boutique->nom); ?></p>
        <?php endif; ?>
    </div>

    <div class="info-box">
        <strong>Résumé :</strong> <?php echo e($produits->count()); ?> produits actifs
        <?php if(isset($magasin)): ?>
            <br><strong>Stock total magasin :</strong> <?php echo e($produits->sum(function($p) use ($magasin) { return $p->stockMagasins->where('magasin_id', $magasin->id)->sum('quantite'); })); ?> unités
            <br><strong>Stock total boutiques :</strong> <?php echo e($produits->sum(function($p) use ($magasin) { return $p->stockBoutiques->filter(function($sb) use ($magasin) { return $sb->boutique->magasin_id == $magasin->id; })->sum('quantite'); })); ?> unités
        <?php elseif(isset($boutique)): ?>
            <br><strong>Stock boutique :</strong> <?php echo e($produits->sum(function($p) use ($boutique) { return $p->stockBoutiques->where('boutique_id', $boutique->id)->sum('quantite'); })); ?> unités
        <?php else: ?>
            <br><strong>Stock total magasins :</strong> <?php echo e($produits->sum(function($p) { return $p->stockMagasins->sum('quantite'); })); ?> unités
            <br><strong>Stock total boutiques :</strong> <?php echo e($produits->sum(function($p) { return $p->stockBoutiques->sum('quantite'); })); ?> unités
        <?php endif; ?>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Catégorie</th>
                <th>Prix Vente</th>
                <?php if(!isset($boutique)): ?>
                    <th class="text-center">Stock Magasin</th>
                <?php endif; ?>
                <?php if(!isset($magasin)): ?>
                    <th class="text-center">Stock Boutiques</th>
                <?php endif; ?>
                <th class="text-center">Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $produits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($produit->nom); ?></strong></td>
                    <td><?php echo e($produit->categorie); ?></td>
                    <td class="text-right"><?php echo e(number_format($produit->prix_vente, 0, ',', ' ')); ?> FCFA</td>
                    
                    <?php if(!isset($boutique)): ?>
                        <td class="text-center">
                            <?php if(isset($magasin)): ?>
                                <?php echo e($produit->stockMagasins->where('magasin_id', $magasin->id)->sum('quantite')); ?>

                            <?php else: ?>
                                <?php echo e($produit->stockMagasins->sum('quantite')); ?>

                                <?php if($produit->stockMagasins->count() > 1): ?>
                                    <small class="text-muted">(<?php echo e($produit->stockMagasins->count()); ?> magasins)</small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    
                    <?php if(!isset($magasin)): ?>
                        <td class="text-center">
                            <?php if(isset($boutique)): ?>
                                <?php echo e($produit->stockBoutiques->where('boutique_id', $boutique->id)->sum('quantite')); ?>

                            <?php else: ?>
                                <?php echo e($produit->stockBoutiques->sum('quantite')); ?>

                                <?php if($produit->stockBoutiques->count() > 1): ?>
                                    <small class="text-muted">(<?php echo e($produit->stockBoutiques->count()); ?> boutiques)</small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    
                    <td class="text-center">
                        <?php
                            $totalStock = 0;
                            if(isset($magasin)) {
                                $totalStock = $produit->stockMagasins->where('magasin_id', $magasin->id)->sum('quantite');
                            } elseif(isset($boutique)) {
                                $totalStock = $produit->stockBoutiques->where('boutique_id', $boutique->id)->sum('quantite');
                            } else {
                                $totalStock = $produit->stockMagasins->sum('quantite') + $produit->stockBoutiques->sum('quantite');
                            }
                            
                            $enRupture = false;
                            if(isset($magasin)) {
                                $stockMagasin = $produit->stockMagasins->where('magasin_id', $magasin->id)->first();
                                $enRupture = $stockMagasin && $stockMagasin->quantite <= $stockMagasin->seuil_alerte;
                            } elseif(isset($boutique)) {
                                $stockBoutique = $produit->stockBoutiques->where('boutique_id', $boutique->id)->first();
                                $enRupture = $stockBoutique && $stockBoutique->quantite <= $stockBoutique->seuil_alerte;
                            } else {
                                $enRupture = $totalStock == 0;
                            }
                        ?>
                        
                        <?php if($totalStock == 0): ?>
                            <span class="badge-danger">RUPTURE</span>
                        <?php elseif($enRupture): ?>
                            <span class="badge-warning">ALERTE</span>
                        <?php else: ?>
                            <span class="badge-success">OK</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center">Aucun produit trouvé</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if(!isset($magasin) && !isset($boutique)): ?>
        <!-- Détail par magasin -->
        <h3>Détail par Magasin</h3>
        <?php $__currentLoopData = $produits->pluck('stockMagasins')->flatten()->groupBy('magasin_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $magasinId => $stocks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $magasin = \App\Models\Magasin::find($magasinId);
            ?>
            <?php if($magasin): ?>
                <h4><?php echo e($magasin->nom); ?></h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-center">Seuil Alerte</th>
                            <th class="text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $stocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($stock->produit->nom); ?></td>
                                <td class="text-center"><?php echo e($stock->quantite); ?></td>
                                <td class="text-center"><?php echo e($stock->seuil_alerte); ?></td>
                                <td class="text-center">
                                    <?php if($stock->quantite == 0): ?>
                                        <span class="badge-danger">RUPTURE</span>
                                    <?php elseif($stock->quantite <= $stock->seuil_alerte): ?>
                                        <span class="badge-warning">ALERTE</span>
                                    <?php else: ?>
                                        <span class="badge-success">OK</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <!-- Détail par boutique -->
        <div class="page-break"></div>
        <h3>Détail par Boutique</h3>
        <?php $__currentLoopData = $produits->pluck('stockBoutiques')->flatten()->groupBy('boutique_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $boutiqueId => $stocks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $boutique = \App\Models\Boutique::find($boutiqueId);
            ?>
            <?php if($boutique): ?>
                <h4><?php echo e($boutique->nom); ?> (<?php echo e($boutique->magasin->nom); ?>)</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-center">Seuil Alerte</th>
                            <th class="text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $stocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($stock->produit->nom); ?></td>
                                <td class="text-center"><?php echo e($stock->quantite); ?></td>
                                <td class="text-center"><?php echo e($stock->seuil_alerte); ?></td>
                                <td class="text-center">
                                    <?php if($stock->quantite == 0): ?>
                                        <span class="badge-danger">RUPTURE</span>
                                    <?php elseif($stock->quantite <= $stock->seuil_alerte): ?>
                                        <span class="badge-warning">ALERTE</span>
                                    <?php else: ?>
                                        <span class="badge-success">OK</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <div class="footer">
        <p>Rapport généré automatiquement par le système de gestion de stock</p>
        <p><?php echo e($dateGeneration); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/rapports/stock_pdf.blade.php ENDPATH**/ ?>