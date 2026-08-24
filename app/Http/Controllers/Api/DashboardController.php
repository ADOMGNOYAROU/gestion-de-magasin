<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\StockBoutique;
use App\Models\StockMagasin;
use App\Models\Vente;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $produitsActifs = Produit::where('statut', 'actif')->count();

        $stockCritique = StockMagasin::whereColumn('quantite', '<=', 'seuil_alerte')->count()
            + StockBoutique::whereColumn('quantite', '<=', 'seuil_alerte')->count();

        $ventesQuery = Vente::query()->whereDate('date_vente', now()->toDateString());

        if ($user->role === 'vendeur' && $user->boutique_id) {
            $ventesQuery->where('boutique_id', $user->boutique_id);
        }

        $ventesJour = (clone $ventesQuery)->count();
        $caJour = (clone $ventesQuery)->sum('montant_total');

        $alertes = StockMagasin::whereColumn('quantite', '<=', 'seuil_alerte')
            ->with('produit', 'magasin')
            ->limit(10)
            ->get()
            ->map(fn ($s) => [
                'produit' => $s->produit?->nom,
                'site' => $s->magasin?->nom,
                'type' => 'magasin',
                'quantite' => $s->quantite,
                'seuil_alerte' => $s->seuil_alerte,
            ])
            ->concat(
                StockBoutique::whereColumn('quantite', '<=', 'seuil_alerte')
                    ->with('produit', 'boutique')
                    ->limit(10)
                    ->get()
                    ->map(fn ($s) => [
                        'produit' => $s->produit?->nom,
                        'site' => $s->boutique?->nom,
                        'type' => 'boutique',
                        'quantite' => $s->quantite,
                        'seuil_alerte' => $s->seuil_alerte,
                    ])
            )
            ->values();

        return response()->json([
            'produits_actifs' => $produitsActifs,
            'stock_critique' => $stockCritique,
            'ventes_jour' => $ventesJour,
            'ca_jour' => (float) $caJour,
            'alertes' => $alertes,
        ]);
    }
}
