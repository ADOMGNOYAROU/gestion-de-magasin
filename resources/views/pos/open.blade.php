@extends('layouts.app')

@section('title', 'Ouvrir la caisse')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">Caisse</a></li>
    <li class="breadcrumb-item active">Ouvrir la caisse</li>
@endsection

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">Ouvrir la caisse</h1>
        <p class="text-muted mb-0">Boutique: {{ $boutique->nom }}</p>
    </div>
    <div>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour au tableau de bord
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-cash-register me-2"></i>
                    Ouverture de session de caisse
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pos.store_open') }}" method="POST">
                    @csrf

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Information:</strong> Vous devez saisir le montant initial en caisse pour commencer vos ventes.
                        Ce montant sera utilisé pour calculer les écarts lors de la fermeture.
                    </div>

                    <div class="mb-4">
                        <label for="montant_initial" class="form-label fw-bold">
                            Montant initial en caisse <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="number" class="form-control text-end"
                                   id="montant_initial" name="montant_initial"
                                   placeholder="0" min="0" step="0.01" required
                                   value="{{ old('montant_initial') }}">
                            <span class="input-group-text">FCFA</span>
                        </div>
                        @error('montant_initial')
                            <div class="text-danger mt-1">
                                <small>{{ $message }}</small>
                            </div>
                        @enderror
                        <div class="form-text">
                            Saisissez le montant exact que vous avez en caisse au début de la session.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-bold">Notes (optionnel)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Ajoutez des notes pour cette session de caisse..."
                                  maxlength="500">{{ old('notes') }}</textarea>
                        <div class="form-text">
                            Maximum 500 caractères.
                        </div>
                        @error('notes')
                            <div class="text-danger mt-1">
                                <small>{{ $message }}</small>
                            </div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-play me-2"></i>
                            Ouvrir la caisse
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-light">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="text-muted small">Boutique</div>
                        <div class="fw-bold">{{ $boutique->nom }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Responsable</div>
                        <div class="fw-bold">{{ $boutique->vendeur ? $boutique->vendeur->name : 'Non assigné' }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Date</div>
                        <div class="fw-bold">{{ now()->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations supplémentaires -->
        <div class="card mt-4 border-warning">
            <div class="card-body">
                <h6 class="card-title text-warning">
                    <i class="fas fa-lightbulb me-2"></i>
                    Conseils pour l'ouverture de caisse
                </h6>
                <ul class="mb-0 small">
                    <li>Vérifiez que vous avez suffisamment de monnaie pour rendre la monnaie</li>
                    <li>Comptez précisément le montant initial en présence d'un témoin si possible</li>
                    <li>Notez tout écart important dans les notes pour référence future</li>
                    <li>Une session de caisse reste ouverte jusqu'à ce que vous la fermiez explicitement</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
