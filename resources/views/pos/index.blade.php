@extends('layouts.app')

@section('title', 'Caisse - ' . $boutique->nom)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item active">Caisse</li>
@endsection

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0">Caisse - {{ $boutique->nom }}</h1>
        <p class="text-muted mb-0">Session ouverte: {{ $sessionActive->date_ouverture->format('d/m/Y H:i') }}</p>
    </div>
    <div>
        <a href="{{ route('pos.close') }}" class="btn btn-warning">
            <i class="fas fa-sign-out-alt me-2"></i>Fermer la caisse
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row g-4">
    <!-- Section Recherche et Produits -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recherche de produits</h5>
            </div>
            <div class="card-body">
                <!-- Barre de recherche -->
                <div class="mb-4">
                    <div class="input-group">
                        <input type="text" id="productSearch" class="form-control form-control-lg"
                               placeholder="Rechercher un produit par nom, code-barres ou catégorie..."
                               autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Résultats de recherche -->
                <div id="searchResults" class="mb-4" style="display: none;">
                    <h6>Résultats de recherche:</h6>
                    <div id="productResults" class="row g-2"></div>
                </div>

                <!-- Produits populaires -->
                <div id="popularProducts">
                    <h6>Produits populaires:</h6>
                    <div class="row g-2" id="popularProductGrid">
                        @foreach($produits->take(20) as $produit)
                        <div class="col-md-4 col-sm-6">
                            <button class="btn btn-outline-primary w-100 product-btn"
                                    data-product-id="{{ $produit->id }}"
                                    data-product-name="{{ $produit->nom }}"
                                    data-product-price="{{ $produit->prix_vente }}"
                                    data-product-category="{{ $produit->categorie }}">
                                <div class="text-start">
                                    <strong>{{ Str::limit($produit->nom, 20) }}</strong><br>
                                    <small class="text-muted">{{ $produit->categorie }}</small><br>
                                    <span class="fw-bold">{{ number_format($produit->prix_vente, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Panier et Paiement -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Panier <span class="badge bg-primary" id="cartCount">0</span></h5>
                <button class="btn btn-sm btn-outline-danger" id="clearCartBtn">
                    <i class="fas fa-trash me-1"></i>Vider
                </button>
            </div>
            <div class="card-body d-flex flex-column">
                <!-- Liste des articles -->
                <div class="flex-grow-1" style="min-height: 300px;">
                    <div id="cartItems" class="mb-3">
                        <p class="text-muted text-center mb-0">Le panier est vide</p>
                    </div>
                </div>

                <!-- Total -->
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong>Total:</strong>
                        <strong id="cartTotal">0 FCFA</strong>
                    </div>

                    <!-- Méthode de paiement -->
                    <div class="mb-3">
                        <label class="form-label">Mode de paiement:</label>
                        <div class="btn-group w-100" role="group">
                            @foreach($paymentMethods as $method)
                            <input type="radio" class="btn-check" name="paymentMethod"
                                   id="payment{{ $method->code }}" value="{{ $method->id }}"
                                   {{ $method->code === 'cash' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="payment{{ $method->code }}">
                                {{ $method->name }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Montant reçu (pour espèces) -->
                    <div class="mb-3" id="cashReceivedDiv">
                        <label for="cashReceived" class="form-label">Montant reçu:</label>
                        <input type="number" class="form-control form-control-lg text-end"
                               id="cashReceived" placeholder="0" min="0" step="0.01">
                        <div class="mt-1">
                            <small class="text-muted">Monnaie à rendre: <span id="changeAmount">0 FCFA</span></small>
                        </div>
                    </div>

                    <!-- Bouton de validation -->
                    <button class="btn btn-success btn-lg w-100" id="checkoutBtn" disabled>
                        <i class="fas fa-check me-2"></i>Valider la vente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de vente -->
<div class="modal fade" id="saleConfirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vente confirmée</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h4>Vente enregistrée avec succès !</h4>
                <p class="mb-2"><strong>Numéro de ticket:</strong> <span id="ticketNumber"></span></p>
                <p class="mb-2"><strong>Total:</strong> <span id="saleTotal"></span></p>
                <p class="mb-0"><strong>Monnaie:</strong> <span id="saleChange"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="printReceipt()">
                    <i class="fas fa-print me-1"></i>Imprimer le ticket
                </button>
                <button type="button" class="btn btn-success" onclick="newSale()">
                    Nouvelle vente
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Variables globales
let cart = [];
let products = @json($produits);

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    setupEventListeners();
    updateCartDisplay();
});

// Configuration des événements
function setupEventListeners() {
    // Recherche de produits
    const searchInput = document.getElementById('productSearch');
    searchInput.addEventListener('input', debounce(handleSearch, 300));

    // Bouton de recherche
    document.getElementById('clearSearch').addEventListener('click', function() {
        searchInput.value = '';
        document.getElementById('searchResults').style.display = 'none';
        document.getElementById('popularProducts').style.display = 'block';
    });

    // Boutons de produits
    document.querySelectorAll('.product-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            addProductToCart(productId, 1);
        });
    });

    // Montant reçu
    document.getElementById('cashReceived').addEventListener('input', calculateChange);

    // Méthodes de paiement
    document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
        radio.addEventListener('change', function() {
            toggleCashInput();
        });
    });

    // Bouton de validation
    document.getElementById('checkoutBtn').addEventListener('click', checkout);

    // Bouton vider panier
    document.getElementById('clearCartBtn').addEventListener('click', clearCart);
}

