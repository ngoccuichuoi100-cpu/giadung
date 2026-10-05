<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'code',
        'name',
        'normalized_name',
        'slug',
        'brand',
        'short_description',
        'description',
        'specifications',
        'price',
        'sale_price',
        'stock_quantity',
        'unit',
        'thumbnail',
        'status',
        'is_featured',
        'is_domestic',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_domestic' => 'boolean',
        'specifications' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brandRelation()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function warranties()
    {
        return $this->hasMany(Warranty::class);
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function views()
    {
        return $this->hasMany(ProductView::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helper to get active price (sale price if exists, else price)
    public function getActivePriceAttribute()
    {
        return $this->sale_price !== null && $this->sale_price < $this->price
            ? $this->sale_price
            : $this->price;
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
