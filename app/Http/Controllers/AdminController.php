<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Magasin;
use App\Models\Boutique;
use App\Models\Produit;
use App\Models\Vente;
use App\Models\StockMagasin;
use App\Models\StockBoutique;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'utilisateurs' => User::count(),
            'magasins' => Magasin::count(),
            'boutiques' => Boutique::count(),
            'produits' => Produit::count(),
            'ventes_aujourd_hui' => Vente::whereDate('date_vente', today())->count(),
            'utilisateurs_par_role' => [
                'admin' => User::where('role', 'admin')->count(),
                'gestionnaire' => User::where('role', 'gestionnaire')->count(),
                'vendeur' => User::where('role', 'vendeur')->count(),
            ]
        ];

        // Alertes de stock faible (quantité < 10)
        $lowStockMagasin = StockMagasin::with(['produit', 'magasin'])
            ->where('quantite', '<', 10)
            ->get();

        $lowStockBoutique = StockBoutique::with(['produit', 'boutique'])
            ->where('quantite', '<', 10)
            ->get();

        $stockAlerts = collect([...$lowStockMagasin, ...$lowStockBoutique])->sortBy('quantite');

        return view('admin.dashboard', compact('stats', 'stockAlerts'));
    }
}
