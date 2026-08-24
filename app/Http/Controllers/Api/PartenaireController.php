<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use Illuminate\Http\Request;

class PartenaireController extends Controller
{
    public function index()
    {
        return response()->json(Partenaire::orderBy('nom')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:partenaires,email'],
            'type_partenariat' => ['required', 'string', 'max:255'],
        ]);

        $partenaire = Partenaire::create($data);

        return response()->json($partenaire, 201);
    }

    public function update(Request $request, Partenaire $partenaire)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:partenaires,email,' . $partenaire->id],
            'type_partenariat' => ['required', 'string', 'max:255'],
        ]);

        $partenaire->update($data);

        return response()->json($partenaire);
    }

    public function destroy(Partenaire $partenaire)
    {
        $partenaire->delete();

        return response()->json(['message' => 'Partenaire supprimé.']);
    }
}
