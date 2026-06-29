<?php

namespace App\Services;

use App\Models\Produit;
use App\Models\StockBoutique;
use App\Models\StockMagasin;
use App\Models\User;
use App\Notifications\StockFaibleNotification;
use Illuminate\Support\Facades\Notification;
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

        $this->notifierSiStockBoutiqueFaible($stock);

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

        $this->notifierSiStockMagasinFaible($stock);

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

    /**
     * Notifie les admins et le gestionnaire concerné si le stock d'une
     * boutique vient de descendre à ou sous son seuil d'alerte.
     */
    private function notifierSiStockBoutiqueFaible(StockBoutique $stock): void
    {
        if ($stock->quantite > $stock->seuil_alerte) {
            return;
        }

        $stock->loadMissing('produit', 'boutique.magasin.responsable');
        $boutique = $stock->boutique;

        $destinataires = User::where('role', 'admin')->get();
        if ($boutique?->magasin?->responsable) {
            $destinataires->push($boutique->magasin->responsable);
        }

        $this->envoyerNotificationStockFaible(
            $destinataires->unique('id'),
            $stock->produit?->nom ?? "produit #{$stock->produit_id}",
            'boutique',
            $boutique?->nom ?? "boutique #{$stock->boutique_id}",
            $stock->quantite,
            $stock->seuil_alerte,
            route('boutiques.show', $stock->boutique_id)
        );
    }

    /**
     * Notifie les admins et le gestionnaire concerné si le stock d'un
     * magasin vient de descendre à ou sous son seuil d'alerte.
     */
    private function notifierSiStockMagasinFaible(StockMagasin $stock): void
    {
        if ($stock->quantite > $stock->seuil_alerte) {
            return;
        }

        $stock->loadMissing('produit', 'magasin.responsable');
        $magasin = $stock->magasin;

        $destinataires = User::where('role', 'admin')->get();
        if ($magasin?->responsable) {
            $destinataires->push($magasin->responsable);
        }

        $this->envoyerNotificationStockFaible(
            $destinataires->unique('id'),
            $stock->produit?->nom ?? "produit #{$stock->produit_id}",
            'magasin',
            $magasin?->nom ?? "magasin #{$stock->magasin_id}",
            $stock->quantite,
            $stock->seuil_alerte,
            route('magasins.show', $stock->magasin_id)
        );
    }

    private function envoyerNotificationStockFaible(
        $destinataires,
        string $produitNom,
        string $lieuType,
        string $lieuNom,
        int $quantite,
        int $seuilAlerte,
        string $url
    ): void {
        if ($destinataires->isEmpty()) {
            return;
        }

        Notification::send(
            $destinataires,
            new StockFaibleNotification($produitNom, $lieuType, $lieuNom, $quantite, $seuilAlerte, $url)
        );
    }
}
