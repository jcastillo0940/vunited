<?php

namespace Database\Factories;

use App\Domain\Ticketing\Enums\IssuedTicketStatus;
use App\Domain\Ticketing\Models\IssuedTicket;
use App\Domain\Ticketing\Models\TicketOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IssuedTicket>
 */
class IssuedTicketFactory extends Factory
{
    protected $model = IssuedTicket::class;

    public function definition(): array
    {
        $token = bin2hex(random_bytes(20));

        return [
            'ticket_order_id'      => TicketOrder::factory()->paid(),
            'ticket_order_item_id' => null,
            'token'                => $token,
            'qr_payload'           => $token,
            'zone_name'            => fake()->randomElement(['General', 'Preferencia', 'VIP']),
            'seat_label'           => null,
            'status'               => IssuedTicketStatus::Issued,
            'issued_at'            => now(),
            'used_at'              => null,
            'voided_at'            => null,
            'metadata'             => null,
        ];
    }

    public function used(): self
    {
        return $this->state([
            'status'  => IssuedTicketStatus::Used,
            'used_at' => now(),
        ]);
    }

    public function voided(): self
    {
        return $this->state([
            'status'    => IssuedTicketStatus::Voided,
            'voided_at' => now(),
        ]);
    }
}
