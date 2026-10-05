<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'sku',
        'price',
        'sale_price',
        'stock_quantity',
        'thumbnail',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    // Helper to get active price (falls back to product base price if variant price is null)
    public function getActivePriceAttribute()
    {
        $vPrice = $this->price !== null ? $this->price : $this->product->price;
        $vSalePrice = $this->sale_price !== null ? $this->sale_price : $this->product->sale_price;

        return $vSalePrice !== null && $vSalePrice < $vPrice
            ? $vSalePrice
            : $vPrice;
    }

    // Accessor for thumbnail URL supporting both absolute web URLs and local storage paths
    public function getThumbnailUrlAttribute()
    {
        if (empty($this->thumbnail)) {
            return null;
        }

        if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
            return $this->thumbnail;
        }

        return asset('storage/' . $this->thumbnail);
    }
}
