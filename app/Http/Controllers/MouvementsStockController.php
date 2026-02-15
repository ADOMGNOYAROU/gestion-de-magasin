<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VenteProduit;
use App\Models\Transfert;
use App\Models\EntreeStock;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Collection;

class MouvementsStockController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-produits');

        $type = $request->get('type');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $produit_id = $request->get('produit_id');
        $user_id = $request->get('user_id');

        $mouvements = collect();

        // Collect from VenteProduit (sorties)
        $ventesQuery = VenteProduit::with(['vente', 'produit']);
        if ($produit_id) $ventesQuery->where('produit_id', $produit_id);
        if ($user_id) $ventesQuery->whereHas('vente', function($q) use ($user_id) { $q->where('user_id', $user_id); });
        if ($date_from) $ventesQuery->whereHas('vente', function($q) use ($date_from) { $q->whereDate('date_vente', '>=', $date_from); });
        if ($date_to) $ventesQuery->whereHas('vente', function($q) use ($date_to) { $q->whereDate('date_vente', '<=', $date_to); });

        $ventesQuery->get()->each(function($vp) use (&$mouvements) {
            $mouvements->push([
                'date' => $vp->vente->date_vente,
                'type' => 'sortie',
                'produit' => $vp->produit,
                'quantite' => -$vp->quantite,
                'user' => $vp->vente->user ?? null,
                'motif' => 'Vente #' . $vp->vente->id,
                'created_at' => $vp->vente->created_at,
            ]);
        });

        // Collect from Transfert (transferts)
        $transfertsQuery = Transfert::with(['produit', 'user']);
        if ($produit_id) $transfertsQuery->where('produit_id', $produit_id);
        if ($user_id) $transfertsQuery->where('user_id', $user_id);
        if ($date_from) $transfertsQuery->whereDate('date', '>=', $date_from);
        if ($date_to) $transfertsQuery->whereDate('date', '<=', $date_to);

        $transfertsQuery->get()->each(function($t) use (&$mouvements) {
            $mouvements->push([
                'date' => $t->date,
                'type' => 'transfert_sortie',
                'produit' => $t->produit,
                'quantite' => -$t->quantite,
                'user' => $t->user,
                'motif' => 'Transfert vers ' . $t->boutique->nom,
                'created_at' => $t->created_at,
            ]);
            $mouvements->push([
                'date' => $t->date,
                'type' => 'transfert_entree',
                'produit' => $t->produit,
                'quantite' => $t->quantite,
                'user' => $t->user,
                'motif' => 'Transfert depuis ' . $t->magasin->nom,
                'created_at' => $t->created_at,
            ]);
        });

        // Collect from EntreeStock (entrées)
        $entreesQuery = EntreeStock::with(['produit', 'user']);
        if ($produit_id) $entreesQuery->where('produit_id', $produit_id);
        if ($user_id) $entreesQuery->where('user_id', $user_id);
        if ($date_from) $entreesQuery->whereDate('date', '>=', $date_from);
        if ($date_to) $entreesQuery->whereDate('date', '<=', $date_to);

        $entreesQuery->get()->each(function($e) use (&$mouvements) {
            $mouvements->push([
                'date' => $e->date,
                'type' => 'entree',
                'produit' => $e->produit,
                'quantite' => $e->quantite,
                'user' => $e->user,
                'motif' => 'Entrée stock #' . $e->id,
                'created_at' => $e->created_at,
            ]);
        });

        // Filter by type if specified
        if ($type) {
            $mouvements = $mouvements->filter(function($m) use ($type) {
                return str_contains($m['type'], $type);
            });
        }

        // Sort by date desc
        $mouvements = $mouvements->sortByDesc('date');

        // Paginate (simple slice for now, or use LengthAwarePaginator)
        $perPage = 25;
        $page = $request->get('page', 1);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $mouvements->forPage($page, $perPage),
            $mouvements->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'pageName' => 'page']
        );

        $produits = Produit::orderBy('nom')->get();
        $users = User::where('role', '!=', 'vendeur')->orderBy('name')->get();

        return view('mouvements-stock.index', compact('paginated', 'type', 'date_from', 'date_to', 'produit_id', 'user_id', 'produits', 'users'));
    }
}
