<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entree_stocks', function (Blueprint $table) {
            $table->foreignId('magasin_id')->nullable()->constrained('magasins')->onDelete('set null');
            $table->foreignId('partenaire_id')->nullable()->constrained('partenaires')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('entree_stocks', function (Blueprint $table) {
            $table->dropForeign(['magasin_id']);
            $table->dropForeign(['partenaire_id']);
            $table->dropColumn(['magasin_id', 'partenaire_id']);
        });
    }
};
