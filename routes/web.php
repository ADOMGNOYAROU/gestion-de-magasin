<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GestionnaireController;
use App\Http\Controllers\VendeurController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\EntreeStockController;
use App\Http\Controllers\TransfertController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\PartenaireController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes handled by Laravel Breeze
require __DIR__.'/auth.php';

Route::post('/logout', function () {
    \Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'vendeur'])->name('dashboard');

// Route de test pour boutiques
Route::get('/boutiques-test', function() {
    return 'Test boutiques - ça marche!';
})->middleware('auth');

// Routes Admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// Routes Gestionnaire
Route::prefix('gestionnaire')->middleware(['auth', 'gestionnaire'])->group(function () {
    Route::get('/dashboard', [GestionnaireController::class, 'dashboard'])->name('gestionnaire.dashboard');
});

// Routes Vendeur
Route::prefix('vendeur')->middleware(['auth', 'vendeur'])->group(function () {
    Route::get('/dashboard', [VendeurController::class, 'dashboard'])->name('vendeur.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Routes CRUD pour les produits (admin et gestionnaire uniquement)
    Route::resource('produits', ProduitController::class)->middleware('gestionnaire');
    Route::post('produits/import', [ProduitController::class, 'import'])->middleware('gestionnaire')->name('produits.import');
    Route::get('produits/search', [ProduitController::class, 'search'])->middleware('gestionnaire')->name('produits.search');
    
    // Routes CRUD pour les boutiques (admin et gestionnaire uniquement)
    Route::resource('boutiques', \App\Http\Controllers\BoutiqueController::class)->middleware('gestionnaire');
    
    // Routes CRUD pour les utilisateurs (admin uniquement)
    Route::resource('users', \App\Http\Controllers\UserController::class)->middleware('admin');
    
    // Routes CRUD pour les magasins (admin uniquement)
    Route::resource('magasins', \App\Http\Controllers\MagasinController::class)->middleware('admin');
    
    // Routes CRUD pour les fournisseurs (admin et gestionnaire uniquement)
    Route::resource('fournisseurs', FournisseurController::class)->middleware('gestionnaire');
    
    // Routes CRUD pour les partenaires (admin et gestionnaire uniquement)
    Route::resource('partenaires', PartenaireController::class)->middleware('gestionnaire');
    
    // Routes CRUD pour les clients (gestionnaires et vendeurs)
    Route::middleware(['auth', 'gestionnaire'])->group(function() {
        Route::resource('clients', ClientController::class)->names([
            'index' => 'clients.index',
            'create' => 'clients.create',
            'store' => 'clients.store',
            'show' => 'clients.show',
            'edit' => 'clients.edit',
            'update' => 'clients.update',
            'destroy' => 'clients.destroy'
        ]);
    });
    
    // ROUTE DE TEST TEMPORAIRE - Supprimer après diagnostic
    Route::get('/test-clients', function() {
        return 'Route de test clients fonctionne ! Role: ' . auth()->user()->role . ' | isGestionnaire: ' . (auth()->user()->isGestionnaire() ? 'true' : 'false');
    })->middleware('auth')->name('test.clients');
    
    // Routes supplémentaires pour les clients
    Route::post('/clients/{client}/ajouter-points', [ClientController::class, 'ajouterPoints'])->middleware('vendeur')->name('clients.ajouter_points');
    Route::post('/clients/{client}/utiliser-points', [ClientController::class, 'utiliserPoints'])->middleware('vendeur')->name('clients.utiliser_points');
    Route::post('/clients/{client}/generer-coupon', [ClientController::class, 'genererCoupon'])->middleware('vendeur')->name('clients.generer_coupon');
    
    // API routes pour les clients
    Route::get('/api/clients/search', [ClientController::class, 'search'])->middleware('vendeur');
    Route::get('/api/coupons/valider', [ClientController::class, 'validerCoupon'])->middleware('vendeur');
    Route::post('/api/coupons/{coupon}/utiliser', [ClientController::class, 'utiliserCoupon'])->middleware('vendeur');
    
    // Routes CRUD pour les entrées de stock (admin et gestionnaire uniquement)
    Route::resource('entrees-stock', EntreeStockController::class)->middleware('gestionnaire')->names([
        'index' => 'entrees-stock.index',
        'create' => 'entrees-stock.create',
        'store' => 'entrees-stock.store',
        'show' => 'entrees-stock.show',
        'edit' => 'entrees-stock.edit',
        'update' => 'entrees-stock.update',
        'destroy' => 'entrees-stock.destroy'
    ]);
    
    // Routes CRUD pour les transferts (admin et gestionnaire uniquement)
    Route::resource('transferts', TransfertController::class)->middleware('gestionnaire')->names([
        'index' => 'transferts.index',
        'create' => 'transferts.create',
        'store' => 'transferts.store',
        'show' => 'transferts.show',
        'edit' => 'transferts.edit',
        'update' => 'transferts.update',
        'destroy' => 'transferts.destroy'
    ]);

    // Route pour l'historique des mouvements de stock
    Route::get('mouvements-stock', [App\Http\Controllers\MouvementsStockController::class, 'index'])->middleware('gestionnaire')->name('mouvements-stock.index');
    
    // Routes CRUD pour les ventes (tous les rôles)
    Route::resource('ventes', VenteController::class)->middleware('vendeur')->names([
        'index' => 'ventes.index',
        'create' => 'ventes.create',
        'store' => 'ventes.store',
        'show' => 'ventes.show',
        'edit' => 'ventes.edit',
        'update' => 'ventes.update',
        'destroy' => 'ventes.destroy'
    ]);
    
    // Route pour le reçu de vente
    Route::get('/ventes/{vente}/recu', [VenteController::class, 'recu'])->middleware('vendeur')->name('ventes.recu');
    
    // Routes pour les rapports (admin et gestionnaire uniquement)
    Route::get('/rapports', [RapportController::class, 'index'])->middleware('gestionnaire')->name('rapports.index');
    Route::get('/rapports/stock/pdf', [RapportController::class, 'rapportStockPDF'])->middleware('gestionnaire')->name('rapports.stock.pdf');
    Route::get('/rapports/ventes/form', [RapportController::class, 'rapportVentesForm'])->middleware('gestionnaire')->name('rapports.ventes.form');
    Route::post('/rapports/ventes/pdf', [RapportController::class, 'rapportVentesPDF'])->middleware('gestionnaire')->name('rapports.ventes.pdf');
    Route::post('/rapports/ventes/excel', [RapportController::class, 'rapportVentesExcel'])->middleware('gestionnaire')->name('rapports.ventes.excel');
    Route::get('/rapports/partenaires/pdf', [RapportController::class, 'rapportPartenairesPDF'])->middleware('gestionnaire')->name('rapports.partenaires.pdf');
    
    // Routes API pour les transferts
    Route::get('/api/stock-disponible', [TransfertController::class, 'getStockDisponible'])->middleware('gestionnaire');
    Route::get('/api/boutiques-par-magasin', [TransfertController::class, 'getBoutiquesByMagasin'])->middleware('gestionnaire');
    Route::get('/api/produits-avec-stock', [TransfertController::class, 'getProduitsAvecStock'])->middleware('gestionnaire');
    
    // Routes API pour les ventes (panier)
    Route::post('/api/panier/ajouter', [VenteController::class, 'ajouterPanier'])->middleware('vendeur');
    Route::delete('/api/panier/retirer', [VenteController::class, 'retirerPanier'])->middleware('vendeur');
    Route::delete('/api/panier/vider', [VenteController::class, 'viderPanier'])->middleware('vendeur');
    Route::get('/api/stock-boutique', [VenteController::class, 'getStockDisponible'])->middleware('vendeur');
    
    // Routes CRUD pour les commandes fournisseurs (gestionnaire uniquement)
    Route::get('/commandes-fournisseurs/comparer-prix', [\App\Http\Controllers\CommandeFournisseurController::class, 'comparerPrix'])->middleware('gestionnaire')->name('commandes-fournisseurs.comparer-prix');
    Route::get('/commandes-fournisseurs/generer-reappro', [\App\Http\Controllers\CommandeFournisseurController::class, 'genererReappro'])->middleware('gestionnaire')->name('commandes-fournisseurs.generer-reappro');
    Route::post('/commandes-fournisseurs/{commandeFournisseur}/changer-status', [\App\Http\Controllers\CommandeFournisseurController::class, 'changerStatus'])->middleware('gestionnaire')->name('commandes-fournisseurs.changer-status');
    Route::get('/commandes-fournisseurs/historique/{fournisseur}', [\App\Http\Controllers\CommandeFournisseurController::class, 'historiqueFournisseur'])->middleware('gestionnaire')->name('commandes-fournisseurs.historique-fournisseur');
    Route::resource('commandes-fournisseurs', \App\Http\Controllers\CommandeFournisseurController::class)->middleware('gestionnaire')->names([
        'index' => 'commandes-fournisseurs.index',
        'create' => 'commandes-fournisseurs.create',
        'store' => 'commandes-fournisseurs.store',
        'show' => 'commandes-fournisseurs.show',
        'edit' => 'commandes-fournisseurs.edit',
        'update' => 'commandes-fournisseurs.update',
        'destroy' => 'commandes-fournisseurs.destroy'
    ]);

    // Route pour le rapport PDF d'une commande fournisseur
    Route::get('/commandes-fournisseurs/{commandeFournisseur}/pdf', [\App\Http\Controllers\CommandeFournisseurController::class, 'rapportPDF'])->middleware('gestionnaire')->name('commandes-fournisseurs.rapport-pdf');

    // Routes CRUD pour le POS (tous les rôles authentifiés)
    Route::resource('pos', POSController::class)->middleware('auth');
});
