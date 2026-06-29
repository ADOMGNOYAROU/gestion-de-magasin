<?php

namespace App\Services;

use App\Models\Produit;
use App\Models\StockBoutique;
use App\Models\StockMagasin;
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

    /**
     * Incrémente le stock d'un produit dans une boutique, en créant la ligne
     * de stock si elle n'existe pas encore.
     */
    public function incrementerStockBoutique(int $produitId, int $boutiqueId, int $quantite, ?float $prixVenteParDefaut = null, int $seuilAlerteParDefaut = 5): StockBoutique
    {
        $stock = StockBoutique::where('produit_id', $produitId)
            ->where('boutique_id', $boutiqueId)
            ->first();

        if ($stock) {
            $stock->quantite += $quantite;
            $stock->save();

            return $stock;
        }

        $prixVente = $prixVenteParDefaut ?? Produit::findOrFail($produitId)->prix_vente;

        return StockBoutique::create([
            'produit_id' => $produitId,
            'boutique_id' => $boutiqueId,
            'quantite' => $quantite,
            'prix_vente' => $prixVente,
            'seuil_alerte' => $seuilAlerteParDefaut,
        ]);
    }

    /**
     * Décrémente le stock d'un produit dans un magasin, après vérification
     * de la disponibilité. Lève une exception si le stock est insuffisant.
     */
    public function decrementerStockMagasin(int $produitId, int $magasinId, int $quantite, string $nomProduit = ''): StockMagasin
    {
        $stock = StockMagasin::where('produit_id', $produitId)
            ->where('magasin_id', $magasinId)
            ->first();

        $disponible = $stock ? $stock->quantite : 0;

        if ($disponible < $quantite) {
            $nom = $nomProduit !== '' ? $nomProduit : "produit #{$produitId}";
            throw new RuntimeException("Stock insuffisant. Quantité disponible : {$disponible}, Quantité demandée : {$quantite} ({$nom})");
        }

        $stock->quantite -= $quantite;
        $stock->save();

        return $stock;
    }

    /**
     * Incrémente le stock d'un produit dans un magasin (sans création si absent).
     * Utilisé pour l'annulation d'un transfert, où la ligne de stock magasin
     * existait nécessairement avant le transfert initial.
     */
    public function incrementerStockMagasin(int $produitId, int $magasinId, int $quantite): ?StockMagasin
    {
        $stock = StockMagasin::where('produit_id', $produitId)
            ->where('magasin_id', $magasinId)
            ->first();

        if ($stock) {
            $stock->quantite += $quantite;
            $stock->save();
        }

        return $stock;
    }

    /**
     * Incrémente le stock d'un produit dans un magasin, en créant la ligne
     * de stock si elle n'existe pas encore (cas d'une entrée de stock).
     */
    public function incrementerOuCreerStockMagasin(int $produitId, int $magasinId, int $quantite, int $seuilAlerteParDefaut = 10): StockMagasin
    {
        $stock = StockMagasin::where('produit_id', $produitId)
            ->where('magasin_id', $magasinId)
            ->first();

        if ($stock) {
            $stock->quantite += $quantite;
            $stock->save();

            return $stock;
        }

        $produit = Produit::findOrFail($produitId);

        return StockMagasin::create([
            'produit_id' => $produitId,
            'magasin_id' => $magasinId,
            'quantite' => $quantite,
            'prix_vente' => $produit->prix_vente,
            'seuil_alerte' => $seuilAlerteParDefaut,
        ]);
    }
}
