<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = Auth::user()->orders()->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['items', 'payment'])->findOrFail($id);

        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        return view('orders.show', compact('order'));
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền hủy đơn hàng này.');
        }

        try {
            $this->orderService->cancelOrder($order);
            return back()->with('success', 'Đơn hàng đã được hủy thành công.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resendReceipt($id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thao tác trên đơn hàng này.');
        }

        if ($order->payment_status !== 'paid') {
            return back()->with('error', 'Chỉ có thể nhận hóa đơn điện tử khi đơn hàng đã thanh toán thành công.');
        }

        if ($order->receipt_type !== 'electronic') {
            $order->update([
                'receipt_type' => 'electronic',
                'receipt_email' => $order->receipt_email ?: ($order->receiver_email ?: Auth::user()->email),
            ]);
        }

        $sent = $this->orderService->sendElectronicInvoice($order, true);

        if ($sent) {
            $target = $order->receipt_email ?: ($order->receiver_email ?: Auth::user()->email);
            return back()->with('success', "Biên lai thanh toán điện tử đã được gửi tới email {$target}. Vui lòng kiểm tra hộp thư đến (hoặc thư mục Spam).");
        }

        return back()->with('error', 'Chưa thể gửi email biên lai vào lúc này. Vui lòng thử lại sau.');
    }
}
