<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMagasin extends Model
{
    protected $fillable = ['produit_id', 'magasin_id', 'quantite', 'seuil_alerte'];

    protected $casts = [
        'quantite' => 'integer',
        'seuil_alerte' => 'integer',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    // Accesseur pour vérifier si le stock est en alerte
    public function getEnAlerteAttribute()
    {
        return $this->quantite <= $this->seuil_alerte;
    }

    // Accesseur pour le statut du stock
    public function getStatutAttribute()
    {
        if ($this->quantite == 0) {
            return 'rupture';
        } elseif ($this->quantite <= $this->seuil_alerte) {
            return 'alerte';
        } else {
            return 'normal';
        }
    }
}
