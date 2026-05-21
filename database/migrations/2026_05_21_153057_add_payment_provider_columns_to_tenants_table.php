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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('payment_provider')->default('stripe')->after('plan');
            $table->string('paystack_customer_code')->nullable()->after('stripe_id');
            $table->string('flutterwave_customer_id')->nullable()->after('paystack_customer_code');
            $table->string('paystack_subscription_code')->nullable()->after('subscription_ends_at');
            $table->string('flutterwave_subscription_id')->nullable()->after('paystack_subscription_code');
            $table->string('paystack_email_token')->nullable()->after('flutterwave_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'payment_provider',
                'paystack_customer_code',
                'flutterwave_customer_id',
                'paystack_subscription_code',
                'flutterwave_subscription_id',
                'paystack_email_token',
            ]);
        });
    }
};
