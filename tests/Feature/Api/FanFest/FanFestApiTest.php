<?php

namespace Tests\Feature\Api\FanFest;

use App\Domain\FanFest\Models\FanFestEvent;
use App\Domain\FanFest\Models\FanFestZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FanFestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_null_when_no_active_event(): void
    {
        FanFestEvent::factory()->count(2)->create(['is_active' => false]);

        $this->getJson('/api/fanfest')
            ->assertOk()
            ->assertJsonPath('data', null);
    }

    public function test_returns_active_event(): void
    {
        $event = FanFestEvent::factory()->active()->create([
            'title'    => 'FanFest Veraguas 2026',
            'location' => 'Estadio Muquita',
        ]);

        $data = $this->getJson('/api/fanfest')->json('data');

        $this->assertNotNull($data);
        $this->assertSame($event->id, $data['id']);
        $this->assertSame('FanFest Veraguas 2026', $data['title']);
        $this->assertSame('Estadio Muquita', $data['location']);
    }

    public function test_response_includes_required_fields(): void
    {
        FanFestEvent::factory()->active()->create();

        $data = $this->getJson('/api/fanfest')->json('data');

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('slug', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('event_date', $data);
        $this->assertArrayHasKey('location', $data);
        $this->assertArrayHasKey('hero_image_path', $data);
        $this->assertArrayHasKey('schedule', $data);
        $this->assertArrayHasKey('is_active', $data);
        $this->assertArrayHasKey('zones', $data);
    }

    public function test_includes_active_zones(): void
    {
        $event = FanFestEvent::factory()->active()->create();
        FanFestZone::factory()->count(3)->create(['fan_fest_event_id' => $event->id, 'is_active' => true]);
        FanFestZone::factory()->inactive()->create(['fan_fest_event_id' => $event->id]);

        $data = $this->getJson('/api/fanfest')->json('data');

        $this->assertCount(3, $data['zones']);
    }

    public function test_zone_fields_present(): void
    {
        $event = FanFestEvent::factory()->active()->create();
        FanFestZone::factory()->create([
            'fan_fest_event_id' => $event->id,
            'name'              => 'Zona Familiar',
            'icon'              => 'family_restroom',
            'is_active'         => true,
        ]);

        $zone = $this->getJson('/api/fanfest')->json('data.zones.0');

        $this->assertArrayHasKey('id', $zone);
        $this->assertArrayHasKey('name', $zone);
        $this->assertArrayHasKey('description', $zone);
        $this->assertArrayHasKey('icon', $zone);
        $this->assertArrayHasKey('sort_order', $zone);
        $this->assertSame('Zona Familiar', $zone['name']);
        $this->assertSame('family_restroom', $zone['icon']);
    }

    public function test_schedule_json_is_array(): void
    {
        FanFestEvent::factory()->active()->create([
            'schedule' => [
                ['time' => '16:00', 'activity' => 'Apertura'],
                ['time' => '19:00', 'activity' => 'Partido'],
            ],
        ]);

        $schedule = $this->getJson('/api/fanfest')->json('data.schedule');

        $this->assertIsArray($schedule);
        $this->assertCount(2, $schedule);
        $this->assertSame('16:00', $schedule[0]['time']);
    }

    public function test_inactive_event_not_returned(): void
    {
        FanFestEvent::factory()->create(['is_active' => false, 'title' => 'Inactivo']);

        $data = $this->getJson('/api/fanfest')->json('data');

        $this->assertNull($data);
    }

    public function test_zones_ordered_by_sort_order(): void
    {
        $event = FanFestEvent::factory()->active()->create();
        FanFestZone::factory()->create(['fan_fest_event_id' => $event->id, 'name' => 'Tercera', 'sort_order' => 3, 'is_active' => true]);
        FanFestZone::factory()->create(['fan_fest_event_id' => $event->id, 'name' => 'Primera', 'sort_order' => 1, 'is_active' => true]);
        FanFestZone::factory()->create(['fan_fest_event_id' => $event->id, 'name' => 'Segunda', 'sort_order' => 2, 'is_active' => true]);

        $names = $this->getJson('/api/fanfest')->json('data.zones.*.name');

        $this->assertSame(['Primera', 'Segunda', 'Tercera'], $names);
    }
}
