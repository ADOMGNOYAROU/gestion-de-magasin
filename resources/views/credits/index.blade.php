@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestion des Crédits</h1>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Liste des Crédits</h5>
                </div>
                <div class="card-body">
                    @if($credits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Montant Total</th>
                                        <th>Solde Restant</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($credits as $credit)
                                    <tr>
                                        <td>
                                            <strong>{{ $credit->client->nom }} {{ $credit->client->prenom ?: '' }}</strong>
                                            <br><small class="text-muted">{{ $credit->vente->numero_ticket }}</small>
                                        </td>
                                        <td>{{ number_format($credit->total_amount, 0) }} FCFA</td>
                                        <td>{{ number_format($credit->remaining_balance, 0) }} FCFA</td>
                                        <td>
                                            <span class="badge bg-{{ $credit->status === 'active' ? 'warning' : 'success' }}">
                                                {{ $credit->status === 'active' ? 'Actif' : 'Payé' }}
                                            </span>
                                        </td>
                                        <td>{{ $credit->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('credits.show', $credit) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $credits->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5>Aucun crédit trouvé</h5>
                            <p class="text-muted">Les crédits apparaîtront ici lorsqu'ils seront créés lors des ventes à crédit.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
