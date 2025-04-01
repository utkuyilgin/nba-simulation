<?php

namespace App\Repositories\Interfaces;

interface PlayerRepositoryInterface
{
    public function findByTeam($teamId);
    public function find($id);
    public function update($id, array $attributes);
    public function getRandomFromTeam($teamId);
    public function getRandomFromTeamExcept($teamId, $exceptId);
}
