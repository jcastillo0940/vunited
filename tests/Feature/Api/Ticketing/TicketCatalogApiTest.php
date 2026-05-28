<?php

namespace Tests\Feature\Api\Ticketing;

use App\Domain\Ticketing\Models\MatchEvent;
use App\Domain\Ticketing\Models\TicketZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketCatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_only_active_matches(): void
    {
        MatchEvent::factory()->create([
            'code' => 'ACTIVE-MATCH',
            'is_active' => true,
        ]);
        MatchEvent::factory()->create([
            'code' => 'INACTIVE-MATCH',
            'is_active' => false,
        ]);

        $this->getJson('/api/ticketing/matches')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', 'ACTIVE-MATCH');
    }

    public function test_api_returns_featured_match_with_active_zones(): void
    {
        $matchEvent = MatchEvent::factory()->create([
            'code' => 'FEATURED-MATCH',
            'is_featured' => true,
            'is_active' => true,
            'match_date' => now()->addDays(3),
        ]);

        TicketZone::factory()->create([
            'match_event_id' => $matchEvent->id,
            'slug' => 'general',
            'is_active' => true,
        ]);
        TicketZone::factory()->create([
            'match_event_id' => $matchEvent->id,
            'slug' => 'vip',
            'is_active' => false,
        ]);

        $this->getJson('/api/ticketing/matches/featured')
            ->assertOk()
            ->assertJsonPath('data.code', 'FEATURED-MATCH')
            ->assertJsonCount(1, 'data.zones');
    }

    public function test_api_returns_active_zones_only(): void
    {
        $matchEvent = MatchEvent::factory()->create([
            'code' => 'MATCH-WITH-ZONES',
            'is_active' => true,
        ]);

        TicketZone::factory()->create([
            'match_event_id' => $matchEvent->id,
            'slug' => 'general',
            'is_active' => true,
        ]);
        TicketZone::factory()->create([
            'match_event_id' => $matchEvent->id,
            'slug' => 'vip',
            'is_active' => false,
        ]);

        $this->getJson('/api/ticketing/matches/MATCH-WITH-ZONES/zones')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'general');
    }

    public function test_api_match_detail_by_code_works(): void
    {
        $matchEvent = MatchEvent::factory()->create([
            'code' => 'MATCH-DETAIL',
            'is_active' => true,
        ]);

        $this->getJson('/api/ticketing/matches/MATCH-DETAIL')
            ->assertOk()
            ->assertJsonPath('data.code', 'MATCH-DETAIL')
            ->assertJsonPath('data.home_team', $matchEvent->home_team);
    }

    public function test_api_returns_controlled_404_if_match_code_not_found(): void
    {
        $this->getJson('/api/ticketing/matches/NO-EXISTE')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'Partido no encontrado.',
            ]);
    }

    public function test_api_can_filter_matches_by_status(): void
    {
        MatchEvent::factory()->create([
            'code' => 'SCHEDULED-MATCH',
            'status' => 'scheduled',
            'is_active' => true,
        ]);
        MatchEvent::factory()->create([
            'code' => 'FINISHED-MATCH',
            'status' => 'finished',
            'is_active' => true,
        ]);

        $this->getJson('/api/ticketing/matches?status=scheduled')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', 'SCHEDULED-MATCH');
    }
}
