<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'start_date',
        'end_date',
        'conditions',
        'reward_info',
        'status',
        'type',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'conditions' => 'array',
    ];

    public function rewards()
    {
        return $this->hasMany(GameReward::class);
    }

    public function attempts()
    {
        return $this->hasMany(GameAttempt::class);
    }
}
