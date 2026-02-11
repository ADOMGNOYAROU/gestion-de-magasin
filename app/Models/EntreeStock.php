<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntreeStock extends Model
{
    protected $fillable = [
        'produit_id', 
        'magasin_id', 
        'fournisseur_id', 
        'partenaire_id', 
        'user_id',
        'quantite', 
        'prix_unitaire', 
        'montant_total',
        'date_entree'
    ];

    protected $casts = [
        'date_entree' => 'date',
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'montant_total' => 'decimal:2',
    ];

    public function getDateAttribute()
    {
        return $this->date_entree;
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function partenaire()
    {
        return $this->belongsTo(Partenaire::class);
    }

    // Accesseur pour le total
    public function getTotalAttribute()
    {
        return $this->quantite * $this->prix_unitaire;
    }
}
