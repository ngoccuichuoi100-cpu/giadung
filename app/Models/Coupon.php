<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_value',
        'max_uses',
        'uses',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
    ];

    /**
     * Validate the coupon against order subtotal.
     * Returns string error message if invalid, or null if valid.
     */
    public function validateForOrder($subtotal)
    {
        if ($this->status !== 'active') {
            return 'Mã giảm giá này hiện không hoạt động.';
        }
        
        if ($this->max_uses > 0 && $this->uses >= $this->max_uses) {
            return 'Mã giảm giá đã đạt giới hạn sử dụng tối đa.';
        }

        $now = now();
        if ($now->lt($this->start_date)) {
            return 'Mã giảm giá chưa đến thời gian áp dụng.';
        }

        if ($now->gt($this->end_date)) {
            return 'Mã giảm giá này đã hết hạn sử dụng.';
        }

        if ($subtotal < $this->min_order_value) {
            return 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($this->min_order_value, 0, ',', '.') . ' ₫ để sử dụng mã này.';
        }

        return null; // Valid!
    }

    public function isFreeShipping(): bool
    {
        return $this->type === 'free_shipping';
    }

    public function calculateShippingDiscount($shippingFee = 30000): float
    {
        if (!$this->isFreeShipping()) {
            return 0.0;
        }
        $shippingFee = (float)$shippingFee;
        $discount = ((float)$this->value > 100) ? (float)$this->value : $shippingFee;
        return min($discount, $shippingFee);
    }

    public function calculateProductDiscount($subtotal): float
    {
        if ($this->isFreeShipping()) {
            return 0.0;
        }

        $subtotal = (float)$subtotal;

        if ($this->type === 'fixed') {
            return min((float)$this->value, $subtotal);
        }
        
        if ($this->type === 'percent') {
            return min(($subtotal * ((float)$this->value / 100)), $subtotal);
        }

        return 0.0;
    }

    /**
     * Calculate discount amount based on order subtotal.
     */
    public function calculateDiscount($subtotal, $shippingFee = 30000)
    {
        if ($this->isFreeShipping()) {
            return $this->calculateShippingDiscount($shippingFee);
        }

        return $this->calculateProductDiscount($subtotal);
    }
}
