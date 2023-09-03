<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameTracking extends Model
{
    use HasFactory;

    protected $table = 'game_tracking';

    protected $fillable = [
        'game_id',
        'status',
        'duration_seconds',
        'start_time'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
}
