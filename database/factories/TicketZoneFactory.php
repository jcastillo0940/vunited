<?php

namespace Database\Factories;

use App\Domain\Ticketing\Models\MatchEvent;
use App\Domain\Ticketing\Models\TicketZone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TicketZone>
 */
class TicketZoneFactory extends Factory
{
    protected $model = TicketZone::class;

    public function definition(): array
    {
        $name = fake()->randomElement(['General', 'Preferencial', 'VIP']);

        return [
            'match_event_id' => MatchEvent::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'price' => fake()->randomElement(['5.00', '12.00', '25.00']),
            'currency' => 'USD',
            'capacity' => fake()->numberBetween(100, 1000),
            'available_quantity' => fake()->numberBetween(10, 500),
            'sort_order' => 0,
            'is_active' => true,
            'metadata' => null,
        ];
    }
}
