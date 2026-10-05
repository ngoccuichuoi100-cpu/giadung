<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Voucher;
use App\Models\UserVoucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVoucherTest extends TestCase
{
    use RefreshDatabase;

    protected function getAdmin()
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_view_vouchers_index()
    {
        $admin = $this->getAdmin();

        Voucher::create([
            'code' => 'TESTCODE1',
            'name' => 'Ưu đãi thử nghiệm 1',
            'type' => 'fixed',
            'acquisition_type' => 'public',
            'value' => 50000,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.vouchers.index'));
        $response->assertStatus(200);
        $response->assertSee('TESTCODE1');
        $response->assertSee('Ưu đãi thử nghiệm 1');
    }

    public function test_admin_can_create_new_voucher()
    {
        $admin = $this->getAdmin();

        $response = $this->actingAs($admin)->post(route('admin.vouchers.store'), [
            'code' => 'FREESHIP100',
            'name' => 'Miễn phí ship toàn quốc',
            'description' => 'Áp dụng cho mọi đơn hàng',
            'type' => 'free_shipping',
            'acquisition_type' => 'new_member',
            'value' => 30000,
            'max_discount_amount' => 30000,
            'min_order_value' => 100000,
            'max_uses' => 200,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(30)->format('Y-m-d H:i:s'),
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.vouchers.index'));
        $this->assertDatabaseHas('vouchers', [
            'code' => 'FREESHIP100',
            'type' => 'free_shipping',
            'acquisition_type' => 'new_member',
        ]);
    }

    public function test_admin_can_update_voucher()
    {
        $admin = $this->getAdmin();

        $voucher = Voucher::create([
            'code' => 'OLDCODE',
            'name' => 'Tên cũ',
            'type' => 'percent',
            'acquisition_type' => 'public',
            'value' => 10,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.vouchers.update', $voucher->id), [
            'code' => 'NEWCODE',
            'name' => 'Tên mới cập nhật',
            'type' => 'percent',
            'acquisition_type' => 'game_reward',
            'value' => 15,
            'max_discount_amount' => 50000,
            'min_order_value' => 200000,
            'max_uses' => 50,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addMonth()->format('Y-m-d H:i:s'),
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.vouchers.index'));
        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'code' => 'NEWCODE',
            'name' => 'Tên mới cập nhật',
            'acquisition_type' => 'game_reward',
        ]);
    }

    public function test_admin_can_toggle_voucher_status()
    {
        $admin = $this->getAdmin();

        $voucher = Voucher::create([
            'code' => 'TOGGLEME',
            'name' => 'Thử bật tắt',
            'type' => 'fixed',
            'value' => 10000,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.vouchers.toggle-status', $voucher->id));
        $response->assertRedirect();
        $this->assertEquals('inactive', $voucher->fresh()->status);
    }

    public function test_admin_can_delete_voucher()
    {
        $admin = $this->getAdmin();

        $voucher = Voucher::create([
            'code' => 'DELETEME',
            'name' => 'Sắp bị xóa',
            'type' => 'fixed',
            'value' => 10000,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.vouchers.destroy', $voucher->id));
        $response->assertRedirect(route('admin.vouchers.index'));
        $this->assertSoftDeleted('vouchers', ['id' => $voucher->id]);
    }

    public function test_admin_can_give_voucher_to_customers()
    {
        $admin = $this->getAdmin();

        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);

        $voucher = Voucher::create([
            'code' => 'GIFTME',
            'name' => 'Quà tặng bí mật',
            'type' => 'fixed',
            'value' => 20000,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.vouchers.give', $voucher->id), [
            'target_type' => 'selected',
            'customer_ids' => [$customer1->id, $customer2->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_vouchers', [
            'user_id' => $customer1->id,
            'voucher_id' => $voucher->id,
        ]);
        $this->assertDatabaseHas('user_vouchers', [
            'user_id' => $customer2->id,
            'voucher_id' => $voucher->id,
        ]);
    }

    public function test_free_shipping_voucher_deducts_shipping_fee_not_product_price()
    {
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);

        $voucher = Voucher::create([
            'code' => 'SHIPFREE',
            'name' => 'Freeship đơn nhỏ',
            'type' => 'free_shipping',
            'acquisition_type' => 'public',
            'value' => 30000,
            'min_order_value' => 0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        \App\Models\UserVoucher::create([
            'user_id' => $customer->id,
            'voucher_id' => $voucher->id,
        ]);

        // Customer has a cart with subtotal = 2,000 VND (< 500,000 VND so shipping fee is 30,000 VND)
        $response = $this->actingAs($customer)->postJson('/api/coupons/apply', [
            'code' => 'SHIPFREE',
            'subtotal' => 2000,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'code' => 'SHIPFREE',
            'type' => 'free_shipping',
            'product_discount' => 0,
            'shipping_discount' => 30000,
            'original_shipping_fee' => 30000,
            'shipping_fee' => 0,
            'subtotal' => 2000,
            'total_amount' => 2000, // Product 2,000 + Shipping (30,000 - 30,000) = 2,000! NOT 30,000!
        ]);
    }

    public function test_order_placement_with_free_shipping_voucher_keeps_product_price()
    {
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $cart = \App\Models\Cart::create(['user_id' => $customer->id]);

        $category = \App\Models\Category::create(['name' => 'Test Cat', 'slug' => 'test-cat', 'status' => 'active']);
        $product = \App\Models\Product::create([
            'category_id' => $category->id,
            'code' => 'P-MINI',
            'name' => 'Sản phẩm thử 2k',
            'slug' => 'san-pham-thu-2k',
            'price' => 2000,
            'stock_quantity' => 10,
            'unit' => 'cái',
            'status' => 'active',
        ]);

        $cartItem = \App\Models\CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 2000,
        ]);

        $voucher = Voucher::create([
            'code' => 'FREESHIP2K',
            'name' => 'Freeship 30k',
            'type' => 'free_shipping',
            'acquisition_type' => 'public',
            'value' => 30000,
            'min_order_value' => 0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        \App\Models\UserVoucher::create([
            'user_id' => $customer->id,
            'voucher_id' => $voucher->id,
        ]);

        $orderService = resolve(\App\Services\OrderService::class);
        $order = $orderService->placeOrder(
            $customer,
            [
                'receiver_name' => 'Khách hàng',
                'receiver_phone' => '0901234567',
                'receiver_email' => 'khach@test.com',
                'province' => 'Hà Nội',
                'district' => 'Cầu Giấy',
                'ward' => 'Dịch Vọng',
                'address' => '123 Cầu Giấy',
            ],
            $cart->fresh()->items,
            'cod',
            [
                'coupon_code' => 'FREESHIP2K',
            ]
        );

        $this->assertEquals(2000, (float)$order->subtotal);
        $this->assertEquals(0, (float)$order->discount_amount); // Product discount is 0!
        $this->assertEquals(30000, (float)$order->shipping_fee); // Standard shipping fee was 30,000
        $this->assertEquals(30000, (float)$order->shipping_discount); // Shipping discount is 30,000
        $this->assertEquals(2000, (float)$order->total_amount); // Total amount = 2,000 + 30,000 - 30,000 = 2,000!
    }
}
