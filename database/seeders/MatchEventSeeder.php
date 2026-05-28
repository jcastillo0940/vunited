<?php

namespace Database\Seeders;

use App\Domain\Ticketing\Models\MatchEvent;
use Illuminate\Database\Seeder;

class MatchEventSeeder extends Seeder
{
    public function run(): void
    {
        $matches = [
            [
                'code' => 'LPF-J12-VU-CAI-20261024',
                'home_team' => 'VERAGUAS UNITED',
                'away_team' => 'CAI PANAMA',
                'competition' => 'LPF',
                'round_label' => 'JORNADA 12',
                'match_date' => '2026-10-24 20:00:00',
                'stadium_name' => 'ESTADIO ATALAYA',
                'stadium_location' => 'VERAGUAS',
                'status' => 'scheduled',
                'home_score' => null,
                'away_score' => null,
                'is_featured' => true,
                'is_active' => true,
                'metadata' => [
                    'home_logo_label' => 'VUFC',
                    'away_logo_label' => 'CAI',
                ],
            ],
            [
                'code' => 'LPF-J11-VU-TAU-20261017',
                'home_team' => 'VERAGUAS UNITED',
                'away_team' => 'TAURO FC',
                'competition' => 'LPF',
                'round_label' => 'JORNADA 11',
                'match_date' => '2026-10-17 18:00:00',
                'stadium_name' => 'ESTADIO ATALAYA',
                'stadium_location' => 'VERAGUAS',
                'status' => 'finished',
                'home_score' => 2,
                'away_score' => 1,
                'is_featured' => false,
                'is_active' => true,
                'metadata' => [
                    'home_logo_label' => 'VUFC',
                    'away_logo_label' => 'TAU',
                ],
            ],
        ];

        foreach ($matches as $match) {
            MatchEvent::query()->updateOrCreate(
                ['code' => $match['code']],
                $match,
            );
        }
    }
}
