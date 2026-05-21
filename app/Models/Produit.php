<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['nom', 'categorie', 'description', 'prix_achat', 'prix_vente', 'statut'];

    public function stockMagasins()
    {
        return $this->hasMany(StockMagasin::class);
    }

    public function stockBoutiques()
    {
        return $this->hasMany(StockBoutique::class);
    }

    public function entreesStock()
    {
        return $this->hasMany(EntreeStock::class);
    }

    public function transferts()
    {
        return $this->hasMany(Transfert::class);
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function getStockTotalMagasinAttribute()
    {
        return $this->stockMagasins()->sum('quantite');
    }

    public function getStockTotalBoutiqueAttribute()
    {
        return $this->stockBoutiques()->sum('quantite');
    }

    // Accesseurs pour les marges
    public function getMargeAttribute()
    {
        return $this->prix_vente - $this->prix_achat;
    }

    public function getStockCreditAttribute()
    {
        $stockCredit = 0;
        $credits = \DB::table('credits')
            ->join('ventes', 'credits.vente_id', '=', 'ventes.id')
            ->join('vente_produits', 'ventes.id', '=', 'vente_produits.vente_id')
            ->where('vente_produits.produit_id', $this->id)
            ->where('credits.remaining_balance', '>', 0)
            ->select('credits.total_amount', 'credits.remaining_balance', 'vente_produits.quantite')
            ->get();

        foreach ($credits as $credit) {
            $fraction = $credit->remaining_balance / $credit->total_amount;
            $stockCredit += $fraction * $credit->quantite;
        }

        return round($stockCredit);
    }

    public function getMargePercentageAttribute()
    {
        if ($this->prix_achat > 0) {
            return round((($this->prix_vente - $this->prix_achat) / $this->prix_achat) * 100, 1);
        }
        return 0;
    }
}
