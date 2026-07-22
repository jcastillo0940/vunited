<?php

namespace Tests\Feature\Ticketing;

use App\Domain\Payments\Contracts\PaymentsGateway;
use App\Domain\Payments\Gateways\FakePaymentsGateway;
use App\Domain\Ticketing\Exceptions\InsufficientCapacityException;
use App\Domain\Ticketing\Exceptions\OrderException;
use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Zone;
use App\Domain\Ticketing\Services\OrderService;
use App\Domain\Ticketing\Support\OrderStateMachine;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGateway(): FakePaymentsGateway
    {
        $fake = new FakePaymentsGateway;
        $this->app->instance(PaymentsGateway::class, $fake);

        return $fake;
    }

    private function makeCustomer(): Customer
    {
        return Customer::create([
            'name' => 'Buyer', 'email' => 'buyer-'.uniqid().'@example.com', 'password' => bcrypt('secret1234'),
        ]);
    }

    private function makeEventWithZone(int $capacity = 5): array
    {
        $event = Event::create([
            'code' => 'test-'.uniqid(),
            'home_team' => 'A', 'away_team' => 'B', 'starts_at' => now()->addDays(5),
            'status' => 'on_sale', 'sales_open_at' => now()->subHour(),
        ]);
        $zone = Zone::create([
            'event_id' => $event->id, 'name' => 'General', 'slug' => 'general',
            'kind' => 'general', 'price' => 10, 'capacity_total' => $capacity,
            'capacity_available' => $capacity, 'capacity_held' => 0,
        ]);

        return [$event, $zone];
    }

    public function test_full_happy_path_draft_to_tickets_issued(): void
    {
        $this->fakeGateway();
        [$event, $zone] = $this->makeEventWithZone();
        $service = app(OrderService::class);
        $customer = $this->makeCustomer();

        $order = $service->createOrder($event, [
            ['zone_public_id' => $zone->public_id, 'quantity' => 2],
        ], $customer->id, 'buyer@example.com', 'Buyer', null, null);

        $this->assertSame('hold_active', $order->status);
        $this->assertSame(2, (int) $order->holds()->sum('quantity'));

        $order = $service->requestPayment($order);
        $this->assertSame('pending_payment', $order->status);
        $this->assertNotNull($order->payment_intent_id);

        $order = $service->markPaid($order);
        $this->assertSame('paid', $order->status);

        $zone->refresh();
        $this->assertSame(3, $zone->capacity_available); // 5 - 2
        $this->assertSame(0, $zone->capacity_held); // consumido, no "held" ya
    }

    public function test_order_creation_rejects_when_capacity_insufficient(): void
    {
        [$event, $zone] = $this->makeEventWithZone(capacity: 1);
        $service = app(OrderService::class);
        $customer = $this->makeCustomer();

        $this->expectException(InsufficientCapacityException::class);
        $service->createOrder($event, [
            ['zone_public_id' => $zone->public_id, 'quantity' => 2],
        ], $customer->id, 'buyer@example.com', null, null, null);
    }

    public function test_idempotency_key_returns_same_order_without_reclaiming_capacity(): void
    {
        [$event, $zone] = $this->makeEventWithZone(capacity: 5);
        $service = app(OrderService::class);
        $customer = $this->makeCustomer();

        $order1 = $service->createOrder($event, [
            ['zone_public_id' => $zone->public_id, 'quantity' => 2],
        ], $customer->id, 'buyer@example.com', null, null, 'idem-key-1');

        $order2 = $service->createOrder($event, [
            ['zone_public_id' => $zone->public_id, 'quantity' => 2],
        ], $customer->id, 'buyer@example.com', null, null, 'idem-key-1');

        $this->assertSame($order1->id, $order2->id);
        $zone->refresh();
        $this->assertSame(3, $zone->capacity_available, 'No debe reclamarse cupo dos veces para la misma idempotency_key.');
    }

    public function test_payment_failure_releases_holds_and_marks_order_failed(): void
    {
        $fake = $this->fakeGateway();
        $fake->nextIntentSucceeds = false;
        [$event, $zone] = $this->makeEventWithZone(capacity: 5);
        $service = app(OrderService::class);
        $customer = $this->makeCustomer();

        $order = $service->createOrder($event, [
            ['zone_public_id' => $zone->public_id, 'quantity' => 3],
        ], $customer->id, 'buyer@example.com', null, null, null);

        $this->expectException(OrderException::class);

        try {
            $service->requestPayment($order);
        } finally {
            $zone->refresh();
            $this->assertSame(5, $zone->capacity_available, 'El cupo debe liberarse por completo tras un fallo de pago.');
            $this->assertSame('failed', $order->fresh()->status);
        }
    }

    public function test_cancel_releases_holds(): void
    {
        [$event, $zone] = $this->makeEventWithZone(capacity: 5);
        $service = app(OrderService::class);
        $customer = $this->makeCustomer();

        $order = $service->createOrder($event, [
            ['zone_public_id' => $zone->public_id, 'quantity' => 4],
        ], $customer->id, 'buyer@example.com', null, null, null);

        $service->cancel($order);

        $zone->refresh();
        $this->assertSame(5, $zone->capacity_available);
        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_state_machine_rejects_invalid_transitions(): void
    {
        $this->assertTrue(OrderStateMachine::canTransition('draft', 'hold_active'));
        $this->assertFalse(OrderStateMachine::canTransition('draft', 'paid'));
        $this->assertFalse(OrderStateMachine::canTransition('refunded', 'paid'));
        $this->assertTrue(OrderStateMachine::isTerminal('refunded'));
        $this->assertFalse(OrderStateMachine::isTerminal('paid'));
    }
}
