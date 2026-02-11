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
        Schema::create('cash_register_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendeur_id')->constrained('users')->onDelete('cascade'); // Vendeur qui gère la caisse
            $table->foreignId('boutique_id')->constrained('boutiques')->onDelete('cascade'); // Boutique concernée
            $table->decimal('montant_initial', 15, 2)->default(0); // Montant initial en caisse
            $table->decimal('montant_final', 15, 2)->nullable(); // Montant final compté
            $table->decimal('montant_theorique', 15, 2)->default(0); // Montant théorique calculé
            $table->decimal('ecart', 15, 2)->default(0); // Écart entre théorique et réel
            $table->timestamp('date_ouverture'); // Date d'ouverture de la session
            $table->timestamp('date_fermeture')->nullable(); // Date de fermeture
            $table->enum('status', ['ouverte', 'fermee', 'en_cours'])->default('ouverte'); // Statut de la session
            $table->text('notes')->nullable(); // Notes optionnelles
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_register_sessions');
    }
};
