<?php

namespace Database\Factories;

use App\Domain\Expedition\Models\BusTrip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusTrip>
 */
class BusTripFactory extends Factory
{
    protected $model = BusTrip::class;

    public function definition(): array
    {
        $capacity = fake()->numberBetween(20, 50);

        return [
            'title'              => 'Expedición India — ' . fake()->city(),
            'match_event_id'     => null,
            'departure_location' => 'Santiago de Veraguas, Terminal de Buses',
            'departure_time'     => fake()->dateTimeBetween('now', '+3 months'),
            'return_time'        => fake()->dateTimeBetween('+4 hours', '+6 months'),
            'price'              => fake()->randomElement(['10.00', '12.00', '15.00', '20.00']),
            'currency'           => 'USD',
            'capacity'           => $capacity,
            'available_seats'    => fake()->numberBetween(0, $capacity),
            'is_active'          => true,
            'metadata'           => null,
        ];
    }

    public function soldOut(): self
    {
        return $this->state(['available_seats' => 0]);
    }

    public function inactive(): self
    {
        return $this->state(['is_active' => false]);
    }
}
