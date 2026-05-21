<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Credit;

class Client extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
    ];

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }
}
