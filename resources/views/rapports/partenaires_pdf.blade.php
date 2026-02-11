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
        <p>Généré le : {{ $dateGeneration }}</p>
        <p>Par : {{ $user->name }} ({{ $user->role }})</p>
        @if(isset($magasin))
            <p>Magasin : {{ $magasin->nom }}</p>
        @endif
    </div>

    <div class="info-box">
        <strong>Résumé :</strong> {{ $partenaires->count() }} partenaires actifs
        <br><strong>Total des achats :</strong> {{ number_format($partenaires->sum(function($p) { return $p->entreesStock->sum('prix_total'); }), 0, ',', ' ') }} FCFA
        <br><strong>Nombre total d'entrées :</strong> {{ $partenaires->sum(function($p) { return $p->entreesStock->count(); }) }}
    </div>

    @forelse($partenaires as $partenaire)
        <div class="partner-box">
            <h3>{{ $partenaire->nom }}</h3>
            
            @if($partenaire->contact || $partenaire->telephone || $partenaire->email)
                <div class="contact-info">
                    @if($partenaire->contact)<strong>Contact :</strong> {{ $partenaire->contact }}<br>@endif
                    @if($partenaire->telephone)<strong>Téléphone :</strong> {{ $partenaire->telephone }}<br>@endif
                    @if($partenaire->email)<strong>Email :</strong> {{ $partenaire->email }}@endif
                </div>
            @endif

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
                    @forelse($partenaire->entreesStock as $entree)
                        <tr>
                            <td>{{ $entree->date->format('d/m/Y') }}</td>
                            <td>{{ $entree->produit->nom }}</td>
                            <td>{{ $entree->produit->categorie }}</td>
                            <td class="text-center">{{ $entree->quantite }}</td>
                            <td class="text-right">{{ number_format($entree->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                            <td class="text-right">{{ number_format($entree->prix_total, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $entree->magasin->nom }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Aucun achat trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($partenaire->entreesStock->count() > 0)
                    <tfoot>
                        <tr class="summary-row">
                            <td colspan="3" class="text-right"><strong>TOTAUX</strong></td>
                            <td class="text-center"><strong>{{ $partenaire->entreesStock->sum('quantite') }}</strong></td>
                            <td class="text-right">-</td>
                            <td class="text-right"><strong>{{ number_format($partenaire->entreesStock->sum('prix_total'), 0, ',', ' ') }} FCFA</strong></td>
                            <td>-</td>
                        </tr>
                    </tfoot>
                @endif
            </table>

            <!-- Produits achetés (résumé) -->
            @if($partenaire->entreesStock->count() > 0)
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
                        @php
                            $produitsAchetes = $partenaire->entreesStock->groupBy('produit_id');
                        @endphp
                        @foreach($produitsAchetes as $produitId => $entrees)
                            @php
                                $produit = $entrees->first()->produit;
                                $quantiteTotale = $entrees->sum('quantite');
                                $montantTotal = $entrees->sum('prix_total');
                            @endphp
                            <tr>
                                <td>{{ $produit->nom }}</td>
                                <td class="text-center">{{ $quantiteTotale }}</td>
                                <td class="text-right">{{ number_format($montantTotal, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @empty
        <div class="text-center">
            <p>Aucun partenaire trouvé</p>
        </div>
    @endforelse

    <!-- Résumé général -->
    @if($partenaires->count() > 0)
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
                    @foreach($partenaires->sortByDesc(function($p) { return $p->entreesStock->sum('prix_total'); }) as $partenaire)
                        <tr>
                            <td><strong>{{ $partenaire->nom }}</strong></td>
                            <td class="text-center">{{ $partenaire->entreesStock->count() }}</td>
                            <td class="text-right">{{ number_format($partenaire->entreesStock->sum('prix_total'), 0, ',', ' ') }} FCFA</td>
                            <td class="text-right">
                                @if($partenaire->entreesStock->count() > 0)
                                    {{ number_format($partenaire->entreesStock->sum('prix_total') / $partenaire->entreesStock->count(), 0, ',', ' ') }} FCFA
                                @else
                                    0 FCFA
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="summary-row">
                        <td><strong>TOTAUX</strong></td>
                        <td class="text-center"><strong>{{ $partenaires->sum(function($p) { return $p->entreesStock->count(); }) }}</strong></td>
                        <td class="text-right"><strong>{{ number_format($partenaires->sum(function($p) { return $p->entreesStock->sum('prix_total'); }), 0, ',', ' ') }} FCFA</strong></td>
                        <td class="text-right">
                            @if($partenaires->sum(function($p) { return $p->entreesStock->count(); }) > 0)
                                <strong>{{ number_format($partenaires->sum(function($p) { return $p->entreesStock->sum('prix_total'); }) / $partenaires->sum(function($p) { return $p->entreesStock->count(); }), 0, ',', ' ') }} FCFA</strong>
                            @else
                                <strong>0 FCFA</strong>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Rapport généré automatiquement par le système de gestion de stock</p>
        <p>{{ $dateGeneration }}</p>
    </div>
</body>
</html>
