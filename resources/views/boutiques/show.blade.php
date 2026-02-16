@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Détails de la Boutique</h1>
                <div>
                    <a href="{{ route('boutiques.edit', $boutique) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('boutiques.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nom :</strong> {{ $boutique->nom }}</p>
                            <p><strong>Adresse :</strong> {{ $boutique->adresse ?? 'Non spécifiée' }}</p>
                            <p><strong>Téléphone :</strong> {{ $boutique->telephone ?? 'Non spécifié' }}</p>
                            <p><strong>Email :</strong> {{ $boutique->email ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Magasin :</strong> {{ $boutique->magasin->nom ?? 'Non spécifié' }}</p>
                            <p><strong>Responsable :</strong> {{ $boutique->responsable->name ?? 'Non spécifié' }}</p>
                            <p><strong>Date de création :</strong> {{ $boutique->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Dernière mise à jour :</strong> {{ $boutique->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Stock de la boutique</h6>
                </div>
                <div class="card-body">
                    @if($boutique->stockBoutiques->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité en stock</th>
                                        <th>Seuil d'alerte</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($boutique->stockBoutiques as $stock)
                                        <tr>
                                            <td>{{ $stock->produit->nom ?? 'Produit inconnu' }}</td>
                                            <td>{{ $stock->quantite }}</td>
                                            <td>{{ $stock->seuil_alerte ?? 'Non défini' }}</td>
                                            <td>
                                                @if(isset($stock->seuil_alerte) && $stock->quantite <= $stock->seuil_alerte)
                                                    <span class="badge bg-danger">Stock faible</span>
                                                @else
                                                    <span class="badge bg-success">En stock</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Aucun produit enregistré dans le stock de cette boutique.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
