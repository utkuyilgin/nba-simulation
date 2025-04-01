<?php

namespace App\Repositories;

use App\Models\Team;
use App\Repositories\Interfaces\TeamRepositoryInterface;

class TeamRepository implements TeamRepositoryInterface
{
    protected $model;

    public function __construct(Team $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }
}
