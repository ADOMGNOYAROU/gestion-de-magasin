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
        Schema::create('mobile_money_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vente_id')->nullable()->constrained('ventes')->onDelete('set null');
            $table->string('identifier')->unique();
            $table->string('tx_reference')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('phone_number');
            $table->enum('network', ['FLOOZ', 'TMONEY']);
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending'); // pending | success | failed | expired | cancelled
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_money_payments');
    }
};
