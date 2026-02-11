@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails de la Vente</h1>
                <div>
                    <a href="{{ route('ventes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Informations de la Vente</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID Vente :</strong></td>
                                    <td>#{{ $vente->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date :</strong></td>
                                    <td>{{ $vente->date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Produit :</strong></td>
                                    <td>
                                        <strong>{{ $vente->produit->nom }}</strong>
                                        <br><small class="text-muted">{{ $vente->produit->categorie }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Boutique :</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $vente->boutique->nom }}</span>
                                        <br><small class="text-muted">{{ $vente->boutique->localisation }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Magasin :</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $vente->boutique->magasin->nom }}</span>
                                        <br><small class="text-muted">{{ $vente->boutique->magasin->localisation }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Quantité Vendue :</strong></td>
                                    <td>
                                        <span class="badge bg-warning fs-6">{{ $vente->quantite }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Détails Financiers</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Prix Unitaire :</strong></td>
                                    <td>{{ number_format($vente->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Prix d'Achat :</strong></td>
                                    <td>{{ number_format($vente->produit->prix_achat, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Marge Unitaire :</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $vente->prix_unitaire > $vente->produit->prix_achat ? 'success' : 'secondary' }}">
                                            {{ number_format($vente->prix_unitaire - $vente->produit->prix_achat, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Vente :</strong></td>
                                    <td>
                                        <strong class="text-primary">{{ number_format($vente->prix_total, 0, ',', ' ') }} FCFA</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Bénéfice Total :</strong></td>
                                    <td>
                                        <strong class="text-success">{{ number_format($vente->benefice, 0, ',', ' ') }} FCFA</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Marge % :</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $vente->prix_unitaire > 0 ? round(($vente->benefice / $vente->prix_total) * 100, 1) : 0 }}%
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0">Impact sur le Stock</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger py-2">
                                <i class="fas fa-minus-circle"></i>
                                <strong>Stock Boutique</strong><br>
                                Stock diminué de {{ $vente->quantite }} unités
                            </div>

                            <hr>

                            <h6>Stock Actuel</h6>
                            
                            <div class="mb-2">
                                <small class="text-muted">Stock Boutique :</small><br>
                                <strong>{{ $vente->produit->stockTotalBoutique ?? 0 }} unités</strong>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">Stock Magasin :</small><br>
                                <strong>{{ $vente->produit->stockTotalMagasin ?? 0 }} unités</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Actions</h6>
                            <div class="btn-group" role="group">
                                <form action="{{ route('ventes.destroy', $vente->id) }}" 
                                      method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-warning" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir annuler cette vente? Le stock sera automatiquement restauré.')">
                                        <i class="fas fa-undo"></i> Annuler la vente
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('produits.show', $vente->produit->id) }}" class="btn btn-outline-info">
                                <i class="fas fa-box"></i> Voir le produit
                            </a>
                            <a href="{{ route('ventes.index') }}" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-list"></i> Voir toutes les ventes
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations système -->
            <div class="card mt-3">
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Informations système :</strong><br>
                        <strong>Créé le :</strong> {{ $vente->created_at->format('d/m/Y H:i:s') }}<br>
                        <strong>Modifié le :</strong> {{ $vente->updated_at->format('d/m/Y H:i:s') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
