<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EntreeStock;
use App\Models\Produit;
use App\Models\StockMagasin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntreeStockController extends Controller
{
    public function index()
    {
        return response()->json(
            EntreeStock::with(['produit', 'magasin', 'fournisseur', 'partenaire'])
                ->latest('date_entree')
                ->latest('id')
                ->limit(50)
                ->get()
                ->map(fn (EntreeStock $e) => [
                    'id' => $e->id,
                    'produit' => $e->produit?->nom,
                    'magasin' => $e->magasin?->nom,
                    'fournisseur' => $e->fournisseur?->nom,
                    'partenaire' => $e->partenaire?->nom,
                    'quantite' => $e->quantite,
                    'prix_unitaire' => (float) $e->prix_unitaire,
                    'montant_total' => (float) $e->montant_total,
                    'date_entree' => $e->date_entree?->toDateString(),
                    'numero_bon' => $e->numero_bon,
                ])
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'produit_id' => ['required', 'exists:produits,id'],
            'magasin_id' => ['required', 'exists:magasins,id'],
            'fournisseur_id' => ['nullable', 'exists:fournisseurs,id'],
            'partenaire_id' => ['nullable', 'exists:partenaires,id'],
            'quantite' => ['required', 'integer', 'min:1'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
            'date_entree' => ['nullable', 'date'],
            'numero_bon' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $entree = DB::transaction(function () use ($data, $request) {
            $entree = EntreeStock::create([
                ...$data,
                'user_id' => $request->user()->id,
                'montant_total' => $data['quantite'] * $data['prix_unitaire'],
                'date_entree' => $data['date_entree'] ?? now()->toDateString(),
            ]);

            $produit = Produit::findOrFail($data['produit_id']);

            $stock = StockMagasin::firstOrNew([
                'magasin_id' => $data['magasin_id'],
                'produit_id' => $data['produit_id'],
            ]);
            $stock->quantite = ($stock->exists ? $stock->quantite : 0) + $data['quantite'];
            $stock->prix_vente = $stock->exists ? $stock->prix_vente : $produit->prix_vente;
            $stock->seuil_alerte = $stock->exists ? $stock->seuil_alerte : 10;
            $stock->save();

            return $entree;
        });

        return response()->json(['id' => $entree->id], 201);
    }
}
