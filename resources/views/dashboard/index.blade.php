@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- En-tête du dashboard -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt"></i>
            {{ __('messages.dashboard') }}
            @if(isset($magasin))
                <small class="text-muted">- {{ $magasin->nom }}</small>
            @elseif(isset($boutique))
                <small class="text-muted">- {{ $boutique->nom }}</small>
            @endif
        </h1>
        <div>
            <a href="{{ route('rapports.index') }}" class="btn btn-primary me-2 {{ hideIfCannot('manage-rapports') }}">
                <i class="fas fa-file-alt"></i> {{ __('messages.reports') }}
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
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-primary h-100">
                <div class="stat-card-icon"><i class="fas fa-box"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">{{ __('messages.active_products') }}</div>
                    <div class="stat-card-value">{{ number_format($totalProduits, 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-success h-100">
                <div class="stat-card-icon"><i class="fas fa-warehouse"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">{{ __('messages.store_stock') }}</div>
                    <div class="stat-card-value">{{ number_format($stockTotalMagasin, 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-info h-100">
                <div class="stat-card-icon"><i class="fas fa-store"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">{{ __('messages.shops_stock') }}</div>
                    <div class="stat-card-value">{{ number_format($stockTotalBoutiques, 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-warning h-100">
                <div class="stat-card-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">{{ __('messages.today_sales') }}</div>
                    <div class="stat-card-value">{{ $ventesJour }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne de statistiques -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card stat-card-primary h-100">
                <div class="stat-card-icon"><i class="fas fa-coins"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">{{ __('messages.today_revenue') }}</div>
                    <div class="stat-card-value">{{ number_format($caJour, 0, ',', ' ') }} <span class="stat-card-unit">FCFA</span></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card stat-card-success h-100">
                <div class="stat-card-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">{{ __('messages.monthly_revenue') }}</div>
                    <div class="stat-card-value">{{ number_format($caMois, 0, ',', ' ') }} <span class="stat-card-unit">FCFA</span></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card stat-card-info h-100">
                <div class="stat-card-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">{{ __('messages.monthly_profit') }}</div>
                    <div class="stat-card-value">{{ number_format($beneficeMois, 0, ',', ' ') }} <span class="stat-card-unit">FCFA</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et alertes -->
    <div class="row">
        <!-- Graphique ventes par jour -->
        <div class="col-xl-8 col-lg-7">
            <div class="panel-card mb-4">
                <div class="panel-card-header">
                    <h6><i class="fas fa-chart-area text-primary"></i> {{ __('messages.sales_last_7_days') }}</h6>
                </div>
                <div class="panel-card-body">
                    <div class="chart-area">
                        <canvas id="ventesParJourChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top produits -->
        <div class="col-xl-4 col-lg-5">
            <div class="panel-card mb-4">
                <div class="panel-card-header">
                    <h6><i class="fas fa-trophy text-primary"></i> {{ __('messages.top_5_products') }}</h6>
                </div>
                <div class="panel-card-body">
                    @forelse($topProduits as $i => $top)
                        <div class="rank-item">
                            <div class="rank-badge rank-{{ $i + 1 <= 3 ? $i + 1 : 'default' }}">{{ $i + 1 }}</div>
                            <div class="rank-content">
                                <div class="rank-name">{{ $top['produit']->nom }}</div>
                                <div class="rank-progress">
                                    <div class="rank-progress-bar" style="width: {{ ($top['quantite'] / $topProduits->first()['quantite']) * 100 }}%"></div>
                                </div>
                                <div class="rank-meta">
                                    <span>{{ $top['quantite'] }} {{ __('messages.units') ?? 'unités' }}</span>
                                    <span class="rank-ca">{{ number_format($top['ca'], 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-chart-bar"></i>
                            <p>Aucune vente enregistrée</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique produits et alertes -->
    <div class="row">
        <!-- Graphique ventes par produit -->
        <div class="col-xl-8 col-lg-7">
            <div class="panel-card mb-4">
                <div class="panel-card-header">
                    <h6><i class="fas fa-chart-bar text-primary"></i> {{ __('messages.sales_by_product_top_10') }}</h6>
                </div>
                <div class="panel-card-body">
                    <div class="chart-bar">
                        <canvas id="ventesParProduitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes stock -->
        <div class="col-xl-4 col-lg-5">
            <div class="panel-card mb-4">
                <div class="panel-card-header">
                    <h6><i class="fas fa-exclamation-triangle text-warning"></i> {{ __('messages.stock_alerts') }}</h6>
                </div>
                <div class="panel-card-body">
                    @forelse($produitsEnRupture as $alerte)
                        <div class="alert-item">
                            <div class="alert-item-icon"><i class="fas fa-exclamation-circle"></i></div>
                            <div class="alert-item-content">
                                <div class="alert-item-title">{{ $alerte['produit']->nom }}</div>
                                <div class="alert-item-meta">
                                    <span class="alert-item-badge alert-item-badge-{{ $alerte['type'] == 'Magasin' ? 'primary' : 'info' }}">
                                        {{ $alerte['type'] }}
                                    </span>
                                    <span>{{ $alerte['lieu'] }}</span>
                                </div>
                                <div class="alert-item-stock">Stock : <strong>{{ $alerte['quantite'] }}</strong> / Seuil : {{ $alerte['seuil'] }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state empty-state-success">
                            <i class="fas fa-check-circle"></i>
                            <p>{{ __('messages.no_stock_alerts') }}</p>
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

Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#858796';

// Graphique ventes par jour (zone dégradée)
const ventesParJourCtx = document.getElementById('ventesParJourChart').getContext('2d');
const caGradient = ventesParJourCtx.createLinearGradient(0, 0, 0, 280);
caGradient.addColorStop(0, 'rgba(78, 115, 223, 0.35)');
caGradient.addColorStop(1, 'rgba(78, 115, 223, 0)');
const ventesGradient = ventesParJourCtx.createLinearGradient(0, 0, 0, 280);
ventesGradient.addColorStop(0, 'rgba(28, 200, 138, 0.25)');
ventesGradient.addColorStop(1, 'rgba(28, 200, 138, 0)');

const ventesParJourChart = new Chart(ventesParJourCtx, {
    type: 'line',
    data: {
        labels: ventesParJour.map(item => item.date),
        datasets: [{
            label: 'Chiffre d\'affaires (FCFA)',
            data: ventesParJour.map(item => item.ca),
            borderColor: '#4e73df',
            backgroundColor: caGradient,
            pointBackgroundColor: '#4e73df',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            borderWidth: 2.5,
            tension: 0.4,
            fill: true
        }, {
            label: 'Nombre de ventes',
            data: ventesParJour.map(item => item.ventes),
            borderColor: '#1cc88a',
            backgroundColor: ventesGradient,
            pointBackgroundColor: '#1cc88a',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            borderWidth: 2.5,
            tension: 0.4,
            fill: true,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                position: 'top',
                align: 'end',
                labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, padding: 16 }
            },
            tooltip: {
                backgroundColor: '#1e293b',
                titleColor: '#fff',
                bodyColor: '#e2e8f0',
                padding: 12,
                cornerRadius: 8,
                displayColors: true,
            }
        },
        scales: {
            x: { grid: { display: false } },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                grid: { color: 'rgba(0,0,0,0.05)' },
                title: { display: true, text: 'CA (FCFA)' }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: { display: true, text: 'Ventes' },
                grid: { drawOnChartArea: false }
            }
        }
    }
});

// Graphique ventes par produit (barres arrondies en dégradé)
const ventesParProduitCtx = document.getElementById('ventesParProduitChart').getContext('2d');
const barGradient = ventesParProduitCtx.createLinearGradient(0, 0, 0, 280);
barGradient.addColorStop(0, '#60a5fa');
barGradient.addColorStop(1, '#3b82f6');

const ventesParProduitChart = new Chart(ventesParProduitCtx, {
    type: 'bar',
    data: {
        labels: ventesParProduit.map(item => item.nom),
        datasets: [{
            label: 'Quantité vendue',
            data: ventesParProduit.map(item => item.quantite),
            backgroundColor: barGradient,
            borderRadius: 8,
            borderSkipped: false,
            maxBarThickness: 36
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                titleColor: '#fff',
                bodyColor: '#e2e8f0',
                padding: 12,
                cornerRadius: 8,
            }
        },
        scales: {
            x: { grid: { display: false } },
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.05)' },
                title: { display: true, text: 'Quantité' }
            }
        }
    }
});
</script>

<style>
/* Cartes de statistiques */
.stat-card {
    background: #fff;
    border-radius: 1rem;
    padding: 1.35rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.1rem;
    box-shadow: 0 0.15rem 1.5rem rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.04);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: var(--accent);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.6rem 2rem rgba(0, 0, 0, 0.1);
}

.stat-card-icon {
    width: 54px;
    height: 54px;
    min-width: 54px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    background: var(--accent-bg);
    color: var(--accent);
}

.stat-card-body { overflow: hidden; }

.stat-card-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #858796;
    margin-bottom: 0.3rem;
    white-space: nowrap;
}

.stat-card-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2e3440;
    line-height: 1.2;
}

.stat-card-unit {
    font-size: 0.8rem;
    font-weight: 600;
    color: #858796;
}

.stat-card-primary { --accent: #4e73df; --accent-bg: rgba(78, 115, 223, 0.12); }
.stat-card-success { --accent: #1cc88a; --accent-bg: rgba(28, 200, 138, 0.12); }
.stat-card-info    { --accent: #36b9cc; --accent-bg: rgba(54, 185, 204, 0.12); }
.stat-card-warning { --accent: #f6c23e; --accent-bg: rgba(246, 194, 62, 0.12); }
.stat-card-danger  { --accent: #e74a3b; --accent-bg: rgba(231, 74, 59, 0.12); }

/* Panneaux (graphiques, listes) */
.panel-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 0.15rem 1.5rem rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.04);
    overflow: hidden;
}

.panel-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.panel-card-header h6 {
    margin: 0;
    font-weight: 700;
    font-size: 0.92rem;
    color: #2e3440;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.panel-card-body { padding: 1.5rem; }

/* Classement Top produits */
.rank-item {
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.rank-item:last-child { border-bottom: none; }

.rank-badge {
    width: 30px;
    height: 30px;
    min-width: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    color: #fff;
    background: #d1d5db;
}

.rank-badge.rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
.rank-badge.rank-2 { background: linear-gradient(135deg, #cbd5e1, #94a3b8); }
.rank-badge.rank-3 { background: linear-gradient(135deg, #fdba74, #d97706); }

.rank-content { flex: 1; min-width: 0; }

.rank-name {
    font-size: 0.85rem;
    font-weight: 700;
    color: #2e3440;
    margin-bottom: 0.4rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.rank-progress {
    height: 6px;
    border-radius: 999px;
    background: rgba(78, 115, 223, 0.12);
    overflow: hidden;
    margin-bottom: 0.4rem;
}

.rank-progress-bar {
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #60a5fa, #4e73df);
}

.rank-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: #858796;
}

.rank-ca { font-weight: 600; color: #1cc88a; }

/* Alertes stock */
.alert-item {
    display: flex;
    gap: 0.85rem;
    padding: 0.75rem 0.9rem;
    border-radius: 0.65rem;
    background: rgba(246, 194, 62, 0.08);
    border-left: 3px solid #f6c23e;
    margin-bottom: 0.65rem;
}

.alert-item-icon {
    color: #f6c23e;
    font-size: 1.1rem;
    margin-top: 0.1rem;
}

.alert-item-title {
    font-weight: 700;
    font-size: 0.85rem;
    color: #2e3440;
}

.alert-item-meta {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;
    color: #858796;
    margin: 0.25rem 0;
}

.alert-item-badge {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
    color: #fff;
}

.alert-item-badge-primary { background: #4e73df; }
.alert-item-badge-info { background: #36b9cc; }

.alert-item-stock {
    font-size: 0.75rem;
    color: #858796;
}

/* États vides */
.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: #858796;
}

.empty-state i {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
    opacity: 0.4;
}

.empty-state-success i { color: #1cc88a; opacity: 0.6; }

.chart-area { position: relative; height: 320px; }
.chart-bar { position: relative; height: 320px; }
</style>
@endsection
