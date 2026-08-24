<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileMoneyPayment extends Model
{
    protected $fillable = [
        'user_id',
        'vente_id',
        'identifier',
        'tx_reference',
        'payment_reference',
        'phone_number',
        'network',
        'amount',
        'status',
        'paid_at',
        'raw_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'raw_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }
}
