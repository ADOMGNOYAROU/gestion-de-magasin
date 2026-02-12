<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Produit;
use App\Models\Vente;
use App\Models\VenteProduit;
use App\Models\PaymentMethod;
use App\Models\CashRegisterSession;
use App\Models\StockBoutique;

class POSController extends Controller
{
    public function __construct()
    {
        // Remove middleware for now - handle auth in methods
    }

    /**
     * Afficher l'interface de caisse
     */
    public function index()
    {
        try {
            // Handle authentication here instead of middleware
            if (!Auth::check()) {
                \Log::warning('POS access attempt without authentication');
                return redirect()->route('login')->with('error', 'Vous devez être connecté.');
            }

            $user = Auth::user();
            \Log::info('POS accessed by user: ' . $user->name . ' (role: ' . $user->role . ')');

            // Vérifier que l'utilisateur est un vendeur OU un admin/gestionnaire
            if (!$user->isVendeur() && !$user->isAdmin() && !$user->isGestionnaire()) {
                \Log::warning('Unauthorized POS access attempt by user: ' . $user->name);
                return redirect()->route('dashboard')
                    ->with('error', 'Accès non autorisé. Seuls les vendeurs, gestionnaires et administrateurs peuvent accéder à la caisse.');
            }

            // Pour les vendeurs : vérifier boutique assignée
            if ($user->isVendeur()) {
                \Log::info('POS access for vendeur: ' . $user->name);

                $boutique = $user->boutique;
                if (!$boutique) {
                    \Log::warning('Vendeur without assigned boutique: ' . $user->name);
                    return redirect()->route('dashboard')
                        ->with('error', 'Aucune boutique ne vous est assignée. Contactez un administrateur.');
                }

                // Vérifier la session de caisse active du vendeur
                $sessionActive = CashRegisterSession::where('vendeur_id', Auth::id())
                                                  ->where('boutique_id', $boutique->id)
                                                  ->whereIn('status', ['ouverte', 'en_cours'])
                                                  ->first();

                if (!$sessionActive) {
                    \Log::info('No active session for vendeur: ' . $user->name);
                    return redirect()->route('pos.open')
                        ->with('info', 'Vous devez ouvrir une session de caisse avant de commencer les ventes.');
                }

                \Log::info('Loading products for vendeur: ' . $user->name);

                // Produits avec stock pour le vendeur
                $produits = Produit::where('statut', 'actif')
                                  ->with(['stockBoutiques' => function($query) use ($boutique) {
                                      $query->where('boutique_id', $boutique->id);
                                  }])
                                  ->orderBy('nom')
                                  ->get()
                                  ->map(function($produit) use ($boutique) {
                                      $stockBoutique = $produit->stockBoutiques->first();
                                      $produit->stock_disponible = $stockBoutique ? $stockBoutique->quantite : 0;
                                      return $produit;
                                  });

                $paymentMethods = PaymentMethod::active()->get();

                \Log::info('POS view rendered for vendeur: ' . $user->name);
                return view('pos.index', compact('produits', 'paymentMethods', 'sessionActive', 'boutique'));

            // Pour admin/gestionnaire : interface de gestion des caisses
            } else {
                \Log::info('POS admin access for user: ' . $user->name . ' (role: ' . $user->role . ')');

                $boutiques = collect();
                $sessionsActives = collect();

                try {
                    if ($user->isAdmin()) {
                        \Log::info('Loading all boutiques for admin');
                        $boutiques = \App\Models\Boutique::all(); // Remove with(['magasin']) temporarily
                    } elseif ($user->isGestionnaire()) {
                        \Log::info('Loading boutiques for gestionnaire: ' . $user->name);
                        $magasin = $user->magasinResponsable;
                        if ($magasin) {
                            $boutiques = $magasin->boutiques; // Remove with('magasin') temporarily
                        } else {
                            \Log::warning('Gestionnaire without magasin: ' . $user->name);
                        }
                    }

                    \Log::info('Boutiques loaded: ' . $boutiques->count());

                    \Log::info('Loading active cash sessions');
                    $sessionsActives = CashRegisterSession::whereIn('status', ['ouverte', 'en_cours'])->get();
                    \Log::info('Sessions loaded: ' . $sessionsActives->count());

                } catch (\Exception $e) {
                    \Log::error('Database error in POS admin: ' . $e->getMessage());
                    \Log::error('Stack trace: ' . $e->getTraceAsString());

                    // Return view with empty collections
                    $boutiques = collect();
                    $sessionsActives = collect();
                }

                \Log::info('POS admin view rendered for user: ' . $user->name);
                return view('pos.admin', compact('boutiques', 'sessionsActives'));
            }

        } catch (\Exception $e) {
            \Log::error('Critical error in POSController@index: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return a simple error response instead of crashing
            return response()->view('errors.500', [
                'message' => 'Une erreur est survenue lors du chargement de la caisse.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Ouvrir une session de caisse
     */
    public function open(Request $request = null)
    {
        $user = Auth::user();

        // Pour les vendeurs
        if ($user->isVendeur()) {
            $boutique = $user->boutique;
            if (!$boutique) {
                return redirect()->route('dashboard')
                    ->with('error', 'Aucune boutique ne vous est assignée.');
            }

            // Vérifier s'il y a déjà une session ouverte
            $sessionExistante = CashRegisterSession::where('vendeur_id', Auth::id())
                                                  ->where('boutique_id', $boutique->id)
                                                  ->whereIn('status', ['ouverte', 'en_cours'])
                                                  ->first();

            if ($sessionExistante) {
                return redirect()->route('pos.index')
                    ->with('info', 'Une session de caisse est déjà ouverte.');
            }

            return view('pos.open', compact('boutique'));

        // Pour admin/gestionnaire : ouvrir caisse pour un vendeur
        } elseif ($user->isAdmin() || $user->isGestionnaire()) {
            // Récupérer tous les vendeurs disponibles
            $vendeurs = \App\Models\User::where('role', 'vendeur')
                                       ->when($user->isGestionnaire(), function($query) use ($user) {
                                           $magasin = $user->magasinResponsable;
                                           if ($magasin) {
                                               return $query->whereHas('boutique', function($q) use ($magasin) {
                                                   $q->where('magasin_id', $magasin->id);
                                               });
                                           }
                                           return $query;
                                       })
                                       ->get();

            return view('pos.admin_open', compact('vendeurs'));
        }

        return redirect()->route('dashboard')
            ->with('error', 'Accès non autorisé.');
    }

    /**
     * Stocker l'ouverture de session de caisse
     */
    public function storeOpen(Request $request)
    {
        $request->validate([
            'montant_initial' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'vendeur_id' => 'nullable|exists:users,id', // Pour admin
        ]);

        $user = Auth::user();
        $vendeurId = $request->vendeur_id ?: Auth::id();

        // Vérifier permissions
        if ($user->isVendeur()) {
            // Vendeur ne peut ouvrir que sa propre caisse
            if ($vendeurId !== Auth::id()) {
                return redirect()->back()
                    ->with('error', 'Vous ne pouvez ouvrir que votre propre caisse.');
            }
        } elseif ($user->isAdmin() || $user->isGestionnaire()) {
            // Admin/Gestionnaire peuvent ouvrir pour n'importe quel vendeur de leur périmètre
            $vendeur = \App\Models\User::findOrFail($vendeurId);
            if (!$vendeur->isVendeur()) {
                return redirect()->back()
                    ->with('error', 'L\'utilisateur sélectionné n\'est pas un vendeur.');
            }

            // Vérifier que le vendeur appartient au périmètre de l'admin/gestionnaire
            if ($user->isGestionnaire()) {
                $magasin = $user->magasinResponsable;
                if ($magasin && !$vendeur->boutique->magasin()->where('id', $magasin->id)->exists()) {
                    return redirect()->back()
                        ->with('error', 'Ce vendeur n\'appartient pas à votre magasin.');
                }
            }
        } else {
            return redirect()->back()
                ->with('error', 'Accès non autorisé.');
        }

        $vendeur = \App\Models\User::findOrFail($vendeurId);
        $boutique = $vendeur->boutique;

        if (!$boutique) {
            return redirect()->back()
                ->with('error', 'Le vendeur n\'a pas de boutique assignée.');
        }

        // Vérifier s'il y a déjà une session ouverte pour ce vendeur
        $sessionExistante = CashRegisterSession::where('vendeur_id', $vendeurId)
                                              ->where('boutique_id', $boutique->id)
                                              ->whereIn('status', ['ouverte', 'en_cours'])
                                              ->first();

        if ($sessionExistante) {
            return redirect()->back()
                ->with('error', 'Une session de caisse est déjà ouverte pour ce vendeur.');
        }

        try {
            CashRegisterSession::create([
                'vendeur_id' => $vendeurId,
                'boutique_id' => $boutique->id,
                'montant_initial' => $request->montant_initial,
                'date_ouverture' => now(),
                'status' => 'ouverte',
                'notes' => $request->notes,
            ]);

            $message = $user->isVendeur() ? 'Session de caisse ouverte avec succès.' : 'Session de caisse ouverte pour le vendeur avec succès.';

            return redirect()->route('pos.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'ouverture de la session : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Fermer une session de caisse
     */
    public function close(Request $request = null)
    {
        $user = Auth::user();

        // Pour les vendeurs
        if ($user->isVendeur()) {
            $session = CashRegisterSession::where('vendeur_id', Auth::id())
                                         ->whereIn('status', ['ouverte', 'en_cours'])
                                         ->first();

            if (!$session) {
                return redirect()->route('pos.open')
                    ->with('error', 'Aucune session de caisse active trouvée.');
            }

            // Calculer le montant théorique
            $session->calculerMontantTheorique();

            return view('pos.close', compact('session'));

        // Pour admin/gestionnaire : fermer caisse pour un vendeur
        } elseif ($user->isAdmin() || $user->isGestionnaire()) {
            $vendeurId = $request->get('vendeur_id');

            if (!$vendeurId) {
                // Afficher la liste des sessions actives pour sélection
                $sessions = CashRegisterSession::with(['vendeur', 'boutique'])
                                             ->whereIn('status', ['ouverte', 'en_cours'])
                                             ->when($user->isGestionnaire(), function($query) use ($user) {
                                                 $magasin = $user->magasinResponsable;
                                                 if ($magasin) {
                                                     return $query->whereHas('boutique', function($q) use ($magasin) {
                                                         $q->where('magasin_id', $magasin->id);
                                                     });
                                                 }
                                                 return $query;
                                             })
                                             ->get();

                return view('pos.admin_close_select', compact('sessions'));
            }

            // Fermer une session spécifique
            $session = CashRegisterSession::where('vendeur_id', $vendeurId)
                                         ->whereIn('status', ['ouverte', 'en_cours'])
                                         ->first();

            if (!$session) {
                return redirect()->back()
                    ->with('error', 'Aucune session de caisse active trouvée pour ce vendeur.');
            }

            // Vérifier permissions
            if ($user->isGestionnaire()) {
                $magasin = $user->magasinResponsable;
                if ($magasin && !$session->boutique->magasin()->where('id', $magasin->id)->exists()) {
                    return redirect()->back()
                        ->with('error', 'Cette session n\'appartient pas à votre magasin.');
                }
            }

            // Calculer le montant théorique
            $session->calculerMontantTheorique();

            return view('pos.admin_close', compact('session'));
        }

        return redirect()->route('dashboard')
            ->with('error', 'Accès non autorisé.');
    }

    /**
     * Stocker la fermeture de session de caisse
     */
    public function storeClose(Request $request)
    {
        $request->validate([
            'montant_final' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'session_id' => 'nullable|exists:cash_register_sessions,id', // Pour admin
        ]);

        $user = Auth::user();

        if ($request->session_id) {
            // Admin/Gestionnaire ferme une session spécifique
            $session = CashRegisterSession::findOrFail($request->session_id);

            // Vérifier permissions
            if ($user->isGestionnaire()) {
                $magasin = $user->magasinResponsable;
                if ($magasin && !$session->boutique->magasin()->where('id', $magasin->id)->exists()) {
                    return redirect()->back()
                        ->with('error', 'Cette session n\'appartient pas à votre magasin.');
                }
            }
        } else {
            // Vendeur ferme sa propre session
            if (!$user->isVendeur()) {
                return redirect()->back()
                    ->with('error', 'Accès non autorisé.');
            }

            $session = CashRegisterSession::where('vendeur_id', Auth::id())
                                         ->whereIn('status', ['ouverte', 'en_cours'])
                                         ->first();
        }

        if (!$session) {
            return redirect()->back()
                ->with('error', 'Aucune session de caisse active trouvée.');
        }

        try {
            $session->fermer($request->montant_final, $request->notes);

            $message = $user->isVendeur() ? 'Session de caisse fermée avec succès.' : 'Session de caisse fermée pour le vendeur avec succès.';

            return redirect()->route('dashboard')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la fermeture de la session : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Ajouter un produit au panier
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
        ]);

        $session = $this->getActiveSession();
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Aucune session de caisse active.'], 400);
        }

        $produit = Produit::findOrFail($request->produit_id);

        // Vérifier le stock disponible
        $stock = StockBoutique::where('produit_id', $request->produit_id)
                             ->where('boutique_id', $session->boutique_id)
                             ->first();

        $quantiteDisponible = $stock ? $stock->quantite : 0;

        if ($quantiteDisponible < $request->quantite) {
            return response()->json([
                'success' => false,
                'message' => "Stock insuffisant. Disponible: {$quantiteDisponible}"
            ], 400);
        }

        $panier = Session::get('pos_cart', []);

        // Vérifier si le produit est déjà dans le panier
        $produitExiste = false;
        foreach ($panier as &$item) {
            if ($item['produit_id'] == $request->produit_id) {
                $nouvelleQuantite = $item['quantite'] + $request->quantite;
                if ($nouvelleQuantite > $quantiteDisponible) {
                    return response()->json([
                        'success' => false,
                        'message' => "Quantité totale dépassant le stock disponible."
                    ], 400);
                }
                $item['quantite'] = $nouvelleQuantite;
                $produitExiste = true;
                break;
            }
        }

        if (!$produitExiste) {
            $panier[] = [
                'produit_id' => $request->produit_id,
                'quantite' => $request->quantite,
                'nom' => $produit->nom,
                'prix_unitaire' => $produit->prix_vente,
                'categorie' => $produit->categorie,
            ];
        }

        Session::put('pos_cart', $panier);

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté au panier',
            'cart' => $panier,
            'total_items' => count($panier)
        ]);
    }

    /**
     * Retirer un produit du panier
     */
    public function removeFromCart(Request $request)
    {
        $produitId = $request->get('produit_id');

        $panier = Session::get('pos_cart', []);

        $panier = array_filter($panier, function($item) use ($produitId) {
            return $item['produit_id'] != $produitId;
        });

        Session::put('pos_cart', array_values($panier));

        return response()->json([
            'success' => true,
            'message' => 'Produit retiré du panier',
            'cart' => $panier,
            'total_items' => count($panier)
        ]);
    }

    /**
     * Modifier la quantité d'un produit dans le panier
     */
    public function updateCartQuantity(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|integer',
            'quantite' => 'required|integer|min:0',
        ]);

        $panier = Session::get('pos_cart', []);

        foreach ($panier as &$item) {
            if ($item['produit_id'] == $request->produit_id) {
                if ($request->quantite == 0) {
                    // Supprimer l'item si quantité = 0
                    $panier = array_filter($panier, function($i) use ($request) {
                        return $i['produit_id'] != $request->produit_id;
                    });
                    $panier = array_values($panier);
                } else {
                    $item['quantite'] = $request->quantite;
                }
                break;
            }
        }

        Session::put('pos_cart', $panier);

        return response()->json([
            'success' => true,
            'cart' => $panier,
            'total_items' => count($panier)
        ]);
    }

    /**
     * Vider le panier
     */
    public function clearCart()
    {
        Session::forget('pos_cart');

        return response()->json([
            'success' => true,
            'message' => 'Panier vidé',
            'cart' => [],
            'total_items' => 0
        ]);
    }

    /**
     * Finaliser la vente
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'montant_recu' => 'required|numeric|min:0',
        ]);

        $session = $this->getActiveSession();
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Aucune session de caisse active.'], 400);
        }

        $panier = Session::get('pos_cart', []);
        if (empty($panier)) {
            return response()->json(['success' => false, 'message' => 'Le panier est vide.'], 400);
        }

        DB::beginTransaction();
        try {
            // Créer la vente
            $vente = Vente::create([
                'boutique_id' => $session->boutique_id,
                'user_id' => Auth::id(),
                'session_caisse_id' => $session->id,
                'payment_method_id' => $request->payment_method_id,
                'montant_recu' => $request->montant_recu,
                'date_vente' => now(),
                'status' => 'terminee',
            ]);

            $montantTotal = 0;

            // Ajouter les produits à la vente et mettre à jour le stock
            foreach ($panier as $item) {
                $produit = Produit::findOrFail($item['produit_id']);
                $prixUnitaire = $item['prix_unitaire'];
                $quantite = $item['quantite'];
                $sousTotal = $prixUnitaire * $quantite;

                // Créer VenteProduit
                VenteProduit::create([
                    'vente_id' => $vente->id,
                    'produit_id' => $item['produit_id'],
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'sous_total' => $sousTotal,
                ]);

                // Mettre à jour le stock
                $stock = StockBoutique::where('produit_id', $item['produit_id'])
                                     ->where('boutique_id', $session->boutique_id)
                                     ->first();

                if ($stock) {
                    $stock->quantite -= $quantite;
                    $stock->save();
                }

                $montantTotal += $sousTotal;
            }

            // Mettre à jour le montant total de la vente
            $vente->montant_total = $montantTotal;
            $vente->calculerMonnaie();
            $vente->save();

            // Mettre à jour la session
            $session->calculerMontantTheorique();

            DB::commit();

            // Vider le panier
            Session::forget('pos_cart');

            return response()->json([
                'success' => true,
                'message' => 'Vente finalisée avec succès',
                'vente_id' => $vente->id,
                'numero_ticket' => $vente->numero_ticket,
                'montant_total' => $montantTotal,
                'monnaie' => $vente->monnaie,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la finalisation : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les informations du panier actuel
     */
    public function getCart()
    {
        $panier = Session::get('pos_cart', []);

        $total = 0;
        foreach ($panier as $item) {
            $total += $item['prix_unitaire'] * $item['quantite'];
        }

        return response()->json([
            'cart' => $panier,
            'total' => $total,
            'total_items' => count($panier)
        ]);
    }

    /**
     * Rechercher des produits
     */
    public function searchProducts(Request $request)
    {
        try {
            $query = $request->get('q', '');

            $session = $this->getActiveSession();
            if (!$session) {
                return response()->json([]);
            }

            $produits = Produit::where('statut', 'actif')
                              ->where(function($q) use ($query) {
                                  $q->where('nom', 'LIKE', "%{$query}%")
                                    ->orWhere('code_barre', 'LIKE', "%{$query}%")
                                    ->orWhere('categorie', 'LIKE', "%{$query}%");
                              })
                              ->with(['stockBoutiques' => function($query) use ($session) {
                                  $query->where('boutique_id', $session->boutique_id);
                              }])
                              ->limit(20)
                              ->get()
                              ->map(function($produit) {
                                  $stockBoutique = $produit->stockBoutiques->first();
                                  $produit->stock_disponible = $stockBoutique ? $stockBoutique->quantite : 0;
                                  return $produit;
                              });

            return response()->json($produits);
        } catch (\Exception $e) {
            \Log::error('Erreur recherche produits : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la recherche'], 500);
        }
    }

    /**
     * Obtenir la session de caisse active
     */
    private function getActiveSession()
    {
        return CashRegisterSession::where('vendeur_id', Auth::id())
                                 ->whereIn('status', ['ouverte', 'en_cours'])
                                 ->first();
    }
}
