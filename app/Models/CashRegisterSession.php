<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegisterSession extends Model
{
    protected $fillable = [
        'vendeur_id',
        'boutique_id',
        'montant_initial',
        'montant_final',
        'montant_theorique',
        'ecart',
        'date_ouverture',
        'date_fermeture',
        'status',
        'notes',
    ];

    protected $casts = [
        'montant_initial' => 'decimal:2',
        'montant_final' => 'decimal:2',
        'montant_theorique' => 'decimal:2',
        'ecart' => 'decimal:2',
        'date_ouverture' => 'datetime',
        'date_fermeture' => 'datetime',
    ];

    // Relations
    public function vendeur()
    {
        return $this->belongsTo(User::class, 'vendeur_id');
    }

    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class, 'session_caisse_id');
    }

    // Scopes
    public function scopeOuverte($query)
    {
        return $query->where('status', 'ouverte');
    }

    public function scopeFermee($query)
    {
        return $query->where('status', 'fermee');
    }

    public function scopeEnCours($query)
    {
        return $query->where('status', 'en_cours');
    }

    public function scopeForVendeur($query, $vendeurId)
    {
        return $query->where('vendeur_id', $vendeurId);
    }

    public function scopeForBoutique($query, $boutiqueId)
    {
        return $query->where('boutique_id', $boutiqueId);
    }

    // Helper methods
    public function isOuverte()
    {
        return $this->status === 'ouverte';
    }

    public function isFermee()
    {
        return $this->status === 'fermee';
    }

    public function isEnCours()
    {
        return $this->status === 'en_cours';
    }

    public function calculerMontantTheorique()
    {
        $totalVentes = $this->ventes()->sum('montant_total');
        $this->montant_theorique = $this->montant_initial + $totalVentes;
        $this->save();
    }

    public function calculerEcart()
    {
        if ($this->montant_final !== null) {
            $this->ecart = $this->montant_final - $this->montant_theorique;
            $this->save();
        }
    }

    public function fermer($montantFinal, $notes = null)
    {
        $this->calculerMontantTheorique();
        $this->montant_final = $montantFinal;
        $this->calculerEcart();
        $this->date_fermeture = now();
        $this->status = 'fermee';
        if ($notes) {
            $this->notes = $notes;
        }
        $this->save();
    }
}
