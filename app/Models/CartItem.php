<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'color_id',
        'size_id',
        'warranty_id',
        'quantity',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function getUnitPriceAttribute()
    {
        $price = $this->variant ? $this->variant->active_price : ($this->product ? $this->product->active_price : 0);
        if ($this->warranty && $this->warranty->status === 'active') {
            $price += $this->warranty->price;
        }
        return $price;
    }

    public function getLinePriceAttribute()
    {
        return $this->unit_price * $this->quantity;
    }
}
