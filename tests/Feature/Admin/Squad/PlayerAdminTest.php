<?php

namespace Tests\Feature\Admin\Squad;

use App\Domain\AccessControl\Models\Permission;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\Squad\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PlayerAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_admin_auth(): void
    {
        $this->get('/admin/players')->assertRedirect('/admin/login');
    }

    public function test_admin_with_permission_can_view_index(): void
    {
        $admin = $this->adminWith(['players.view']);
        Player::factory()->count(3)->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/players')
            ->assertOk()
            ->assertSee('Jugadores');
    }

    public function test_admin_without_permission_is_forbidden(): void
    {
        $admin = AdminUser::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/players')
            ->assertForbidden();
    }

    public function test_admin_can_create_player(): void
    {
        $admin = $this->adminWith(['players.view', 'players.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/players', [
                'name'         => 'Carlos Ríos',
                'position'     => 'Defensa',
                'position_key' => 'defender',
                'category'     => 'first-team',
                'sort_order'   => 0,
            ])
            ->assertRedirect('/admin/players');

        $this->assertDatabaseHas('players', [
            'name'     => 'Carlos Ríos',
            'slug'     => 'carlos-rios',
            'category' => 'first-team',
        ]);
    }

    public function test_slug_is_auto_generated_from_name(): void
    {
        $admin = $this->adminWith(['players.view', 'players.manage']);

        $this->actingAs($admin, 'admin')
            ->post('/admin/players', [
                'name'       => 'Miguel Ángel Paz',
                'category'   => 'academy',
                'sort_order' => 0,
            ]);

        $player = Player::query()->where('name', 'Miguel Ángel Paz')->first();
        $this->assertNotNull($player);
        $this->assertSame('miguel-angel-paz', $player->slug);
    }

    public function test_admin_can_update_player(): void
    {
        $admin  = $this->adminWith(['players.view', 'players.manage']);
        $player = Player::factory()->create(['name' => 'Viejo Nombre', 'category' => 'first-team']);

        $this->actingAs($admin, 'admin')
            ->put("/admin/players/{$player->id}", [
                'name'       => 'Nuevo Nombre',
                'slug'       => $player->slug,
                'category'   => 'first-team',
                'sort_order' => 1,
            ])
            ->assertRedirect('/admin/players');

        $this->assertDatabaseHas('players', ['name' => 'Nuevo Nombre']);
    }

    public function test_admin_can_delete_player(): void
    {
        $admin  = $this->adminWith(['players.view', 'players.manage']);
        $player = Player::factory()->create();

        $this->actingAs($admin, 'admin')
            ->delete("/admin/players/{$player->id}")
            ->assertRedirect('/admin/players');

        $this->assertDatabaseMissing('players', ['id' => $player->id]);
    }

    public function test_index_filters_by_category(): void
    {
        $admin = $this->adminWith(['players.view']);
        Player::factory()->create(['name' => 'Primer Equipo Player', 'category' => 'first-team']);
        Player::factory()->create(['name' => 'Academia Player', 'category' => 'academy']);

        $this->actingAs($admin, 'admin')
            ->get('/admin/players?category=academy')
            ->assertOk()
            ->assertSee('Academia Player')
            ->assertDontSee('Primer Equipo Player');
    }

    private function adminWith(array $permissions): AdminUser
    {
        $admin = AdminUser::factory()->create();
        $role  = Role::create(['name' => 'squad-role-' . Str::random(6), 'label' => 'Squad Role']);

        foreach ($permissions as $perm) {
            $p = Permission::firstOrCreate(['name' => $perm], ['label' => $perm]);
            $role->permissions()->syncWithoutDetaching([$p->id]);
        }

        $admin->roles()->syncWithoutDetaching([$role->id]);

        return $admin;
    }
}
