@extends('layouts.app')

@section('title', 'Fermer la caisse - ' . $session->vendeur->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">Gestion des Caisses</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pos.close') }}">Fermer une caisse</a></li>
    <li class="breadcrumb-item active">{{ $session->vendeur->name }}</li>
@endsection

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">Fermer la caisse</h1>
        <p class="text-muted mb-0">Session de {{ $session->vendeur->name }} - {{ $session->boutique->nom }}</p>
    </div>
    <div>
        <a href="{{ route('pos.close') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Fermeture de caisse</h5>
            </div>
            <div class="card-body">
                <!-- Informations de la session -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-2">Informations de la session</h6>
                            <p class="mb-1"><strong>Vendeur:</strong> {{ $session->vendeur->name }}</p>
                            <p class="mb-1"><strong>Boutique:</strong> {{ $session->boutique->nom }}</p>
                            <p class="mb-1"><strong>Ouverture:</strong> {{ $session->date_ouverture->format('d/m/Y H:i') }}</p>
                            <p class="mb-0"><strong>Montant initial:</strong> {{ number_format($session->montant_initial, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-success mb-2">Calculs théoriques</h6>
                            <p class="mb-1"><strong>Montant théorique:</strong> {{ number_format($session->montant_theorique ?? 0, 0, ',', ' ') }} FCFA</p>
                            <p class="mb-1"><strong>Écart:</strong>
                                <span class="text-{{ ($session->ecart ?? 0) >= 0 ? 'success' : 'danger' }}">
                                    {{ number_format($session->ecart ?? 0, 0, ',', ' ') }} FCFA
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('pos.store_close') }}" method="POST">
                    @csrf
                    <input type="hidden" name="session_id" value="{{ $session->id }}">

                    <div class="mb-3">
                        <label for="montant_final" class="form-label">Montant final en caisse</label>
                        <input type="number" class="form-control @error('montant_final') is-invalid @enderror"
                               id="montant_final" name="montant_final"
                               value="{{ old('montant_final') }}" min="0" step="0.01" required>
                        <div class="form-text">Montant d'argent compté physiquement dans la caisse</div>
                        @error('montant_final')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes de fermeture (optionnel)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Remarques sur la fermeture de la caisse...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Résumé -->
                    <div class="alert alert-info">
                        <h6>Résumé de la session:</h6>
                        <p class="mb-0">
                            <strong>Montant initial:</strong> {{ number_format($session->montant_initial, 0, ',', ' ') }} FCFA<br>
                            <strong>Montant théorique:</strong> {{ number_format($session->montant_theorique ?? 0, 0, ',', ' ') }} FCFA<br>
                            <strong>Écart calculé:</strong> {{ number_format($session->ecart ?? 0, 0, ',', ' ') }} FCFA
                        </p>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Fermer la caisse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
