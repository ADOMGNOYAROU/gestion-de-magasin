<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vente extends Model
{
    protected $fillable = [
        'boutique_id',
        'user_id',
        'session_caisse_id',
        'payment_method_id',
        'montant_total',
        'montant_recu',
        'monnaie',
        'numero_ticket',
        'status',
        'date_vente',
        'notes',
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'montant_recu' => 'decimal:2',
        'monnaie' => 'decimal:2',
        'date_vente' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($vente) {
            if (!$vente->numero_ticket) {
                $vente->numero_ticket = 'TKT-' . strtoupper(Str::random(8));
            }
        });
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    public function sessionCaisse()
    {
        return $this->belongsTo(CashRegisterSession::class, 'session_caisse_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function venteProduits()
    {
        return $this->hasMany(VenteProduit::class);
    }

    // Scopes
    public function scopeTerminee($query)
    {
        return $query->where('status', 'terminee');
    }

    public function scopeAnnulee($query)
    {
        return $query->where('status', 'annulee');
    }

    public function scopeEnCours($query)
    {
        return $query->where('status', 'en_cours');
    }

    public function scopeForBoutique($query, $boutiqueId)
    {
        return $query->where('boutique_id', $boutiqueId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_caisse_id', $sessionId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date_vente', $date);
    }

    // Helper methods
    public function isTerminee()
    {
        return $this->status === 'terminee';
    }

    public function isAnnulee()
    {
        return $this->status === 'annulee';
    }

    public function isEnCours()
    {
        return $this->status === 'en_cours';
    }

    public function calculerTotal()
    {
        $this->montant_total = $this->venteProduits()->sum('sous_total');
        $this->save();
        return $this->montant_total;
    }

    public function calculerMonnaie()
    {
        if ($this->montant_recu > 0) {
            $this->monnaie = $this->montant_recu - $this->montant_total;
            $this->save();
        }
        return $this->monnaie;
    }

    public function ajouterProduit(Produit $produit, $quantite = 1, $prixUnitaire = null, $remise = 0)
    {
        $prix = $prixUnitaire ?? $produit->prix_vente;

        $venteProduit = $this->venteProduits()->create([
            'produit_id' => $produit->id,
            'quantite' => $quantite,
            'prix_unitaire' => $prix,
            'remise' => $remise,
            'remise_pourcentage' => $prix > 0 ? ($remise / $prix) * 100 : 0,
        ]);

        $venteProduit->calculerSousTotal();
        $this->calculerTotal();

        return $venteProduit;
    }

    public function annuler()
    {
        // Remettre les stocks
        foreach ($this->venteProduits as $vp) {
            $stock = StockBoutique::where('produit_id', $vp->produit_id)
                                 ->where('boutique_id', $this->boutique_id)
                                 ->first();
            if ($stock) {
                $stock->quantite += $vp->quantite;
                $stock->save();
            }
        }

        $this->status = 'annulee';
        $this->save();
    }

    public function finaliser()
    {
        $this->status = 'terminee';
        $this->save();

        // Mettre à jour la session de caisse
        if ($this->sessionCaisse) {
            $this->sessionCaisse->calculerMontantTheorique();
        }
    }

    // Accesseurs
    public function getTotalProduitsAttribute()
    {
        return $this->venteProduits()->sum('quantite');
    }

    public function getBeneficeTotalAttribute()
    {
        $benefice = 0;
        foreach ($this->venteProduits as $vp) {
            $prixAchat = $vp->produit->prix_achat ?? 0;
            $benefice += ($vp->prix_unitaire - $prixAchat) * $vp->quantite - $vp->remise;
        }
        return $benefice;
    }
}
