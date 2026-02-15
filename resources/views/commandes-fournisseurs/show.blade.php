@extends('layouts.app')

@section('title', 'Commande Fournisseur - ' . $commandeFournisseur->numero_commande)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('commandes-fournisseurs.index') }}">Commandes Fournisseurs</a></li>
    <li class="breadcrumb-item active">{{ $commandeFournisseur->numero_commande }}</li>
@endsection

@section('header')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
    <div class="mb-3 mb-sm-0">
        <h1 class="h4 h3-sm mb-1">{{ $commandeFournisseur->numero_commande }}</h1>
        <p class="text-muted mb-0 small">
            Commande du {{ $commandeFournisseur->date_commande ? $commandeFournisseur->date_commande->format('d/m/Y') : 'Date inconnue' }} •
            {!! $commandeFournisseur->status_badge !!}
        </p>
    </div>
    <div class="w-100 w-sm-auto">
        <div class="btn-group" role="group">
            @if($commandeFournisseur->peutEtreModifiee())
                <a href="{{ route('commandes-fournisseurs.edit', $commandeFournisseur) }}" class="btn btn-warning btn-mobile">
                    <i class="fas fa-edit me-2"></i>
                    <span class="d-sm-inline">Modifier</span>
                </a>
            @endif
            <button class="btn btn-info btn-mobile" onclick="changerStatus()">
                <i class="fas fa-exchange-alt me-2"></i>
                <span class="d-sm-inline">Changer Statut</span>
            </button>
            <a href="{{ route('commandes-fournisseurs.rapport-pdf', $commandeFournisseur) }}" class="btn btn-success btn-mobile" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>
                <span class="d-sm-inline">PDF</span>
            </a>
            <a href="{{ route('commandes-fournisseurs.index') }}" class="btn btn-secondary btn-mobile">
                <i class="fas fa-arrow-left me-2"></i>
                <span class="d-sm-inline">Retour</span>
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Informations générales -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle"></i> Informations Générales
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>N° Commande:</strong></td>
                                <td>{{ $commandeFournisseur->numero_commande }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fournisseur:</strong></td>
                                <td>
                                    <a href="{{ route('fournisseurs.show', $commandeFournisseur->fournisseur) }}" class="text-decoration-none">
                                        {{ $commandeFournisseur->fournisseur->nom }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Date commande:</strong></td>
                                <td>{{ $commandeFournisseur->date_commande ? $commandeFournisseur->date_commande->format('d/m/Y') : 'Non définie' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Statut:</strong></td>
                                <td>{!! $commandeFournisseur->status_badge !!}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Créée par:</strong></td>
                                <td>{{ $commandeFournisseur->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Magasin:</strong></td>
                                <td>{{ $commandeFournisseur->magasin ? $commandeFournisseur->magasin->nom : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date livraison prévue:</strong></td>
                                <td>{{ $commandeFournisseur->date_livraison_prevue ? $commandeFournisseur->date_livraison_prevue->format('d/m/Y') : 'Non définie' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date livraison réelle:</strong></td>
                                <td>{{ $commandeFournisseur->date_livraison_reelle ? $commandeFournisseur->date_livraison_reelle->format('d/m/Y') : 'Non livrée' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($commandeFournisseur->notes)
                    <div class="mt-3">
                        <strong>Notes:</strong>
                        <div class="mt-1 p-2 bg-light rounded">{{ $commandeFournisseur->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Produits commandés -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-shopping-cart"></i> Produits Commandés
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th class="text-center">Quantité</th>
                                <th class="text-center">Prix HT</th>
                                <th class="text-center">TVA</th>
                                <th class="text-end">Sous-total HT</th>
                                <th class="text-end">Sous-total TTC</th>
                                <th class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commandeFournisseur->lignes as $ligne)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $ligne->produit->nom }}</strong><br>
                                            <small class="text-muted">{{ $ligne->produit->categorie }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $ligne->quantite_commandee }}</span>
                                        @if($ligne->quantite_livree > 0)
                                            <br><small class="text-success">Reçu: {{ $ligne->quantite_livree }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($ligne->prix_unitaire_ht, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-center">{{ $ligne->tva_taux }}%</td>
                                    <td class="text-end">{{ number_format($ligne->sous_total_ht, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-end">{{ number_format($ligne->sous_total_ttc, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-center">
                                        @if($ligne->estComplete())
                                            <span class="badge bg-success">Complet</span>
                                        @elseif($ligne->quantite_livree > 0)
                                            <span class="badge bg-warning">Partiel</span>
                                        @else
                                            <span class="badge bg-secondary">En attente</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="4" class="text-end">TOTAL</th>
                                <th class="text-end">{{ number_format($commandeFournisseur->total_ht, 0, ',', ' ') }} FCFA</th>
                                <th class="text-end">{{ number_format($commandeFournisseur->total_ttc, 0, ',', ' ') }} FCFA</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques et actions -->
    <div class="col-lg-4">
        <!-- Totaux -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-calculator"></i> Totaux
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-primary mb-1">{{ number_format($commandeFournisseur->total_ht, 0, ',', ' ') }} FCFA</h4>
                            <small class="text-muted">Total HT</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h6 class="text-info mb-1">{{ number_format($commandeFournisseur->tva, 0, ',', ' ') }} FCFA</h6>
                            <small class="text-muted">TVA</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h6 class="text-success mb-1">{{ number_format($commandeFournisseur->total_ttc, 0, ',', ' ') }} FCFA</h6>
                            <small class="text-muted">Total TTC</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques de livraison -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-chart-bar"></i> Livraison
                </h6>
            </div>
            <div class="card-body">
                @php
                    $totalCommande = $commandeFournisseur->lignes->sum('quantite_commandee');
                    $totalLivre = $commandeFournisseur->lignes->sum('quantite_livree');
                    $pourcentageLivraison = $totalCommande > 0 ? ($totalLivre / $totalCommande) * 100 : 0;
                @endphp

                <div class="text-center mb-3">
                    <h4 class="text-primary">{{ number_format($pourcentageLivraison, 1) }}%</h4>
                    <small class="text-muted">Taux de livraison</small>
                </div>

                <div class="progress mb-3" style="height: 20px;">
                    <div class="progress-bar {{ $pourcentageLivraison == 100 ? 'bg-success' : ($pourcentageLivraison > 0 ? 'bg-warning' : 'bg-secondary') }}"
                         role="progressbar" style="width: {{ $pourcentageLivraison }}%">
                        {{ number_format($pourcentageLivraison, 1) }}%
                    </div>
                </div>

                <div class="row text-center">
                    <div class="col-6">
                        <strong>{{ $totalCommande }}</strong><br>
                        <small class="text-muted">Commandé</small>
                    </div>
                    <div class="col-6">
                        <strong>{{ $totalLivre }}</strong><br>
                        <small class="text-muted">Reçu</small>
                    </div>
                </div>

                @if($commandeFournisseur->estEnRetard())
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        Cette commande est en retard sur la date de livraison prévue.
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-bolt"></i> Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($commandeFournisseur->peutEtreModifiee())
                        <a href="{{ route('commandes-fournisseurs.edit', $commandeFournisseur) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    @endif

                    <a href="{{ route('commandes-fournisseurs.generer-reappro', ['fournisseur_id' => $commandeFournisseur->fournisseur_id]) }}" class="btn btn-info">
                        <i class="fas fa-robot"></i> Réappro similaire
                    </a>

                    <a href="{{ route('commandes-fournisseurs.comparer-prix', ['produit_id' => $commandeFournisseur->lignes->first()->produit_id ?? null]) }}" class="btn btn-success">
                        <i class="fas fa-balance-scale"></i> Comparer prix
                    </a>

                    @if($commandeFournisseur->peutEtreAnnulee())
                        <button class="btn btn-danger" onclick="annulerCommande()">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Historique du fournisseur -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history"></i> Fournisseur
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h6>{{ $commandeFournisseur->fournisseur->nom }}</h6>
                    @if($commandeFournisseur->fournisseur->contact_personne)
                        <small class="text-muted">{{ $commandeFournisseur->fournisseur->contact_personne }}</small>
                    @endif
                </div>

                <div class="row text-center">
                    <div class="col-6">
                        <a href="{{ route('fournisseurs.show', $commandeFournisseur->fournisseur) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('commandes-fournisseurs.historique-fournisseur', $commandeFournisseur->fournisseur) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-history"></i> Historique
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal changement de statut -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('commandes-fournisseurs.changer_status', $commandeFournisseur) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Changer le statut de la commande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Nouveau statut</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="brouillon" {{ $commandeFournisseur->status == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="envoyee" {{ $commandeFournisseur->status == 'envoyee' ? 'selected' : '' }}>Envoyée</option>
                            <option value="confirmee" {{ $commandeFournisseur->status == 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                            <option value="en_cours_livraison" {{ $commandeFournisseur->status == 'en_cours_livraison' ? 'selected' : '' }}>En cours de livraison</option>
                            <option value="livree" {{ $commandeFournisseur->status == 'livree' ? 'selected' : '' }}>Livrée</option>
                            <option value="annulee" {{ $commandeFournisseur->status == 'annulee' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>

                    <div class="mb-3" id="dateLivraisonDiv" style="display: none;">
                        <label for="date_livraison_reelle" class="form-label">Date de livraison réelle</label>
                        <input type="date" class="form-control" id="date_livraison_reelle" name="date_livraison_reelle">
                    </div>

                    <div class="mb-3">
                        <label for="notes_status" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" id="notes_status" name="notes" rows="3" placeholder="Ajouter une note sur ce changement de statut..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Changer le statut</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changerStatus() {
    $('#statusModal').modal('show');
}

$('#status').on('change', function() {
    const status = $(this).val();
    const dateLivraisonDiv = $('#dateLivraisonDiv');

    if (status === 'livree') {
        dateLivraisonDiv.show();
        $('#date_livraison_reelle').attr('required', true);
    } else {
        dateLivraisonDiv.hide();
        $('#date_livraison_reelle').attr('required', false);
    }
});

function annulerCommande() {
    if (confirm('Êtes-vous sûr de vouloir annuler cette commande ? Cette action est irréversible.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('commandes-fournisseurs.changer_status', $commandeFournisseur) }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';

        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'status';
        statusField.value = 'annulee';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(statusField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
