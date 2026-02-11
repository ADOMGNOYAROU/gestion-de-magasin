<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\StockMagasin;
use App\Models\StockBoutique;
use App\Models\Vente;
use App\Models\EntreeStock;
use App\Models\Partenaire;
use App\Models\Magasin;
use App\Models\Boutique;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentesExport;

class RapportController extends Controller
{
    /**
     * Affiche la page des rapports
     */
    public function index()
    {
        // Vérifier la permission de gérer les rapports
        Gate::authorize('manage-rapports');
        
        return view('rapports.index');
    }

    /**
     * Génère le rapport de stock en PDF
     */
    public function rapportStockPDF()
    {
        $user = Auth::user();
        $data = [];

        if ($user->isAdmin()) {
            // Admin : voir tout le stock
            $data['produits'] = Produit::with(['stockMagasins.magasin', 'stockBoutiques.boutique'])
                                     ->where('statut', 'actif')
                                     ->orderBy('nom')
                                     ->get();
        } elseif ($user->isGestionnaire()) {
            // Gestionnaire : voir le stock de son magasin
            $magasin = $user->magasinResponsable;
            $data['produits'] = Produit::with(['stockMagasins' => function($q) use ($magasin) {
                                            $q->where('magasin_id', $magasin->id);
                                        }, 'stockBoutiques.boutique'])
                                     ->whereHas('stockMagasins', function($q) use ($magasin) {
                                         $q->where('magasin_id', $magasin->id);
                                     })
                                     ->where('statut', 'actif')
                                     ->orderBy('nom')
                                     ->get();
            $data['magasin'] = $magasin;
        } else {
            // Vendeur : voir le stock de sa boutique
            $boutique = $user->boutique;
            $data['produits'] = Produit::with(['stockBoutiques' => function($q) use ($boutique) {
                                            $q->where('boutique_id', $boutique->id);
                                        }])
                                     ->whereHas('stockBoutiques', function($q) use ($boutique) {
                                         $q->where('boutique_id', $boutique->id);
                                     })
                                     ->where('statut', 'actif')
                                     ->orderBy('nom')
                                     ->get();
            $data['boutique'] = $boutique;
        }

        $data['dateGeneration'] = now()->format('d/m/Y H:i:s');
        $data['user'] = $user;

        $pdf = PDF::loadView('rapports.stock_pdf', $data);
        
        $filename = 'rapport_stock_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Affiche le formulaire de rapport de ventes
     */
    public function rapportVentesForm()
    {
        $user = Auth::user();
        $magasins = [];
        $boutiques = [];

        if ($user->isAdmin()) {
            $magasins = Magasin::orderBy('nom')->get();
            $boutiques = Boutique::with('magasin')->orderBy('nom')->get();
        } elseif ($user->isGestionnaire()) {
            $magasins = collect([$user->magasinResponsable]);
            $boutiques = Boutique::where('magasin_id', $user->magasinResponsable->id)->orderBy('nom')->get();
        } else {
            $boutiques = collect([$user->boutique]);
        }

        return view('rapports.ventes_form', compact('magasins', 'boutiques'));
    }

    /**
     * Génère le rapport de ventes en PDF
     */
    public function rapportVentesPDF(Request $request)
    {
        $validated = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'magasin_id' => 'nullable|exists:magasins,id',
            'boutique_id' => 'nullable|exists:boutiques,id',
        ]);

        $user = Auth::user();
        $data = $this->getVentesData($validated, $user);
        $data['periode'] = [
            'debut' => Carbon::parse($validated['date_debut'])->format('d/m/Y'),
            'fin' => Carbon::parse($validated['date_fin'])->format('d/m/Y')
        ];
        $data['dateGeneration'] = now()->format('d/m/Y H:i:s');
        $data['user'] = $user;

        $pdf = PDF::loadView('rapports.ventes_pdf', $data);
        
