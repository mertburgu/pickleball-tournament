<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['game_id', 'score', 'match_duration_seconds'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function playerResults()
    {
        return $this->hasMany(PlayerResult::class, 'result_id', 'id');
    }
}
