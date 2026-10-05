<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cho phép email có thể null
        DB::statement("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL;");

        // 2. Thêm cột nơi cư trú và nơi làm việc
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'residence_address')) {
                $table->text('residence_address')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'workplace')) {
                $table->string('workplace')->nullable()->after('residence_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'residence_address')) {
                $table->dropColumn('residence_address');
            }
            if (Schema::hasColumn('users', 'workplace')) {
                $table->dropColumn('workplace');
            }
        });

        DB::statement("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NOT NULL;");
    }
};
