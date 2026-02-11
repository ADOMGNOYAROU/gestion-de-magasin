@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails du Produit</h1>
                <div>
                    <a href="{{ route('produits.edit', $produit->id) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('produits.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Informations du Produit</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID :</strong></td>
                                    <td>{{ $produit->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nom :</strong></td>
                                    <td>{{ $produit->nom }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Catégorie :</strong></td>
                                    <td><span class="badge bg-info">{{ $produit->categorie }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Description :</strong></td>
                                    <td>{{ $produit->description ?: 'Aucune description' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Statut :</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $produit->statut == 'actif' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($produit->statut) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Informations de Prix</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Prix d'achat :</strong></td>
                                    <td>{{ number_format($produit->prix_achat, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Prix de vente :</strong></td>
                                    <td>{{ number_format($produit->prix_vente, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td><strong>Marge :</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $produit->marge_percentage > 30 ? 'success' : ($produit->marge_percentage > 15 ? 'warning' : 'danger') }}">
                                            {{ number_format($produit->marge, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>% Marge :</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $produit->marge_percentage > 30 ? 'success' : ($produit->marge_percentage > 15 ? 'warning' : 'danger') }}">
                                            {{ $produit->marge_percentage }}%
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Informations Système</h5>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <strong>Créé le :</strong><br>
                                {{ $produit->created_at->format('d/m/Y H:i:s') }}<br><br>
                                <strong>Modifié le :</strong><br>
                                {{ $produit->updated_at->format('d/m/Y H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Actions rapides</h6>
                            <div class="btn-group" role="group">
                                <a href="{{ route('produits.edit', $produit->id) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <form action="{{ route('produits.destroy', $produit->id) }}" 
                                      method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit?')">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                        <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> Voir tous les produits
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
