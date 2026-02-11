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
        <p>Période : {{ $periode['debut'] }} au {{ $periode['fin'] }}</p>
        <p>Généré le : {{ $dateGeneration }}</p>
        <p>Par : {{ $user->name }} ({{ $user->role }})</p>
    </div>

    <div class="summary-box">
        <h3>RÉSUMÉ GÉNÉRAL</h3>
        <table class="table">
            <tr>
                <td><strong>Total des ventes</strong></td>
                <td class="text-right">{{ $totalVentes }}</td>
            </tr>
            <tr>
                <td><strong>Chiffre d'affaires total</strong></td>
                <td class="text-right">{{ number_format($totalCA, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td><strong>Bénéfice total</strong></td>
                <td class="text-right">{{ number_format($totalBenefice, 0, ',', ' ') }} FCFA</td>
            </tr>
            @if($totalCA > 0)
            <tr>
                <td><strong>Marge bénéficiaire</strong></td>
                <td class="text-right">{{ round(($totalBenefice / $totalCA) * 100, 1) }}%</td>
            </tr>
            @endif
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
            @forelse($ventesParBoutique as $boutiqueData)
                <tr>
                    <td><strong>{{ $boutiqueData['boutique']->nom }}</strong></td>
                    <td>{{ $boutiqueData['boutique']->magasin->nom }}</td>
                    <td class="text-center">{{ $boutiqueData['ventes'] }}</td>
                    <td class="text-right">{{ number_format($boutiqueData['ca'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($boutiqueData['benefice'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">
                        @if($boutiqueData['ca'] > 0)
                            {{ round(($boutiqueData['benefice'] / $boutiqueData['ca']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Aucune vente trouvée</td>
                </tr>
            @endforelse
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
            @forelse($ventesParProduit as $produitData)
                <tr>
                    <td><strong>{{ $produitData['produit']->nom }}</strong></td>
                    <td>{{ $produitData['produit']->categorie }}</td>
                    <td class="text-center">{{ $produitData['quantite'] }}</td>
                    <td class="text-right">{{ number_format($produitData['ca'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($produitData['benefice'], 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">
                        @if($produitData['ca'] > 0)
                            {{ round(($produitData['benefice'] / $produitData['ca']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Aucune vente trouvée</td>
                </tr>
            @endforelse
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
            @forelse($venteProduits as $vp)
                <tr>
                    <td>{{ $vp->vente->date_vente->format('d/m/Y') }}</td>
                    <td>{{ $vp->produit->nom }}</td>
                    <td>{{ $vp->produit->categorie }}</td>
                    <td>{{ $vp->vente->boutique->nom }}</td>
                    <td>{{ $vp->vente->boutique->magasin->nom }}</td>
                    <td class="text-center">{{ $vp->quantite }}</td>
                    <td class="text-right">{{ number_format($vp->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($vp->sous_total, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format(($vp->prix_unitaire - $vp->produit->prix_achat) * $vp->quantite, 0, ',', ' ') }} FCFA</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Aucune vente trouvée</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($ventes->count() > 0)
        <!-- Totaux du détail -->
        <table class="table">
            <tfoot>
                <tr style="background-color: #f2f2f2; font-weight: bold;">
                    <td colspan="6" class="text-right"><strong>TOTAUX</strong></td>
                    <td class="text-right">{{ number_format($ventes->sum('prix_unitaire') * $ventes->sum('quantite') / $ventes->count(), 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($totalCA, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($totalBenefice, 0, ',', ' ') }} FCFA</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="footer">
        <p>Rapport généré automatiquement par le système de gestion de stock</p>
        <p>{{ $dateGeneration }}</p>
    </div>
</body>
</html>
