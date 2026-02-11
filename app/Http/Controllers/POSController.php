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
        $this->middleware('auth');
    }

    /**
     * Afficher l'interface de caisse
     */
    public function index()
    {
        // Vérifier que l'utilisateur est un vendeur
        if (!Auth::user()->isVendeur()) {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé. Seuls les vendeurs peuvent accéder à la caisse.');
        }

        // Vérifier que le vendeur a une boutique assignée
        $boutique = Auth::user()->boutique;
        if (!$boutique) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucune boutique ne vous est assignée. Contactez un administrateur.');
        }

        // Récupérer la session de caisse active
        $sessionActive = CashRegisterSession::where('vendeur_id', Auth::id())
                                          ->where('boutique_id', $boutique->id)
                                          ->whereIn('status', ['ouverte', 'en_cours'])
                                          ->first();

        if (!$sessionActive) {
            return redirect()->route('pos.open')
                ->with('info', 'Vous devez ouvrir une session de caisse avant de commencer les ventes.');
        }

        // Produits pour recherche rapide
        $produits = Produit::where('statut', 'actif')->orderBy('nom')->get();

        // Méthodes de paiement actives
        $paymentMethods = PaymentMethod::active()->get();

        return view('pos.index', compact('produits', 'paymentMethods', 'sessionActive', 'boutique'));
    }

    /**
     * Ouvrir une session de caisse
     */
    public function open()
    {
        if (!Auth::user()->isVendeur()) {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé.');
        }

        $boutique = Auth::user()->boutique;
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
    }

    /**
     * Stocker l'ouverture de session de caisse
     */
    public function storeOpen(Request $request)
    {
        $request->validate([
            'montant_initial' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $boutique = Auth::user()->boutique;
        if (!$boutique) {
            return redirect()->back()
                ->with('error', 'Aucune boutique ne vous est assignée.');
        }

        // Vérifier s'il y a déjà une session ouverte
        $sessionExistante = CashRegisterSession::where('vendeur_id', Auth::id())
                                              ->where('boutique_id', $boutique->id)
                                              ->whereIn('status', ['ouverte', 'en_cours'])
                                              ->first();

        if ($sessionExistante) {
            return redirect()->route('pos.index')
                ->with('error', 'Une session de caisse est déjà ouverte.');
        }

        try {
            CashRegisterSession::create([
                'vendeur_id' => Auth::id(),
                'boutique_id' => $boutique->id,
                'montant_initial' => $request->montant_initial,
                'date_ouverture' => now(),
                'status' => 'ouverte',
                'notes' => $request->notes,
            ]);

            return redirect()->route('pos.index')
                ->with('success', 'Session de caisse ouverte avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'ouverture de la session : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Fermer une session de caisse
     */
    public function close()
    {
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
    }

    /**
     * Stocker la fermeture de session de caisse
     */
    public function storeClose(Request $request)
    {
        $request->validate([
            'montant_final' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $session = CashRegisterSession::where('vendeur_id', Auth::id())
                                     ->whereIn('status', ['ouverte', 'en_cours'])
                                     ->first();

        if (!$session) {
            return redirect()->route('pos.open')
                ->with('error', 'Aucune session de caisse active trouvée.');
        }

        try {
            $session->fermer($request->montant_final, $request->notes);

            return redirect()->route('dashboard')
                ->with('success', 'Session de caisse fermée avec succès.');

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
        $query = $request->get('q', '');

        $produits = Produit::where('statut', 'actif')
                          ->where(function($q) use ($query) {
                              $q->where('nom', 'LIKE', "%{$query}%")
                                ->orWhere('code_barre', 'LIKE', "%{$query}%")
                                ->orWhere('categorie', 'LIKE', "%{$query}%");
                          })
                          ->limit(20)
                          ->get();

        return response()->json($produits);
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
