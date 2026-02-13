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
    
    // Routes pour le système de caisse (POS)
    Route::prefix('pos')->name('pos.')->middleware(['auth'])->group(function () {
        Route::get('/', [POSController::class, 'index'])->name('index');
        Route::get('/open', [POSController::class, 'open'])->name('open');
        Route::post('/open', [POSController::class, 'storeOpen'])->name('store_open');
        Route::get('/close', [POSController::class, 'close'])->name('close');
        Route::post('/close', [POSController::class, 'storeClose'])->name('store_close');

        // API routes pour le POS
        Route::post('/cart/add', [POSController::class, 'addToCart'])->name('cart.add');
        Route::delete('/cart/remove', [POSController::class, 'removeFromCart'])->name('cart.remove');
        Route::patch('/cart/update-quantity', [POSController::class, 'updateCartQuantity'])->name('cart.update_quantity');
        Route::delete('/cart/clear', [POSController::class, 'clearCart'])->name('cart.clear');
        Route::get('/cart', [POSController::class, 'getCart'])->name('cart.get');
        Route::post('/checkout', [POSController::class, 'checkout'])->name('checkout');
        Route::get('/search-products', [POSController::class, 'searchProducts'])->name('search_products');
    });
});
