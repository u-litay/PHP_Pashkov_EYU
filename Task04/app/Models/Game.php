<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['player_id', 'played_at', 'number1', 'number2', 'gcd', 'player_answer', 'result'];
    protected $dates = ['played_at'];


    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
