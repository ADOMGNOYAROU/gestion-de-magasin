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
        Schema::table('produits', function (Blueprint $table) {
            $table->string('statut')->default('actif');
            $table->decimal('prix_achat', 10, 2)->nullable();
            $table->decimal('prix_vente', 10, 2)->nullable();
            $table->renameColumn('prix', 'prix_ancien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->renameColumn('prix_ancien', 'prix');
            $table->dropColumn(['statut', 'prix_achat', 'prix_vente']);
        });
    }
};
