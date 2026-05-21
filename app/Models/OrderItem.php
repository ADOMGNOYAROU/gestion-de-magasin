<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'sous_total',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'sous_total' => 'decimal:2',
    ];

    // Relations
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    // Accessors
    public function getPrixApresRemiseAttribute()
    {
        return $this->prix_unitaire;
    }

    public function getMontantTotalAttribute()
    {
        return $this->prix_unitaire * $this->quantite;
    }

    // Helper methods
    public function calculerSousTotal()
    {
        $this->sous_total = $this->prix_unitaire * $this->quantite;
        $this->save();
    }
}
