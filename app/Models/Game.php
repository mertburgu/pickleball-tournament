<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['code', 'tournament_id', 'court_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($game) {
            $game->code = uniqid();
        });
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'game_players');
    }

    public function result()
    {
        return $this->hasOne(Result::class, 'game_id', 'id');
    }
}
