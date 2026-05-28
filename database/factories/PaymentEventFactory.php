<?php

namespace Database\Factories;

use App\Domain\Payments\Enums\PaymentEventProcessingStatus;
use App\Domain\Payments\Enums\PaymentEventVerificationStatus;
use App\Domain\Payments\Models\PaymentEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentEvent>
 */
class PaymentEventFactory extends Factory
{
    protected $model = PaymentEvent::class;

    public function definition(): array
    {
        return [
            'payment_id'          => null,
            'provider'            => 'paypal',
            'provider_event_id'   => 'WH-' . strtoupper(fake()->lexify('????????????????????')),
            'event_type'          => 'PAYMENT.CAPTURE.COMPLETED',
            'resource_type'       => 'capture',
            'provider_order_id'   => 'PAYID-' . strtoupper(fake()->lexify('????????????')),
            'provider_capture_id' => null,
            'verification_status' => PaymentEventVerificationStatus::Verified,
            'processing_status'   => PaymentEventProcessingStatus::Received,
            'payload'             => ['event_type' => 'PAYMENT.CAPTURE.COMPLETED'],
            'headers'             => null,
            'received_at'         => now(),
            'verified_at'         => now(),
            'processed_at'        => null,
            'error_message'       => null,
        ];
    }

    public function processed(): self
    {
        return $this->state([
            'processing_status' => PaymentEventProcessingStatus::Processed,
            'processed_at'      => now(),
        ]);
    }

    public function ignored(): self
    {
        return $this->state([
            'processing_status' => PaymentEventProcessingStatus::Ignored,
        ]);
    }
}
