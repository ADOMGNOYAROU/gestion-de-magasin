@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Modifier le Partenaire</h1>
                <a href="{{ route('partenaires.index') }}" class="btn btn-secondary">
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
                            <h6 class="m-0 font-weight-bold text-primary">Informations du Partenaire</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('partenaires.update', $partenaire) }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nom" class="form-label">Nom du Partenaire <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                               id="nom" name="nom" value="{{ old('nom', $partenaire->nom) }}" required>
                                        @error('nom')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="adresse" class="form-label">Adresse <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('adresse') is-invalid @enderror"
                                               id="adresse" name="adresse" value="{{ old('adresse', $partenaire->adresse) }}" required>
                                        @error('adresse')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                               id="telephone" name="telephone" value="{{ old('telephone', $partenaire->telephone) }}" required>
                                        @error('telephone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               id="email" name="email" value="{{ old('email', $partenaire->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="type_partenariat" class="form-label">Type de Partenariat <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('type_partenariat') is-invalid @enderror"
                                           id="type_partenariat" name="type_partenariat" value="{{ old('type_partenariat', $partenaire->type_partenariat) }}" required>
                                    @error('type_partenariat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('partenaires.index') }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Mettre à Jour
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
                                <h6><i class="fas fa-info-circle"></i> À propos de la modification</h6>
                                <ul class="mb-0 small">
                                    <li>Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires</li>
                                    <li>Le nom du partenaire doit être unique</li>
                                    <li>Les informations de contact sont optionnelles</li>
                                    <li>Le type d'accord définit la nature de la relation</li>
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
