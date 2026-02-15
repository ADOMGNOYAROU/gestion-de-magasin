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
        Schema::create('ligne_commande_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_fournisseur_id')->constrained('commande_fournisseurs')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->integer('quantite_commandee');
            $table->integer('quantite_livree')->default(0);
            $table->decimal('prix_unitaire_ht', 10, 2);
            $table->decimal('tva_taux', 5, 2)->default(18.00); // TVA par défaut 18%
            $table->decimal('sous_total_ht', 12, 2);
            $table->decimal('sous_total_ttc', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['commande_fournisseur_id', 'produit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_commande_fournisseurs');
    }
};
