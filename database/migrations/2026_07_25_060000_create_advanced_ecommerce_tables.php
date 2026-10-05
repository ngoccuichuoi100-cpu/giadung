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
        // 1. brands
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
        });

        // 2. brand_partnerships
        Schema::create('brand_partnerships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('url')->nullable();
            $table->string('type')->default('current'); // current, past
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 3. colors
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('hex_code')->nullable();
            $table->timestamps();
        });

        // 4. sizes
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 5. warranties
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->string('name');
            $table->integer('duration_months');
            $table->decimal('price', 15, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 6. product_variants
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('color_id')->nullable()->constrained('colors')->onDelete('set null');
            $table->foreignId('size_id')->nullable()->constrained('sizes')->onDelete('set null');
            $table->string('sku')->unique()->nullable();
            $table->decimal('price', 15, 2)->nullable(); // Overrides product price if set
            $table->decimal('sale_price', 15, 2)->nullable(); // Overrides product sale price if set
            $table->integer('stock_quantity')->default(0);
            $table->string('thumbnail')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'status']);
        });

        // 7. product_reviews
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->integer('rating'); // 1 to 5
            $table->text('comment')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('approved'); // pending, approved, rejected
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'status']);
            $table->index('user_id');
        });

        // 8. review_votes
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('review_id')->constrained('product_reviews')->onDelete('cascade');
            $table->string('vote_type')->default('upvote'); // upvote
            $table->timestamps();

            $table->unique(['user_id', 'review_id']);
        });

        // 9. wishlists
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
        });

        // 10. news_categories
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 11. news
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('news_categories')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('type'); // new_stock, new_release, promotion, store_news, event
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('published'); // draft, published
            $table->dateTime('published_at')->nullable();
            $table->json('related_product_ids')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });

        // 12. notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('link')->nullable();
            $table->string('type'); // order, voucher, discount, game, system
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });

        // 13. vouchers
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type')->default('fixed'); // fixed, percent
            $table->decimal('value', 15, 2);
            $table->decimal('min_order_value', 15, 2)->default(0);
            $table->integer('max_uses')->default(0);
            $table->integer('uses')->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes();
        });

        // 14. user_vouchers
        Schema::create('user_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'used_at']);
        });

        // 15. games
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->json('conditions')->nullable();
            $table->text('reward_info')->nullable();
            $table->string('status')->default('active');
            $table->string('type'); // checkin, lucky_wheel, even_odd, custom
            $table->timestamps();
        });

        // 16. game_rewards
        Schema::create('game_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // points, voucher, none
            $table->string('value')->nullable(); // amount of points or voucher code
            $table->decimal('probability', 8, 4); // probability percentage
            $table->integer('quantity_limit')->default(-1); // -1 = unlimited
            $table->integer('quantity_claimed')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 17. game_attempts
        Schema::create('game_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->foreignId('reward_id')->nullable()->constrained('game_rewards')->onDelete('set null');
            $table->text('result')->nullable();
            $table->integer('points_spent')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'game_id']);
        });

        // 18. daily_checkins
        Schema::create('daily_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('checkin_date');
            $table->integer('points_earned');
            $table->integer('streak')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'checkin_date']);
        });

        // 19. events
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 20. reward_items
        Schema::create('reward_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('points_required');
            $table->integer('stock_quantity')->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 21. reward_redemptions
        Schema::create('reward_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reward_item_id')->constrained('reward_items')->onDelete('cascade');
            $table->integer('points_spent');
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->timestamps();
        });

        // 22. point_transactions
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('amount'); // can be positive or negative
            $table->string('type'); // checkin, game_play, game_win, redeem, order_earn, order_refund, manual
            $table->string('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });

        // 23. reports
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('type'); // product, review, comment, system
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('reason');
            $table->text('content')->nullable();
            $table->string('status')->default('pending'); // pending, resolved, dismissed
            $table->timestamps();
        });

        // 24. friendships
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('friend_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, accepted, declined, blocked
            $table->timestamps();

            $table->unique(['user_id', 'friend_id']);
        });

        // 25. user_settings
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->unique();
            $table->string('theme')->default('light'); // light, dark
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('friend_system_enabled')->default(true);
            $table->timestamps();
        });

        // 26. product_views
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->timestamp('viewed_at')->useCurrent();
            $table->timestamps();

            $table->index('product_id');
        });

        // 27. Alter existing tables
        Schema::table('users', function (Blueprint $table) {
            $table->integer('points')->default(0)->after('status');
            $table->string('avatar')->nullable()->after('name');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->onDelete('set null');
            $table->boolean('is_domestic')->default(true)->after('is_featured');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('color_id')->nullable()->after('product_variant_id')->constrained('colors')->onDelete('set null');
            $table->foreignId('size_id')->nullable()->after('color_id')->constrained('sizes')->onDelete('set null');
            $table->foreignId('warranty_id')->nullable()->after('size_id')->constrained('warranties')->onDelete('set null');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->onDelete('set null');
            $table->foreignId('color_id')->nullable()->after('product_variant_id')->constrained('colors')->onDelete('set null');
            $table->foreignId('size_id')->nullable()->after('color_id')->constrained('sizes')->onDelete('set null');
            $table->foreignId('warranty_id')->nullable()->after('size_id')->constrained('warranties')->onDelete('set null');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('coupon_code')->constrained('vouchers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn('voucher_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropForeign(['color_id']);
            $table->dropForeign(['size_id']);
            $table->dropForeign(['warranty_id']);
            $table->dropColumn(['product_variant_id', 'color_id', 'size_id', 'warranty_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropForeign(['color_id']);
            $table->dropForeign(['size_id']);
            $table->dropForeign(['warranty_id']);
            $table->dropColumn(['product_variant_id', 'color_id', 'size_id', 'warranty_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['brand_id', 'is_domestic']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['points', 'avatar']);
        });

        Schema::dropIfExists('product_views');
        Schema::dropIfExists('user_settings');
        Schema::dropIfExists('friendships');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('reward_redemptions');
        Schema::dropIfExists('reward_items');
        Schema::dropIfExists('events');
        Schema::dropIfExists('daily_checkins');
        Schema::dropIfExists('game_attempts');
        Schema::dropIfExists('game_rewards');
        Schema::dropIfExists('games');
        Schema::dropIfExists('user_vouchers');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_categories');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('review_votes');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('warranties');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('brand_partnerships');
        Schema::dropIfExists('brands');
    }
};
