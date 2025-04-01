<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchPlayerStats extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['match_id', 'player_id', 'points', 'assists', 'two_point_success', 'three_point_success', 'two_point_made', 'three_point_made', 'two_point_attempts', 'three_point_attempts'];

    public function match()
    {
        return $this->belongsTo(Matches::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
