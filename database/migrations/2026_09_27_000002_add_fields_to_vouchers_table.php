<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('vouchers', 'acquisition_type')) {
                $table->string('acquisition_type', 50)->default('public')->after('type'); // public, new_member, game_reward, referral, loyalty_points, special_gift
            }
            if (!Schema::hasColumn('vouchers', 'max_discount_amount')) {
                $table->decimal('max_discount_amount', 15, 2)->nullable()->after('value');
            }
            if (!Schema::hasColumn('vouchers', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
        });

        // Cập nhật acquisition_type cho các voucher mặc định hiện tại
        DB::table('vouchers')->where('code', 'FREE10')->update(['acquisition_type' => 'new_member', 'description' => 'Mã ưu đãi giảm 10% dành tặng khách hàng mới đăng ký tài khoản.']);
        DB::table('vouchers')->where('code', 'like', 'WHEEL%')->update(['acquisition_type' => 'game_reward', 'description' => 'Phần thưởng may mắn khi tham gia vòng quay trúng thưởng.']);
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            if (Schema::hasColumn('vouchers', 'acquisition_type')) {
                $table->dropColumn('acquisition_type');
            }
            if (Schema::hasColumn('vouchers', 'max_discount_amount')) {
                $table->dropColumn('max_discount_amount');
            }
            if (Schema::hasColumn('vouchers', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
