<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class StockFaibleNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $produitNom,
        public string $lieuType,
        public string $lieuNom,
        public int $quantite,
        public int $seuilAlerte,
        public string $url,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Stock faible',
            'message' => "{$this->produitNom} : {$this->quantite} restant(s) dans {$this->lieuNom} (seuil : {$this->seuilAlerte})",
            'url' => $this->url,
            'lieu_type' => $this->lieuType,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'title' => 'Stock faible',
            'message' => "{$this->produitNom} : {$this->quantite} restant(s) dans {$this->lieuNom} (seuil : {$this->seuilAlerte})",
            'url' => $this->url,
            'lieu_type' => $this->lieuType,
            'created_at' => now()->toIso8601String(),
        ]);
    }
}
