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
        Schema::table('magasins', function (Blueprint $table) {
            if (Schema::hasColumn('magasins', 'adresse')) {
                $table->string('adresse')->nullable()->change();
            }
            if (Schema::hasColumn('magasins', 'telephone')) {
                $table->string('telephone')->nullable()->change();
            }
            if (Schema::hasColumn('magasins', 'email')) {
                $table->string('email')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magasins', function (Blueprint $table) {
            if (Schema::hasColumn('magasins', 'adresse')) {
                $table->string('adresse')->nullable(false)->change();
            }
            if (Schema::hasColumn('magasins', 'telephone')) {
                $table->string('telephone')->nullable(false)->change();
            }
            if (Schema::hasColumn('magasins', 'email')) {
                $table->string('email')->nullable(false)->change();
            }
        });
    }
};
