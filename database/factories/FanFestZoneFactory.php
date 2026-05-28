<?php

namespace Database\Factories;

use App\Domain\FanFest\Models\FanFestEvent;
use App\Domain\FanFest\Models\FanFestZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FanFestZone>
 */
class FanFestZoneFactory extends Factory
{
    protected $model = FanFestZone::class;

    private static int $seq = 0;

    public function definition(): array
    {
        self::$seq++;

        return [
            'fan_fest_event_id' => FanFestEvent::factory()->active(),
            'name'              => fake()->randomElement(['Zona Familiar', 'Zona Gastronómica', 'Zona Deportiva', 'Zona Cultural', 'Zona VIP']),
            'description'       => fake()->sentence(),
            'icon'              => fake()->randomElement(['family_restroom', 'restaurant', 'sports_soccer', 'music_note', 'star']),
            'sort_order'        => self::$seq,
            'is_active'         => true,
            'metadata'          => null,
        ];
    }

    public function inactive(): self
    {
        return $this->state(['is_active' => false]);
    }
}
