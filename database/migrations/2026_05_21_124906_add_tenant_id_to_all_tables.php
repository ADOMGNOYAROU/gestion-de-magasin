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
        // Tables principales
        $tables = [
            'users',
            'magasins',
            'boutiques',
            'produits',
            'stock_magasins',
            'stock_boutiques',
            'fournisseurs',
            'partenaires',
            'entree_stocks',
            'transferts',
            'ventes',
            'vente_produits',
            'clients',
            'credits',
            'credit_payments',
            'orders',
            'order_items',
            'payment_methods',
            'cash_register_sessions',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    $blueprint->foreignId('tenant_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'magasins',
            'boutiques',
            'produits',
            'stock_magasins',
            'stock_boutiques',
            'fournisseurs',
            'partenaires',
            'entree_stocks',
            'transferts',
            'ventes',
            'vente_produits',
            'clients',
            'credits',
            'credit_payments',
            'orders',
            'order_items',
            'payment_methods',
            'cash_register_sessions',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['tenant_id']);
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};
