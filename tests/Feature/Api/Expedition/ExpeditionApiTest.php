<?php

namespace Tests\Feature\Api\Expedition;

use App\Domain\Expedition\Models\BusTrip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpeditionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_empty_when_no_active_trips(): void
    {
        BusTrip::factory()->inactive()->count(2)->create([
            'departure_time' => now()->addDays(10),
        ]);

        $this->getJson('/api/expeditions')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_returns_only_future_active_trips(): void
    {
        BusTrip::factory()->create(['is_active' => true,  'departure_time' => now()->addDays(5)]);
        BusTrip::factory()->create(['is_active' => true,  'departure_time' => now()->subDays(1)]); // past
        BusTrip::factory()->inactive()->create(['departure_time' => now()->addDays(10)]);

        $this->getJson('/api/expeditions')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_response_includes_required_fields(): void
    {
        BusTrip::factory()->create([
            'title'              => 'Expedición Copa',
            'departure_location' => 'Santiago',
            'price'              => '12.00',
            'capacity'           => 40,
            'available_seats'    => 25,
            'is_active'          => true,
            'departure_time'     => now()->addDays(7),
        ]);

        $data = $this->getJson('/api/expeditions')->json('data.0');

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('departure_location', $data);
        $this->assertArrayHasKey('departure_time', $data);
        $this->assertArrayHasKey('return_time', $data);
        $this->assertArrayHasKey('price', $data);
        $this->assertArrayHasKey('currency', $data);
        $this->assertArrayHasKey('capacity', $data);
        $this->assertArrayHasKey('available_seats', $data);
        $this->assertArrayHasKey('is_available', $data);
        $this->assertArrayHasKey('metadata', $data);
        $this->assertArrayHasKey('match', $data);
        $this->assertSame('Expedición Copa', $data['title']);
        $this->assertSame('12.00', $data['price']);
    }

    public function test_is_available_true_when_seats_remain(): void
    {
        BusTrip::factory()->create([
            'is_active'       => true,
            'available_seats' => 10,
            'departure_time'  => now()->addDays(5),
        ]);

        $data = $this->getJson('/api/expeditions')->json('data.0');

        $this->assertTrue($data['is_available']);
    }

    public function test_is_available_false_when_sold_out(): void
    {
        BusTrip::factory()->soldOut()->create([
            'is_active'      => true,
            'departure_time' => now()->addDays(5),
        ]);

        $data = $this->getJson('/api/expeditions')->json('data.0');

        $this->assertFalse($data['is_available']);
    }

    public function test_ordered_by_departure_time(): void
    {
        BusTrip::factory()->create(['title' => 'Tercero', 'departure_time' => now()->addDays(15), 'is_active' => true]);
        BusTrip::factory()->create(['title' => 'Primero', 'departure_time' => now()->addDays(3),  'is_active' => true]);
        BusTrip::factory()->create(['title' => 'Segundo', 'departure_time' => now()->addDays(8),  'is_active' => true]);

        $titles = $this->getJson('/api/expeditions')->json('data.*.title');

        $this->assertSame(['Primero', 'Segundo', 'Tercero'], $titles);
    }

    public function test_match_null_when_no_match_linked(): void
    {
        BusTrip::factory()->create([
            'match_event_id' => null,
            'is_active'      => true,
            'departure_time' => now()->addDays(5),
        ]);

        $data = $this->getJson('/api/expeditions')->json('data.0');

        $this->assertNull($data['match']);
    }
}
