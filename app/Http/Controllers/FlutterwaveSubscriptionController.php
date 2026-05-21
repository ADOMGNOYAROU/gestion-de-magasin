<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Services\FlutterwaveService;
use Illuminate\Support\Facades\Auth;

class FlutterwaveSubscriptionController extends Controller
{
    protected $flutterwave;

    public function __construct()
    {
        $this->flutterwave = new FlutterwaveService();
    }

    /**
     * Afficher le dashboard d'abonnement Flutterwave
     */
    public function show()
    {
        $tenant = app('currentTenant');
        
        if (!$tenant) {
            return redirect()->route('tenant.create');
        }

        $plan = $tenant->getPlanConfig();
        $plans = config('plans');

        return view('subscription.flutterwave_show', compact('tenant', 'plan', 'plans'));
    }

    /**
     * Initialiser le paiement Flutterwave
     */
    public function initializePayment(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:starter,pro,enterprise',
        ]);

        $tenant = app('currentTenant');

        if (!$tenant) {
            return back()->with('error', 'Tenant non trouvé');
        }

        try {
            $plan = $validated['plan'];
            $planConfig = config('plans.' . $plan);
            $amount = $planConfig['price']; // En FCFA

            // Créer le client Flutterwave si nécessaire
            if (!$tenant->flutterwave_customer_id) {
                $customer = $this->flutterwave->createCustomer([
                    'email' => $tenant->email,
                    'name' => $tenant->name,
                    'phone_number' => $tenant->phone,
                ]);

                if ($customer && $customer['status'] === 'success') {
                    $tenant->update([
                        'flutterwave_customer_id' => $customer['data']['id'],
                        'payment_provider' => 'flutterwave',
                    ]);
                }
            }

            // Initialiser la transaction
            $response = $this->flutterwave->initializeTransaction([
                'tx_ref' => 'sub_' . $tenant->id . '_' . time(),
                'amount' => $amount,
                'currency' => 'XAF', // Franc CFA
                'email' => $tenant->email,
                'payment_plan' => $planConfig['flutterwave_plan_id'] ?? null,
                'meta' => [
                    'tenant_id' => $tenant->id,
                    'plan' => $plan,
                ],
                'redirect_url' => route('flutterwave.callback'),
            ]);

            if ($response && $response['status'] === 'success') {
                return redirect($response['data']['link']);
            }

            return back()->with('error', 'Erreur lors de l\'initialisation du paiement');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Callback Flutterwave après paiement
     */
    public function callback(Request $request)
    {
        $transactionId = $request->query('transaction_id');

        if (!$transactionId) {
            return redirect()->route('subscription.show')->with('error', 'ID de transaction manquant');
        }

        try {
            $response = $this->flutterwave->verifyTransaction($transactionId);

            if ($response && $response['status'] === 'success' && $response['data']['status'] === 'successful') {
                $metadata = $response['data']['meta'];
                $tenant = Tenant::find($metadata['tenant_id']);
                $plan = $metadata['plan'];

                if ($tenant) {
                    // Mettre à jour le tenant
                    $tenant->update([
                        'plan' => $plan,
                        'subscription_ends_at' => now()->addMonth(),
                        'payment_provider' => 'flutterwave',
                    ]);

                    return redirect()->route('subscription.show')
                        ->with('success', 'Paiement réussi ! Votre abonnement est actif.');
                }
            }

            return redirect()->route('subscription.show')
                ->with('error', 'Paiement échoué');

        } catch (\Exception $e) {
            return redirect()->route('subscription.show')
                ->with('error', 'Erreur lors de la vérification: ' . $e->getMessage());
        }
    }

    /**
     * Annuler l'abonnement Flutterwave
     */
    public function cancel()
    {
        $tenant = app('currentTenant');

        if (!$tenant) {
            return back()->with('error', 'Tenant non trouvé');
        }

        try {
            if ($tenant->flutterwave_subscription_id) {
                $response = $this->flutterwave->cancelSubscription(
                    $tenant->flutterwave_subscription_id
                );

                if ($response && $response['status'] === 'success') {
                    return redirect()->route('subscription.show')
                        ->with('success', 'Abonnement annulé avec succès');
                }
            }

            // Annuler localement si l'API échoue
            $tenant->update([
                'subscription_ends_at' => now()->addMonth(),
            ]);

            return redirect()->route('subscription.show')
                ->with('success', 'Abonnement sera annulé à la fin de la période actuelle');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir la clé publique Flutterwave
     */
    public function getPublicKey()
    {
        return response()->json([
            'public_key' => $this->flutterwave->getPublicKey(),
        ]);
    }
}
