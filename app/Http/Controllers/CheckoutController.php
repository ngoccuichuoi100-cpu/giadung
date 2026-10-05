<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

use App\Http\Requests\PlaceOrderRequest;
use App\Services\LocationService;

class CheckoutController extends Controller
{
    protected $orderService;
    protected $locationService;

    public function __construct(OrderService $orderService, LocationService $locationService)
    {
        $this->orderService = $orderService;
        $this->locationService = $locationService;
    }

    public function index()
    {
        if (!Auth::check()) {
            session()->put('url.intended', route('checkout.index'));
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thanh toán.');
        }

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItems = $cart ? $cart->items()->with(['product', 'variant', 'color', 'size', 'warranty'])->get() : collect();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->variant ? $item->variant->active_price : $item->product->active_price;
            if ($item->warranty) {
                $price += $item->warranty->price;
            }
            $subtotal += $price * $item->quantity;
        }

        $shippingFee = $subtotal >= 500000 ? 0 : 30000;
        $totalAmount = $subtotal + $shippingFee;

        // Get provinces list
        $provinces = $this->locationService->getProvinces();

        // Get user claimed vouchers
        $vouchers = \App\Models\UserVoucher::where('user_id', $user->id)
            ->whereNull('used_at')
            ->with('voucher')
            ->get();

        return view('checkout', compact('cartItems', 'subtotal', 'shippingFee', 'totalAmount', 'user', 'provinces', 'vouchers'));
    }

    public function placeOrder(PlaceOrderRequest $request)
    {
        if (!Auth::check()) {
            session()->put('url.intended', route('checkout.index'));
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thanh toán.');
        }

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItems = $cart ? $cart->items : collect();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        try {
            // Get text names for province, district, ward
            $locationNames = $this->locationService->getLocationNames(
                $request->input('province_code'),
                $request->input('district_code'),
                $request->input('ward_code')
            );

            $receiverInfo = $request->only([
                'receiver_name',
                'receiver_phone',
                'receiver_email',
                'address',
                'note',
            ]);
            $receiverInfo['province'] = $locationNames['province'];
            $receiverInfo['district'] = $locationNames['district'];
            $receiverInfo['ward'] = $locationNames['ward'];

            $extraData = [
                'province_code' => $request->input('province_code'),
                'district_code' => $request->input('district_code'),
                'ward_code' => $request->input('ward_code'),
                'coupon_code' => $request->input('coupon_code'),
                'voucher_code' => $request->input('voucher_code'),
                'installment_months' => $request->input('installment_months'),
                'receipt_type' => $request->input('receipt_type'),
                'receipt_email' => $request->input('receipt_email'),
            ];

            $order = $this->orderService->placeOrder(
                $user,
                $receiverInfo,
                $cartItems,
                $request->input('payment_method'),
                $extraData
            );

            // Handle PayOS payment redirect
            if ($request->input('payment_method') === 'payos') {
                $payOSService = resolve(\App\Services\PayOSService::class);
                if ($payOSService->isConfigured()) {
                    try {
                        $paymentLink = $payOSService->createPaymentLink($order);
                        return redirect()->away($paymentLink['checkout_url']);
                    } catch (Exception $e) {
                        return redirect()->route('orders.show', $order->id)
                            ->with('warning', 'Đơn hàng đã được tạo nhưng chưa mở được cổng PayOS: ' . $e->getMessage() . '. Bạn có thể bấm nút thanh toán lại trong đơn hàng.');
                    }
                } else {
                    return redirect()->route('orders.show', $order->id)
                        ->with('warning', 'Đơn hàng đã được tạo thành công! (Lưu ý: Hệ thống chưa cấu hình PAYOS_CLIENT_ID / API_KEY trong .env. Vui lòng bổ sung để kích hoạt chuyển tiếp tự động).');
                }
            }

            return redirect()->route('orders.show', $order->id)->with('success', 'Đặt hàng thành công! Đơn hàng của bạn đã được tạo.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
