<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'customer_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('customer_code', 20)->nullable()->unique()->after('id');
            });
        }

        // Backfill customer_code cho các khách hàng hiện tại
        $characters = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $customers = DB::table('users')->where('role', 'customer')->whereNull('customer_code')->get();

        foreach ($customers as $cust) {
            do {
                $randomStr = '';
                for ($i = 0; $i < 6; $i++) {
                    $randomStr .= $characters[random_int(0, strlen($characters) - 1)];
                }
                $code = 'KH-' . $randomStr;
            } while (DB::table('users')->where('customer_code', $code)->exists());

            DB::table('users')->where('id', $cust->id)->update(['customer_code' => $code]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'customer_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('customer_code');
            });
        }
    }
};
