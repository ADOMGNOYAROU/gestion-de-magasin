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
        Schema::create('points_fidelite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('vente_id')->nullable()->constrained('ventes')->onDelete('cascade');
            $table->enum('type_operation', ['gain', 'utilisation', 'expiration', 'bonus']);
            $table->decimal('montant_achat', 10, 2)->default(0);
            $table->integer('points_gagnes')->default(0);
            $table->integer('points_utilises')->default(0);
            $table->text('description')->nullable();
            $table->timestamp('date_transaction')->useCurrent();
            $table->timestamps();

            $table->index(['client_id', 'date_transaction']);
            $table->index('type_operation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_fidelite');
    }
};
