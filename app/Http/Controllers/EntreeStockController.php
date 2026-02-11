<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntreeStock;
use App\Models\Produit;
use App\Models\StockMagasin;
use App\Models\Fournisseur;
use App\Models\Partenaire;
use App\Models\Magasin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EntreeStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $date_debut = $request->get('date_debut');
        $date_fin = $request->get('date_fin');
        
        $query = EntreeStock::with(['produit', 'fournisseur', 'partenaire', 'magasin'])
            ->orderBy('date', 'desc');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('produit', function($subq) use ($search) {
                    $subq->where('nom', 'like', '%'.$search.'%');
                })->orWhereHas('fournisseur', function($subq) use ($search) {
                    $subq->where('nom', 'like', '%'.$search.'%');
                })->orWhereHas('partenaire', function($subq) use ($search) {
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
        
        $entrees = $query->paginate(15);
        
        return view('entrees-stock.index', compact('entrees', 'search', 'date_debut', 'date_fin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produits = Produit::where('statut', 'actif')->orderBy('nom')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $partenaires = Partenaire::orderBy('nom')->get();
        
        // Récupérer le magasin de l'utilisateur connecté
        $magasin = null;
        $magasins = collect();
        if (Auth::user()->isGestionnaire()) {
            $magasin = Auth::user()->magasinResponsable;
            if ($magasin) {
                $magasins = collect([$magasin]);
            }
        } elseif (Auth::user()->isAdmin()) {
            $magasins = Magasin::orderBy('nom')->get();
        }
        
        return view('entrees-stock.create', compact('produits', 'fournisseurs', 'partenaires', 'magasin', 'magasins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'magasin_id' => 'required|exists:magasins,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'partenaire_id' => 'nullable|exists:partenaires,id',
            'quantite' => 'required|integer|min:1',
            'prix_unitaire' => 'required|numeric|min:0',
            'date' => 'required|date',
        ], [
            'fournisseur_id.required_without' => 'Vous devez sélectionner un fournisseur ou un partenaire',
            'partenaire_id.required_without' => 'Vous devez sélectionner un fournisseur ou un partenaire',
        ]);

        // Validation personnalisée : soit fournisseur soit partenaire
        if (!$request->fournisseur_id && !$request->partenaire_id) {
            return back()->withErrors([
                'fournisseur_id' => 'Vous devez sélectionner au moins un fournisseur ou un partenaire'
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            // 1. Créer l'entrée de stock
            $entree = EntreeStock::create([
                'produit_id' => $validated['produit_id'],
                'magasin_id' => $validated['magasin_id'],
                'fournisseur_id' => $validated['fournisseur_id'],
                'partenaire_id' => $validated['partenaire_id'],
                'quantite' => $validated['quantite'],
                'prix_unitaire' => $validated['prix_unitaire'],
                'date' => $validated['date'],
            ]);

            // 2. Mettre à jour ou créer le stock dans le magasin
            $stockMagasin = StockMagasin::where('produit_id', $validated['produit_id'])
                                       ->where('magasin_id', $validated['magasin_id'])
                                       ->first();

            if ($stockMagasin) {
                // Mettre à jour le stock existant
                $stockMagasin->quantite += $validated['quantite'];
                $stockMagasin->save();
            } else {
                // Créer un nouveau stock
                StockMagasin::create([
                    'produit_id' => $validated['produit_id'],
                    'magasin_id' => $validated['magasin_id'],
                    'quantite' => $validated['quantite'],
                    'seuil_alerte' => 10, // Valeur par défaut
                ]);
            }

            DB::commit();

            return redirect()->route('entrees-stock.index')
                ->with('success', 'Entrée de stock enregistrée avec succès. Stock mis à jour.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $entree = EntreeStock::with(['produit', 'fournisseur', 'partenaire', 'magasin'])->findOrFail($id);
        return view('entrees-stock.show', compact('entree'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Les entrées de stock ne devraient pas être modifiables pour éviter les incohérences
        return redirect()->route('entrees-stock.index')
            ->with('error', 'Les entrées de stock ne peuvent pas être modifiées pour maintenir la cohérence des stocks.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('entrees-stock.index')
            ->with('error', 'Les entrées de stock ne peuvent pas être modifiées.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $entree = EntreeStock::findOrFail($id);
            
            // 1. Mettre à jour le stock (soustraire la quantité)
            $stockMagasin = StockMagasin::where('produit_id', $entree->produit_id)
                                       ->where('magasin_id', $entree->magasin_id)
                                       ->first();

            if ($stockMagasin) {
                $nouvelleQuantite = $stockMagasin->quantite - $entree->quantite;
                if ($nouvelleQuantite >= 0) {
                    $stockMagasin->quantite = $nouvelleQuantite;
                    $stockMagasin->save();
                } else {
                    throw new \Exception('Impossible de supprimer cette entrée : stock insuffisant');
                }
            }

            // 2. Supprimer l'entrée
            $entree->delete();

            DB::commit();

            return redirect()->route('entrees-stock.index')
                ->with('success', 'Entrée de stock supprimée avec succès. Stock mis à jour.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la suppression : ' . $e->getMessage()
            ]);
        }
    }
}
