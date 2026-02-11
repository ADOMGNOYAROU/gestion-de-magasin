@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-file-alt"></i> Rapports et Export
                </h1>
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

            <!-- Cartes de rapports -->
            <div class="row">
                <!-- Rapport de stock -->
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="icon-circle bg-primary mb-3">
                                    <i class="fas fa-warehouse text-white fa-2x"></i>
                                </div>
                                <h5 class="card-title">Rapport de Stock</h5>
                                <p class="card-text text-muted">
                                    Exportez l'état complet du stock (magasin et boutiques)
                                </p>
                                <div class="d-grid">
                                    <a href="{{ route('rapports.stock.pdf') }}" class="btn btn-primary">
                                        <i class="fas fa-file-pdf"></i> Générer PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rapport de ventes -->
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="icon-circle bg-success mb-3">
                                    <i class="fas fa-chart-line text-white fa-2x"></i>
                                </div>
                                <h5 class="card-title">Rapport de Ventes</h5>
                                <p class="card-text text-muted">
                                    Exportez les ventes par période avec détails et totaux
                                </p>
                                <div class="d-grid">
                                    <a href="{{ route('rapports.ventes.form') }}" class="btn btn-success">
                                        <i class="fas fa-calendar-alt"></i> Personnaliser
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rapport partenaires -->
                @if(Auth::user()->isAdmin() || Auth::user()->isGestionnaire())
                    <div class="col-xl-4 col-lg-6 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-body">
                                <div class="text-center">
                                    <div class="icon-circle bg-info mb-3">
                                        <i class="fas fa-handshake text-white fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Rapport Partenaires</h5>
                                    <p class="card-text text-muted">
                                        Exportez les achats par partenaire et dépenses
                                    </p>
                                    <div class="d-grid">
                                        <a href="{{ route('rapports.partenaires.pdf') }}" class="btn btn-info">
                                            <i class="fas fa-file-pdf"></i> Générer PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Informations sur les rapports -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle"></i> Informations sur les rapports
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-primary">
                                        <i class="fas fa-warehouse"></i> Rapport de Stock
                                    </h6>
                                    <ul class="small">
                                        <li>Liste complète des produits actifs</li>
                                        <li>Quantités en stock (magasin + boutiques)</li>
                                        <li>Seuils d'alerte et statuts</li>
                                        <li>Total général par emplacement</li>
                                        <li>Date et heure de génération</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success">
                                        <i class="fas fa-chart-line"></i> Rapport de Ventes
                                    </h6>
                                    <ul class="small">
                                        <li>Période personnalisable</li>
                                        <li>Filtres par magasin et/ou boutique</li>
                                        <li>Détail par produit et boutique</li>
                                        <li>Chiffre d'affaires et bénéfices</li>
                                        <li>Export PDF et Excel</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-info">
                                        <i class="fas fa-handshake"></i> Rapport Partenaires
                                    </h6>
                                    <ul class="small">
                                        <li>Liste des partenaires actifs</li>
                                        <li>Historique des achats par partenaire</li>
                                        <li>Total dépensé par partenaire</li>
                                        <li>Produits achetés et quantités</li>
                                        <li>Disponible pour Admin et Gestionnaire</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accès rapide -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-bolt"></i> Accès Rapide
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="d-grid">
                                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-tachometer-alt"></i> Dashboard
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-grid">
                                        <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-box"></i> Produits
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-grid">
                                        <a href="{{ route('ventes.index') }}" class="btn btn-outline-success">
                                            <i class="fas fa-shopping-cart"></i> Ventes
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-grid">
                                        <a href="{{ route('entrees-stock.index') }}" class="btn btn-outline-warning">
                                            <i class="fas fa-plus-circle"></i> Entrées Stock
                                        </a>
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

<style>
.icon-circle {
    height: 4rem;
    width: 4rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
</style>
@endsection
