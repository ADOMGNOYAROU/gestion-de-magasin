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
        Schema::table('entrees_stock', function (Blueprint $table) {
            // Modifier les colonnes de montant pour accepter de grandes valeurs
            $table->decimal('prix_unitaire', 20, 2)->change();
            $table->decimal('prix_achat', 20, 2)->change();
            $table->decimal('montant_total', 20, 2)->change();
        });

        Schema::table('ventes', function (Blueprint $table) {
            // Modifier les colonnes de montant pour accepter de grandes valeurs
            $table->decimal('prix_vente', 20, 2)->change();
            $table->decimal('montant_total', 20, 2)->change();
        });

        Schema::table('produits', function (Blueprint $table) {
            // Modifier les colonnes de prix pour accepter de grandes valeurs
            $table->decimal('prix_achat', 20, 2)->change();
            $table->decimal('prix_vente', 20, 2)->change();
        });

        Schema::table('credits', function (Blueprint $table) {
            // Modifier les colonnes de montant pour accepter de grandes valeurs
            $table->decimal('montant_total', 20, 2)->change();
            $table->decimal('montant_rembourse', 20, 2)->change();
            $table->decimal('montant_restant', 20, 2)->change();
        });

        Schema::table('transferts', function (Blueprint $table) {
            // Modifier les colonnes de montant pour accepter de grandes valeurs
            $table->decimal('montant_total', 20, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrees_stock', function (Blueprint $table) {
            $table->decimal('prix_unitaire', 10, 2)->change();
            $table->decimal('prix_achat', 10, 2)->change();
            $table->decimal('montant_total', 10, 2)->change();
        });

        Schema::table('ventes', function (Blueprint $table) {
            $table->decimal('prix_vente', 10, 2)->change();
            $table->decimal('montant_total', 10, 2)->change();
        });

        Schema::table('produits', function (Blueprint $table) {
            $table->decimal('prix_achat', 10, 2)->change();
            $table->decimal('prix_vente', 10, 2)->change();
        });

        Schema::table('credits', function (Blueprint $table) {
            $table->decimal('montant_total', 10, 2)->change();
            $table->decimal('montant_rembourse', 10, 2)->change();
            $table->decimal('montant_restant', 10, 2)->change();
        });

        Schema::table('transferts', function (Blueprint $table) {
            $table->decimal('montant_total', 10, 2)->change();
        });
    }
};
