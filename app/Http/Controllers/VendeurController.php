<?php

namespace App\Http\Controllers;

use App\Models\StockBoutique;
use App\Models\Vente;
use Illuminate\Http\Request;

class VendeurController extends Controller
{
    public function dashboard()
    {
        $boutique = auth()->user()->boutique;

        if (!$boutique) {
            return view('vendeur.no-boutique');
        }

        $stats = [
            'boutique' => $boutique,
            'produits_en_stock' => StockBoutique::where('boutique_id', $boutique->id)->sum('quantite'),
            'alertes_stock' => StockBoutique::where('boutique_id', $boutique->id)
                ->whereColumn('quantite', '<=', 'seuil_alerte')
                ->count(),
            'ventes_aujourd_hui' => Vente::where('boutique_id', $boutique->id)
                ->whereDate('date_vente', today())->count(),
            'ventes_mois' => Vente::where('boutique_id', $boutique->id)
                ->whereMonth('date_vente', now()->month)
                ->sum('montant_total'),
        ];

        return view('vendeur.dashboard', compact('stats'));
    }
}
