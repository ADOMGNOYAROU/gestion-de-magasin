<?php

namespace App\Http\Controllers;

use App\Models\Partenaire;
use Illuminate\Http\Request;

class PartenaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partenaires = Partenaire::orderBy('nom')->paginate(10);

        return view('partenaires.index', compact('partenaires'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('partenaires.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:partenaires,email',
            'type_partenariat' => 'required|string|max:255',
        ]);

        Partenaire::create($validated);

        return redirect()->route('partenaires.index')
            ->with('success', 'Partenaire créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Partenaire $partenaire)
    {
        return view('partenaires.show', compact('partenaire'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partenaire $partenaire)
    {
        return view('partenaires.edit', compact('partenaire'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partenaire $partenaire)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:partenaires,email,' . $partenaire->id,
            'type_partenariat' => 'required|string|max:255',
        ]);

        $partenaire->update($validated);

        return redirect()->route('partenaires.index')
            ->with('success', 'Partenaire mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partenaire $partenaire)
    {
        $partenaire->delete();

        return redirect()->route('partenaires.index')
            ->with('success', 'Partenaire supprimé avec succès.');
    }
}
