<?php

namespace App\Domain\Ticketing\Services;

use App\Domain\Ticketing\Exceptions\TicketIssuingException;
use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Models\Ticket;
use App\Domain\Ticketing\Support\OrderStateMachine;
use App\Domain\Ticketing\Support\TicketQrSigner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TicketIssuingService
{
    public function __construct(private readonly TicketQrSigner $signer) {}

    /**
     * Emite los tickets de una orden pagada. Idempotente: si ya se emitieron
     * tickets para esta orden (reintento del job/webhook), devuelve los
     * mismos sin duplicar. El bloqueo de la orden dentro de la transaccion
     * evita que dos llamadas concurrentes (p. ej. un reintento de webhook
     * solapado con el worker) emitan dos veces.
     *
     * @throws TicketIssuingException
     */
    public function issueForOrder(Order $order): Collection
    {
        return DB::transaction(function () use ($order) {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            $existing = Ticket::query()->where('order_id', $locked->id)->get();
            if ($existing->isNotEmpty()) {
                return $existing;
            }

            if ($locked->status !== 'paid') {
                throw new TicketIssuingException(
                    "No se pueden emitir tickets para la orden {$locked->order_number}: estado es {$locked->status}.",
                );
            }

            OrderStateMachine::assertTransitionAllowed($locked->status, 'tickets_issued');

            $tickets = collect();
            $locked->loadMissing('items');

            foreach ($locked->items as $item) {
                $count = $item->seat_id ? 1 : $item->quantity;

                for ($i = 0; $i < $count; $i++) {
                    $ticket = Ticket::create([
                        'order_id' => $locked->id,
                        'order_item_id' => $item->id,
                        'event_id' => $locked->event_id,
                        'zone_id' => $item->zone_id,
                        'seat_id' => $item->seat_id,
                        'status' => 'issued',
                        'qr_token' => bin2hex(random_bytes(16)), // placeholder unico, se reemplaza abajo
                        'issued_at' => now(),
                    ]);

                    $ticket->update(['qr_token' => $this->signer->sign($ticket)]);
                    $tickets->push($ticket);
                }
            }

            $locked->update(['status' => 'tickets_issued']);

            return $tickets;
        });
    }

    /**
     * Revoca un ticket (perdida reportada, fraude, cambio de orden). No
     * borra el registro - queda auditado con revoked_at/revoked_reason.
     */
    public function revoke(Ticket $ticket, string $reason): Ticket
    {
        $ticket->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoked_reason' => $reason,
        ]);

        return $ticket->fresh();
    }

    /**
     * Reemision controlada: revoca el ticket original y emite uno nuevo con
     * un qr_token distinto, enlazado via reissue_of_ticket_id. El original
     * nunca vuelve a ser valido.
     */
    public function reissue(Ticket $original, string $reason): Ticket
    {
        return DB::transaction(function () use ($original, $reason) {
            $seatId = $original->seat_id;

            // Le quitamos el seat_id al original ANTES de crear el nuevo:
            // la UNIQUE(event_id, seat_id) es sobre tickets vigentes, y
            // ambas filas (revocada + nueva) no pueden compartir el mismo
            // seat_id a la vez. El asiento fisico no cambia, solo el
            // ticket que lo representa.
            $original->update([
                'status' => 'revoked',
                'revoked_at' => now(),
                'revoked_reason' => $reason,
                'seat_id' => null,
            ]);

            $new = Ticket::create([
                'order_id' => $original->order_id,
                'order_item_id' => $original->order_item_id,
                'event_id' => $original->event_id,
                'zone_id' => $original->zone_id,
                'seat_id' => $seatId,
                'status' => 'issued',
                'qr_token' => bin2hex(random_bytes(16)),
                'issued_at' => now(),
                'reissue_of_ticket_id' => $original->id,
            ]);

            $new->update(['qr_token' => $this->signer->sign($new)]);

            return $new;
        });
    }
}
