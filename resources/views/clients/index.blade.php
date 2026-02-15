@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestion des Clients</h1>
                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouveau Client
                </a>
            </div>

            <!-- Filtres et recherche -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filtres et Recherche</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('clients.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Rechercher</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ $search }}" placeholder="Nom, prénom, email, téléphone...">
                        </div>
                        <div class="col-md-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-control" id="statut" name="statut">
                                <option value="">Tous</option>
                                <option value="actif" {{ $statut == 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="inactif" {{ $statut == 'inactif' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tri" class="form-label">Trier par</label>
                            <select class="form-control" id="tri" name="tri">
                                <option value="nom" {{ $tri == 'nom' ? 'selected' : '' }}>Nom</option>
                                <option value="prenom" {{ $tri == 'prenom' ? 'selected' : '' }}>Prénom</option>
                                <option value="email" {{ $tri == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="solde_points" {{ $tri == 'solde_points' ? 'selected' : '' }}>Points</option>
                                <option value="total_achats" {{ $tri == 'total_achats' ? 'selected' : '' }}>Total achats</option>
                                <option value="date_inscription" {{ $tri == 'date_inscription' ? 'selected' : '' }}>Date inscription</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des clients -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Clients ({{ $clients->total() }})
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                             aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('clients.index') }}?tri=solde_points&direction=desc">
                                <i class="fas fa-star fa-sm fa-fw mr-2 text-gray-400"></i>
                                Meilleurs clients (points)
                            </a>
                            <a class="dropdown-item" href="{{ route('clients.index') }}?tri=total_achats&direction=desc">
                                <i class="fas fa-euro-sign fa-sm fa-fw mr-2 text-gray-400"></i>
                                Plus gros acheteurs
                            </a>
                            <a class="dropdown-item" href="{{ route('clients.index') }}?tri=date_inscription&direction=desc">
                                <i class="fas fa-calendar fa-sm fa-fw mr-2 text-gray-400"></i>
                                Clients récents
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($clients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Contact</th>
                                        <th>Points Fidélité</th>
                                        <th>Total Achats</th>
                                        <th>Dernière Vente</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clients as $client)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white mr-3" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                        {{ strtoupper(substr($client->nom, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold">{{ $client->nom_complet }}</div>
                                                        <small class="text-muted">ID: {{ $client->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($client->email)
                                                    <div><i class="fas fa-envelope text-muted"></i> {{ $client->email }}</div>
                                                @endif
                                                @if($client->telephone)
                                                    <div><i class="fas fa-phone text-muted"></i> {{ $client->telephone }}</div>
                                                @endif
                                                @if(!$client->email && !$client->telephone)
                                                    <span class="text-muted">Aucun contact</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-primary badge-pill">
                                                    <i class="fas fa-star"></i> {{ number_format($client->solde_points, 0, ',', ' ') }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($client->total_achats, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                @if($client->derniere_vente)
                                                    {{ $client->derniere_vente->format('d/m/Y') }}
                                                @else
                                                    Jamais
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $client->statut == 'actif' ? 'badge-success' : 'badge-secondary' }}">
                                                    {{ ucfirst($client->statut) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-info" title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ? Cette action est irréversible.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $clients->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun client trouvé</h5>
                            <p class="text-muted">Commencez par créer votre premier client.</p>
                            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Créer le premier client
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.avatar-circle {
    font-size: 16px;
}
</style>
