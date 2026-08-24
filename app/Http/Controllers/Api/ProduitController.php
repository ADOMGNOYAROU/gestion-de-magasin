<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::query();

        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        if ($categorie = $request->query('categorie')) {
            $query->where('categorie', $categorie);
        }

        return response()->json(
            $query->orderBy('nom')->get()->map(fn (Produit $p) => $this->format($p))
        );
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $produit = Produit::create($data);

        return response()->json($this->format($produit), 201);
    }

    public function update(Request $request, Produit $produit)
    {
        $data = $this->validated($request, $produit->id);
        $produit->update($data);

        return response()->json($this->format($produit));
    }

    public function destroy(Produit $produit)
    {
        $produit->delete();

        return response()->json(['message' => 'Produit supprimé.']);
    }

    public function restore($id)
    {
        $produit = Produit::withTrashed()->findOrFail($id);
        $produit->restore();

        return response()->json($this->format($produit));
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'categorie' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'reference' => ['nullable', 'string', 'max:255', 'unique:produits,reference' . ($ignoreId ? ",{$ignoreId}" : '')],
            'prix_achat' => ['required', 'numeric', 'min:0'],
            'prix_vente' => ['required', 'numeric', 'min:0'],
            'statut' => ['nullable', 'in:actif,inactif'],
        ]);
    }

    private function format(Produit $p): array
    {
        return [
            'id' => $p->id,
            'nom' => $p->nom,
            'categorie' => $p->categorie,
            'description' => $p->description,
            'reference' => $p->reference,
            'prix_achat' => (float) $p->prix_achat,
            'prix_vente' => (float) $p->prix_vente,
            'statut' => $p->statut,
            'deleted_at' => $p->deleted_at?->toIso8601String(),
        ];
    }
}
