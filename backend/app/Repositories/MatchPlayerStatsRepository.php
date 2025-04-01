<?php

namespace App\Repositories;

use App\Models\MatchPlayerStats;
use App\Repositories\Interfaces\MatchPlayerStatsRepositoryInterface;

class MatchPlayerStatsRepository implements MatchPlayerStatsRepositoryInterface
{
    protected $model;

    public function __construct(MatchPlayerStats $model)
    {
        $this->model = $model;
    }

    public function firstOrNew(array $attributes)
    {
        return $this->model->firstOrNew($attributes);
    }

    public function getByMatch($matchId)
    {
        return $this->model->where('match_id', $matchId)->get();
    }

    public function truncate()
    {
      return $this->model->query()->delete();
    }
}
