<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vente_produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained('ventes')->onDelete('cascade'); // Référence à la vente
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade'); // Produit vendu
            $table->integer('quantite'); // Quantité vendue
            $table->decimal('prix_unitaire', 10, 2); // Prix unitaire au moment de la vente
            $table->decimal('remise', 10, 2)->default(0); // Remise appliquée (montant)
            $table->decimal('remise_pourcentage', 5, 2)->default(0); // Remise en pourcentage
            $table->decimal('sous_total', 15, 2); // Sous-total après remise
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vente_produits');
    }
};