        $filename = 'rapport_ventes_' . $validated['date_debut'] . '_au_' . $validated['date_fin'] . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Génère le rapport de ventes en Excel
     */
    public function rapportVentesExcel(Request $request)
    {
        $validated = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'magasin_id' => 'nullable|exists:magasins,id',
            'boutique_id' => 'nullable|exists:boutiques,id',
        ]);

        $user = Auth::user();
        $data = $this->getVentesData($validated, $user);

        $filename = 'rapport_ventes_' . $validated['date_debut'] . '_au_' . $validated['date_fin'] . '.xlsx';
        
        return Excel::download(new VentesExport($data, $validated, $user), $filename);
    }

    /**
     * Génère le rapport des partenaires en PDF
     */
    public function rapportPartenairesPDF()
    {
        // Vérifier la permission de voir les rapports partenaires
        Gate::authorize('view-rapports-partenaires');
        
        $user = Auth::user();
        $data = [];

        if ($user->isAdmin()) {
            // Admin : voir tous les partenaires
            $data['partenaires'] = Partenaire::with(['entreesStock.produit', 'entreesStock.magasin'])
                                            ->orderBy('nom')
                                            ->get();
        } elseif ($user->isGestionnaire()) {
            // Gestionnaire : voir les partenaires de son magasin
            $data['partenaires'] = Partenaire::with(['entreesStock' => function($q) use ($user) {
                                                $q->where('magasin_id', $user->magasinResponsable->id);
                                            }, 'entreesStock.produit', 'entreesStock.magasin'])
                                            ->whereHas('entreesStock', function($q) use ($user) {
                                                $q->where('magasin_id', $user->magasinResponsable->id);
                                            })
                                            ->orderBy('nom')
                                            ->get();
            $data['magasin'] = $user->magasinResponsable;
        } else {
            // Vendeur : pas accès aux rapports partenaires
            return back()->with('error', 'Vous n\'avez pas les permissions pour accéder aux rapports partenaires.');
        }

        $data['dateGeneration'] = now()->format('d/m/Y H:i:s');
        $data['user'] = $user;

        $pdf = PDF::loadView('rapports.partenaires_pdf', $data);
        
        $filename = 'rapport_partenaires_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Récupère les données de ventes selon les filtres
     */
    private function getVentesData($validated, $user)
    {
        $query = Vente::with(['venteProduits.produit', 'boutique.magasin'])
                      ->whereDate('date_vente', '>=', $validated['date_debut'])
                      ->whereDate('date_vente', '<=', $validated['date_fin']);

        // Filtrer selon le rôle
        if ($user->isVendeur()) {
            $query->where('boutique_id', $user->boutique_id);
        } elseif ($user->isGestionnaire()) {
            $query->whereHas('boutique', function($q) use ($user) {
                $q->where('magasin_id', $user->magasinResponsable->id);
            });
        }

        // Filtrer par magasin si spécifié
        if (!empty($validated['magasin_id'])) {
            $query->whereHas('boutique', function($q) use ($validated) {
                $q->where('magasin_id', $validated['magasin_id']);
            });
        }

        // Filtrer par boutique si spécifié
        if (!empty($validated['boutique_id'])) {
            $query->where('boutique_id', $validated['boutique_id']);
        }

        $ventes = $query->orderBy('date_vente')->get();

        // Calculer les totaux
        $data['ventes'] = $ventes;
        $data['venteProduits'] = $ventes->pluck('venteProduits')->flatten();
        $data['totalVentes'] = $ventes->count();
        $data['totalCA'] = $ventes->sum('montant_total');
        $data['totalBenefice'] = $ventes->sum(function($v) { return $v->benefice_total; });

        // Grouper par boutique
        $data['ventesParBoutique'] = $ventes->groupBy('boutique_id')
                                           ->map(function($group) {
                                               $first = $group->first();
                                               return [
                                                   'boutique' => $first->boutique,
                                                   'ventes' => $group->count(),
                                                   'ca' => $group->sum('montant_total'),
                                                   'benefice' => $group->sum(function($v) { return $v->benefice_total; })
                                               ];
                                           });

        // Grouper par produit
        $ventesParProduit = [];
        foreach ($ventes as $vente) {
            foreach ($vente->venteProduits as $vp) {
                $produitId = $vp->produit_id;
                if (!isset($ventesParProduit[$produitId])) {
                    $ventesParProduit[$produitId] = [
                        'produit' => $vp->produit,
                        'quantite' => 0,
                        'ca' => 0,
                        'benefice' => 0
                    ];
                }
                $ventesParProduit[$produitId]['quantite'] += $vp->quantite;
                $ventesParProduit[$produitId]['ca'] += $vp->sous_total;
                $ventesParProduit[$produitId]['benefice'] += ($vp->prix_unitaire - $vp->produit->prix_achat) * $vp->quantite;
            }
        }
        $data['ventesParProduit'] = collect($ventesParProduit);

        return $data;
    }
}
