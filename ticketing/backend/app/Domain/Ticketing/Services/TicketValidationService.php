<?php

namespace App\Domain\Ticketing\Services;

use App\Domain\Ticketing\Models\Door;
use App\Domain\Ticketing\Models\Ticket;
use App\Domain\Ticketing\Models\ValidationEvent;
use App\Domain\Ticketing\Support\TicketQrSigner;
use App\Models\Operator;
use Illuminate\Support\Facades\DB;

/**
 * Validacion en puerta. La operacion central es un UPDATE ... WHERE
 * status='issued' atomico (igual patron que CapacityService): si dos
 * escaneos del mismo ticket llegan al mismo tiempo, solo uno puede ganar la
 * carrera de escritura - el otro ve 0 filas afectadas y se reporta como
 * already_used, nunca como valido. Esto es lo que arregla el bug real
 * documentado en docs/architecture/ticketing-legacy-findings.md.
 */
class TicketValidationService
{
    public function __construct(private readonly TicketQrSigner $signer) {}

    /**
     * @return array{valid: bool, result: string, ticket?: array, message: string}
     */
    public function validate(
        string $token,
        ?int $doorId,
        ?int $operatorId,
        ?int $deviceId,
        string $correlationId,
    ): array {
        $decoded = $this->signer->verify($token);

        if ($decoded === null) {
            return $this->reject(null, 'invalid', $token, $doorId, $operatorId, $deviceId, $correlationId, 'QR invalido o alterado.');
        }

        $ticket = Ticket::query()->where('public_id', $decoded['ticket_public_id'])->first();

        if ($ticket === null || $ticket->event_id !== $decoded['event_id']) {
            return $this->reject(null, 'not_found', $token, $doorId, $operatorId, $deviceId, $correlationId, 'Boleto no encontrado.');
        }

        if ($doorId !== null) {
            $door = Door::find($doorId);
            if ($door && $door->event_id !== $ticket->event_id) {
                return $this->reject($ticket, 'wrong_event', $token, $doorId, $operatorId, $deviceId, $correlationId, 'Este boleto es de otro evento.');
            }
        }

        if ($operatorId !== null && $doorId !== null) {
            $operator = Operator::find($operatorId);
            if (! $operator || ! $this->operatorCanValidateAtDoor($operator, $ticket->event_id, $doorId)) {
                return $this->reject($ticket, 'wrong_door', $token, $doorId, $operatorId, $deviceId, $correlationId, 'Operador sin permiso para esta puerta/evento.');
            }
        }

        if ($ticket->status === 'revoked') {
            return $this->reject($ticket, 'revoked', $token, $doorId, $operatorId, $deviceId, $correlationId, 'Boleto revocado.');
        }

        // Unico UPDATE atomico que decide quien "gana" el escaneo.
        $affected = DB::table('tickets')
            ->where('id', $ticket->id)
            ->where('status', 'issued')
            ->update(['status' => 'used', 'used_at' => now(), 'updated_at' => now()]);

        if ($affected === 0) {
            $current = $ticket->fresh();
            $result = $current->status === 'used' ? 'already_used' : ($current->status === 'revoked' ? 'revoked' : 'invalid');

            return $this->reject($ticket, $result, $token, $doorId, $operatorId, $deviceId, $correlationId,
                $result === 'already_used' ? 'Boleto ya fue utilizado.' : 'Boleto no valido.');
        }

        $this->log($ticket->id, 'valid', $token, $ticket->event_id, $doorId, $operatorId, $deviceId, $correlationId);

        return [
            'valid' => true,
            'result' => 'valid',
            'message' => 'Boleto valido.',
            'ticket' => $this->summary($ticket->fresh()),
        ];
    }

    private function operatorCanValidateAtDoor(Operator $operator, int $eventId, int $doorId): bool
    {
        if ($operator->isAdmin()) {
            return true;
        }

        return $operator->assignments()
            ->where('event_id', $eventId)
            ->where(fn ($q) => $q->whereNull('door_id')->orWhere('door_id', $doorId))
            ->exists();
    }

    private function reject(?Ticket $ticket, string $result, string $token, ?int $doorId, ?int $operatorId, ?int $deviceId, string $correlationId, string $message): array
    {
        $this->log($ticket?->id, $result, $token, $ticket?->event_id, $doorId, $operatorId, $deviceId, $correlationId);

        return [
            'valid' => false,
            'result' => $result,
            'message' => $message,
            'ticket' => $ticket ? $this->summary($ticket) : null,
        ];
    }

    private function log(?int $ticketId, string $result, string $token, ?int $eventId, ?int $doorId, ?int $operatorId, ?int $deviceId, string $correlationId): void
    {
        ValidationEvent::create([
            'ticket_id' => $ticketId,
            'scanned_token' => substr($token, 0, 128),
            'result' => $result,
            'event_id' => $eventId,
            'door_id' => $doorId,
            'operator_id' => $operatorId,
            'device_id' => $deviceId,
            'correlation_id' => $correlationId,
            'occurred_at' => now(),
        ]);
    }

    private function summary(Ticket $ticket): array
    {
        return [
            'id' => $ticket->public_id,
            'status' => $ticket->status,
            'zone' => $ticket->zone?->name,
            'seat_label' => $ticket->seat?->label,
            'issued_at' => $ticket->issued_at?->toIso8601String(),
            'used_at' => $ticket->used_at?->toIso8601String(),
        ];
    }
}
