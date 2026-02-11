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
        Schema::table('ventes', function (Blueprint $table) {
            // Supprimer les colonnes pour vente produit unique
            $table->dropForeign(['produit_id']);
            $table->dropColumn(['produit_id', 'quantite', 'prix_vente']);

            // Ajouter les colonnes pour le système de caisse
            $table->foreignId('session_caisse_id')->nullable()->constrained('cash_register_sessions')->onDelete('set null');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->decimal('montant_recu', 15, 2)->default(0); // Montant reçu du client
            $table->decimal('monnaie', 15, 2)->default(0); // Monnaie à rendre
            $table->decimal('montant_total', 15, 2)->change(); // Garder mais ajuster la précision

            // Ajouter des colonnes optionnelles
            $table->string('numero_ticket')->nullable()->unique(); // Numéro unique du ticket
            $table->enum('status', ['en_cours', 'terminee', 'annulee'])->default('terminee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            // Restaurer les colonnes supprimées
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
            $table->integer('quantite');
            $table->decimal('prix_vente', 10, 2);

            // Supprimer les nouvelles colonnes
            $table->dropForeign(['session_caisse_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn([
                'session_caisse_id',
                'payment_method_id',
                'montant_recu',
                'monnaie',
                'numero_ticket',
                'status'
            ]);

            $table->decimal('montant_total', 10, 2)->change();
        });
    }
};
