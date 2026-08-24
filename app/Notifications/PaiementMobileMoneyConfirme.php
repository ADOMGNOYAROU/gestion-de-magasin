<?php

namespace App\Notifications;

use App\Models\MobileMoneyPayment;
use Illuminate\Notifications\Notification;

class PaiementMobileMoneyConfirme extends Notification
{
    public function __construct(private MobileMoneyPayment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Paiement Mobile Money confirmé',
            'message' => "Paiement de {$this->payment->amount} FCFA via {$this->payment->network} confirmé.",
            'mobile_money_payment_id' => $this->payment->id,
            'amount' => (float) $this->payment->amount,
            'network' => $this->payment->network,
        ];
    }
}
