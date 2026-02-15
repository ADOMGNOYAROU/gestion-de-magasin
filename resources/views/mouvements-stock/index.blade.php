@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Historique des Mouvements de Stock</h1>
                <a href="{{ route('rapports.stock.pdf') }}" class="btn btn-outline-danger">
                    <i class="fas fa-file-pdf"></i> Rapport Stock
                </a>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('mouvements-stock.index') }}" class="row g-3">
                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">Tous</option>
                                <option value="entree" {{ $type == 'entree' ? 'selected' : '' }}>Entrée</option>
                                <option value="sortie" {{ $type == 'sortie' ? 'selected' : '' }}>Sortie</option>
                                <option value="transfert" {{ $type == 'transfert' ? 'selected' : '' }}>Transfert</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Date début</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $date_from }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Date fin</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $date_to }}">
                        </div>
                        <div class="col-md-3">
                            <label for="produit_id" class="form-label">Produit</label>
                            <select name="produit_id" id="produit_id" class="form-select">
                                <option value="">Tous les produits</option>
                                @foreach($produits as $produit)
                                    <option value="{{ $produit->id }}" {{ $produit_id == $produit->id ? 'selected' : '' }}>
                                        {{ $produit->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="user_id" class="form-label">Utilisateur</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">Tous les utilisateurs</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des mouvements -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Utilisateur</th>
                                    <th>Motif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginated as $mouvement)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($mouvement['date'])->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($mouvement['type'] == 'entree')
                                                <span class="badge bg-success">Entrée</span>
                                            @elseif($mouvement['type'] == 'sortie')
                                                <span class="badge bg-danger">Sortie</span>
                                            @elseif(str_contains($mouvement['type'], 'transfert'))
                                                <span class="badge bg-primary">Transfert</span>
                                            @endif
                                        </td>
                                        <td>{{ $mouvement['produit']->nom }}</td>
                                        <td>
                                            <span class="{{ $mouvement['quantite'] > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $mouvement['quantite'] > 0 ? '+' : '' }}{{ $mouvement['quantite'] }}
                                            </span>
                                        </td>
                                        <td>{{ $mouvement['user'] ? $mouvement['user']->name : 'N/A' }}</td>
                                        <td>{{ $mouvement['motif'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucun mouvement trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($paginated->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $paginated->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
