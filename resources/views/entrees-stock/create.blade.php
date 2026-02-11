@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Nouvelle Entrée de Stock</h1>
                <a href="{{ route('entrees-stock.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('entrees-stock.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="produit_id" class="form-label">Produit <span class="text-danger">*</span></label>
                                    <select class="form-select @error('produit_id') is-invalid @enderror" 
                                            id="produit_id" name="produit_id" required>
                                        <option value="">Sélectionner un produit</option>
                                        @foreach($produits as $produit)
                                            <option value="{{ $produit->id }}" 
                                                    {{ old('produit_id') == $produit->id ? 'selected' : '' }}
                                                    data-prix="{{ $produit->prix_achat }}"
                                                    data-categorie="{{ $produit->categorie }}">
                                                {{ $produit->nom }} - {{ $produit->categorie }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('produit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="magasin_id" class="form-label">Magasin <span class="text-danger">*</span></label>
                                    @if(isset($magasin))
                                        <!-- Gestionnaire : magasin prédéfini -->
                                        <input type="text" class="form-control" value="{{ $magasin->nom }}" readonly>
                                        <input type="hidden" name="magasin_id" value="{{ $magasin->id }}">
                                    @else
                                        <!-- Admin : choix du magasin -->
                                        <select class="form-select @error('magasin_id') is-invalid @enderror" 
                                                id="magasin_id" name="magasin_id" required>
                                            <option value="">Sélectionner un magasin</option>
                                            @foreach($magasins as $m)
                                                <option value="{{ $m->id }}" 
                                                        {{ old('magasin_id') == $m->id ? 'selected' : '' }}>
                                                    {{ $m->nom }} - {{ $m->localisation }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('magasin_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Source <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="fournisseur_id" class="form-label">Fournisseur</label>
                                            <select class="form-select @error('fournisseur_id') is-invalid @enderror" 
                                                    id="fournisseur_id" name="fournisseur_id">
                                                <option value="">Sélectionner un fournisseur</option>
                                                @foreach($fournisseurs as $fournisseur)
                                                    <option value="{{ $fournisseur->id }}" 
                                                            {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                                        {{ $fournisseur->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('fournisseur_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="partenaire_id" class="form-label">Partenaire</label>
                                            <select class="form-select @error('partenaire_id') is-invalid @enderror" 
                                                    id="partenaire_id" name="partenaire_id">
                                                <option value="">Sélectionner un partenaire</option>
                                                @foreach($partenaires as $partenaire)
                                                    <option value="{{ $partenaire->id }}" 
                                                            {{ old('partenaire_id') == $partenaire->id ? 'selected' : '' }}>
                                                        {{ $partenaire->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('partenaire_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <small class="text-muted">Sélectionnez un fournisseur OU un partenaire</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                           id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantite" class="form-label">Quantité <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('quantite') is-invalid @enderror" 
                                           id="quantite" name="quantite" value="{{ old('quantite') }}" 
                                           min="1" max="2147483647" required>
                                    @error('quantite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="prix_unitaire" class="form-label">Prix Unitaire (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('prix_unitaire') is-invalid @enderror" 
                                           id="prix_unitaire" name="prix_unitaire" value="{{ old('prix_unitaire') }}" 
                                           step="0.01" min="0" required>
                                    @error('prix_unitaire')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Total</label>
                                    <div class="form-control bg-light">
                                        <span id="total-calculé">0 FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du produit sélectionné -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Informations du produit</h6>
                                    <div id="info-produit">
                                        <p class="mb-0">Sélectionnez un produit pour voir ses informations</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('entrees-stock.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Enregistrer l'entrée
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
    const produitSelect = document.getElementById('produit_id');
    const quantiteInput = document.getElementById('quantite');
    const prixUnitaireInput = document.getElementById('prix_unitaire');
    const totalCalcule = document.getElementById('total-calculé');
    const infoProduit = document.getElementById('info-produit');

    function calculerTotal() {
        const quantite = parseFloat(quantiteInput.value) || 0;
        const prixUnitaire = parseFloat(prixUnitaireInput.value) || 0;
        const total = quantite * prixUnitaire;
        totalCalcule.textContent = `${total.toFixed(2)} FCFA`;
    }

    function afficherInfoProduit() {
        const selectedOption = produitSelect.options[produitSelect.selectedIndex];
        if (selectedOption.value) {
            const prix = selectedOption.dataset.prix;
            const categorie = selectedOption.dataset.categorie;
            
            infoProduit.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <strong>Catégorie :</strong> ${categorie}
                    </div>
                    <div class="col-md-4">
                        <strong>Prix d'achat standard :</strong> ${parseFloat(prix).toFixed(2)} FCFA
                    </div>
                    <div class="col-md-4">
                        <strong>Écart prix :</strong> 
                        <span id="ecart-prix" class="badge">
                            ${prixUnitaireInput.value ? (prixUnitaireInput.value - prix).toFixed(2) + ' FCFA' : '0 FCFA'}
                        </span>
                    </div>
                </div>
            `;
            
            // Mettre à jour la classe du badge selon l'écart
            const ecartPrixSpan = document.getElementById('ecart-prix');
            if (prixUnitaireInput.value && prixUnitaireInput.value > prix) {
                ecartPrixSpan.className = 'badge bg-danger';
            } else if (prixUnitaireInput.value && prixUnitaireInput.value < prix) {
                ecartPrixSpan.className = 'badge bg-success';
            } else {
                ecartPrixSpan.className = 'badge bg-secondary';
            }
            
            // Suggérer le prix standard si non renseigné
            if (!prixUnitaireInput.value) {
                prixUnitaireInput.value = prix;
                calculerTotal();
            }
        } else {
            infoProduit.innerHTML = '<p class="mb-0">Sélectionnez un produit pour voir ses informations</p>';
        }
    }

    produitSelect.addEventListener('change', function() {
        afficherInfoProduit();
        calculerTotal();
    });

    quantiteInput.addEventListener('input', calculerTotal);
    prixUnitaireInput.addEventListener('input', function() {
        calculerTotal();
        afficherInfoProduit(); // Pour mettre à jour l'écart
    });

    // Initialisation
    afficherInfoProduit();
    calculerTotal();
});
</script>
@endsection
