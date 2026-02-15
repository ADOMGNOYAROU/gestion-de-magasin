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
        Schema::create('commande_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Qui a créé la commande
            $table->foreignId('magasin_id')->nullable()->constrained('magasins')->onDelete('set null');
            $table->string('numero_commande')->unique(); // Numéro unique de commande
            $table->date('date_commande');
            $table->enum('status', ['brouillon', 'envoyee', 'confirmee', 'en_cours_livraison', 'livree', 'annulee'])->default('brouillon');
            $table->date('date_livraison_prevue')->nullable();
            $table->date('date_livraison_reelle')->nullable();
            $table->decimal('total_ht', 15, 2)->default(0);
            $table->decimal('tva', 15, 2)->default(0);
            $table->decimal('total_ttc', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('conditions_paiement')->nullable(); // Conditions de paiement
            $table->timestamps();

            $table->index(['fournisseur_id', 'status']);
            $table->index(['status', 'date_commande']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_fournisseurs');
    }
};
