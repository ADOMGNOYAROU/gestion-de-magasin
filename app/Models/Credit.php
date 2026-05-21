<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CreditPayment;

class Credit extends Model
{
    protected $fillable = [
        'vente_id',
        'client_id',
        'total_amount',
        'remaining_balance',
        'status',
        'due_date',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creditPayments()
    {
        return $this->hasMany(CreditPayment::class);
    }

    public function getRemainingStockAttribute()
    {
        $remainingStock = 0;
        $venteProduits = $this->vente->venteProduits;

        foreach ($venteProduits as $vp) {
            $fraction = $this->remaining_balance / $this->total_amount;
            $remainingStock += $fraction * $vp->quantite;
        }

        return round($remainingStock);
    }
}
