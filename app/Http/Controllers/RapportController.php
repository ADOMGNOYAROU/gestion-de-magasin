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
use App\Models\Credit;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use PDF;
use TCPDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentesExport;
use App\Exports\CreditsExport;

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

        // Solution finale : créer DomPDF manuellement sans ServiceProvider
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'Arial');
        $options->set('chroot', null);
        $options->set('publicPath', null);
        
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Charger la vue
        $html = view('rapports.stock_pdf', $data)->render();
        $dompdf->loadHtml($html);
        
        // Rendre le PDF
        $dompdf->render();
        
        $filename = 'rapport_stock_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
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

        // Solution finale : créer DomPDF manuellement sans ServiceProvider
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'Arial');
        $options->set('chroot', null);
        $options->set('publicPath', null);
        
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Charger la vue
        $html = view('rapports.ventes_pdf', $data)->render();
        $dompdf->loadHtml($html);
        
        // Rendre le PDF
        $dompdf->render();
        
        $filename = 'rapport_ventes_' . $validated['date_debut'] . '_au_' . $validated['date_fin'] . '.pdf';
        
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
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

        // Solution finale : créer DomPDF manuellement sans ServiceProvider
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'Arial');
        $options->set('chroot', null);
        $options->set('publicPath', null);
        
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Charger la vue
        $html = view('rapports.partenaires_pdf', $data)->render();
        $dompdf->loadHtml($html);
        
        // Rendre le PDF
        $dompdf->render();
        
        $filename = 'rapport_partenaires_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Affiche le formulaire de rapport de crédits
     */
    public function rapportCreditsForm()
    {
        Gate::authorize('manage-rapports');

        $clients = Client::orderBy('nom')->get();

        return view('rapports.credits_form', compact('clients'));
    }

    /**
     * Génère le rapport de crédits en PDF
     */
    public function rapportCreditsPDF(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|string|in:unpaid,overdue',
            'client_id' => 'nullable|exists:clients,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
        ]);

        if (!empty($validated['date_debut']) && !empty($validated['date_fin']) && $validated['date_fin'] < $validated['date_debut']) {
            return back()->withErrors(['date_fin' => 'La date de fin doit être après la date de début.']);
        }

        $user = Auth::user();
        $data = $this->getCreditsData($validated, $user);
        $data['filters'] = $validated;
        $data['dateGeneration'] = now()->format('d/m/Y H:i:s');
        $data['user'] = $user;

        // Solution finale : créer DomPDF manuellement sans ServiceProvider
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'Arial');
        $options->set('chroot', null);
        $options->set('publicPath', null);
        
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Charger la vue
        $html = view('rapports.credits_pdf', $data)->render();
        $dompdf->loadHtml($html);
        
        // Rendre le PDF
        $dompdf->render();
        
        $filename = 'rapport_credits_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Génère le rapport de crédits en Excel
     */
    public function rapportCreditsExcel(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|string|in:unpaid,overdue',
            'client_id' => 'nullable|exists:clients,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
        ]);

        if (!empty($validated['date_debut']) && !empty($validated['date_fin']) && $validated['date_fin'] < $validated['date_debut']) {
            return back()->withErrors(['date_fin' => 'La date de fin doit être après la date de début.']);
        }

        $user = Auth::user();
        $data = $this->getCreditsData($validated, $user);

        $filename = 'rapport_credits_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new CreditsExport($data, $validated, $user), $filename);
    }

    /**
     * Récupère les données de crédits selon les filtres
     */
    private function getCreditsData($validated, $user)
    {
        $query = Credit::with(['client', 'vente.boutique.magasin']);

        if (!empty($validated['status'])) {
            if ($validated['status'] == 'unpaid') {
                $query->where('status', 'active');
            } elseif ($validated['status'] == 'overdue') {
                $query->where('status', 'active')->where('due_date', '<', now());
            }
        }

        if (!empty($validated['client_id'])) {
            $query->where('client_id', $validated['client_id']);
        }

        if (!empty($validated['date_debut'])) {
            $query->whereDate('created_at', '>=', $validated['date_debut']);
        }

        if (!empty($validated['date_fin'])) {
            $query->whereDate('created_at', '<=', $validated['date_fin']);
        }

        // Filtrer selon le rôle
        if ($user->isVendeur()) {
            $query->whereHas('vente', function($q) use ($user) {
                $q->where('boutique_id', $user->boutique_id);
            });
        } elseif ($user->isGestionnaire()) {
            $query->whereHas('vente.boutique', function($q) use ($user) {
                $q->where('magasin_id', $user->magasinResponsable->id);
            });
        }

        $credits = $query->orderBy('created_at')->get();

        $data['credits'] = $credits;
        $data['totalCredits'] = $credits->count();
        $data['totalAmount'] = $credits->sum('total_amount');
        $data['totalRemaining'] = $credits->sum('remaining_balance');

        return $data;
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
                $ventesParProduit[$produitId]['benefice'] += $vp->produit ? (($vp->prix_unitaire - ($vp->produit->prix_achat ?? 0)) * $vp->quantite) : 0;
            }
        }
        $data['ventesParProduit'] = collect($ventesParProduit);

        return $data;
    }
}
