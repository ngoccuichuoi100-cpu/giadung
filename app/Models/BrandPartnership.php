<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandPartnership extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'url',
        'type',
        'status',
    ];

    public function getLogoUrlAttribute()
    {
        if (empty($this->logo)) {
            return null;
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }

        return asset('storage/' . $this->logo);
    }
}
