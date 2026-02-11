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
        Schema::table('entree_stocks', function (Blueprint $table) {
            $table->foreignId('magasin_id')->nullable()->after('produit_id')->constrained('magasins')->onDelete('set null');
            $table->foreignId('partenaire_id')->nullable()->after('fournisseur_id')->constrained('partenaires')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entree_stocks', function (Blueprint $table) {
            $table->dropForeign(['magasin_id']);
            $table->dropColumn('magasin_id');

            $table->dropForeign(['partenaire_id']);
            $table->dropColumn('partenaire_id');
        });
    }
};
