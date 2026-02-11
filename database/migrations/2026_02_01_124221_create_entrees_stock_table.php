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
        Schema::create('entrees_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
            $table->foreignId('magasin_id')->constrained()->onDelete('cascade');
            $table->foreignId('fournisseur_id')->constrained()->onDelete('cascade');
            $table->integer('quantite');
            $table->decimal('prix_achat', 10, 2);
            $table->date('date_entree');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrees_stock');
    }
};