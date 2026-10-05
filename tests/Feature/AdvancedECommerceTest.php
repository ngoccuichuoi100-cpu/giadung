<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Color;
use App\Models\Size;
use App\Models\Warranty;
use App\Models\Voucher;
use App\Models\Game;
use App\Models\GameReward;
use App\Models\RewardItem;
use App\Models\Friendship;
use App\Models\FriendBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedECommerceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test daily check-in streak points incrementation.
     */
    public function test_daily_checkin_streak_calculates_points()
    {
        $user = User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'userA@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'status' => 'active',
            'points' => 0
        ]);

        // First check-in
        $this->actingAs($user)->post('/games/checkin');
        $user->refresh();
        $this->assertEquals(12, $user->points); // 10 + 2 = 12 points

        // Second check-in on the same day should fail
        $response = $this->actingAs($user)->post('/games/checkin');
        $response->assertSessionHas('error');
        $user->refresh();
        $this->assertEquals(12, $user->points);
    }

    /**
     * Test lucky wheel spin back-end probability check.
     */
    public function test_lucky_wheel_spin_deducts_points_and_returns_correct_response()
    {
        $user = User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'userA@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'status' => 'active',
            'points' => 50
        ]);

        $game = Game::create([
            'name' => 'Vòng quay may mắn',
            'slug' => 'vong-quay-may-man',
            'description' => 'Mô tả',
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
            'type' => 'lucky_wheel',
        ]);

        // Seed rewards
        GameReward::create([
            'game_id' => $game->id,
            'name' => 'Cộng 10 điểm',
            'type' => 'points',
            'value' => '10',
            'probability' => 100.0 // guarantee win for testing
        ]);

        // First spin of the day is FREE (doesn't deduct points)
        $response = $this->actingAs($user)->postJson('/games/spin');
        $response->assertJson([
            'success' => true,
            'reward_name' => 'Cộng 10 điểm',
        ]);
        $user->refresh();
        $this->assertEquals(60, $user->points); // 50 + 10 = 60 points

        // Second spin of the day should cost 20 points
        $response = $this->actingAs($user)->postJson('/games/spin');
        $response->assertJson([
            'success' => true,
        ]);
        $user->refresh();
        $this->assertEquals(50, $user->points); // 60 - 20 (cost) + 10 (prize) = 50 points
    }

    /**
     * Test point cashing for vouchers and gifts.
     */
    public function test_point_redemption_checks_funds_and_stock()
    {
        $user = User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'userA@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'status' => 'active',
            'points' => 50 // insufficient points
        ]);

        $gift = RewardItem::create([
            'name' => 'Ấm đun nước',
            'description' => 'Ấm siêu tốc cao cấp',
            'points_required' => 100,
            'stock_quantity' => 5,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth()
        ]);

        // Attempt redeem with insufficient points
        $response = $this->actingAs($user)->post('/games/redeem', [
            'reward_item_id' => $gift->id
        ]);
        $response->assertSessionHas('error', 'Bạn không có đủ điểm thưởng để đổi phần quà này.');

        // Add points
        $user->update(['points' => 150]);

        // Redeem successfully
        $response = $this->actingAs($user)->post('/games/redeem', [
            'reward_item_id' => $gift->id
        ]);
        $response->assertSessionHas('success');

        $user->refresh();
        $gift->refresh();
        $this->assertEquals(50, $user->points); // 150 - 100 = 50
        $this->assertEquals(4, $gift->stock_quantity); // stock decremented
    }

    /**
     * Test friendships workflow (Send request, accept, block).
     */
    public function test_friendships_workflow()
    {
        $userA = User::create(['name' => 'User A', 'email' => 'a@gmail.com', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'active']);
        $userB = User::create(['name' => 'User B', 'email' => 'b@gmail.com', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'active']);

        // A sends request to B
        $response = $this->actingAs($userA)->post('/friends/send', ['friend_id' => $userB->id]);
        $response->assertRedirect();
        $this->assertDatabaseHas('friendships', [
            'user_id' => $userA->id,
            'friend_id' => $userB->id,
            'status' => 'pending'
        ]);

        // B accepts request from A
        $response = $this->actingAs($userB)->post('/friends/' . $userA->id . '/accept');
        $response->assertRedirect();
        $this->assertDatabaseHas('friendships', [
            'user_id' => $userA->id,
            'friend_id' => $userB->id,
            'status' => 'accepted'
        ]);

        // A blocks B
        $response = $this->actingAs($userA)->post('/friends/block', ['user_id' => $userB->id]);
        $response->assertRedirect();
        $this->assertDatabaseHas('friendships', [
            'user_id' => $userA->id,
            'friend_id' => $userB->id,
            'status' => 'blocked'
        ]);
    }

    /**
     * Test merging session cart to database upon login.
     */
    public function test_merge_session_cart_to_db_on_login()
    {
        $category = Category::create(['name' => 'Chảo', 'slug' => 'chao', 'status' => 'active']);
        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'NC-03',
            'name' => 'Chảo Tefal',
            'slug' => 'chao-tefal',
            'price' => 350000,
            'stock_quantity' => 10,
            'unit' => 'cái',
            'status' => 'active'
        ]);

        // Add to guest session cart
        $response = $this->withSession([
            'cart' => [
                $product->id . '_' => [
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'color_id' => null,
                    'size_id' => null,
                    'warranty_id' => null,
                    'quantity' => 2,
                    'price' => 350000
                ]
            ]
        ])->postJson('/register', [
            'username' => 'newuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'policy' => '1',
        ]);

        $this->postJson('/register/profile', [
            'name' => 'New User',
            'age' => 30,
            'gender' => 'Nam',
            'address' => 'Hà Nội',
            'email' => 'newuser@gmail.com',
            'phone' => '0912121212',
        ]);
        $user = User::where('email', 'newuser@gmail.com')->first();
        $this->assertNotNull($user);

        // Session cart should be merged to DB cart items
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }
}
