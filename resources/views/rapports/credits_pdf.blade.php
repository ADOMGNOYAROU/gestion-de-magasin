<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Crédits</title>
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
            background-color: #cce7ff;
            border: 1px solid #007bff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-box h3 {
            color: #007bff;
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
        <h1>RAPPORT DE CRÉDITS</h1>
        <p>Filtres appliqués : @if($filters['status']) Statut: {{ $filters['status'] == 'unpaid' ? 'Non payés' : 'En retard' }} @endif @if($filters['client_id']) Client: {{ $credits->firstWhere('client_id', $filters['client_id'])->client->nom ?? 'N/A' }} @endif @if($filters['date_debut'] || $filters['date_fin']) Période: {{ $filters['date_debut'] ?? 'Début' }} au {{ $filters['date_fin'] ?? 'Fin' }} @endif</p>
        <p>Généré le : {{ $dateGeneration }}</p>
        <p>Par : {{ $user->name }} ({{ $user->role }})</p>
    </div>

    <div class="summary-box">
        <h3>RÉSUMÉ GÉNÉRAL</h3>
        <table class="table">
            <tr>
                <td><strong>Total des crédits</strong></td>
                <td class="text-right">{{ $totalCredits }}</td>
            </tr>
            <tr>
                <td><strong>Montant total</strong></td>
                <td class="text-right">{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td><strong>Solde restant total</strong></td>
                <td class="text-right">{{ number_format($totalRemaining, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <div class="section-title">DÉTAIL DES CRÉDITS</div>
    <table class="table">
        <thead>
            <tr>
                <th>Client</th>
                <th>Boutique</th>
                <th>Magasin</th>
                <th class="text-right">Montant total</th>
                <th class="text-right">Solde restant</th>
                <th>Date d'échéance</th>
                <th>Statut</th>
                <th>Date de création</th>
            </tr>
        </thead>
        <tbody>
            @forelse($credits as $credit)
                <tr>
                    <td>{{ $credit->client->nom }}</td>
                    <td>{{ $credit->vente->boutique->nom }}</td>
                    <td>{{ $credit->vente->boutique->magasin->nom }}</td>
                    <td class="text-right">{{ number_format($credit->total_amount, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($credit->remaining_balance, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $credit->due_date ? $credit->due_date->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $credit->status == 'active' ? 'Actif' : 'Payé' }}</td>
                    <td>{{ $credit->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Aucun crédit trouvé</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Rapport généré automatiquement par le système de gestion de magasin</p>
        <p>{{ $dateGeneration }}</p>
    </div>
</body>
</html>
