<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transferts', function (Blueprint $table) {
            $table->dropForeign(['magasin_source_id']);
            $table->dropForeign(['magasin_destination_id']);
            $table->dropColumn(['magasin_source_id', 'magasin_destination_id', 'date_transfert']);

            $table->foreignId('magasin_id')->constrained('magasins')->onDelete('cascade');
            $table->foreignId('boutique_id')->constrained('boutiques')->onDelete('cascade');
            $table->date('date');
        });
    }

    public function down(): void
    {
        Schema::table('transferts', function (Blueprint $table) {
            $table->dropForeign(['magasin_id']);
            $table->dropForeign(['boutique_id']);
            $table->dropColumn(['magasin_id', 'boutique_id', 'date']);

            $table->foreignId('magasin_source_id')->constrained('magasins')->onDelete('cascade');
            $table->foreignId('magasin_destination_id')->constrained('magasins')->onDelete('cascade');
            $table->date('date_transfert');
        });
    }
};
