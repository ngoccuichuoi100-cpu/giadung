<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'acquisition_type',
        'value',
        'max_discount_amount',
        'min_order_value',
        'max_uses',
        'uses',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function userVouchers()
    {
        return $this->hasMany(UserVoucher::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'percent' => 'Giảm phần trăm (%)',
            'free_shipping' => 'Miễn phí vận chuyển (Freeship)',
            default => 'Giảm số tiền cố định (₫)',
        };
    }

    public function getAcquisitionTypeLabelAttribute(): string
    {
        return match ($this->acquisition_type) {
            'new_member' => 'Tặng thành viên mới',
            'game_reward' => 'Vòng quay / Trò chơi',
            'referral' => 'Thưởng giới thiệu bạn bè',
            'loyalty_points' => 'Đổi điểm tích lũy',
            'special_gift' => 'Tặng riêng đặc biệt',
            default => 'Công khai tại giỏ hàng',
        };
    }

    public function isExpired(): bool
    {
        return now()->gt($this->end_date);
    }

    public function isUpcoming(): bool
    {
        return now()->lt($this->start_date);
    }

    public function validateForOrder($subtotal)
    {
        if ($this->status !== 'active') {
            return 'Mã giảm giá này hiện không khả dụng hoặc đã bị tạm ngưng.';
        }

        if ($this->isUpcoming()) {
            return 'Mã giảm giá này chưa đến thời hạn áp dụng (bắt đầu từ ' . $this->start_date->format('d/m/Y') . ').';
        }

        if ($this->isExpired()) {
            return 'Mã giảm giá này đã hết hạn sử dụng vào ngày ' . $this->end_date->format('d/m/Y') . '.';
        }

        if ($this->max_uses > 0 && $this->uses >= $this->max_uses) {
            return 'Mã giảm giá này đã đạt giới hạn lượt sử dụng tối đa.';
        }

        if ($subtotal < $this->min_order_value) {
            return 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($this->min_order_value, 0, ',', '.') . ' ₫ để áp dụng mã này.';
        }

        return null;
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
        // If value > 100, treat as explicit shipping support amount (e.g. 15.000, 30.000)
        // If value <= 100 or 0, treat as full shipping discount
        $discount = ((float)$this->value > 100) ? (float)$this->value : $shippingFee;

        if ($this->max_discount_amount && $this->max_discount_amount > 0) {
            $discount = min($discount, (float)$this->max_discount_amount);
        }

        return min($discount, $shippingFee);
    }

    public function calculateProductDiscount($subtotal): float
    {
        if ($this->isFreeShipping()) {
            return 0.0;
        }

        $subtotal = (float)$subtotal;

        if ($this->type === 'percent') {
            $discount = $subtotal * ((float)$this->value / 100);
            if ($this->max_discount_amount && $this->max_discount_amount > 0) {
                $discount = min($discount, (float)$this->max_discount_amount);
            }
            return min($discount, $subtotal);
        }

        return min((float)$this->value, $subtotal);
    }

    public function calculateDiscount($subtotal, $shippingFee = 30000)
    {
        if ($this->isFreeShipping()) {
            return $this->calculateShippingDiscount($shippingFee);
        }

        return $this->calculateProductDiscount($subtotal);
    }
}
