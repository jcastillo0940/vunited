<?php

namespace Database\Factories;

use App\Domain\Sports\Models\MatchGoal;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MatchGoal> */
class MatchGoalFactory extends Factory
{
    protected $model = MatchGoal::class;

    public function definition(): array
    {
        return [
            'match_event_id' => \App\Domain\Ticketing\Models\MatchEvent::factory(),
            'club_id'        => \App\Domain\Sports\Models\Club::factory(),
            'player_id'      => null,
            'scorer_name'    => fake()->name(),
            'minute'         => fake()->numberBetween(1, 90),
            'is_own_goal'    => false,
            'is_penalty'     => false,
            'sort_order'     => fake()->numberBetween(0, 10),
        ];
    }
}
