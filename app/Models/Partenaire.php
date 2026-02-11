<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partenaire extends Model
{
    protected $fillable = ['nom', 'adresse', 'telephone', 'email', 'type_partenariat'];

    public function entreesStock()
    {
        return $this->hasMany(EntreeStock::class);
    }
}
