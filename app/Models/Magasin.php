<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Magasin extends Model
{
    protected $fillable = ['nom', 'localisation', 'responsable_id'];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function boutiques()
    {
        return $this->hasMany(Boutique::class);
    }

    public function stockMagasins()
    {
        return $this->hasMany(StockMagasin::class);
    }

    public function entreesStock()
    {
        return $this->hasMany(EntreeStock::class);
    }

    public function transferts()
    {
        return $this->hasMany(Transfert::class, 'magasin_id');
    }
}
