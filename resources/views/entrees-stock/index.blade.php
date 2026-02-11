@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Historique des Entrées de Stock</h1>
                <div>
                    <a href="{{ route('rapports.partenaires.pdf') }}" class="btn btn-outline-info me-2 {{ hideIfCannot('view-rapports-partenaires') }}">
                        <i class="fas fa-handshake"></i> Rapport Partenaires
                    </a>
                    <a href="{{ route('entrees-stock.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Nouvelle Entrée
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filtres de recherche -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('entrees-stock.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Rechercher produit, fournisseur..." 
                                       value="{{ $search ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_debut" class="form-control" 
                                       placeholder="Date début" value="{{ $date_debut ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_fin" class="form-control" 
                                       placeholder="Date fin" value="{{ $date_fin ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                                @if($search || $date_debut || $date_fin)
                                    <a href="{{ route('entrees-stock.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Effacer
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des entrées -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Magasin</th>
                                    <th>Fournisseur/Partenaire</th>
                                    <th>Quantité</th>
                                    <th>Prix Unitaire</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entrees as $entree)
                                    <tr>
                                        <td>{{ $entree->date->format('d/m/Y') }}</td>
                                        <td>
                                            <strong>{{ $entree->produit->nom }}</strong>
                                            <br><small class="text-muted">{{ $entree->produit->categorie }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $entree->magasin->nom }}</span>
                                        </td>
                                        <td>
                                            @if($entree->fournisseur)
                                                <span class="badge bg-primary">F: {{ $entree->fournisseur->nom }}</span>
                                            @else
                                                <span class="badge bg-warning">P: {{ $entree->partenaire->nom }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">+{{ $entree->quantite }}</span>
                                        </td>
                                        <td>{{ number_format($entree->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            <strong>{{ number_format($entree->quantite * $entree->prix_unitaire, 0, ',', ' ') }} FCFA</strong>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('entrees-stock.show', $entree->id) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('entrees-stock.destroy', $entree->id) }}" 
                                                      method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Supprimer" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée? Le stock sera mis à jour.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucune entrée de stock trouvée</p>
                                            <a href="{{ route('entrees-stock.create') }}" class="btn btn-success">
                                                Créer la première entrée
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($entrees->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $entrees->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Entrées</h5>
                            <h3>{{ $entrees->total() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Quantité Totale</h5>
                            <h3>{{ number_format($entrees->sum('quantite'), 0, ',', ' ') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Valeur Totale</h5>
                            <h3>{{ number_format($entrees->sum(function($e) { return $e->quantite * $e->prix_unitaire; }), 0, ',', ' ') }} FCFA</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Prix Moyen</h5>
                            <h3>{{ number_format($entrees->avg('prix_unitaire'), 0, ',', ' ') }} FCFA</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
