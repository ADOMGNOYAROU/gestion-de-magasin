@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- En-tête du dashboard -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt"></i> 
            Tableau de Bord
            @if(isset($magasin))
                <small class="text-muted">- {{ $magasin->nom }}</small>
            @elseif(isset($boutique))
                <small class="text-muted">- {{ $boutique->nom }}</small>
            @endif
        </h1>
        <div>
            <a href="{{ route('rapports.index') }}" class="btn btn-primary me-2 {{ hideIfCannot('manage-rapports') }}">
                <i class="fas fa-file-alt"></i> Rapports
            </a>
            <div class="text-muted d-inline-block">
                <i class="fas fa-clock"></i> {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <!-- Messages flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Cartes de statistiques -->
    <div class="row">
        <!-- Carte Produits -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Produits Actifs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalProduits, 0, ',', ' ') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Stock Magasin -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Stock Magasin
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stockTotalMagasin, 0, ',', ' ') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Stock Boutiques -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Stock Boutiques
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stockTotalBoutiques, 0, ',', ' ') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Ventes du jour -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Ventes du jour
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $ventesJour }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne de statistiques -->
    <div class="row">
        <!-- CA du jour -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                CA du jour
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($caJour, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CA du mois -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                CA du mois
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($caMois, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bénéfice du mois -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Bénéfice du mois
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($beneficeMois, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et alertes -->
    <div class="row">
        <!-- Graphique ventes par jour -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area"></i> Ventes des 7 derniers jours
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="ventesParJourChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top produits -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy"></i> Top 5 Produits
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($topProduits as $top)
                        <div class="row no-gutters align-items-center mb-3">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    {{ $top['produit']->nom }}
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                            {{ $top['quantite'] }}
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar bg-info" role="progressbar" 
                                                 style="width: {{ ($top['quantite'] / $topProduits->first()['quantite']) * 100 }}%"
                                                 aria-valuenow="{{ $top['quantite'] }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="{{ $topProduits->first()['quantite'] }}"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-muted">
                                    {{ number_format($top['ca'], 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Aucune vente enregistrée</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique produits et alertes -->
    <div class="row">
        <!-- Graphique ventes par produit -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Ventes par produit (Top 10)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="ventesParProduitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes stock -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle"></i> Alertes Stock
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($produitsEnRupture as $alerte)
                        <div class="alert alert-warning py-2 mb-2">
                            <div class="small">
                                <strong>{{ $alerte['produit']->nom }}</strong><br>
                                <span class="badge badge-{{ $alerte['type'] == 'Magasin' ? 'primary' : 'info' }}">
                                    {{ $alerte['type'] }}
                                </span>
                                {{ $alerte['lieu'] }}<br>
                                <small>Stock: {{ $alerte['quantite'] }} / Seuil: {{ $alerte['seuil'] }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted">
                            <i class="fas fa-check-circle fa-3x mb-2"></i>
                            <p>Aucune alerte de stock</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Données pour les graphiques
const ventesParJour = @json($ventesParJour);
const ventesParProduit = @json($ventesParProduit);

// Graphique ventes par jour
const ventesParJourCtx = document.getElementById('ventesParJourChart').getContext('2d');
const ventesParJourChart = new Chart(ventesParJourCtx, {
    type: 'line',
    data: {
        labels: ventesParJour.map(item => item.date),
        datasets: [{
            label: 'Chiffre d\'affaires (FCFA)',
            data: ventesParJour.map(item => item.ca),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }, {
            label: 'Nombre de ventes',
            data: ventesParJour.map(item => item.ventes),
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1,
            fill: true,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'CA (FCFA)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Ventes'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});

// Graphique ventes par produit
const ventesParProduitCtx = document.getElementById('ventesParProduitChart').getContext('2d');
const ventesParProduitChart = new Chart(ventesParProduitCtx, {
    type: 'bar',
    data: {
        labels: ventesParProduit.map(item => item.nom),
        datasets: [{
            label: 'Quantité vendue',
            data: ventesParProduit.map(item => item.quantite),
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Quantité'
                }
            }
        }
    }
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endsection
