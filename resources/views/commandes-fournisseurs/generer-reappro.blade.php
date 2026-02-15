@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-robot"></i> Générer Réapprovisionnement Automatique
                    </h1>
                    <small class="text-muted">Produits en rupture de stock ou sous le seuil d'alerte</small>
                </div>
                <a href="{{ route('commandes-fournisseurs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('commandes-fournisseurs.generer-reappro') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="seuil_alerte" class="form-label">Seuil d'alerte</label>
                                <input type="number" class="form-control" id="seuil_alerte" name="seuil_alerte" value="{{ $seuil }}" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="fournisseur_id" class="form-label">Filtrer par fournisseur</label>
                                <select class="form-control" id="fournisseur_id" name="fournisseur_id">
                                    <option value="">Tous les fournisseurs</option>
                                    @foreach($fournisseurs as $fournisseur)
                                        <option value="{{ $fournisseur->id }}" {{ $fournisseurId == $fournisseur->id ? 'selected' : '' }}>
                                            {{ $fournisseur->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Produits à réapprovisionner -->
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-boxes"></i> Produits nécessitant un réapprovisionnement
                        <span class="badge badge-primary">{{ $produitsARappro->count() }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($produitsARappro->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>Aucun produit ne nécessite de réapprovisionnement</h5>
                            <p class="text-muted">Tous les produits ont un stock suffisant.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Catégorie</th>
                                        <th>Stock Total</th>
                                        <th>Stock par Boutique</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($produitsARappro as $produit)
                                        <tr>
                                            <td>
                                                <strong>{{ $produit->nom }}</strong>
                                                @if($produit->description)
                                                    <br><small class="text-muted">{{ Str::limit($produit->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $produit->categorie }}</span>
                                            </td>
                                            <td>
                                                {{ $produit->stockBoutiques->sum('quantite') }}
                                            </td>
                                            <td>
                                                @foreach($produit->stockBoutiques as $stock)
                                                    <small class="d-block">
                                                        {{ $stock->boutique->nom ?? 'Boutique inconnue' }}: {{ $stock->quantite }}
                                                    </small>
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ route('commandes-fournisseurs.create', ['fournisseur_id' => $fournisseurId, 'produit_id' => $produit->id]) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus"></i> Commander
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
