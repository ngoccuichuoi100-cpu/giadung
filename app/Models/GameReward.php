<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
        'type',
        'value',
        'probability',
        'quantity_limit',
        'quantity_claimed',
        'status',
    ];

    protected $casts = [
        'probability' => 'decimal:4',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
