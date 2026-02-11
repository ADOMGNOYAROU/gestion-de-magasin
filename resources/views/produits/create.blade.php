@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Créer un Nouveau Produit</h1>
                <a href="{{ route('produits.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('produits.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom') }}" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categorie" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                    <select class="form-select @error('categorie') is-invalid @enderror" 
                                            id="categorie" name="categorie" required>
                                        <option value="">Sélectionner une catégorie</option>
                                        <option value="Électronique" {{ old('categorie') == 'Électronique' ? 'selected' : '' }}>Électronique</option>
                                        <option value="Vêtements" {{ old('categorie') == 'Vêtements' ? 'selected' : '' }}>Vêtements</option>
                                        <option value="Alimentation" {{ old('categorie') == 'Alimentation' ? 'selected' : '' }}>Alimentation</option>
                                        <option value="Maison" {{ old('categorie') == 'Maison' ? 'selected' : '' }}>Maison</option>
                                        <option value="Beauté" {{ old('categorie') == 'Beauté' ? 'selected' : '' }}>Beauté</option>
                                        <option value="Sport" {{ old('categorie') == 'Sport' ? 'selected' : '' }}>Sport</option>
                                        <option value="Livres" {{ old('categorie') == 'Livres' ? 'selected' : '' }}>Livres</option>
                                        <option value="Autre" {{ old('categorie') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('categorie')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prix_achat" class="form-label">Prix d'achat (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('prix_achat') is-invalid @enderror" 
                                           id="prix_achat" name="prix_achat" value="{{ old('prix_achat') }}" 
                                           step="0.01" min="0" required>
                                    @error('prix_achat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prix_vente" class="form-label">Prix de vente (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('prix_vente') is-invalid @enderror" 
                                           id="prix_vente" name="prix_vente" value="{{ old('prix_vente') }}" 
                                           step="0.01" min="0" required>
                                    @error('prix_vente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                    <select class="form-select @error('statut') is-invalid @enderror" 
                                            id="statut" name="statut" required>
                                        <option value="actif" {{ old('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Calcul de la marge en temps réel -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Marge estimée :</strong> 
                                    <span id="marge-estimee">0 FCFA (0%)</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('produits.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const prixAchat = document.getElementById('prix_achat');
    const prixVente = document.getElementById('prix_vente');
    const margeEstimee = document.getElementById('marge-estimee');

    function calculerMarge() {
        const achat = parseFloat(prixAchat.value) || 0;
        const vente = parseFloat(prixVente.value) || 0;
        
        if (achat > 0 && vente > 0) {
            const marge = vente - achat;
            const pourcentage = ((marge / achat) * 100).toFixed(1);
            margeEstimee.textContent = `${marge.toFixed(2)} FCFA (${pourcentage}%)`;
        } else {
            margeEstimee.textContent = '0 FCFA (0%)';
        }
    }

    prixAchat.addEventListener('input', calculerMarge);
    prixVente.addEventListener('input', calculerMarge);
});
</script>
@endsection
