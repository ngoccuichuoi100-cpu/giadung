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
            $table->string('province_code')->nullable()->after('province');
            $table->string('district_code')->nullable()->after('district');
            $table->string('ward_code')->nullable()->after('ward');
            $table->string('coupon_code')->nullable()->after('note');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('coupon_code');
            $table->integer('installment_months')->nullable()->after('payment_method');
            $table->decimal('installment_monthly_amount', 15, 2)->nullable()->after('installment_months');
            $table->string('receipt_type')->default('paper')->after('installment_monthly_amount'); // 'paper', 'electronic'
            $table->string('receipt_email')->nullable()->after('receipt_type');
            $table->date('est_delivery_start')->nullable()->after('receipt_email');
            $table->date('est_delivery_end')->nullable()->after('est_delivery_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'province_code',
                'district_code',
                'ward_code',
                'coupon_code',
                'discount_amount',
                'installment_months',
                'installment_monthly_amount',
                'receipt_type',
                'receipt_email',
                'est_delivery_start',
                'est_delivery_end',
            ]);
        });
    }
};
