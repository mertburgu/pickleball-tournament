<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerResult extends Model
{
    protected $fillable = ['result_id', 'player_id', 'score', 'status'];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
