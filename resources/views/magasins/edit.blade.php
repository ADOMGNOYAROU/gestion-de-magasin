@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Modifier le Magasin</h1>
                <a href="{{ route('magasins.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('magasins.update', $magasin) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom du magasin *</label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                           id="nom" name="nom" value="{{ old('nom', $magasin->nom) }}" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="localisation" class="form-label">Localisation *</label>
                                    <input type="text" class="form-control @error('localisation') is-invalid @enderror"
                                           id="localisation" name="localisation" value="{{ old('localisation', $magasin->localisation) }}" required>
                                    @error('localisation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="responsable_id" class="form-label">Responsable *</label>
                            <select class="form-select @error('responsable_id') is-invalid @enderror" id="responsable_id" name="responsable_id" required>
                                <option value="">Sélectionner un responsable</option>
                                @foreach($responsables as $responsable)
                                    <option value="{{ $responsable->id }}" {{ old('responsable_id', $magasin->responsable_id) == $responsable->id ? 'selected' : '' }}>
                                        {{ $responsable->name }} ({{ $responsable->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('responsable_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour le magasin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
