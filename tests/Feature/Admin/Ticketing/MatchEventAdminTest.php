<?php

namespace Tests\Feature\Admin\Ticketing;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Ticketing\Models\MatchEvent;
use App\Domain\Ticketing\Models\TicketZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchEventAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_permission_can_view_match_events(): void
    {
        $admin = $this->createAdminWithPermissions(['match_events.view']);
        $matchEvent = MatchEvent::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/match-events')
            ->assertOk()
            ->assertSee('Match Events')
            ->assertSee($matchEvent->code);
    }

    public function test_admin_without_permission_cannot_view_match_events(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/match-events')
            ->assertForbidden();
    }

    public function test_admin_can_create_and_edit_match_event(): void
    {
        $admin = $this->createAdminWithPermissions(['match_events.view', 'match_events.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/match-events', [
                'code' => 'LPF-J13-VU-PLA-20261101',
                'home_team' => 'VERAGUAS UNITED',
                'away_team' => 'PLAZA AMADOR',
                'competition' => 'LPF',
                'round_label' => 'JORNADA 13',
                'match_date' => '2026-11-01 19:00:00',
                'stadium_name' => 'ESTADIO ATALAYA',
                'stadium_location' => 'VERAGUAS',
                'status' => 'scheduled',
                'home_score' => '',
                'away_score' => '',
                'is_featured' => '0',
                'is_active' => '1',
            ])
            ->assertRedirect('/admin/match-events');

        $matchEvent = MatchEvent::query()->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->put("/admin/match-events/{$matchEvent->id}", [
                'code' => $matchEvent->code,
                'home_team' => 'VERAGUAS UNITED',
                'away_team' => 'PLAZA AMADOR',
                'competition' => 'LPF',
                'round_label' => 'JORNADA 13',
                'match_date' => '2026-11-01 20:00:00',
                'stadium_name' => 'ESTADIO ATALAYA',
                'stadium_location' => 'VERAGUAS',
                'status' => 'live',
                'home_score' => 1,
                'away_score' => 0,
                'is_featured' => '1',
                'is_active' => '1',
            ])
            ->assertRedirect('/admin/match-events');

        $this->assertDatabaseHas('match_events', [
            'id' => $matchEvent->id,
            'status' => 'live',
            'home_score' => 1,
            'is_featured' => 1,
        ]);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $admin = $this->createAdminWithPermissions(['match_events.view', 'match_events.manage']);

        $this->from('/admin/match-events/create')
            ->actingAs($admin, 'admin')
            ->post('/admin/match-events', [
                'home_team' => 'VERAGUAS UNITED',
                'away_team' => 'CAI PANAMA',
                'match_date' => '2026-10-24 20:00:00',
                'status' => 'invalid',
            ])
            ->assertRedirect('/admin/match-events/create')
            ->assertSessionHasErrors('status');
    }

    public function test_admin_can_mark_match_event_as_featured(): void
    {
        $admin = $this->createAdminWithPermissions(['match_events.view', 'match_events.manage']);
        $matchEvent = MatchEvent::factory()->create(['is_featured' => false]);

        $this->actingAs($admin, 'admin')
            ->put("/admin/match-events/{$matchEvent->id}", [
                'code' => $matchEvent->code,
                'home_team' => $matchEvent->home_team,
                'away_team' => $matchEvent->away_team,
                'competition' => $matchEvent->competition,
                'round_label' => $matchEvent->round_label,
                'match_date' => $matchEvent->match_date?->format('Y-m-d H:i:s'),
                'stadium_name' => $matchEvent->stadium_name,
                'stadium_location' => $matchEvent->stadium_location,
                'status' => $matchEvent->status,
                'home_score' => $matchEvent->home_score,
                'away_score' => $matchEvent->away_score,
                'is_featured' => '1',
                'is_active' => $matchEvent->is_active ? '1' : '0',
            ])
            ->assertRedirect('/admin/match-events');

        $this->assertTrue($matchEvent->fresh()->is_featured);
    }

    public function test_match_event_with_zones_cannot_be_deleted(): void
    {
        $admin = $this->createAdminWithPermissions(['match_events.view', 'match_events.manage']);
        $matchEvent = MatchEvent::factory()->create();
        TicketZone::factory()->create(['match_event_id' => $matchEvent->id]);

        $this->actingAs($admin, 'admin')
            ->delete("/admin/match-events/{$matchEvent->id}")
            ->assertRedirect('/admin/match-events');

        $this->assertDatabaseHas('match_events', [
            'id' => $matchEvent->id,
        ]);
    }

    private function createAdminWithPermissions(array $permissionNames): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role = Role::create([
            'name' => 'ticketing-match-role-' . fake()->unique()->slug(),
            'label' => 'Ticketing Match Role',
        ]);

        foreach ($permissionNames as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                ['label' => str($permissionName)->replace('.', ' ')->title()->toString()],
            );

            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
