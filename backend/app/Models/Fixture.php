<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    use HasFactory;

    protected $fillable = ['team1_id', 'team2_id', 'team1_score', 'team2_score', 'week'];

    public function team1()
    {
        return $this->hasOne(Team::class, 'id', 'team1_id');
    }
    public function team2()
    {
        return $this->hasOne(Team::class, 'id', 'team2_id');
    }
    public function matches() {
        return $this->hasOne(Matches::class);
    }
}

