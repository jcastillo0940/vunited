<?php

namespace Tests\Feature\Api\Board;

use App\Domain\Board\Models\BoardMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardMemberApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_active_board_members(): void
    {
        BoardMember::factory()->count(4)->create(['is_active' => true]);
        BoardMember::factory()->inactive()->create();

        $this->getJson('/api/board-members')
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }

    public function test_response_includes_required_fields(): void
    {
        BoardMember::factory()->presidency()->create([
            'name'      => 'Ing. Ricardo Méndez',
            'role'      => 'Presidente Ejecutivo',
            'biography' => 'Líder del club.',
            'email'     => 'presidente@club.test',
        ]);

        $data = $this->getJson('/api/board-members')->json('data.0');

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('slug', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('role', $data);
        $this->assertArrayHasKey('group', $data);
        $this->assertArrayHasKey('group_label', $data);
        $this->assertArrayHasKey('photo_path', $data);
        $this->assertArrayHasKey('biography', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertArrayHasKey('sort_order', $data);
        $this->assertArrayHasKey('metadata', $data);
        $this->assertSame('Ing. Ricardo Méndez', $data['name']);
        $this->assertSame('presidency', $data['group']);
        $this->assertSame('Presidencia', $data['group_label']);
    }

    public function test_group_labels_are_correct(): void
    {
        BoardMember::factory()->create(['group' => 'presidency',      'is_active' => true]);
        BoardMember::factory()->create(['group' => 'executive_staff', 'is_active' => true]);
        BoardMember::factory()->create(['group' => 'board',           'is_active' => true]);
        BoardMember::factory()->create(['group' => 'transparency',    'is_active' => true]);

        $data = $this->getJson('/api/board-members')->json('data');
        $labels = array_column($data, 'group_label');

        $this->assertContains('Presidencia',      $labels);
        $this->assertContains('Staff Ejecutivo',  $labels);
        $this->assertContains('Junta Directiva',  $labels);
        $this->assertContains('Gobernanza',        $labels);
    }

    public function test_ordered_presidency_first(): void
    {
        BoardMember::factory()->create(['group' => 'board',           'sort_order' => 1, 'is_active' => true]);
        BoardMember::factory()->create(['group' => 'executive_staff', 'sort_order' => 1, 'is_active' => true]);
        BoardMember::factory()->create(['group' => 'presidency',      'sort_order' => 1, 'is_active' => true]);

        $groups = $this->getJson('/api/board-members')->json('data.*.group');

        $this->assertSame('presidency',      $groups[0]);
        $this->assertSame('executive_staff', $groups[1]);
        $this->assertSame('board',           $groups[2]);
    }

    public function test_filters_by_group(): void
    {
        BoardMember::factory()->presidency()->create(['is_active' => true]);
        BoardMember::factory()->board()->count(3)->create(['is_active' => true]);

        $this->getJson('/api/board-members?group=board')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.group', 'board');
    }

    public function test_inactive_members_excluded(): void
    {
        BoardMember::factory()->create(['name' => 'Activo',   'is_active' => true]);
        BoardMember::factory()->inactive()->create(['name' => 'Inactivo']);

        $names = $this->getJson('/api/board-members')->json('data.*.name');

        $this->assertContains('Activo',     $names);
        $this->assertNotContains('Inactivo', $names);
    }

    public function test_sort_order_within_group(): void
    {
        BoardMember::factory()->board()->create(['name' => 'Tercero', 'sort_order' => 3, 'is_active' => true]);
        BoardMember::factory()->board()->create(['name' => 'Primero', 'sort_order' => 1, 'is_active' => true]);
        BoardMember::factory()->board()->create(['name' => 'Segundo', 'sort_order' => 2, 'is_active' => true]);

        $names = $this->getJson('/api/board-members?group=board')->json('data.*.name');

        $this->assertSame(['Primero', 'Segundo', 'Tercero'], $names);
    }

    public function test_metadata_is_included(): void
    {
        BoardMember::factory()->executiveStaff()->create([
            'is_active' => true,
            'metadata'  => ['area' => 'Operaciones', 'tone' => 'accent', 'icons' => ['groups']],
        ]);

        $data = $this->getJson('/api/board-members?group=executive_staff')->json('data.0.metadata');

        $this->assertSame('Operaciones', $data['area']);
        $this->assertSame('accent', $data['tone']);
    }
}
