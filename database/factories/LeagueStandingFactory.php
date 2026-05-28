<?php

namespace Database\Factories;

use App\Domain\Sports\Models\LeagueStanding;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<LeagueStanding> */
class LeagueStandingFactory extends Factory
{
    protected $model = LeagueStanding::class;

    public function definition(): array
    {
        $won   = fake()->numberBetween(0, 14);
        $drawn = fake()->numberBetween(0, 14 - $won);
        $lost  = 14 - $won - $drawn;
        $gf    = fake()->numberBetween(0, 30);
        $ga    = fake()->numberBetween(0, 30);

        return [
            'club_id'         => \App\Domain\Sports\Models\Club::factory(),
            'competition'     => 'LPF',
            'season'          => '2026',
            'position'        => fake()->numberBetween(1, 10),
            'played'          => 14,
            'won'             => $won,
            'drawn'           => $drawn,
            'lost'            => $lost,
            'goals_for'       => $gf,
            'goals_against'   => $ga,
            'goal_difference' => $gf - $ga,
            'points'          => ($won * 3) + $drawn,
            'is_active'       => true,
        ];
    }
}
