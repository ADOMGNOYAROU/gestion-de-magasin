@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-credit-card text-primary"></i> Gestion de l'abonnement
            </h1>
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

    <div class="row">
        <!-- Plan Actuel -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">Plan Actuel</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="text-3xl font-bold text-gray-800">{{ $plan['name'] }}</h2>
                        <p class="text-5xl font-bold text-primary my-3">
                            {{ $plan['price'] }}€<span class="text-lg text-gray-600">/mois</span>
                        </p>
                        <span class="badge bg-{{ $tenant->subscription_status_color }} fs-6">
                            {{ $tenant->subscription_status_label }}
                        </span>
                    </div>

                    @if($tenant->isOnTrial())
                    <div class="alert alert-info">
                        <i class="fas fa-clock"></i>
                        Essai gratuit se termine le {{ $tenant->trial_ends_at->format('d/m/Y') }}
                    </div>
                    @endif

                    @if($tenant->subscription_ends_at)
                    <div class="text-center text-gray-600 mb-3">
                        @if($tenant->subscription('default') && $tenant->subscription('default')->cancelled())
                            <p>Abonnement annulé. Accès jusqu'au {{ $tenant->subscription_ends_at->format('d/m/Y') }}</p>
                        @else
                            <p>Prochain renouvellement: {{ $tenant->subscription_ends_at->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        @if($tenant->subscription('default') && $tenant->subscription('default')->cancelled())
                        <form action="{{ route('subscription.resume') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-redo"></i> Réactiver l'abonnement
                            </button>
                        </form>
                        @else
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fas fa-times"></i> Annuler l'abonnement
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Utilisation -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info">
                    <h6 class="m-0 font-weight-bold text-white">Utilisation</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Utilisateurs</span>
                            <span>{{ $tenant->users()->count() }} / {{ $plan['max_users'] === -1 ? '∞' : $plan['max_users'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            @php
                                $userPercent = $plan['max_users'] === -1 ? 0 : ($tenant->users()->count() / $plan['max_users']) * 100;
                            @endphp
                            <div class="progress-bar {{ $userPercent > 80 ? 'bg-danger' : 'bg-success' }}" 
                                 style="width: {{ $userPercent }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Magasins</span>
                            <span>{{ $tenant->magasins()->count() }} / {{ $plan['max_magasins'] === -1 ? '∞' : $plan['max_magasins'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            @php
                                $magasinPercent = $plan['max_magasins'] === -1 ? 0 : ($tenant->magasins()->count() / $plan['max_magasins']) * 100;
                            @endphp
                            <div class="progress-bar {{ $magasinPercent > 80 ? 'bg-danger' : 'bg-success' }}" 
                                 style="width: {{ $magasinPercent }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Boutiques</span>
                            <span>{{ $tenant->boutiques()->count() }} / {{ $plan['max_boutiques'] === -1 ? '∞' : $plan['max_boutiques'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            @php
                                $boutiquePercent = $plan['max_boutiques'] === -1 ? 0 : ($tenant->boutiques()->count() / $plan['max_boutiques']) * 100;
                            @endphp
                            <div class="progress-bar {{ $boutiquePercent > 80 ? 'bg-danger' : 'bg-success' }}" 
                                 style="width: {{ $boutiquePercent }}%"></div>
                        </div>
                    </div>

                    @if(isset($plan['max_products']) && $plan['max_products'] !== -1)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Produits</span>
                            <span>{{ $tenant->produits()->count() }} / {{ $plan['max_products'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            @php
                                $productPercent = ($tenant->produits()->count() / $plan['max_products']) * 100;
                            @endphp
                            <div class="progress-bar {{ $productPercent > 80 ? 'bg-danger' : 'bg-success' }}" 
                                 style="width: {{ $productPercent }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Changer de Plan -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">Changer de Plan</h6>
                </div>
                <div class="card-body">
                    <p class="text-gray-600 mb-4">Upgradez ou downgradez votre plan à tout moment.</p>
                    
                    <div class="list-group mb-3">
                        @foreach($plans as $key => $planConfig)
                            @if($key !== 'currency' && $key !== 'currency_symbol' && $key !== 'trial_days' && $key !== 'warning_days' && $key !== 'redirect_expired' && $key !== 'redirect_cancelled')
                            <button type="button" 
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $key === $tenant->plan ? 'active' : '' }}"
                                    onclick="selectPlan('{{ $key }}', '{{ $planConfig['price'] }}')">
                                <div>
                                    <strong>{{ $planConfig['name'] }}</strong>
                                    <small class="d-block text-muted">{{ $planConfig['description'] }}</small>
                                </div>
                                <span class="badge bg-primary">{{ $planConfig['price'] }}€/mois</span>
                            </button>
                            @endif
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-primary w-100" onclick="showPaymentModal()">
                        <i class="fas fa-arrow-up"></i> Changer de plan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations de l'entreprise -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-secondary">
                    <h6 class="m-0 font-weight-bold text-white">Informations de l'entreprise</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nom:</strong> {{ $tenant->name }}</p>
                            <p><strong>Email:</strong> {{ $tenant->email }}</p>
                            <p><strong>Téléphone:</strong> {{ $tenant->phone ?? 'Non renseigné' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Adresse:</strong> {{ $tenant->address ?? 'Non renseignée' }}</p>
                            <p><strong>Ville:</strong> {{ $tenant->city ?? 'Non renseignée' }}</p>
                            <p><strong>Code postal:</strong> {{ $tenant->postal_code ?? 'Non renseigné' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'annulation -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Annuler l'abonnement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir annuler votre abonnement ?</p>
                <p class="text-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Votre accès sera maintenu jusqu'à la fin de la période actuelle ({{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d/m/Y') : 'fin du trial' }}).
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('subscription.cancel') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de paiement -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer le changement de plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Vous allez passer au plan <strong id="selectedPlanName"></strong> pour <strong id="selectedPlanPrice"></strong>€/mois.</p>
                
                <form id="upgradeForm" action="{{ route('subscription.upgrade') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" id="planInput">
                    
                    <div class="mb-3">
                        <label class="form-label">Méthode de paiement</label>
                        <!-- Stripe Elements sera ajouté ici -->
                        <div id="card-element" class="form-control"></div>
                        <div id="card-errors" class="text-danger mt-2"></div>
                    </div>
                    
                    <input type="hidden" name="payment_method" id="paymentMethodInput">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="upgradeForm" class="btn btn-primary">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedPlan = 'starter';
let selectedPlanPrice = 29;

function selectPlan(plan, price) {
    selectedPlan = plan;
    selectedPlanPrice = price;
    document.getElementById('selectedPlanName').textContent = plan.charAt(0).toUpperCase() + plan.slice(1);
    document.getElementById('selectedPlanPrice').textContent = price;
    document.getElementById('planInput').value = plan;
}

function showPaymentModal() {
    // Charger Stripe Elements ici
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}
</script>
@endsection
