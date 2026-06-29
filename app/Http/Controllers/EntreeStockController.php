<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntreeStock;
use App\Models\Produit;
use App\Models\Fournisseur;
use App\Models\Partenaire;
use App\Models\Magasin;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\StockService;
use App\Http\Requests\EntreeStockRequest;

class EntreeStockController extends Controller
{
    public function __construct(private StockService $stockService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $date_debut = $request->get('date_debut');
        $date_fin = $request->get('date_fin');
        
        $query = EntreeStock::with(['produit', 'fournisseur', 'partenaire', 'magasin'])
            ->orderBy('date_entree', 'desc');
            
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
            $query->whereDate('date_entree', '>=', $date_debut);
        }
        
        if ($date_fin) {
            $query->whereDate('date_entree', '<=', $date_fin);
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
        $orders = Order::with('orderItems')->where('status', 'en_cours')->get();
        
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
        
        return view('entrees-stock.create', compact('produits', 'fournisseurs', 'partenaires', 'magasin', 'magasins', 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EntreeStockRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // 1. Créer l'entrée de stock
            $montantTotal = $validated['quantite'] * $validated['prix_unitaire'];
            $entree = EntreeStock::create([
                'produit_id' => $validated['produit_id'],
                'magasin_id' => $validated['magasin_id'],
                'fournisseur_id' => $validated['fournisseur_id'],
                'partenaire_id' => $validated['partenaire_id'],
                'user_id' => Auth::id(),
                'quantite' => $validated['quantite'],
                'prix_unitaire' => $validated['prix_unitaire'],
                'prix_achat' => $validated['prix_unitaire'],
                'montant_total' => $montantTotal,
                'date_entree' => $validated['date'],
            ]);

            // 2. Mettre à jour ou créer le stock dans le magasin
            $this->stockService->incrementerOuCreerStockMagasin(
                $validated['produit_id'],
                $validated['magasin_id'],
                $validated['quantite']
            );

            // 3. Lier à la commande si spécifiée et vérifier si la commande est entièrement livrée
            if ($validated['order_id']) {
                $orderItem = OrderItem::where('order_id', $validated['order_id'])
                                      ->where('produit_id', $validated['produit_id'])
                                      ->first();
                if ($orderItem) {
                    $entree->order_item_id = $orderItem->id;
                    $entree->save();

                    // Vérifier si la commande est entièrement reçue
                    $order = Order::find($validated['order_id']);
                    $fullyReceived = true;
                    foreach ($order->orderItems as $item) {
                        $received = EntreeStock::where('order_item_id', $item->id)->sum('quantite');
                        if ($received < $item->quantite) {
                            $fullyReceived = false;
                            break;
                        }
                    }
                    if ($fullyReceived) {
                        $order->status = 'livree';
                        $order->save();
                    }
                }
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

            // 1. Mettre à jour le stock (soustraire la quantité, vérifie la disponibilité)
            $this->stockService->decrementerStockMagasin(
                $entree->produit_id,
                $entree->magasin_id,
                $entree->quantite
            );

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
