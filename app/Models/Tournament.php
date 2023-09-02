<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = [
        'name', 'game_format', 'score_format', 'tournament_format',
        'player_limit', 'average_game_time', 'number_of_courts', 'started'
    ];

    public function courts()
    {
        return $this->hasMany(Court::class, 'tournament_id', 'id');
    }
    public function games()
    {
        return $this->hasMany(Game::class, 'tournament_id', 'id');
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
