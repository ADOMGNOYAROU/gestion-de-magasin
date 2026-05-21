<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fournisseur_id',
        'user_id',
        'numero_commande',
        'date_commande',
        'date_livraison_prevue',
        'status',
        'montant_total',
        'notes',
    ];

    protected $casts = [
        'date_commande' => 'date',
        'date_livraison_prevue' => 'date',
        'montant_total' => 'decimal:2',
    ];

    // Relations
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeEnCours($query)
    {
        return $query->where('status', 'en_cours');
    }

    public function scopeLivree($query)
    {
        return $query->where('status', 'livree');
    }

    public function scopeAnnulee($query)
    {
        return $query->where('status', 'annulee');
    }

    // Helper methods
    public static function generateNumeroCommande()
    {
        $date = now()->format('Ymd');
        $lastOrder = static::where('numero_commande', 'like', "CMD-{$date}-%")
                           ->orderBy('numero_commande', 'desc')
                           ->first();

        $sequence = 1;
        if ($lastOrder) {
            $parts = explode('-', $lastOrder->numero_commande);
            if (count($parts) >= 3) {
                $sequence = (int)$parts[2] + 1;
            }
        }

        return sprintf('CMD-%s-%04d', $date, $sequence);
    }

    public function calculerTotal()
    {
        $this->montant_total = $this->orderItems()->sum('sous_total');
        $this->save();
        return $this->montant_total;
    }

    public function ajouterProduit(Produit $produit, $quantite = 1, $prixUnitaire = null)
    {
        $prix = $prixUnitaire ?? $produit->prix_achat;

        $orderItem = $this->orderItems()->create([
            'produit_id' => $produit->id,
            'quantite' => $quantite,
            'prix_unitaire' => $prix,
            'sous_total' => $prix * $quantite,
        ]);

        $this->calculerTotal();
        return $orderItem;
    }
}
