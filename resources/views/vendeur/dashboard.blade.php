@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-store"></i> {{ $stats['boutique']->nom }}
            <small class="text-muted">{{ $stats['boutique']->localisation }}</small>
        </h1>
        <div class="text-muted">
            <i class="fas fa-clock"></i> {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card stat-card-success h-100">
                <div class="stat-card-icon"><i class="fas fa-boxes"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">Stock total</div>
                    <div class="stat-card-value">{{ number_format($stats['produits_en_stock'], 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card stat-card-warning h-100">
                <div class="stat-card-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">Alertes stock</div>
                    <div class="stat-card-value">{{ $stats['alertes_stock'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card stat-card-primary h-100">
                <div class="stat-card-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">Ventes du jour</div>
                    <div class="stat-card-value">{{ $stats['ventes_aujourd_hui'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Actions rapides -->
        <div class="col-lg-7 mb-4">
            <div class="panel-card h-100">
                <div class="panel-card-header">
                    <h6><i class="fas fa-bolt text-primary"></i> Actions rapides</h6>
                </div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('pos.index') }}" class="quick-action">
                                <div class="quick-action-icon quick-action-icon-success"><i class="fas fa-cash-register"></i></div>
                                <div class="quick-action-title">Nouvelle vente</div>
                                <div class="quick-action-desc">Ouvrir la caisse</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('pos.index') }}" class="quick-action">
                                <div class="quick-action-icon quick-action-icon-primary"><i class="fas fa-boxes"></i></div>
                                <div class="quick-action-title">Voir le stock</div>
                                <div class="quick-action-desc">Disponibilités produits</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('ventes.index') }}" class="quick-action">
                                <div class="quick-action-icon quick-action-icon-info"><i class="fas fa-history"></i></div>
                                <div class="quick-action-title">Historique</div>
                                <div class="quick-action-desc">Mes ventes</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance du mois -->
        <div class="col-lg-5 mb-4">
            <div class="panel-card h-100">
                <div class="panel-card-header">
                    <h6><i class="fas fa-chart-line text-primary"></i> Performance du mois</h6>
                </div>
                <div class="panel-card-body d-flex flex-column justify-content-center h-100 text-center">
                    <div class="text-muted small mb-1">Chiffre d'affaires du mois</div>
                    <div class="display-6 fw-bold text-success">{{ number_format($stats['ventes_mois'], 0, ',', ' ') }}</div>
                    <div class="text-muted small">FCFA</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: #fff; border-radius: 1rem; padding: 1.35rem 1.5rem;
    display: flex; align-items: center; gap: 1.1rem;
    box-shadow: 0 0.15rem 1.5rem rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.04);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    position: relative; overflow: hidden;
}
.stat-card::before { content: ''; position: absolute; top:0; left:0; right:0; height:4px; background: var(--accent); }
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 0.6rem 2rem rgba(0,0,0,0.1); }
.stat-card-icon {
    width: 54px; height: 54px; min-width: 54px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; background: var(--accent-bg); color: var(--accent);
}
.stat-card-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #858796; margin-bottom: 0.3rem; }
.stat-card-value { font-size: 1.5rem; font-weight: 700; color: #2e3440; line-height: 1.2; }
.stat-card-primary { --accent: #4e73df; --accent-bg: rgba(78,115,223,0.12); }
.stat-card-success { --accent: #1cc88a; --accent-bg: rgba(28,200,138,0.12); }
.stat-card-info    { --accent: #36b9cc; --accent-bg: rgba(54,185,204,0.12); }
.stat-card-warning { --accent: #f6c23e; --accent-bg: rgba(246,194,62,0.12); }

.panel-card { background: #fff; border-radius: 1rem; box-shadow: 0 0.15rem 1.5rem rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.04); overflow: hidden; }
.panel-card-header { padding: 1rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.06); }
.panel-card-header h6 { margin:0; font-weight:700; font-size:0.92rem; color:#2e3440; display:flex; align-items:center; gap:0.5rem; }
.panel-card-body { padding: 1.5rem; }

.quick-action {
    display:block; padding:1.1rem; border-radius:0.85rem; text-decoration:none;
    border:1px solid rgba(0,0,0,0.06); transition: all 0.2s ease; height:100%;
}
.quick-action:hover { transform: translateY(-3px); box-shadow: 0 0.4rem 1.2rem rgba(0,0,0,0.08); border-color: rgba(78,115,223,0.3); }
.quick-action-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.05rem; margin-bottom:0.7rem; }
.quick-action-icon-primary { background: rgba(78,115,223,0.12); color:#4e73df; }
.quick-action-icon-success { background: rgba(28,200,138,0.12); color:#1cc88a; }
.quick-action-icon-info { background: rgba(54,185,204,0.12); color:#36b9cc; }
.quick-action-title { font-weight:700; font-size:0.88rem; color:#2e3440; margin-bottom:0.2rem; }
.quick-action-desc { font-size:0.78rem; color:#858796; }
</style>
@endsection
