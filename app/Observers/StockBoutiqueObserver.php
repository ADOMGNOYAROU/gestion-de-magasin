<?php

namespace App\Observers;

use App\Models\StockBoutique;
use App\Models\AlerteStock;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;

class StockBoutiqueObserver
{
    /**
     * Handle the StockBoutique "created" event.
     */
    public function created(StockBoutique $stockBoutique): void
    {
        $this->checkStockAlert($stockBoutique->produit_id);
    }

    /**
     * Handle the StockBoutique "updated" event.
     */
    public function updated(StockBoutique $stockBoutique): void
    {
        $this->checkStockAlert($stockBoutique->produit_id);
    }

    /**
     * Handle the StockBoutique "deleted" event.
     */
    public function deleted(StockBoutique $stockBoutique): void
    {
        //
    }

    /**
     * Handle the StockBoutique "restored" event.
     */
    public function restored(StockBoutique $stockBoutique): void
    {
        //
    }

    /**
     * Handle the StockBoutique "force deleted" event.
     */
    public function forceDeleted(StockBoutique $stockBoutique): void
    {
        //
    }

    private function checkStockAlert($produitId)
    {
        $totalStock = DB::table('stock_magasins')->where('produit_id', $produitId)->sum('quantite') +
                      DB::table('stock_boutiques')->where('produit_id', $produitId)->sum('quantite');

        $seuilMin = 250;

        if ($totalStock > $seuilMin) {
            $niveau = 'normal';
        } elseif ($totalStock > 0) {
            $niveau = 'faible';
        } else {
            $niveau = 'rupture';
        }

        // Check if active alert exists for this produit and niveau
        $existing = AlerteStock::where('produit_id', $produitId)->where('niveau', $niveau)->where('statut', 'active')->exists();

        if (!$existing) {
            $produit = Produit::find($produitId);
            $message = "Alerte stock pour {$produit->nom}: {$totalStock} unités disponibles.";

            AlerteStock::create([
                'produit_id' => $produitId,
                'niveau' => $niveau,
                'message' => $message,
                'statut' => 'active'
            ]);
        }
    }
}
