<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $acteurNom,
        public string $action,
        public string $ressourceLabel,
        public string $ressourceDisplay,
        public ?string $url,
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
            'title' => "{$this->ressourceLabel} {$this->action}",
            'message' => "{$this->acteurNom} a {$this->action} {$this->ressourceLabel} \"{$this->ressourceDisplay}\"",
            'url' => $this->url,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'title' => "{$this->ressourceLabel} {$this->action}",
            'message' => "{$this->acteurNom} a {$this->action} {$this->ressourceLabel} \"{$this->ressourceDisplay}\"",
            'url' => $this->url,
            'created_at' => now()->toIso8601String(),
        ]);
    }
}
