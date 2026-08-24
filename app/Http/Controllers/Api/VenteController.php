<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Boutique;
use App\Models\CashRegisterSession;
use App\Models\Produit;
use App\Models\StockBoutique;
use App\Models\Vente;
use App\Services\StockAlertNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VenteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Vente::with(['venteProduits.produit', 'paymentMethod', 'boutique'])
            ->latest('date_vente')
            ->latest('id');

        if ($user->role === 'vendeur' && $user->boutique_id) {
            $query->where('boutique_id', $user->boutique_id);
        }

        return response()->json(
            $query->limit(50)->get()->map(fn (Vente $v) => [
                'id' => $v->id,
                'numero_ticket' => $v->numero_ticket,
                'boutique' => $v->boutique?->nom,
                'montant_total' => (float) $v->montant_total,
                'montant_recu' => (float) $v->montant_recu,
                'monnaie' => (float) $v->monnaie,
                'mode_paiement' => $v->paymentMethod?->name,
                'status' => $v->status,
                'date_vente' => $v->date_vente?->toDateString(),
                'created_at' => $v->created_at?->toIso8601String(),
                'lignes' => $v->venteProduits->map(fn ($vp) => [
                    'produit' => $vp->produit?->nom,
                    'quantite' => $vp->quantite,
                    'prix_unitaire' => (float) $vp->prix_unitaire,
                    'remise' => (float) $vp->remise,
                    'sous_total' => (float) $vp->sous_total,
                ]),
            ])
        );
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'boutique_id' => ['nullable', 'exists:boutiques,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'montant_recu' => ['required', 'numeric', 'min:0'],
            'lignes' => ['required', 'array', 'min:1'],
            'lignes.*.produit_id' => ['required', 'exists:produits,id'],
            'lignes.*.quantite' => ['required', 'integer', 'min:1'],
            'lignes.*.remise' => ['nullable', 'numeric', 'min:0'],
        ]);

        $boutiqueId = $data['boutique_id'] ?? $user->boutique_id;

        if (! $boutiqueId) {
            throw ValidationException::withMessages([
                'boutique_id' => ['Aucune boutique associée à cet utilisateur.'],
            ]);
        }

        $sessionCaisse = CashRegisterSession::where('vendeur_id', $user->id)
            ->where('status', 'ouverte')
            ->first();

        $boutique = Boutique::findOrFail($boutiqueId);

        $vente = DB::transaction(function () use ($data, $boutiqueId, $boutique, $user, $sessionCaisse) {
            $vente = Vente::create([
                'boutique_id' => $boutiqueId,
                'user_id' => $user->id,
                'session_caisse_id' => $sessionCaisse?->id,
                'payment_method_id' => $data['payment_method_id'],
                'montant_total' => 0,
                'montant_recu' => $data['montant_recu'],
                'date_vente' => now()->toDateString(),
                'status' => 'terminee',
            ]);

            foreach ($data['lignes'] as $ligne) {
                $produit = Produit::findOrFail($ligne['produit_id']);
                $vente->ajouterProduit($produit, $ligne['quantite'], null, $ligne['remise'] ?? 0);

                $stock = StockBoutique::where('boutique_id', $boutiqueId)
                    ->where('produit_id', $produit->id)
                    ->first();

                if ($stock) {
                    $quantiteAvant = $stock->quantite;
                    $stock->decrement('quantite', $ligne['quantite']);

                    StockAlertNotifier::notifyIfNewlyCritical(
                        $produit,
                        $boutique->nom,
                        $quantiteAvant,
                        $quantiteAvant - $ligne['quantite'],
                        $stock->seuil_alerte,
                        $boutique->magasin_id,
                    );
                }
            }

            $vente->calculerMonnaie();

            if ($sessionCaisse) {
                $sessionCaisse->calculerMontantTheorique();
            }

            return $vente->fresh(['venteProduits.produit', 'paymentMethod', 'boutique']);
        });

        return response()->json([
            'id' => $vente->id,
            'numero_ticket' => $vente->numero_ticket,
            'montant_total' => (float) $vente->montant_total,
            'monnaie' => (float) $vente->monnaie,
        ], 201);
    }
}
