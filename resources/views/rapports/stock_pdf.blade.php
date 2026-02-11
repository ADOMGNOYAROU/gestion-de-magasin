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
        <p>Généré le : {{ $dateGeneration }}</p>
        <p>Par : {{ $user->name }} ({{ $user->role }})</p>
        @if(isset($magasin))
            <p>Magasin : {{ $magasin->nom }}</p>
        @elseif(isset($boutique))
            <p>Boutique : {{ $boutique->nom }}</p>
        @endif
    </div>

    <div class="info-box">
        <strong>Résumé :</strong> {{ $produits->count() }} produits actifs
        @if(isset($magasin))
            <br><strong>Stock total magasin :</strong> {{ $produits->sum(function($p) use ($magasin) { return $p->stockMagasins->where('magasin_id', $magasin->id)->sum('quantite'); }) }} unités
            <br><strong>Stock total boutiques :</strong> {{ $produits->sum(function($p) use ($magasin) { return $p->stockBoutiques->filter(function($sb) use ($magasin) { return $sb->boutique->magasin_id == $magasin->id; })->sum('quantite'); }) }} unités
        @elseif(isset($boutique))
            <br><strong>Stock boutique :</strong> {{ $produits->sum(function($p) use ($boutique) { return $p->stockBoutiques->where('boutique_id', $boutique->id)->sum('quantite'); }) }} unités
        @else
            <br><strong>Stock total magasins :</strong> {{ $produits->sum(function($p) { return $p->stockMagasins->sum('quantite'); }) }} unités
            <br><strong>Stock total boutiques :</strong> {{ $produits->sum(function($p) { return $p->stockBoutiques->sum('quantite'); }) }} unités
        @endif
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Catégorie</th>
                <th>Prix Vente</th>
                @if(!isset($boutique))
                    <th class="text-center">Stock Magasin</th>
                @endif
                @if(!isset($magasin))
                    <th class="text-center">Stock Boutiques</th>
                @endif
                <th class="text-center">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produits as $produit)
                <tr>
                    <td><strong>{{ $produit->nom }}</strong></td>
                    <td>{{ $produit->categorie }}</td>
                    <td class="text-right">{{ number_format($produit->prix_vente, 0, ',', ' ') }} FCFA</td>
                    
                    @if(!isset($boutique))
                        <td class="text-center">
                            @if(isset($magasin))
                                {{ $produit->stockMagasins->where('magasin_id', $magasin->id)->sum('quantite') }}
                            @else
                                {{ $produit->stockMagasins->sum('quantite') }}
                                @if($produit->stockMagasins->count() > 1)
                                    <small class="text-muted">({{ $produit->stockMagasins->count() }} magasins)</small>
                                @endif
                            @endif
                        </td>
                    @endif
                    
                    @if(!isset($magasin))
                        <td class="text-center">
                            @if(isset($boutique))
                                {{ $produit->stockBoutiques->where('boutique_id', $boutique->id)->sum('quantite') }}
                            @else
                                {{ $produit->stockBoutiques->sum('quantite') }}
                                @if($produit->stockBoutiques->count() > 1)
                                    <small class="text-muted">({{ $produit->stockBoutiques->count() }} boutiques)</small>
                                @endif
                            @endif
                        </td>
                    @endif
                    
                    <td class="text-center">
                        @php
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
                        @endphp
                        
                        @if($totalStock == 0)
                            <span class="badge-danger">RUPTURE</span>
                        @elseif($enRupture)
                            <span class="badge-warning">ALERTE</span>
                        @else
                            <span class="badge-success">OK</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Aucun produit trouvé</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!isset($magasin) && !isset($boutique))
        <!-- Détail par magasin -->
        <h3>Détail par Magasin</h3>
        @foreach($produits->pluck('stockMagasins')->flatten()->groupBy('magasin_id') as $magasinId => $stocks)
            @php
                $magasin = \App\Models\Magasin::find($magasinId);
            @endphp
            @if($magasin)
                <h4>{{ $magasin->nom }}</h4>
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
                        @foreach($stocks as $stock)
                            <tr>
                                <td>{{ $stock->produit->nom }}</td>
                                <td class="text-center">{{ $stock->quantite }}</td>
                                <td class="text-center">{{ $stock->seuil_alerte }}</td>
                                <td class="text-center">
                                    @if($stock->quantite == 0)
                                        <span class="badge-danger">RUPTURE</span>
                                    @elseif($stock->quantite <= $stock->seuil_alerte)
                                        <span class="badge-warning">ALERTE</span>
                                    @else
                                        <span class="badge-success">OK</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach

        <!-- Détail par boutique -->
        <div class="page-break"></div>
        <h3>Détail par Boutique</h3>
        @foreach($produits->pluck('stockBoutiques')->flatten()->groupBy('boutique_id') as $boutiqueId => $stocks)
            @php
                $boutique = \App\Models\Boutique::find($boutiqueId);
            @endphp
            @if($boutique)
                <h4>{{ $boutique->nom }} ({{ $boutique->magasin->nom }})</h4>
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
                        @foreach($stocks as $stock)
                            <tr>
                                <td>{{ $stock->produit->nom }}</td>
                                <td class="text-center">{{ $stock->quantite }}</td>
                                <td class="text-center">{{ $stock->seuil_alerte }}</td>
                                <td class="text-center">
                                    @if($stock->quantite == 0)
                                        <span class="badge-danger">RUPTURE</span>
                                    @elseif($stock->quantite <= $stock->seuil_alerte)
                                        <span class="badge-warning">ALERTE</span>
                                    @else
                                        <span class="badge-success">OK</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    @endif

    <div class="footer">
        <p>Rapport généré automatiquement par le système de gestion de stock</p>
        <p>{{ $dateGeneration }}</p>
    </div>
</body>
</html>
