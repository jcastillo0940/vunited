<?php

namespace Tests\Feature\Ticketing;

use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Models\PaymentSetting;
use App\Domain\Ticketing\Enums\TicketOrderStatus;
use App\Domain\Ticketing\Models\MatchEvent;
use App\Domain\Ticketing\Models\TicketOrder;
use App\Domain\Ticketing\Models\TicketZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TicketOrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_ticket_order_with_active_match_and_zone(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $match = MatchEvent::factory()->create([
            'is_active' => true,
            'code' => 'MATCH-123',
        ]);

        $zone = TicketZone::factory()->create([
            'match_event_id' => $match->id,
            'price' => '25.00',
            'currency' => 'USD',
            'is_active' => true,
            'available_quantity' => 10,
        ]);

        $response = $this->postJson('/api/ticketing/orders', $this->validPayload($match, $zone));

        $response->assertStatus(201)
            ->assertJsonStructure(['order_number', 'status', 'approve_url', 'payment_id', 'total', 'currency']);

        $this->assertDatabaseHas('ticket_orders', [
            'customer_email' => 'tribu@example.com',
            'status' => 'pending_payment',
            'currency' => 'USD',
            'total' => '50.00',
        ]);
    }

    public function test_calculates_total_in_backend(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $match = MatchEvent::factory()->create(['is_active' => true, 'code' => 'MATCH-ABC']);
        $zone = TicketZone::factory()->create([
            'match_event_id' => $match->id,
            'price' => '30.00',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/ticketing/orders', array_merge(
            $this->validPayload($match, $zone, quantity: 3),
            ['total' => '1.00'] // should be ignored and calculated by backend
        ));

        $response->assertStatus(201)
            ->assertJsonFragment([
                'total' => '90.00',
                'currency' => 'USD',
            ]);

        $this->assertDatabaseHas('ticket_orders', [
            'subtotal' => '90.00',
            'total' => '90.00',
        ]);
    }

    public function test_creates_ticket_order_items_with_snapshot(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $match = MatchEvent::factory()->create(['is_active' => true]);
        $zone = TicketZone::factory()->create([
            'match_event_id' => $match->id,
            'name' => 'Preferencia Este',
            'price' => '15.00',
            'is_active' => true,
        ]);

        $this->postJson('/api/ticketing/orders', $this->validPayload($match, $zone))->assertStatus(201);

        $order = TicketOrder::query()->with('items')->firstOrFail();
        $item = $order->items->first();

        $this->assertNotNull($item);
        $this->assertSame('Preferencia Este', $item->zone_name);
        $this->assertSame('15.00', $item->unit_price);
        $this->assertSame('Preferencia Este', $order->metadata['zone_snapshot']['name'] ?? null);
    }

    public function test_creates_associated_payment_and_returns_approve_url(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $match = MatchEvent::factory()->create(['is_active' => true]);
        $zone = TicketZone::factory()->create([
            'match_event_id' => $match->id,
            'price' => '40.00',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/ticketing/orders', $this->validPayload($match, $zone));

        $response->assertStatus(201);
        $this->assertStringContainsString('sandbox.paypal.com', $response->json('approve_url'));

        $order = TicketOrder::query()->firstOrFail();
        $payment = Payment::query()
            ->where('payable_type', TicketOrder::class)
            ->where('payable_id', $order->id)
            ->first();

        $this->assertNotNull($payment);
        $this->assertSame(PaymentStatus::ProviderCreated, $payment->status);
        $this->assertSame('80.00', $payment->amount);
    }

    public function test_rejects_inactive_match(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $match = MatchEvent::factory()->create(['is_active' => false]);
        $zone = TicketZone::factory()->create([
            'match_event_id' => $match->id,
            'is_active' => true,
        ]);

        $this->postJson('/api/ticketing/orders', $this->validPayload($match, $zone))
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'El partido no está disponible para compra de boletos.']);
    }

    public function test_rejects_inactive_zone(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $match = MatchEvent::factory()->create(['is_active' => true]);
        $zone = TicketZone::factory()->create([
            'match_event_id' => $match->id,
            'is_active' => false,
        ]);

        $this->postJson('/api/ticketing/orders', $this->validPayload($match, $zone))
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'La zona seleccionada no está disponible.']);
    }

    public function test_rejects_insufficient_available_quantity(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $match = MatchEvent::factory()->create(['is_active' => true]);
        $zone = TicketZone::factory()->create([
            'match_event_id' => $match->id,
            'available_quantity' => 1,
            'is_active' => true,
        ]);

        $this->postJson('/api/ticketing/orders', $this->validPayload($match, $zone, quantity: 2))
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Disponibilidad insuficiente. Solo quedan 1 boletos en esta zona.']);
    }

    public function test_webhook_captured_marks_ticket_order_paid_and_decrements_capacity(): void
    {
        $this->makeEnabledSetting();

        $match = MatchEvent::factory()->create(['is_active' => true]);
        $zone = TicketZone::factory()->create([
            'match_event_id' => $match->id,
            'available_quantity' => 15,
            'is_active' => true,
        ]);

        $order = TicketOrder::factory()->create([
            'match_event_id' => $match->id,
            'status' => TicketOrderStatus::PendingPayment,
            'total' => '50.00',
        ]);

        $order->items()->create([
            'ticket_zone_id' => $zone->id,
            'zone_name' => $zone->name,
            'unit_price' => '25.00',
            'quantity' => 2,
            'line_total' => '50.00',
        ]);

        Payment::factory()->create([
            'payable_type' => TicketOrder::class,
            'payable_id' => $order->id,
            'provider_order_id' => 'PAYID-TICKET001',
            'status' => PaymentStatus::Approved,
            'amount' => 50.00,
        ]);

        $this->postJson('/api/webhooks/paypal', [
            'id' => 'WH-TICKET-CAP-01',
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
            'resource_type' => 'capture',
            'resource' => [
                'id' => 'CAP-TICKET001',
                'status' => 'COMPLETED',
                'supplementary_data' => [
                    'related_ids' => ['order_id' => 'PAYID-TICKET001'],
                ],
            ],
        ])->assertStatus(200);

        $order->refresh();
        $zone->refresh();

        $this->assertSame(TicketOrderStatus::Paid, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame(13, $zone->available_quantity);
    }

    public function test_webhook_failed_marks_ticket_order_failed(): void
    {
        $this->makeEnabledSetting();

        $order = TicketOrder::factory()->create(['status' => TicketOrderStatus::PendingPayment]);

        Payment::factory()->create([
            'payable_type' => TicketOrder::class,
            'payable_id' => $order->id,
            'provider_order_id' => 'PAYID-TICKET002',
            'status' => PaymentStatus::Approved,
            'amount' => 25.00,
        ]);

        $this->postJson('/api/webhooks/paypal', [
            'id' => 'WH-TICKET-DEN-01',
            'event_type' => 'PAYMENT.CAPTURE.DENIED',
            'resource_type' => 'capture',
            'resource' => [
                'id' => 'CAP-TICKET002',
                'status' => 'DECLINED',
                'supplementary_data' => [
                    'related_ids' => ['order_id' => 'PAYID-TICKET002'],
                ],
            ],
        ])->assertStatus(200);

        $this->assertSame(TicketOrderStatus::Failed, $order->fresh()->status);
    }

    public function test_webhook_refunded_marks_ticket_order_cancelled(): void
    {
        $this->makeEnabledSetting();

        $order = TicketOrder::factory()->create(['status' => TicketOrderStatus::Paid, 'paid_at' => now()]);

        Payment::factory()->captured()->create([
            'payable_type' => TicketOrder::class,
            'payable_id' => $order->id,
            'provider_order_id' => 'PAYID-TICKET003',
        ]);

        $this->postJson('/api/webhooks/paypal', [
            'id' => 'WH-TICKET-REF-01',
            'event_type' => 'PAYMENT.CAPTURE.REFUNDED',
            'resource_type' => 'capture',
            'resource' => [
                'id' => 'CAP-TICKET003',
                'status' => 'COMPLETED',
                'supplementary_data' => [
                    'related_ids' => [
                        'order_id' => 'PAYID-TICKET003',
                        'capture_id' => 'CAP-TICKET003',
                    ],
                ],
            ],
        ])->assertStatus(200);

        $order->refresh();
        $this->assertSame(TicketOrderStatus::Cancelled, $order->status);
        $this->assertNotNull($order->cancelled_at);
    }

    public function test_does_not_process_card_or_cvv_fields(): void
    {
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $match = MatchEvent::factory()->create(['is_active' => true]);
        $zone = TicketZone::factory()->create(['match_event_id' => $match->id, 'is_active' => true]);

        $payload = array_merge($this->validPayload($match, $zone), [
            'card_number' => '4111111111111111',
            'card_cvv' => '123',
        ]);

        $this->postJson('/api/ticketing/orders', $payload)->assertStatus(201);

        $order = TicketOrder::query()->firstOrFail();
        $payment = Payment::query()->where('payable_type', TicketOrder::class)->firstOrFail();

        $this->assertNull($order->metadata['card_number'] ?? null);
        $this->assertNull($payment->metadata['card_cvv'] ?? null);
        $this->assertStringNotContainsString('4111111111111111', json_encode($payment->provider_payload ?? []));
    }

    private function makeEnabledSetting(): PaymentSetting
    {
        return PaymentSetting::create([
            'provider' => 'paypal',
            'mode' => 'sandbox',
            'client_id' => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'currency' => 'USD',
            'is_enabled' => true,
            'webhook_id' => null,
        ]);
    }

    private function fakePayPalSuccess(): void
    {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'fake-token',
                'token_type' => 'Bearer',
                'expires_in' => 32400,
            ], 200),
            '*/v2/checkout/orders' => Http::response([
                'id' => 'PAYID-TICKET-TEST',
                'status' => 'CREATED',
                'links' => [
                    ['rel' => 'approve', 'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=PAYID-TICKET-TEST'],
                ],
            ], 201),
        ]);
    }

    private function validPayload(MatchEvent $match, TicketZone $zone, int $quantity = 2): array
    {
        return [
            'match_event_code' => $match->code,
            'ticket_zone_id' => $zone->id,
            'quantity' => $quantity,
            'customer_email' => 'tribu@example.com',
            'accept_terms' => true,
        ];
    }
}
