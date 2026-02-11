<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\Produit;
use App\Models\StockBoutique;
use App\Models\Boutique;
use App\Models\Magasin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

class VenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Vérifier la permission de gérer les ventes
        Gate::authorize('manage-ventes');
        
        $search = $request->get('search');
        $date_debut = $request->get('date_debut');
        $date_fin = $request->get('date_fin');
        
        $query = Vente::with(['produit', 'boutique.magasin'])
            ->orderBy('date', 'desc');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('produit', function($subq) use ($search) {
                    $subq->where('nom', 'like', '%'.$search.'%');
                })->orWhereHas('boutique', function($subq) use ($search) {
                    $subq->where('nom', 'like', '%'.$search.'%');
                });
            });
        }
        
        if ($date_debut) {
            $query->whereDate('date', '>=', $date_debut);
        }
        
        if ($date_fin) {
            $query->whereDate('date', '<=', $date_fin);
        }
        
        // Filtrer selon le rôle
        $user = Auth::user();
        
        if ($user->isVendeur()) {
            $query->where('boutique_id', $user->boutique_id);
        } elseif ($user->isGestionnaire()) {
            $magasinResponsable = $user->magasinResponsable;
            if ($magasinResponsable) {
                $query->whereHas('boutique', function($q) use ($magasinResponsable) {
                    $q->where('magasin_id', $magasinResponsable->id);
                });
            } else {
                // Si le gestionnaire n'a pas de magasin associé, on ne retourne aucune vente
                $query->where('id', 0);
            }
        }
        
        $ventes = $query->paginate(15);
        
        return view('ventes.index', compact('ventes', 'search', 'date_debut', 'date_fin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Vider le panier existant pour commencer une nouvelle vente
        Session::forget('panier');
        
        $produits = Produit::where('statut', 'actif')->orderBy('nom')->get();
        
        // Récupérer les boutiques selon le rôle
        if (Auth::user()->isVendeur()) {
            $boutique = Auth::user()->boutique;
            if (!$boutique) {
                return redirect()->route('dashboard')
                    ->with('error', 'Aucune boutique ne vous est assignée. Contactez un administrateur.');
            }
            $boutiques = collect([$boutique]);
        } elseif (Auth::user()->isGestionnaire()) {
            $magasin = Auth::user()->magasinResponsable;
            if (!$magasin) {
                return redirect()->route('dashboard')
                    ->with('error', 'Aucun magasin ne vous est assigné. Contactez un administrateur.');
            }
            $boutiques = Boutique::where('magasin_id', $magasin->id)->orderBy('nom')->get();
        } elseif (Auth::user()->isAdmin()) {
            $boutiques = Boutique::with('magasin')->orderBy('nom')->get();
        } else {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions pour effectuer des ventes.');
        }
        
        return view('ventes.create', compact('produits', 'boutiques'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $panier = Session::get('panier', []);
        
        if (empty($panier)) {
            return back()->withErrors(['error' => 'Le panier est vide. Ajoutez des produits avant de valider la vente.']);
        }

        $validated = $request->validate([
            'boutique_id' => 'required|exists:boutiques,id',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $totalVente = 0;
            $totalBenefice = 0;
            $ventesCrees = [];

            foreach ($panier as $item) {
                $produit = Produit::findOrFail($item['produit_id']);
                $quantite = $item['quantite'];
                
                // Vérifier le stock disponible dans la boutique
                $stockBoutique = StockBoutique::where('produit_id', $item['produit_id'])
                                            ->where('boutique_id', $validated['boutique_id'])
                                            ->first();

                if (!$stockBoutique || $stockBoutique->quantite < $quantite) {
                    $quantiteDisponible = $stockBoutique ? $stockBoutique->quantite : 0;
                    throw new \Exception("Stock insuffisant pour {$produit->nom}. Disponible: {$quantiteDisponible}, Demandé: {$quantite}");
                }

                // Calculer les totaux
                $prixUnitaire = $produit->prix_vente;
                $prixAchat = $produit->prix_achat;
                $total = $quantite * $prixUnitaire;
                $benefice = $quantite * ($prixUnitaire - $prixAchat);

                // Créer la vente
                $vente = Vente::create([
                    'produit_id' => $item['produit_id'],
                    'boutique_id' => $validated['boutique_id'],
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'prix_total' => $total,
                    'benefice' => $benefice,
                    'date' => $validated['date'],
                ]);

                $ventesCrees[] = $vente;
                $totalVente += $total;
                $totalBenefice += $benefice;

                // Mettre à jour le stock de la boutique
                $stockBoutique->quantite -= $quantite;
                $stockBoutique->save();
            }

            DB::commit();

            // Vider le panier
            Session::forget('panier');

            return redirect()->route('ventes.index')
                ->with('success', "Vente enregistrée avec succès ! Total : " . number_format($totalVente, 0, ',', ' ') . " FCFA | Bénéfice : " . number_format($totalBenefice, 0, ',', ' ') . " FCFA");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Erreur lors de la vente : ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vente = Vente::with(['produit', 'boutique.magasin'])->findOrFail($id);
        
        // Vérifier les permissions
        if (Auth::user()->isVendeur() && $vente->boutique_id != Auth::user()->boutique_id) {
            return redirect()->route('ventes.index')
                ->with('error', 'Vous n\'avez pas les permissions pour voir cette vente.');
        }
        
        return view('ventes.show', compact('vente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Les ventes ne devraient pas être modifiables pour éviter les incohérences
        return redirect()->route('ventes.index')
            ->with('error', 'Les ventes ne peuvent pas être modifiées pour maintenir la cohérence des stocks.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('ventes.index')
            ->with('error', 'Les ventes ne peuvent pas être modifiées.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $vente = Vente::findOrFail($id);
            
            // Vérifier les permissions
            if (Auth::user()->isVendeur() && $vente->boutique_id != Auth::user()->boutique_id) {
                throw new \Exception('Vous n\'avez pas les permissions pour annuler cette vente.');
            }
            
            // Restaurer le stock de la boutique
            $stockBoutique = StockBoutique::where('produit_id', $vente->produit_id)
                                        ->where('boutique_id', $vente->boutique_id)
                                        ->first();

            if ($stockBoutique) {
                $stockBoutique->quantite += $vente->quantite;
                $stockBoutique->save();
            }

            // Supprimer la vente
            $vente->delete();

            DB::commit();

            return redirect()->route('ventes.index')
                ->with('success', 'Vente annulée avec succès. Stock restauré.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Erreur lors de l\'annulation : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Ajouter un produit au panier
     */
    public function ajouterPanier(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'boutique_id' => 'required|exists:boutiques,id',
        ]);

        $produit = Produit::findOrFail($validated['produit_id']);
        
        // Vérifier le stock disponible
        $stockBoutique = StockBoutique::where('produit_id', $validated['produit_id'])
                                    ->where('boutique_id', $validated['boutique_id'])
                                    ->first();

        $quantiteDisponible = $stockBoutique ? $stockBoutique->quantite : 0;
        
        if ($quantiteDisponible < $validated['quantite']) {
            return response()->json([
                'success' => false,
                'message' => "Stock insuffisant. Disponible: {$quantiteDisponible}, Demandé: {$validated['quantite']}"
            ], 400);
        }

        $panier = Session::get('panier', []);
        
        // Vérifier si le produit est déjà dans le panier
        $produitExiste = false;
        foreach ($panier as &$item) {
            if ($item['produit_id'] == $validated['produit_id']) {
                $nouvelleQuantite = $item['quantite'] + $validated['quantite'];
                if ($nouvelleQuantite > $quantiteDisponible) {
                    return response()->json([
                        'success' => false,
                        'message' => "Quantité totale dépassant le stock disponible. Disponible: {$quantiteDisponible}"
                    ], 400);
                }
                $item['quantite'] = $nouvelleQuantite;
                $produitExiste = true;
                break;
            }
        }

        if (!$produitExiste) {
            $panier[] = [
                'produit_id' => $validated['produit_id'],
                'quantite' => $validated['quantite'],
                'nom' => $produit->nom,
                'categorie' => $produit->categorie,
                'prix_vente' => $produit->prix_vente,
                'prix_achat' => $produit->prix_achat,
            ];
        }

        Session::put('panier', $panier);

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté au panier',
            'panier' => $panier,
            'total_items' => count($panier)
        ]);
    }

    /**
     * Retirer un produit du panier
     */
    public function retirerPanier(Request $request)
    {
        $produitId = $request->get('produit_id');
        
        $panier = Session::get('panier', []);
        
        $panier = array_filter($panier, function($item) use ($produitId) {
            return $item['produit_id'] != $produitId;
        });

        Session::put('panier', array_values($panier));

        return response()->json([
            'success' => true,
            'message' => 'Produit retiré du panier',
            'panier' => $panier,
            'total_items' => count($panier)
        ]);
    }

    /**
     * Vider le panier
     */
    public function viderPanier()
    {
        Session::forget('panier');
        
        return response()->json([
            'success' => true,
            'message' => 'Panier vidé',
            'panier' => [],
            'total_items' => 0
        ]);
    }

    /**
     * API pour récupérer le stock disponible d'un produit dans une boutique
     */
    public function getStockDisponible(Request $request)
    {
        $produitId = $request->get('produit_id');
        $boutiqueId = $request->get('boutique_id');

        $stock = StockBoutique::where('produit_id', $produitId)
                             ->where('boutique_id', $boutiqueId)
                             ->first();

        return response()->json([
            'quantite' => $stock ? $stock->quantite : 0,
            'seuil_alerte' => $stock ? $stock->seuil_alerte : 0,
            'en_alerte' => $stock ? $stock->en_alerte : false,
        ]);
    }
}
