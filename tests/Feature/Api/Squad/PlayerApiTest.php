<?php

namespace Tests\Feature\Api\Squad;

use App\Domain\Squad\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_active_players(): void
    {
        Player::factory()->count(3)->create(['is_active' => true]);
        Player::factory()->create(['is_active' => false]);

        $this->getJson('/api/players')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_response_includes_required_fields(): void
    {
        Player::factory()->create([
            'name'         => 'Juan García',
            'number'       => '07',
            'position'     => 'Volante',
            'position_key' => 'midfielder',
            'category'     => 'first-team',
        ]);

        $data = $this->getJson('/api/players')->json('data.0');

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('slug', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('first_name', $data);
        $this->assertArrayHasKey('last_name', $data);
        $this->assertArrayHasKey('number', $data);
        $this->assertArrayHasKey('position', $data);
        $this->assertArrayHasKey('position_key', $data);
        $this->assertArrayHasKey('category', $data);
        $this->assertArrayHasKey('nationality', $data);
        $this->assertArrayHasKey('photo_path', $data);
        $this->assertSame('Juan García', $data['name']);
        $this->assertSame('midfielder', $data['position_key']);
    }

    public function test_filters_by_category(): void
    {
        Player::factory()->create(['category' => 'first-team', 'is_active' => true]);
        Player::factory()->create(['category' => 'academy',    'is_active' => true]);

        $this->getJson('/api/players?category=academy')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category', 'academy');
    }

    public function test_filters_by_position(): void
    {
        Player::factory()->create(['position_key' => 'goalkeeper', 'is_active' => true]);
        Player::factory()->create(['position_key' => 'defender',   'is_active' => true]);

        $this->getJson('/api/players?position=goalkeeper')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_show_returns_player_detail(): void
    {
        $player = Player::factory()->create([
            'name'      => 'Pedro López',
            'biography' => 'Gran jugador.',
            'stats'     => [['key' => 'goals', 'label' => 'Goles', 'value' => '5', 'tone' => 'accent']],
            'is_active' => true,
        ]);

        $this->getJson("/api/players/{$player->slug}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Pedro López')
            ->assertJsonPath('data.biography', 'Gran jugador.');
    }

    public function test_show_returns_404_for_unknown_slug(): void
    {
        $this->getJson('/api/players/no-existe')
            ->assertNotFound();
    }

    public function test_show_returns_404_for_inactive_player(): void
    {
        $player = Player::factory()->inactive()->create();

        $this->getJson("/api/players/{$player->slug}")
            ->assertNotFound();
    }

    public function test_show_includes_stats_and_attributes(): void
    {
        $player = Player::factory()->create([
            'stats'      => [['key' => 'goals', 'label' => 'Goles', 'value' => '10', 'tone' => 'accent']],
            'attributes' => [['key' => 'speed', 'label' => 'Velocidad', 'value' => 90]],
            'is_active'  => true,
        ]);

        $data = $this->getJson("/api/players/{$player->slug}")->json('data');

        $this->assertIsArray($data['stats']);
        $this->assertIsArray($data['attributes']);
        $this->assertSame('goals', $data['stats'][0]['key']);
        $this->assertSame(90, $data['attributes'][0]['value']);
    }

    public function test_name_is_split_into_first_and_last(): void
    {
        Player::factory()->create(['name' => 'Alexis Canto', 'is_active' => true]);

        $data = $this->getJson('/api/players')->json('data.0');

        $this->assertSame('Alexis', $data['first_name']);
        $this->assertSame('Canto', $data['last_name']);
    }
}
