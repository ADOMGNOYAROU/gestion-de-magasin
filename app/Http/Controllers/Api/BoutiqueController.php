<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Boutique;
use Illuminate\Http\Request;

class BoutiqueController extends Controller
{
    public function index()
    {
        return response()->json(
            Boutique::with(['magasin', 'responsable'])->orderBy('nom')->get()->map(fn (Boutique $b) => $this->format($b))
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:boutiques,email'],
            'magasin_id' => ['required', 'exists:magasins,id'],
            'vendeur_id' => ['nullable', 'exists:users,id'],
        ]);

        $boutique = Boutique::create($data);

        return response()->json($this->format($boutique->load(['magasin', 'responsable'])), 201);
    }

    public function update(Request $request, Boutique $boutique)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:boutiques,email,' . $boutique->id],
            'magasin_id' => ['required', 'exists:magasins,id'],
            'vendeur_id' => ['nullable', 'exists:users,id'],
        ]);

        $boutique->update($data);

        return response()->json($this->format($boutique->load(['magasin', 'responsable'])));
    }

    public function destroy(Boutique $boutique)
    {
        $boutique->delete();

        return response()->json(['message' => 'Boutique supprimée.']);
    }

    private function format(Boutique $b): array
    {
        return [
            'id' => $b->id,
            'nom' => $b->nom,
            'adresse' => $b->adresse,
            'telephone' => $b->telephone,
            'email' => $b->email,
            'magasin_id' => $b->magasin_id,
            'magasin_nom' => $b->magasin?->nom,
            'vendeur_id' => $b->vendeur_id,
            'vendeur' => $b->responsable?->name,
        ];
    }
}
