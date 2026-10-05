<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Voucher;
use App\Models\UserVoucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Apply coupon code or voucher to a cart subtotal and calculate discount.
     */
    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $code = strtoupper(trim($request->input('code')));
        $subtotal = (float)$request->input('subtotal');

        $coupon = Coupon::where('code', $code)->first();

        $baseShippingFee = $subtotal >= 500000 ? 0.0 : 30000.0;

        if ($coupon) {
            // Validate coupon requirements
            $validationError = $coupon->validateForOrder($subtotal);
            if ($validationError) {
                return response()->json([
                    'success' => false,
                    'message' => $validationError,
                ]);
            }

            if ($coupon->isFreeShipping()) {
                if ($baseShippingFee <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Đơn hàng từ 500.000 ₫ đã được miễn phí vận chuyển tự động, không cần sử dụng mã này.',
                    ]);
                }
                $productDiscount = 0.0;
                $shippingDiscount = (float)$coupon->calculateShippingDiscount($baseShippingFee);
                $effectiveShippingFee = max(0.0, $baseShippingFee - $shippingDiscount);
                $totalAmount = $subtotal + $effectiveShippingFee;
                $message = 'Áp dụng mã miễn phí vận chuyển thành công!';
            } else {
                $productDiscount = (float)$coupon->calculateProductDiscount($subtotal);
                $shippingDiscount = 0.0;
                $effectiveShippingFee = $baseShippingFee;
                $totalAmount = max(0.0, $subtotal - $productDiscount) + $effectiveShippingFee;
                $message = 'Áp dụng mã giảm giá thành công!';
            }

            $discountAmount = $productDiscount + $shippingDiscount;

            return response()->json([
                'success' => true,
                'code' => $coupon->code,
                'name' => null,
                'type' => $coupon->type,
                'is_free_shipping' => $coupon->isFreeShipping(),
                'value' => (float)$coupon->value,
                'product_discount' => $productDiscount,
                'shipping_discount' => $shippingDiscount,
                'discount_amount' => $discountAmount,
                'original_shipping_fee' => $baseShippingFee,
                'shipping_fee' => $effectiveShippingFee,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'message' => $message,
            ]);
        }

        // Check if code matches a personal Voucher
        $voucher = Voucher::where('code', $code)->first();
        if ($voucher) {
            if (Auth::check()) {
                $userVoucher = UserVoucher::where('user_id', Auth::id())
                    ->where('voucher_id', $voucher->id)
                    ->whereNull('used_at')
                    ->first();

                if (!$userVoucher) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không sở hữu voucher này hoặc voucher đã được sử dụng.',
                    ]);
                }
            }

            $validationError = $voucher->validateForOrder($subtotal);
            if ($validationError) {
                return response()->json([
                    'success' => false,
                    'message' => $validationError,
                ]);
            }

            if ($voucher->isFreeShipping()) {
                if ($baseShippingFee <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Đơn hàng từ 500.000 ₫ đã được miễn phí vận chuyển tự động, không cần sử dụng mã này.',
                    ]);
                }
                $productDiscount = 0.0;
                $shippingDiscount = (float)$voucher->calculateShippingDiscount($baseShippingFee);
                $effectiveShippingFee = max(0.0, $baseShippingFee - $shippingDiscount);
                $totalAmount = $subtotal + $effectiveShippingFee;
                $message = 'Áp dụng voucher "' . $voucher->name . '" (Miễn phí vận chuyển) thành công!';
            } else {
                $productDiscount = (float)$voucher->calculateProductDiscount($subtotal);
                $shippingDiscount = 0.0;
                $effectiveShippingFee = $baseShippingFee;
                $totalAmount = max(0.0, $subtotal - $productDiscount) + $effectiveShippingFee;
                $message = 'Áp dụng voucher "' . $voucher->name . '" thành công!';
            }

            $discountAmount = $productDiscount + $shippingDiscount;

            return response()->json([
                'success' => true,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'type' => $voucher->type,
                'is_free_shipping' => $voucher->isFreeShipping(),
                'value' => (float)$voucher->value,
                'product_discount' => $productDiscount,
                'shipping_discount' => $shippingDiscount,
                'discount_amount' => $discountAmount,
                'original_shipping_fee' => $baseShippingFee,
                'shipping_fee' => $effectiveShippingFee,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'message' => $message,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Mã giảm giá hoặc voucher không tồn tại hoặc đã nhập sai.',
        ]);
    }
}
