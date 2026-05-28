<?php

namespace App\Domain\Ticketing\Services;

use App\Domain\Ticketing\Enums\IssuedTicketStatus;
use App\Domain\Ticketing\Enums\TicketOrderStatus;
use App\Domain\Ticketing\Exceptions\TicketIssuingException;
use App\Domain\Ticketing\Models\IssuedTicket;
use App\Domain\Ticketing\Models\TicketOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TicketIssuingService
{
    public function issueForOrder(TicketOrder $order): Collection
    {
        if ($order->status !== TicketOrderStatus::Paid) {
            throw new TicketIssuingException(
                "Cannot issue tickets for order {$order->order_number}: status is {$order->status->value}.",
            );
        }

        // Idempotent: return existing tickets if already issued
        $existing = IssuedTicket::query()
            ->where('ticket_order_id', $order->id)
            ->get();

        if ($existing->isNotEmpty()) {
            return $existing;
        }

        $issued = collect();

        DB::transaction(function () use ($order, &$issued): void {
            $order->loadMissing('items');

            foreach ($order->items as $item) {
                for ($i = 1; $i <= $item->quantity; $i++) {
                    $token  = bin2hex(random_bytes(20));
                    $ticket = IssuedTicket::create([
                        'ticket_order_id'      => $order->id,
                        'ticket_order_item_id' => $item->id,
                        'token'                => $token,
                        'qr_payload'           => $token,
                        'zone_name'            => $item->zone_name,
                        'seat_label'           => "{$item->zone_name} #{$i}",
                        'status'               => IssuedTicketStatus::Issued,
                        'issued_at'            => now(),
                    ]);

                    $issued->push($ticket);
                }
            }
        });

        return $issued;
    }
}
