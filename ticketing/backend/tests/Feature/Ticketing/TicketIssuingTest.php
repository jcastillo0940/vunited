<?php

namespace Tests\Feature\Ticketing;

use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Models\OrderItem;
use App\Domain\Ticketing\Models\Seat;
use App\Domain\Ticketing\Models\Ticket;
use App\Domain\Ticketing\Models\Zone;
use App\Domain\Ticketing\Services\TicketIssuingService;
use App\Domain\Ticketing\Support\TicketQrSigner;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketIssuingTest extends TestCase
{
    use RefreshDatabase;

    private function paidOrderWithGeneralItem(int $quantity = 3): Order
    {
        $event = Event::create(['code' => 'issue-'.uniqid(), 'home_team' => 'A', 'away_team' => 'B', 'starts_at' => now()->addDays(3)]);
        $zone = Zone::create([
            'event_id' => $event->id, 'name' => 'General', 'slug' => 'general', 'kind' => 'general',
            'price' => 5, 'capacity_total' => 10, 'capacity_available' => 10 - $quantity, 'capacity_held' => 0,
        ]);
        $order = Order::create(['event_id' => $event->id, 'status' => 'paid', 'customer_email' => 'buyer@example.com', 'total' => 5 * $quantity]);
        $order->assignOrderNumber();
        OrderItem::create(['order_id' => $order->id, 'zone_id' => $zone->id, 'quantity' => $quantity, 'unit_price' => 5, 'line_total' => 5 * $quantity]);

        return $order->fresh();
    }

    public function test_issuing_is_idempotent(): void
    {
        $order = $this->paidOrderWithGeneralItem(3);
        $service = app(TicketIssuingService::class);

        $first = $service->issueForOrder($order);
        $second = $service->issueForOrder($order->fresh());

        $this->assertCount(3, $first);
        $this->assertCount(3, $second);
        $this->assertSame($first->pluck('id')->sort()->values()->all(), $second->pluck('id')->sort()->values()->all());
        $this->assertSame('tickets_issued', $order->fresh()->status);
    }

    public function test_cannot_issue_two_tickets_for_the_same_seat_and_event_at_db_level(): void
    {
        $event = Event::create(['code' => 'issue-seat-'.uniqid(), 'home_team' => 'A', 'away_team' => 'B', 'starts_at' => now()->addDays(3)]);
        $zone = Zone::create([
            'event_id' => $event->id, 'name' => 'VIP', 'slug' => 'vip', 'kind' => 'seated',
            'price' => 25, 'capacity_total' => 1, 'capacity_available' => 0, 'capacity_held' => 0,
        ]);
        $seat = Seat::create(['zone_id' => $zone->id, 'label' => 'A-1', 'status' => 'sold']);

        $order1 = Order::create(['event_id' => $event->id, 'status' => 'paid', 'customer_email' => 'a@example.com']);
        $item1 = OrderItem::create(['order_id' => $order1->id, 'zone_id' => $zone->id, 'seat_id' => $seat->id, 'quantity' => 1, 'unit_price' => 25, 'line_total' => 25]);
        Ticket::create([
            'order_id' => $order1->id, 'order_item_id' => $item1->id,
            'event_id' => $event->id, 'zone_id' => $zone->id, 'seat_id' => $seat->id,
            'status' => 'issued', 'qr_token' => 'tok-1', 'issued_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        $order2 = Order::create(['event_id' => $event->id, 'status' => 'paid', 'customer_email' => 'b@example.com']);
        $item2 = OrderItem::create(['order_id' => $order2->id, 'zone_id' => $zone->id, 'seat_id' => $seat->id, 'quantity' => 1, 'unit_price' => 25, 'line_total' => 25]);
        Ticket::create([
            'order_id' => $order2->id, 'order_item_id' => $item2->id,
            'event_id' => $event->id, 'zone_id' => $zone->id, 'seat_id' => $seat->id,
            'status' => 'issued', 'qr_token' => 'tok-2', 'issued_at' => now(),
        ]);
    }

    public function test_qr_signature_verifies_and_detects_tampering(): void
    {
        $order = $this->paidOrderWithGeneralItem(1);
        $ticket = app(TicketIssuingService::class)->issueForOrder($order)->first();

        $signer = app(TicketQrSigner::class);
        $verified = $signer->verify($ticket->qr_token);

        $this->assertNotNull($verified);
        $this->assertSame($ticket->public_id, $verified['ticket_public_id']);

        // Alterar un solo caracter del token debe invalidar la firma.
        $tampered = substr_replace($ticket->qr_token, 'x', 5, 1);
        $this->assertNull($signer->verify($tampered));
    }

    public function test_qr_token_never_contains_pii(): void
    {
        $order = $this->paidOrderWithGeneralItem(1);
        $order->update(['customer_email' => 'very-identifiable-person@example.com']);
        $ticket = app(TicketIssuingService::class)->issueForOrder($order->fresh())->first();

        $this->assertStringNotContainsString('very-identifiable-person', $ticket->qr_token);
        $this->assertStringNotContainsString('example.com', $ticket->qr_token);

        // El payload firmado (antes de base64) solo debe tener ticket_public_id|event_id.
        $signer = app(TicketQrSigner::class);
        $verified = $signer->verify($ticket->qr_token);
        $this->assertSame(['ticket_public_id', 'event_id'], array_keys($verified));
    }

    public function test_reissue_revokes_original_and_keeps_seat_unique(): void
    {
        $event = Event::create(['code' => 'reissue-'.uniqid(), 'home_team' => 'A', 'away_team' => 'B', 'starts_at' => now()->addDays(3)]);
        $zone = Zone::create([
            'event_id' => $event->id, 'name' => 'VIP', 'slug' => 'vip', 'kind' => 'seated',
            'price' => 25, 'capacity_total' => 1, 'capacity_available' => 0, 'capacity_held' => 0,
        ]);
        $seat = Seat::create(['zone_id' => $zone->id, 'label' => 'A-1', 'status' => 'sold']);
        $order = Order::create(['event_id' => $event->id, 'status' => 'paid', 'customer_email' => 'a@example.com']);
        $item = OrderItem::create(['order_id' => $order->id, 'zone_id' => $zone->id, 'seat_id' => $seat->id, 'quantity' => 1, 'unit_price' => 25, 'line_total' => 25]);
        $original = Ticket::create([
            'order_id' => $order->id, 'order_item_id' => $item->id, 'event_id' => $event->id,
            'zone_id' => $zone->id, 'seat_id' => $seat->id, 'status' => 'issued', 'qr_token' => 'orig-tok', 'issued_at' => now(),
        ]);

        $reissued = app(TicketIssuingService::class)->reissue($original, 'boleto perdido');

        $this->assertSame('revoked', $original->fresh()->status);
        $this->assertNull($original->fresh()->seat_id);
        $this->assertSame('issued', $reissued->status);
        $this->assertSame($seat->id, $reissued->seat_id);
        $this->assertSame($original->id, $reissued->reissue_of_ticket_id);
        $this->assertNotSame($original->qr_token, $reissued->qr_token);
    }
}
