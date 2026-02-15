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
        Schema::create('preferences_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('categorie_preferee')->nullable();
            $table->foreignId('produit_prefere_id')->nullable()->constrained('produits')->onDelete('set null');
            $table->enum('frequence_achat', ['quotidien', 'hebdomadaire', 'mensuel', 'occasionnel'])->nullable();
            $table->decimal('budget_moyen', 10, 2)->nullable();
            $table->enum('canal_prefere', ['magasin', 'boutique', 'online'])->default('magasin');
            $table->boolean('notifications_email')->default(true);
            $table->boolean('notifications_sms')->default(false);
            $table->json('tags')->nullable(); // Pour stocker des tags personnalisés
            $table->timestamps();

            $table->unique('client_id');
            $table->index('categorie_preferee');
            $table->index('frequence_achat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferences_clients');
    }
};
