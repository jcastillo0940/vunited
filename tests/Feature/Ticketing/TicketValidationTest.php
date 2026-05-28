<?php

namespace Tests\Feature\Ticketing;

use App\Domain\Ticketing\Enums\IssuedTicketStatus;
use App\Domain\Ticketing\Models\IssuedTicket;
use App\Domain\Ticketing\Models\TicketOrder;
use App\Domain\Ticketing\Services\TicketValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketValidationTest extends TestCase
{
    use RefreshDatabase;

    private TicketValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TicketValidationService::class);
    }

    public function test_valid_ticket_is_marked_used(): void
    {
        $ticket = IssuedTicket::factory()->create();

        $result = $this->service->validate($ticket->token);

        $this->assertTrue($result['valid']);
        $this->assertSame('used', $result['ticket']['status']);

        $ticket->refresh();
        $this->assertSame(IssuedTicketStatus::Used, $ticket->status);
        $this->assertNotNull($ticket->used_at);
    }

    public function test_unknown_token_returns_not_found(): void
    {
        $result = $this->service->validate(str_repeat('a', 40));

        $this->assertFalse($result['valid']);
        $this->assertSame('not_found', $result['reason']);
    }

    public function test_already_used_ticket_fails(): void
    {
        $ticket = IssuedTicket::factory()->used()->create();

        $result = $this->service->validate($ticket->token);

        $this->assertFalse($result['valid']);
        $this->assertSame('already_used', $result['reason']);
    }

    public function test_voided_ticket_fails(): void
    {
        $ticket = IssuedTicket::factory()->voided()->create();

        $result = $this->service->validate($ticket->token);

        $this->assertFalse($result['valid']);
        $this->assertSame('voided', $result['reason']);
    }

    public function test_used_ticket_cannot_be_reused(): void
    {
        $ticket = IssuedTicket::factory()->create();

        $first  = $this->service->validate($ticket->token);
        $second = $this->service->validate($ticket->token);

        $this->assertTrue($first['valid']);
        $this->assertFalse($second['valid']);
        $this->assertSame('already_used', $second['reason']);
    }

    public function test_result_includes_order_and_match_info(): void
    {
        $ticket = IssuedTicket::factory()->create(['seat_label' => 'VIP #1', 'zone_name' => 'VIP']);

        $result = $this->service->validate($ticket->token);

        $this->assertTrue($result['valid']);
        $this->assertSame('VIP #1', $result['ticket']['seat_label']);
        $this->assertNotNull($result['ticket']['order_number']);
    }

    public function test_order_tickets_api_returns_issued_tickets(): void
    {
        $order  = TicketOrder::factory()->paid()->create();
        $ticket = IssuedTicket::factory()->create(['ticket_order_id' => $order->id]);

        $this->getJson("/api/ticketing/orders/{$order->order_number}/tickets")
            ->assertOk()
            ->assertJsonPath('order_number', $order->order_number)
            ->assertJsonCount(1, 'tickets')
            ->assertJsonPath('tickets.0.token', $ticket->token);
    }

    public function test_order_tickets_api_returns_empty_when_not_issued(): void
    {
        $order = TicketOrder::factory()->paid()->create();

        $this->getJson("/api/ticketing/orders/{$order->order_number}/tickets")
            ->assertOk()
            ->assertJsonCount(0, 'tickets');
    }

    public function test_order_tickets_api_returns_404_for_unknown_order(): void
    {
        $this->getJson('/api/ticketing/orders/TICKET-9999-9999/tickets')
            ->assertStatus(404);
    }
}
