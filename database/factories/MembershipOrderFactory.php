<?php

namespace Database\Factories;

use App\Domain\Memberships\Enums\MembershipOrderStatus;
use App\Domain\Memberships\Models\MembershipOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MembershipOrder>
 */
class MembershipOrderFactory extends Factory
{
    protected $model = MembershipOrder::class;

    public function definition(): array
    {
        static $seq = 0;
        $seq++;

        return [
            'order_number'          => sprintf('TRIBU-%d-%04d', now()->year, $seq),
            'status'                => MembershipOrderStatus::PendingPayment,
            'full_name'             => fake()->name(),
            'email'                 => fake()->safeEmail(),
            'identification_number' => null,
            'birth_date'            => null,
            'age'                   => null,
            'address'               => null,
            'phone'                 => null,
            'membership_plan'       => 'tribu',
            'membership_price'      => '120.00',
            'currency'              => 'USD',
            'starts_at'             => null,
            'expires_at'            => null,
            'paid_at'               => null,
            'cancelled_at'          => null,
            'metadata'              => null,
        ];
    }

    public function paid(): self
    {
        return $this->state([
            'status'     => MembershipOrderStatus::Paid,
            'paid_at'    => now(),
            'starts_at'  => now(),
            'expires_at' => now()->addYear(),
        ]);
    }

    public function failed(): self
    {
        return $this->state(['status' => MembershipOrderStatus::Failed]);
    }
}
