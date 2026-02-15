@extends('layouts.app')

@section('title', 'Commandes Fournisseurs')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item active">Commandes Fournisseurs</li>
@endsection

@section('header')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
    <div class="mb-3 mb-sm-0">
        <h1 class="h4 h3-sm mb-1">Commandes Fournisseurs</h1>
        <p class="text-muted mb-0 small">Gestion des achats et réapprovisionnements</p>
    </div>
    <div class="w-100 w-sm-auto">
        <a href="{{ route('commandes-fournisseurs.create') }}" class="btn btn-primary w-100">
            <i class="fas fa-plus me-2"></i>
            <span class="d-sm-inline">Nouvelle commande</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Statistiques -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Commandes
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-secondary shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Brouillons
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['brouillons'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-edit fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    En cours
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['en_cours'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Livrées
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['livrees'] }}</div>
                                <div class="text-xs text-muted">
                                    {{ number_format($stats['total_montant'], 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter"></i> Filtres
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('commandes-fournisseurs.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="fournisseur_id" class="form-label">Fournisseur</label>
                        <select class="form-select" id="fournisseur_id" name="fournisseur_id">
                            <option value="">Tous les fournisseurs</option>
                            @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" {{ request('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                    {{ $fournisseur->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="brouillon" {{ request('status') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="envoyee" {{ request('status') == 'envoyee' ? 'selected' : '' }}>Envoyée</option>
                            <option value="confirmee" {{ request('status') == 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                            <option value="en_cours_livraison" {{ request('status') == 'en_cours_livraison' ? 'selected' : '' }}>En livraison</option>
                            <option value="livree" {{ request('status') == 'livree' ? 'selected' : '' }}>Livrée</option>
                            <option value="annulee" {{ request('status') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="date_debut" class="form-label">Date début</label>
                        <input type="date" class="form-control" id="date_debut" name="date_debut"
                               value="{{ request('date_debut') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="date_fin" class="form-label">Date fin</label>
                        <input type="date" class="form-control" id="date_fin" name="date_fin"
                               value="{{ request('date_fin') }}">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                            <a href="{{ route('commandes-fournisseurs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-magic text-primary"></i> Actions Rapides
                        </h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('commandes-fournisseurs.generer-reappro') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-robot"></i> Générer réappro
                            </a>
                            <button class="btn btn-outline-info btn-sm" onclick="openPriceComparison()">
                                <i class="fas fa-balance-scale"></i> Comparer prix
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-chart-bar text-success"></i> Rapports
                        </h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-outline-success btn-sm" onclick="exportCommandes()">
                                <i class="fas fa-download"></i> Exporter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des commandes -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list"></i> Liste des Commandes
                </h6>
                <span class="text-muted">{{ $commandes->total() }} commandes</span>
            </div>
            <div class="card-body">
                @if($commandes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <a href="{{ route('commandes-fournisseurs.index', array_merge(request()->query(), ['tri' => 'numero_commande', 'direction' => $tri == 'numero_commande' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                            N° Commande
                                            @if($tri == 'numero_commande')
                                                <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Fournisseur</th>
                                    <th>
                                        <a href="{{ route('commandes-fournisseurs.index', array_merge(request()->query(), ['tri' => 'date_commande', 'direction' => $tri == 'date_commande' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                            Date
                                            @if($tri == 'date_commande')
                                                <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Statut</th>
                                    <th class="text-end">Total TTC</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commandes as $commande)
                                    <tr>
                                        <td>
                                            <a href="{{ route('commandes-fournisseurs.show', $commande) }}" class="text-decoration-none">
                                                <strong>{{ $commande->numero_commande }}</strong>
                                            </a>
                                        </td>
                                        <td>{{ $commande->fournisseur->nom }}</td>
                                        <td>{{ $commande->date_commande->format('d/m/Y') }}</td>
                                        <td>{!! $commande->status_badge !!}</td>
                                        <td class="text-end">
                                            <strong>{{ number_format($commande->total_ttc, 0, ',', ' ') }} FCFA</strong>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('commandes-fournisseurs.show', $commande) }}"
                                                   class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($commande->peutEtreModifiee())
                                                    <a href="{{ route('commandes-fournisseurs.edit', $commande) }}"
                                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="{{ route('commandes-fournisseurs.historique-fournisseur', $commande->fournisseur) }}"
                                                               class="dropdown-item">
                                                                <i class="fas fa-history"></i> Historique fournisseur
                                                            </a>
                                                        </li>
                                                        @if($commande->peutEtreModifiee())
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form method="POST" action="{{ route('commandes-fournisseurs.destroy', $commande) }}"
                                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-trash"></i> Supprimer
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $commandes->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Aucune commande trouvée</h4>
                        <p class="text-muted">Il n'y a encore aucune commande fournisseur dans le système.</p>
                        <a href="{{ route('commandes-fournisseurs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Créer la première commande
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour comparaison de prix -->
<div class="modal fade" id="priceComparisonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Comparer les prix des fournisseurs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="priceComparisonForm">
                    @csrf
                    <div class="mb-3">
                        <label for="produit_compare" class="form-label">Sélectionner un produit</label>
                        <select class="form-select" id="produit_compare" name="produit_id" required>
                            <option value="">Choisir un produit...</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-balance-scale"></i> Comparer les prix
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openPriceComparison() {
    $('#priceComparisonModal').modal('show');

    // Charger les produits pour la comparaison
    $.get('{{ route('produits.search') }}?limit=50')
        .done(function(data) {
            const select = $('#produit_compare');
            select.empty().append('<option value="">Choisir un produit...</option>');

            data.forEach(function(produit) {
                select.append(`<option value="${produit.id}">${produit.nom} - ${produit.categorie}</option>`);
            });
        });
}

$('#priceComparisonForm').on('submit', function(e) {
    e.preventDefault();
    const produitId = $('#produit_compare').val();

    if (produitId) {
        window.open('{{ url('commandes-fournisseurs/comparer-prix') }}?produit_id=' + produitId, '_blank');
        $('#priceComparisonModal').modal('hide');
    }
});

function exportCommandes() {
    const params = new URLSearchParams(window.location.search);
    window.open('{{ route('commandes-fournisseurs.index') }}?export=1&' + params.toString(), '_blank');
}
</script>
@endpush
