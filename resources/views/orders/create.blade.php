@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Créer une Commande Fournisseur</h1>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                @csrf

                <div class="row">
                    <!-- Informations générales -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Informations de la Commande</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="fournisseur_id" class="form-label">Fournisseur <span class="text-danger">*</span></label>
                                        <select name="fournisseur_id" id="fournisseur_id" class="form-select @error('fournisseur_id') is-invalid @enderror" required>
                                            <option value="">Sélectionner un fournisseur</option>
                                            @foreach($fournisseurs as $fournisseur)
                                                <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                                    {{ $fournisseur->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('fournisseur_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_livraison_prevue" class="form-label">Date de livraison prévue</label>
                                        <input type="date" name="date_livraison_prevue" id="date_livraison_prevue"
                                               class="form-control @error('date_livraison_prevue') is-invalid @enderror"
                                               value="{{ old('date_livraison_prevue') }}">
                                        @error('date_livraison_prevue')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                                              rows="3" placeholder="Notes supplémentaires...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Produits -->
                        <div class="card mt-3">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Produits à Commander</h5>
                                <button type="button" class="btn btn-light btn-sm" id="addProductBtn">
                                    <i class="fas fa-plus"></i> Ajouter un produit
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="productsContainer">
                                    <!-- Les produits seront ajoutés ici dynamiquement -->
                                </div>

                                <div class="text-end mt-3">
                                    <strong>Total: <span id="totalAmount">0</span> FCFA</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Résumé -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Résumé de la Commande</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Créer la Commande
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template pour les produits -->
<script type="text/template" id="productTemplate">
    <div class="product-row border rounded p-3 mb-3 bg-light">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Produit <span class="text-danger">*</span></label>
                <select name="produits[{index}][id]" class="form-select product-select" required>
                    <option value="">Sélectionner un produit</option>
                    @foreach($produits as $produit)
                        <option value="{{ $produit->id }}" data-price="{{ $produit->prix_achat }}">
                            {{ $produit->nom }} ({{ number_format($produit->prix_achat, 0) }} FCFA)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantité <span class="text-danger">*</span></label>
                <input type="number" name="produits[{index}][quantite]" class="form-control quantity-input"
                       min="1" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Prix unitaire <span class="text-danger">*</span></label>
                <input type="number" name="produits[{index}][prix_unitaire]" class="form-control price-input"
                       step="0.01" min="0" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger remove-product-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <small class="text-muted">Sous-total: <span class="subtotal">0</span> FCFA</small>
            </div>
        </div>
    </div>
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = 0;
    const productsContainer = document.getElementById('productsContainer');
    const addProductBtn = document.getElementById('addProductBtn');
    const totalAmountEl = document.getElementById('totalAmount');
    const productTemplate = document.getElementById('productTemplate').innerHTML;

    // Fonction pour ajouter un produit
    function addProduct() {
        const productHtml = productTemplate.replace(/{index}/g, productIndex);
        productsContainer.insertAdjacentHTML('beforeend', productHtml);
        productIndex++;
        updateTotal();
    }

    // Événement pour ajouter un produit
    addProductBtn.addEventListener('click', addProduct);

    // Événement pour supprimer un produit
    productsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product-btn') || e.target.closest('.remove-product-btn')) {
            e.target.closest('.product-row').remove();
            updateTotal();
        }
    });

    // Événement pour mettre à jour les calculs
    productsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input') || e.target.classList.contains('price-input')) {
            updateSubtotal(e.target.closest('.product-row'));
            updateTotal();
        }
    });

    // Événement pour changer de produit (prix automatique)
    productsContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const selectedOption = e.target.selectedOptions[0];
            const price = selectedOption.getAttribute('data-price');
            const priceInput = e.target.closest('.product-row').querySelector('.price-input');
            if (price) {
                priceInput.value = price;
                updateSubtotal(e.target.closest('.product-row'));
                updateTotal();
            }
        }
    });

    // Fonction pour mettre à jour le sous-total
    function updateSubtotal(productRow) {
        const quantity = parseFloat(productRow.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(productRow.querySelector('.price-input').value) || 0;
        const subtotal = quantity * price;
        productRow.querySelector('.subtotal').textContent = subtotal.toLocaleString();
    }

    // Fonction pour mettre à jour le total
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(function(el) {
            total += parseFloat(el.textContent.replace(/\s/g, '').replace(',', '.')) || 0;
        });
        totalAmountEl.textContent = total.toLocaleString();
    }

    // Ajouter un produit par défaut
    addProduct();
});
</script>
@endsection
