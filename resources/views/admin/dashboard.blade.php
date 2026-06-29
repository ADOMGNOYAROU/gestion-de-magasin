@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-shield"></i> {{ __('messages.admin_dashboard') }}
        </h1>
        <div class="text-muted">
            <i class="fas fa-clock"></i> {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-primary h-100">
                <div class="stat-card-icon"><i class="fas fa-users"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">{{ __('messages.users') }}</div>
                    <div class="stat-card-value">{{ $stats['utilisateurs'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-warning h-100">
                <div class="stat-card-icon"><i class="fas fa-building"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">{{ __('messages.stores') }}</div>
                    <div class="stat-card-value">{{ $stats['magasins'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-success h-100">
                <div class="stat-card-icon"><i class="fas fa-store-alt"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">{{ __('messages.shops') }}</div>
                    <div class="stat-card-value">{{ $stats['boutiques'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-info h-100">
                <div class="stat-card-icon"><i class="fas fa-box"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">Produits</div>
                    <div class="stat-card-value">{{ $stats['produits'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-card-danger h-100">
                <div class="stat-card-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-card-body">
                    <div class="stat-card-label">Ventes aujourd'hui</div>
                    <div class="stat-card-value">{{ $stats['ventes_aujourd_hui'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section principale -->
    <div class="row">
        <!-- Répartition des utilisateurs -->
        <div class="col-lg-4 mb-4">
            <div class="panel-card h-100">
                <div class="panel-card-header">
                    <h6><i class="fas fa-user-tag text-primary"></i> Répartition des utilisateurs</h6>
                </div>
                <div class="panel-card-body">
                    <div class="role-row">
                        <span class="role-row-label"><span class="role-dot role-dot-admin"></span> Administrateurs</span>
                        <span class="role-row-count">{{ $stats['utilisateurs_par_role']['admin'] ?? 0 }}</span>
                    </div>
                    <div class="role-row">
                        <span class="role-row-label"><span class="role-dot role-dot-gestionnaire"></span> Gestionnaires</span>
                        <span class="role-row-count">{{ $stats['utilisateurs_par_role']['gestionnaire'] ?? 0 }}</span>
                    </div>
                    <div class="role-row">
                        <span class="role-row-label"><span class="role-dot role-dot-vendeur"></span> Vendeurs</span>
                        <span class="role-row-count">{{ $stats['utilisateurs_par_role']['vendeur'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="col-lg-8 mb-4">
            <div class="panel-card h-100">
                <div class="panel-card-header">
                    <h6><i class="fas fa-bolt text-primary"></i> Actions rapides</h6>
                </div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('users.index') }}" class="quick-action">
                                <div class="quick-action-icon quick-action-icon-primary"><i class="fas fa-users-cog"></i></div>
                                <div class="quick-action-title">Utilisateurs</div>
                                <div class="quick-action-desc">Gérer les comptes</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('magasins.index') }}" class="quick-action">
                                <div class="quick-action-icon quick-action-icon-warning"><i class="fas fa-building"></i></div>
                                <div class="quick-action-title">Magasins</div>
                                <div class="quick-action-desc">Gérer les magasins</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('boutiques.index') }}" class="quick-action">
                                <div class="quick-action-icon quick-action-icon-success"><i class="fas fa-store-alt"></i></div>
                                <div class="quick-action-title">Boutiques</div>
                                <div class="quick-action-desc">Gérer les boutiques</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('rapports.index') }}" class="quick-action">
                                <div class="quick-action-icon quick-action-icon-info"><i class="fas fa-file-alt"></i></div>
                                <div class="quick-action-title">Rapports</div>
                                <div class="quick-action-desc">Statistiques détaillées</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('produits.index') }}" class="quick-action">
                                <div class="quick-action-icon quick-action-icon-secondary"><i class="fas fa-box"></i></div>
                                <div class="quick-action-title">Produits</div>
                                <div class="quick-action-desc">Catalogue produits</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('notifications.index') }}" class="quick-action">
                                <div class="quick-action-icon quick-action-icon-danger"><i class="fas fa-bell"></i></div>
                                <div class="quick-action-title">Notifications</div>
                                <div class="quick-action-desc">Activité récente</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes de stock -->
    @if($stockAlerts->count() > 0)
    <div class="row">
        <div class="col-12 mb-4">
            <div class="panel-card">
                <div class="panel-card-header d-flex justify-content-between align-items-center">
                    <h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> Alertes de stock faible ({{ $stockAlerts->count() }})</h6>
                    <a href="{{ route('rapports.stock.pdf') }}" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-file-pdf me-1"></i>Rapport complet
                    </a>
                </div>
                <div class="panel-card-body">
                    <div class="row g-3">
                        @foreach($stockAlerts as $alert)
                        <div class="col-md-6 col-lg-4">
                            <div class="alert-item">
                                <div class="alert-item-icon"><i class="fas fa-exclamation-circle"></i></div>
                                <div class="alert-item-content">
                                    <div class="alert-item-title">{{ $alert->produit->nom }}</div>
                                    <div class="alert-item-meta">
                                        @if($alert instanceof \App\Models\StockMagasin)
                                            <span class="alert-item-badge alert-item-badge-primary">Magasin</span>
                                            <span>{{ $alert->magasin->nom }}</span>
                                        @else
                                            <span class="alert-item-badge alert-item-badge-info">Boutique</span>
                                            <span>{{ $alert->boutique->nom }}</span>
                                        @endif
                                    </div>
                                    <div class="alert-item-stock">{{ $alert->quantite }} restant(s)</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
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
.stat-card-danger  { --accent: #e74a3b; --accent-bg: rgba(231,74,59,0.12); }

.panel-card { background: #fff; border-radius: 1rem; box-shadow: 0 0.15rem 1.5rem rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.04); overflow: hidden; }
.panel-card-header { padding: 1rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.06); }
.panel-card-header h6 { margin:0; font-weight:700; font-size:0.92rem; color:#2e3440; display:flex; align-items:center; gap:0.5rem; }
.panel-card-body { padding: 1.5rem; }

.role-row { display:flex; justify-content:space-between; align-items:center; padding:0.6rem 0; border-bottom:1px solid rgba(0,0,0,0.05); }
.role-row:last-child { border-bottom:none; }
.role-row-label { font-size:0.85rem; color:#2e3440; display:flex; align-items:center; gap:0.5rem; }
.role-row-count { font-weight:700; color:#2e3440; }
.role-dot { width:10px; height:10px; border-radius:50%; display:inline-block; }
.role-dot-admin { background:#e74a3b; }
.role-dot-gestionnaire { background:#4e73df; }
.role-dot-vendeur { background:#1cc88a; }

.quick-action {
    display:block; padding:1.1rem; border-radius:0.85rem; text-decoration:none;
    border:1px solid rgba(0,0,0,0.06); transition: all 0.2s ease; height:100%;
}
.quick-action:hover { transform: translateY(-3px); box-shadow: 0 0.4rem 1.2rem rgba(0,0,0,0.08); border-color: rgba(78,115,223,0.3); }
.quick-action-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.05rem; margin-bottom:0.7rem; }
.quick-action-icon-primary { background: rgba(78,115,223,0.12); color:#4e73df; }
.quick-action-icon-warning { background: rgba(246,194,62,0.12); color:#f6c23e; }
.quick-action-icon-success { background: rgba(28,200,138,0.12); color:#1cc88a; }
.quick-action-icon-info { background: rgba(54,185,204,0.12); color:#36b9cc; }
.quick-action-icon-danger { background: rgba(231,74,59,0.12); color:#e74a3b; }
.quick-action-icon-secondary { background: rgba(133,135,150,0.12); color:#858796; }
.quick-action-title { font-weight:700; font-size:0.88rem; color:#2e3440; margin-bottom:0.2rem; }
.quick-action-desc { font-size:0.78rem; color:#858796; }

.alert-item { display:flex; gap:0.85rem; padding:0.75rem 0.9rem; border-radius:0.65rem; background: rgba(231,74,59,0.06); border-left:3px solid #e74a3b; height:100%; }
.alert-item-icon { color:#e74a3b; font-size:1.1rem; margin-top:0.1rem; }
.alert-item-title { font-weight:700; font-size:0.85rem; color:#2e3440; }
.alert-item-meta { display:flex; align-items:center; gap:0.4rem; font-size:0.75rem; color:#858796; margin:0.25rem 0; }
.alert-item-badge { font-size:0.65rem; font-weight:700; text-transform:uppercase; padding:0.15rem 0.5rem; border-radius:999px; color:#fff; }
.alert-item-badge-primary { background:#4e73df; }
.alert-item-badge-info { background:#36b9cc; }
.alert-item-stock { font-size:0.78rem; font-weight:700; color:#e74a3b; }
</style>
@endsection
