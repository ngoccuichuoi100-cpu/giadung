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
            $table->string('username')->nullable()->unique()->after('id');
            $table->integer('age')->nullable()->after('name');
            $table->string('gender')->nullable()->after('age');
            $table->string('friend_invite_code')->nullable()->after('address');
            $table->string('referral_code')->nullable()->unique()->after('friend_invite_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'age', 'gender', 'friend_invite_code', 'referral_code']);
        });
    }
};
