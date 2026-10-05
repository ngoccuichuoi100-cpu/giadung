<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use App\Models\ProductReview;

class FullE2EPurchasingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_end_to_end_purchasing_and_ordering_flow()
    {
        // 1. Setup Seed Data
        $admin = User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@giadung.com',
            'role' => 'admin',
            'status' => 'active',
            'password' => bcrypt('123'),
        ]);

        $customer = User::factory()->create([
            'username' => 'khachhang',
            'email' => 'khachhang@giadung.com',
            'role' => 'customer',
            'status' => 'active',
            'points' => 500,
            'password' => bcrypt('123'),
        ]);

        $category = Category::create(['name' => 'Nồi Lẩu Điện', 'slug' => 'noi-lau-dien', 'status' => 'active']);
        $brand = Brand::create(['name' => 'Sunhouse', 'slug' => 'sunhouse', 'status' => 'active']);

        $province = Province::create(['code' => '79', 'name' => 'TP. Hồ Chí Minh']);
        $district = District::create(['code' => '760', 'province_code' => '79', 'name' => 'Quận 1']);
        $ward = Ward::create(['code' => '26734', 'district_code' => '760', 'name' => 'Phường Bến Nghé']);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'code' => 'SHD4527',
            'name' => 'Nồi lẩu điện đa năng Sunhouse SHD4527',
            'slug' => 'noi-lau-dien-sunhouse-shd4527',
            'price' => 850000,
            'sale_price' => 720000,
            'stock_quantity' => 10,
            'unit' => 'cái',
            'status' => 'active',
            'is_domestic' => true
        ]);

        $coupon = Coupon::create([
            'code' => 'GIAM50K',
            'type' => 'fixed',
            'value' => 50000,
            'min_order_value' => 500000,
            'status' => 'active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(10),
        ]);

        // --- STEP A: Quick View API Test ---
        $qvResponse = $this->get(route('products.quick-view', $product->id));
        $qvResponse->assertStatus(200);
        $qvResponse->assertJson([
            'success' => true,
            'id' => $product->id,
            'name' => 'Nồi lẩu điện đa năng Sunhouse SHD4527',
            'brand' => 'Sunhouse'
        ]);

        // --- STEP B: Customer Adds Item to DB Cart ---
        $this->actingAs($customer);
        $cart = Cart::firstOrCreate(['user_id' => $customer->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        // --- STEP C: Place Order via Checkout ---
        $checkoutData = [
            'receiver_name' => 'Nguyen Van A',
            'receiver_phone' => '0987654321',
            'receiver_email' => 'khachhang@giadung.com',
            'province_code' => '79',
            'district_code' => '760',
            'ward_code' => '26734',
            'address' => '123 Le Loi, Quan 1',
            'payment_method' => 'cod',
            'receipt_type' => 'paper',
            'note' => 'Giao trong giờ hành chính',
        ];

        $orderResponse = $this->post(route('checkout.place-order'), $checkoutData);
        $orderResponse->assertSessionHasNoErrors();
        $orderResponse->assertRedirect();

        // Verify Order created in DB & Stock Decremented
        $order = Order::where('user_id', $customer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->order_status);
        $this->assertEquals('cod', $order->payment_method);
        
        // Stock decremented by 2 (10 - 2 = 8)
        $product->refresh();
        $this->assertEquals(8, $product->stock_quantity);

        // --- STEP D: Admin Reviews & Completes Order ---
        $this->actingAs($admin);
        $updateStatusResponse = $this->put(route('admin.orders.update-status', $order->id), [
            'order_status' => 'completed',
            'payment_status' => 'paid'
        ]);
        
        $order->refresh();
        $this->assertEquals('completed', $order->order_status);
        $this->assertEquals('paid', $order->payment_status);

        // --- STEP E: Customer Writes Product Review ---
        $this->actingAs($customer);
        $reviewResponse = $this->post(route('products.reviews.store', $product->id), [
            'rating' => 5,
            'comment' => 'Nồi lẩu điện xài rất mượt, đun sôi nhanh!',
        ]);

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'user_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Nồi lẩu điện xài rất mượt, đun sôi nhanh!'
        ]);
    }
}
