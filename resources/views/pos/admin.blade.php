@extends('layouts.app')

@section('title', 'Gestion des Caisses')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item active">Gestion des Caisses</li>
@endsection

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">Gestion des Caisses</h1>
        <p class="text-muted mb-0">Ouvrir et fermer les sessions de caisse des vendeurs</p>
    </div>
    <div>
        <a href="{{ route('pos.open') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Ouvrir une caisse
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row g-4">
    <!-- Test simple -->
    <div class="col-12">
        <div class="alert alert-info">
            <h5>Test de fonctionnement</h5>
            <p>Interface de gestion des caisses chargée avec succès !</p>
            <ul>
                <li>Utilisateur: {{ Auth::user()->name }}</li>
                <li>Rôle: {{ Auth::user()->role }}</li>
                <li>Boutiques disponibles: {{ $boutiques->count() ?? 0 }}</li>
                <li>Sessions actives: {{ $sessionsActives->count() ?? 0 }}</li>
            </ul>
        </div>
    </div>

    <!-- Boutiques -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Boutiques</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @forelse($boutiques ?? collect() as $boutique)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $boutique->nom }}</strong><br>
                            <small class="text-muted">{{ $boutique->magasin->nom ?? 'Aucun magasin' }}</small>
                        </div>
                        <span class="badge bg-secondary">vendeurs</span>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted">
                        Aucune boutique trouvée
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions de caisse actives -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sessions de caisse actives</h5>
                <a href="{{ route('pos.close') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Fermer une caisse
                </a>
            </div>
            <div class="card-body">
                @if(($sessionsActives ?? collect())->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Vendeur</th>
                                <th>Boutique</th>
                                <th>Ouverture</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessionsActives as $session)
                            <tr>
                                <td>
                                    <strong>{{ $session->vendeur->name }}</strong><br>
                                    <small class="text-muted">{{ $session->vendeur->email }}</small>
                                </td>
                                <td>{{ $session->boutique->nom }}</td>
                                <td>{{ $session->date_ouverture->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $session->status === 'ouverte' ? 'success' : 'warning' }}">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('pos.close', ['vendeur_id' => $session->vendeur_id]) }}"
                                       class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-sign-out-alt me-1"></i>Fermer
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-cash-register fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune session de caisse active</h5>
                    <p class="text-muted">Toutes les caisses sont fermées</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
