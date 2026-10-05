<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'reward_id',
        'result',
        'points_spent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function reward()
    {
        return $this->belongsTo(GameReward::class, 'reward_id');
    }
}
