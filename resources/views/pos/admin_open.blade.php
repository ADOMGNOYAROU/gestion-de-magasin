@extends('layouts.app')

@section('title', 'Ouvrir une caisse')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">Gestion des Caisses</a></li>
    <li class="breadcrumb-item active">Ouvrir une caisse</li>
@endsection

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">Ouvrir une caisse</h1>
        <p class="text-muted mb-0">Sélectionner un vendeur et ouvrir sa session de caisse</p>
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
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Sélection du vendeur</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pos.store_open') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="vendeur_id" class="form-label">Vendeur</label>
                        <select class="form-select @error('vendeur_id') is-invalid @enderror"
                                id="vendeur_id" name="vendeur_id" required>
                            <option value="">-- Sélectionner un vendeur --</option>
                            @foreach($vendeurs as $vendeur)
                            <option value="{{ $vendeur->id }}"
                                    {{ old('vendeur_id') == $vendeur->id ? 'selected' : '' }}>
                                {{ $vendeur->name }} - {{ $vendeur->boutique ? $vendeur->boutique->nom : 'Aucune boutique' }}
                            </option>
                            @endforeach
                        </select>
                        @error('vendeur_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="montant_initial" class="form-label">Montant initial en caisse</label>
                        <input type="number" class="form-control @error('montant_initial') is-invalid @enderror"
                               id="montant_initial" name="montant_initial"
                               value="{{ old('montant_initial', 0) }}" min="0" step="0.01" required>
                        <div class="form-text">Montant d'argent présent dans la caisse au début de la session</div>
                        @error('montant_initial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Notes sur l'ouverture de la caisse...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-play me-2"></i>Ouvrir la caisse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
