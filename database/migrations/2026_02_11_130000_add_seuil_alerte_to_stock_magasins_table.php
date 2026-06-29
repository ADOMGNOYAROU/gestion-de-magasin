<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_magasins', function (Blueprint $table) {
            $table->integer('seuil_alerte')->default(10);
        });
    }

    public function down(): void
    {
        Schema::table('stock_magasins', function (Blueprint $table) {
            $table->dropColumn('seuil_alerte');
        });
    }
};
