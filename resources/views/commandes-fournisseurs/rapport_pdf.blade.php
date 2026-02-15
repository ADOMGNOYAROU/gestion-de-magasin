<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Commande Fournisseur - {{ $commande->numero_commande }}</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT DE COMMANDE FOURNISSEUR</h1>
        <p>N° Commande : {{ $commande->numero_commande }}</p>
        <p>Généré le : {{ $dateGeneration }}</p>
    </div>

    <div class="info-box">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Fournisseur:</strong></td>
                        <td>{{ $commande->fournisseur->nom }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date commande:</strong></td>
                        <td>{{ $commande->date_commande ? $commande->date_commande->format('d/m/Y') : 'Non définie' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Statut:</strong></td>
                        <td>{!! $commande->status_badge !!}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Créée par:</strong></td>
                        <td>{{ $commande->user->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Magasin:</strong></td>
                        <td>{{ $commande->magasin ? $commande->magasin->nom : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date livraison prévue:</strong></td>
                        <td>{{ $commande->date_livraison_prevue ? $commande->date_livraison_prevue->format('d/m/Y') : 'Non définie' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date livraison réelle:</strong></td>
                        <td>{{ $commande->date_livraison_reelle ? $commande->date_livraison_reelle->format('d/m/Y') : 'Non livrée' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($commande->notes)
            <div class="mt-3">
                <strong>Notes:</strong>
                <div class="mt-1 p-2 bg-light rounded">{{ $commande->notes }}</div>
            </div>
        @endif
    </div>

    <div class="summary-box">
        <h3>TOTAUX</h3>
        <table class="table">
            <tr>
                <td><strong>Total HT</strong></td>
                <td class="text-right">{{ number_format($commande->total_ht, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td><strong>TVA</strong></td>
                <td class="text-right">{{ number_format($commande->tva, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td><strong>Total TTC</strong></td>
                <td class="text-right">{{ number_format($commande->total_ttc, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <h3>PRODUITS COMMANDÉS</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Produit</th>
                <th class="text-center">Quantité</th>
                <th class="text-center">Prix HT</th>
                <th class="text-center">TVA</th>
                <th class="text-right">Sous-total HT</th>
                <th class="text-right">Sous-total TTC</th>
                <th class="text-center">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->lignes as $ligne)
                <tr>
                    <td>
                        <div>
                            <strong>{{ $ligne->produit->nom }}</strong><br>
                            <small class="text-muted">{{ $ligne->produit->categorie }}</small>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary">{{ $ligne->quantite_commandee }}</span>
                        @if($ligne->quantite_livree > 0)
                            <br><small class="text-success">Reçu: {{ $ligne->quantite_livree }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($ligne->prix_unitaire_ht, 0, ',', ' ') }} FCFA</td>
                    <td class="text-center">{{ $ligne->tva_taux }}%</td>
                    <td class="text-right">{{ number_format($ligne->sous_total_ht, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($ligne->sous_total_ttc, 0, ',', ' ') }} FCFA</td>
                    <td class="text-center">
                        @if($ligne->estComplete())
                            <span class="badge bg-success">Complet</span>
                        @elseif($ligne->quantite_livree > 0)
                            <span class="badge bg-warning">Partiel</span>
                        @else
                            <span class="badge bg-secondary">En attente</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-dark">
            <tr>
                <th colspan="4" class="text-right">TOTAL</th>
                <th class="text-right">{{ number_format($commande->total_ht, 0, ',', ' ') }} FCFA</th>
                <th class="text-right">{{ number_format($commande->total_ttc, 0, ',', ' ') }} FCFA</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Rapport généré automatiquement par le système de gestion de stock</p>
        <p>{{ $dateGeneration }}</p>
    </div>
</body>
</html>
