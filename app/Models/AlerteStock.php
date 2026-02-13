<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlerteStock extends Model
{
    protected $fillable = ['produit_id', 'niveau', 'message', 'statut'];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
