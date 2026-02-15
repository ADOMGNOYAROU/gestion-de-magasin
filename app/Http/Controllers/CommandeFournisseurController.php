<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\CommandeFournisseur;
use App\Models\LigneCommandeFournisseur;
use App\Models\Fournisseur;
use App\Models\Produit;
use App\Models\Magasin;

class CommandeFournisseurController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'gestionnaire']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CommandeFournisseur::with(['fournisseur', 'user', 'magasin']);

        // Filtres
        if ($request->filled('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_debut')) {
            $query->where('date_commande', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date_commande', '<=', $request->date_fin);
        }

        // Tri
        $tri = $request->get('tri', 'date_commande');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($tri, $direction);

        $commandes = $query->paginate(15);

        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $stats = [
            'total' => CommandeFournisseur::count(),
            'brouillons' => CommandeFournisseur::where('status', 'brouillon')->count(),
            'en_cours' => CommandeFournisseur::whereIn('status', ['envoyee', 'confirmee', 'en_cours_livraison'])->count(),
            'livrees' => CommandeFournisseur::where('status', 'livree')->count(),
            'total_montant' => CommandeFournisseur::where('status', 'livree')->sum('total_ttc'),
        ];

        return view('commandes-fournisseurs.index', compact(
            'commandes', 'fournisseurs', 'stats', 'tri', 'direction'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $produits = Produit::where('statut', 'actif')->orderBy('nom')->get();
        $magasins = Magasin::all();

        // Si fournisseur sélectionné via paramètre
        $fournisseurId = $request->get('fournisseur_id');
        $selectedFournisseur = $fournisseurId ? Fournisseur::find($fournisseurId) : null;

        return view('commandes-fournisseurs.create', compact(
            'fournisseurs', 'produits', 'magasins', 'selectedFournisseur'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'magasin_id' => 'nullable|exists:magasins,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after:date_commande',
            'notes' => 'nullable|string|max:1000',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
            'produits.*.tva_taux' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Créer la commande
            $commande = CommandeFournisseur::create([
                'fournisseur_id' => $request->fournisseur_id,
                'user_id' => Auth::id(),
                'magasin_id' => $request->magasin_id ?: Auth::user()->magasinResponsable?->id,
                'numero_commande' => 'TEMP-' . time(), // Sera mis à jour après sauvegarde
                'date_commande' => $request->date_commande,
                'status' => 'brouillon',
                'date_livraison_prevue' => $request->date_livraison_prevue,
                'notes' => $request->notes,
            ]);

            // Générer le numéro de commande
            $commande->numero_commande = $commande->genererNumeroCommande();
            $commande->save();

            // Ajouter les lignes de commande
            foreach ($request->produits as $produitData) {
                LigneCommandeFournisseur::create([
                    'commande_fournisseur_id' => $commande->id,
                    'produit_id' => $produitData['produit_id'],
                    'quantite_commandee' => $produitData['quantite'],
                    'prix_unitaire_ht' => $produitData['prix_unitaire'],
                    'tva_taux' => $produitData['tva_taux'],
                ]);
            }

            // Calculer les totaux
            $commande->calculerTotaux();

            DB::commit();

            return redirect()->route('commandes-fournisseurs.show', $commande)
                            ->with('success', 'Commande fournisseur créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', 'Erreur lors de la création : ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CommandeFournisseur $commandeFournisseur)
    {
        $commandeFournisseur->load(['fournisseur', 'user', 'magasin', 'lignes.produit']);

        return view('commandes-fournisseurs.show', compact('commandeFournisseur'));
    }

    /**
     * Générer un rapport PDF pour une commande fournisseur
     */
    public function rapportPDF(CommandeFournisseur $commandeFournisseur)
    {
        // Vérifier les permissions
        Gate::authorize('manage-commandes-fournisseurs');

        $data = [
            'commande' => $commandeFournisseur->load(['fournisseur', 'user', 'magasin', 'lignes.produit']),
            'dateGeneration' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = PDF::loadView('commandes-fournisseurs.rapport_pdf', $data);
        
        $filename = 'commande_' . $commandeFournisseur->numero_commande . '_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CommandeFournisseur $commandeFournisseur)
    {
        if (!$commandeFournisseur->peutEtreModifiee()) {
            return redirect()->back()
                            ->with('error', 'Cette commande ne peut plus être modifiée.');
        }

        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $produits = Produit::where('statut', 'actif')->orderBy('nom')->get();
        $magasins = Magasin::all();

        $commandeFournisseur->load(['lignes.produit']);

        return view('commandes-fournisseurs.edit', compact(
            'commandeFournisseur', 'fournisseurs', 'produits', 'magasins'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CommandeFournisseur $commandeFournisseur)
    {
        if (!$commandeFournisseur->peutEtreModifiee()) {
            return redirect()->back()
                            ->with('error', 'Cette commande ne peut plus être modifiée.');
        }

        $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'magasin_id' => 'nullable|exists:magasins,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after:date_commande',
            'notes' => 'nullable|string|max:1000',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
            'produits.*.tva_taux' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour la commande
            $commandeFournisseur->update([
                'fournisseur_id' => $request->fournisseur_id,
                'magasin_id' => $request->magasin_id ?: Auth::user()->magasinResponsable?->id,
                'date_commande' => $request->date_commande,
                'date_livraison_prevue' => $request->date_livraison_prevue,
                'notes' => $request->notes,
            ]);

            // Supprimer les anciennes lignes
            $commandeFournisseur->lignes()->delete();

            // Ajouter les nouvelles lignes
            foreach ($request->produits as $produitData) {
                LigneCommandeFournisseur::create([
                    'commande_fournisseur_id' => $commandeFournisseur->id,
                    'produit_id' => $produitData['produit_id'],
                    'quantite_commandee' => $produitData['quantite'],
                    'prix_unitaire_ht' => $produitData['prix_unitaire'],
                    'tva_taux' => $produitData['tva_taux'],
                ]);
            }

            // Recalculer les totaux
            $commandeFournisseur->calculerTotaux();

            DB::commit();

            return redirect()->route('commandes-fournisseurs.show', $commandeFournisseur)
                            ->with('success', 'Commande fournisseur mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommandeFournisseur $commandeFournisseur)
    {
        if (!$commandeFournisseur->peutEtreModifiee()) {
            return redirect()->back()
                            ->with('error', 'Cette commande ne peut plus être supprimée.');
        }

        try {
            $commandeFournisseur->delete();

            return redirect()->route('commandes-fournisseurs.index')
                            ->with('success', 'Commande fournisseur supprimée avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Changer le statut de la commande
     */
    public function changerStatus(Request $request, CommandeFournisseur $commandeFournisseur)
    {
        $request->validate([
            'status' => 'required|in:brouillon,envoyee,confirmee,en_cours_livraison,livree,annulee',
            'date_livraison_reelle' => 'nullable|date|required_if:status,livree',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $updateData = ['status' => $request->status];

            if ($request->status === 'livree') {
                $updateData['date_livraison_reelle'] = $request->date_livraison_reelle;
            }

            if ($request->notes) {
                $updateData['notes'] = $commandeFournisseur->notes . "\n\n" . now()->format('d/m/Y H:i') . " - " . $request->notes;
            }

            $commandeFournisseur->update($updateData);

            return redirect()->back()
                            ->with('success', 'Statut de la commande mis à jour avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Erreur lors de la mise à jour du statut : ' . $e->getMessage());
        }
    }

    /**
     * Comparer les prix entre fournisseurs pour un produit
     */
    public function comparerPrix(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
        ]);

        $produit = Produit::findOrFail($request->produit_id);

        // Récupérer tous les fournisseurs qui ont fourni ce produit
        $fournisseursPrix = DB::table('ligne_commande_fournisseurs')
            ->join('commande_fournisseurs', 'ligne_commande_fournisseurs.commande_fournisseur_id', '=', 'commande_fournisseurs.id')
            ->join('fournisseurs', 'commande_fournisseurs.fournisseur_id', '=', 'fournisseurs.id')
            ->where('ligne_commande_fournisseurs.produit_id', $request->produit_id)
            ->where('commande_fournisseurs.status', 'livree')
            ->select(
                'fournisseurs.id',
                'fournisseurs.nom',
                'ligne_commande_fournisseurs.prix_unitaire_ht',
                'ligne_commande_fournisseurs.tva_taux',
                'commande_fournisseurs.date_commande'
            )
            ->orderBy('ligne_commande_fournisseurs.prix_unitaire_ht')
            ->get();

        return view('commandes-fournisseurs.comparer-prix', compact('produit', 'fournisseursPrix'));
    }

    /**
     * Générer des commandes de réapprovisionnement automatique
     */
    public function genererReappro(Request $request)
    {
        $seuil = $request->get('seuil_alerte', 10);
        $fournisseurId = $request->get('fournisseur_id');

        // Produits en rupture de stock ou sous le seuil
        $produitsARappro = Produit::where('statut', 'actif')
            ->whereHas('stockBoutiques', function($query) use ($seuil) {
                $query->where('quantite', '<=', $seuil);
            })
            ->with(['stockBoutiques'])
            ->get();

        if ($fournisseurId) {
            // Filtrer par fournisseur
            $produitsARappro = $produitsARappro->filter(function($produit) use ($fournisseurId) {
                $fournisseurs = DB::table('ligne_commande_fournisseurs')
                    ->join('commande_fournisseurs', 'ligne_commande_fournisseurs.commande_fournisseur_id', '=', 'commande_fournisseurs.id')
                    ->join('fournisseurs', 'commande_fournisseurs.fournisseur_id', '=', 'fournisseurs.id')
                    ->where('ligne_commande_fournisseurs.produit_id', $produit->id)
                    ->where('commande_fournisseurs.status', 'livree')
                    ->select('fournisseurs.id')
                    ->distinct()
                    ->pluck('id');
                return $fournisseurs->contains($fournisseurId);
            });
        }

        $fournisseurs = Fournisseur::orderBy('nom')->get();

        return view('commandes-fournisseurs.generer-reappro', compact(
            'produitsARappro', 'fournisseurs', 'seuil', 'fournisseurId'
        ));
    }

    /**
     * Historique des achats par fournisseur
     */
    public function historiqueFournisseur(Fournisseur $fournisseur)
    {
        $commandes = CommandeFournisseur::where('fournisseur_id', $fournisseur->id)
            ->with(['lignes.produit'])
            ->orderBy('date_commande', 'desc')
            ->paginate(15);

        $stats = [
            'total_commandes' => $commandes->total(),
            'total_montant' => $commandes->sum('total_ttc'),
            'commandes_livrees' => CommandeFournisseur::where('fournisseur_id', $fournisseur->id)->where('status', 'livree')->count(),
            'moyenne_delai' => $this->calculerDelaiMoyenLivraison($fournisseur->id),
        ];

        return view('commandes-fournisseurs.historique-fournisseur', compact(
            'fournisseur', 'commandes', 'stats'
        ));
    }

    private function calculerDelaiMoyenLivraison($fournisseurId)
    {
        $commandes = CommandeFournisseur::where('fournisseur_id', $fournisseurId)
            ->where('status', 'livree')
            ->whereNotNull('date_livraison_reelle')
            ->get();

        if ($commandes->isEmpty()) {
            return null;
        }

        $totalDelai = 0;
        foreach ($commandes as $commande) {
            $delai = $commande->date_livraison_reelle->diffInDays($commande->date_commande);
            $totalDelai += $delai;
        }

        return round($totalDelai / $commandes->count(), 1);
    }
}
