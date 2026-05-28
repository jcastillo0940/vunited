<?php

namespace Tests\Feature\Ticketing;

use App\Domain\Ticketing\Enums\IssuedTicketStatus;
use App\Domain\Ticketing\Enums\TicketOrderStatus;
use App\Domain\Ticketing\Exceptions\TicketIssuingException;
use App\Domain\Ticketing\Models\IssuedTicket;
use App\Domain\Ticketing\Models\TicketOrder;
use App\Domain\Ticketing\Services\TicketIssuingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketIssuingTest extends TestCase
{
    use RefreshDatabase;

    private TicketIssuingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TicketIssuingService::class);
    }

    public function test_issues_one_ticket_per_seat_for_paid_order(): void
    {
        $order = TicketOrder::factory()->paid()->create();
        $order->items()->create([
            'zone_name'  => 'General Norte',
            'unit_price' => '15.00',
            'quantity'   => 3,
            'line_total' => '45.00',
        ]);

        $tickets = $this->service->issueForOrder($order->refresh());

        $this->assertCount(3, $tickets);
        $this->assertDatabaseCount('issued_tickets', 3);

        $first = IssuedTicket::query()->where('ticket_order_id', $order->id)->first();
        $this->assertSame(IssuedTicketStatus::Issued, $first->status);
        $this->assertSame('General Norte', $first->zone_name);
        $this->assertSame(40, strlen($first->token));
        $this->assertNotNull($first->issued_at);
    }

    public function test_seat_labels_are_sequential(): void
    {
        $order = TicketOrder::factory()->paid()->create();
        $order->items()->create([
            'zone_name'  => 'VIP',
            'unit_price' => '50.00',
            'quantity'   => 2,
            'line_total' => '100.00',
        ]);

        $tickets = $this->service->issueForOrder($order->refresh());

        $labels = $tickets->pluck('seat_label')->toArray();
        $this->assertContains('VIP #1', $labels);
        $this->assertContains('VIP #2', $labels);
    }

    public function test_each_ticket_has_unique_token(): void
    {
        $order = TicketOrder::factory()->paid()->create();
        $order->items()->create([
            'zone_name'  => 'Preferencia',
            'unit_price' => '25.00',
            'quantity'   => 4,
            'line_total' => '100.00',
        ]);

        $tickets = $this->service->issueForOrder($order->refresh());

        $tokens = $tickets->pluck('token');
        $this->assertCount(4, $tokens->unique());
    }

    public function test_issues_across_multiple_items(): void
    {
        $order = TicketOrder::factory()->paid()->create();
        $order->items()->create(['zone_name' => 'General', 'unit_price' => '10.00', 'quantity' => 2, 'line_total' => '20.00']);
        $order->items()->create(['zone_name' => 'VIP', 'unit_price' => '40.00', 'quantity' => 1, 'line_total' => '40.00']);

        $tickets = $this->service->issueForOrder($order->refresh());

        $this->assertCount(3, $tickets);
        $this->assertDatabaseCount('issued_tickets', 3);
    }

    public function test_is_idempotent_when_called_twice(): void
    {
        $order = TicketOrder::factory()->paid()->create();
        $order->items()->create(['zone_name' => 'General', 'unit_price' => '10.00', 'quantity' => 2, 'line_total' => '20.00']);

        $first  = $this->service->issueForOrder($order->refresh());
        $second = $this->service->issueForOrder($order->refresh());

        $this->assertCount(2, $first);
        $this->assertCount(2, $second);
        $this->assertDatabaseCount('issued_tickets', 2);
    }

    public function test_throws_for_pending_order(): void
    {
        $order = TicketOrder::factory()->create(['status' => TicketOrderStatus::PendingPayment]);

        $this->expectException(TicketIssuingException::class);
        $this->service->issueForOrder($order);
    }

    public function test_throws_for_failed_order(): void
    {
        $order = TicketOrder::factory()->failed()->create();

        $this->expectException(TicketIssuingException::class);
        $this->service->issueForOrder($order);
    }

    public function test_webhook_captured_emits_tickets_automatically(): void
    {
        $order = TicketOrder::factory()->create(['status' => TicketOrderStatus::PendingPayment]);
        $order->items()->create(['zone_name' => 'General', 'unit_price' => '20.00', 'quantity' => 2, 'line_total' => '40.00']);

        \App\Domain\Payments\Models\Payment::factory()->create([
            'payable_type'     => TicketOrder::class,
            'payable_id'       => $order->id,
            'provider_order_id' => 'PAYID-ISSUE-01',
            'status'           => \App\Domain\Payments\Enums\PaymentStatus::Approved,
            'amount'           => 40.00,
        ]);

        \App\Domain\Payments\Models\PaymentSetting::create([
            'provider'      => 'paypal',
            'mode'          => 'sandbox',
            'client_id'     => 'x',
            'client_secret' => 'x',
            'currency'      => 'USD',
            'is_enabled'    => true,
        ]);

        $this->postJson('/api/webhooks/paypal', [
            'id'            => 'WH-ISSUE-AUTO-01',
            'event_type'    => 'PAYMENT.CAPTURE.COMPLETED',
            'resource_type' => 'capture',
            'resource'      => [
                'id'     => 'CAP-ISSUE-01',
                'status' => 'COMPLETED',
                'supplementary_data' => [
                    'related_ids' => ['order_id' => 'PAYID-ISSUE-01'],
                ],
            ],
        ])->assertStatus(200);

        $order->refresh();
        $this->assertSame(TicketOrderStatus::Paid, $order->status);
        $this->assertDatabaseCount('issued_tickets', 2);

        $ticket = IssuedTicket::query()->where('ticket_order_id', $order->id)->first();
        $this->assertSame(IssuedTicketStatus::Issued, $ticket->status);
    }

    public function test_webhook_does_not_issue_tickets_on_failed_capture(): void
    {
        $order = TicketOrder::factory()->create(['status' => TicketOrderStatus::PendingPayment]);
        $order->items()->create(['zone_name' => 'General', 'unit_price' => '20.00', 'quantity' => 1, 'line_total' => '20.00']);

        \App\Domain\Payments\Models\Payment::factory()->create([
            'payable_type'     => TicketOrder::class,
            'payable_id'       => $order->id,
            'provider_order_id' => 'PAYID-ISSUE-02',
            'status'           => \App\Domain\Payments\Enums\PaymentStatus::Approved,
            'amount'           => 20.00,
        ]);

        \App\Domain\Payments\Models\PaymentSetting::create([
            'provider'      => 'paypal',
            'mode'          => 'sandbox',
            'client_id'     => 'x',
            'client_secret' => 'x',
            'currency'      => 'USD',
            'is_enabled'    => true,
        ]);

        $this->postJson('/api/webhooks/paypal', [
            'id'            => 'WH-ISSUE-DENIED-01',
            'event_type'    => 'PAYMENT.CAPTURE.DENIED',
            'resource_type' => 'capture',
            'resource'      => [
                'id'     => 'CAP-ISSUE-02',
                'status' => 'DECLINED',
                'supplementary_data' => [
                    'related_ids' => ['order_id' => 'PAYID-ISSUE-02'],
                ],
            ],
        ])->assertStatus(200);

        $this->assertDatabaseCount('issued_tickets', 0);
    }
}
