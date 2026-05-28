<?php

namespace Tests\Feature\Payments;

use App\Domain\Payments\Enums\PaymentEventProcessingStatus;
use App\Domain\Payments\Enums\PaymentEventVerificationStatus;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Models\PaymentEvent;
use App\Domain\Payments\Models\PaymentSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayPalWebhookTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────────────────────────────────
    // Endpoint existence + basic validation
    // ─────────────────────────────────────────────────────────────────────────

    public function test_webhook_endpoint_accepts_post(): void
    {
        $this->makeEnabledSetting();
        $this->fakeVerificationSkipped(); // no webhook_id

        $this->postJson('/api/webhooks/paypal', $this->orderApprovedPayload())
            ->assertStatus(200);
    }

    public function test_empty_payload_returns_400(): void
    {
        $this->postJson('/api/webhooks/paypal', [])
            ->assertStatus(400);
    }

    public function test_payload_without_event_type_returns_400(): void
    {
        $this->postJson('/api/webhooks/paypal', ['id' => 'WH-001', 'resource' => []])
            ->assertStatus(400);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Persistence
    // ─────────────────────────────────────────────────────────────────────────

    public function test_valid_event_is_saved_to_payment_events(): void
    {
        $this->makeEnabledSetting();
        $this->fakeVerificationSkipped();

        $this->postJson('/api/webhooks/paypal', $this->orderApprovedPayload('WH-EVT-SAVE'));

        $this->assertDatabaseHas('payment_events', [
            'provider'          => 'paypal',
            'provider_event_id' => 'WH-EVT-SAVE',
            'event_type'        => 'CHECKOUT.ORDER.APPROVED',
        ]);
    }

    public function test_paypal_headers_are_stored(): void
    {
        $this->makeEnabledSetting();
        $this->fakeVerificationSkipped();

        $this->withHeaders([
            'PAYPAL-TRANSMISSION-ID' => 'tx-12345',
            'PAYPAL-AUTH-ALGO'       => 'SHA256withRSA',
        ])->postJson('/api/webhooks/paypal', $this->orderApprovedPayload('WH-EVT-HDR'));

        $event = PaymentEvent::query()->where('provider_event_id', 'WH-EVT-HDR')->first();
        $this->assertNotNull($event);
        $this->assertSame('tx-12345', $event->headers['paypal-transmission-id'] ?? null);
        $this->assertSame('SHA256withRSA', $event->headers['paypal-auth-algo'] ?? null);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Idempotency
    // ─────────────────────────────────────────────────────────────────────────

    public function test_duplicate_event_is_not_processed_twice(): void
    {
        $this->makeEnabledSetting();
        $this->fakeVerificationSkipped();

        $payment = Payment::factory()->create([
            'provider_order_id' => 'PAYID-IDEMPTEST',
            'status'            => PaymentStatus::Pending,
        ]);

        $payload = $this->orderApprovedPayload('WH-DUPL-001', 'PAYID-IDEMPTEST');

        $this->postJson('/api/webhooks/paypal', $payload)->assertStatus(200);
        $this->postJson('/api/webhooks/paypal', $payload)->assertStatus(200);

        $this->assertDatabaseCount('payment_events', 1);
        $payment->refresh();
        $this->assertSame(PaymentStatus::Approved, $payment->status);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Verification
    // ─────────────────────────────────────────────────────────────────────────

    public function test_verification_failure_does_not_update_payment(): void
    {
        $this->makeEnabledSetting(['webhook_id' => 'WH-REAL-WEBHOOK-ID']);
        $this->fakeVerificationCall(verified: false);

        $payment = Payment::factory()->create([
            'provider_order_id' => 'PAYID-NOUPDATE',
            'status'            => PaymentStatus::ProviderCreated,
        ]);

        $this->postJson('/api/webhooks/paypal', $this->orderApprovedPayload('WH-FAIL-001', 'PAYID-NOUPDATE'))
            ->assertStatus(200);

        $payment->refresh();
        $this->assertSame(PaymentStatus::ProviderCreated, $payment->status);

        $event = PaymentEvent::query()->where('provider_event_id', 'WH-FAIL-001')->first();
        $this->assertSame(PaymentEventVerificationStatus::Failed, $event->verification_status);
        $this->assertSame(PaymentEventProcessingStatus::Received, $event->processing_status);
    }

    public function test_verification_success_stores_verified_status(): void
    {
        $this->makeEnabledSetting(['webhook_id' => 'WH-REAL-WEBHOOK-ID']);
        $this->fakeVerificationCall(verified: true);

        Payment::factory()->create([
            'provider_order_id' => 'PAYID-VEROK',
            'status'            => PaymentStatus::ProviderCreated,
        ]);

        $this->postJson('/api/webhooks/paypal', $this->orderApprovedPayload('WH-VER-OK', 'PAYID-VEROK'));

        $event = PaymentEvent::query()->where('provider_event_id', 'WH-VER-OK')->first();
        $this->assertSame(PaymentEventVerificationStatus::Verified, $event->verification_status);
        $this->assertNotNull($event->verified_at);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Event processing — supported types
    // ─────────────────────────────────────────────────────────────────────────

    public function test_order_approved_marks_payment_approved(): void
    {
        $this->makeEnabledSetting();
        $this->fakeVerificationSkipped();

        $payment = Payment::factory()->create([
            'provider_order_id' => 'PAYID-APV01',
            'status'            => PaymentStatus::ProviderCreated,
        ]);

        $this->postJson('/api/webhooks/paypal', $this->orderApprovedPayload('WH-APV-01', 'PAYID-APV01'))
            ->assertStatus(200);

        $payment->refresh();
        $this->assertSame(PaymentStatus::Approved, $payment->status);
        $this->assertNotNull($payment->approved_at);
    }

    public function test_capture_completed_marks_payment_captured(): void
    {
        $this->makeEnabledSetting();
        $this->fakeVerificationSkipped();

        $payment = Payment::factory()->create([
            'provider_order_id' => 'PAYID-CAP01',
            'status'            => PaymentStatus::Approved,
            'amount'            => 50.00,
        ]);

        $payload = $this->captureCompletedPayload('WH-CAP-01', 'PAYID-CAP01', 'CAP-XYZ001');

        $this->postJson('/api/webhooks/paypal', $payload)->assertStatus(200);

        $payment->refresh();
        $this->assertSame(PaymentStatus::Captured, $payment->status);
        $this->assertSame('CAP-XYZ001', $payment->provider_capture_id);
        $this->assertNotNull($payment->captured_at);
    }

    public function test_capture_denied_marks_payment_failed(): void
    {
        $this->makeEnabledSetting();
        $this->fakeVerificationSkipped();

        $payment = Payment::factory()->create([
            'provider_order_id' => 'PAYID-DEN01',
            'status'            => PaymentStatus::Approved,
        ]);

        $this->postJson('/api/webhooks/paypal', $this->captureDeniedPayload('WH-DEN-01', 'PAYID-DEN01', 'CAP-DEN001'))
            ->assertStatus(200);

        $payment->refresh();
        $this->assertSame(PaymentStatus::Failed, $payment->status);
        $this->assertNotNull($payment->failed_at);
    }

    public function test_capture_refunded_marks_payment_refunded(): void
    {
        $this->makeEnabledSetting();
        $this->fakeVerificationSkipped();

        $payment = Payment::factory()->captured()->create(['provider_order_id' => 'PAYID-REF01']);

        $this->postJson('/api/webhooks/paypal', $this->captureRefundedPayload('WH-REF-01', 'PAYID-REF01', $payment->provider_capture_id))
            ->assertStatus(200);

        $payment->refresh();
        $this->assertSame(PaymentStatus::Refunded, $payment->status);
        $this->assertNotNull($payment->refunded_at);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Edge cases
    // ─────────────────────────────────────────────────────────────────────────

    public function test_unknown_event_type_is_marked_ignored(): void
    {
        $this->makeEnabledSetting();
        $this->fakeVerificationSkipped();

        $this->postJson('/api/webhooks/paypal', [
            'id'         => 'WH-UNK-01',
            'event_type' => 'BILLING.SUBSCRIPTION.CREATED',
            'resource'   => [],
        ])->assertStatus(200);

        $event = PaymentEvent::query()->where('provider_event_id', 'WH-UNK-01')->first();
        $this->assertNotNull($event);
        $this->assertSame(PaymentEventProcessingStatus::Ignored, $event->processing_status);
    }

    public function test_event_without_matching_payment_is_marked_ignored(): void
    {
        $this->makeEnabledSetting();
        $this->fakeVerificationSkipped();

        $this->postJson('/api/webhooks/paypal', $this->orderApprovedPayload('WH-NOMATCH', 'PAYID-DOESNOTEXIST'))
            ->assertStatus(200);

        $event = PaymentEvent::query()->where('provider_event_id', 'WH-NOMATCH')->first();
        $this->assertNotNull($event);
        $this->assertSame(PaymentEventProcessingStatus::Ignored, $event->processing_status);
    }

    public function test_event_is_linked_to_payment_after_processing(): void
    {
        $this->makeEnabledSetting();
        $this->fakeVerificationSkipped();

        $payment = Payment::factory()->create([
            'provider_order_id' => 'PAYID-LINK01',
            'status'            => PaymentStatus::ProviderCreated,
        ]);

        $this->postJson('/api/webhooks/paypal', $this->orderApprovedPayload('WH-LINK-01', 'PAYID-LINK01'));

        $event = PaymentEvent::query()->where('provider_event_id', 'WH-LINK-01')->first();
        $this->assertSame($payment->id, $event->payment_id);
        $this->assertSame(PaymentEventProcessingStatus::Processed, $event->processing_status);
        $this->assertNotNull($event->processed_at);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // No checkout / no frontend / no modules
    // ─────────────────────────────────────────────────────────────────────────

    public function test_no_checkout_endpoints_exist(): void
    {
        $this->post('/payment/create-order')->assertStatus(404);
        $this->post('/payment/capture-order')->assertStatus(404);
        $this->post('/api/payment/create-order')->assertStatus(404);
        $this->post('/api/payment/capture-order')->assertStatus(404);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function makeEnabledSetting(array $overrides = []): PaymentSetting
    {
        return PaymentSetting::create(array_merge([
            'provider'      => 'paypal',
            'mode'          => 'sandbox',
            'client_id'     => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'currency'      => 'USD',
            'is_enabled'    => true,
            'webhook_id'    => null,
        ], $overrides));
    }

    private function fakeVerificationSkipped(): void
    {
        // webhook_id is null → service returns Skipped without HTTP calls
    }

    private function fakeVerificationCall(bool $verified): void
    {
        Http::fake([
            '*/v1/oauth2/token' => Http::response([
                'access_token' => 'fake-token',
                'token_type'   => 'Bearer',
                'expires_in'   => 32400,
            ], 200),
            '*/v1/notifications/verify-webhook-signature' => Http::response([
                'verification_status' => $verified ? 'SUCCESS' : 'FAILURE',
            ], 200),
        ]);
    }

    private function orderApprovedPayload(string $eventId = 'WH-EVT-001', string $orderId = 'PAYID-ORDER123'): array
    {
        return [
            'id'            => $eventId,
            'event_type'    => 'CHECKOUT.ORDER.APPROVED',
            'resource_type' => 'checkout-order',
            'resource'      => [
                'id'     => $orderId,
                'status' => 'APPROVED',
            ],
        ];
    }

    private function captureCompletedPayload(string $eventId, string $orderId, string $captureId): array
    {
        return [
            'id'            => $eventId,
            'event_type'    => 'PAYMENT.CAPTURE.COMPLETED',
            'resource_type' => 'capture',
            'resource'      => [
                'id'     => $captureId,
                'status' => 'COMPLETED',
                'supplementary_data' => [
                    'related_ids' => ['order_id' => $orderId],
                ],
            ],
        ];
    }

    private function captureDeniedPayload(string $eventId, string $orderId, string $captureId): array
    {
        return [
            'id'            => $eventId,
            'event_type'    => 'PAYMENT.CAPTURE.DENIED',
            'resource_type' => 'capture',
            'resource'      => [
                'id'     => $captureId,
                'status' => 'DECLINED',
                'supplementary_data' => [
                    'related_ids' => ['order_id' => $orderId],
                ],
            ],
        ];
    }

    private function captureRefundedPayload(string $eventId, string $orderId, ?string $captureId): array
    {
        return [
            'id'            => $eventId,
            'event_type'    => 'PAYMENT.CAPTURE.REFUNDED',
            'resource_type' => 'capture',
            'resource'      => [
                'id'     => $captureId ?? 'CAP-REF',
                'status' => 'COMPLETED',
                'supplementary_data' => [
                    'related_ids' => [
                        'order_id'   => $orderId,
                        'capture_id' => $captureId,
                    ],
                ],
            ],
        ];
    }
}
