<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenteProduit extends Model
{
    protected $fillable = [
        'vente_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'remise',
        'remise_pourcentage',
        'sous_total',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'remise' => 'decimal:2',
        'remise_pourcentage' => 'decimal:2',
        'sous_total' => 'decimal:2',
    ];

    // Relations
    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    // Accessors
    public function getPrixApresRemiseAttribute()
    {
        return $this->prix_unitaire - $this->remise;
    }

    public function getMontantTotalAttribute()
    {
        return $this->prix_unitaire * $this->quantite - $this->remise;
    }

    // Helper methods
    public function calculerSousTotal()
    {
        $prixApresRemise = $this->prix_unitaire - $this->remise;
        $this->sous_total = $prixApresRemise * $this->quantite;
        $this->save();
    }

    public function appliquerRemiseMontant($montant)
    {
        $this->remise = min($montant, $this->prix_unitaire * $this->quantite);
        $this->remise_pourcentage = ($this->remise / ($this->prix_unitaire * $this->quantite)) * 100;
        $this->calculerSousTotal();
    }

    public function appliquerRemisePourcentage($pourcentage)
    {
        $this->remise_pourcentage = min($pourcentage, 100);
        $this->remise = ($this->prix_unitaire * $this->quantite * $this->remise_pourcentage) / 100;
        $this->calculerSousTotal();
    }
}
