<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boutique extends Model
{
    protected $fillable = ['nom', 'adresse', 'telephone', 'email', 'magasin_id', 'vendeur_id'];

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'vendeur_id');
    }

    public function stockBoutiques()
    {
        return $this->hasMany(StockBoutique::class);
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function transferts()
    {
        return $this->hasMany(Transfert::class, 'boutique_id');
    }
}
