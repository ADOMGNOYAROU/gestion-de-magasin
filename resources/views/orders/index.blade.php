@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Commandes Fournisseurs</h1>
                <a href="{{ route('orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Commande
                </a>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('orders.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Rechercher par numéro ou fournisseur..." value="{{ $search }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="en_cours" {{ $status === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="livree" {{ $status === 'livree' ? 'selected' : '' }}>Livrée</option>
                                <option value="annulee" {{ $status === 'annulee' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times"></i> Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des commandes -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Liste des Commandes ({{ $orders->total() }})</h6>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>N° Commande</th>
                                        <th>Fournisseur</th>
                                        <th>Date Commande</th>
                                        <th>Montant Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->numero_commande }}</td>
                                        <td>{{ $order->fournisseur->nom }}</td>
                                        <td>{{ $order->date_commande->format('d/m/Y') }}</td>
                                        <td>{{ number_format($order->montant_total, 0) }} FCFA</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status === 'en_cours' ? 'warning' : ($order->status === 'livree' ? 'success' : 'secondary') }}">
                                                {{ $order->status === 'en_cours' ? 'En cours' : ($order->status === 'livree' ? 'Livrée' : 'Annulée') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($order->status === 'en_cours')
                                                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('orders.livrer', $order) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Marquer comme livrée" onclick="return confirm('Confirmer la livraison de cette commande?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Annuler" onclick="return confirm('Annuler cette commande?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $orders->appends(request()->query())->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5>Aucune commande trouvée</h5>
                            <p class="text-muted">Commencez par créer une nouvelle commande fournisseur.</p>
                            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Créer une commande
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
