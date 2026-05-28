<?php

namespace Tests\Feature\Api;

use App\Domain\Stadium\Models\Stadium;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StadiumApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_stadium_api_returns_active_stadium(): void
    {
        $stadium = Stadium::factory()->create(['is_active' => true]);

        $data = $this->getJson('/api/stadium')->assertOk()->json('data');

        $this->assertEquals($stadium->name, $data['name']);
        $this->assertEquals($stadium->location, $data['location']);
        $this->assertEquals($stadium->capacity, $data['capacity']);
    }

    public function test_stadium_api_returns_404_when_no_active_stadium(): void
    {
        Stadium::factory()->create(['is_active' => false]);

        $this->getJson('/api/stadium')->assertNotFound();
    }

    public function test_stadium_api_returns_zones_and_rules(): void
    {
        Stadium::factory()->create(['is_active' => true]);

        $data = $this->getJson('/api/stadium')->assertOk()->json('data');

        $this->assertIsArray($data['zones']);
        $this->assertIsArray($data['rules']);
        $this->assertIsArray($data['matchday']);
        $this->assertNotEmpty($data['zones']);
    }

    public function test_stadium_api_returns_metadata(): void
    {
        Stadium::factory()->create([
            'is_active' => true,
            'metadata'  => ['cta_title' => 'VEN AL INDIO', 'cta_action_href' => '/boletos'],
        ]);

        $data = $this->getJson('/api/stadium')->assertOk()->json('data');

        $this->assertEquals('VEN AL INDIO', $data['metadata']['cta_title']);
    }

    public function test_inactive_stadium_is_not_returned(): void
    {
        Stadium::factory()->create(['is_active' => false, 'name' => 'Estadio Inactivo']);

        $this->getJson('/api/stadium')->assertNotFound();
    }
}
