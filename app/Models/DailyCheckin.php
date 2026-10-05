<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCheckin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'checkin_date',
        'points_earned',
        'streak',
    ];

    protected $casts = [
        'checkin_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
