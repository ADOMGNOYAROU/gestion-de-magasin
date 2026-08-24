<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BoutiqueController;
use App\Http\Controllers\Api\CashRegisterSessionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EntreeStockController;
use App\Http\Controllers\Api\FournisseurController;
use App\Http\Controllers\Api\MagasinController;
use App\Http\Controllers\Api\MobileMoneyController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PartenaireController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\ProduitController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\TransfertController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VenteController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Webhook public appelé par PayGateGlobal (nécessite une URL joignable depuis internet).
Route::post('/webhooks/paygate', [MobileMoneyController::class, 'webhook']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource('produits', ProduitController::class)->except(['show']);
    Route::post('/produits/{id}/restore', [ProduitController::class, 'restore']);

    Route::apiResource('magasins', MagasinController::class)->except(['show']);
    Route::apiResource('boutiques', BoutiqueController::class)->except(['show']);
    Route::apiResource('fournisseurs', FournisseurController::class)->except(['show']);
    Route::apiResource('partenaires', PartenaireController::class)->except(['show']);
    Route::apiResource('users', UserController::class)->except(['show']);

    Route::get('/stock-magasins', [StockController::class, 'magasins']);
    Route::get('/stock-boutiques', [StockController::class, 'boutiques']);
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);

    Route::get('/entrees-stock', [EntreeStockController::class, 'index']);
    Route::post('/entrees-stock', [EntreeStockController::class, 'store']);

    Route::get('/transferts', [TransfertController::class, 'index']);
    Route::post('/transferts', [TransfertController::class, 'store']);
    Route::get('/stock-disponible', [TransfertController::class, 'stockDisponible']);

    Route::get('/cash-register-session/current', [CashRegisterSessionController::class, 'current']);
    Route::post('/cash-register-session/open', [CashRegisterSessionController::class, 'open']);
    Route::post('/cash-register-session/{cashRegisterSession}/close', [CashRegisterSessionController::class, 'close']);

    Route::get('/ventes', [VenteController::class, 'index']);
    Route::post('/ventes', [VenteController::class, 'store']);

    Route::post('/mobile-money/pay', [MobileMoneyController::class, 'pay']);
    Route::get('/mobile-money/{identifier}/status', [MobileMoneyController::class, 'status']);
    Route::get('/mobile-money/balance', [MobileMoneyController::class, 'balance']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});
