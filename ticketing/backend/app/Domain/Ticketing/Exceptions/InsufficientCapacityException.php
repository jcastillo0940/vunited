<?php

namespace App\Domain\Ticketing\Exceptions;

use RuntimeException;

class InsufficientCapacityException extends RuntimeException
{
    public static function forZone(int $zoneId): self
    {
        return new self("No hay cupo disponible en la zona {$zoneId}.");
    }

    public static function forSeat(int $seatId): self
    {
        return new self("El asiento {$seatId} ya no esta disponible.");
    }
}
