<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Magasin;
use Illuminate\Http\Request;

class MagasinController extends Controller
{
    public function index()
    {
        return response()->json(
            Magasin::with('responsable')->orderBy('nom')->get()->map(fn (Magasin $m) => $this->format($m))
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'localisation' => ['required', 'string', 'max:255'],
            'responsable_id' => ['nullable', 'exists:users,id'],
        ]);

        $magasin = Magasin::create($data);

        return response()->json($this->format($magasin->load('responsable')), 201);
    }

    public function update(Request $request, Magasin $magasin)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'localisation' => ['required', 'string', 'max:255'],
            'responsable_id' => ['nullable', 'exists:users,id'],
        ]);

        $magasin->update($data);

        return response()->json($this->format($magasin->load('responsable')));
    }

    public function destroy(Magasin $magasin)
    {
        $magasin->delete();

        return response()->json(['message' => 'Magasin supprimé.']);
    }

    private function format(Magasin $m): array
    {
        return [
            'id' => $m->id,
            'nom' => $m->nom,
            'localisation' => $m->localisation,
            'responsable_id' => $m->responsable_id,
            'responsable' => $m->responsable?->name,
        ];
    }
}
