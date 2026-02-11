<?php

namespace App\Http\Controllers;

use App\Models\Magasin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MagasinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $magasins = Magasin::with('responsable')->paginate(10);

        return view('magasins.index', compact('magasins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $responsables = User::whereIn('role', ['admin', 'gestionnaire'])->get();

        return view('magasins.create', compact('responsables'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:magasins',
            'localisation' => 'required|string|max:255',
            'responsable_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Magasin::create($request->all());

            return redirect()->route('magasins.index')->with('success', 'Magasin créé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la création du magasin: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Magasin $magasin)
    {
        $magasin->load(['responsable', 'boutiques', 'stockMagasins.produit']);

        return view('magasins.show', compact('magasin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Magasin $magasin)
    {
        $responsables = User::whereIn('role', ['admin', 'gestionnaire'])->get();

        return view('magasins.edit', compact('magasin', 'responsables'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Magasin $magasin)
    {
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'max:255', 'unique:magasins,nom,' . $magasin->id],
            'localisation' => 'required|string|max:255',
            'responsable_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $magasin->update($request->all());

        return redirect()->route('magasins.index')->with('success', 'Magasin mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Magasin $magasin)
    {
        // Check if magasin has boutiques
        if ($magasin->boutiques()->count() > 0) {
            return redirect()->route('magasins.index')->with('error', 'Impossible de supprimer ce magasin car il contient des boutiques.');
        }

        $magasin->delete();

        return redirect()->route('magasins.index')->with('success', 'Magasin supprimé avec succès.');
    }
}
