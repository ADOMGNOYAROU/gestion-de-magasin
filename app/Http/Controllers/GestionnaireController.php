<?php

namespace App\Http\Controllers;

use App\Models\Magasin;
use App\Models\Boutique;
use App\Models\Produit;
use App\Models\StockMagasin;
use App\Models\Vente;
use App\Models\AlerteStock;
use Illuminate\Http\Request;

class GestionnaireController extends Controller
{
    public function dashboard()
    {
        $magasin = Magasin::where('responsable_id', auth()->id())->first();
        
        if (!$magasin) {
            return view('gestionnaire.no-magasin');
        }

        $stats = [
            'magasin' => $magasin,
            'boutiques' => Boutique::where('magasin_id', $magasin->id)->count(),
            'produits_en_stock' => StockMagasin::where('magasin_id', $magasin->id)->sum('quantite'),
            'alertes_stock' => StockMagasin::where('magasin_id', $magasin->id)
                ->whereColumn('quantite', '<=', 'seuil_alerte')
                ->count(),
            'ventes_boutiques' => Vente::whereIn('boutique_id', 
                Boutique::where('magasin_id', $magasin->id)->pluck('id')
            )->whereDate('date_vente', today())->count(),
            'alertes' => AlerteStock::with('produit')->where('statut', 'active')->orderBy('created_at', 'desc')->limit(5)->get(),
            'alertes_count' => AlerteStock::where('statut', 'active')->count(),
        ];

        return view('gestionnaire.dashboard', compact('stats'));
    }
}
