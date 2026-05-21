<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage-produits');

        $search = $request->get('search');
        $status = $request->get('status');

        $orders = Order::with(['fournisseur', 'user'])
            ->when($search, function($query, $search) {
                return $query->where('numero_commande', 'like', '%'.$search.'%')
                           ->orWhereHas('fournisseur', function($q) use ($search) {
                               $q->where('nom', 'like', '%'.$search.'%');
                           });
            })
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('orders.index', compact('orders', 'search', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('manage-produits');

        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $produits = Produit::orderBy('nom')->get();

        return view('orders.create', compact('fournisseurs', 'produits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage-produits');

        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_livraison_prevue' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'produits' => 'required|array',
            'produits.*.id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $order = Order::create([
                'fournisseur_id' => $validated['fournisseur_id'],
                'user_id' => auth()->id(),
                'numero_commande' => Order::generateNumeroCommande(),
                'date_commande' => now(),
                'date_livraison_prevue' => $validated['date_livraison_prevue'] ?? null,
                'status' => 'en_cours',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['produits'] as $produitData) {
                $produit = Produit::find($produitData['id']);
                $order->ajouterProduit($produit, $produitData['quantite'], $produitData['prix_unitaire']);
            }
        });

        return redirect()->route('orders.index')
            ->with('success', 'Commande créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        Gate::authorize('manage-produits');

        $order = Order::with(['fournisseur', 'user', 'orderItems.produit'])
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        Gate::authorize('manage-produits');

        $order = Order::with('orderItems.produit')->findOrFail($id);

        if ($order->status !== 'en_cours') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Seules les commandes en cours peuvent être modifiées.');
        }

        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $produits = Produit::orderBy('nom')->get();

        return view('orders.edit', compact('order', 'fournisseurs', 'produits'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Gate::authorize('manage-produits');

        $order = Order::findOrFail($id);

        if ($order->status !== 'en_cours') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Seules les commandes en cours peuvent être modifiées.');
        }

        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_livraison_prevue' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'produits' => 'required|array',
            'produits.*.id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $order) {
            $order->update([
                'fournisseur_id' => $validated['fournisseur_id'],
                'date_livraison_prevue' => $validated['date_livraison_prevue'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete existing items and recreate
            $order->orderItems()->delete();

            foreach ($validated['produits'] as $produitData) {
                $produit = Produit::find($produitData['id']);
                $order->ajouterProduit($produit, $produitData['quantite'], $produitData['prix_unitaire']);
            }
        });

        return redirect()->route('orders.show', $order)
            ->with('success', 'Commande mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('manage-produits');

        $order = Order::findOrFail($id);

        if ($order->status !== 'en_cours') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Seules les commandes en cours peuvent être annulées.');
        }

        $order->status = 'annulee';
        $order->save();

        return redirect()->route('orders.index')
            ->with('success', 'Commande annulée avec succès.');
    }

    /**
     * Mark order as delivered.
     */
    public function livrer(string $id)
    {
        Gate::authorize('manage-produits');

        $order = Order::findOrFail($id);

        if ($order->status !== 'en_cours') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'La commande n\'est pas en cours.');
        }

        DB::transaction(function () use ($order) {
            // Update stock
            foreach ($order->orderItems as $item) {
                $stock = \App\Models\StockMagasin::where('produit_id', $item->produit_id)->first();
                if ($stock) {
                    $stock->quantite += $item->quantite;
                    $stock->save();
                }
            }

            $order->status = 'livree';
            $order->save();
        });

        return redirect()->route('orders.show', $order)
            ->with('success', 'Commande marquée comme livrée et stock mis à jour.');
    }

    /**
     * Show price comparison between suppliers.
     */
    public function priceComparison()
    {
        Gate::authorize('manage-produits');

        $produits = Produit::with('fournisseurs')->get();

        // Get all suppliers with their prices for products
        $suppliers = Fournisseur::all();

        return view('orders.price_comparison', compact('produits', 'suppliers'));
    }

    /**
     * Show auto-replenishment suggestions.
     */
    public function autoReplenishment()
    {
        Gate::authorize('manage-produits');

        // Get products with low stock (less than 10 for example)
        $lowStockProducts = Produit::whereRaw('stock_boutiques_sum < 10')->get();

        // For each product, find the best supplier (lowest price)
        $suggestions = [];
        foreach ($lowStockProducts as $produit) {
            $bestSupplier = null;
            $bestPrice = null;

            // Find suppliers who have supplied this product
            $suppliers = \DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('fournisseurs', 'orders.fournisseur_id', '=', 'fournisseurs.id')
                ->where('order_items.produit_id', $produit->id)
                ->select('fournisseurs.id', 'fournisseurs.nom', 'order_items.prix_unitaire')
                ->orderBy('orders.date_commande', 'desc')
                ->get()
                ->unique('id');

            foreach ($suppliers as $supplier) {
                if (!$bestPrice || $supplier->prix_unitaire < $bestPrice) {
                    $bestPrice = $supplier->prix_unitaire;
                    $bestSupplier = $supplier;
                }
            }

            if ($bestSupplier) {
                $suggestions[] = [
                    'produit' => $produit,
                    'supplier' => $bestSupplier,
                    'suggested_quantity' => max(10 - $produit->stock_total_boutique, 0),
                    'price' => $bestPrice,
                ];
            }
        }

        return view('orders.auto_replenishment', compact('suggestions'));
    }

    /**
     * Generate orders from suggestions.
     */
    public function generateReplenishmentOrders(Request $request)
    {
        Gate::authorize('manage-produits');

        $selectedSuggestions = $request->input('suggestions', []);

        $ordersCreated = 0;

        foreach ($selectedSuggestions as $suggestion) {
            // Group by supplier
            $supplierId = $suggestion['supplier_id'];
            $produitId = $suggestion['produit_id'];
            $quantity = $suggestion['quantity'];

            // For simplicity, create separate orders or group them
            // Here, create one order per supplier with selected products

            // This is simplified; in reality, might want to group by supplier
            $order = Order::create([
                'fournisseur_id' => $supplierId,
                'user_id' => auth()->id(),
                'numero_commande' => Order::generateNumeroCommande(),
                'date_commande' => now(),
                'status' => 'en_cours',
            ]);

            $produit = Produit::find($produitId);
            $price = $suggestion['price'];
            $order->ajouterProduit($produit, $quantity, $price);

            $ordersCreated++;
        }

        return redirect()->route('orders.index')
            ->with('success', "$ordersCreated commande(s) de réapprovisionnement créée(s) avec succès.");
    }
}
