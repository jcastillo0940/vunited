<?php

namespace Tests\Feature\Memberships;

use App\Domain\Memberships\Enums\MembershipOrderStatus;
use App\Domain\Memberships\Models\MembershipOrder;
use App\Domain\Memberships\Models\MembershipPlan;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Models\PaymentSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MembershipOrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────────────────────────────────
    // Create order
    // ─────────────────────────────────────────────────────────────────────────

    public function test_can_create_membership_order(): void
    {
        $this->makeActivePlan();
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $response = $this->postJson('/api/membership-orders', $this->validPayload());

        $response->assertStatus(201)
            ->assertJsonStructure(['order_number', 'status', 'approve_url', 'payment_id']);

        $this->assertDatabaseHas('membership_orders', [
            'full_name'       => 'Juan Perez',
            'email'           => 'juan@example.com',
            'membership_plan' => 'tribu',
            'status'          => 'pending_payment',
        ]);
    }

    public function test_create_order_creates_associated_payment(): void
    {
        $this->makeActivePlan(price: '135.00');
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $this->postJson('/api/membership-orders', $this->validPayload())->assertStatus(201);

        $order = MembershipOrder::query()->first();
        $this->assertNotNull($order);

        $payment = Payment::query()
            ->where('payable_type', MembershipOrder::class)
            ->where('payable_id', $order->id)
            ->first();

        $this->assertNotNull($payment);
        $this->assertSame('135.00', $payment->amount);
        $this->assertSame('USD', $payment->currency);
        $this->assertSame(PaymentStatus::ProviderCreated, $payment->status);
    }

    public function test_returns_approve_url_from_paypal(): void
    {
        $this->makeActivePlan();
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $response = $this->postJson('/api/membership-orders', $this->validPayload());

        $response->assertStatus(201);
        $this->assertNotEmpty($response->json('approve_url'));
        $this->assertStringContainsString('sandbox.paypal.com', $response->json('approve_url'));
    }

    public function test_paypal_order_uses_membership_return_and_cancel_urls(): void
    {
        $this->makeActivePlan();
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $response = $this->postJson('/api/membership-orders', $this->validPayload());

        $response->assertStatus(201);
        $orderNumber = $response->json('order_number');

        Http::assertSent(function ($request) use ($orderNumber) {
            if (! str_contains($request->url(), '/v2/checkout/orders')) {
                return false;
            }

            $body = $request->data();

            return ($body['application_context']['return_url'] ?? null) === url('/registro-confirmado') . '?order=' . $orderNumber
                && ($body['application_context']['cancel_url'] ?? null) === url('/registro-tribu') . '?cancelled=1';
        });
    }

    public function test_order_number_follows_tribu_format(): void
    {
        $this->makeActivePlan();
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $response = $this->postJson('/api/membership-orders', $this->validPayload());

        $orderNumber = $response->json('order_number');
        $this->assertMatchesRegularExpression('/^TRIBU-\d{4}-\d{4}$/', $orderNumber);
    }

    public function test_two_orders_get_different_order_numbers(): void
    {
        $this->makeActivePlan();
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $r1 = $this->postJson('/api/membership-orders', $this->validPayload())->json('order_number');
        $r2 = $this->postJson('/api/membership-orders', array_merge($this->validPayload(), ['email' => 'otra@example.com']))->json('order_number');

        $this->assertNotSame($r1, $r2);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Validation
    // ─────────────────────────────────────────────────────────────────────────

    public function test_invalid_email_is_rejected(): void
    {
        $this->makeActivePlan();
        $this->postJson('/api/membership-orders', array_merge($this->validPayload(), ['email' => 'not-an-email']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_terms_not_accepted_is_rejected(): void
    {
        $this->makeActivePlan();
        $this->postJson('/api/membership-orders', array_merge($this->validPayload(), ['accept_terms' => false]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('accept_terms');
    }

    public function test_full_name_is_required(): void
    {
        $this->makeActivePlan();
        $payload = $this->validPayload();
        unset($payload['full_name']);

        $this->postJson('/api/membership-orders', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors('full_name');
    }

    public function test_invalid_membership_plan_is_rejected(): void
    {
        $this->makeActivePlan();
        $this->postJson('/api/membership-orders', array_merge($this->validPayload(), ['membership_plan' => 'vip-gold']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('membership_plan');
    }

    public function test_membership_order_uses_active_plan_price_and_snapshot(): void
    {
        $this->makeActivePlan(
            price: '149.00',
            name: 'Socio Indio Premium',
            benefits: ['Preventa exclusiva', 'Descuento tienda'],
        );
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $this->postJson('/api/membership-orders', $this->validPayload())->assertStatus(201);

        $order = MembershipOrder::query()->first();

        $this->assertNotNull($order);
        $this->assertSame('149.00', $order->membership_price);
        $this->assertSame('Socio Indio Premium', $order->metadata['plan_snapshot']['name'] ?? null);
        $this->assertSame('149.00', $order->metadata['plan_snapshot']['price'] ?? null);
        $this->assertSame(['Preventa exclusiva', 'Descuento tienda'], $order->metadata['plan_snapshot']['benefits'] ?? null);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET order
    // ─────────────────────────────────────────────────────────────────────────

    public function test_get_order_returns_status(): void
    {
        $order = MembershipOrder::factory()->create([
            'order_number'    => 'TRIBU-2026-0001',
            'status'          => MembershipOrderStatus::PendingPayment,
            'membership_plan' => 'tribu',
        ]);

        $response = $this->getJson('/api/membership-orders/TRIBU-2026-0001');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'order_number' => 'TRIBU-2026-0001',
                'status'       => 'pending_payment',
            ]);
    }

    public function test_get_unknown_order_returns_404(): void
    {
        $this->getJson('/api/membership-orders/TRIBU-9999-9999')
            ->assertStatus(404);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Webhook → MembershipOrder sync
    // ─────────────────────────────────────────────────────────────────────────

    public function test_webhook_captured_marks_membership_order_paid(): void
    {
        $this->makeEnabledSetting(); // no webhook_id → verification skipped

        $order   = MembershipOrder::factory()->create(['status' => MembershipOrderStatus::PendingPayment]);
        $payment = Payment::factory()->create([
            'payable_type'      => MembershipOrder::class,
            'payable_id'        => $order->id,
            'provider_order_id' => 'PAYID-MEMB001',
            'status'            => PaymentStatus::Approved,
            'amount'            => 120.00,
        ]);

        $this->postJson('/api/webhooks/paypal', [
            'id'            => 'WH-MEMB-CAPT-01',
            'event_type'    => 'PAYMENT.CAPTURE.COMPLETED',
            'resource_type' => 'capture',
            'resource'      => [
                'id'     => 'CAP-MEMB001',
                'status' => 'COMPLETED',
                'supplementary_data' => [
                    'related_ids' => ['order_id' => 'PAYID-MEMB001'],
                ],
            ],
        ])->assertStatus(200);

        $order->refresh();
        $this->assertSame(MembershipOrderStatus::Paid, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertNotNull($order->starts_at);
        $this->assertNotNull($order->expires_at);
    }

    public function test_webhook_denied_marks_membership_order_failed(): void
    {
        $this->makeEnabledSetting();

        $order   = MembershipOrder::factory()->create(['status' => MembershipOrderStatus::PendingPayment]);
        $payment = Payment::factory()->create([
            'payable_type'      => MembershipOrder::class,
            'payable_id'        => $order->id,
            'provider_order_id' => 'PAYID-MEMB002',
            'status'            => PaymentStatus::Approved,
            'amount'            => 120.00,
        ]);

        $this->postJson('/api/webhooks/paypal', [
            'id'            => 'WH-MEMB-FAIL-01',
            'event_type'    => 'PAYMENT.CAPTURE.DENIED',
            'resource_type' => 'capture',
            'resource'      => [
                'id'     => 'CAP-FAIL001',
                'status' => 'DECLINED',
                'supplementary_data' => [
                    'related_ids' => ['order_id' => 'PAYID-MEMB002'],
                ],
            ],
        ])->assertStatus(200);

        $order->refresh();
        $this->assertSame(MembershipOrderStatus::Failed, $order->status);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Security / isolation
    // ─────────────────────────────────────────────────────────────────────────

    public function test_card_data_fields_are_not_stored(): void
    {
        $this->makeActivePlan();
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $payloadWithCard = array_merge($this->validPayload(), [
            'card_number' => '4111111111111111',
            'card_expiry' => '01/28',
            'card_cvv'    => '123',
        ]);

        $this->postJson('/api/membership-orders', $payloadWithCard)->assertStatus(201);

        $order = MembershipOrder::query()->first();
        $this->assertNull($order?->metadata['card_number'] ?? null);
        $this->assertNull($order?->metadata['card_cvv'] ?? null);

        $this->assertDatabaseMissing('membership_orders', ['identification_number' => '4111111111111111']);
    }

    public function test_client_secret_is_not_persisted_in_payment_provider_payload(): void
    {
        $this->makeActivePlan();
        $this->makeEnabledSetting();
        $this->fakePayPalSuccess();

        $this->postJson('/api/membership-orders', $this->validPayload())->assertStatus(201);

        $payment = Payment::query()->first();

        $this->assertNotNull($payment);
        $this->assertStringNotContainsString(
            'test-client-secret',
            json_encode($payment->provider_payload ?? []),
        );
    }

    public function test_no_store_or_ticket_endpoints_created(): void
    {
        $this->post('/api/store-orders')->assertStatus(404);
        $this->post('/api/ticket-orders')->assertStatus(404);
        $this->post('/api/cart/checkout')->assertStatus(404);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function makeEnabledSetting(): PaymentSetting
    {
        return PaymentSetting::create([
            'provider'      => 'paypal',
            'mode'          => 'sandbox',
            'client_id'     => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'currency'      => 'USD',
            'is_enabled'    => true,
            'webhook_id'    => null,
        ]);
    }

    private function makeActivePlan(
        string $price = '120.00',
        string $name = 'Socio Indio',
        array $benefits = ['Preventa exclusiva'],
    ): MembershipPlan {
        return MembershipPlan::factory()->create([
            'code' => 'tribu',
            'name' => $name,
            'price' => $price,
            'currency' => 'USD',
            'duration_months' => 12,
            'benefits' => $benefits,
            'kit_items' => ['Carnet digital'],
            'partner_discounts' => ['Cafe Atalaya'],
            'is_active' => true,
        ]);
    }

    private function fakePayPalSuccess(): void
    {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'fake-token',
                'token_type'   => 'Bearer',
                'expires_in'   => 32400,
            ], 200),
            '*/v2/checkout/orders' => Http::response([
                'id'     => 'PAYID-MEMB-TEST',
                'status' => 'CREATED',
                'links'  => [
                    ['rel' => 'approve', 'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=PAYID-MEMB-TEST'],
                ],
            ], 201),
        ]);
    }

    private function validPayload(): array
    {
        return [
            'full_name'       => 'Juan Perez',
            'email'           => 'juan@example.com',
            'membership_plan' => 'tribu',
            'accept_terms'    => true,
        ];
    }
}
