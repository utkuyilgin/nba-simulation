<?php

namespace App\Repositories;

use App\Models\Player;
use App\Repositories\Interfaces\PlayerRepositoryInterface;

class PlayerRepository implements PlayerRepositoryInterface
{
    protected $model;

    public function __construct(Player $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findByTeam($teamId)
    {
        return $this->model->where('team_id', $teamId)->get();
    }

    public function update($id, array $attributes)
    {
        $player = $this->model->find($id);
        $player->update($attributes);
        return $player;
    }

    public function getRandomFromTeam($teamId)
    {
        return $this->model->where('team_id', $teamId)
            ->inRandomOrder()
            ->first();
    }

    public function getRandomFromTeamExcept($teamId, $exceptId)
    {
        return $this->model->where('team_id', $teamId)
            ->where('id', '!=', $exceptId)
            ->inRandomOrder()
            ->first();
    }
}
