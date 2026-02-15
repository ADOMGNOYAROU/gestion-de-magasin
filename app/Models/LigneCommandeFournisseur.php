<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneCommandeFournisseur extends Model
{
    protected $fillable = [
        'commande_fournisseur_id',
        'produit_id',
        'quantite_commandee',
        'quantite_livree',
        'prix_unitaire_ht',
        'tva_taux',
        'sous_total_ht',
        'sous_total_ttc',
        'notes',
    ];

    protected $casts = [
        'quantite_commandee' => 'integer',
        'quantite_livree' => 'integer',
        'prix_unitaire_ht' => 'decimal:2',
        'tva_taux' => 'decimal:2',
        'sous_total_ht' => 'decimal:2',
        'sous_total_ttc' => 'decimal:2',
    ];

    // Relations
    public function commandeFournisseur(): BelongsTo
    {
        return $this->belongsTo(CommandeFournisseur::class);
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    // Méthodes utilitaires
    public function calculerSousTotaux(): void
    {
        $this->sous_total_ht = $this->prix_unitaire_ht * $this->quantite_commandee;
        $this->sous_total_ttc = $this->sous_total_ht * (1 + ($this->tva_taux / 100));
        $this->save();
    }

    public function getQuantiteRestanteAttribute(): int
    {
        return $this->quantite_commandee - $this->quantite_livree;
    }

    public function estComplete(): bool
    {
        return $this->quantite_livree >= $this->quantite_commandee;
    }

    public function getTauxLivraisonAttribute(): float
    {
        if ($this->quantite_commandee == 0) {
            return 0;
        }
        return ($this->quantite_livree / $this->quantite_commandee) * 100;
    }

    // Boot method pour calcul automatique des sous-totaux
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($ligne) {
            $ligne->calculerSousTotaux();
        });
    }
}
