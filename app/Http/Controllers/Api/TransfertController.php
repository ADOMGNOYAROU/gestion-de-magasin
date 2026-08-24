<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Magasin;
use App\Models\Produit;
use App\Models\StockBoutique;
use App\Models\StockMagasin;
use App\Models\Transfert;
use App\Services\StockAlertNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransfertController extends Controller
{
    public function index()
    {
        return response()->json(
            Transfert::with(['produit', 'magasin', 'boutique'])
                ->latest('date')
                ->latest('id')
                ->limit(50)
                ->get()
                ->map(fn (Transfert $t) => [
                    'id' => $t->id,
                    'produit' => $t->produit?->nom,
                    'magasin' => $t->magasin?->nom,
                    'boutique' => $t->boutique?->nom,
                    'quantite' => $t->quantite,
                    'date' => $t->date?->toDateString(),
                ])
        );
    }

    public function stockDisponible(Request $request)
    {
        $request->validate([
            'produit_id' => ['required', 'exists:produits,id'],
            'magasin_id' => ['required', 'exists:magasins,id'],
        ]);

        $stock = StockMagasin::where('magasin_id', $request->query('magasin_id'))
            ->where('produit_id', $request->query('produit_id'))
            ->first();

        return response()->json(['quantite' => $stock?->quantite ?? 0]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'produit_id' => ['required', 'exists:produits,id'],
            'magasin_id' => ['required', 'exists:magasins,id'],
            'boutique_id' => ['required', 'exists:boutiques,id'],
            'quantite' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $transfert = DB::transaction(function () use ($data) {
            $stockMagasin = StockMagasin::where('magasin_id', $data['magasin_id'])
                ->where('produit_id', $data['produit_id'])
                ->lockForUpdate()
                ->first();

            if (! $stockMagasin || $stockMagasin->quantite < $data['quantite']) {
                throw ValidationException::withMessages([
                    'quantite' => ['Stock insuffisant dans ce magasin pour ce produit.'],
                ]);
            }

            $quantiteAvant = $stockMagasin->quantite;
            $stockMagasin->decrement('quantite', $data['quantite']);

            StockAlertNotifier::notifyIfNewlyCritical(
                Produit::findOrFail($data['produit_id']),
                Magasin::findOrFail($data['magasin_id'])->nom,
                $quantiteAvant,
                $quantiteAvant - $data['quantite'],
                $stockMagasin->seuil_alerte,
                $data['magasin_id'],
            );

            $stockBoutique = StockBoutique::firstOrNew([
                'boutique_id' => $data['boutique_id'],
                'produit_id' => $data['produit_id'],
            ]);
            $stockBoutique->quantite = ($stockBoutique->exists ? $stockBoutique->quantite : 0) + $data['quantite'];
            $stockBoutique->prix_vente = $stockBoutique->exists ? $stockBoutique->prix_vente : $stockMagasin->prix_vente;
            $stockBoutique->seuil_alerte = $stockBoutique->exists ? $stockBoutique->seuil_alerte : 5;
            $stockBoutique->save();

            return Transfert::create([
                'produit_id' => $data['produit_id'],
                'magasin_id' => $data['magasin_id'],
                'boutique_id' => $data['boutique_id'],
                'quantite' => $data['quantite'],
                'notes' => $data['notes'] ?? null,
                'date' => now()->toDateString(),
            ]);
        });

        return response()->json(['id' => $transfert->id], 201);
    }
}
