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
            if (!Schema::hasColumn('entrees_stock', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('entrees_stock', 'produit_id')) {
                $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            }
            if (!Schema::hasColumn('entrees_stock', 'magasin_id')) {
                $table->foreignId('magasin_id')->constrained('magasins')->onDelete('cascade');
            }
            if (!Schema::hasColumn('entrees_stock', 'fournisseur_id')) {
                $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->onDelete('cascade');
            }
            if (!Schema::hasColumn('entrees_stock', 'partenaire_id')) {
                $table->foreignId('partenaire_id')->nullable()->constrained('partenaires')->onDelete('cascade');
            }
            if (!Schema::hasColumn('entrees_stock', 'quantite')) {
                $table->integer('quantite');
            }
            if (!Schema::hasColumn('entrees_stock', 'prix_unitaire')) {
                $table->decimal('prix_unitaire', 10, 2);
            }
            if (!Schema::hasColumn('entrees_stock', 'montant_total')) {
                $table->decimal('montant_total', 10, 2);
            }
            if (!Schema::hasColumn('entrees_stock', 'date_entree')) {
                $table->date('date_entree');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrees_stock', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'produit_id', 'magasin_id', 'fournisseur_id', 'partenaire_id', 'quantite', 'prix_unitaire', 'montant_total', 'date_entree']);
        });
    }
};
