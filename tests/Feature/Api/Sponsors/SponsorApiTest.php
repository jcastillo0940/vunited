<?php

namespace Tests\Feature\Api\Sponsors;

use App\Domain\Sponsors\Models\Sponsor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SponsorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_active_sponsors(): void
    {
        Sponsor::factory()->count(4)->create(['is_active' => true]);
        Sponsor::factory()->inactive()->create();

        $this->getJson('/api/sponsors')
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }

    public function test_response_includes_required_fields(): void
    {
        Sponsor::factory()->mainPartner()->create([
            'name'        => 'Banco Provincial',
            'description' => 'Aliado principal.',
            'website_url' => 'https://banco.test',
        ]);

        $data = $this->getJson('/api/sponsors')->json('data.0');

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('slug', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('tier', $data);
        $this->assertArrayHasKey('tier_label', $data);
        $this->assertArrayHasKey('logo_path', $data);
        $this->assertArrayHasKey('website_url', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('sort_order', $data);
        $this->assertSame('Banco Provincial', $data['name']);
        $this->assertSame('main_partner', $data['tier']);
        $this->assertSame('Main Partner', $data['tier_label']);
    }

    public function test_tier_label_is_human_readable(): void
    {
        Sponsor::factory()->create(['tier' => 'main_partner',    'is_active' => true]);
        Sponsor::factory()->create(['tier' => 'official_sponsor','is_active' => true]);
        Sponsor::factory()->create(['tier' => 'strategic_ally',  'is_active' => true]);

        $data = $this->getJson('/api/sponsors')->json('data');

        $labels = array_column($data, 'tier_label');
        $this->assertContains('Main Partner',        $labels);
        $this->assertContains('Official Sponsor',    $labels);
        $this->assertContains('Alianza Estratégica', $labels);
    }

    public function test_ordered_main_partners_first(): void
    {
        Sponsor::factory()->strategicAlly()->create(['name'  => 'Ally',   'sort_order' => 1, 'is_active' => true]);
        Sponsor::factory()->officialSponsor()->create(['name' => 'Official','sort_order' => 1, 'is_active' => true]);
        Sponsor::factory()->mainPartner()->create(['name'     => 'Main',   'sort_order' => 1, 'is_active' => true]);

        $tiers = $this->getJson('/api/sponsors')->json('data.*.tier');

        $this->assertSame('main_partner',     $tiers[0]);
        $this->assertSame('official_sponsor', $tiers[1]);
        $this->assertSame('strategic_ally',   $tiers[2]);
    }

    public function test_filters_by_tier(): void
    {
        Sponsor::factory()->mainPartner()->create(['is_active' => true]);
        Sponsor::factory()->officialSponsor()->count(2)->create(['is_active' => true]);

        $this->getJson('/api/sponsors?tier=official_sponsor')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.tier', 'official_sponsor');
    }

    public function test_inactive_sponsors_excluded(): void
    {
        Sponsor::factory()->create(['name' => 'Activo',   'is_active' => true]);
        Sponsor::factory()->inactive()->create(['name' => 'Inactivo']);

        $names = $this->getJson('/api/sponsors')->json('data.*.name');

        $this->assertContains('Activo',  $names);
        $this->assertNotContains('Inactivo', $names);
    }

    public function test_empty_list_when_no_active_sponsors(): void
    {
        Sponsor::factory()->inactive()->count(3)->create();

        $this->getJson('/api/sponsors')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_sort_order_within_tier(): void
    {
        Sponsor::factory()->officialSponsor()->create(['name' => 'Tercero', 'sort_order' => 3, 'is_active' => true]);
        Sponsor::factory()->officialSponsor()->create(['name' => 'Primero', 'sort_order' => 1, 'is_active' => true]);
        Sponsor::factory()->officialSponsor()->create(['name' => 'Segundo', 'sort_order' => 2, 'is_active' => true]);

        $names = $this->getJson('/api/sponsors')->json('data.*.name');

        $this->assertSame(['Primero', 'Segundo', 'Tercero'], $names);
    }
}
