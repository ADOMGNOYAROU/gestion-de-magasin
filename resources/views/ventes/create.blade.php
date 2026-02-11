@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-cash-register"></i> Caisse - Point de Vente
                </h1>
                <a href="{{ route('ventes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="row">
                <!-- Section Produits -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-box"></i> Sélection des Produits
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Sélection boutique (pour admin/gestionnaire) -->
                            @if(!Auth::user()->isVendeur())
                                <div class="mb-3">
                                    <label for="boutique_id" class="form-label">Boutique <span class="text-danger">*</span></label>
                                    <select class="form-select" id="boutique_id" name="boutique_id" required>
                                        <option value="">Sélectionner une boutique</option>
                                        @foreach($boutiques as $boutique)
                                            <option value="{{ $boutique->id }}" 
                                                    data-magasin="{{ $boutique->magasin->nom ?? '' }}">
                                                {{ $boutique->nom }} - {{ $boutique->localisation }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                @if(Auth::user()->boutique)
                                    <input type="hidden" id="boutique_id" value="{{ Auth::user()->boutique_id }}">
                                    <div class="alert alert-info">
                                        <i class="fas fa-store"></i>
                                        <strong>Boutique :</strong> {{ Auth::user()->boutique->nom }}
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Attention :</strong> Aucune boutique n'est associée à votre compte. Veuillez contacter un administrateur.
                                    </div>
                                @endif
                            @endif

                            <!-- Recherche produit -->
                            <div class="mb-3">
                                <label for="produit_search" class="form-label">Rechercher un produit</label>
                                <input type="text" class="form-control" id="produit_search" 
                                       placeholder="Tapez le nom du produit..." autocomplete="off">
                                <div id="produits_suggestions" class="position-absolute w-100 bg-white border rounded shadow-sm" style="max-height: 200px; overflow-y: auto; display: none; z-index: 1000;"></div>
                            </div>

                            <!-- Formulaire d'ajout rapide -->
                            <div class="row" id="ajout_produit_form" style="display: none;">
                                <div class="col-md-6">
                                    <label class="form-label">Produit sélectionné</label>
                                    <div class="form-control bg-light" id="produit_selectionne">-</div>
                                </div>
                                <div class="col-md-3">
                                    <label for="quantite_ajout" class="form-label">Quantité</label>
                                    <input type="number" class="form-control" id="quantite_ajout" 
                                           min="1" value="1">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Stock disponible</label>
                                    <div class="form-control bg-light" id="stock_disponible">-</div>
                                </div>
                            </div>

                            <div class="mt-3" id="actions_ajout" style="display: none;">
                                <button type="button" class="btn btn-success" id="btn_ajouter_panier">
                                    <i class="fas fa-plus"></i> Ajouter au panier
                                </button>
                                <button type="button" class="btn btn-secondary" id="btn_annuler_selection">
                                    <i class="fas fa-times"></i> Annuler
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Panier -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-cart"></i> Panier
                                <span class="badge bg-light text-dark float-end" id="panier_count">0</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="panier_vide" class="text-center py-4">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Le panier est vide</p>
                            </div>

                            <div id="panier_contenu" style="display: none;">
                                <div id="panier_items"></div>
                                
                                <hr>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Total :</strong>
                                    </div>
                                    <div class="col-6 text-end">
                                        <strong class="text-primary" id="panier_total">0 FCFA</strong>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Bénéfice :</strong>
                                    </div>
                                    <div class="col-6 text-end">
                                        <strong class="text-success" id="panier_benefice">0 FCFA</strong>
                                    </div>
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <label for="date_vente" class="form-label">Date de vente</label>
                                    <input type="date" class="form-control" id="date_vente" 
                                           value="{{ now()->format('Y-m-d') }}">
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-warning" id="btn_vider_panier">
                                        <i class="fas fa-trash"></i> Vider le panier
                                    </button>
                                    <button type="button" class="btn btn-success" id="btn_valider_vente">
                                        <i class="fas fa-check"></i> Valider la vente
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation -->
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de vente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir valider cette vente ?</p>
                <div id="recap_vente"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" id="btn_confirmer_vente">
                    Confirmer la vente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let produits = @json($produits);
    let produitSelectionne = null;
    let panier = [];

    // Éléments DOM
    const produitSearch = document.getElementById('produit_search');
    const produitsSuggestions = document.getElementById('produits_suggestions');
    const boutiqueSelect = document.getElementById('boutique_id');
    const ajoutProduitForm = document.getElementById('ajout_produit_form');
    const produitSelectionneDiv = document.getElementById('produit_selectionne');
    const quantiteAjout = document.getElementById('quantite_ajout');
    const stockDisponible = document.getElementById('stock_disponible');
    const actionsAjout = document.getElementById('actions_ajout');
    const panierVide = document.getElementById('panier_vide');
    const panierContenu = document.getElementById('panier_contenu');
    const panierItems = document.getElementById('panier_items');
    const panierCount = document.getElementById('panier_count');
    const panierTotal = document.getElementById('panier_total');
    const panierBenefice = document.getElementById('panier_benefice');
    const dateVente = document.getElementById('date_vente');

    // Recherche de produits
    produitSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        if (searchTerm.length < 2) {
            produitsSuggestions.style.display = 'none';
            return;
        }

        const filtered = produits.filter(p => 
            p.nom.toLowerCase().includes(searchTerm) || 
            p.categorie.toLowerCase().includes(searchTerm)
        );

        if (filtered.length > 0) {
            let html = '';
            filtered.forEach(p => {
                html += `
                    <div class="p-2 border-bottom produit-suggestion" data-produit='${JSON.stringify(p)}'>
                        <strong>${p.nom}</strong><br>
                        <small class="text-muted">${p.categorie} - ${p.prix_vente} FCFA</small>
                    </div>
                `;
            });
            produitsSuggestions.innerHTML = html;
            produitsSuggestions.style.display = 'block';
        } else {
            produitsSuggestions.style.display = 'none';
        }
    });

    // Sélection d'un produit
    document.addEventListener('click', function(e) {
        if (e.target.closest('.produit-suggestion')) {
            const produitData = JSON.parse(e.target.closest('.produit-suggestion').dataset.produit);
            selectionnerProduit(produitData);
            produitsSuggestions.style.display = 'none';
            produitSearch.value = '';
        }
    });

    function selectionnerProduit(produit) {
        produitSelectionne = produit;
        produitSelectionneDiv.innerHTML = `
            <strong>${produit.nom}</strong><br>
            <small class="text-muted">${produit.categorie}</small><br>
            <strong>${produit.prix_vente} FCFA</strong>
        `;
        
        // Récupérer le stock disponible
        const boutiqueId = boutiqueSelect.value || document.querySelector('#boutique_id').value;
        if (boutiqueId) {
            fetch(`/api/stock-boutique?produit_id=${produit.id}&boutique_id=${boutiqueId}`)
                .then(response => response.json())
                .then(stock => {
                    stockDisponible.textContent = `${stock.quantite} unités`;
                    if (stock.en_alerte) {
                        stockDisponible.className = 'form-control bg-warning text-dark';
                    } else {
                        stockDisponible.className = 'form-control bg-light';
                    }
                });
        }
        
        ajoutProduitForm.style.display = 'flex';
        actionsAjout.style.display = 'block';
    }

    // Ajouter au panier
    document.getElementById('btn_ajouter_panier').addEventListener('click', function() {
        if (!produitSelectionne) return;
        
        const quantite = parseInt(quantiteAjout.value);
        const boutiqueId = boutiqueSelect.value || document.querySelector('#boutique_id').value;
        
        fetch('/api/panier/ajouter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                produit_id: produitSelectionne.id,
                quantite: quantite,
                boutique_id: boutiqueId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                panier = data.panier;
                updatePanier();
                annulerSelection();
                afficherMessage('success', data.message);
            } else {
                afficherMessage('error', data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            afficherMessage('error', 'Erreur lors de l\'ajout au panier');
        });
    });

    // Annuler sélection
    document.getElementById('btn_annuler_selection').addEventListener('click', annulerSelection);

    function annulerSelection() {
        produitSelectionne = null;
        produitSelectionneDiv.textContent = '-';
        stockDisponible.textContent = '-';
        quantiteAjout.value = 1;
        ajoutProduitForm.style.display = 'none';
        actionsAjout.style.display = 'none';
        produitSearch.value = '';
    }

    // Mettre à jour l'affichage du panier
    function updatePanier() {
        if (panier.length === 0) {
            panierVide.style.display = 'block';
            panierContenu.style.display = 'none';
            panierCount.textContent = '0';
            return;
        }

        panierVide.style.display = 'none';
        panierContenu.style.display = 'block';
        panierCount.textContent = panier.length;

        let html = '';
        let total = 0;
        let benefice = 0;

        panier.forEach(item => {
            const itemTotal = item.quantite * item.prix_vente;
            const itemBenefice = item.quantite * (item.prix_vente - item.prix_achat);
            total += itemTotal;
            benefice += itemBenefice;

            html += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                    <div>
                        <strong>${item.nom}</strong><br>
                        <small class="text-muted">${item.quantite} × ${item.prix_vente} FCFA</small>
                    </div>
                    <div class="text-end">
                        <strong>${itemTotal} FCFA</strong><br>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="retirerDuPanier(${item.produit_id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        panierItems.innerHTML = html;
        panierTotal.textContent = `${total} FCFA`;
        panierBenefice.textContent = `${benefice} FCFA`;
    }

    // Retirer du panier
    window.retirerDuPanier = function(produitId) {
        fetch('/api/panier/retirer', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ produit_id: produitId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                panier = data.panier;
                updatePanier();
                afficherMessage('success', data.message);
            }
        });
    };

    // Vider le panier
    document.getElementById('btn_vider_panier').addEventListener('click', function() {
        fetch('/api/panier/vider', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                panier = data.panier;
                updatePanier();
                afficherMessage('success', data.message);
            }
        });
    });

    // Valider la vente
    document.getElementById('btn_valider_vente').addEventListener('click', function() {
        if (panier.length === 0) {
            afficherMessage('error', 'Le panier est vide');
            return;
        }

        // Afficher le récapitulatif
        let recap = '<div class="list-group">';
        let total = 0;
        panier.forEach(item => {
            const itemTotal = item.quantite * item.prix_vente;
            total += itemTotal;
            recap += `
                <div class="list-group-item d-flex justify-content-between">
                    <span>${item.nom} (${item.quantite})</span>
                    <strong>${itemTotal} FCFA</strong>
                </div>
            `;
        });
        recap += `
            <div class="list-group-item d-flex justify-content-between bg-light">
                <strong>Total</strong>
                <strong>${total} FCFA</strong>
            </div>
        </div>`;
        
        document.getElementById('recap_vente').innerHTML = recap;
        new bootstrap.Modal(document.getElementById('confirmationModal')).show();
    });

    // Confirmer la vente
    document.getElementById('btn_confirmer_vente').addEventListener('click', function() {
        const boutiqueId = boutiqueSelect.value || document.querySelector('#boutique_id').value;
        
        fetch('{{ route("ventes.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                boutique_id: boutiqueId,
                date: dateVente.value
            })
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.json();
            }
        })
        .then(data => {
            if (data && data.error) {
                afficherMessage('error', data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            afficherMessage('error', 'Erreur lors de la validation de la vente');
        });
    });

    // Fonctions utilitaires
    function afficherMessage(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.row'));
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Cacher les suggestions en cliquant ailleurs
    document.addEventListener('click', function(e) {
        if (!produitSearch.contains(e.target) && !produitsSuggestions.contains(e.target)) {
            produitsSuggestions.style.display = 'none';
        }
    });
});
</script>
@endsection
