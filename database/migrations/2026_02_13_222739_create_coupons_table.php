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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->enum('type', ['pourcentage', 'montant_fixe']);
            $table->decimal('valeur', 8, 2);
            $table->decimal('montant_minimum', 10, 2)->default(0);
            $table->timestamp('date_expiration')->nullable();
            $table->boolean('utilise')->default(false);
            $table->timestamp('date_utilisation')->nullable();
            $table->text('conditions_utilisation')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'utilise']);
            $table->index('code');
            $table->index('date_expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
