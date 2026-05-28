<?php

namespace Database\Factories;

use App\Domain\Ticketing\Models\MatchEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MatchEvent>
 */
class MatchEventFactory extends Factory
{
    protected $model = MatchEvent::class;

    public function definition(): array
    {
        $homeTeam = 'Veraguas United';
        $awayTeam = fake()->randomElement(['CAI Panama', 'Tauro FC', 'Plaza Amador']);
        $date = fake()->dateTimeBetween('+3 days', '+30 days');

        return [
            'code' => Str::upper(Str::slug($homeTeam . '-' . $awayTeam . '-' . $date->format('Ymd'))),
            'home_team' => $homeTeam,
            'away_team' => $awayTeam,
            'competition' => 'LPF Apertura',
            'round_label' => 'Jornada ' . fake()->numberBetween(1, 18),
            'match_date' => $date,
            'stadium_name' => 'Estadio Atalaya',
            'stadium_location' => 'Veraguas',
            'status' => 'scheduled',
            'home_score' => null,
            'away_score' => null,
            'is_featured' => false,
            'is_active' => true,
            'metadata' => null,
        ];
    }
}
