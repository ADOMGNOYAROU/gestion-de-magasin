<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointFidelite extends Model
{
    protected $table = 'points_fidelite';
    protected $fillable = [
        'client_id',
        'vente_id',
        'type_operation',
        'montant_achat',
        'points_gagnes',
        'points_utilises',
        'description',
        'date_transaction'
    ];

    protected $casts = [
        'date_transaction' => 'datetime',
        'montant_achat' => 'decimal:2',
        'points_gagnes' => 'integer',
        'points_utilises' => 'integer'
    ];

    // Relations
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class);
    }

    // Scopes
    public function scopeGains($query)
    {
        return $query->where('type_operation', 'gain');
    }

    public function scopeUtilisations($query)
    {
        return $query->where('type_operation', 'utilisation');
    }

    public function scopeParClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeRecent($query, $jours = 30)
    {
        return $query->where('date_transaction', '>=', now()->subDays($jours));
    }

    // Méthodes utilitaires
    public function getPointsNetAttribute(): int
    {
        return $this->points_gagnes - $this->points_utilises;
    }

    public function getDescriptionCompleteAttribute(): string
    {
        $description = $this->description ?? '';

        if ($this->vente) {
            $description .= " (Vente #{$this->vente->numero_ticket})";
        }

        return $description;
    }
}
