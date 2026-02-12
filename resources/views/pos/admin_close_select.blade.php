@extends('layouts.app')

@section('title', 'Fermer une caisse')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">Gestion des Caisses</a></li>
    <li class="breadcrumb-item active">Fermer une caisse</li>
@endsection

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">Fermer une caisse</h1>
        <p class="text-muted mb-0">Sélectionner la session de caisse à fermer</p>
    </div>
    <div>
        <a href="{{ route('pos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Sessions de caisse actives</h5>
            </div>
            <div class="card-body">
                @if($sessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Vendeur</th>
                                <th>Boutique</th>
                                <th>Ouverture</th>
                                <th>Montant initial</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                            <tr>
                                <td>
                                    <strong>{{ $session->vendeur->name }}</strong><br>
                                    <small class="text-muted">{{ $session->vendeur->email }}</small>
                                </td>
                                <td>
                                    <strong>{{ $session->boutique->nom }}</strong><br>
                                    <small class="text-muted">{{ $session->boutique->magasin->nom ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $session->date_ouverture->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($session->montant_initial, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <a href="{{ route('pos.close', ['vendeur_id' => $session->vendeur_id]) }}"
                                       class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-sign-out-alt me-1"></i>Fermer cette caisse
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
                    <p class="text-muted">Toutes les caisses sont déjà fermées</p>
                    <a href="{{ route('pos.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la gestion des caisses
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
