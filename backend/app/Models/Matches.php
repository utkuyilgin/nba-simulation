<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\SoftDeletes;

class Matches extends Model
{
    use InteractsWithSockets;

    use SoftDeletes;

    protected $fillable = [
        'team1_id', 'team2_id', 'score_team1', 'score_team2', 'status', 'start_time', 'fixture_id', 'week'
    ];

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function playerStats() {
        return $this->hasMany(MatchPlayerStats::class, 'match_id');
    }

    public function fixture() {
        return $this->belongsTo(Fixture::class);
    }

}
