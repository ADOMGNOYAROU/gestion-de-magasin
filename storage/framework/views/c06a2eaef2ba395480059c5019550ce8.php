<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Ventes</title>
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
        .summary-box {
            background-color: #e8f5e8;
            border: 1px solid #28a745;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-box h3 {
            color: #28a745;
            margin-top: 0;
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
        .section-title {
            background-color: #007bff;
            color: white;
            padding: 10px;
            margin: 20px 0 10px 0;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT DE VENTES</h1>
        <p>Période : <?php echo e($periode['debut']); ?> au <?php echo e($periode['fin']); ?></p>
        <p>Généré le : <?php echo e($dateGeneration); ?></p>
        <p>Par : <?php echo e($user->name); ?> (<?php echo e($user->role); ?>)</p>
    </div>

    <div class="summary-box">
        <h3>RÉSUMÉ GÉNÉRAL</h3>
        <table class="table">
            <tr>
                <td><strong>Total des ventes</strong></td>
                <td class="text-right"><?php echo e($totalVentes); ?></td>
            </tr>
            <tr>
                <td><strong>Chiffre d'affaires total</strong></td>
                <td class="text-right"><?php echo e(number_format($totalCA, 0, ',', ' ')); ?> FCFA</td>
            </tr>
            <tr>
                <td><strong>Bénéfice total</strong></td>
                <td class="text-right"><?php echo e(number_format($totalBenefice, 0, ',', ' ')); ?> FCFA</td>
            </tr>
            <?php if($totalCA > 0): ?>
            <tr>
                <td><strong>Marge bénéficiaire</strong></td>
                <td class="text-right"><?php echo e(round(($totalBenefice / $totalCA) * 100, 1)); ?>%</td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="section-title">VENTES PAR BOUTIQUE</div>
    <table class="table">
        <thead>
            <tr>
                <th>Boutique</th>
                <th>Magasin</th>
                <th class="text-center">Nombre de ventes</th>
                <th class="text-right">Chiffre d'affaires</th>
                <th class="text-right">Bénéfice</th>
                <th class="text-right">Marge</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $ventesParBoutique; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $boutiqueData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($boutiqueData['boutique']->nom); ?></strong></td>
                    <td><?php echo e($boutiqueData['boutique']->magasin->nom); ?></td>
                    <td class="text-center"><?php echo e($boutiqueData['ventes']); ?></td>
                    <td class="text-right"><?php echo e(number_format($boutiqueData['ca'], 0, ',', ' ')); ?> FCFA</td>
                    <td class="text-right"><?php echo e(number_format($boutiqueData['benefice'], 0, ',', ' ')); ?> FCFA</td>
                    <td class="text-right">
                        <?php if($boutiqueData['ca'] > 0): ?>
                            <?php echo e(round(($boutiqueData['benefice'] / $boutiqueData['ca']) * 100, 1)); ?>%
                        <?php else: ?>
                            0%
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center">Aucune vente trouvée</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="section-title">VENTES PAR PRODUIT</div>
    <table class="table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Catégorie</th>
                <th class="text-center">Quantité vendue</th>
                <th class="text-right">Chiffre d'affaires</th>
                <th class="text-right">Bénéfice</th>
                <th class="text-right">Marge</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $ventesParProduit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produitData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <?php if($produitData['produit']): ?>
                            <strong><?php echo e($produitData['produit']->nom); ?></strong>
                        <?php else: ?>
                            <strong class="text-danger">Produit inconnu</strong>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($produitData['produit'] && $produitData['produit']->categorie): ?>
                            <?php echo e($produitData['produit']->categorie); ?>

                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo e($produitData['quantite']); ?></td>
                    <td class="text-right"><?php echo e(number_format($produitData['ca'], 0, ',', ' ')); ?> FCFA</td>
                    <td class="text-right"><?php echo e(number_format($produitData['benefice'], 0, ',', ' ')); ?> FCFA</td>
                    <td class="text-right">
                        <?php if($produitData['ca'] > 0): ?>
                            <?php echo e(round(($produitData['benefice'] / $produitData['ca']) * 100, 1)); ?>%
                        <?php else: ?>
                            0%
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center">Aucune vente trouvée</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="section-title">DÉTAIL DES VENTES</div>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Produit</th>
                <th>Catégorie</th>
                <th>Boutique</th>
                <th>Magasin</th>
                <th class="text-center">Quantité</th>
                <th class="text-right">Prix unitaire</th>
                <th class="text-right">Total</th>
                <th class="text-right">Bénéfice</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $venteProduits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($vp->vente->date_vente->format('d/m/Y')); ?></td>
                    <td>
                        <?php if($vp->produit): ?>
                            <?php echo e($vp->produit->nom); ?>

                        <?php else: ?>
                            Produit inconnu
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($vp->produit && $vp->produit->categorie): ?>
                            <?php echo e($vp->produit->categorie); ?>

                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($vp->vente->boutique): ?>
                            <?php echo e($vp->vente->boutique->nom); ?>

                        <?php else: ?>
                            Boutique inconnue
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($vp->vente->boutique && $vp->vente->boutique->magasin): ?>
                            <?php echo e($vp->vente->boutique->magasin->nom); ?>

                        <?php else: ?>
                            Magasin inconnu
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo e($vp->quantite); ?></td>
                    <td class="text-right"><?php echo e(number_format($vp->prix_unitaire, 0, ',', ' ')); ?> FCFA</td>
                    <td class="text-right"><?php echo e(number_format($vp->sous_total, 0, ',', ' ')); ?> FCFA</td>
                    <td class="text-right">
                        <?php
                            $prixAchat = $vp->produit ? ($vp->produit->prix_achat ?? 0) : 0;
                            $benefice = ($vp->prix_unitaire - $prixAchat) * $vp->quantite;
                        ?>
                        <?php echo e(number_format($benefice, 0, ',', ' ')); ?> FCFA
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" class="text-center">Aucune vente trouvée</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($ventes->count() > 0): ?>
        <!-- Totaux du détail -->
        <table class="table">
            <tfoot>
                <tr style="background-color: #f2f2f2; font-weight: bold;">
                    <td colspan="6" class="text-right"><strong>TOTAUX</strong></td>
                    <td class="text-right"><?php echo e(number_format($ventes->sum('prix_unitaire') * $ventes->sum('quantite') / $ventes->count(), 0, ',', ' ')); ?> FCFA</td>
                    <td class="text-right"><?php echo e(number_format($totalCA, 0, ',', ' ')); ?> FCFA</td>
                    <td class="text-right"><?php echo e(number_format($totalBenefice, 0, ',', ' ')); ?> FCFA</td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <div class="footer">
        <p>Rapport généré automatiquement par le système de gestion de stock</p>
        <p><?php echo e($dateGeneration); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/rapports/ventes_pdf.blade.php ENDPATH**/ ?>