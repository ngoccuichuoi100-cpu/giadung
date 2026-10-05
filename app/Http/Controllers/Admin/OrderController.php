<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderController extends Controller
{
    protected $orderService;
    protected $notificationService;

    public function __construct(OrderService $orderService, NotificationService $notificationService)
    {
        $this->orderService = $orderService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('q')) {
            $q = trim($request->q);
            if (mb_strlen($q, 'UTF-8') === 1) {
                // Gõ 1 ký tự: Chỉ tìm các từ bắt đầu bằng ký tự này
                $query->where(function ($sub) use ($q) {
                    $sub->where('order_code', 'like', "{$q}%")
                        ->orWhere('receiver_name', 'like', "{$q}%")
                        ->orWhere('receiver_name', 'like', "% {$q}%")
                        ->orWhere('receiver_phone', 'like', "{$q}%")
                        ->orWhereHas('user', function ($u) use ($q) {
                            $u->where('name', 'like', "{$q}%")
                                ->orWhere('name', 'like', "% {$q}%")
                                ->orWhere('email', 'like', "{$q}%")
                                ->orWhere('phone', 'like', "{$q}%");
                        });
                });
            } else {
                // Gõ >= 2 ký tự: Tìm linh hoạt ở mọi vị trí
                $query->where(function ($sub) use ($q) {
                    $sub->where('order_code', 'like', "%{$q}%")
                        ->orWhere('receiver_name', 'like', "%{$q}%")
                        ->orWhere('receiver_phone', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($u) use ($q) {
                            $u->where('name', 'like', "%{$q}%")
                                ->orWhere('email', 'like', "%{$q}%")
                                ->orWhere('phone', 'like', "%{$q}%");
                        });
                });
            }
        }

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['items', 'payment', 'user'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isSales())) {
            abort(403, 'Chỉ Quản trị viên hoặc Nhân viên bán hàng mới có quyền duyệt đơn hàng.');
        }

        $order = Order::findOrFail($id);

        $request->validate([
            'order_status' => 'required|in:pending,confirmed,processing,shipping,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $newOrderStatus = $request->order_status;
        $newPaymentStatus = $request->payment_status;

        // Transition restrictions:
        if ($order->order_status === 'cancelled') {
            return back()->with('error', 'Đơn hàng đã hủy không thể cập nhật trạng thái khác.');
        }

        if ($order->order_status === 'completed' && $newOrderStatus !== 'completed') {
            return back()->with('error', 'Đơn hàng đã hoàn thành không thể chuyển về trạng thái khác.');
        }

        // Logic check: cancel order
        if ($newOrderStatus === 'cancelled' && $order->order_status !== 'cancelled') {
            try {
                $this->orderService->cancelOrder($order, true);
                // Also update payment status if admin specified it
                $order->update([
                    'payment_status' => $newPaymentStatus,
                ]);
                return back()->with('success', 'Đơn hàng đã được hủy và hoàn lại tồn kho thành công.');
            } catch (Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        // Standard update
        try {
            DB::transaction(function () use ($order, $newOrderStatus, $newPaymentStatus) {
                $order->update([
                    'order_status' => $newOrderStatus,
                    'payment_status' => $newPaymentStatus,
                ]);

                // Update payment details as well if payment status changed
                if ($order->payment) {
                    $order->payment->update([
                        'status' => $newPaymentStatus === 'paid' ? 'completed' : ($newPaymentStatus === 'refunded' ? 'refunded' : 'pending'),
                        'paid_at' => $newPaymentStatus === 'paid' ? now() : null,
                    ]);
                }
            });

            // Tích điểm thưởng cho khách hàng khi hoàn thành đơn
            if ($newOrderStatus === 'completed') {
                $this->orderService->awardOrderPoints($order);
            }

            // Send notification to customer based on status
            if ($order->user) {
                $title = "";
                $content = "";

                if ($newOrderStatus === 'confirmed' && $order->order_status !== 'confirmed') {
                    $title = "Đơn hàng đã được duyệt";
                    $content = "Đơn hàng {$order->order_code} của bạn đã được quản trị viên duyệt.";
                } elseif ($newOrderStatus === 'processing' && $order->order_status !== 'processing') {
                    $title = "Đơn đang chuẩn bị";
                    $content = "Đơn hàng {$order->order_code} của bạn đang được đóng gói chuẩn bị hàng.";
                } elseif ($newOrderStatus === 'shipping' && $order->order_status !== 'shipping') {
                    $title = "Đơn đang giao";
                    $content = "Đơn hàng {$order->order_code} đang được giao đi bởi đơn vị vận chuyển.";
                } elseif ($newOrderStatus === 'completed' && $order->order_status !== 'completed') {
                    $title = "Giao hàng thành công";
                    $content = "Đơn hàng {$order->order_code} đã giao thành công tới tay quý khách.";
                }

                if ($newPaymentStatus === 'refunded' && $order->payment_status !== 'refunded') {
                    $title = "Đơn hàng đã hoàn tiền";
                    $content = "Số tiền của đơn hàng {$order->order_code} đã được hoàn lại.";
                }

                if ($title && $content) {
                    $this->notificationService->notify(
                        $order->user,
                        $title,
                        $content,
                        "order",
                        route('orders.show', $order->id)
                    );
                }
            }

            // Gửi hóa đơn điện tử nếu khách hàng chọn nhận biên lai điện tử
            if ($newPaymentStatus === 'paid') {
                try {
                    $this->orderService->sendElectronicInvoice($order->fresh());
                } catch (Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Gửi hóa đơn điện tử tự động thất bại: " . $e->getMessage());
                }
            }

            return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * Gửi / Gửi lại hóa đơn điện tử thủ công qua email
     */
    public function sendReceipt($id)
    {
        $order = Order::findOrFail($id);

        if ($order->payment_status !== 'paid') {
            return back()->with('error', 'Chỉ có thể xuất biên lai điện tử cho đơn hàng đã thanh toán.');
        }

        if ($order->receipt_type !== 'electronic') {
            $order->update([
                'receipt_type' => 'electronic',
                'receipt_email' => $order->receipt_email ?: $order->receiver_email,
            ]);
        }

        $sent = $this->orderService->sendElectronicInvoice($order, true);

        if ($sent) {
            $target = $order->receipt_email ?: $order->receiver_email;
            return back()->with('success', "Đã gửi hóa đơn điện tử thành công tới email {$target}.");
        }

        return back()->with('error', 'Gửi hóa đơn điện tử thất bại. Vui lòng kiểm tra lại kết nối email.');
    }
}
