<?php

namespace Tests\Feature\Api;

use App\Domain\Sports\Models\Club;
use App\Domain\Sports\Models\LeagueStanding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_standings_returns_active_rows_ordered_by_position(): void
    {
        $club1 = Club::factory()->create(['name' => 'Veraguas United FC']);
        $club2 = Club::factory()->create(['name' => 'Tauro FC']);

        LeagueStanding::factory()->create(['club_id' => $club1->id, 'position' => 1, 'points' => 32, 'season' => '2026', 'competition' => 'LPF', 'is_active' => true]);
        LeagueStanding::factory()->create(['club_id' => $club2->id, 'position' => 2, 'points' => 29, 'season' => '2026', 'competition' => 'LPF', 'is_active' => true]);

        $data = $this->getJson('/api/standings')->assertOk()->json('data');

        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]['position']);
        $this->assertEquals('Veraguas United FC', $data[0]['club']['name']);
    }

    public function test_standings_filters_by_season_and_competition(): void
    {
        $club = Club::factory()->create();
        LeagueStanding::factory()->create(['club_id' => $club->id, 'season' => '2025', 'competition' => 'LPF', 'is_active' => true, 'position' => 1]);
        LeagueStanding::factory()->create(['club_id' => $club->id, 'season' => '2026', 'competition' => 'LPF', 'is_active' => true, 'position' => 1]);

        $data = $this->getJson('/api/standings?season=2025')->assertOk()->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('2025', $data[0]['season']);
    }

    public function test_inactive_standings_are_excluded(): void
    {
        $club = Club::factory()->create();
        LeagueStanding::factory()->create(['club_id' => $club->id, 'is_active' => false, 'season' => date('Y'), 'competition' => 'LPF', 'position' => 1]);

        $this->getJson('/api/standings')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_clubs_api_returns_active_clubs(): void
    {
        Club::factory()->create(['is_active' => true, 'name' => 'Veraguas United FC']);
        Club::factory()->create(['is_active' => false, 'name' => 'Inactive Club']);

        $data = $this->getJson('/api/clubs')->assertOk()->json('data');

        $this->assertCount(1, $data);
        $this->assertEquals('Veraguas United FC', $data[0]['name']);
    }
}
