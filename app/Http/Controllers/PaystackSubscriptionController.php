<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Services\PaystackService;
use Illuminate\Support\Facades\Auth;

class PaystackSubscriptionController extends Controller
{
    protected $paystack;

    public function __construct()
    {
        $this->paystack = new PaystackService();
    }

    /**
     * Afficher le dashboard d'abonnement Paystack
     */
    public function show()
    {
        $tenant = app('currentTenant');
        
        if (!$tenant) {
            return redirect()->route('tenant.create');
        }

        $plan = $tenant->getPlanConfig();
        $plans = config('plans');

        return view('subscription.paystack_show', compact('tenant', 'plan', 'plans'));
    }

    /**
     * Initialiser le paiement Paystack
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
            $amount = $planConfig['price'] * 100; // En kobo (FCFA)

            // Créer le client Paystack si nécessaire
            if (!$tenant->paystack_customer_code) {
                $customer = $this->paystack->createCustomer([
                    'email' => $tenant->email,
                    'first_name' => explode(' ', $tenant->name)[0] ?? '',
                    'last_name' => explode(' ', $tenant->name)[1] ?? '',
                    'phone' => $tenant->phone,
                ]);

                if ($customer && $customer['status']) {
                    $tenant->update([
                        'paystack_customer_code' => $customer['data']['customer_code'],
                        'payment_provider' => 'paystack',
                    ]);
                }
            }

            // Initialiser la transaction
            $response = $this->paystack->initializeTransaction([
                'amount' => $amount,
                'email' => $tenant->email,
                'plan' => $planConfig['paystack_plan_code'] ?? null,
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'plan' => $plan,
                ],
                'callback_url' => route('paystack.callback'),
            ]);

            if ($response && $response['status']) {
                return redirect($response['data']['authorization_url']);
            }

            return back()->with('error', 'Erreur lors de l\'initialisation du paiement');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Callback Paystack après paiement
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('subscription.show')->with('error', 'Référence de transaction manquante');
        }

        try {
            $response = $this->paystack->verifyTransaction($reference);

            if ($response && $response['status'] && $response['data']['status'] === 'success') {
                $metadata = $response['data']['metadata'];
                $tenant = Tenant::find($metadata['tenant_id']);
                $plan = $metadata['plan'];

                if ($tenant) {
                    // Mettre à jour le tenant
                    $tenant->update([
                        'plan' => $plan,
                        'subscription_ends_at' => now()->addMonth(),
                        'payment_provider' => 'paystack',
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
     * Annuler l'abonnement Paystack
     */
    public function cancel()
    {
        $tenant = app('currentTenant');

        if (!$tenant) {
            return back()->with('error', 'Tenant non trouvé');
        }

        try {
            if ($tenant->paystack_subscription_code && $tenant->paystack_email_token) {
                $response = $this->paystack->cancelSubscription(
                    $tenant->paystack_subscription_code,
                    $tenant->paystack_email_token
                );

                if ($response && $response['status']) {
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
     * Obtenir la clé publique Paystack
     */
    public function getPublicKey()
    {
        return response()->json([
            'public_key' => $this->paystack->getPublicKey(),
        ]);
    }
}
