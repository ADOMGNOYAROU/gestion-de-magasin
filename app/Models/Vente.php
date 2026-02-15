<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vente extends Model
{
    protected $fillable = [
        'boutique_id',
        'user_id',
        'client_id',
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
                $vente->numero_ticket = static::generateNumeroTicket();
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

    public function client()
    {
        return $this->belongsTo(Client::class);
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
    public static function generateNumeroTicket()
    {
        $date = now()->format('Ymd');
        $lastTicket = static::where('numero_ticket', 'like', "TKT-{$date}-%")
                           ->orderBy('numero_ticket', 'desc')
                           ->first();

        $sequence = 1;
        if ($lastTicket) {
            // Extraire le numéro séquentiel du dernier ticket
            $parts = explode('-', $lastTicket->numero_ticket);
            if (count($parts) >= 3) {
                $sequence = (int)$parts[2] + 1;
            }
        }

        return sprintf('TKT-%s-%04d', $date, $sequence);
    }
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

        // Calculer et attribuer les points de fidélité
        if ($this->client) {
            $this->attribuerPointsFidelite();
        }

        // Mettre à jour la session de caisse
        if ($this->sessionCaisse) {
            $this->sessionCaisse->calculerMontantTheorique();
        }

        // Mettre à jour les statistiques du client
        if ($this->client) {
            $this->client->mettreAJourStatistiques();
        }
    }

    private function attribuerPointsFidelite()
    {
        if (!$this->client || $this->montant_total <= 0) {
            return;
        }

        // Calcul des points : 1 point par tranche de 1000 FCFA
        $pointsGagnes = floor($this->montant_total / 1000);

        if ($pointsGagnes > 0) {
            $this->client->ajouterPoints(
                $pointsGagnes,
                "Achat - Ticket {$this->numero_ticket}",
                $this
            );

            // Vérifier si le client atteint un seuil pour un coupon bonus
            $this->verifierSeuilCouponBonus();
        }
    }

    private function verifierSeuilCouponBonus()
    {
        $seuils = [
            100 => ['type' => 'montant_fixe', 'valeur' => 5000, 'jours' => 30], // 100 points = 5000 FCFA
            250 => ['type' => 'pourcentage', 'valeur' => 10, 'jours' => 60],    // 250 points = 10%
            500 => ['type' => 'montant_fixe', 'valeur' => 15000, 'jours' => 90], // 500 points = 15000 FCFA
        ];

        foreach ($seuils as $seuil => $coupon) {
            if ($this->client->solde_points >= $seuil) {
                // Vérifier si le client a déjà reçu ce type de coupon récemment
                $couponRecent = $this->client->coupons()
                    ->where('valeur', $coupon['valeur'])
                    ->where('type', $coupon['type'])
                    ->where('date_expiration', '>', now())
                    ->exists();

                if (!$couponRecent) {
                    $this->client->genererCoupon(
                        $coupon['type'],
                        $coupon['valeur'],
                        $coupon['jours']
                    );
                }
            }
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
