<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PayOSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use PayOS\Exceptions\WebhookException;

class PayOSController extends Controller
{
    protected PayOSService $payOSService;

    public function __construct(PayOSService $payOSService)
    {
        $this->payOSService = $payOSService;
    }

    /**
     * Create or re-open PayOS payment link for an order.
     */
    public function createPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Security check
        if (!Auth::check() || ($order->user_id !== Auth::id() && !Auth::user()->isAdmin())) {
            abort(403, 'Bạn không có quyền thực hiện thanh toán cho đơn hàng này.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order->id)->with('info', 'Đơn hàng này đã được thanh toán hoàn tất.');
        }

        if ($order->order_status === 'cancelled') {
            return redirect()->route('orders.show', $order->id)->with('error', 'Đơn hàng này đã bị hủy, không thể tiếp tục thanh toán.');
        }

        try {
            $paymentLink = $this->payOSService->createPaymentLink($order);
            return redirect()->away($paymentLink['checkout_url']);
        } catch (Exception $e) {
            Log::error("PayOS createPayment error: " . $e->getMessage());
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Không thể khởi tạo cổng PayOS: ' . $e->getMessage());
        }
    }

    /**
     * Return URL after customer completes payment on PayOS.
     */
    public function handleReturn(Request $request)
    {
        $orderId = $request->query('order_id');
        $orderCode = $request->query('orderCode');
        $status = $request->query('status');
        $cancel = $request->query('cancel');

        $order = null;
        if ($orderId) {
            $order = Order::find($orderId);
        }
        if (!$order && $orderCode) {
            $order = Order::where('payos_order_code', $orderCode)->first();
        }

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Không tìm thấy thông tin đơn hàng.');
        }

        // If user cancelled on PayOS checkout
        if ($cancel === 'true' || $status === 'CANCELLED') {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'Giao dịch thanh toán PayOS đã được hủy. Bạn có thể thanh toán lại bất cứ lúc nào.');
        }

        // Check payment status with PayOS API
        try {
            $check = $this->payOSService->checkPaymentStatus($order);
            if ($check['paid']) {
                return redirect()->route('orders.show', $order->id)
                    ->with('success', 'Thanh toán thành công! Đơn hàng của bạn đã được xác nhận thanh toán qua PayOS.');
            }
        } catch (Exception $e) {
            Log::warning("PayOS return check error: " . $e->getMessage());
        }

        // If already paid or code 00
        if ($order->fresh()->payment_status === 'paid') {
            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Thanh toán đơn hàng thành công qua PayOS!');
        }

        return redirect()->route('orders.show', $order->id)
            ->with('info', 'Hệ thống đang chờ xác nhận giao dịch từ ngân hàng. Trạng thái sẽ được cập nhật tự động.');
    }

    /**
     * Cancel URL if customer clicks cancel on PayOS checkout page.
     */
    public function handleCancel(Request $request)
    {
        $orderId = $request->query('order_id');
        $orderCode = $request->query('orderCode');

        $order = null;
        if ($orderId) {
            $order = Order::find($orderId);
        }
        if (!$order && $orderCode) {
            $order = Order::where('payos_order_code', $orderCode)->first();
        }

        if (!$order) {
            return redirect()->route('orders.index')->with('info', 'Bạn đã hủy phiên thanh toán.');
        }

        return redirect()->route('orders.show', $order->id)
            ->with('info', 'Bạn đã hủy giao dịch PayOS. Đơn hàng của bạn vẫn được lưu để thanh toán lại.');
    }

    /**
     * PayOS Webhook (IPN) handler.
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('PayOS Webhook received: ', $payload);

        try {
            // Verify webhook signature
            $verifiedData = $this->payOSService->verifyWebhookData($payload);

            // Handle test / ping webhook from PayOS
            if (isset($verifiedData->orderCode) && ($verifiedData->orderCode == 123 || $verifiedData->description === 'VQRIO123')) {
                Log::info('PayOS Webhook verified: Test webhook acknowledged.');
                return response()->json([
                    'success' => true,
                    'message' => 'Test webhook confirmed',
                ], 200);
            }

            if (!empty($verifiedData->orderCode)) {
                $order = Order::where('payos_order_code', $verifiedData->orderCode)->first();

                if (!$order) {
                    // Try fallback by order ID
                    $order = Order::find($verifiedData->orderCode);
                }

                if ($order) {
                    $ref = $verifiedData->reference ?? ('PAYOS-' . $verifiedData->orderCode);
                    $this->payOSService->markOrderAsPaid($order, (string)$ref);

                    Log::info("PayOS Webhook: Order #{$order->id} ({$order->order_code}) paid successfully via reference {$ref}.");

                    return response()->json([
                        'success' => true,
                        'message' => 'Order payment updated successfully',
                    ], 200);
                } else {
                    Log::warning("PayOS Webhook: Order not found for orderCode: " . $verifiedData->orderCode);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook received',
            ], 200);
        } catch (WebhookException $e) {
            Log::error('PayOS Webhook signature verification failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature: ' . $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            Log::error('PayOS Webhook processing exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX endpoint to poll status of order payment in real-time.
     */
    public function checkStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if (Auth::check() && $order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $result = $this->payOSService->checkPaymentStatus($order);
        return response()->json($result);
    }
}
