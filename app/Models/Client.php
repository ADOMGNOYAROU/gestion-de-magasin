<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'date_naissance',
        'adresse',
        'ville',
        'pays',
        'code_postal',
        'sexe',
        'statut',
        'solde_points',
        'total_achats',
        'derniere_vente'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_inscription' => 'datetime',
        'derniere_vente' => 'datetime',
        'solde_points' => 'decimal:2',
        'total_achats' => 'decimal:2',
        'statut' => 'string'
    ];

    protected $attributes = [
        'pays' => 'Togo',
        'statut' => 'actif',
        'solde_points' => 0,
        'total_achats' => 0
    ];

    // Relations
    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }

    public function points(): HasMany
    {
        return $this->hasMany(PointFidelite::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(PreferenceClient::class);
    }

    // Méthodes utilitaires
    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance ? $this->date_naissance->age : null;
    }

    public function ajouterPoints(int $points, string $description = '', ?Vente $vente = null): void
    {
        $this->increment('solde_points', $points);

        PointFidelite::create([
            'client_id' => $this->id,
            'vente_id' => $vente?->id,
            'type_operation' => 'gain',
            'montant_achat' => $vente?->montant_total ?? 0,
            'points_gagnes' => $points,
            'description' => $description
        ]);
    }

    public function utiliserPoints(int $points, string $description = ''): void
    {
        if ($this->solde_points < $points) {
            throw new \Exception('Solde de points insuffisant');
        }

        $this->decrement('solde_points', $points);

        PointFidelite::create([
            'client_id' => $this->id,
            'type_operation' => 'utilisation',
            'points_utilises' => $points,
            'description' => $description
        ]);
    }

    public function genererCoupon(string $type, float $valeur, ?int $joursExpiration = null): Coupon
    {
        $code = 'COUPON-' . strtoupper(substr(md5(uniqid()), 0, 8));

        return $this->coupons()->create([
            'code' => $code,
            'type' => $type,
            'valeur' => $valeur,
            'date_expiration' => $joursExpiration ? now()->addDays($joursExpiration) : null,
            'conditions_utilisation' => 'Coupon personnel non transférable'
        ]);
    }

    public function mettreAJourStatistiques(): void
    {
        $stats = $this->ventes()
            ->selectRaw('COUNT(*) as nombre_ventes, SUM(montant_total) as total_achats, MAX(date_vente) as derniere_vente')
            ->first();

        $this->update([
            'total_achats' => $stats->total_achats ?? 0,
            'derniere_vente' => $stats->derniere_vente
        ]);
    }

    // Scopes
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeAvecEmail($query)
    {
        return $query->whereNotNull('email');
    }

    public function scopeAvecPoints($query, $minPoints = 0)
    {
        return $query->where('solde_points', '>=', $minPoints);
    }
}
