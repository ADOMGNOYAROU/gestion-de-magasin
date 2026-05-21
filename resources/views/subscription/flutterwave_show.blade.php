@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-credit-card text-primary"></i> Gestion de l'abonnement (Flutterwave)
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
                            {{ number_format($plan['price'], 0, ',', ' ') }} FCFA<span class="text-lg text-gray-600">/mois</span>
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
                        <p>Prochain renouvellement: {{ $tenant->subscription_ends_at->format('d/m/Y') }}</p>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <form action="{{ route('flutterwave.initialize') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $tenant->plan }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-credit-card"></i> Payer avec Flutterwave
                            </button>
                        </form>
                        
                        @if($tenant->flutterwave_subscription_id)
                        <form action="{{ route('flutterwave.cancel') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Annuler l'abonnement
                            </button>
                        </form>
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
                                    onclick="selectPlan('{{ $key }}')">
                                <div>
                                    <strong>{{ $planConfig['name'] }}</strong>
                                    <small class="d-block text-muted">{{ $planConfig['description'] }}</small>
                                </div>
                                <span class="badge bg-primary">{{ number_format($planConfig['price'], 0, ',', ' ') }} FCFA</span>
                            </button>
                            @endif
                        @endforeach
                    </div>

                    <form action="{{ route('flutterwave.initialize') }}" method="POST" id="upgradeForm">
                        @csrf
                        <input type="hidden" name="plan" id="selectedPlan" value="{{ $tenant->plan }}">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-arrow-up"></i> Changer de plan
                        </button>
                    </form>
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

<script>
function selectPlan(plan) {
    document.getElementById('selectedPlan').value = plan;
}
</script>
@endsection
