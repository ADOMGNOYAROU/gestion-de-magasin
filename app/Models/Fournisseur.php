<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $fillable = ['nom', 'adresse', 'telephone', 'email', 'contact_personne'];

    public function entreesStock()
    {
        return $this->hasMany(EntreeStock::class);
    }
}
