<?php

namespace Tests\Feature\Admin\Sports;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Sports\Models\Club;
use App\Domain\Sports\Models\MatchGoal;
use App\Domain\Ticketing\Models\MatchEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchGoalAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_goals_for_match(): void
    {
        $admin      = $this->createAdminWithPermissions(['match_events.view']);
        $club       = Club::factory()->create();
        $matchEvent = MatchEvent::factory()->create();
        $goal       = MatchGoal::factory()->create([
            'match_event_id' => $matchEvent->id,
            'club_id'        => $club->id,
            'scorer_name'    => 'Juan Perez',
            'minute'         => 45,
        ]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/match-events/{$matchEvent->id}/goals")
            ->assertOk()
            ->assertSee('Juan Perez');
    }

    public function test_admin_can_add_goal_to_match(): void
    {
        $admin      = $this->createAdminWithPermissions(['match_events.view', 'match_goals.manage']);
        $club       = Club::factory()->create();
        $matchEvent = MatchEvent::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post("/admin/match-events/{$matchEvent->id}/goals", [
                'club_id'     => $club->id,
                'scorer_name' => 'Carlos Lopez',
                'minute'      => 67,
                'is_own_goal' => '0',
                'is_penalty'  => '0',
                'sort_order'  => '0',
            ])
            ->assertRedirect("/admin/match-events/{$matchEvent->id}/goals");

        $this->assertDatabaseHas('match_goals', [
            'match_event_id' => $matchEvent->id,
            'scorer_name'    => 'Carlos Lopez',
            'minute'         => 67,
        ]);
    }

    public function test_admin_can_delete_goal(): void
    {
        $admin      = $this->createAdminWithPermissions(['match_events.view', 'match_goals.manage']);
        $club       = Club::factory()->create();
        $matchEvent = MatchEvent::factory()->create();
        $goal       = MatchGoal::factory()->create(['match_event_id' => $matchEvent->id, 'club_id' => $club->id]);

        $this->actingAs($admin, 'admin')
            ->delete("/admin/match-events/{$matchEvent->id}/goals/{$goal->id}")
            ->assertRedirect("/admin/match-events/{$matchEvent->id}/goals");

        $this->assertDatabaseMissing('match_goals', ['id' => $goal->id]);
    }

    private function createAdminWithPermissions(array $names): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create(['name' => 'goal-role-' . fake()->unique()->slug(), 'label' => 'Goal Role']);

        foreach ($names as $name) {
            $perm = Permission::firstOrCreate(
                ['name' => $name],
                ['label' => str($name)->replace('.', ' ')->title()->toString()],
            );
            $role->permissions()->syncWithoutDetaching([$perm->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
