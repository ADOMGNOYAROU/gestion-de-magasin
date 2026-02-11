@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Nouveau Transfert de Stock</h1>
                <a href="{{ route('transferts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('transferts.store') }}">
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
                                    <label for="magasin_id" class="form-label">Magasin Source <span class="text-danger">*</span></label>
                                    <select class="form-select @error('magasin_id') is-invalid @enderror" 
                                            id="magasin_id" name="magasin_id" required>
                                        <option value="">Sélectionner un magasin</option>
                                        @foreach($magasins as $magasin)
                                            <option value="{{ $magasin->id }}" 
                                                    {{ old('magasin_id') == $magasin->id ? 'selected' : '' }}>
                                                {{ $magasin->nom }} - {{ $magasin->localisation }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('magasin_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="boutique_id" class="form-label">Boutique Destination <span class="text-danger">*</span></label>
                                    <select class="form-select @error('boutique_id') is-invalid @enderror" 
                                            id="boutique_id" name="boutique_id" required disabled>
                                        <option value="">Sélectionnez d'abord un magasin</option>
                                    </select>
                                    @error('boutique_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantite" class="form-label">Quantité à Transférer <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('quantite') is-invalid @enderror" 
                                           id="quantite" name="quantite" value="{{ old('quantite') }}" 
                                           min="1" required>
                                    @error('quantite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Stock Disponible</label>
                                    <div class="form-control bg-light" id="stock-info">
                                        <span id="stock-disponible">-</span>
                                        <small class="text-muted" id="stock-alerte"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du produit et validation -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" id="info-produit">
                                    <h6><i class="fas fa-info-circle"></i> Informations</h6>
                                    <p class="mb-0">Sélectionnez un produit et un magasin pour voir les informations de stock</p>
                                </div>
                            </div>
                        </div>

                        <!-- Validation en temps réel -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert" id="validation-message" style="display: none;">
                                    <h6><i class="fas fa-exclamation-triangle"></i> Validation</h6>
                                    <p class="mb-0" id="validation-text"></p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('transferts.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                <i class="fas fa-exchange-alt"></i> Effectuer le transfert
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
    const magasinSelect = document.getElementById('magasin_id');
    const boutiqueSelect = document.getElementById('boutique_id');
    const quantiteInput = document.getElementById('quantite');
    const stockDisponible = document.getElementById('stock-disponible');
    const stockAlerte = document.getElementById('stock-alerte');
    const infoProduit = document.getElementById('info-produit');
    const validationMessage = document.getElementById('validation-message');
    const validationText = document.getElementById('validation-text');
    const submitBtn = document.getElementById('submit-btn');

    let currentStock = 0;

    function updateBoutiques() {
        const magasinId = magasinSelect.value;
        
        if (magasinId) {
            fetch(`/api/boutiques-par-magasin?magasin_id=${magasinId}`)
                .then(response => response.json())
                .then(boutiques => {
                    boutiqueSelect.innerHTML = '<option value="">Sélectionner une boutique</option>';
                    boutiques.forEach(boutique => {
                        boutiqueSelect.innerHTML += `<option value="${boutique.id}">${boutique.nom}</option>`;
                    });
                    boutiqueSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
        } else {
            boutiqueSelect.innerHTML = '<option value="">Sélectionnez d\'abord un magasin</option>';
            boutiqueSelect.disabled = true;
        }
        
        updateStockInfo();
    }

    function updateStockInfo() {
        const produitId = produitSelect.value;
        const magasinId = magasinSelect.value;
        
        if (produitId && magasinId) {
            fetch(`/api/stock-disponible?produit_id=${produitId}&magasin_id=${magasinId}`)
                .then(response => response.json())
                .then(stock => {
                    currentStock = stock.quantite;
                    stockDisponible.textContent = `${stock.quantite} unités`;
                    
                    if (stock.en_alerte) {
                        stockAlerte.textContent = '⚠️ Stock en alerte';
                        stockAlerte.className = 'text-danger';
                    } else {
                        stockAlerte.textContent = '';
                    }
                    
                    updateValidation();
                    updateInfoProduit();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    stockDisponible.textContent = 'Erreur';
                });
        } else {
            stockDisponible.textContent = '-';
            stockAlerte.textContent = '';
            currentStock = 0;
            updateValidation();
        }
    }

    function updateInfoProduit() {
        const selectedOption = produitSelect.options[produitSelect.selectedIndex];
        if (selectedOption.value && magasinSelect.value) {
            const categorie = selectedOption.dataset.categorie;
            const magasinOption = magasinSelect.options[magasinSelect.selectedIndex];
            
            infoProduit.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <strong>Catégorie :</strong> ${categorie}
                    </div>
                    <div class="col-md-4">
                        <strong>Stock disponible :</strong> ${currentStock} unités
                    </div>
                    <div class="col-md-4">
                        <strong>Magasin :</strong> ${magasinOption.text}
                    </div>
                </div>
            `;
        } else {
            infoProduit.innerHTML = '<p class="mb-0">Sélectionnez un produit et un magasin pour voir les informations</p>';
        }
    }

    function updateValidation() {
        const quantite = parseInt(quantiteInput.value) || 0;
        
        if (quantite === 0) {
            validationMessage.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }
        
        validationMessage.style.display = 'block';
        
        if (quantite > currentStock) {
            validationMessage.className = 'alert alert-danger';
            validationText.textContent = `❌ Quantité (${quantite}) supérieure au stock disponible (${currentStock})`;
            submitBtn.disabled = true;
        } else if (quantite === currentStock) {
            validationMessage.className = 'alert alert-warning';
            validationText.textContent = `⚠️ Vous allez transférer tout le stock disponible (${quantite} unités)`;
            submitBtn.disabled = false;
        } else {
            validationMessage.className = 'alert alert-success';
            validationText.textContent = `✅ Transfert possible : ${quantite} unités sur ${currentStock} disponibles`;
            submitBtn.disabled = false;
        }
    }

    // Écouteurs d'événements
    produitSelect.addEventListener('change', updateStockInfo);
    magasinSelect.addEventListener('change', function() {
        updateBoutiques();
        updateStockInfo();
    });
    quantiteInput.addEventListener('input', updateValidation);

    // Initialisation
    updateValidation();
});
</script>
@endsection