// Recherche de produits
function handleSearch() {
    const query = document.getElementById('productSearch').value.trim();

    if (query.length < 2) {
        document.getElementById('searchResults').style.display = 'none';
        document.getElementById('popularProducts').style.display = 'block';
        return;
    }

    fetch(`{{ route('pos.search_products') }}?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
        })
        .catch(error => {
            console.error('Erreur de recherche:', error);
        });
}

// Affichage des résultats de recherche
function displaySearchResults(products) {
    const resultsDiv = document.getElementById('productResults');
    const searchResults = document.getElementById('searchResults');
    const popularProducts = document.getElementById('popularProducts');

    if (products.length === 0) {
        resultsDiv.innerHTML = '<p class="text-muted">Aucun produit trouvé.</p>';
        searchResults.style.display = 'block';
        popularProducts.style.display = 'none';
        return;
    }

    let html = '';
    products.forEach(product => {
        html += `
            <div class="col-md-6">
                <button class="btn btn-outline-success w-100 product-btn"
                        data-product-id="${product.id}"
                        data-product-name="${product.nom}"
                        data-product-price="${product.prix_vente}"
                        data-product-category="${product.categorie}">
                    <div class="text-start">
                        <strong>${product.nom}</strong><br>
                        <small class="text-muted">${product.categorie}</small><br>
                        <span class="fw-bold">${formatCurrency(product.prix_vente)}</span>
                    </div>
                </button>
            </div>
        `;
    });

    resultsDiv.innerHTML = html;
    searchResults.style.display = 'block';
    popularProducts.style.display = 'none';

    // Réattacher les événements aux nouveaux boutons
    document.querySelectorAll('#productResults .product-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            addProductToCart(productId, 1);
        });
    });
}

// Ajouter un produit au panier
function addProductToCart(productId, quantity) {
    fetch('{{ route('pos.cart.add') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            produit_id: productId,
            quantite: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cart = data.cart;
            updateCartDisplay();
            showToast('Produit ajouté au panier', 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur lors de l\'ajout au panier', 'error');
    });
}

// Charger le panier depuis le serveur
function loadCart() {
    fetch('{{ route('pos.cart.get') }}')
        .then(response => response.json())
        .then(data => {
            cart = data.cart;
            updateCartDisplay();
        })
        .catch(error => {
            console.error('Erreur de chargement du panier:', error);
        });
}

// Mettre à jour l'affichage du panier
function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const cartTotal = document.getElementById('cartTotal');
    const checkoutBtn = document.getElementById('checkoutBtn');

    cartCount.textContent = cart.length;

    if (cart.length === 0) {
        cartItems.innerHTML = '<p class="text-muted text-center mb-0">Le panier est vide</p>';
        cartTotal.textContent = '0 FCFA';
        checkoutBtn.disabled = true;
        return;
    }

    let total = 0;
    let html = '<div class="list-group">';

    cart.forEach(item => {
        const itemTotal = item.prix_unitaire * item.quantite;
        total += itemTotal;

        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <strong>${item.nom}</strong><br>
                    <small class="text-muted">${item.categorie}</small>
                    <div class="d-flex align-items-center mt-1">
                        <button class="btn btn-sm btn-outline-secondary me-1" onclick="updateQuantity(${item.produit_id}, ${item.quantite - 1})">-</button>
                        <span class="mx-2">Qty: ${item.quantite}</span>
                        <button class="btn btn-sm btn-outline-secondary ms-1" onclick="updateQuantity(${item.produit_id}, ${item.quantite + 1})">+</button>
                    </div>
                </div>
                <div class="text-end">
                    <div>${formatCurrency(itemTotal)}</div>
                    <button class="btn btn-sm btn-outline-danger mt-1" onclick="removeFromCart(${item.produit_id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });

    html += '</div>';
    cartItems.innerHTML = html;
    cartTotal.textContent = formatCurrency(total);
    checkoutBtn.disabled = false;

    calculateChange();
}

// Modifier la quantité
function updateQuantity(productId, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(productId);
        return;
    }

    fetch('{{ route('pos.cart.update_quantity') }}', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            produit_id: productId,
            quantite: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cart = data.cart;
            updateCartDisplay();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Retirer du panier
function removeFromCart(productId) {
    fetch('{{ route('pos.cart.remove') }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: `produit_id=${productId}`,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cart = data.cart;
            updateCartDisplay();
            showToast('Produit retiré du panier', 'success');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Vider le panier
function clearCart() {
    if (!confirm('Êtes-vous sûr de vouloir vider le panier ?')) {
        return;
    }

    fetch('{{ route('pos.cart.clear') }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cart = [];
            updateCartDisplay();
            showToast('Panier vidé', 'success');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Calculer la monnaie
function calculateChange() {
    const cashReceived = parseFloat(document.getElementById('cashReceived').value) || 0;
    const total = getCartTotal();
    const change = Math.max(0, cashReceived - total);

    document.getElementById('changeAmount').textContent = formatCurrency(change);
}

// Obtenir le total du panier
function getCartTotal() {
    return cart.reduce((total, item) => total + (item.prix_unitaire * item.quantite), 0);
}

// Basculer l'affichage du montant reçu
function toggleCashInput() {
    const selectedPayment = document.querySelector('input[name="paymentMethod"]:checked');
    const cashDiv = document.getElementById('cashReceivedDiv');

    if (selectedPayment && selectedPayment.value) {
        const paymentMethod = @json($paymentMethods)->find(m => m.id == selectedPayment.value);
        cashDiv.style.display = paymentMethod && paymentMethod.code === 'cash' ? 'block' : 'none';
    }
}

// Validation de la vente
function checkout() {
    const selectedPayment = document.querySelector('input[name="paymentMethod"]:checked');
    if (!selectedPayment) {
        showToast('Veuillez sélectionner un mode de paiement', 'warning');
        return;
    }

    const paymentMethodId = selectedPayment.value;
    const paymentMethod = @json($paymentMethods)->find(m => m.id == paymentMethodId);
    let montantRecu = getCartTotal();

    if (paymentMethod.code === 'cash') {
        montantRecu = parseFloat(document.getElementById('cashReceived').value) || 0;
        if (montantRecu < getCartTotal()) {
            showToast('Le montant reçu est insuffisant', 'error');
            return;
        }
    }

    if (!confirm('Confirmer la validation de la vente ?')) {
        return;
    }

    fetch('{{ route('pos.checkout') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            payment_method_id: paymentMethodId,
            montant_recu: montantRecu
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSaleConfirmation(data);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur lors de la validation de la vente', 'error');
    });
}

// Afficher la confirmation de vente
function showSaleConfirmation(data) {
    document.getElementById('ticketNumber').textContent = data.numero_ticket;
    document.getElementById('saleTotal').textContent = formatCurrency(data.montant_total);
    document.getElementById('saleChange').textContent = formatCurrency(data.monnaie);

    const modal = new bootstrap.Modal(document.getElementById('saleConfirmationModal'));
    modal.show();
}

// Nouvelle vente
function newSale() {
    bootstrap.Modal.getInstance(document.getElementById('saleConfirmationModal')).hide();
    cart = [];
    updateCartDisplay();
    document.getElementById('cashReceived').value = '';
    document.getElementById('changeAmount').textContent = '0 FCFA';
}

// Imprimer le ticket
function printReceipt() {
    // Implémentation de l'impression
    window.print();
}

// Utilitaires
function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XOF',
        minimumFractionDigits: 0
    }).format(amount);
}

function showToast(message, type = 'info') {
    // Utiliser le système de notifications existant
    const alertClass = type === 'success' ? 'alert-success' :
                      type === 'error' ? 'alert-danger' :
                      type === 'warning' ? 'alert-warning' : 'alert-info';

    const toastHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed"
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', toastHtml);

    // Auto-hide after 3 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert:last-child');
        if (alert) {
            alert.remove();
        }
    }, 3000);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialisation
toggleCashInput();
</script>
@endpush
