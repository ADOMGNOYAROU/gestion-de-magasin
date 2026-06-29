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
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

// Authentication routes handled by Laravel Breeze
require __DIR__.'/auth.php';

// Inscription SaaS, abonnements et paiements (Stripe/Paystack/Flutterwave)
require __DIR__.'/subscription.php';

// Language switch route
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'vendeur'])->name('dashboard');

Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('read');
    Route::post('/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('read_all');
});

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
    
    // Routes CRUD pour les boutiques (admin et gestionnaire uniquement)
    Route::resource('boutiques', \App\Http\Controllers\BoutiqueController::class)->middleware('gestionnaire');
    
    // Routes CRUD pour les utilisateurs (admin uniquement)
    Route::resource('users', \App\Http\Controllers\UserController::class)->middleware('admin');
    
    // Routes CRUD pour les magasins : création/suppression admin uniquement,
    // consultation/édition ouvertes au gestionnaire de son propre magasin (Gate manage-magasin)
    Route::resource('magasins', \App\Http\Controllers\MagasinController::class)
        ->only(['index', 'create', 'store', 'destroy'])
        ->middleware('admin');
    Route::resource('magasins', \App\Http\Controllers\MagasinController::class)
        ->only(['show', 'edit', 'update'])
        ->middleware('gestionnaire');
    
    // Routes CRUD pour les fournisseurs (admin et gestionnaire uniquement)
    Route::resource('fournisseurs', FournisseurController::class)->middleware('gestionnaire');
    
    // Routes CRUD pour les commandes fournisseurs (admin et gestionnaire uniquement)
    Route::resource('orders', \App\Http\Controllers\OrderController::class)->middleware('gestionnaire')->names([
        'index' => 'orders.index',
        'create' => 'orders.create',
        'store' => 'orders.store',
        'show' => 'orders.show',
        'edit' => 'orders.edit',
        'update' => 'orders.update',
        'destroy' => 'orders.destroy'
    ]);
    Route::post('orders/{id}/livrer', [\App\Http\Controllers\OrderController::class, 'livrer'])->middleware('gestionnaire')->name('orders.livrer');
    Route::get('orders/price-comparison', [\App\Http\Controllers\OrderController::class, 'priceComparison'])->middleware('gestionnaire')->name('orders.price_comparison');
    Route::get('orders/auto-replenishment', [\App\Http\Controllers\OrderController::class, 'autoReplenishment'])->middleware('gestionnaire')->name('orders.auto_replenishment');
    Route::post('orders/generate-replenishment', [\App\Http\Controllers\OrderController::class, 'generateReplenishmentOrders'])->middleware('gestionnaire')->name('orders.generate_replenishment');
    
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
    
    // Routes CRUD pour les clients (tous les rôles)
    Route::resource('clients', \App\Http\Controllers\ClientController::class)->middleware('vendeur')->names([
        'index' => 'clients.index',
        'create' => 'clients.create',
        'store' => 'clients.store',
        'show' => 'clients.show',
        'edit' => 'clients.edit',
        'update' => 'clients.update',
        'destroy' => 'clients.destroy'
    ]);
    
    // Routes CRUD pour les crédits (tous les rôles avec manage-ventes)
    Route::resource('credits', \App\Http\Controllers\CreditController::class)->middleware('vendeur')->names([
        'index' => 'credits.index',
        'create' => 'credits.create',
        'store' => 'credits.store',
        'show' => 'credits.show',
        'edit' => 'credits.edit',
        'update' => 'credits.update',
        'destroy' => 'credits.destroy'
    ]);
    Route::get('credits/{id}/add-payment', [\App\Http\Controllers\CreditController::class, 'createPayment'])->middleware('vendeur')->name('credits.add_payment');
    Route::post('credits/{id}/add-payment', [\App\Http\Controllers\CreditController::class, 'storePayment'])->middleware('vendeur')->name('credits.store_payment');
    
    // Route pour le reçu de vente
    Route::get('/ventes/{vente}/recu', [VenteController::class, 'recu'])->middleware('vendeur')->name('ventes.recu');
    
    // Routes pour les rapports (admin et gestionnaire uniquement)
    Route::get('/rapports', [RapportController::class, 'index'])->middleware('gestionnaire')->name('rapports.index');
    Route::get('/rapports/stock/pdf', [RapportController::class, 'rapportStockPDF'])->middleware('gestionnaire')->name('rapports.stock.pdf');
    Route::get('/rapports/ventes/form', [RapportController::class, 'rapportVentesForm'])->middleware('gestionnaire')->name('rapports.ventes.form');
    Route::post('/rapports/ventes/pdf', [RapportController::class, 'rapportVentesPDF'])->middleware('gestionnaire')->name('rapports.ventes.pdf');
    Route::post('/rapports/ventes/excel', [RapportController::class, 'rapportVentesExcel'])->middleware('gestionnaire')->name('rapports.ventes.excel');
    Route::get('/rapports/partenaires/pdf', [RapportController::class, 'rapportPartenairesPDF'])->middleware('gestionnaire')->name('rapports.partenaires.pdf');
    Route::get('/rapports/credits/form', [RapportController::class, 'rapportCreditsForm'])->middleware('gestionnaire')->name('rapports.credits.form');
    Route::post('/rapports/credits/pdf', [RapportController::class, 'rapportCreditsPDF'])->middleware('gestionnaire')->name('rapports.credits.pdf');
    Route::post('/rapports/credits/excel', [RapportController::class, 'rapportCreditsExcel'])->middleware('gestionnaire')->name('rapports.credits.excel');
    
    // Routes API pour les transferts
    Route::get('/api/stock-disponible', [TransfertController::class, 'getStockDisponible'])->middleware('gestionnaire');
    Route::get('/api/boutiques-par-magasin', [TransfertController::class, 'getBoutiquesByMagasin'])->middleware('gestionnaire');
    
    // Routes API pour les ventes (panier)
    Route::post('/api/panier/ajouter', [VenteController::class, 'ajouterPanier'])->middleware('vendeur');
    Route::delete('/api/panier/retirer', [VenteController::class, 'retirerPanier'])->middleware('vendeur');
    Route::delete('/api/panier/vider', [VenteController::class, 'viderPanier'])->middleware('vendeur');
    Route::get('/api/stock-boutique', [VenteController::class, 'getStockDisponible'])->middleware('vendeur');
    
    // Routes pour le système de caisse (POS)
    Route::prefix('pos')->name('pos.')->middleware(['auth', 'vendeur'])->group(function () {
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
