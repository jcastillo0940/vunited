<?php

namespace Tests\Feature\Admin\Sports;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Sports\Models\Club;
use App\Domain\Sports\Models\LeagueStanding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeagueStandingAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_standings(): void
    {
        $admin   = $this->createAdminWithPermissions(['standings.view']);
        $club    = Club::factory()->create(['name' => 'Tauro FC']);
        LeagueStanding::factory()->create(['club_id' => $club->id, 'season' => date('Y'), 'competition' => 'LPF']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/standings')
            ->assertOk()
            ->assertSee('Tauro FC');
    }

    public function test_admin_can_create_standing(): void
    {
        $admin = $this->createAdminWithPermissions(['standings.view', 'standings.manage']);
        $club  = Club::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post('/admin/standings', [
                'club_id'         => $club->id,
                'competition'     => 'LPF',
                'season'          => '2026',
                'position'        => 1,
                'played'          => 14,
                'won'             => 10,
                'drawn'           => 2,
                'lost'            => 2,
                'goals_for'       => 28,
                'goals_against'   => 12,
                'goal_difference' => 16,
                'points'          => 32,
                'is_active'       => '1',
            ])
            ->assertRedirect('/admin/standings');

        $this->assertDatabaseHas('league_standings', [
            'club_id'  => $club->id,
            'position' => 1,
            'points'   => 32,
        ]);
    }

    public function test_admin_can_update_standing(): void
    {
        $admin    = $this->createAdminWithPermissions(['standings.view', 'standings.manage']);
        $club     = Club::factory()->create();
        $standing = LeagueStanding::factory()->create(['club_id' => $club->id]);

        $this->actingAs($admin, 'admin')
            ->put("/admin/standings/{$standing->id}", [
                'club_id'         => $club->id,
                'competition'     => $standing->competition,
                'season'          => $standing->season,
                'position'        => 2,
                'played'          => $standing->played,
                'won'             => $standing->won,
                'drawn'           => $standing->drawn,
                'lost'            => $standing->lost,
                'goals_for'       => $standing->goals_for,
                'goals_against'   => $standing->goals_against,
                'goal_difference' => $standing->goal_difference,
                'points'          => 30,
                'is_active'       => '1',
            ])
            ->assertRedirect('/admin/standings');

        $this->assertDatabaseHas('league_standings', ['id' => $standing->id, 'points' => 30, 'position' => 2]);
    }

    private function createAdminWithPermissions(array $names): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create(['name' => 'standing-role-' . fake()->unique()->slug(), 'label' => 'Standing Role']);

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
