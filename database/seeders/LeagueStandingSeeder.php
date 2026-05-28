<?php

namespace Database\Seeders;

use App\Domain\Sports\Models\Club;
use App\Domain\Sports\Models\LeagueStanding;
use Illuminate\Database\Seeder;

class LeagueStandingSeeder extends Seeder
{
    public function run(): void
    {
        $season      = '2026';
        $competition = 'LPF';

        $rows = [
            ['club' => 'Veraguas United FC', 'pos' => 1, 'pj' => 14, 'g' => 10, 'e' => 2, 'p' => 2, 'gf' => 28, 'gc' => 12],
            ['club' => 'Tauro FC',           'pos' => 2, 'pj' => 14, 'g' => 9,  'e' => 2, 'p' => 3, 'gf' => 25, 'gc' => 14],
            ['club' => 'CAI Panama',         'pos' => 3, 'pj' => 14, 'g' => 8,  'e' => 2, 'p' => 4, 'gf' => 22, 'gc' => 16],
            ['club' => 'Plaza Amador',       'pos' => 4, 'pj' => 13, 'g' => 7,  'e' => 3, 'p' => 3, 'gf' => 20, 'gc' => 15],
            ['club' => 'Alianza FC',         'pos' => 5, 'pj' => 14, 'g' => 6,  'e' => 3, 'p' => 5, 'gf' => 18, 'gc' => 18],
            ['club' => 'UMECIT FC',          'pos' => 6, 'pj' => 14, 'g' => 5,  'e' => 2, 'p' => 7, 'gf' => 14, 'gc' => 20],
            ['club' => 'Herrera FC',         'pos' => 7, 'pj' => 13, 'g' => 3,  'e' => 2, 'p' => 8, 'gf' => 11, 'gc' => 22],
            ['club' => 'Atletico Chiriqui',  'pos' => 8, 'pj' => 13, 'g' => 2,  'e' => 1, 'p' => 10,'gf' => 8,  'gc' => 28],
        ];

        foreach ($rows as $row) {
            $club = Club::query()->where('name', $row['club'])->first();
            if (! $club) {
                continue;
            }

            LeagueStanding::query()->updateOrCreate(
                ['club_id' => $club->id, 'competition' => $competition, 'season' => $season],
                [
                    'position'        => $row['pos'],
                    'played'          => $row['pj'],
                    'won'             => $row['g'],
                    'drawn'           => $row['e'],
                    'lost'            => $row['p'],
                    'goals_for'       => $row['gf'],
                    'goals_against'   => $row['gc'],
                    'goal_difference' => $row['gf'] - $row['gc'],
                    'points'          => ($row['g'] * 3) + $row['e'],
                    'is_active'       => true,
                ],
            );
        }
    }
}
