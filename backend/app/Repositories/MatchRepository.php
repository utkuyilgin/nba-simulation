<?php

namespace App\Repositories;

use App\Models\Matches;
use App\Repositories\Interfaces\MatchRepositoryInterface;

class MatchRepository implements MatchRepositoryInterface
{
    protected $model;

    public function __construct(Matches $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function update($id, array $attributes)
    {
        $match = $this->model->find($id);
        $match->update($attributes);
        return $match;
    }

    public function getWithPlayerStats($id)
    {
        return $this->model->with('playerStats.player')->find($id);
    }

    public function finalizeMatch($match)
    {
        $match->status = 'completed';
        $match->save();
        return $match;
    }

    public function updateTeamStats($match)
    {
        return true;
    }

    public function getAll()
    {
        return $this->model->with(['team1', 'team2', 'playerStats.player'])->get();
    }

    public function getByWeek($week)
    {
        return $this->model->where('week', $week)
            ->with(['team1', 'team2', 'playerStats.player'])
            ->get();
    }

    public function truncate()
    {
        return $this->model->query()->delete();
    }
}
