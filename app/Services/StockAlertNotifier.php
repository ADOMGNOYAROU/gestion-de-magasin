<?php

namespace App\Services;

use App\Models\Produit;
use App\Models\User;
use App\Notifications\StockCritiqueNotification;

class StockAlertNotifier
{
    /**
     * Notifie les admins et le gestionnaire concerné si le stock vient de passer
     * sous le seuil d'alerte (évite de renotifier à chaque vente une fois déjà en alerte).
     */
    public static function notifyIfNewlyCritical(
        Produit $produit,
        string $site,
        int $quantiteAvant,
        int $quantiteApres,
        int $seuilAlerte,
        ?int $magasinId = null,
    ): void {
        $etaitEnAlerte = $quantiteAvant <= $seuilAlerte;
        $estEnAlerte = $quantiteApres <= $seuilAlerte;

        if ($etaitEnAlerte || ! $estEnAlerte) {
            return;
        }

        $destinataires = User::where('role', 'admin')->get();

        if ($magasinId) {
            $destinataires = $destinataires->merge(
                User::where('role', 'gestionnaire')->where('magasin_id', $magasinId)->get()
            );
        }

        $notification = new StockCritiqueNotification($produit, $site, $quantiteApres, $seuilAlerte);

        foreach ($destinataires->unique('id') as $user) {
            $user->notify($notification);
        }
    }
}
