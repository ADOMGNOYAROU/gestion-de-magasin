<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Afficher le dashboard d'abonnement
     */
    public function show()
    {
        $tenant = app('currentTenant');
        
        if (!$tenant) {
            return redirect()->route('tenant.create');
        }

        $plan = $tenant->getPlanConfig();
        $plans = config('plans');

        return view('subscription.show', compact('tenant', 'plan', 'plans'));
    }

    /**
     * Afficher la page de pricing
     */
    public function pricing()
    {
        $plans = config('plans');
        return view('pricing', compact('plans'));
    }

    /**
     * Afficher la page d'abonnement expiré
     */
    public function expired()
    {
        $tenant = app('currentTenant');
        $plans = config('plans');
        
        return view('subscription.expired', compact('tenant', 'plans'));
    }

    /**
     * Changer de plan (upgrade/downgrade)
     */
    public function upgrade(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:starter,pro,enterprise',
            'payment_method' => 'required|string',
        ]);

        $tenant = app('currentTenant');

        if (!$tenant) {
            return back()->with('error', 'Tenant non trouvé');
        }

        try {
            $plan = $validated['plan'];
            $planConfig = config('plans.' . $plan);
            $priceId = $planConfig['stripe_price_id'];

            // Si l'utilisateur a déjà un abonnement, le changer
            if ($tenant->subscribed('default')) {
                $tenant->subscription('default')->swap($priceId);
            } else {
                // Créer un nouvel abonnement
                $tenant->newSubscription('default', $priceId)
                    ->create($validated['payment_method']);
            }

            // Mettre à jour le plan dans la base de données
            $tenant->update(['plan' => $plan]);

            return redirect()->route('subscription.show')
                ->with('success', 'Votre abonnement a été mis à jour avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Annuler l'abonnement
     */
    public function cancel()
    {
        $tenant = app('currentTenant');

        if (!$tenant) {
            return back()->with('error', 'Tenant non trouvé');
        }

        try {
            if ($tenant->subscribed('default')) {
                $tenant->subscription('default')->cancel();
            }

            return redirect()->route('subscription.show')
                ->with('success', 'Votre abonnement sera annulé à la fin de la période actuelle.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    /**
     * Reprendre un abonnement annulé
     */
    public function resume()
    {
        $tenant = app('currentTenant');

        if (!$tenant) {
            return back()->with('error', 'Tenant non trouvé');
        }

        try {
            if ($tenant->subscription('default') && $tenant->subscription('default')->cancelled()) {
                $tenant->subscription('default')->resume();
            }

            return redirect()->route('subscription.show')
                ->with('success', 'Votre abonnement a été réactivé!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la réactivation: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour la méthode de paiement
     */
    public function updatePaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string',
        ]);

        $tenant = app('currentTenant');

        if (!$tenant) {
            return back()->with('error', 'Tenant non trouvé');
        }

        try {
            $tenant->updateDefaultPaymentMethod($validated['payment_method']);
            $tenant->updateDefaultPaymentMethodFromStripe();

            return back()->with('success', 'Méthode de paiement mise à jour avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir l'intention de paiement Stripe
     */
    public function paymentIntent(Request $request)
    {
        $tenant = app('currentTenant');

        if (!$tenant) {
            return response()->json(['error' => 'Tenant non trouvé'], 404);
        }

        try {
            $plan = $request->get('plan', 'starter');
            $planConfig = config('plans.' . $plan);
            $amount = $planConfig['price'] * 100; // En centimes

            $paymentIntent = $tenant->createSetupIntent([
                'amount' => $amount,
                'currency' => 'eur',
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
