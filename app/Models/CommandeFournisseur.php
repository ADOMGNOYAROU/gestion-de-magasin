<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CommandeFournisseur extends Model
{
    protected $fillable = [
        'fournisseur_id',
        'user_id',
        'magasin_id',
        'numero_commande',
        'date_commande',
        'status',
        'date_livraison_prevue',
        'date_livraison_reelle',
        'total_ht',
        'tva',
        'total_ttc',
        'notes',
        'conditions_paiement',
    ];

    protected $casts = [
        'date_commande' => 'date',
        'date_livraison_prevue' => 'date',
        'date_livraison_reelle' => 'date',
        'total_ht' => 'decimal:2',
        'tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'conditions_paiement' => 'array',
    ];

    // Relations
    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function magasin(): BelongsTo
    {
        return $this->belongsTo(Magasin::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneCommandeFournisseur::class);
    }

    // Scopes
    public function scopeBrouillons($query)
    {
        return $query->where('status', 'brouillon');
    }

    public function scopeEnvoyees($query)
    {
        return $query->where('status', 'envoyee');
    }

    public function scopeConfirmees($query)
    {
        return $query->where('status', 'confirmee');
    }

    public function scopeEnCours($query)
    {
        return $query->whereIn('status', ['envoyee', 'confirmee', 'en_cours_livraison']);
    }

    public function scopeLivrees($query)
    {
        return $query->where('status', 'livree');
    }

    public function scopeAnnulees($query)
    {
        return $query->where('status', 'annulee');
    }

    // Méthodes utilitaires
    public function genererNumeroCommande(): string
    {
        return 'CF-' . date('Y') . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function calculerTotaux(): void
    {
        $totalHT = $this->lignes->sum('sous_total_ht');
        $totalTVA = $this->lignes->sum(function ($ligne) {
            return $ligne->sous_total_ht * ($ligne->tva_taux / 100);
        });
        $totalTTC = $totalHT + $totalTVA;

        $this->update([
            'total_ht' => $totalHT,
            'tva' => $totalTVA,
            'total_ttc' => $totalTTC,
        ]);
    }

    public function peutEtreModifiee(): bool
    {
        return in_array($this->status, ['brouillon', 'envoyee']);
    }

    public function peutEtreAnnulee(): bool
    {
        return !in_array($this->status, ['livree', 'annulee']);
    }

    public function estEnRetard(): bool
    {
        return $this->date_livraison_prevue &&
               $this->date_livraison_prevue->isPast() &&
               !in_array($this->status, ['livree', 'annulee']);
    }

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'brouillon' => '<span class="badge badge-secondary">Brouillon</span>',
            'envoyee' => '<span class="badge badge-info">Envoyée</span>',
            'confirmee' => '<span class="badge badge-primary">Confirmée</span>',
            'en_cours_livraison' => '<span class="badge badge-warning">En livraison</span>',
            'livree' => '<span class="badge badge-success">Livrée</span>',
            'annulee' => '<span class="badge badge-danger">Annulée</span>',
            default => '<span class="badge badge-light">Inconnu</span>',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'brouillon' => 'secondary',
            'envoyee' => 'info',
            'confirmee' => 'primary',
            'en_cours_livraison' => 'warning',
            'livree' => 'success',
            'annulee' => 'danger',
            default => 'light',
        };
    }
}
