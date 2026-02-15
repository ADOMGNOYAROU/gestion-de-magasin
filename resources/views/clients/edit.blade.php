@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-edit"></i> Modifier le Client
                        </h4>
                        <div>
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('clients.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                           id="nom" name="nom" value="{{ old('nom', $client->nom) }}" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control @error('prenom') is-invalid @enderror"
                                           id="prenom" name="prenom" value="{{ old('prenom', $client->prenom) }}">
                                    @error('prenom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $client->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                           id="telephone" name="telephone" value="{{ old('telephone', $client->telephone) }}">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_naissance" class="form-label">Date de naissance</label>
                                    <input type="date" class="form-control @error('date_naissance') is-invalid @enderror"
                                           id="date_naissance" name="date_naissance" value="{{ old('date_naissance', $client->date_naissance?->format('Y-m-d')) }}">
                                    @error('date_naissance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sexe" class="form-label">Sexe</label>
                                    <select class="form-control @error('sexe') is-invalid @enderror" id="sexe" name="sexe">
                                        <option value="">Non spécifié</option>
                                        <option value="M" {{ old('sexe', $client->sexe) == 'M' ? 'selected' : '' }}>Masculin</option>
                                        <option value="F" {{ old('sexe', $client->sexe) == 'F' ? 'selected' : '' }}>Féminin</option>
                                    </select>
                                    @error('sexe')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ville" class="form-label">Ville</label>
                                    <input type="text" class="form-control @error('ville') is-invalid @enderror"
                                           id="ville" name="ville" value="{{ old('ville', $client->ville) }}">
                                    @error('ville')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code_postal" class="form-label">Code postal</label>
                                    <input type="text" class="form-control @error('code_postal') is-invalid @enderror"
                                           id="code_postal" name="code_postal" value="{{ old('code_postal', $client->code_postal) }}">
                                    @error('code_postal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror"
                                      id="adresse" name="adresse" rows="3">{{ old('adresse', $client->adresse) }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="statut" class="form-label">Statut</label>
                                    <select class="form-control @error('statut') is-invalid @enderror" id="statut" name="statut">
                                        <option value="actif" {{ old('statut', $client->statut) == 'actif' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactif" {{ old('statut', $client->statut) == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Préférences de notification</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="notifications_email"
                                               name="notifications_email" value="1" {{ old('notifications_email', $client->preferences?->notifications_email ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="notifications_email">
                                            Accepter les notifications par email
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="notifications_sms"
                                               name="notifications_sms" value="1" {{ old('notifications_sms', $client->preferences?->notifications_sms ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="notifications_sms">
                                            Accepter les notifications par SMS
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format telephone number
    document.getElementById('telephone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + ' ' + value.substring(2);
        }
        if (value.length >= 5) {
            value = value.substring(0, 5) + ' ' + value.substring(5);
        }
        if (value.length >= 8) {
            value = value.substring(0, 8) + ' ' + value.substring(8);
        }
        e.target.value = value.substring(0, 11);
    });
});
</script>
