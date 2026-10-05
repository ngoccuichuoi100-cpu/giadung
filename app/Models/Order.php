<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'receiver_name',
        'receiver_phone',
        'receiver_email',
        'province',
        'province_code',
        'district',
        'district_code',
        'ward',
        'ward_code',
        'address',
        'note',
        'coupon_code',
        'voucher_id',
        'discount_amount',
        'subtotal',
        'shipping_fee',
        'shipping_discount',
        'total_amount',
        'payos_order_code',
        'payos_checkout_url',
        'payment_method',
        'installment_months',
        'installment_monthly_amount',
        'receipt_type',
        'receipt_email',
        'receipt_sent_at',
        'est_delivery_start',
        'est_delivery_end',
        'payment_status',
        'order_status',
        'cancelled_at',
    ];

    protected $casts = [
        'payos_order_code' => 'integer',
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'shipping_discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'installment_monthly_amount' => 'decimal:2',
        'est_delivery_start' => 'date',
        'est_delivery_end' => 'date',
        'receipt_sent_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
