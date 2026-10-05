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
        Schema::table('users', function (Blueprint $table) {
            $table->string('province_code')->nullable()->after('address');
            $table->string('district_code')->nullable()->after('province_code');
            $table->string('ward_code')->nullable()->after('district_code');
            $table->string('detail_address')->nullable()->after('ward_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['province_code', 'district_code', 'ward_code', 'detail_address']);
        });
    }
};
