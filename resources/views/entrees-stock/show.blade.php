@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails de l'Entrée de Stock</h1>
                <div>
                    <a href="{{ route('entrees-stock.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Informations de l'Entrée</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID Entrée :</strong></td>
                                    <td>#{{ $entree->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date :</strong></td>
                                    <td>{{ $entree->date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Produit :</strong></td>
                                    <td>
                                        <strong>{{ $entree->produit->nom }}</strong>
                                        <br><small class="text-muted">{{ $entree->produit->categorie }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Magasin :</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $entree->magasin->nom }}</span>
                                        <br><small class="text-muted">{{ $entree->magasin->localisation }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Source :</strong></td>
                                    <td>
                                        @if($entree->fournisseur)
                                            <div class="alert alert-primary py-2">
                                                <i class="fas fa-truck"></i>
                                                <strong>Fournisseur :</strong> {{ $entree->fournisseur->nom }}
                                                <br><small>{{ $entree->fournisseur->contact }} - {{ $entree->fournisseur->telephone }}</small>
                                            </div>
                                        @else
                                            <div class="alert alert-warning py-2">
                                                <i class="fas fa-handshake"></i>
                                                <strong>Partenaire :</strong> {{ $entree->partenaire->nom }}
                                                <br><small>{{ $entree->partenaire->contact }} - {{ $entree->partenaire->telephone }}</small>
                                                <br><small><strong>Type d'accord :</strong> {{ $entree->partenaire->type_accord }}</small>
                                            </div>
                                        @endif
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
                                    <td><strong>Quantité :</strong></td>
                                    <td>
                                        <span class="badge bg-success fs-6">{{ $entree->quantite }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Prix Unitaire :</strong></td>
                                    <td>{{ number_format($entree->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Total :</strong></td>
                                    <td>
                                        <strong class="text-primary">{{ number_format($entree->quantite * $entree->prix_unitaire, 0, ',', ' ') }} FCFA</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Prix Standard :</strong></td>
                                    <td>{{ number_format($entree->produit->prix_achat, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Écart :</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $entree->prix_unitaire > $entree->produit->prix_achat ? 'danger' : ($entree->prix_unitaire < $entree->produit->prix_achat ? 'success' : 'secondary') }}">
                                            {{ number_format($entree->prix_unitaire - $entree->produit->prix_achat, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Impact sur le Stock</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success">
                                <i class="fas fa-arrow-up"></i>
                                <strong>Stock augmenté de {{ $entree->quantite }} unités</strong>
                            </div>
                            
                            @if($entree->produit->stockTotalMagasin)
                                <small class="text-muted">
                                    <strong>Stock total actuel :</strong> {{ $entree->produit->stockTotalMagasin }} unités
                                </small>
                            @endif
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
                                <form action="{{ route('entrees-stock.destroy', $entree->id) }}" 
                                      method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée? Le stock sera automatiquement mis à jour.')">
                                        <i class="fas fa-trash"></i> Supprimer l'entrée
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('produits.show', $entree->produit->id) }}" class="btn btn-outline-info">
                                <i class="fas fa-box"></i> Voir le produit
                            </a>
                            <a href="{{ route('entrees-stock.index') }}" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-list"></i> Voir toutes les entrées
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
                        <strong>Créé le :</strong> {{ $entree->created_at->format('d/m/Y H:i:s') }}<br>
                        <strong>Modifié le :</strong> {{ $entree->updated_at->format('d/m/Y H:i:s') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
