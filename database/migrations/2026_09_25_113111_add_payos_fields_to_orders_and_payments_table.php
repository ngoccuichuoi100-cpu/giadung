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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('payos_order_code')->nullable()->unique()->after('order_code');
            $table->text('payos_checkout_url')->nullable()->after('payment_method');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('payos_order_code')->nullable()->after('payment_code');
            $table->string('payos_payment_link_id')->nullable()->after('transaction_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payos_order_code', 'payos_payment_link_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payos_order_code', 'payos_checkout_url']);
        });
    }
};
