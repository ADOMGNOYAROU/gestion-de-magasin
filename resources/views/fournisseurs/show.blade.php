@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">{{ __('messages.details_fournisseur') }}</h1>
                <div>
                    <a href="{{ route('fournisseurs.edit', $fournisseur) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> {{ __('messages.modifier') }}
                    </a>
                    <a href="{{ route('fournisseurs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.retour') }}
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.informations') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>{{ __('messages.nom') }} :</strong> {{ $fournisseur->nom }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>{{ __('messages.contact') }} :</strong> {{ $fournisseur->contact_personne }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>{{ __('messages.adresse') }} :</strong> {{ $fournisseur->adresse }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>{{ __('messages.telephone') }} :</strong> {{ $fournisseur->telephone }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>{{ __('messages.email') }} :</strong> {{ $fournisseur->email }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des achats -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('messages.historique_achats') }}</h6>
                </div>
                <div class="card-body">
                    @php
                        $orders = $fournisseur->orders()->with('orderItems.produit')->orderBy('created_at', 'desc')->get();
                        $totalOrders = $orders->count();
                        $totalAmount = $orders->sum('montant_total');
                        $totalProducts = $orders->sum(function($order) {
                            return $order->orderItems->sum('quantite');
                        });
                    @endphp

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $totalOrders }}</h4>
                                    <small>Commandes</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>{{ number_format($totalAmount, 0) }} FCFA</h4>
                                    <small>Montant Total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $totalProducts }}</h4>
                                    <small>Produits commandés</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>N° Commande</th>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Status</th>
                                        <th>Produits</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->numero_commande }}</td>
                                        <td>{{ $order->date_commande->format('d/m/Y') }}</td>
                                        <td>{{ number_format($order->montant_total, 0) }} FCFA</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status === 'en_cours' ? 'warning' : ($order->status === 'livree' ? 'success' : 'secondary') }}">
                                                {{ $order->status === 'en_cours' ? 'En cours' : ($order->status === 'livree' ? 'Livrée' : 'Annulée') }}
                                            </span>
                                        </td>
                                        <td>{{ $order->orderItems->count() }} produits</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Aucune commande trouvée pour ce fournisseur.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
