<?php

namespace App\Notifications;

use App\Models\Produit;
use Illuminate\Notifications\Notification;

class StockCritiqueNotification extends Notification
{
    public function __construct(
        private Produit $produit,
        private string $site,
        private int $quantite,
        private int $seuilAlerte,
    ) {
    }

    /**
     * Pas de ShouldQueue : aucun worker de queue n'est lancé dans ce projet,
     * la notification doit donc être envoyée de façon synchrone.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Stock critique',
            'message' => "{$this->produit->nom} — {$this->site} : il ne reste que {$this->quantite} unité(s) (seuil : {$this->seuilAlerte}).",
            'produit_id' => $this->produit->id,
            'site' => $this->site,
            'quantite' => $this->quantite,
            'seuil_alerte' => $this->seuilAlerte,
        ];
    }
}
