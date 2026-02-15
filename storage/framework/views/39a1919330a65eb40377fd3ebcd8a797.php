<?php $__env->startSection('title', 'Créer une Commande Fournisseur'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Accueil</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('commandes-fournisseurs.index')); ?>">Commandes Fournisseurs</a></li>
    <li class="breadcrumb-item active">Créer</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header'); ?>
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
    <div class="mb-3 mb-sm-0">
        <h1 class="h4 h3-sm mb-1">Créer une Commande Fournisseur</h1>
        <p class="text-muted mb-0 small">Nouvelle commande d'achat</p>
    </div>
    <div class="w-100 w-sm-auto">
        <a href="<?php echo e(route('commandes-fournisseurs.index')); ?>" class="btn btn-secondary btn-mobile w-100">
            <i class="fas fa-arrow-left me-2"></i>
            <span class="d-sm-inline">Retour</span>
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <form action="<?php echo e(route('commandes-fournisseurs.store')); ?>" method="POST" id="commandeForm">
            <?php echo csrf_field(); ?>

            <!-- Informations générales -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Informations Générales
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fournisseur_id" class="form-label">Fournisseur <span class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['fournisseur_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="fournisseur_id" name="fournisseur_id" required>
                                    <option value="">Sélectionner un fournisseur...</option>
                                    <?php $__currentLoopData = $fournisseurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fournisseur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($fournisseur->id); ?>"
                                                <?php echo e(($selectedFournisseur && $selectedFournisseur->id == $fournisseur->id) ? 'selected' : ''); ?>>
                                            <?php echo e($fournisseur->nom); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['fournisseur_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="magasin_id" class="form-label">Magasin</label>
                                <select class="form-select <?php $__errorArgs = ['magasin_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="magasin_id" name="magasin_id">
                                    <option value="">Sélectionner un magasin...</option>
                                    <?php $__currentLoopData = $magasins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $magasin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($magasin->id); ?>">
                                            <?php echo e($magasin->nom); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['magasin_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date_commande" class="form-label">Date de commande <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?php $__errorArgs = ['date_commande'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       id="date_commande" name="date_commande"
                                       value="<?php echo e(old('date_commande', date('Y-m-d'))); ?>" required>
                                <?php $__errorArgs = ['date_commande'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date_livraison_prevue" class="form-label">Date livraison prévue</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['date_livraison_prevue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       id="date_livraison_prevue" name="date_livraison_prevue"
                                       value="<?php echo e(old('date_livraison_prevue')); ?>">
                                <?php $__errorArgs = ['date_livraison_prevue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Statut</label>
                                <input type="text" class="form-control" value="Brouillon" readonly>
                                <small class="text-muted">La commande sera créée en brouillon</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Notes sur la commande..."><?php echo e(old('notes')); ?></textarea>
                        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- Produits à commander -->
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shopping-cart"></i> Produits à Commander
                    </h6>
                    <button type="button" class="btn btn-primary btn-sm" id="addProductBtn">
                        <i class="fas fa-plus"></i> Ajouter un produit
                    </button>
                </div>
                <div class="card-body">
                    <div id="productsContainer">
                        <!-- Les lignes de produits seront ajoutées ici -->
                    </div>

                    <?php $__errorArgs = ['produits'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="alert alert-danger"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <!-- Template pour une ligne de produit -->
                    <div id="productTemplate" style="display: none;">
                        <div class="product-row border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Produit</label>
                                    <select class="form-select produit-select" name="produits[{index}][produit_id]" required>
                                        <option value="">Sélectionner un produit...</option>
                                        <?php $__currentLoopData = $produits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($produit->id); ?>" data-categorie="<?php echo e($produit->categorie); ?>">
                                                <?php echo e($produit->nom); ?> - <?php echo e($produit->categorie); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Quantité</label>
                                    <input type="number" class="form-control quantite-input"
                                           name="produits[{index}][quantite]" min="1" value="1" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Prix unitaire HT</label>
                                    <input type="number" class="form-control prix-input"
                                           name="produits[{index}][prix_unitaire]" min="0" step="0.01" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">TVA (%)</label>
                                    <input type="number" class="form-control tva-input"
                                           name="produits[{index}][tva_taux]" min="0" max="100" step="0.01" value="18.00" required>
                                </div>

                                <div class="col-md-1">
                                    <label class="form-label">Sous-total</label>
                                    <input type="text" class="form-control sous-total-input" readonly>
                                </div>

                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger remove-product-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Totaux -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total HT:</span>
                                    <strong id="totalHT">0 FCFA</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>TVA:</span>
                                    <strong id="totalTVA">0 FCFA</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="h5">Total TTC:</span>
                                    <strong class="h5 text-primary" id="totalTTC">0 FCFA</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="d-flex justify-content-between">
                <a href="<?php echo e(route('commandes-fournisseurs.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>

                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer la commande
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let productIndex = 0;

$(document).ready(function() {
    // Validation des dates
    $('#date_commande').on('change', function() {
        const dateCommande = $(this).val();
        $('#date_livraison_prevue').attr('min', dateCommande);
    });

    // Ajouter première ligne de produit
    addProductRow();
});

$('#addProductBtn').on('click', function() {
    addProductRow();
});

function addProductRow() {
    const template = $('#productTemplate').html();
    const html = template.replace(/{index}/g, productIndex);

    $('#productsContainer').append(html);
    updateProductRowEvents(productIndex);
    productIndex++;
    updateTotals();
}

function updateProductRowEvents(index) {
    const row = $(`#productsContainer .product-row`).last();

    // Calcul automatique des sous-totaux
    row.find('.quantite-input, .prix-input, .tva-input').on('input', function() {
        calculerSousTotal(row);
        updateTotals();
    });

    // Supprimer la ligne
    row.find('.remove-product-btn').on('click', function() {
        if ($('#productsContainer .product-row').length > 1) {
            row.remove();
            updateTotals();
        } else {
            alert('Vous devez avoir au moins un produit dans la commande.');
        }
    });

    // Calcul initial
    calculerSousTotal(row);
}

function calculerSousTotal(row) {
    const quantite = parseFloat(row.find('.quantite-input').val()) || 0;
    const prix = parseFloat(row.find('.prix-input').val()) || 0;
    const tva = parseFloat(row.find('.tva-input').val()) || 0;

    const sousTotalHT = quantite * prix;
    const sousTotalTTC = sousTotalHT * (1 + tva / 100);

    row.find('.sous-total-input').val(formatCurrency(sousTotalTTC));
}

function updateTotals() {
    let totalHT = 0;
    let totalTVA = 0;

    $('#productsContainer .product-row').each(function() {
        const quantite = parseFloat($(this).find('.quantite-input').val()) || 0;
        const prix = parseFloat($(this).find('.prix-input').val()) || 0;
        const tva = parseFloat($(this).find('.tva-input').val()) || 0;

        const sousTotalHT = quantite * prix;
        const sousTotalTVA = sousTotalHT * (tva / 100);

        totalHT += sousTotalHT;
        totalTVA += sousTotalTVA;
    });

    const totalTTC = totalHT + totalTVA;

    $('#totalHT').text(formatCurrency(totalHT));
    $('#totalTVA').text(formatCurrency(totalTVA));
    $('#totalTTC').text(formatCurrency(totalTTC));
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XOF',
        minimumFractionDigits: 0
    }).format(amount);
}

// Validation du formulaire avant soumission
$('#commandeForm').on('submit', function(e) {
    const productRows = $('#productsContainer .product-row');
    if (productRows.length === 0) {
        e.preventDefault();
        alert('Vous devez ajouter au moins un produit à la commande.');
        return false;
    }

    // Vérifier que tous les champs requis sont remplis
    let isValid = true;
    productRows.each(function() {
        const produitId = $(this).find('.produit-select').val();
        const quantite = $(this).find('.quantite-input').val();
        const prix = $(this).find('.prix-input').val();

        if (!produitId || !quantite || !prix) {
            isValid = false;
        }
    });

    if (!isValid) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires pour chaque produit.');
        return false;
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestion-magasin\resources\views/commandes-fournisseurs/create.blade.php ENDPATH**/ ?>