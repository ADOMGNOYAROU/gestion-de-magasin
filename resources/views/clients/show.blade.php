@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user"></i> {{ $client->nom_complet }}
                    </h1>
                    <small class="text-muted">Client ID: {{ $client->id }} • Inscrit le {{ $client->date_inscription->format('d/m/Y') }}</small>
                </div>
                <div>
                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <!-- Informations générales -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle"></i> Informations Générales
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Nom complet:</strong></td>
                                            <td>{{ $client->nom_complet }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>
                                                @if($client->email)
                                                    {{ $client->email }}
                                                @else
                                                    <span class="text-muted">Non fourni</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Téléphone:</strong></td>
                                            <td>
                                                @if($client->telephone)
                                                    {{ $client->telephone }}
                                                @else
                                                    <span class="text-muted">Non fourni</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date de naissance:</strong></td>
                                            <td>
                                                @if($client->date_naissance)
                                                    {{ $client->date_naissance->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">Non fournie</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Âge:</strong></td>
                                            <td>
                                                @if($client->age)
                                                    {{ $client->age }} ans
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sexe:</strong></td>
                                            <td>
                                                @if($client->sexe == 'M')
                                                    <span class="badge badge-primary">Masculin</span>
                                                @elseif($client->sexe == 'F')
                                                    <span class="badge badge-info">Féminin</span>
                                                @else
                                                    <span class="text-muted">Non spécifié</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Adresse:</strong></td>
                                            <td>
                                                @if($client->adresse)
                                                    {{ $client->adresse }}
                                                @else
                                                    <span class="text-muted">Non fournie</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ville:</strong></td>
                                            <td>
                                                @if($client->ville)
                                                    {{ $client->ville }}
                                                @else
                                                    <span class="text-muted">Non fournie</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Code postal:</strong></td>
                                            <td>
                                                @if($client->code_postal)
                                                    {{ $client->code_postal }}
                                                @else
                                                    <span class="text-muted">Non fourni</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Pays:</strong></td>
                                            <td>{{ $client->pays }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut:</strong></td>
                                            <td>
                                                <span class="badge {{ $client->statut == 'actif' ? 'badge-success' : 'badge-secondary' }}">
                                                    {{ ucfirst($client->statut) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dernière vente:</strong></td>
                                            <td>
                                                @if($client->derniere_vente)
                                                    {{ $client->derniere_vente->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">Jamais</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques de fidélité -->
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">
                                <i class="fas fa-star"></i> Programme de Fidélité
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <h2 class="text-success font-weight-bold">{{ number_format($client->solde_points, 0, ',', ' ') }}</h2>
                                <p class="text-muted mb-0">Points disponibles</p>
                            </div>

                            <div class="mb-3">
                                <h4 class="text-primary">{{ number_format($client->total_achats, 0, ',', ' ') }} FCFA</h4>
                                <p class="text-muted mb-0">Total des achats</p>
                            </div>

                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-right">
                                        <h5 class="text-info">{{ $client->ventes->count() }}</h5>
                                        <small class="text-muted">Ventes</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h5 class="text-warning">{{ $client->coupons->where('utilise', false)->count() }}</h5>
                                    <small class="text-muted">Coupons actifs</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglets pour différentes sections -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="clientTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ventes-tab" data-toggle="tab" href="#ventes" role="tab">
                                <i class="fas fa-shopping-cart"></i> Historique des Achats
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="points-tab" data-toggle="tab" href="#points" role="tab">
                                <i class="fas fa-star"></i> Points de Fidélité
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="coupons-tab" data-toggle="tab" href="#coupons" role="tab">
                                <i class="fas fa-ticket-alt"></i> Coupons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="actions-tab" data-toggle="tab" href="#actions" role="tab">
                                <i class="fas fa-cogs"></i> Actions
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="clientTabsContent">
                        <!-- Onglet Historique des Achats -->
                        <div class="tab-pane fade show active" id="ventes" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Numéro Ticket</th>
                                            <th>Montant</th>
                                            <th>Points Gagnés</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($client->ventes as $vente)
                                            <tr>
                                                <td>{{ $vente->date_vente->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('ventes.show', $vente) }}" class="text-decoration-none">
                                                        {{ $vente->numero_ticket }}
                                                    </a>
                                                </td>
                                                <td>{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</td>
                                                <td>
                                                    {{ $vente->pointsGagnes ?? 0 }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('ventes.show', $vente) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Voir
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                                    <br>Aucun achat enregistré
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Onglet Points de Fidélité -->
                        <div class="tab-pane fade" id="points" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Points</th>
                                            <th>Solde</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($client->points as $point)
                                            <tr>
                                                <td>{{ $point->date_transaction->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @if($point->type_operation == 'gain')
                                                        <span class="badge badge-success">Gain</span>
                                                    @elseif($point->type_operation == 'utilisation')
                                                        <span class="badge badge-warning">Utilisation</span>
                                                    @else
                                                        <span class="badge badge-info">{{ ucfirst($point->type_operation) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $point->description_complete }}</td>
                                                <td>
                                                    @if($point->points_gagnes > 0)
                                                        <span class="text-success">+{{ $point->points_gagnes }}</span>
                                                    @else
                                                        <span class="text-danger">-{{ $point->points_utilises }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $point->points_net }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="fas fa-star fa-2x mb-2"></i>
                                                    <br>Aucun mouvement de points
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Onglet Coupons -->
                        <div class="tab-pane fade" id="coupons" role="tabpanel">
                            <div class="row">
                                @forelse($client->coupons as $coupon)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border {{ $coupon->est_valide ? 'border-success' : 'border-secondary' }}">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="card-title mb-1">{{ $coupon->code }}</h6>
                                                        <p class="card-text mb-1">{{ $coupon->description_type }}</p>
                                                        <small class="text-muted">
                                                            Créé le {{ $coupon->created_at->format('d/m/Y') }}
                                                            @if($coupon->date_expiration)
                                                                <br>Expire le {{ $coupon->date_expiration->format('d/m/Y') }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                    <div class="text-right">
                                                        @if($coupon->utilise)
                                                            <span class="badge badge-secondary">Utilisé</span>
                                                        @elseif($coupon->est_expire)
                                                            <span class="badge badge-danger">Expiré</span>
                                                        @else
                                                            <span class="badge badge-success">Valide</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted py-4">
                                        <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                                        <br>Aucun coupon
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Onglet Actions -->
                        <div class="tab-pane fade" id="actions" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-plus-circle text-success"></i> Ajouter des Points</h6>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('clients.ajouter_points', $client) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="points_ajouter" class="form-label">Nombre de points</label>
                                                    <input type="number" class="form-control" id="points_ajouter" name="points" min="1" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description_ajouter" class="form-label">Description</label>
                                                    <input type="text" class="form-control" id="description_ajouter" name="description" required>
                                                </div>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-plus"></i> Ajouter les points
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-ticket-alt text-primary"></i> Générer un Coupon</h6>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('clients.generer_coupon', $client) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="type_coupon" class="form-label">Type de coupon</label>
                                                    <select class="form-control" id="type_coupon" name="type" required>
                                                        <option value="pourcentage">Pourcentage</option>
                                                        <option value="montant_fixe">Montant fixe</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="valeur_coupon" class="form-label">Valeur</label>
                                                    <input type="number" class="form-control" id="valeur_coupon" name="valeur" min="0.01" step="0.01" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="expiration_coupon" class="form-label">Jours avant expiration</label>
                                                    <input type="number" class="form-control" id="expiration_coupon" name="jours_expiration" min="1" placeholder="Optionnel">
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Générer le coupon
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
