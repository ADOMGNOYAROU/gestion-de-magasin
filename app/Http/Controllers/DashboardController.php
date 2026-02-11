<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\StockMagasin;
use App\Models\StockBoutique;
use App\Models\Vente;
use App\Models\VenteProduit;
use App\Models\Magasin;
use App\Models\Boutique;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Affiche le dashboard avec les statistiques
     */
    public function index()
    {
        $user = Auth::user();
        $stats = [];

        if ($user->isAdmin()) {
            $stats = $this->getAdminStats();
        } elseif ($user->isGestionnaire()) {
            $stats = $this->getGestionnaireStats();
        } elseif ($user->isVendeur()) {
            $stats = $this->getVendeurStats();
        }

        return view('dashboard.index', $stats);
    }

    /**
     * Statistiques pour l'administrateur
     */
    private function getAdminStats()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfDay = $now->copy()->startOfDay();

        // Statistiques générales
        $stats['totalProduits'] = Produit::where('statut', 'actif')->count();
        $stats['stockTotalMagasin'] = StockMagasin::sum('quantite');
        $stats['stockTotalBoutiques'] = StockBoutique::sum('quantite');

        // Ventes du jour
        $ventesJour = Vente::whereDate('date_vente', $startOfDay->toDateString());
        $stats['ventesJour'] = $ventesJour->count();
        $stats['caJour'] = $ventesJour->sum('montant_total');
        $stats['beneficeJour'] = $ventesJour->sum('montant_total'); // À ajuster si vous avez un champ bénéfice

        // Ventes du mois
        $ventesMois = Vente::whereDate('date_vente', '>=', $startOfMonth->toDateString());
        $stats['ventesMois'] = $ventesMois->count();
        $stats['caMois'] = $ventesMois->sum('montant_total');
        $stats['beneficeMois'] = $ventesMois->sum('montant_total'); // À ajuster si vous avez un champ bénéfice

        // Produits en rupture
        $stats['produitsEnRupture'] = $this->getProduitsEnRupture();

        // Top 5 produits les plus vendus
        $stats['topProduits'] = $this->getTopProduits(5);

        // Graphiques
        $stats['ventesParJour'] = $this->getVentesParJour(7);
        $stats['ventesParProduit'] = $this->getVentesParProduit(10);

        return $stats;
    }

    /**
     * Statistiques pour le gestionnaire
     */
    private function getGestionnaireStats()
    {
        $user = Auth::user();
        $magasin = $user->magasinResponsable;
        
        // Si le gestionnaire n'a pas de magasin, retourner des stats vides
        if (!$magasin) {
            return [
                'totalProduits' => Produit::where('statut', 'actif')->count(),
                'stockTotalMagasin' => 0,
                'stockTotalBoutiques' => 0,
                'ventesJour' => 0,
                'caJour' => 0,
                'beneficeJour' => 0,
                'ventesMois' => 0,
                'caMois' => 0,
                'beneficeMois' => 0,
                'magasin' => null,
                'boutiques' => 0,
                'produits_en_stock' => 0,
                'alertes_stock' => 0,
                'ventes_boutiques' => 0,
                'topProduits' => [],
                'ventesParJour' => [],
                'ventesParProduit' => [],
                'produitsEnRupture' => [],
            ];
        }
        
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfDay = $now->copy()->startOfDay();

        // Statistiques générales du magasin
        $stats['totalProduits'] = Produit::where('statut', 'actif')->count();
        $stats['stockTotalMagasin'] = StockMagasin::where('magasin_id', $magasin->id)->sum('quantite');
        $stats['stockTotalBoutiques'] = StockBoutique::whereHas('boutique', function($q) use ($magasin) {
            $q->where('magasin_id', $magasin->id);
        })->sum('quantite');

        // Ventes du jour (boutiques du magasin)
        $ventesJour = Vente::whereDate('date_vente', $startOfDay)
                          ->whereHas('boutique', function($q) use ($magasin) {
                              $q->where('magasin_id', $magasin->id);
                          });
        $stats['ventesJour'] = $ventesJour->count();
        $stats['caJour'] = $ventesJour->sum('montant_total');
        $stats['beneficeJour'] = $ventesJour->sum('montant_total'); // À ajuster si vous avez un champ bénéfice

        // Ventes du mois
        $ventesMois = Vente::whereDate('date_vente', '>=', $startOfMonth)
                          ->whereHas('boutique', function($q) use ($magasin) {
                              $q->where('magasin_id', $magasin->id);
                          });
        $stats['ventesMois'] = $ventesMois->count();
        $stats['caMois'] = $ventesMois->sum('montant_total');
        $stats['beneficeMois'] = $ventesMois->sum('montant_total'); // À ajuster si vous avez un champ bénéfice

        // Produits en rupture (magasin et boutiques)
        $stats['produitsEnRupture'] = $this->getProduitsEnRuptureMagasin($magasin->id);

        // Top 5 produits du magasin
        $stats['topProduits'] = $this->getTopProduitsMagasin(5, $magasin->id);

        // Graphiques
        $stats['ventesParJour'] = $this->getVentesParJourMagasin(7, $magasin->id);
        $stats['ventesParProduit'] = $this->getVentesParProduitMagasin(10, $magasin->id);

        // Infos du magasin
        $stats['magasin'] = $magasin;

        return $stats;
    }

    /**
     * Statistiques pour le vendeur
     */
    private function getVendeurStats()
    {
        $user = Auth::user();
        $boutique = $user->boutique;
        
        // Si le vendeur n'a pas de boutique, retourner des stats vides
        if (!$boutique) {
            return [
                'totalProduits' => Produit::where('statut', 'actif')->count(),
                'stockTotalMagasin' => 0,
                'stockTotalBoutiques' => 0,
                'ventesJour' => 0,
                'caJour' => 0,
                'beneficeJour' => 0,
                'ventesMois' => 0,
                'caMois' => 0,
                'beneficeMois' => 0,
                'boutique' => null,
                'produits_en_stock' => 0,
                'alertes_stock' => 0,
                'ventes_aujourd_hui' => 0,
                'ventes_mois' => 0,
                'topProduits' => [],
                'ventesParJour' => [],
                'ventesParProduit' => [],
                'produitsEnRupture' => [],
            ];
        }
        
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfDay = $now->copy()->startOfDay();

        // Statistiques générales de la boutique
        $stats['totalProduits'] = Produit::where('statut', 'actif')->count();
        $stats['stockTotalMagasin'] = StockMagasin::sum('quantite'); // Pour info
        $stats['stockTotalBoutiques'] = StockBoutique::where('boutique_id', $boutique->id)->sum('quantite');

        // Ventes du jour (boutique du vendeur)
        $ventesJour = Vente::whereDate('date_vente', $startOfDay)
                          ->where('boutique_id', $boutique->id);
        $stats['ventesJour'] = $ventesJour->count();
        $stats['caJour'] = $ventesJour->sum('montant_total');
        $stats['beneficeJour'] = $ventesJour->sum('montant_total'); // À ajuster si vous avez un champ bénéfice

        // Ventes du mois
        $ventesMois = Vente::whereDate('date_vente', '>=', $startOfMonth)
                          ->where('boutique_id', $boutique->id);
        $stats['ventesMois'] = $ventesMois->count();
        $stats['caMois'] = $ventesMois->sum('montant_total');
        $stats['beneficeMois'] = $ventesMois->sum('montant_total'); // À ajuster si vous avez un champ bénéfice

        // Produits en rupture dans la boutique
        $stats['produitsEnRupture'] = $this->getProduitsEnRuptureBoutique($boutique->id);

        // Top 5 produits de la boutique
        $stats['topProduits'] = $this->getTopProduitsBoutique(5, $boutique->id);

        // Graphiques
        $stats['ventesParJour'] = $this->getVentesParJourBoutique(7, $boutique->id);
        $stats['ventesParProduit'] = $this->getVentesParProduitBoutique(10, $boutique->id);

        // Infos de la boutique
        $stats['boutique'] = $boutique;

        return $stats;
    }

    /**
     * Produits en rupture de stock (global)
     */
    private function getProduitsEnRupture()
    {
        $seuilAlerte = 10; // Seuil d'alerte fixe, vous pouvez ajuster cette valeur
        
        $rupturesMagasin = StockMagasin::with('produit', 'magasin')
                                      ->where('quantite', '<=', $seuilAlerte)
                                      ->get()
                                      ->map(function($stock) use ($seuilAlerte) {
                                          return [
                                              'produit' => $stock->produit,
                                              'type' => 'Magasin',
                                              'lieu' => $stock->magasin->nom,
                                              'quantite' => $stock->quantite,
                                              'seuil' => $seuilAlerte
                                          ];
                                      });

        $rupturesBoutique = StockBoutique::with('produit', 'boutique')
                                        ->where('quantite', '<=', $seuilAlerte)
                                        ->get()
                                        ->map(function($stock) use ($seuilAlerte) {
                                            return [
                                                'produit' => $stock->produit,
                                                'type' => 'Boutique',
                                                'lieu' => $stock->boutique->nom,
                                                'quantite' => $stock->quantite,
                                                'seuil' => $seuilAlerte
                                            ];
                                        });

        return $rupturesMagasin->merge($rupturesBoutique)->sortBy('quantite')->take(10);
    }

    /**
     * Produits en rupture pour un magasin
     */
    private function getProduitsEnRuptureMagasin($magasinId)
    {
        $seuilAlerte = 10; // Seuil d'alerte fixe
        
        $rupturesMagasin = StockMagasin::with('produit', 'magasin')
                                      ->where('magasin_id', $magasinId)
                                      ->where('quantite', '<=', $seuilAlerte)
                                      ->get()
                                      ->map(function($stock) use ($seuilAlerte) {
                                          return [
                                              'produit' => $stock->produit,
                                              'type' => 'Magasin',
                                              'lieu' => $stock->magasin->nom,
                                              'quantite' => $stock->quantite,
                                              'seuil' => $seuilAlerte
                                          ];
                                      });

        $rupturesBoutique = StockBoutique::with(['produit', 'boutique'])
                                        ->whereHas('boutique', function($q) use ($magasinId) {
                                            $q->where('magasin_id', $magasinId);
                                        })
                                        ->where('quantite', '<=', $seuilAlerte)
                                        ->get()
                                        ->map(function($stock) use ($seuilAlerte) {
                                            return [
                                                'produit' => $stock->produit,
                                                'type' => 'Boutique',
                                                'lieu' => $stock->boutique->nom,
                                                'quantite' => $stock->quantite,
                                                'seuil' => $seuilAlerte
                                            ];
                                        });

        return $rupturesMagasin->merge($rupturesBoutique)->sortBy('quantite')->take(10);
    }

    /**
     * Produits en rupture pour une boutique
     */
    private function getProduitsEnRuptureBoutique($boutiqueId)
    {
        $seuilAlerte = 10; // Seuil d'alerte fixe
        
        return StockBoutique::with('produit', 'boutique')
                           ->where('boutique_id', $boutiqueId)
                           ->where('quantite', '<=', $seuilAlerte)
                           ->get()
                           ->map(function($stock) use ($seuilAlerte) {
                               return [
                                   'produit' => $stock->produit,
                                   'type' => 'Boutique',
                                   'lieu' => $stock->boutique->nom,
                                   'quantite' => $stock->quantite,
                                   'seuil' => $seuilAlerte
                               ];
                           })
                           ->sortBy('quantite')
                           ->take(10);
    }

    /**
     * Top produits les plus vendus (global)
     */
    private function getTopProduits($limit = 5)
    {
        return VenteProduit::with(['produit', 'vente'])
                   ->select('produit_id', DB::raw('SUM(quantite) as total_vendu'), DB::raw('SUM(sous_total) as total_ca'))
                   ->join('ventes', 'vente_produits.vente_id', '=', 'ventes.id')
                   ->groupBy('produit_id')
                   ->orderBy('total_vendu', 'desc')
                   ->limit($limit)
                   ->get()
                   ->map(function($venteProduit) {
                       return [
                           'produit' => $venteProduit->produit,
                           'quantite' => $venteProduit->total_vendu,
                           'ca' => $venteProduit->total_ca
                       ];
                   });
    }

    /**
     * Top produits pour un magasin
     */
    private function getTopProduitsMagasin($limit = 5, $magasinId)
    {
        return VenteProduit::with(['produit', 'vente.boutique'])
                   ->select('produit_id', DB::raw('SUM(quantite) as total_vendu'), DB::raw('SUM(sous_total) as total_ca'))
                   ->join('ventes', 'vente_produits.vente_id', '=', 'ventes.id')
                   ->join('boutiques', 'ventes.boutique_id', '=', 'boutiques.id')
                   ->where('boutiques.magasin_id', $magasinId)
                   ->groupBy('produit_id')
                   ->orderBy('total_vendu', 'desc')
                   ->limit($limit)
                   ->get()
                   ->map(function($venteProduit) {
                       return [
                           'produit' => $venteProduit->produit,
                           'quantite' => $venteProduit->total_vendu,
                           'ca' => $venteProduit->total_ca
                       ];
                   });
    }

    /**
     * Top produits pour une boutique
     */
    private function getTopProduitsBoutique($limit = 5, $boutiqueId)
    {
        return VenteProduit::with(['produit', 'vente'])
                   ->select('produit_id', DB::raw('SUM(quantite) as total_vendu'), DB::raw('SUM(sous_total) as total_ca'))
                   ->join('ventes', 'vente_produits.vente_id', '=', 'ventes.id')
                   ->where('ventes.boutique_id', $boutiqueId)
                   ->groupBy('produit_id')
                   ->orderBy('total_vendu', 'desc')
                   ->limit($limit)
                   ->get()
                   ->map(function($venteProduit) {
                       return [
                           'produit' => $venteProduit->produit,
                           'quantite' => $venteProduit->total_vendu,
                           'ca' => $venteProduit->total_ca
                       ];
                   });
    }

    /**
     * Ventes par jour (global)
     */
    private function getVentesParJour($jours = 7)
    {
        $data = Vente::whereDate('date_vente', '>=', Carbon::now()->subDays($jours - 1))
                   ->selectRaw('DATE(date_vente) as date, SUM(montant_total) as ca, COUNT(*) as ventes')
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get();

        // Remplir les jours manquants avec 0
        $result = [];
        for ($i = $jours - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayData = $data->where('date', $date)->first();
            $result[] = [
                'date' => Carbon::parse($date)->format('d/m'),
                'ca' => $dayData ? $dayData->ca : 0,
                'ventes' => $dayData ? $dayData->ventes : 0
            ];
        }

        return $result;
    }

    /**
     * Ventes par jour pour un magasin
     */
    private function getVentesParJourMagasin($jours = 7, $magasinId)
    {
        $data = Vente::whereDate('date', '>=', Carbon::now()->subDays($jours - 1))
                   ->whereHas('boutique', function($q) use ($magasinId) {
                       $q->where('magasin_id', $magasinId);
                   })
                   ->selectRaw('DATE(date) as date, SUM(prix_total) as ca, COUNT(*) as ventes')
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get();

        $result = [];
        for ($i = $jours - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayData = $data->where('date', $date)->first();
            $result[] = [
                'date' => Carbon::parse($date)->format('d/m'),
                'ca' => $dayData ? $dayData->ca : 0,
                'ventes' => $dayData ? $dayData->ventes : 0
            ];
        }

        return $result;
    }

    /**
     * Ventes par jour pour une boutique
     */
    private function getVentesParJourBoutique($jours = 7, $boutiqueId)
    {
        $data = Vente::whereDate('date', '>=', Carbon::now()->subDays($jours - 1))
                   ->where('boutique_id', $boutiqueId)
                   ->selectRaw('DATE(date) as date, SUM(prix_total) as ca, COUNT(*) as ventes')
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get();

        $result = [];
        for ($i = $jours - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayData = $data->where('date', $date)->first();
            $result[] = [
                'date' => Carbon::parse($date)->format('d/m'),
                'ca' => $dayData ? $dayData->ca : 0,
                'ventes' => $dayData ? $dayData->ventes : 0
            ];
        }

        return $result;
    }

    /**
     * Ventes par produit (global)
     */
    private function getVentesParProduit($limit = 10)
    {
        return VenteProduit::with('produit')
                   ->select('produit_id', DB::raw('SUM(quantite) as total_vendu'))
                   ->join('ventes', 'vente_produits.vente_id', '=', 'ventes.id')
                   ->groupBy('produit_id')
                   ->orderBy('total_vendu', 'desc')
                   ->limit($limit)
                   ->get()
                   ->map(function($venteProduit) {
                       return [
                           'nom' => $venteProduit->produit->nom,
                           'quantite' => $venteProduit->total_vendu
                       ];
                   });
    }

    /**
     * Ventes par produit pour un magasin
     */
    private function getVentesParProduitMagasin($limit = 10, $magasinId)
    {
        return VenteProduit::with(['produit', 'vente.boutique'])
                   ->select('produit_id', DB::raw('SUM(quantite) as total_vendu'))
                   ->join('ventes', 'vente_produits.vente_id', '=', 'ventes.id')
                   ->join('boutiques', 'ventes.boutique_id', '=', 'boutiques.id')
                   ->where('boutiques.magasin_id', $magasinId)
                   ->groupBy('produit_id')
                   ->orderBy('total_vendu', 'desc')
                   ->limit($limit)
                   ->get()
                   ->map(function($venteProduit) {
                       return [
                           'nom' => $venteProduit->produit->nom,
                           'quantite' => $venteProduit->total_vendu
                       ];
                   });
    }

    /**
     * Ventes par produit pour une boutique
     */
    private function getVentesParProduitBoutique($limit = 10, $boutiqueId)
    {
        return VenteProduit::with(['produit', 'vente'])
                   ->select('produit_id', DB::raw('SUM(quantite) as total_vendu'))
                   ->join('ventes', 'vente_produits.vente_id', '=', 'ventes.id')
                   ->where('ventes.boutique_id', $boutiqueId)
                   ->groupBy('produit_id')
                   ->orderBy('total_vendu', 'desc')
                   ->limit($limit)
                   ->get()
                   ->map(function($venteProduit) {
                       return [
                           'nom' => $venteProduit->produit->nom,
                           'quantite' => $venteProduit->total_vendu
                       ];
                   });
    }
}
