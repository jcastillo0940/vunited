<?php

namespace Database\Factories;

use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'payable_type'        => null,
            'payable_id'          => null,
            'provider'            => 'paypal',
            'provider_order_id'   => null,
            'provider_capture_id' => null,
            'status'              => PaymentStatus::Pending,
            'currency'            => 'USD',
            'amount'              => fake()->randomFloat(2, 5, 500),
            'description'         => fake()->sentence(),
            'customer_email'      => fake()->safeEmail(),
            'customer_name'       => fake()->name(),
            'metadata'            => null,
            'provider_payload'    => null,
            'approved_at'         => null,
            'captured_at'         => null,
            'failed_at'           => null,
            'cancelled_at'        => null,
            'refunded_at'         => null,
        ];
    }

    public function pending(): self
    {
        return $this->state(['status' => PaymentStatus::Pending]);
    }

    public function captured(): self
    {
        return $this->state([
            'status'              => PaymentStatus::Captured,
            'provider_order_id'   => 'PAYID-' . fake()->regexify('[A-Z0-9]{20}'),
            'provider_capture_id' => 'CAP-' . fake()->regexify('[A-Z0-9]{20}'),
            'approved_at'         => now()->subMinutes(5),
            'captured_at'         => now(),
        ]);
    }

    public function failed(): self
    {
        return $this->state([
            'status'    => PaymentStatus::Failed,
            'failed_at' => now(),
        ]);
    }

    public function cancelled(): self
    {
        return $this->state([
            'status'       => PaymentStatus::Cancelled,
            'cancelled_at' => now(),
        ]);
    }
}
