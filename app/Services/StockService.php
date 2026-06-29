<?php

namespace App\Services;

use App\Models\StockBoutique;
use RuntimeException;

class StockService
{
    /**
     * Décrémente le stock d'un produit dans une boutique, après vérification
     * de la disponibilité. Lève une exception si le stock est insuffisant.
     */
    public function decrementerStockBoutique(int $produitId, int $boutiqueId, int $quantite, string $nomProduit = ''): StockBoutique
    {
        $stock = StockBoutique::where('produit_id', $produitId)
            ->where('boutique_id', $boutiqueId)
            ->first();

        $disponible = $stock ? $stock->quantite : 0;

        if ($disponible < $quantite) {
            $nom = $nomProduit !== '' ? $nomProduit : "produit #{$produitId}";
            throw new RuntimeException("Stock insuffisant pour {$nom}. Disponible: {$disponible}, Demandé: {$quantite}");
        }

        $stock->quantite -= $quantite;
        $stock->save();

        return $stock;
    }
}
