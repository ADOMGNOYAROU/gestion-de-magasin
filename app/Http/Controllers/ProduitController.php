<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProduitsImport;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Vérifier la permission de gérer les produits
        Gate::authorize('manage-produits');
        
        $search = $request->get('search');
        
        $produits = Produit::when($search, function($query, $search) {
                return $query->where('nom', 'like', '%'.$search.'%')
                           ->orWhere('categorie', 'like', '%'.$search.'%');
            })
            ->orderBy('nom')
            ->paginate(10);
            
        return view('produits.index', compact('produits', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produits.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|max:255',
            'categorie' => 'required',
            'description' => 'nullable',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0|gte:prix_achat',
            'statut' => 'required|in:actif,inactif',
        ]);

        Produit::create($validated);

        return redirect()->route('produits.index')
            ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produit = Produit::findOrFail($id);
        return view('produits.show', compact('produit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $produit = Produit::findOrFail($id);
        return view('produits.edit', compact('produit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $produit = Produit::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|max:255',
            'categorie' => 'required',
            'description' => 'nullable',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0|gte:prix_achat',
            'statut' => 'required|in:actif,inactif',
        ]);

        $produit->update($validated);

        return redirect()->route('produits.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produit = Produit::findOrFail($id);
        $produit->delete();

        return redirect()->route('produits.index')
            ->with('success', 'Produit supprimé avec succès.');
    }

    /**
     * Import products from Excel file.
     */
    public function import(Request $request)
    {
        Gate::authorize('manage-produits');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        Excel::import(new ProduitsImport, $request->file('file'));

        return redirect()->route('produits.index')
            ->with('success', 'Produits importés avec succès.');
    }
}
