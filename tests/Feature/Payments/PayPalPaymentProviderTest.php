<?php

namespace Tests\Feature\Payments;

use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Models\PaymentSetting;
use App\Domain\Payments\Providers\PayPalPaymentProvider;
use App\Domain\Payments\Services\PayPalAccessTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayPalPaymentProviderTest extends TestCase
{
    use RefreshDatabase;

    private PayPalPaymentProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new PayPalPaymentProvider(new PayPalAccessTokenService());
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Configuration guards
    // ─────────────────────────────────────────────────────────────────────────

    public function test_create_order_fails_if_paypal_is_disabled(): void
    {
        $this->makeSandboxSetting(['is_enabled' => false]);
        $payment = Payment::factory()->create(['amount' => 25.00]);

        $result = $this->provider->createOrder($payment);

        $this->assertFalse($result->success);
        $this->assertStringContainsStringIgnoringCase('not enabled', $result->message ?? '');
    }

    public function test_create_order_fails_if_no_setting_exists(): void
    {
        // No PaymentSetting created — settings table empty
        $payment = Payment::factory()->create(['amount' => 10.00]);

        $result = $this->provider->createOrder($payment);

        $this->assertFalse($result->success);
        $this->assertNotEmpty($result->message);
    }

    public function test_create_order_fails_if_credentials_missing(): void
    {
        $this->makeSandboxSetting(['client_secret' => null]);
        $payment = Payment::factory()->create(['amount' => 30.00]);

        $result = $this->provider->createOrder($payment);

        $this->assertFalse($result->success);
        $this->assertNotEmpty($result->message);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Access token
    // ─────────────────────────────────────────────────────────────────────────

    public function test_access_token_requested_with_basic_auth(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response($this->tokenPayload(), 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response($this->createdOrderPayload(), 201),
        ]);

        $this->makeSandboxSetting();
        $payment = Payment::factory()->create(['amount' => 50.00]);

        $this->provider->createOrder($payment);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v1/oauth2/token')
                && str_starts_with($request->header('Authorization')[0] ?? '', 'Basic ');
        });
    }

    public function test_create_order_fails_if_token_request_fails(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['error' => 'invalid_client'], 401),
        ]);

        $this->makeSandboxSetting();
        $payment = Payment::factory()->create(['amount' => 20.00]);

        $result = $this->provider->createOrder($payment);

        $this->assertFalse($result->success);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // createOrder — success path
    // ─────────────────────────────────────────────────────────────────────────

    public function test_create_order_sends_correct_payload(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response($this->tokenPayload(), 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response($this->createdOrderPayload(), 201),
        ]);

        $this->makeSandboxSetting();
        $payment = Payment::factory()->create([
            'amount'      => 75.00,
            'currency'    => 'USD',
            'description' => 'Test ticket purchase',
        ]);

        $this->provider->createOrder($payment);

        Http::assertSent(function ($request) use ($payment) {
            if (! str_contains($request->url(), '/v2/checkout/orders')) {
                return false;
            }
            $body = $request->data();

            return $body['intent'] === 'CAPTURE'
                && ($body['purchase_units'][0]['amount']['currency_code'] ?? null) === 'USD'
                && ($body['purchase_units'][0]['amount']['value'] ?? null) === '75.00'
                && ($body['purchase_units'][0]['description'] ?? null) === 'Test ticket purchase'
                && ($body['purchase_units'][0]['reference_id'] ?? null) === 'payment-' . $payment->id;
        });
    }

    public function test_create_order_returns_provider_order_id_and_redirect_url(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response($this->tokenPayload(), 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response($this->createdOrderPayload(), 201),
        ]);

        $this->makeSandboxSetting();
        $payment = Payment::factory()->create(['amount' => 40.00]);

        $result = $this->provider->createOrder($payment);

        $this->assertTrue($result->success);
        $this->assertSame('PAYID-TESTORDER123', $result->providerOrderId);
        $this->assertStringContainsString('sandbox.paypal.com', $result->redirectUrl ?? '');
        $this->assertNotEmpty($result->rawPayload);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // createOrder — error paths
    // ─────────────────────────────────────────────────────────────────────────

    public function test_create_order_handles_paypal_http_error(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response($this->tokenPayload(), 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response([
                'name'    => 'UNPROCESSABLE_ENTITY',
                'message' => 'The requested action could not be performed.',
            ], 422),
        ]);

        $this->makeSandboxSetting();
        $payment = Payment::factory()->create(['amount' => 30.00]);

        $result = $this->provider->createOrder($payment);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('could not be performed', $result->message ?? '');
    }

    public function test_create_order_handles_missing_approve_link(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response($this->tokenPayload(), 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response([
                'id'     => 'PAYID-NOAPPROVE',
                'status' => 'CREATED',
                'links'  => [
                    ['rel' => 'self', 'href' => 'https://api-m.sandbox.paypal.com/v2/checkout/orders/PAYID-NOAPPROVE'],
                ],
            ], 201),
        ]);

        $this->makeSandboxSetting();
        $payment = Payment::factory()->create(['amount' => 35.00]);

        $result = $this->provider->createOrder($payment);

        $this->assertFalse($result->success);
        $this->assertStringContainsStringIgnoringCase('approve link', $result->message ?? '');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // captureOrder — guards
    // ─────────────────────────────────────────────────────────────────────────

    public function test_capture_order_fails_if_payment_has_no_provider_order_id(): void
    {
        Http::fake(); // No HTTP call should be made

        $this->makeSandboxSetting();
        $payment = Payment::factory()->create([
            'amount'            => 50.00,
            'provider_order_id' => null,
        ]);

        $result = $this->provider->captureOrder($payment);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('provider_order_id', $result->message ?? '');
        Http::assertNothingSent();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // captureOrder — success path
    // ─────────────────────────────────────────────────────────────────────────

    public function test_capture_order_sends_request_to_correct_url(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response($this->tokenPayload(), 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders/PAYID-ORDER99/capture' => Http::response(
                $this->capturedOrderPayload('PAYID-ORDER99', 'CAP-XYZ123'),
                201,
            ),
        ]);

        $this->makeSandboxSetting();
        $payment = Payment::factory()->create([
            'amount'            => 60.00,
            'provider_order_id' => 'PAYID-ORDER99',
        ]);

        $this->provider->captureOrder($payment);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v2/checkout/orders/PAYID-ORDER99/capture');
        });
    }

    public function test_capture_order_returns_provider_capture_id(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response($this->tokenPayload(), 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders/PAYID-ORDER99/capture' => Http::response(
                $this->capturedOrderPayload('PAYID-ORDER99', 'CAP-XYZ123'),
                201,
            ),
        ]);

        $this->makeSandboxSetting();
        $payment = Payment::factory()->create([
            'amount'            => 60.00,
            'provider_order_id' => 'PAYID-ORDER99',
        ]);

        $result = $this->provider->captureOrder($payment);

        $this->assertTrue($result->success);
        $this->assertSame('PAYID-ORDER99', $result->providerOrderId);
        $this->assertSame('CAP-XYZ123', $result->providerCaptureId);
        $this->assertSame('COMPLETED', $result->status);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // captureOrder — error path
    // ─────────────────────────────────────────────────────────────────────────

    public function test_capture_order_handles_paypal_http_error(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response($this->tokenPayload(), 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders/PAYID-BAD/capture' => Http::response([
                'name'    => 'ORDER_ALREADY_CAPTURED',
                'message' => 'Order already captured.',
            ], 422),
        ]);

        $this->makeSandboxSetting();
        $payment = Payment::factory()->create([
            'amount'            => 50.00,
            'provider_order_id' => 'PAYID-BAD',
        ]);

        $result = $this->provider->captureOrder($payment);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('already captured', $result->message ?? '');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Refund
    // ─────────────────────────────────────────────────────────────────────────

    public function test_refund_returns_not_supported_failure(): void
    {
        $payment = Payment::factory()->captured()->create();

        $result = $this->provider->refund($payment);

        $this->assertFalse($result->success);
        $this->assertStringContainsStringIgnoringCase('not yet supported', $result->message ?? '');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Security
    // ─────────────────────────────────────────────────────────────────────────

    public function test_client_secret_not_in_raw_payload(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response($this->tokenPayload(), 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response($this->createdOrderPayload(), 201),
        ]);

        $this->makeSandboxSetting(['client_secret' => 'ultra-secret-credential-value']);
        $payment = Payment::factory()->create(['amount' => 50.00]);

        $result = $this->provider->createOrder($payment);

        $this->assertTrue($result->success);
        $this->assertStringNotContainsString('ultra-secret-credential-value', json_encode($result->rawPayload));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Sandbox vs Live URL
    // ─────────────────────────────────────────────────────────────────────────

    public function test_sandbox_url_used_when_mode_is_sandbox(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response($this->tokenPayload(), 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response($this->createdOrderPayload(), 201),
        ]);

        $this->makeSandboxSetting(['mode' => 'sandbox']);
        $payment = Payment::factory()->create(['amount' => 20.00]);

        $this->provider->createOrder($payment);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api-m.sandbox.paypal.com');
        });
    }

    public function test_live_url_used_when_mode_is_live(): void
    {
        Http::fake([
            'https://api-m.paypal.com/v1/oauth2/token' => Http::response($this->tokenPayload(), 200),
            'https://api-m.paypal.com/v2/checkout/orders' => Http::response($this->createdOrderPayload(), 201),
        ]);

        $this->makeSandboxSetting(['mode' => 'live']);
        $payment = Payment::factory()->create(['amount' => 20.00]);

        $this->provider->createOrder($payment);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api-m.paypal.com')
                && ! str_contains($request->url(), 'sandbox');
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // No public endpoints
    // ─────────────────────────────────────────────────────────────────────────

    public function test_no_public_payment_endpoints_exist(): void
    {
        $this->get('/payment/create-order')->assertStatus(404);
        $this->post('/payment/create-order')->assertStatus(404);
        $this->post('/payment/capture-order')->assertStatus(404);
        $this->post('/webhooks/paypal')->assertStatus(404);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function makeSandboxSetting(array $overrides = []): PaymentSetting
    {
        return PaymentSetting::create(array_merge([
            'provider'      => 'paypal',
            'mode'          => 'sandbox',
            'client_id'     => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'currency'      => 'USD',
            'is_enabled'    => true,
        ], $overrides));
    }

    private function tokenPayload(): array
    {
        return [
            'access_token' => 'fake-access-token-ABC',
            'token_type'   => 'Bearer',
            'expires_in'   => 32400,
        ];
    }

    private function createdOrderPayload(): array
    {
        return [
            'id'     => 'PAYID-TESTORDER123',
            'status' => 'CREATED',
            'links'  => [
                [
                    'rel'    => 'self',
                    'href'   => 'https://api-m.sandbox.paypal.com/v2/checkout/orders/PAYID-TESTORDER123',
                    'method' => 'GET',
                ],
                [
                    'rel'    => 'approve',
                    'href'   => 'https://www.sandbox.paypal.com/checkoutnow?token=PAYID-TESTORDER123',
                    'method' => 'GET',
                ],
                [
                    'rel'    => 'capture',
                    'href'   => 'https://api-m.sandbox.paypal.com/v2/checkout/orders/PAYID-TESTORDER123/capture',
                    'method' => 'POST',
                ],
            ],
        ];
    }

    private function capturedOrderPayload(string $orderId, string $captureId): array
    {
        return [
            'id'             => $orderId,
            'status'         => 'COMPLETED',
            'purchase_units' => [
                [
                    'payments' => [
                        'captures' => [
                            [
                                'id'            => $captureId,
                                'status'        => 'COMPLETED',
                                'amount'        => ['currency_code' => 'USD', 'value' => '60.00'],
                                'final_capture' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
