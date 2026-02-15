<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'client_id',
        'type',
        'valeur',
        'montant_minimum',
        'date_expiration',
        'utilise',
        'date_utilisation',
        'conditions_utilisation'
    ];

    protected $casts = [
        'valeur' => 'decimal:2',
        'montant_minimum' => 'decimal:2',
        'date_expiration' => 'datetime',
        'date_utilisation' => 'datetime',
        'utilise' => 'boolean'
    ];

    // Relations
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Méthodes utilitaires
    public function getEstExpireAttribute(): bool
    {
        return $this->date_expiration && $this->date_expiration->isPast();
    }

    public function getEstValideAttribute(): bool
    {
        return !$this->utilise && !$this->est_expire;
    }

    public function getDescriptionTypeAttribute(): string
    {
        return match($this->type) {
            'pourcentage' => "{$this->valeur}% de réduction",
            'montant_fixe' => "{$this->valeur} FCFA de réduction",
            default => 'Type inconnu'
        };
    }

    public function utiliser(): void
    {
        if (!$this->est_valide) {
            throw new \Exception('Ce coupon n\'est pas valide');
        }

        $this->update([
            'utilise' => true,
            'date_utilisation' => now()
        ]);
    }

    public function calculerReduction(float $montantTotal): float
    {
        if (!$this->est_valide) {
            return 0;
        }

        if ($montantTotal < $this->montant_minimum) {
            return 0;
        }

        return match($this->type) {
            'pourcentage' => $montantTotal * ($this->valeur / 100),
            'montant_fixe' => min($this->valeur, $montantTotal),
            default => 0
        };
    }

    // Scopes
    public function scopeValides($query)
    {
        return $query->where('utilise', false)
                    ->where(function($q) {
                        $q->whereNull('date_expiration')
                          ->orWhere('date_expiration', '>', now());
                    });
    }

    public function scopeExpires($query, $jours = 7)
    {
        return $query->where('utilise', false)
                    ->where('date_expiration', '<=', now()->addDays($jours))
                    ->where('date_expiration', '>', now());
    }

    public function scopeParClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }
}
