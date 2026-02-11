@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Historique des Ventes</h1>
                <div>
                    <a href="{{ route('rapports.ventes.form') }}" class="btn btn-outline-primary me-2 {{ hideIfCannot('manage-rapports') }}">
                        <i class="fas fa-chart-line"></i> Rapport Ventes
                    </a>
                    <a href="{{ route('ventes.create') }}" class="btn btn-success">
                        <i class="fas fa-cash-register"></i> Nouvelle Vente
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
                    <form method="GET" action="{{ route('ventes.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Rechercher produit, boutique..." 
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
                                    <a href="{{ route('ventes.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Effacer
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des ventes -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Boutique</th>
                                    <th>Quantité</th>
                                    <th>Prix Unitaire</th>
                                    <th>Total</th>
                                    <th>Bénéfice</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventes as $vente)
                                    <tr>
                                        <td>{{ $vente->date_vente->format('d/m/Y') }}</td>
                                        <td>
                                            @foreach($vente->venteProduits as $vp)
                                                <strong>{{ $vp->produit->nom }}</strong> ({{ $vp->quantite }})
                                                @if(!$loop->last)<br>@endif
                                                <br><small class="text-muted">{{ $vp->produit->categorie }}</small>
                                            @endforeach
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $vente->boutique->nom }}</span>
                                            <br><small class="text-muted">{{ $vente->boutique->magasin->nom }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $vente->total_produits }}</span>
                                        </td>
                                        <td>{{ $vente->total_produits > 0 ? number_format($vente->montant_total / $vente->total_produits, 0, ',', ' ') : 0 }} FCFA</td>
                                        <td>
                                            <strong class="text-primary">{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ number_format($vente->benefice_total, 0, ',', ' ') }} FCFA</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('ventes.show', $vente->id) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('ventes.destroy', $vente->id) }}" 
                                                      method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Annuler la vente" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir annuler cette vente? Le stock sera restauré.')">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucune vente trouvée</p>
                                            <a href="{{ route('ventes.create') }}" class="btn btn-success">
                                                Enregistrer la première vente
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($ventes->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $ventes->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Ventes</h5>
                            <h3>{{ $ventes->total() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Chiffre d'Affaires</h5>
                            <h3>{{ number_format($ventes->sum('montant_total'), 0, ',', ' ') }} FCFA</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Bénéfice Total</h5>
                            <h3>{{ number_format($ventes->sum(function($vente) { return $vente->benefice_total; }), 0, ',', ' ') }} FCFA</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Marge Moyenne</h5>
                            <h3>{{ $ventes->sum('montant_total') > 0 ? round(($ventes->sum(function($vente) { return $vente->benefice_total; }) / $ventes->sum('montant_total')) * 100, 1) : 0 }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
