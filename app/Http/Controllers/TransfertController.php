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
            'produit_id' => 'required|exists:produits,id',
            'magasin_id' => 'required|exists:magasins,id',
            'boutique_id' => 'required|exists:boutiques,id',
            'quantite' => 'required|integer|min:1',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // 1. Vérifier que la boutique appartient bien au magasin
            $boutique = Boutique::findOrFail($validated['boutique_id']);
            if ($boutique->magasin_id != $validated['magasin_id']) {
                throw new \Exception('La boutique sélectionnée n\'appartient pas au magasin spécifié.');
            }

            // 2. Vérifier le stock disponible dans le magasin
            $stockMagasin = StockMagasin::where('produit_id', $validated['produit_id'])
                                       ->where('magasin_id', $validated['magasin_id'])
                                       ->first();

            if (!$stockMagasin || $stockMagasin->quantite < $validated['quantite']) {
                $quantiteDisponible = $stockMagasin ? $stockMagasin->quantite : 0;
                throw new \Exception("Stock insuffisant. Quantité disponible : {$quantiteDisponible}, Quantité demandée : {$validated['quantite']}");
            }

            // 3. Créer le transfert
            $transfert = Transfert::create([
                'produit_id' => $validated['produit_id'],
                'magasin_id' => $validated['magasin_id'],
                'boutique_id' => $validated['boutique_id'],
                'quantite' => $validated['quantite'],
                'date' => $validated['date'],
            ]);

            // 4. Diminuer le stock du magasin
            $stockMagasin->quantite -= $validated['quantite'];
            $stockMagasin->save();

            // 5. Augmenter le stock de la boutique
            $stockBoutique = StockBoutique::where('produit_id', $validated['produit_id'])
                                        ->where('boutique_id', $validated['boutique_id'])
                                        ->first();

            // Récupérer le produit pour obtenir le prix de vente
            $produit = Produit::find($validated['produit_id']);

            if ($stockBoutique) {
                // Mettre à jour le stock existant
                $stockBoutique->quantite += $validated['quantite'];
                $stockBoutique->save();
            } else {
                // Créer un nouveau stock
                StockBoutique::create([
                    'produit_id' => $validated['produit_id'],
                    'boutique_id' => $validated['boutique_id'],
                    'quantite' => $validated['quantite'],
                    'prix_vente' => $produit->prix_vente,
                    'seuil_alerte' => 5, // Valeur par défaut pour les boutiques
                ]);
            }

            DB::commit();

            return redirect()->route('transferts.index')
                ->with('success', "Transfert de {$validated['quantite']} unités effectué avec succès. Stock mis à jour.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Erreur lors du transfert : ' . $e->getMessage()
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
}
