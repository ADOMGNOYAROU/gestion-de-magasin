@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails de la Commande - {{ $order->numero_commande }}</h1>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    @if($order->status === 'en_cours')
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <form action="{{ route('orders.livrer', $order) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-success" onclick="return confirm('Confirmer la livraison de cette commande?')">
                                <i class="fas fa-check"></i> Marquer comme livrée
                            </button>
                        </form>
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Annuler cette commande?')">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- Informations de la commande -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Informations de la Commande</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>N° Commande :</strong></td>
                                    <td>{{ $order->numero_commande }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fournisseur :</strong></td>
                                    <td>{{ $order->fournisseur->nom }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date de commande :</strong></td>
                                    <td>{{ $order->date_commande->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date de livraison prévue :</strong></td>
                                    <td>{{ $order->date_livraison_prevue ? $order->date_livraison_prevue->format('d/m/Y') : 'Non spécifiée' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Montant Total :</strong></td>
                                    <td><strong>{{ number_format($order->montant_total, 0) }} FCFA</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Status :</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'en_cours' ? 'warning' : ($order->status === 'livree' ? 'success' : 'secondary') }}">
                                            {{ $order->status === 'en_cours' ? 'En cours' : ($order->status === 'livree' ? 'Livrée' : 'Annulée') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Créé par :</strong></td>
                                    <td>{{ $order->user->name }}</td>
                                </tr>
                                @if($order->notes)
                                <tr>
                                    <td><strong>Notes :</strong></td>
                                    <td>{{ $order->notes }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Produits commandés -->
                    <div class="card mt-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Produits Commandés ({{ $order->orderItems->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if($order->orderItems->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Produit</th>
                                                <th>Quantité</th>
                                                <th>Prix Unitaire</th>
                                                <th>Sous-total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->orderItems as $item)
                                            <tr>
                                                <td>{{ $item->produit->nom }}</td>
                                                <td>{{ $item->quantite }}</td>
                                                <td>{{ number_format($item->prix_unitaire, 0) }} FCFA</td>
                                                <td>{{ number_format($item->sous_total, 0) }} FCFA</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-end">Total :</th>
                                                <th>{{ number_format($order->montant_total, 0) }} FCFA</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">Aucun produit dans cette commande.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions et résumé -->
                <div class="col-md-4">
                    <!-- Résumé -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Résumé</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Produits :</strong></td>
                                    <td>{{ $order->orderItems->sum('quantite') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Montant :</strong></td>
                                    <td>{{ number_format($order->montant_total, 0) }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Status :</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'en_cours' ? 'warning' : ($order->status === 'livree' ? 'success' : 'secondary') }}">
                                            {{ $order->status === 'en_cours' ? 'En cours' : ($order->status === 'livree' ? 'Livrée' : 'Annulée') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Informations système -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Informations Système</h6>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <strong>Créé le :</strong><br>
                                {{ $order->created_at->format('d/m/Y H:i:s') }}<br><br>
                                <strong>Modifié le :</strong><br>
                                {{ $order->updated_at->format('d/m/Y H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
