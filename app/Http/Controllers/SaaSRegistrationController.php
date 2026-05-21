<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;

class SaaSRegistrationController extends Controller
{
    /**
     * Afficher le formulaire d'inscription SaaS
     */
    public function showRegistrationForm()
    {
        return view('saas.register');
    }

    /**
     * Traiter l'inscription SaaS
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'plan' => 'required|in:starter,pro,enterprise',
        ]);

        DB::beginTransaction();
        try {
            // Créer le tenant
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'slug' => Str::slug($validated['company_name']) . '-' . Str::random(6),
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'plan' => $validated['plan'],
                'is_active' => true,
                'trial_ends_at' => now()->addDays(config('plans.trial_days', 14)),
                'subscription_ends_at' => now()->addDays(config('plans.trial_days', 14)),
            ]);

            // Créer l'utilisateur admin du tenant
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
                'tenant_id' => $tenant->id,
            ]);

            // Créer le client Stripe uniquement pour les plans payants
            if ($validated['plan'] !== 'starter') {
                try {
                    $tenant->createAsStripeCustomer();
                } catch (\Exception $e) {
                    // Si Stripe n'est pas configuré, continuer quand même
                    // L'utilisateur pourra configurer le paiement plus tard
                }
            }

            DB::commit();

            // Connecter l'utilisateur
            Auth::login($user);
            event(new Registered($user));

            // Rediriger vers le dashboard pour l'essai gratuit, ou vers la page de paiement pour les plans payants
            if ($validated['plan'] === 'starter') {
                return redirect()->route('dashboard')
                    ->with('success', 'Votre compte a été créé avec succès! Profitez de votre essai gratuit de 14 jours.');
            } else {
                return redirect()->route('subscription.show')
                    ->with('success', 'Votre compte a été créé avec succès! Veuillez configurer votre abonnement.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'inscription: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire de création de tenant (pour utilisateurs existants)
     */
    public function createTenant()
    {
        return view('saas.create-tenant');
    }

    /**
     * Créer un tenant pour un utilisateur existant
     */
    public function storeTenant(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'plan' => 'required|in:starter,pro,enterprise',
        ]);

        try {
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'slug' => Str::slug($validated['company_name']) . '-' . Str::random(6),
                'email' => auth()->user()->email,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'plan' => $validated['plan'],
                'is_active' => true,
                'trial_ends_at' => now()->addDays(config('plans.trial_days', 14)),
                'subscription_ends_at' => now()->addDays(config('plans.trial_days', 14)),
            ]);

            // Associer l'utilisateur au tenant
            auth()->user()->update([
                'tenant_id' => $tenant->id,
            ]);

            // Créer le client Stripe
            $tenant->createAsStripeCustomer();

            return redirect()->route('subscription.show')
                ->with('success', 'Votre entreprise a été créée avec succès!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }
}
