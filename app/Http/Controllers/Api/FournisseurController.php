<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    public function index()
    {
        return response()->json(Fournisseur::orderBy('nom')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:fournisseurs,email'],
            'contact_personne' => ['required', 'string', 'max:255'],
        ]);

        $fournisseur = Fournisseur::create($data);

        return response()->json($fournisseur, 201);
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:fournisseurs,email,' . $fournisseur->id],
            'contact_personne' => ['required', 'string', 'max:255'],
        ]);

        $fournisseur->update($data);

        return response()->json($fournisseur);
    }

    public function destroy(Fournisseur $fournisseur)
    {
        $fournisseur->delete();

        return response()->json(['message' => 'Fournisseur supprimé.']);
    }
}
