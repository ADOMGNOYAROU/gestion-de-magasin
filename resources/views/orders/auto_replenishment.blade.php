@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Réapprovisionnement Automatique</h1>
                <div>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Retour aux commandes
                    </a>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle commande
                    </a>
                </div>
            </div>

            <form action="{{ route('orders.generate_replenishment') }}" method="POST" id="replenishmentForm">
                @csrf

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Suggestions de Réapprovisionnement</h6>
                    </div>
                    <div class="card-body">
                        @if(count($suggestions) > 0)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Les suggestions ci-dessous sont basées sur les produits ayant un stock faible (moins de 10 unités) et les meilleurs prix des fournisseurs historiques.
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th>Produit</th>
                                            <th>Fournisseur suggéré</th>
                                            <th>Prix unitaire</th>
                                            <th>Stock actuel</th>
                                            <th>Quantité suggérée</th>
                                            <th>Sous-total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($suggestions as $index => $suggestion)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="suggestions[{{ $index }}][selected]" value="1" class="suggestion-checkbox">
                                                <input type="hidden" name="suggestions[{{ $index }}][supplier_id]" value="{{ $suggestion['supplier']->id }}">
                                                <input type="hidden" name="suggestions[{{ $index }}][produit_id]" value="{{ $suggestion['produit']->id }}">
                                                <input type="hidden" name="suggestions[{{ $index }}][price]" value="{{ $suggestion['price'] }}">
                                            </td>
                                            <td>
                                                <strong>{{ $suggestion['produit']->nom }}</strong><br>
                                                <small class="text-muted">{{ $suggestion['produit']->categorie }}</small>
                                            </td>
                                            <td>{{ $suggestion['supplier']->nom }}</td>
                                            <td>{{ number_format($suggestion['price'], 0) }} FCFA</td>
                                            <td>{{ $suggestion['produit']->stock_total_boutique }} unités</td>
                                            <td>
                                                <input type="number" name="suggestions[{{ $index }}][quantity]"
                                                       value="{{ $suggestion['suggested_quantity'] }}" min="1"
                                                       class="form-control form-control-sm quantity-input">
                                            </td>
                                            <td class="subtotal">
                                                {{ number_format($suggestion['price'] * $suggestion['suggested_quantity'], 0) }} FCFA
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-between align-items-center mt-3">
                                <div>
                                    <strong>Total sélectionné: <span id="totalSelected">0</span> FCFA</strong>
                                </div>
                                <button type="submit" class="btn btn-success" id="generateBtn" disabled>
                                    <i class="fas fa-magic"></i> Générer les commandes
                                </button>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5>Tous les stocks sont suffisants</h5>
                                <p class="text-muted">Aucun produit n'a un stock faible nécessitant un réapprovisionnement automatique.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const suggestionCheckboxes = document.querySelectorAll('.suggestion-checkbox');
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const generateBtn = document.getElementById('generateBtn');
    const totalSelectedEl = document.getElementById('totalSelected');

    // Fonction pour mettre à jour le total
    function updateTotal() {
        let total = 0;
        const checkedBoxes = document.querySelectorAll('.suggestion-checkbox:checked');

        checkedBoxes.forEach(function(checkbox) {
            const row = checkbox.closest('tr');
            const price = parseFloat(row.querySelector('input[name*="[price]"]').value);
            const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
            total += price * quantity;
        });

        totalSelectedEl.textContent = total.toLocaleString();
        generateBtn.disabled = checkedBoxes.length === 0;
    }

    // Sélectionner/désélectionner tout
    selectAllCheckbox.addEventListener('change', function() {
        suggestionCheckboxes.forEach(function(checkbox) {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateTotal();
    });

    // Mettre à jour le total quand une case est cochée/décochée
    suggestionCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.suggestion-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === suggestionCheckboxes.length && suggestionCheckboxes.length > 0;
            updateTotal();
        });
    });

    // Mettre à jour le total quand la quantité change
    quantityInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            updateTotal();
        });
    });

    // Calcul initial
    updateTotal();
});
</script>
@endsection
