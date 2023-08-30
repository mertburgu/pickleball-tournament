<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    protected $fillable = ['name', 'tournament_id'];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
