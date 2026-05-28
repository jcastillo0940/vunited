<?php

namespace Tests\Feature\Api\Ticketing;

use App\Domain\Ticketing\Enums\TicketOrderStatus;
use App\Domain\Ticketing\Models\IssuedTicket;
use App\Domain\Ticketing\Models\TicketOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the API responses that drive /orden-boletos-confirmada.
 *
 * The frontend fetches GET /api/ticketing/orders/{orderNumber}/tickets
 * only when the order status is "paid". This test suite verifies that:
 * - paid orders return their issued tickets
 * - non-paid orders return an empty tickets array (no sensitive data leaked)
 * - the response shape matches what the frontend TicketCard component expects
 */
class TicketOrderConfirmedApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_order_with_tickets_returns_ticket_list(): void
    {
        $order = TicketOrder::factory()->paid()->create();

        IssuedTicket::factory()->count(2)->create([
            'ticket_order_id' => $order->id,
            'zone_name'       => 'General Norte',
        ]);

        $response = $this->getJson("/api/ticketing/orders/{$order->order_number}/tickets");

        $response->assertOk()
            ->assertJsonPath('order_number', $order->order_number)
            ->assertJsonPath('status', 'paid')
            ->assertJsonCount(2, 'tickets');

        $ticket = $response->json('tickets.0');
        $this->assertArrayHasKey('id', $ticket);
        $this->assertArrayHasKey('token', $ticket);
        $this->assertArrayHasKey('qr_payload', $ticket);
        $this->assertArrayHasKey('zone_name', $ticket);
        $this->assertArrayHasKey('seat_label', $ticket);
        $this->assertArrayHasKey('status', $ticket);
        $this->assertArrayHasKey('issued_at', $ticket);
        $this->assertSame('General Norte', $ticket['zone_name']);
        $this->assertSame('issued', $ticket['status']);
    }

    public function test_paid_order_without_tickets_returns_empty_list(): void
    {
        $order = TicketOrder::factory()->paid()->create();

        $this->getJson("/api/ticketing/orders/{$order->order_number}/tickets")
            ->assertOk()
            ->assertJsonPath('status', 'paid')
            ->assertJsonCount(0, 'tickets');
    }

    public function test_pending_payment_order_returns_empty_tickets(): void
    {
        $order = TicketOrder::factory()->create([
            'status' => TicketOrderStatus::PendingPayment,
        ]);

        $this->getJson("/api/ticketing/orders/{$order->order_number}/tickets")
            ->assertOk()
            ->assertJsonPath('status', 'pending_payment')
            ->assertJsonCount(0, 'tickets');
    }

    public function test_failed_order_returns_empty_tickets(): void
    {
        $order = TicketOrder::factory()->failed()->create();

        $this->getJson("/api/ticketing/orders/{$order->order_number}/tickets")
            ->assertOk()
            ->assertJsonPath('status', 'failed')
            ->assertJsonCount(0, 'tickets');
    }

    public function test_cancelled_order_returns_empty_tickets(): void
    {
        $order = TicketOrder::factory()->create([
            'status'       => TicketOrderStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        $this->getJson("/api/ticketing/orders/{$order->order_number}/tickets")
            ->assertOk()
            ->assertJsonPath('status', 'cancelled')
            ->assertJsonCount(0, 'tickets');
    }

    public function test_unknown_order_returns_404(): void
    {
        $this->getJson('/api/ticketing/orders/TICKET-0000-9999/tickets')
            ->assertNotFound();
    }

    public function test_ticket_token_is_40_chars(): void
    {
        $order  = TicketOrder::factory()->paid()->create();
        $ticket = IssuedTicket::factory()->create(['ticket_order_id' => $order->id]);

        $response = $this->getJson("/api/ticketing/orders/{$order->order_number}/tickets");

        $token = $response->json('tickets.0.token');
        $this->assertSame(40, strlen($token));
    }

    public function test_multiple_seats_across_items_all_returned(): void
    {
        $order = TicketOrder::factory()->paid()->create();

        IssuedTicket::factory()->count(3)->create(['ticket_order_id' => $order->id, 'zone_name' => 'Preferencia']);
        IssuedTicket::factory()->count(1)->create(['ticket_order_id' => $order->id, 'zone_name' => 'VIP']);

        $this->getJson("/api/ticketing/orders/{$order->order_number}/tickets")
            ->assertOk()
            ->assertJsonCount(4, 'tickets');
    }

    public function test_match_info_included_when_present(): void
    {
        $order = TicketOrder::factory()->paid()->create();

        $response = $this->getJson("/api/ticketing/orders/{$order->order_number}/tickets");

        $response->assertOk();
        $this->assertArrayHasKey('match', $response->json());
    }
}
