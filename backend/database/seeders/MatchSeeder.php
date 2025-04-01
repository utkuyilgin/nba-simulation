<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matches;
use App\Models\Team;

class MatchSeeder extends Seeder
{
    public function run()
    {
        $teams = Team::all();

        if ($teams->count() < 2) {
            $this->command->warn("Yeterli takım yok, en az 2 takım olmalı.");
            return;
        }

        $teams = $teams->shuffle(); // Takımları rastgele sırala

        for ($i = 0; $i < $teams->count(); $i += 2) {
            if (!isset($teams[$i + 1])) break; // Tek takım kaldıysa, eşleştirme yapma

            Matches::create([
                'team1_id' => $teams[$i]->id,
                'team2_id' => $teams[$i + 1]->id,
                'score_team1' => 0,
                'score_team2' => 0,
                'status' => 'not_started',
            ]);
        }
    }
}
