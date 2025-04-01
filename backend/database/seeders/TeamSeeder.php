<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Player;

class TeamSeeder extends Seeder
{
    public function run()
    {
        $teams = [
            ['name' => 'Los Angeles Lakers', 'city' => 'Los Angeles'],
            ['name' => 'Golden State Warriors', 'city' => 'San Francisco'],
            ['name' => 'Chicago Bulls', 'city' => 'Chicago'],
            ['name' => 'Miami Heat', 'city' => 'Miami'],
            ['name' => 'Boston Celtics', 'city' => 'Boston'],
            ['name' => 'Brooklyn Nets', 'city' => 'Brooklyn'],
            ['name' => 'Milwaukee Bucks', 'city' => 'Milwaukee'],
            ['name' => 'Dallas Mavericks', 'city' => 'Dallas'],
        ];

        // Takımları ekle
        foreach ($teams as $teamData) {
            $team = Team::create([
                'name' => $teamData['name'],
                'city' => $teamData['city']
            ]);

            // Her takım için 15 oyuncu ekle
            for ($i = 1; $i <= 15; $i++) {
                Player::create([
                    'name' => "Player $i",
                    'team_id' => $team->id,
                    'position' => $this->getRandomPosition(),
                ]);
            }
        }
    }

    /**
     * Random player position for the player.
     *
     * @return string
     */
    private function getRandomPosition()
    {
        $positions = ['PG', 'SG', 'SF', 'PF', 'C'];
        return $positions[array_rand($positions)];
    }
}
