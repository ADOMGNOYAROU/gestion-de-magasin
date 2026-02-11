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
        Schema::table('transferts', function (Blueprint $table) {
            if (Schema::hasColumn('transferts', 'magasin_source_id')) {
                $table->dropForeign(['magasin_source_id']);
                $table->dropColumn('magasin_source_id');
            }

            if (Schema::hasColumn('transferts', 'magasin_destination_id')) {
                $table->dropForeign(['magasin_destination_id']);
                $table->dropColumn('magasin_destination_id');
            }

            if (Schema::hasColumn('transferts', 'date_transfert')) {
                $table->dropColumn('date_transfert');
            }

            if (!Schema::hasColumn('transferts', 'magasin_id')) {
                $table->foreignId('magasin_id')->after('produit_id')->constrained('magasins')->onDelete('cascade');
            }

            if (!Schema::hasColumn('transferts', 'boutique_id')) {
                $table->foreignId('boutique_id')->after('magasin_id')->constrained('boutiques')->onDelete('cascade');
            }

            if (!Schema::hasColumn('transferts', 'date')) {
                $table->date('date')->after('quantite');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transferts', function (Blueprint $table) {
            if (Schema::hasColumn('transferts', 'boutique_id')) {
                $table->dropForeign(['boutique_id']);
                $table->dropColumn('boutique_id');
            }

            if (Schema::hasColumn('transferts', 'magasin_id')) {
                $table->dropForeign(['magasin_id']);
                $table->dropColumn('magasin_id');
            }

            if (Schema::hasColumn('transferts', 'date')) {
                $table->dropColumn('date');
            }

            if (!Schema::hasColumn('transferts', 'magasin_source_id')) {
                $table->foreignId('magasin_source_id')->after('produit_id')->constrained('magasins')->onDelete('cascade');
            }

            if (!Schema::hasColumn('transferts', 'magasin_destination_id')) {
                $table->foreignId('magasin_destination_id')->after('magasin_source_id')->constrained('magasins')->onDelete('cascade');
            }

            if (!Schema::hasColumn('transferts', 'date_transfert')) {
                $table->date('date_transfert')->after('quantite');
            }
        });
    }
};
