<?php

namespace Tests\Feature\Api;

use App\Domain\Sports\Models\Club;
use App\Domain\Ticketing\Models\MatchEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_matches_index_returns_active_matches(): void
    {
        MatchEvent::factory()->create(['is_active' => true, 'status' => 'scheduled']);
        MatchEvent::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/matches');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_matches_index_filters_by_status(): void
    {
        MatchEvent::factory()->create(['is_active' => true, 'status' => 'finished']);
        MatchEvent::factory()->create(['is_active' => true, 'status' => 'scheduled']);

        $response = $this->getJson('/api/matches?status=finished');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('finished', $response->json('data.0.status'));
    }

    public function test_matches_featured_returns_featured_future_match(): void
    {
        MatchEvent::factory()->create([
            'is_active'  => true,
            'is_featured' => true,
            'status'     => 'scheduled',
            'match_date' => now()->addDays(5),
        ]);

        $response = $this->getJson('/api/matches/featured');
        $response->assertOk()->assertJsonPath('data.is_featured', true);
    }

    public function test_matches_featured_returns_404_when_none(): void
    {
        $this->getJson('/api/matches/featured')->assertNotFound();
    }

    public function test_matches_show_returns_match_by_code(): void
    {
        $match = MatchEvent::factory()->create([
            'code'      => 'TEST-CODE-001',
            'is_active' => true,
        ]);

        $this->getJson('/api/matches/TEST-CODE-001')
            ->assertOk()
            ->assertJsonPath('data.code', 'TEST-CODE-001');
    }

    public function test_matches_show_returns_404_for_unknown_code(): void
    {
        $this->getJson('/api/matches/NONEXISTENT')->assertNotFound();
    }

    public function test_match_resource_includes_club_data_when_linked(): void
    {
        $homeClub = Club::factory()->create(['name' => 'Veraguas United FC', 'short_name' => 'VUA', 'slug' => 'veraguas-united-fc']);
        $awayClub = Club::factory()->create(['name' => 'Tauro FC', 'short_name' => 'TAU', 'slug' => 'tauro-fc']);

        MatchEvent::factory()->create([
            'is_active'    => true,
            'home_club_id' => $homeClub->id,
            'away_club_id' => $awayClub->id,
            'status'       => 'scheduled',
        ]);

        $data = $this->getJson('/api/matches')->json('data.0');

        $this->assertEquals('VUA', $data['home_club']['short_name']);
        $this->assertEquals('TAU', $data['away_club']['short_name']);
    }
}
