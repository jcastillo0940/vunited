<?php

namespace App\Domain\Ticketing\Services;

use App\Domain\Ticketing\Enums\IssuedTicketStatus;
use App\Domain\Ticketing\Models\IssuedTicket;

class TicketValidationService
{
    public function validate(string $token): array
    {
        $ticket = IssuedTicket::query()
            ->where('token', $token)
            ->with(['ticketOrder.matchEvent'])
            ->first();

        if ($ticket === null) {
            return [
                'valid'  => false,
                'reason' => 'not_found',
                'error'  => 'Boleto no encontrado.',
            ];
        }

        if ($ticket->status === IssuedTicketStatus::Used) {
            return [
                'valid'  => false,
                'reason' => 'already_used',
                'error'  => 'Boleto ya fue utilizado.',
                'ticket' => $this->ticketSummary($ticket),
            ];
        }

        if ($ticket->status === IssuedTicketStatus::Voided) {
            return [
                'valid'  => false,
                'reason' => 'voided',
                'error'  => 'Boleto anulado.',
                'ticket' => $this->ticketSummary($ticket),
            ];
        }

        $ticket->update([
            'status'  => IssuedTicketStatus::Used,
            'used_at' => now(),
        ]);

        return [
            'valid'  => true,
            'ticket' => $this->ticketSummary($ticket->refresh()),
        ];
    }

    private function ticketSummary(IssuedTicket $ticket): array
    {
        $order = $ticket->ticketOrder;
        $match = $order?->matchEvent;

        return [
            'id'           => $ticket->id,
            'seat_label'   => $ticket->seat_label,
            'zone_name'    => $ticket->zone_name,
            'status'       => $ticket->status->value,
            'issued_at'    => $ticket->issued_at?->toISOString(),
            'used_at'      => $ticket->used_at?->toISOString(),
            'order_number' => $order?->order_number,
            'customer'     => $order?->customer_name ?? $order?->customer_email,
            'match'        => $match ? "{$match->home_team} vs {$match->away_team}" : null,
            'match_date'   => $match?->match_date?->toISOString(),
        ];
    }
}
