<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'name', 'assists', 'points', 'two_point_success', 'three_point_success'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
