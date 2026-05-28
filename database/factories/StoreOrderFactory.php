<?php

namespace Database\Factories;

use App\Domain\Store\Enums\StoreOrderStatus;
use App\Domain\Store\Models\StoreOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreOrder>
 */
class StoreOrderFactory extends Factory
{
    protected $model = StoreOrder::class;

    public function definition(): array
    {
        return [
            'order_number' => sprintf('STORE-%d-%04d', now()->year, fake()->unique()->numberBetween(1, 9999)),
            'status' => StoreOrderStatus::PendingPayment,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->optional()->phoneNumber(),
            'subtotal' => '65.00',
            'discount_total' => '0.00',
            'tax_total' => '0.00',
            'total' => '65.00',
            'currency' => 'USD',
            'paid_at' => null,
            'cancelled_at' => null,
            'metadata' => null,
        ];
    }
}
