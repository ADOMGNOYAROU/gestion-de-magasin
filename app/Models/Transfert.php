<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{
    protected $fillable = [
        'produit_id', 
        'magasin_id', 
        'boutique_id', 
        'quantite', 
        'date'
    ];

    protected $casts = [
        'date' => 'date',
        'quantite' => 'integer',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }
}
