<?php

use App\Http\Controllers\SaaSRegistrationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\FlutterwaveSubscriptionController;
use App\Http\Controllers\PaystackSubscriptionController;
use Illuminate\Support\Facades\Route;

// Inscription SaaS publique (création tenant + compte admin)
Route::middleware('guest')->group(function () {
    Route::get('/saas/register', [SaaSRegistrationController::class, 'showRegistrationForm'])->name('saas.register');
    Route::post('/saas/register', [SaaSRegistrationController::class, 'register'])->name('saas.register.store');
});

Route::get('/pricing', [SubscriptionController::class, 'pricing'])->name('pricing');

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/tenant/create', [SaaSRegistrationController::class, 'createTenant'])->name('tenant.create');
    Route::post('/tenant/create', [SaaSRegistrationController::class, 'storeTenant'])->name('tenant.store');

    Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');

    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'show'])->name('show');
        Route::post('/upgrade', [SubscriptionController::class, 'upgrade'])->name('upgrade');
        Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
        Route::post('/payment-method', [SubscriptionController::class, 'updatePaymentMethod'])->name('payment_method.update');
        Route::post('/payment-intent', [SubscriptionController::class, 'paymentIntent'])->name('payment_intent');
    });

    Route::prefix('subscription/flutterwave')->name('subscription.flutterwave.')->group(function () {
        Route::get('/', [FlutterwaveSubscriptionController::class, 'show'])->name('show');
        Route::post('/initialize', [FlutterwaveSubscriptionController::class, 'initializePayment'])->name('initialize');
        Route::post('/cancel', [FlutterwaveSubscriptionController::class, 'cancel'])->name('cancel');
        Route::get('/public-key', [FlutterwaveSubscriptionController::class, 'getPublicKey'])->name('public_key');
    });
    Route::get('/subscription/flutterwave/callback', [FlutterwaveSubscriptionController::class, 'callback'])->name('subscription.flutterwave.callback');

    Route::prefix('subscription/paystack')->name('subscription.paystack.')->group(function () {
        Route::get('/', [PaystackSubscriptionController::class, 'show'])->name('show');
        Route::post('/initialize', [PaystackSubscriptionController::class, 'initializePayment'])->name('initialize');
        Route::post('/cancel', [PaystackSubscriptionController::class, 'cancel'])->name('cancel');
        Route::get('/public-key', [PaystackSubscriptionController::class, 'getPublicKey'])->name('public_key');
    });
    Route::get('/subscription/paystack/callback', [PaystackSubscriptionController::class, 'callback'])->name('subscription.paystack.callback');
});
