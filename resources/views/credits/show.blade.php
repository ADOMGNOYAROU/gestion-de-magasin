@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    Détails du Crédit - {{ $credit->client->nom }} {{ $credit->client->prenom ?: '' }}
                </h1>
                <div>
                    <a href="{{ route('credits.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Informations du Crédit</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Client :</strong></td>
                                    <td>{{ $credit->client->nom }} {{ $credit->client->prenom ?: '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Vente associée :</strong></td>
                                    <td>
                                        <a href="{{ route('ventes.show', $credit->vente) }}" class="text-decoration-none">
                                            #{{ $credit->vente->numero_ticket }}
                                        </a>
                                        <br><small class="text-muted">{{ $credit->vente->date_vente->format('d/m/Y H:i') }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Montant Total :</strong></td>
                                    <td>{{ number_format($credit->total_amount, 0) }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Solde Restant :</strong></td>
                                    <td>{{ number_format($credit->remaining_balance, 0) }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Status :</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $credit->status === 'active' ? 'warning' : 'success' }}">
                                            {{ $credit->status === 'active' ? 'Actif' : 'Payé' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Stock restant à crédit :</strong></td>
                                    <td>{{ $credit->remaining_stock }} unités</td>
                                </tr>
                                <tr>
                                    <td><strong>Date de création :</strong></td>
                                    <td>{{ $credit->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Paiements effectués -->
                    <div class="card mt-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Paiements Effectués ({{ $credit->creditPayments->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if($credit->creditPayments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Montant</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($credit->creditPayments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                                <td>{{ number_format($payment->amount, 0) }} FCFA</td>
                                                <td>{{ $payment->notes ?: '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">Aucun paiement enregistré pour ce crédit.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Actions -->
                    <div class="card">
                        <div class="card-body">
                            <h6>Actions</h6>
                            @if($credit->status === 'active')
                                <a href="{{ route('credits.add_payment', $credit) }}" class="btn btn-success btn-sm w-100 mb-2">
                                    <i class="fas fa-plus"></i> Ajouter un paiement
                                </a>
                            @endif
                            <a href="{{ route('ventes.recu', $credit->vente) }}" class="btn btn-outline-primary btn-sm w-100" target="_blank">
                                <i class="fas fa-receipt"></i> Voir le reçu
                            </a>
                        </div>
                    </div>

                    <!-- Résumé -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Résumé</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Total dû :</strong></td>
                                    <td>{{ number_format($credit->total_amount, 0) }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Total payé :</strong></td>
                                    <td>{{ number_format($credit->total_amount - $credit->remaining_balance, 0) }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Restant :</strong></td>
                                    <td><strong>{{ number_format($credit->remaining_balance, 0) }} FCFA</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
