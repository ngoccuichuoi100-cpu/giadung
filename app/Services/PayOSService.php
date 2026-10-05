<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use PayOS\PayOS;
use PayOS\Models\V2\PaymentRequests\CreatePaymentLinkRequest;
use PayOS\Models\V2\PaymentRequests\PaymentLinkItem;
use PayOS\Models\V2\PaymentRequests\PaymentLinkStatus;
use PayOS\Exceptions\APIException;
use PayOS\Exceptions\WebhookException;

class PayOSService
{
    protected ?PayOS $client = null;
    protected OrderService $orderService;
    protected NotificationService $notificationService;

    public function __construct(OrderService $orderService, NotificationService $notificationService)
    {
        $this->orderService = $orderService;
        $this->notificationService = $notificationService;

        $clientId = config('services.payos.client_id');
        $apiKey = config('services.payos.api_key');
        $checksumKey = config('services.payos.checksum_key');

        if (!empty($clientId) && !empty($apiKey) && !empty($checksumKey)) {
            try {
                $this->client = new PayOS(
                    clientId: $clientId,
                    apiKey: $apiKey,
                    checksumKey: $checksumKey,
                );
            } catch (Exception $e) {
                Log::error('PayOS initialization error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Check if PayOS is configured with valid credentials.
     */
    public function isConfigured(): bool
    {
        return $this->client !== null;
    }

    /**
     * Get underlying PayOS client instance.
     */
    public function getClient(): ?PayOS
    {
        return $this->client;
    }

    /**
     * Create a PayOS payment link for an order.
     *
     * @param Order $order
     * @return array
     * @throws Exception
     */
    public function createPaymentLink(Order $order): array
    {
        if (!$this->isConfigured()) {
            throw new Exception("Cổng thanh toán PayOS chưa được cấu hình Client ID, API Key hoặc Checksum Key.");
        }

        // Generate unique integer orderCode for PayOS (within JS safe integer range)
        $payosOrderCode = intval(time() . rand(10, 99));

        // Description max 25 characters without accents/special chars
        $description = 'DH ' . $order->id . ' ' . substr(preg_replace('/[^0-9A-Za-z]/', '', $order->order_code), -6);
        if (strlen($description) > 25) {
            $description = substr($description, 0, 25);
        }

        $amount = (int) round($order->total_amount);

        // Single summary item to guarantee item total exactly matches order amount
        $items = [
            new PaymentLinkItem(
                name: 'Đơn hàng ' . $order->order_code,
                quantity: 1,
                price: $amount
            )
        ];

        $paymentData = new CreatePaymentLinkRequest(
            orderCode: $payosOrderCode,
            amount: $amount,
            description: $description,
            cancelUrl: route('payos.cancel', ['order_id' => $order->id]),
            returnUrl: route('payos.return', ['order_id' => $order->id]),
            items: $items,
            buyerName: $order->receiver_name,
            buyerEmail: $order->receiver_email,
            buyerPhone: $order->receiver_phone,
            buyerAddress: $order->address . ', ' . $order->ward . ', ' . $order->district . ', ' . $order->province,
        );

        try {
            $response = $this->client->paymentRequests->create($paymentData);

            // Save PayOS orderCode and checkout URL
            $order->update([
                'payos_order_code' => $payosOrderCode,
                'payos_checkout_url' => $response->checkoutUrl,
            ]);

            if ($order->payment) {
                $order->payment->update([
                    'payos_order_code' => $payosOrderCode,
                    'payos_payment_link_id' => $response->paymentLinkId,
                ]);
            }

            return [
                'success' => true,
                'checkout_url' => $response->checkoutUrl,
                'order_code' => $payosOrderCode,
                'payment_link_id' => $response->paymentLinkId,
                'qr_code' => $response->qrCode,
                'account_number' => $response->accountNumber,
                'account_name' => $response->accountName,
                'bin' => $response->bin,
            ];
        } catch (APIException $e) {
            Log::error("PayOS Create Payment Link API Error: {$e->getMessage()} | Status: {$e->status}");
            throw new Exception("Lỗi khi kết nối tới PayOS: " . $e->getMessage());
        } catch (Exception $e) {
            Log::error("PayOS Create Payment Link Error: {$e->getMessage()}");
            throw new Exception("Không thể tạo liên kết thanh toán PayOS: " . $e->getMessage());
        }
    }

    /**
     * Mark order and payment as paid.
     *
     * @param Order $order
     * @param string|null $transactionRef
     * @return bool
     */
    public function markOrderAsPaid(Order $order, ?string $transactionRef = null): bool
    {
        if ($order->payment_status === 'paid') {
            return true;
        }

        return DB::transaction(function () use ($order, $transactionRef) {
            $order->update([
                'payment_status' => 'paid',
                'order_status' => $order->order_status === 'pending' ? 'confirmed' : $order->order_status,
            ]);

            if ($order->payment) {
                $order->payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'transaction_reference' => $transactionRef ?? $order->payment->transaction_reference ?? ('PAYOS-' . ($order->payos_order_code ?? $order->id)),
                ]);
            }

            // Award points for the completed payment
            try {
                $this->orderService->awardOrderPoints($order);
            } catch (Exception $e) {
                Log::warning("Award points on PayOS success failed for order {$order->id}: {$e->getMessage()}");
            }

            // Notify customer
            try {
                if ($order->user) {
                    $this->notificationService->notify(
                        $order->user,
                        "Thanh toán PayOS thành công",
                        "Đơn hàng {$order->order_code} đã được thanh toán thành công qua PayOS (" . number_format($order->total_amount, 0, ',', '.') . " ₫).",
                        "order",
                        route('orders.show', $order->id)
                    );
                }
            } catch (Exception $e) {
                Log::warning("Notify on PayOS success failed: {$e->getMessage()}");
            }

            // Gửi hóa đơn điện tử qua email nếu khách hàng yêu cầu
            try {
                $this->orderService->sendElectronicInvoice($order);
            } catch (Exception $e) {
                Log::warning("Send electronic invoice on PayOS success failed for order {$order->id}: {$e->getMessage()}");
            }

            Log::info("Order #{$order->id} ({$order->order_code}) successfully marked as PAID via PayOS.");

            return true;
        });
    }

    /**
     * Check payment status with PayOS API and update locally if paid.
     *
     * @param Order $order
     * @return array
     */
    public function checkPaymentStatus(Order $order): array
    {
        if ($order->payment_status === 'paid') {
            return [
                'status' => 'PAID',
                'paid' => true,
                'message' => 'Đơn hàng đã được thanh toán.',
            ];
        }

        if (!$this->isConfigured() || !$order->payos_order_code) {
            return [
                'status' => 'UNKNOWN',
                'paid' => false,
                'message' => 'Chưa cấu hình PayOS hoặc đơn hàng chưa có mã PayOS.',
            ];
        }

        try {
            $paymentLink = $this->client->paymentRequests->get($order->payos_order_code);
            $statusStr = is_object($paymentLink->status) && isset($paymentLink->status->value)
                ? $paymentLink->status->value
                : (string)$paymentLink->status;

            if ($statusStr === 'PAID') {
                $this->markOrderAsPaid($order, (string)$order->payos_order_code);
                return [
                    'status' => 'PAID',
                    'paid' => true,
                    'message' => 'Thanh toán thành công.',
                ];
            }

            return [
                'status' => $statusStr,
                'paid' => false,
                'message' => "Trạng thái hiện tại: {$statusStr}",
            ];
        } catch (Exception $e) {
            Log::error("PayOS check payment status error for order {$order->id}: " . $e->getMessage());
            return [
                'status' => 'ERROR',
                'paid' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook request data from PayOS.
     *
     * @param array $payload
     * @return mixed
     * @throws WebhookException
     */
    public function verifyWebhookData(array $payload)
    {
        if (!$this->isConfigured()) {
            throw new Exception("PayOS chưa được cấu hình.");
        }

        return $this->client->webhooks->verify($payload);
    }
}
