@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Créer une Nouvelle Boutique</h1>
                <a href="{{ route('boutiques.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreurs de validation :</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Informations de la Boutique</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('boutiques.store') }}">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nom" class="form-label">Nom de la Boutique <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                               id="nom" name="nom" value="{{ old('nom') }}" required>
                                        @error('nom')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="telephone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                               id="telephone" name="telephone" value="{{ old('telephone') }}">
                                        @error('telephone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="adresse" class="form-label">Adresse <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('adresse') is-invalid @enderror"
                                              id="adresse" name="adresse" rows="3" required>{{ old('adresse') }}</textarea>
                                    @error('adresse')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="magasin_id" class="form-label">Magasin <span class="text-danger">*</span></label>
                                        <select class="form-select @error('magasin_id') is-invalid @enderror"
                                                id="magasin_id" name="magasin_id" required>
                                            <option value="">Sélectionner un magasin</option>
                                            @foreach($magasins as $magasin)
                                                <option value="{{ $magasin->id }}" {{ old('magasin_id') == $magasin->id ? 'selected' : '' }}>
                                                    {{ $magasin->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('magasin_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="vendeur_id" class="form-label">Vendeur Responsable</label>
                                        <select class="form-select @error('vendeur_id') is-invalid @enderror"
                                                id="vendeur_id" name="vendeur_id">
                                            <option value="">Sélectionner un vendeur (optionnel)</option>
                                            @foreach($vendeurs as $vendeur)
                                                <option value="{{ $vendeur->id }}" {{ old('vendeur_id') == $vendeur->id ? 'selected' : '' }}>
                                                    {{ $vendeur->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vendeur_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('boutiques.index') }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Créer la Boutique
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-info">Informations</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> À propos de la création</h6>
                                <ul class="mb-0 small">
                                    <li>Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires</li>
                                    <li>Le vendeur responsable peut être assigné ultérieurement</li>
                                    <li>La boutique sera créée dans le magasin sélectionné</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
