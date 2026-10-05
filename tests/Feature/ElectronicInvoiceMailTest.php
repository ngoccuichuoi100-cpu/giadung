<?php

namespace Tests\Feature;

use App\Mail\ElectronicInvoiceMail;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ElectronicInvoiceMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_electronic_invoice_mail_renders_properly()
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'customer_code' => 'KH-TEST01',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'GD-20260927-TEST01',
            'receiver_name' => 'Nguyễn Thị Khách',
            'receiver_phone' => '0987654321',
            'receiver_email' => 'khach@test.com',
            'address' => '123 Phố Huế',
            'province' => 'Hà Nội',
            'district' => 'Hai Bà Trưng',
            'ward' => 'Phố Huế',
            'subtotal' => 500000,
            'shipping_fee' => 0,
            'total_amount' => 500000,
            'payment_method' => 'payos',
            'receipt_type' => 'electronic',
            'receipt_email' => 'ereceipt@test.com',
            'payment_status' => 'paid',
            'order_status' => 'confirmed',
        ]);

        $mailable = new ElectronicInvoiceMail($order);

        $mailable->assertHasSubject('Hóa đơn điện tử & Biên lai thanh toán thành công - Đơn hàng #GD-20260927-TEST01 | GiaDụngPro');
        $mailable->assertSeeInHtml('Nguyễn Thị Khách');
        $mailable->assertSeeInHtml('GD-20260927-TEST01');
        $mailable->assertSeeInHtml('KH-TEST01');
        $mailable->assertSeeInHtml('ereceipt@test.com');
        $mailable->assertSeeInHtml('500.000 ₫');
    }

    public function test_order_service_sends_electronic_invoice_when_requested()
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'GD-20260927-TEST02',
            'receiver_name' => 'Trần Văn Mua',
            'receiver_phone' => '0912345678',
            'receiver_email' => 'tranvanmua@test.com',
            'address' => '456 Lê Lợi',
            'province' => 'TP.HCM',
            'district' => 'Quận 1',
            'ward' => 'Bến Nghé',
            'subtotal' => 200000,
            'shipping_fee' => 30000,
            'total_amount' => 230000,
            'payment_method' => 'payos',
            'receipt_type' => 'electronic',
            'receipt_email' => 'invoice_buyer@test.com',
            'payment_status' => 'paid',
            'order_status' => 'confirmed',
        ]);

        $orderService = resolve(OrderService::class);
        $result = $orderService->sendElectronicInvoice($order);

        $this->assertTrue($result);
        $this->assertNotNull($order->fresh()->receipt_sent_at);

        Mail::assertSent(ElectronicInvoiceMail::class, function ($mail) {
            return $mail->hasTo('invoice_buyer@test.com') &&
                   $mail->order->order_code === 'GD-20260927-TEST02';
        });
    }

    public function test_order_service_does_not_send_if_paper_receipt()
    {
        Mail::fake();

        $user = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'GD-20260927-TEST03',
            'receiver_name' => 'Lê Giấy',
            'receiver_phone' => '0911223344',
            'receiver_email' => 'paper@test.com',
            'address' => '789 Trần Hưng Đạo',
            'province' => 'Đà Nẵng',
            'district' => 'Hải Châu',
            'ward' => 'Thạch Thang',
            'subtotal' => 100000,
            'shipping_fee' => 30000,
            'total_amount' => 130000,
            'payment_method' => 'cod',
            'receipt_type' => 'paper',
            'payment_status' => 'paid',
            'order_status' => 'confirmed',
        ]);

        $orderService = resolve(OrderService::class);
        $result = $orderService->sendElectronicInvoice($order);

        $this->assertFalse($result);
        Mail::assertNothingSent();
    }
}
