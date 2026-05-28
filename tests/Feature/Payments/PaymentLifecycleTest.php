<?php

namespace Tests\Feature\Payments;

use App\Domain\Payments\Data\PaymentProviderResult;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Models\Payment;
use App\Domain\Payments\Services\PaymentLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class PaymentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private PaymentLifecycleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentLifecycleService();
    }

    public function test_can_create_pending_payment(): void
    {
        $payment = $this->service->createPendingPayment([
            'amount'         => 50.00,
            'currency'       => 'USD',
            'description'    => 'Test payment',
            'customer_email' => 'fan@veraguasunited.test',
            'customer_name'  => 'Juan Perez',
        ]);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame(PaymentStatus::Pending, $payment->status);
        $this->assertSame('50.00', $payment->amount);
        $this->assertSame('USD', $payment->currency);
        $this->assertNull($payment->approved_at);
        $this->assertNull($payment->captured_at);
        $this->assertDatabaseHas('payments', [
            'id'     => $payment->id,
            'status' => 'pending',
        ]);
    }

    public function test_amount_must_be_greater_than_zero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment amount must be greater than zero.');

        $this->service->createPendingPayment([
            'amount'   => 0,
            'currency' => 'USD',
        ]);
    }

    public function test_negative_amount_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->createPendingPayment([
            'amount'   => -10.00,
            'currency' => 'USD',
        ]);
    }

    public function test_currency_defaults_to_usd(): void
    {
        $payment = $this->service->createPendingPayment(['amount' => 20.00]);

        $this->assertSame('USD', $payment->currency);
    }

    public function test_can_mark_provider_created_with_provider_order_id(): void
    {
        $payment = Payment::factory()->create(['amount' => 30.00]);

        $result = PaymentProviderResult::success(
            providerOrderId: 'PAYID-ABC123',
            rawPayload: ['id' => 'PAYID-ABC123', 'status' => 'CREATED'],
        );

        $updated = $this->service->markProviderCreated($payment, $result);

        $this->assertSame(PaymentStatus::ProviderCreated, $updated->status);
        $this->assertSame('PAYID-ABC123', $updated->provider_order_id);
        $this->assertSame(['id' => 'PAYID-ABC123', 'status' => 'CREATED'], $updated->provider_payload);
    }

    public function test_can_mark_approved(): void
    {
        $payment = Payment::factory()->create(['amount' => 30.00]);

        $updated = $this->service->markApproved($payment, ['approval_token' => 'tok_xyz']);

        $this->assertSame(PaymentStatus::Approved, $updated->status);
        $this->assertNotNull($updated->approved_at);
        $this->assertSame(['approval_token' => 'tok_xyz'], $updated->provider_payload);
    }

    public function test_can_mark_captured_with_provider_capture_id(): void
    {
        $payment = Payment::factory()->create(['amount' => 75.00]);

        $result = PaymentProviderResult::success(
            providerOrderId: 'PAYID-ORDER99',
            providerCaptureId: 'CAP-XYZ999',
            rawPayload: ['capture_id' => 'CAP-XYZ999', 'status' => 'COMPLETED'],
        );

        $updated = $this->service->markCaptured($payment, $result);

        $this->assertSame(PaymentStatus::Captured, $updated->status);
        $this->assertSame('CAP-XYZ999', $updated->provider_capture_id);
        $this->assertNotNull($updated->captured_at);
        $this->assertSame(['capture_id' => 'CAP-XYZ999', 'status' => 'COMPLETED'], $updated->provider_payload);
    }

    public function test_can_mark_captured_with_array_payload(): void
    {
        $payment = Payment::factory()->create(['amount' => 40.00]);

        $updated = $this->service->markCaptured($payment, ['raw' => 'data']);

        $this->assertSame(PaymentStatus::Captured, $updated->status);
        $this->assertNotNull($updated->captured_at);
        $this->assertSame(['raw' => 'data'], $updated->provider_payload);
    }

    public function test_can_mark_failed_with_message_and_payload(): void
    {
        $payment = Payment::factory()->create(['amount' => 60.00]);

        $updated = $this->service->markFailed(
            $payment,
            'Card declined by issuer.',
            ['error_code' => 'INSTRUMENT_DECLINED'],
        );

        $this->assertSame(PaymentStatus::Failed, $updated->status);
        $this->assertNotNull($updated->failed_at);
        $this->assertSame('Card declined by issuer.', $updated->metadata['failure_reason']);
        $this->assertSame(['error_code' => 'INSTRUMENT_DECLINED'], $updated->provider_payload);
    }

    public function test_can_mark_cancelled(): void
    {
        $payment = Payment::factory()->create(['amount' => 25.00]);

        $updated = $this->service->markCancelled($payment, ['reason' => 'user_aborted']);

        $this->assertSame(PaymentStatus::Cancelled, $updated->status);
        $this->assertNotNull($updated->cancelled_at);
        $this->assertSame(['reason' => 'user_aborted'], $updated->provider_payload);
    }

    public function test_can_mark_refunded(): void
    {
        $payment = Payment::factory()->captured()->create();

        $updated = $this->service->markRefunded($payment, ['refund_id' => 'REF-001']);

        $this->assertSame(PaymentStatus::Refunded, $updated->status);
        $this->assertNotNull($updated->refunded_at);
    }

    public function test_captured_requires_amount_greater_than_zero(): void
    {
        // Create directly bypassing the service guard to test markCaptured's own guard.
        $payment = Payment::factory()->create(['amount' => 0.00]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot capture a payment with amount <= 0.');

        $this->service->markCaptured($payment, []);
    }

    public function test_payment_can_store_polymorphic_payable_values(): void
    {
        $payment = Payment::factory()->create([
            'payable_type' => 'App\\Domain\\Tickets\\Models\\TicketOrder',
            'payable_id'   => 42,
        ]);

        $this->assertSame('App\\Domain\\Tickets\\Models\\TicketOrder', $payment->payable_type);
        $this->assertSame(42, $payment->payable_id);
    }

    public function test_payment_payable_fields_can_be_null(): void
    {
        $payment = Payment::factory()->create([
            'payable_type' => null,
            'payable_id'   => null,
        ]);

        $this->assertNull($payment->payable_type);
        $this->assertNull($payment->payable_id);
    }

    public function test_no_public_payment_endpoints_exist(): void
    {
        $this->get('/payment/create-order')->assertStatus(404);
        $this->post('/payment/create-order')->assertStatus(404);
        $this->post('/payment/capture-order')->assertStatus(404);
        $this->post('/webhooks/paypal')->assertStatus(404);
        $this->get('/api/payment')->assertStatus(404);
    }
}
