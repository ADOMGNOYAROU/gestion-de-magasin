<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockBoutique;
use App\Models\StockMagasin;

class StockController extends Controller
{
    public function magasins()
    {
        return response()->json(
            StockMagasin::with(['produit', 'magasin'])->get()->map(fn (StockMagasin $s) => [
                'id' => $s->id,
                'produit_id' => $s->produit_id,
                'produit_nom' => $s->produit?->nom,
                'site_id' => $s->magasin_id,
                'site_nom' => $s->magasin?->nom,
                'quantite' => $s->quantite,
                'seuil_alerte' => $s->seuil_alerte,
                'en_alerte' => $s->quantite <= $s->seuil_alerte,
            ])
        );
    }

    public function boutiques()
    {
        return response()->json(
            StockBoutique::with(['produit', 'boutique'])->get()->map(fn (StockBoutique $s) => [
                'id' => $s->id,
                'produit_id' => $s->produit_id,
                'produit_nom' => $s->produit?->nom,
                'site_id' => $s->boutique_id,
                'site_nom' => $s->boutique?->nom,
                'quantite' => $s->quantite,
                'seuil_alerte' => $s->seuil_alerte,
                'en_alerte' => $s->quantite <= $s->seuil_alerte,
            ])
        );
    }
}
