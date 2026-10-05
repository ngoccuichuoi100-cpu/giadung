<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ECommerceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test user registration and login.
     */
    public function test_register_and_login(): void
    {
        // 1. Test Register Step 1
        $registerData1 = [
            'username' => 'testuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'policy' => '1',
        ];

        $response = $this->postJson('/register', $registerData1);
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('users', ['username' => 'testuser']);

        // 2. Test Register Step 2
        $registerData2 = [
            'name' => 'Nguyễn Văn Test',
            'age' => 25,
            'gender' => 'Nam',
            'address' => '123 Đường Test, Hà Nội',
            'email' => 'testuser@gmail.com',
            'phone' => '0987654321',
        ];

        $response = $this->postJson('/register/profile', $registerData2);
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'testuser@gmail.com',
            'phone' => '0987654321',
            'age' => 25,
            'gender' => 'Nam',
        ]);

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // 3. Test Login with Username
        $loginDataUsername = [
            'login' => 'testuser',
            'password' => 'password123',
        ];

        $response = $this->post('/login', $loginDataUsername);
        $response->assertRedirect('/');
        $this->assertAuthenticated();

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // 4. Test Login with Email
        $loginDataEmail = [
            'login' => 'testuser@gmail.com',
            'password' => 'password123',
        ];

        $response = $this->post('/login', $loginDataEmail);
        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    /**
     * Test guests and standard customers cannot access the admin area.
     */
    public function test_guest_and_customer_cannot_access_admin(): void
    {
        // Guest
        $response = $this->get('/admin');
        $response->assertRedirect('/login'); // redirected by auth middleware to login

        // Customer
        $customer = User::create([
            'name' => 'Khách hàng',
            'email' => 'cust@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $response = $this->actingAs($customer)->get('/admin');
        $response->assertRedirect('/');
    }

    /**
     * Test that admin can perform CRUD on categories.
     */
    public function test_admin_can_manage_category(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Access dashboard
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);

        // Create Category
        $catData = [
            'name' => 'Nồi Cơm Điện',
            'slug' => 'noi-com-dien',
            'description' => 'Mô tả nồi cơm',
            'status' => 'active',
        ];
        $response = $this->actingAs($admin)->post('/admin/categories', $catData);
        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', ['slug' => 'noi-com-dien']);
    }

    /**
     * Test adding to cart checks stock limits.
     */
    public function test_cannot_add_more_than_stock(): void
    {
        $customer = User::create([
            'name' => 'Khách hàng',
            'email' => 'cust@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $category = Category::create([
            'name' => 'Nồi cơm điện',
            'slug' => 'noi-com-dien',
            'status' => 'active',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'NCD-01',
            'name' => 'Nồi Cơm Cuckoo',
            'slug' => 'noi-com-cuckoo',
            'price' => 1000000,
            'stock_quantity' => 5,
            'unit' => 'cái',
            'status' => 'active',
        ]);

        // Act as customer and try to add 6 items to cart (stock is 5)
        $response = $this->actingAs($customer)->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 6,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('cart_items', ['product_id' => $product->id]);

        // Add 3 items (success)
        $response = $this->actingAs($customer)->from('/cart')->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
        $response->assertRedirect('/cart');
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 3]);

        // Add 3 more items (would make total 6, should fail)
        $response = $this->actingAs($customer)->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
        $response->assertSessionHas('error');
    }

    /**
     * Test successful ordering workflow.
     */
    public function test_place_order_success_decrements_stock_and_clears_cart(): void
    {
        $customer = User::create([
            'name' => 'Khách hàng A',
            'email' => 'userA@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $category = Category::create([
            'name' => 'Ấm siêu tốc',
            'slug' => 'am-sieu-toc',
            'status' => 'active',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'AST-01',
            'name' => 'Ấm siêu tốc Philips',
            'slug' => 'am-sieu-toc-philips',
            'price' => 500000,
            'stock_quantity' => 10,
            'unit' => 'cái',
            'status' => 'active',
        ]);

        $province = \App\Models\Province::create(['code' => '01', 'name' => 'Hà Nội']);
        $district = \App\Models\District::create(['code' => '005', 'province_code' => '01', 'name' => 'Quận Cầu Giấy']);
        $ward = \App\Models\Ward::create(['code' => '00151', 'district_code' => '005', 'name' => 'Phường Dịch Vọng']);

        // Setup Cart
        $cart = Cart::create(['user_id' => $customer->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Checkout data
        $checkoutData = [
            'receiver_name' => 'Nguyễn Văn Nhận',
            'receiver_phone' => '0912345678',
            'receiver_email' => 'userA@gmail.com',
            'province_code' => '01',
            'district_code' => '005',
            'ward_code' => '00151',
            'address' => 'Số 1 Trần Thái Tông',
            'note' => 'Giao buổi tối',
            'payment_method' => 'cod',
            'receipt_type' => 'paper',
        ];

        $response = $this->actingAs($customer)->post('/checkout/place-order', $checkoutData);
        
        // Assert redirected to order show
        $order = Order::first();
        $this->assertNotNull($order);
        $response->assertRedirect('/orders/' . $order->id);

        // Assert stock decremented
        $product->refresh();
        $this->assertEquals(8, $product->stock_quantity);

        // Assert cart is cleared
        $this->assertDatabaseMissing('cart_items', ['cart_id' => $cart->id]);
    }

    /**
     * Test customer cannot see other customer's order.
     */
    public function test_customer_cannot_view_other_users_order(): void
    {
        $user1 = User::create(['name' => 'User 1', 'email' => 'u1@gmail.com', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'active']);
        $user2 = User::create(['name' => 'User 2', 'email' => 'u2@gmail.com', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'active']);

        $order = Order::create([
            'user_id' => $user1->id,
            'order_code' => 'GD-123',
            'receiver_name' => 'User 1',
            'receiver_phone' => '123',
            'receiver_email' => 'u1@gmail.com',
            'province' => 'A', 'district' => 'B', 'ward' => 'C', 'address' => 'D',
            'subtotal' => 100, 'shipping_fee' => 0, 'total_amount' => 100,
        ]);

        // User 2 trying to view User 1's order
        $response = $this->actingAs($user2)->get('/orders/' . $order->id);
        $response->assertStatus(403);

        // User 1 viewing own order
        $response = $this->actingAs($user1)->get('/orders/' . $order->id);
        $response->assertStatus(200);
    }

    /**
     * Test order cancellation restores stock.
     */
    public function test_cancel_order_restores_stock(): void
    {
        $customer = User::create(['name' => 'User', 'email' => 'u@gmail.com', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'active']);
        
        $category = Category::create(['name' => 'C', 'slug' => 'c', 'status' => 'active']);
        $product = Product::create([
            'category_id' => $category->id, 'code' => 'P1', 'name' => 'P1', 'slug' => 'p1',
            'price' => 100, 'stock_quantity' => 10, 'unit' => 'cái', 'status' => 'active'
        ]);

        // Create order directly
        $order = Order::create([
            'user_id' => $customer->id, 'order_code' => 'GD-999',
            'receiver_name' => 'User', 'receiver_phone' => '123', 'receiver_email' => 'u@gmail.com',
            'province' => 'A', 'district' => 'B', 'ward' => 'C', 'address' => 'D',
            'subtotal' => 200, 'shipping_fee' => 0, 'total_amount' => 200,
            'order_status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id, 'product_id' => $product->id,
            'product_code' => 'P1', 'product_name' => 'P1', 'price' => 100, 'quantity' => 2, 'total' => 200
        ]);

        // Simulate order placement has already decremented stock to 8
        $product->decrement('stock_quantity', 2);

        // Cancel order
        $response = $this->actingAs($customer)->post('/orders/' . $order->id . '/cancel');
        $response->assertRedirect();
        
        // Assert status is cancelled
        $order->refresh();
        $this->assertEquals('cancelled', $order->order_status);

        // Assert stock is restored to 10
        $product->refresh();
        $this->assertEquals(10, $product->stock_quantity);
    }

    /**
     * Test historical price remains same when product price is updated.
     */
    public function test_historical_price_retained_when_product_price_changes(): void
    {
        $customer = User::create(['name' => 'User', 'email' => 'u@gmail.com', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'active']);
        $category = Category::create(['name' => 'C', 'slug' => 'c', 'status' => 'active']);
        $product = Product::create([
            'category_id' => $category->id, 'code' => 'P1', 'name' => 'P1', 'slug' => 'p1',
            'price' => 500000, 'stock_quantity' => 10, 'unit' => 'cái', 'status' => 'active'
        ]);

        // Create order
        $order = Order::create([
            'user_id' => $customer->id, 'order_code' => 'GD-888',
            'receiver_name' => 'User', 'receiver_phone' => '123', 'receiver_email' => 'u@gmail.com',
            'province' => 'A', 'district' => 'B', 'ward' => 'C', 'address' => 'D',
            'subtotal' => 500000, 'shipping_fee' => 0, 'total_amount' => 500000,
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id, 'product_id' => $product->id,
            'product_code' => 'P1', 'product_name' => 'P1', 'price' => 500000, 'quantity' => 1, 'total' => 500000
        ]);

        // Update product price
        $product->update(['price' => 700000]);

        // Eager load order item and check its price
        $item->refresh();
        $this->assertEquals(500000, $item->price);
    }

    /**
     * Test location cascading APIs return correct nested data.
     */
    public function test_location_cascading_data(): void
    {
        $customer = User::create(['name' => 'User', 'email' => 'u@gmail.com', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'active']);

        $province = \App\Models\Province::create(['code' => '01', 'name' => 'Hà Nội']);
        $district = \App\Models\District::create(['code' => '005', 'province_code' => '01', 'name' => 'Quận Cầu Giấy']);
        $ward = \App\Models\Ward::create(['code' => '00151', 'district_code' => '005', 'name' => 'Phường Dịch Vọng']);

        // Check provinces
        $response = $this->actingAs($customer)->get('/api/locations/provinces');
        $response->assertStatus(200)->assertJsonFragment(['code' => '01', 'name' => 'Hà Nội']);

        // Check districts
        $response = $this->actingAs($customer)->get('/api/locations/provinces/01/districts');
        $response->assertStatus(200)->assertJsonFragment(['code' => '005', 'name' => 'Quận Cầu Giấy']);

        // Check wards
        $response = $this->actingAs($customer)->get('/api/locations/districts/005/wards');
        $response->assertStatus(200)->assertJsonFragment(['code' => '00151', 'name' => 'Phường Dịch Vọng']);
    }

    /**
     * Test applying coupons for discounts.
     */
    public function test_apply_coupon_success_and_failures(): void
    {
        $customer = User::create(['name' => 'User', 'email' => 'u@gmail.com', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'active']);

        // Active coupon
        $couponActive = \App\Models\Coupon::create([
            'code' => 'GIADUNG100',
            'type' => 'fixed',
            'value' => 100000,
            'min_order_value' => 500000,
            'max_uses' => 10,
            'uses' => 0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        // Inactive coupon
        $couponInactive = \App\Models\Coupon::create([
            'code' => 'OFF50',
            'type' => 'fixed',
            'value' => 50000,
            'min_order_value' => 100000,
            'max_uses' => 10,
            'uses' => 0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'inactive',
        ]);

        // Apply invalid (inactive)
        $response = $this->actingAs($customer)->postJson('/api/coupons/apply', [
            'code' => 'OFF50',
            'subtotal' => 200000
        ]);
        $response->assertStatus(200)->assertJson(['success' => false]);

        // Apply with subtotal too low
        $response = $this->actingAs($customer)->postJson('/api/coupons/apply', [
            'code' => 'GIADUNG100',
            'subtotal' => 400000
        ]);
        $response->assertStatus(200)->assertJson(['success' => false]);

        // Apply success
        $response = $this->actingAs($customer)->postJson('/api/coupons/apply', [
            'code' => 'GIADUNG100',
            'subtotal' => 600000
        ]);
        $response->assertStatus(200)->assertJson([
            'success' => true,
            'discount_amount' => 100000,
            'total_amount' => 500000, // 600K - 100K = 500K subtotal
        ]);
    }

    /**
     * Test checkout with bank transfer (VietQR) and installment methods.
     */
    public function test_checkout_advanced_methods(): void
    {
        $customer = User::create(['name' => 'User', 'email' => 'u@gmail.com', 'phone' => '0987654321', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'active']);
        $category = Category::create(['name' => 'C', 'slug' => 'c', 'status' => 'active']);
        $product = Product::create([
            'category_id' => $category->id, 'code' => 'P1', 'name' => 'P1', 'slug' => 'p1',
            'price' => 600000, 'stock_quantity' => 10, 'unit' => 'cái', 'status' => 'active'
        ]);

        $province = \App\Models\Province::create(['code' => '01', 'name' => 'Hà Nội']);
        $district = \App\Models\District::create(['code' => '005', 'province_code' => '01', 'name' => 'Quận Cầu Giấy']);
        $ward = \App\Models\Ward::create(['code' => '00151', 'district_code' => '005', 'name' => 'Phường Dịch Vọng']);

        $coupon = \App\Models\Coupon::create([
            'code' => 'GIADUNG100',
            'type' => 'fixed',
            'value' => 100000,
            'min_order_value' => 500000,
            'max_uses' => 10,
            'uses' => 0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        // Cart items setup
        $cart = Cart::create(['user_id' => $customer->id]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        // 1. Checkout via Bank Transfer with Coupon and Electronic Receipt
        $checkoutData = [
            'receiver_name' => 'Nguyễn Văn Nhận',
            'receiver_phone' => '0987654321',
            'receiver_email' => 'u@gmail.com',
            'province_code' => '01',
            'district_code' => '005',
            'ward_code' => '00151',
            'address' => 'Số 1 Trần Thái Tông',
            'note' => 'Giao tối',
            'payment_method' => 'bank_transfer',
            'coupon_code' => 'GIADUNG100',
            'receipt_type' => 'electronic',
            'receipt_email' => 'invoice@gmail.com'
        ];

        $response = $this->actingAs($customer)->post('/checkout/place-order', $checkoutData);
        
        $order = Order::first();
        $this->assertNotNull($order);
        $response->assertRedirect('/orders/' . $order->id);

        $this->assertEquals('bank_transfer', $order->payment_method);
        $this->assertEquals('GIADUNG100', $order->coupon_code);
        $this->assertEquals(100000, $order->discount_amount);
        $this->assertEquals(500000, $order->total_amount); // 600K - 100K = 500K (free shipping >= 500K)
        $this->assertEquals('electronic', $order->receipt_type);
        $this->assertEquals('invoice@gmail.com', $order->receipt_email);
        $this->assertNotNull($order->est_delivery_start);
        $this->assertNotNull($order->est_delivery_end);

        // Verify coupon uses incremented
        $coupon->refresh();
        $this->assertEquals(1, $coupon->uses);

        // 2. Checkout via Installment (6 months)
        // Reset cart for second order
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);
        
        $checkoutData2 = [
            'receiver_name' => 'Nguyễn Văn Trả Góp',
            'receiver_phone' => '0987654321',
            'receiver_email' => 'u@gmail.com',
            'province_code' => '01',
            'district_code' => '005',
            'ward_code' => '00151',
            'address' => 'Số 1 Trần Thái Tông',
            'payment_method' => 'installment',
            'installment_months' => 6,
            'receipt_type' => 'paper',
        ];

        $response2 = $this->actingAs($customer)->post('/checkout/place-order', $checkoutData2);
        
        $order2 = Order::orderBy('id', 'desc')->first();
        $this->assertNotNull($order2);
        $response2->assertRedirect('/orders/' . $order2->id);

        $this->assertEquals('installment', $order2->payment_method);
        $this->assertEquals(6, $order2->installment_months);
        $this->assertEquals(100000, $order2->installment_monthly_amount); // 600K / 6 = 100K
        $this->assertEquals('paper', $order2->receipt_type);
    }
}
