<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transfert;
use App\Models\Produit;
use App\Models\StockMagasin;
use App\Models\StockBoutique;
use App\Models\Magasin;
use App\Models\Boutique;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransfertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $date_debut = $request->get('date_debut');
        $date_fin = $request->get('date_fin');
        
        $query = Transfert::with(['produit', 'magasin', 'boutique'])
            ->orderBy('date', 'desc');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('produit', function($subq) use ($search) {
                    $subq->where('nom', 'like', '%'.$search.'%');
                })->orWhereHas('magasin', function($subq) use ($search) {
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
        
        $transferts = $query->paginate(15);
        
        return view('transferts.index', compact('transferts', 'search', 'date_debut', 'date_fin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produits = Produit::where('statut', 'actif')->orderBy('nom')->get();
        
        // Récupérer les magasins et boutiques selon le rôle
        if (Auth::user()->isAdmin()) {
            $magasins = Magasin::with('boutiques')->orderBy('nom')->get();
        } elseif (Auth::user()->isGestionnaire()) {
            $magasin = Auth::user()->magasinResponsable;
            if (!$magasin) {
                return redirect()->route('dashboard')
                    ->with('error', 'Aucun magasin ne vous est assigné. Contactez un administrateur.');
            }
            $magasins = collect([$magasin]);
            $magasins->first()->load('boutiques');
        } else {
            // Vendeur - pas accès aux transferts
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions pour effectuer des transferts.');
        }
        
        return view('transferts.create', compact('produits', 'magasins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'magasin_id' => 'required|exists:magasins,id',
            'boutique_id' => 'required|exists:boutiques,id',
            'date' => 'required|date',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:0',
        ]);

        // Filtrer les produits avec quantité > 0
        $transferData = array_filter($validated['quantite'], function($q) {
            return $q > 0;
        });

        if (empty($transferData)) {
            return back()->withErrors(['error' => 'Aucun produit sélectionné pour le transfert.']);
        }

        DB::beginTransaction();
        try {
            // 1. Vérifier que la boutique appartient bien au magasin
            $boutique = Boutique::findOrFail($validated['boutique_id']);
            if ($boutique->magasin_id != $validated['magasin_id']) {
                throw new \Exception('La boutique sélectionnée n\'appartient pas au magasin spécifié.');
            }

            $totalTransfers = 0;

            // 2. Pour chaque produit sélectionné, effectuer le transfert
            foreach ($transferData as $produitId => $quantite) {
                // Vérifier le stock disponible dans le magasin
                $stockMagasin = StockMagasin::where('produit_id', $produitId)
                                           ->where('magasin_id', $validated['magasin_id'])
                                           ->first();

                if (!$stockMagasin || $stockMagasin->quantite < $quantite) {
                    $quantiteDisponible = $stockMagasin ? $stockMagasin->quantite : 0;
                    throw new \Exception("Stock insuffisant pour le produit ID {$produitId}. Quantité disponible : {$quantiteDisponible}, Quantité demandée : {$quantite}");
                }

                // Créer le transfert
                Transfert::create([
                    'produit_id' => $produitId,
                    'magasin_id' => $validated['magasin_id'],
                    'boutique_id' => $validated['boutique_id'],
                    'quantite' => $quantite,
                    'date' => $validated['date'],
                ]);

                // Diminuer le stock du magasin
                $stockMagasin->quantite -= $quantite;
                $stockMagasin->save();

                // Augmenter le stock de la boutique
                $produit = Produit::find($produitId);
                $stockBoutique = StockBoutique::where('produit_id', $produitId)
                                            ->where('boutique_id', $validated['boutique_id'])
                                            ->first();

                if ($stockBoutique) {
                    // Mettre à jour le stock existant
                    $stockBoutique->quantite += $quantite;
                    $stockBoutique->save();
                } else {
                    // Créer un nouveau stock
                    StockBoutique::create([
                        'produit_id' => $produitId,
                        'boutique_id' => $validated['boutique_id'],
                        'quantite' => $quantite,
                        'prix_vente' => $produit->prix_vente,
                        'seuil_alerte' => 5, // Valeur par défaut pour les boutiques
                    ]);
                }

                $totalTransfers++;
            }

            DB::commit();

            return redirect()->route('transferts.index')
                ->with('success', "{$totalTransfers} transfert(s) effectué(s) avec succès. Stocks mis à jour.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Erreur lors des transferts : ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transfert = Transfert::with(['produit', 'magasin', 'boutique'])->findOrFail($id);
        return view('transferts.show', compact('transfert'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Les transferts ne devraient pas être modifiables pour éviter les incohérences
        return redirect()->route('transferts.index')
            ->with('error', 'Les transferts ne peuvent pas être modifiés pour maintenir la cohérence des stocks.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('transferts.index')
            ->with('error', 'Les transferts ne peuvent pas être modifiés.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $transfert = Transfert::findOrFail($id);
            
            // 1. Augmenter le stock du magasin (annuler le transfert)
            $stockMagasin = StockMagasin::where('produit_id', $transfert->produit_id)
                                       ->where('magasin_id', $transfert->magasin_id)
                                       ->first();

            if ($stockMagasin) {
                $stockMagasin->quantite += $transfert->quantite;
                $stockMagasin->save();
            }

            // 2. Diminuer le stock de la boutique
            $stockBoutique = StockBoutique::where('produit_id', $transfert->produit_id)
                                        ->where('boutique_id', $transfert->boutique_id)
                                        ->first();

            if ($stockBoutique) {
                $nouvelleQuantite = $stockBoutique->quantite - $transfert->quantite;
                if ($nouvelleQuantite >= 0) {
                    $stockBoutique->quantite = $nouvelleQuantite;
                    $stockBoutique->save();
                } else {
                    throw new \Exception('Impossible d\'annuler ce transfert : stock boutique insuffisant');
                }
            }

            // 3. Supprimer le transfert
            $transfert->delete();

            DB::commit();

            return redirect()->route('transferts.index')
                ->with('success', 'Transfert annulé avec succès. Stocks restaurés.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Erreur lors de l\'annulation : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API pour récupérer le stock disponible d'un produit dans un magasin
     */
    public function getStockDisponible(Request $request)
    {
        $produitId = $request->get('produit_id');
        $magasinId = $request->get('magasin_id');

        $stock = StockMagasin::where('produit_id', $produitId)
                           ->where('magasin_id', $magasinId)
                           ->first();

        return response()->json([
            'quantite' => $stock ? $stock->quantite : 0,
            'seuil_alerte' => $stock ? $stock->seuil_alerte : 0,
            'en_alerte' => $stock ? $stock->en_alerte : false,
        ]);
    }

    /**
     * API pour récupérer les boutiques d'un magasin
     */
    public function getBoutiquesByMagasin(Request $request)
    {
        $magasinId = $request->get('magasin_id');
        
        $boutiques = Boutique::where('magasin_id', $magasinId)
                            ->orderBy('nom')
                            ->get();

        return response()->json($boutiques);
    }

    /**
     * API pour récupérer les produits avec leur stock dans un magasin
     */
    public function getProduitsAvecStock(Request $request)
    {
        $magasinId = $request->get('magasin_id');

        $produits = Produit::leftJoin('stock_magasins', function($join) use ($magasinId) {
                                $join->on('produits.id', '=', 'stock_magasins.produit_id')
                                     ->where('stock_magasins.magasin_id', $magasinId);
                            })
                            ->where('produits.statut', 'actif')
                            ->select('produits.id', 'produits.nom', 'produits.categorie', 
                                     'stock_magasins.quantite as stock', 'stock_magasins.seuil_alerte')
                            ->orderBy('produits.nom')
                            ->get();

        return response()->json($produits);
    }
}
