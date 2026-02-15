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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('telephone')->nullable();
            $table->date('date_naissance')->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->default('Togo');
            $table->string('code_postal')->nullable();
            $table->enum('sexe', ['M', 'F'])->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamp('date_inscription')->useCurrent();
            $table->decimal('solde_points', 10, 2)->default(0);
            $table->decimal('total_achats', 15, 2)->default(0);
            $table->timestamp('derniere_vente')->nullable();
            $table->timestamps();

            $table->index(['nom', 'prenom']);
            $table->index('email');
            $table->index('telephone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
