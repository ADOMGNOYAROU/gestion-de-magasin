<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PointFidelite;
use App\Models\Coupon;
use App\Models\PreferenceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function __construct()
    {
        // Les middlewares sont gérés directement dans les routes
    }

    /**
     * Display a listing of clients.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statut = $request->get('statut');
        $tri = $request->get('tri', 'nom');
        $direction = $request->get('direction', 'asc');

        $query = Client::with(['preferences']);

        // Recherche
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        // Filtre par statut
        if ($statut) {
            $query->where('statut', $statut);
        }

        // Tri
        $query->orderBy($tri, $direction);

        $clients = $query->paginate(15);

        return view('clients.index', compact('clients', 'search', 'statut', 'tri', 'direction'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:clients,email',
            'telephone' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date|before:today',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'sexe' => 'nullable|in:M,F',
            'notifications_email' => 'boolean',
            'notifications_sms' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $client = Client::create($validated);

            // Créer les préférences par défaut
            $client->preferences()->create([
                'notifications_email' => $validated['notifications_email'] ?? true,
                'notifications_sms' => $validated['notifications_sms'] ?? false,
                'canal_prefere' => 'magasin',
            ]);

            DB::commit();

            return redirect()->route('clients.show', $client)
                           ->with('success', 'Client créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la création du client: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client)
    {
        $client->load(['ventes' => function($query) {
            $query->latest()->take(10);
        }, 'points' => function($query) {
            $query->latest()->take(10);
        }, 'coupons' => function($query) {
            $query->where('utilise', false)->latest();
        }, 'preferences']);

        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        $client->load('preferences');

        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'telephone' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date|before:today',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'sexe' => 'nullable|in:M,F',
            'statut' => 'required|in:actif,inactif',
            'notifications_email' => 'boolean',
            'notifications_sms' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $client->update($validated);

            // Mettre à jour les préférences
            if ($client->preferences) {
                $client->preferences->update([
                    'notifications_email' => $validated['notifications_email'] ?? true,
                    'notifications_sms' => $validated['notifications_sms'] ?? false,
                ]);
            } else {
                $client->preferences()->create([
                    'notifications_email' => $validated['notifications_email'] ?? true,
                    'notifications_sms' => $validated['notifications_sms'] ?? false,
                    'canal_prefere' => 'magasin',
                ]);
            }

            DB::commit();

            return redirect()->route('clients.show', $client)
                           ->with('success', 'Client mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        try {
            $client->delete();

            return redirect()->route('clients.index')
                           ->with('success', 'Client supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    /**
     * Ajouter des points à un client.
     */
    public function ajouterPoints(Request $request, Client $client)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);

        try {
            $client->ajouterPoints($validated['points'], $validated['description']);

            return redirect()->route('clients.show', $client)
                           ->with('success', $validated['points'] . ' points ajoutés avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Utiliser des points d'un client.
     */
    public function utiliserPoints(Request $request, Client $client)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1|max:' . $client->solde_points,
            'description' => 'required|string|max:255',
        ]);

        try {
            $client->utiliserPoints($validated['points'], $validated['description']);

            return redirect()->route('clients.show', $client)
                           ->with('success', $validated['points'] . ' points utilisés avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Générer un coupon pour un client.
     */
    public function genererCoupon(Request $request, Client $client)
    {
        $validated = $request->validate([
            'type' => 'required|in:pourcentage,montant_fixe',
            'valeur' => 'required|numeric|min:0.01',
            'jours_expiration' => 'nullable|integer|min:1|max:365',
        ]);

        try {
            $coupon = $client->genererCoupon(
                $validated['type'],
                $validated['valeur'],
                $validated['jours_expiration'] ?? null
            );

            return redirect()->route('clients.show', $client)
                           ->with('success', 'Coupon généré: ' . $coupon->code);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Recherche rapide de clients (API pour AJAX).
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $clients = Client::where('statut', 'actif')
                        ->where(function($q) use ($query) {
                            $q->where('nom', 'like', "%{$query}%")
                              ->orWhere('prenom', 'like', "%{$query}%")
                              ->orWhere('email', 'like', "%{$query}%")
                              ->orWhere('telephone', 'like', "%{$query}%");
                        })
                        ->select('id', 'nom', 'prenom', 'email', 'telephone', 'solde_points')
                        ->limit(10)
                        ->get()
                        ->map(function($client) {
                            return [
                                'id' => $client->id,
                                'text' => $client->nom_complet . ' (' . $client->solde_points . ' pts)',
                                'nom_complet' => $client->nom_complet,
                                'email' => $client->email,
                                'telephone' => $client->telephone,
                                'solde_points' => $client->solde_points,
                            ];
                        });

        return response()->json($clients);
    }

    /**
     * Valider un coupon.
     */
    public function validerCoupon(Request $request)
    {
        $code = $request->get('code');
        $clientId = $request->get('client_id');
        $montantTotal = $request->get('montant_total', 0);

        $coupon = Coupon::where('code', $code)
                       ->where('client_id', $clientId)
                       ->where('utilise', false)
                       ->where(function($q) {
                           $q->whereNull('date_expiration')
                             ->orWhere('date_expiration', '>', now());
                       })
                       ->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon invalide ou expiré.'
            ]);
        }

        $reduction = $coupon->calculerReduction($montantTotal);

        return response()->json([
            'valid' => true,
            'reduction' => $reduction,
            'type' => $coupon->type,
            'valeur' => $coupon->valeur,
            'description' => $coupon->description_type,
            'coupon_id' => $coupon->id
        ]);
    }

    /**
     * Utiliser un coupon.
     */
    public function utiliserCoupon(Request $request, Coupon $coupon)
    {
        try {
            $coupon->utiliser();

            return response()->json([
                'success' => true,
                'message' => 'Coupon utilisé avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
