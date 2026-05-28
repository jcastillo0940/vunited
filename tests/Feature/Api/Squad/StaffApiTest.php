<?php

namespace Tests\Feature\Api\Squad;

use App\Domain\Squad\Models\StaffMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_active_staff(): void
    {
        StaffMember::factory()->count(3)->create(['is_active' => true]);
        StaffMember::factory()->inactive()->create();

        $this->getJson('/api/staff')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_response_includes_required_fields(): void
    {
        StaffMember::factory()->create([
            'name'     => 'Gonzalo Méndez',
            'role'     => 'Director Técnico',
            'category' => 'first-team',
        ]);

        $data = $this->getJson('/api/staff')->json('data.0');

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('slug', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('first_name', $data);
        $this->assertArrayHasKey('last_name', $data);
        $this->assertArrayHasKey('role', $data);
        $this->assertArrayHasKey('category', $data);
        $this->assertArrayHasKey('photo_path', $data);
        $this->assertArrayHasKey('biography', $data);
        $this->assertSame('Gonzalo Méndez', $data['name']);
        $this->assertSame('Director Técnico', $data['role']);
    }

    public function test_filters_by_category(): void
    {
        StaffMember::factory()->create(['category' => 'first-team',  'is_active' => true]);
        StaffMember::factory()->create(['category' => 'women-team',  'is_active' => true]);

        $this->getJson('/api/staff?category=women-team')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category', 'women-team');
    }

    public function test_ordered_by_sort_order(): void
    {
        StaffMember::factory()->create(['name' => 'Tercero', 'sort_order' => 3, 'is_active' => true]);
        StaffMember::factory()->create(['name' => 'Primero', 'sort_order' => 1, 'is_active' => true]);
        StaffMember::factory()->create(['name' => 'Segundo', 'sort_order' => 2, 'is_active' => true]);

        $names = $this->getJson('/api/staff')->json('data.*.name');

        $this->assertSame(['Primero', 'Segundo', 'Tercero'], $names);
    }

    public function test_name_is_split_into_first_and_last(): void
    {
        StaffMember::factory()->create(['name' => 'Ricardo Vega', 'is_active' => true]);

        $data = $this->getJson('/api/staff')->json('data.0');

        $this->assertSame('Ricardo', $data['first_name']);
        $this->assertSame('Vega', $data['last_name']);
    }
}
