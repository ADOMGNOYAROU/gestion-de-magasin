<?php

namespace App\Observers;

use App\Models\StockMagasin;
use App\Models\AlerteStock;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;

class StockMagasinObserver
{
    /**
     * Handle the StockMagasin "created" event.
     */
    public function created(StockMagasin $stockMagasin): void
    {
        $this->checkStockAlert($stockMagasin->produit_id);
    }

    /**
     * Handle the StockMagasin "updated" event.
     */
    public function updated(StockMagasin $stockMagasin): void
    {
        $this->checkStockAlert($stockMagasin->produit_id);
    }

    /**
     * Handle the StockMagasin "deleted" event.
     */
    public function deleted(StockMagasin $stockMagasin): void
    {
        //
    }

    /**
     * Handle the StockMagasin "restored" event.
     */
    public function restored(StockMagasin $stockMagasin): void
    {
        //
    }

    /**
     * Handle the StockMagasin "force deleted" event.
     */
    public function forceDeleted(StockMagasin $stockMagasin): void
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
