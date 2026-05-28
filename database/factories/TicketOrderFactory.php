<?php

namespace Database\Factories;

use App\Domain\Ticketing\Enums\TicketOrderStatus;
use App\Domain\Ticketing\Models\MatchEvent;
use App\Domain\Ticketing\Models\TicketOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketOrder>
 */
class TicketOrderFactory extends Factory
{
    protected $model = TicketOrder::class;

    private static int $seq = 0;

    public function definition(): array
    {
        self::$seq++;

        return [
            'order_number'   => sprintf('TICKET-%d-%04d', now()->year, self::$seq),
            'match_event_id' => MatchEvent::factory(),
            'status'         => TicketOrderStatus::PendingPayment,
            'customer_name'  => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => null,
            'subtotal'       => '25.00',
            'discount_total' => '0.00',
            'tax_total'      => '0.00',
            'total'          => '25.00',
            'currency'       => 'USD',
            'paid_at'        => null,
            'cancelled_at'   => null,
            'metadata'       => null,
        ];
    }

    public function paid(): self
    {
        return $this->state([
            'status'  => TicketOrderStatus::Paid,
            'paid_at' => now(),
        ]);
    }

    public function failed(): self
    {
        return $this->state(['status' => TicketOrderStatus::Failed]);
    }
}
