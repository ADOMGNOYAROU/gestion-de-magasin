<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $fillable = ['nom', 'contact', 'telephone', 'email'];

    public function entreesStock()
    {
        return $this->hasMany(EntreeStock::class);
    }
}
