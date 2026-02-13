<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport des Partenaires</title>
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
        .partner-box {
            background-color: #e8f4fd;
            border: 1px solid #007bff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .partner-box h3 {
            color: #007bff;
            margin-top: 0;
            border-bottom: 1px solid #007bff;
            padding-bottom: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
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
        .summary-row {
            background-color: #e8f5e8 !important;
            font-weight: bold;
        }
        .contact-info {
            background-color: #fff3cd;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 10px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT DES PARTENAIRES</h1>
        <p>Généré le : <?php echo e($dateGeneration); ?></p>
        <p>Par : <?php echo e($user->name); ?> (<?php echo e($user->role); ?>)</p>
        <?php if(isset($magasin)): ?>
            <p>Magasin : <?php echo e($magasin->nom); ?></p>
        <?php endif; ?>
    </div>

    <div class="info-box">
        <strong>Résumé :</strong> <?php echo e($partenaires->count()); ?> partenaires actifs
        <br><strong>Total des achats :</strong> <?php echo e(number_format($partenaires->sum(function($p) { return $p->entreesStock->sum('prix_total'); }), 0, ',', ' ')); ?> FCFA
        <br><strong>Nombre total d'entrées :</strong> <?php echo e($partenaires->sum(function($p) { return $p->entreesStock->count(); })); ?>

    </div>

    <?php $__empty_1 = true; $__currentLoopData = $partenaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partenaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="partner-box">
            <h3><?php echo e($partenaire->nom); ?></h3>
            
            <?php if($partenaire->contact || $partenaire->telephone || $partenaire->email): ?>
                <div class="contact-info">
                    <?php if($partenaire->contact): ?><strong>Contact :</strong> <?php echo e($partenaire->contact); ?><br><?php endif; ?>
                    <?php if($partenaire->telephone): ?><strong>Téléphone :</strong> <?php echo e($partenaire->telephone); ?><br><?php endif; ?>
                    <?php if($partenaire->email): ?><strong>Email :</strong> <?php echo e($partenaire->email); ?><?php endif; ?>
                </div>
            <?php endif; ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th class="text-center">Quantité</th>
                        <th class="text-right">Prix unitaire</th>
                        <th class="text-right">Total</th>
                        <th>Magasin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_2 = true; $__currentLoopData = $partenaire->entreesStock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entree): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                        <tr>
                            <td><?php echo e($entree->date->format('d/m/Y')); ?></td>
                            <td><?php echo e($entree->produit->nom); ?></td>
                            <td><?php echo e($entree->produit->categorie); ?></td>
                            <td class="text-center"><?php echo e($entree->quantite); ?></td>
                            <td class="text-right"><?php echo e(number_format($entree->prix_unitaire, 0, ',', ' ')); ?> FCFA</td>
                            <td class="text-right"><?php echo e(number_format($entree->prix_total, 0, ',', ' ')); ?> FCFA</td>
                            <td><?php echo e($entree->magasin->nom); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                        <tr>
                            <td colspan="7" class="text-center">Aucun achat trouvé</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if($partenaire->entreesStock->count() > 0): ?>
                    <tfoot>
                        <tr class="summary-row">
                            <td colspan="3" class="text-right"><strong>TOTAUX</strong></td>
                            <td class="text-center"><strong><?php echo e($partenaire->entreesStock->sum('quantite')); ?></strong></td>
                            <td class="text-right">-</td>
                            <td class="text-right"><strong><?php echo e(number_format($partenaire->entreesStock->sum('prix_total'), 0, ',', ' ')); ?> FCFA</strong></td>
                            <td>-</td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>

            <!-- Produits achetés (résumé) -->
            <?php if($partenaire->entreesStock->count() > 0): ?>
                <h4 style="margin-top: 15px; color: #007bff; font-size: 14px;">Résumé par produit</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th class="text-center">Quantité totale</th>
                            <th class="text-right">Montant total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $produitsAchetes = $partenaire->entreesStock->groupBy('produit_id');
                        ?>
                        <?php $__currentLoopData = $produitsAchetes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produitId => $entrees): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $produit = $entrees->first()->produit;
                                $quantiteTotale = $entrees->sum('quantite');
                                $montantTotal = $entrees->sum('prix_total');
                            ?>
                            <tr>
                                <td><?php echo e($produit->nom); ?></td>
                                <td class="text-center"><?php echo e($quantiteTotale); ?></td>
                                <td class="text-right"><?php echo e(number_format($montantTotal, 0, ',', ' ')); ?> FCFA</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center">
            <p>Aucun partenaire trouvé</p>
        </div>
    <?php endif; ?>

    <!-- Résumé général -->
    <?php if($partenaires->count() > 0): ?>
        <div class="page-break"></div>
        <div class="partner-box">
            <h3>RÉSUMÉ GÉNÉRAL</h3>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Partenaire</th>
                        <th class="text-center">Nombre d'achats</th>
                        <th class="text-right">Total dépensé</th>
                        <th class="text-right">Moyenne par achat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $partenaires->sortByDesc(function($p) { return $p->entreesStock->sum('prix_total'); }); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partenaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><strong><?php echo e($partenaire->nom); ?></strong></td>
                            <td class="text-center"><?php echo e($partenaire->entreesStock->count()); ?></td>
                            <td class="text-right"><?php echo e(number_format($partenaire->entreesStock->sum('prix_total'), 0, ',', ' ')); ?> FCFA</td>
                            <td class="text-right">
                                <?php if($partenaire->entreesStock->count() > 0): ?>
                                    <?php echo e(number_format($partenaire->entreesStock->sum('prix_total') / $partenaire->entreesStock->count(), 0, ',', ' ')); ?> FCFA
                                <?php else: ?>
                                    0 FCFA
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr class="summary-row">
                        <td><strong>TOTAUX</strong></td>
                        <td class="text-center"><strong><?php echo e($partenaires->sum(function($p) { return $p->entreesStock->count(); })); ?></strong></td>
                        <td class="text-right"><strong><?php echo e(number_format($partenaires->sum(function($p) { return $p->entreesStock->sum('prix_total'); }), 0, ',', ' ')); ?> FCFA</strong></td>
                        <td class="text-right">
                            <?php if($partenaires->sum(function($p) { return $p->entreesStock->count(); }) > 0): ?>
                                <strong><?php echo e(number_format($partenaires->sum(function($p) { return $p->entreesStock->sum('prix_total'); }) / $partenaires->sum(function($p) { return $p->entreesStock->count(); }), 0, ',', ' ')); ?> FCFA</strong>
                            <?php else: ?>
                                <strong>0 FCFA</strong>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>Rapport généré automatiquement par le système de gestion de stock</p>
        <p><?php echo e($dateGeneration); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/rapports/partenaires_pdf.blade.php ENDPATH**/ ?>