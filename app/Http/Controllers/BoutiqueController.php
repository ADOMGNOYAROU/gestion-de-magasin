<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\Magasin;
use App\Models\User;
use Illuminate\Http\Request;

class BoutiqueController extends Controller
{
    /**
     * Afficher la liste des boutiques
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $boutiques = Boutique::with('magasin', 'responsable')->paginate(10);
        } elseif ($user->isGestionnaire()) {
            $magasinId = $user->magasinResponsable?->id;
            $boutiques = Boutique::with('magasin', 'responsable')
                ->where('magasin_id', $magasinId)
                ->paginate(10);
        } else {
            $boutiques = Boutique::whereRaw('1 = 0')->paginate(10); // Empty paginated result
        }

        return view('boutiques.index', compact('boutiques'));
    }

    /**
     * Afficher le formulaire de création de boutique
     */
    public function create()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $magasins = Magasin::all();
        } elseif ($user->isGestionnaire()) {
            $magasins = Magasin::where('id', $user->magasinResponsable?->id)->get();
        } else {
            $magasins = collect();
        }

        $vendeurs = User::where('role', 'vendeur')->get();

        return view('boutiques.create', compact('magasins', 'vendeurs'));
    }

    /**
     * Enregistrer une nouvelle boutique
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'telephone' => 'nullable|string',
            'magasin_id' => 'required|exists:magasins,id',
            'vendeur_id' => 'nullable|exists:users,id',
        ]);

        Boutique::create($request->all());

        return redirect()->route('boutiques.index')
            ->with('success', 'Boutique créée avec succès.');
    }

    /**
     * Afficher une boutique spécifique
     */
    public function show(Boutique $boutique)
    {
        $this->authorizeBoutiqueAccess($boutique);
        
        $boutique->load(['magasin', 'responsable', 'stocks.produit']);

        return view('boutiques.show', compact('boutique'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Boutique $boutique)
    {
        $this->authorizeBoutiqueAccess($boutique);
        
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $magasins = Magasin::all();
        } elseif ($user->isGestionnaire()) {
            $magasins = Magasin::where('id', $user->magasinResponsable?->id)->get();
        } else {
            $magasins = collect();
        }

        $vendeurs = User::where('role', 'vendeur')->get();

        return view('boutiques.edit', compact('boutique', 'magasins', 'vendeurs'));
    }

    /**
     * Mettre à jour une boutique
     */
    public function update(Request $request, Boutique $boutique)
    {
        $this->authorizeBoutiqueAccess($boutique);
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'telephone' => 'nullable|string',
            'magasin_id' => 'required|exists:magasins,id',
            'vendeur_id' => 'nullable|exists:users,id',
        ]);

        $boutique->update($request->all());

        return redirect()->route('boutiques.index')
            ->with('success', 'Boutique mise à jour avec succès.');
    }

    /**
     * Supprimer une boutique
     */
    public function destroy(Boutique $boutique)
    {
        $this->authorizeBoutiqueAccess($boutique);
        
        $boutique->delete();

        return redirect()->route('boutiques.index')
            ->with('success', 'Boutique supprimée avec succès.');
    }

    /**
     * Vérifier les autorisations d'accès à la boutique
     */
    private function authorizeBoutiqueAccess(Boutique $boutique)
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($user->isGestionnaire() && $boutique->magasin_id === $user->magasinResponsable?->id) {
            return true;
        }
        
        abort(403, 'Accès non autorisé.');
    }
}
