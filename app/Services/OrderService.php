<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warranty;
use App\Models\User;
use App\Models\Voucher;
use App\Models\UserVoucher;
use App\Models\PointTransaction;
use App\Mail\ElectronicInvoiceMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class OrderService
{
    /**
     * Place a new order.
     *
     * @param User $user
     * @param array $receiverInfo
     * @param \Illuminate\Support\Collection|\App\Models\CartItem[] $cartItems
     * @param string $paymentMethod
     * @param array $extraData
     * @return Order
     * @throws Exception
     */
    public function placeOrder(User $user, array $receiverInfo, $cartItems, string $paymentMethod = 'cod', array $extraData = []): Order
    {
        if ($cartItems->isEmpty()) {
            throw new Exception("Giỏ hàng trống.");
        }

        return DB::transaction(function () use ($user, $receiverInfo, $cartItems, $paymentMethod, $extraData) {
            $subtotal = 0;

            // Step 1: Calculate subtotal, verify prices and check stock
            foreach ($cartItems as $item) {
                $price = 0;

                if ($item->product_variant_id) {
                    $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                    if (!$variant || $variant->status !== 'active') {
                        throw new Exception("Biến thể của sản phẩm {$item->product->name} không còn tồn tại hoặc đã ngừng kinh doanh.");
                    }
                    if ($variant->stock_quantity < $item->quantity) {
                        throw new Exception("Biến thể của sản phẩm {$item->product->name} không đủ hàng trong kho (Còn lại: {$variant->stock_quantity}).");
                    }
                    $price = $variant->active_price;
                } else {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if (!$product || $product->status !== 'active') {
                        throw new Exception("Sản phẩm {$item->product->name} không còn tồn tại hoặc đã ngừng kinh doanh.");
                    }
                    if ($product->stock_quantity < $item->quantity) {
                        throw new Exception("Sản phẩm {$product->name} không đủ hàng trong kho (Còn lại: {$product->stock_quantity}).");
                    }
                    $price = $product->active_price;
                }

                // Add warranty cost if any
                if ($item->warranty_id) {
                    $warranty = Warranty::find($item->warranty_id);
                    if ($warranty && $warranty->status === 'active') {
                        $price += $warranty->price;
                    }
                }

                $subtotal += $price * $item->quantity;
            }

            // Coupon / Voucher Validation
            $productDiscount = 0.0;
            $shippingDiscount = 0.0;
            $coupon = null;
            $voucher = null;
            $voucherId = null;

            // Shipping fee baseline: Free for orders >= 500,000 VND, otherwise 30,000 VND
            $baseShippingFee = $subtotal >= 500000 ? 0.0 : 30000.0;

            if (!empty($extraData['coupon_code'])) {
                $code = strtoupper(trim($extraData['coupon_code']));
                $coupon = \App\Models\Coupon::where('code', $code)->first();
                if ($coupon) {
                    $validationError = $coupon->validateForOrder($subtotal);
                    if ($validationError) {
                        throw new Exception($validationError);
                    }
                    if ($coupon->isFreeShipping()) {
                        $shippingDiscount = (float)$coupon->calculateShippingDiscount($baseShippingFee);
                    } else {
                        $productDiscount = (float)$coupon->calculateProductDiscount($subtotal);
                    }
                } else {
                    // Fallback: Check if it is a personal Voucher
                    $voucher = Voucher::where('code', $code)->first();
                    if (!$voucher) {
                        throw new Exception("Mã giảm giá hoặc voucher không tồn tại.");
                    }
                    $userVoucher = UserVoucher::where('user_id', $user->id)
                        ->where('voucher_id', $voucher->id)
                        ->whereNull('used_at')
                        ->first();
                    if (!$userVoucher) {
                        throw new Exception("Bạn không sở hữu voucher này hoặc voucher đã được sử dụng.");
                    }
                    $validationError = $voucher->validateForOrder($subtotal);
                    if ($validationError) {
                        throw new Exception($validationError);
                    }
                    if ($voucher->isFreeShipping()) {
                        $shippingDiscount = (float)$voucher->calculateShippingDiscount($baseShippingFee);
                    } else {
                        $productDiscount = (float)$voucher->calculateProductDiscount($subtotal);
                    }
                    $voucherId = $voucher->id;
                }
            } elseif (!empty($extraData['voucher_code'])) {
                $voucher = Voucher::where('code', strtoupper(trim($extraData['voucher_code'])))->first();
                if (!$voucher) {
                    throw new Exception("Voucher không tồn tại.");
                }
                // Check if user owns this voucher and hasn't used it
                $userVoucher = UserVoucher::where('user_id', $user->id)
                    ->where('voucher_id', $voucher->id)
                    ->whereNull('used_at')
                    ->first();
                if (!$userVoucher) {
                    throw new Exception("Bạn không sở hữu voucher này hoặc voucher đã được sử dụng.");
                }
                $validationError = $voucher->validateForOrder($subtotal);
                if ($validationError) {
                    throw new Exception($validationError);
                }
                if ($voucher->isFreeShipping()) {
                    $shippingDiscount = (float)$voucher->calculateShippingDiscount($baseShippingFee);
                } else {
                    $productDiscount = (float)$voucher->calculateProductDiscount($subtotal);
                }
                $voucherId = $voucher->id;
            }

            $effectiveShippingFee = max(0.0, $baseShippingFee - $shippingDiscount);
            $totalAmount = max(0.0, $subtotal - $productDiscount) + $effectiveShippingFee;
            $discountAmount = $productDiscount;

            // Installment calculation
            $installmentMonths = null;
            $installmentMonthlyAmount = null;
            if ($paymentMethod === 'installment') {
                $installmentMonths = (int)($extraData['installment_months'] ?? 3);
                if (!in_array($installmentMonths, [3, 6, 12])) {
                    throw new Exception("Kỳ hạn trả góp không hợp lệ. Chỉ hỗ trợ 3, 6, hoặc 12 tháng.");
                }
                $installmentMonthlyAmount = $totalAmount / $installmentMonths;
            }

            // Est Delivery
            $deliveryService = resolve(\App\Services\DeliveryService::class);
            $estDelivery = $deliveryService->getEstDeliveryRange($extraData['province_code'] ?? '01');
            $estDeliveryStart = $estDelivery['start'];
            $estDeliveryEnd = $estDelivery['end'];

            // Receipt type
            $receiptType = $extraData['receipt_type'] ?? 'paper';
            $receiptEmail = null;
            if ($receiptType === 'electronic') {
                $receiptEmail = $extraData['receipt_email'] ?? $receiverInfo['receiver_email'];
                if (empty($receiptEmail) || !filter_var($receiptEmail, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Email nhận hóa đơn điện tử không hợp lệ.");
                }
            }

            // Increment Coupon / Voucher uses
            if ($coupon) {
                $coupon->increment('uses');
            }
            if ($voucher) {
                $userVoucher = UserVoucher::where('user_id', $user->id)
                    ->where('voucher_id', $voucher->id)
                    ->whereNull('used_at')
                    ->first();
                if ($userVoucher) {
                    $userVoucher->update(['used_at' => now()]);
                }
                $voucher->increment('uses');
            }

            // Step 2: Create Order
            $orderCode = 'GD-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'receiver_name' => $receiverInfo['receiver_name'],
                'receiver_phone' => $receiverInfo['receiver_phone'],
                'receiver_email' => $receiverInfo['receiver_email'],
                'province' => $receiverInfo['province'],
                'province_code' => $extraData['province_code'] ?? null,
                'district' => $receiverInfo['district'],
                'district_code' => $extraData['district_code'] ?? null,
                'ward' => $receiverInfo['ward'],
                'ward_code' => $extraData['ward_code'] ?? null,
                'address' => $receiverInfo['address'],
                'note' => $receiverInfo['note'] ?? null,
                'coupon_code' => $coupon ? $coupon->code : ($voucher ? $voucher->code : null),
                'voucher_id' => $voucherId,
                'discount_amount' => $discountAmount,
                'subtotal' => $subtotal,
                'shipping_fee' => $baseShippingFee,
                'shipping_discount' => $shippingDiscount,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'installment_months' => $installmentMonths,
                'installment_monthly_amount' => $installmentMonthlyAmount,
                'receipt_type' => $receiptType,
                'receipt_email' => $receiptEmail,
                'est_delivery_start' => $estDeliveryStart,
                'est_delivery_end' => $estDeliveryEnd,
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            // Step 3: Create Order Items and update stock
            foreach ($cartItems as $item) {
                $variant = null;
                $price = 0;
                $product = Product::find($item->product_id);

                if ($item->product_variant_id) {
                    $variant = ProductVariant::find($item->product_variant_id);
                    $price = $variant ? $variant->active_price : $product->active_price;
                } else {
                    $price = $product->active_price;
                }

                // Add warranty cost
                if ($item->warranty_id) {
                    $warranty = Warranty::find($item->warranty_id);
                    if ($warranty && $warranty->status === 'active') {
                        $price += $warranty->price;
                    }
                }

                $itemTotal = $price * $item->quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $item->product_variant_id,
                    'color_id' => $item->color_id,
                    'size_id' => $item->size_id,
                    'warranty_id' => $item->warranty_id,
                    'product_code' => $product->code,
                    'product_name' => $product->name,
                    'product_image' => ($variant && $variant->thumbnail) ? $variant->thumbnail : $product->thumbnail,
                    'price' => $price,
                    'quantity' => $item->quantity,
                    'total' => $itemTotal,
                ]);

                // Decrement stock
                if ($item->product_variant_id) {
                    ProductVariant::where('id', $item->product_variant_id)->decrement('stock_quantity', $item->quantity);
                } else {
                    $product->decrement('stock_quantity', $item->quantity);
                }
            }

            // Step 4: Create Payment record
            Payment::create([
                'order_id' => $order->id,
                'payment_code' => 'PAY-' . strtoupper(Str::random(10)),
                'payment_method' => $paymentMethod,
                'amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // Điểm thưởng sẽ được tích khi đơn hàng chuyển sang trạng thái completed (tránh trục lợi khi đơn pending/COD)

            // Step 6: Gửi thông báo đặt hàng thành công
            $notificationService = resolve(NotificationService::class);
            $notificationService->notify(
                $user,
                "Đơn hàng mới được tiếp nhận",
                "Đơn hàng {$order->order_code} trị giá " . number_format($totalAmount, 0, ',', '.') . " ₫ đang chờ xác nhận.",
                "order",
                route('orders.show', $order->id)
            );

            // Step 7: Clear cart items
            foreach ($cartItems as $item) {
                $item->delete();
            }

            return $order;
        });
    }

    /**
     * Cancel an order.
     *
     * @param Order $order
     * @param bool $isAdmin
     * @return bool
     * @throws Exception
     */
    public function cancelOrder(Order $order, bool $isAdmin = false): bool
    {
        if (!$isAdmin && $order->order_status !== 'pending') {
            throw new Exception("Chỉ đơn hàng ở trạng thái 'Chờ xác nhận' mới có thể hủy.");
        }

        if ($order->order_status === 'completed') {
            throw new Exception("Không thể hủy đơn hàng đã hoàn thành giao hàng.");
        }

        if ($order->order_status === 'cancelled') {
            throw new Exception("Đơn hàng này đã ở trạng thái đã hủy.");
        }

        return DB::transaction(function () use ($order) {
            // Update order status
            $order->update([
                'order_status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Return stock quantity
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    ProductVariant::where('id', $item->product_variant_id)->increment('stock_quantity', $item->quantity);
                } elseif ($item->product_id) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $product->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            // Revert voucher used_at
            if ($order->voucher_id && $order->user_id) {
                $userVoucher = UserVoucher::where('user_id', $order->user_id)
                    ->where('voucher_id', $order->voucher_id)
                    ->first();
                if ($userVoucher) {
                    $userVoucher->update(['used_at' => null]);
                }
                Voucher::where('id', $order->voucher_id)->decrement('uses', 1);
            }

            // Revert points earned
            $earnedTransaction = PointTransaction::where('user_id', $order->user_id)
                ->where('type', 'order_earn')
                ->where('reference_id', (string)$order->id)
                ->first();
            if ($earnedTransaction && $earnedTransaction->amount > 0) {
                $pointService = resolve(PointService::class);
                try {
                    $pointService->deductPoints(
                        $order->user,
                        $earnedTransaction->amount,
                        'order_refund',
                        (string)$order->id,
                        "Thu hồi điểm từ đơn hàng hủy {$order->order_code}"
                    );
                } catch (Exception $e) {
                    // Fallback if balance is not enough: deduct whatever user currently has
                    $current = $order->user->points;
                    if ($current > 0) {
                        $pointService->deductPoints(
                            $order->user,
                            $current,
                            'order_refund',
                            (string)$order->id,
                            "Thu hồi điểm một phần từ đơn hàng hủy {$order->order_code}"
                        );
                    }
                }
            }

            // Gửi thông báo hủy đơn
            $notificationService = resolve(NotificationService::class);
            $notificationService->notify(
                $order->user,
                "Đơn hàng của bạn đã hủy",
                "Đơn hàng {$order->order_code} đã được hủy thành công.",
                "order",
                route('orders.show', $order->id)
            );

            // Update payment status if COD/Bank Transfer
            if ($order->payment) {
                $order->payment->update([
                    'status' => 'failed',
                ]);
            }

            return true;
        });
    }

    /**
     * Award loyalty points to user when order is completed.
     *
     * @param Order $order
     * @return void
     */
    public function awardOrderPoints(Order $order): void
    {
        if (!$order->user_id) {
            return;
        }

        // Check if points already awarded for this order
        $exists = PointTransaction::where('user_id', $order->user_id)
            ->where('type', 'order_earn')
            ->where('reference_id', (string)$order->id)
            ->exists();

        if ($exists) {
            return;
        }

        $pointsEarned = floor($order->total_amount / 10000);
        if ($pointsEarned > 0 && $order->user) {
            $pointService = resolve(PointService::class);
            $pointService->addPoints($order->user, $pointsEarned, 'order_earn', (string)$order->id, "Tích điểm từ đơn hàng {$order->order_code}");
        }
    }

    /**
     * Gửi hóa đơn / biên lai điện tử qua email nếu khách hàng yêu cầu và đơn đã thanh toán.
     *
     * @param Order $order
     * @param bool $force (Cho phép gửi lại thủ công kể cả khi đã gửi trước đó)
     * @return bool
     */
    public function sendElectronicInvoice(Order $order, bool $force = false): bool
    {
        // Chỉ gửi nếu khách hàng chọn biên lai điện tử
        if ($order->receipt_type !== 'electronic') {
            return false;
        }

        // Chỉ gửi khi đơn đã được thanh toán thành công
        if ($order->payment_status !== 'paid') {
            return false;
        }

        // Chống gửi lặp nếu tự động kích hoạt nhiều lần (webhook + return url + admin)
        if (!$force && !empty($order->receipt_sent_at)) {
            return false;
        }

        $recipientEmail = $order->receipt_email ?: ($order->receiver_email ?: $order->user?->email);
        if (empty($recipientEmail) || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            Log::warning("Không thể gửi hóa đơn điện tử cho đơn #{$order->order_code}: Email nhận không hợp lệ.");
            return false;
        }

        try {
            Mail::to($recipientEmail)->send(new ElectronicInvoiceMail($order));
            $order->update(['receipt_sent_at' => now()]);
            Log::info("Đã gửi biên lai thanh toán điện tử cho đơn #{$order->order_code} tới email {$recipientEmail}");
            return true;
        } catch (Exception $e) {
            Log::error("Gửi biên lai điện tử thất bại cho đơn #{$order->order_code}: " . $e->getMessage());
            return false;
        }
    }
}
