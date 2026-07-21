<?php

namespace Tests\Feature\Ticketing;

use App\Domain\Payments\Contracts\PaymentsGateway;
use App\Domain\Payments\Gateways\FakePaymentsGateway;
use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Zone;
use App\Domain\Ticketing\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpireHoldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_holds_release_capacity_and_expire_the_order(): void
    {
        $this->app->instance(PaymentsGateway::class, new FakePaymentsGateway);

        $event = Event::create([
            'code' => 'expire-test', 'home_team' => 'A', 'away_team' => 'B',
            'starts_at' => now()->addDays(5), 'status' => 'on_sale', 'sales_open_at' => now()->subHour(),
        ]);
        $zone = Zone::create([
            'event_id' => $event->id, 'name' => 'General', 'slug' => 'general',
            'kind' => 'general', 'price' => 10, 'capacity_total' => 3,
            'capacity_available' => 3, 'capacity_held' => 0,
        ]);

        $order = app(OrderService::class)->createOrder($event, [
            ['zone_public_id' => $zone->public_id, 'quantity' => 3],
        ], 'buyer@example.com', null, null, null, holdMinutes: -1); // ya vencido

        $zone->refresh();
        $this->assertSame(0, $zone->capacity_available, 'El cupo debe quedar reservado al crear el hold.');

        $this->artisan('tickets:expire-holds')->assertExitCode(0);

        $zone->refresh();
        $this->assertSame(3, $zone->capacity_available, 'Un hold vencido debe liberar todo el cupo.');
        $this->assertSame('expired', $order->fresh()->status);
    }
}
