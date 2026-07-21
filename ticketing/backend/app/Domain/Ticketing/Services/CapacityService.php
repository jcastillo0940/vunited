<?php

namespace App\Domain\Ticketing\Services;

use App\Domain\Ticketing\Exceptions\InsufficientCapacityException;
use App\Domain\Ticketing\Models\Seat;
use App\Domain\Ticketing\Models\Zone;
use Illuminate\Support\Facades\DB;

/**
 * Unico punto de escritura sobre capacidad. Todas las operaciones son un
 * solo UPDATE ... WHERE atomico (no SELECT-luego-UPDATE): la fila se bloquea
 * durante el propio UPDATE y la clausula WHERE decide, dentro del motor de
 * base de datos, si la operacion procede o no. Esto es lo que hace
 * imposible la sobreventa bajo concurrencia, no una disciplina de la
 * aplicacion.
 */
class CapacityService
{
    /**
     * Reserva `quantity` cupos de una zona de admision general.
     *
     * @throws InsufficientCapacityException si no hay cupo suficiente.
     */
    public function claimGeneralCapacity(Zone $zone, int $quantity): void
    {
        $affected = DB::table('zones')
            ->where('id', $zone->id)
            ->where('capacity_available', '>=', $quantity)
            ->update([
                'capacity_available' => DB::raw("capacity_available - {$quantity}"),
                'capacity_held' => DB::raw("capacity_held + {$quantity}"),
                'updated_at' => now(),
            ]);

        if ($affected === 0) {
            throw InsufficientCapacityException::forZone($zone->id);
        }

        $zone->refresh();
    }

    /**
     * Libera `quantity` cupos previamente reservados (hold cancelado o
     * expirado) de vuelta a disponible.
     */
    public function releaseGeneralCapacity(Zone $zone, int $quantity): void
    {
        DB::table('zones')
            ->where('id', $zone->id)
            ->update([
                'capacity_available' => DB::raw("capacity_available + {$quantity}"),
                'capacity_held' => DB::raw("GREATEST(capacity_held - {$quantity}, 0)"),
                'updated_at' => now(),
            ]);
    }

    /**
     * Convierte un hold en venta confirmada: el cupo ya estaba descontado de
     * `capacity_available` al crear el hold, aqui solo se libera de "held"
     * (no vuelve a "available").
     */
    public function consumeGeneralCapacity(Zone $zone, int $quantity): void
    {
        DB::table('zones')
            ->where('id', $zone->id)
            ->update([
                'capacity_held' => DB::raw("GREATEST(capacity_held - {$quantity}, 0)"),
                'updated_at' => now(),
            ]);
    }

    /**
     * Reserva un asiento especifico. Atomico: solo una llamada concurrente
     * puede ganar la carrera por el mismo asiento.
     *
     * @throws InsufficientCapacityException si el asiento no esta disponible.
     */
    public function claimSeat(Seat $seat): void
    {
        $affected = DB::table('seats')
            ->where('id', $seat->id)
            ->where('status', 'available')
            ->update(['status' => 'held', 'updated_at' => now()]);

        if ($affected === 0) {
            throw InsufficientCapacityException::forSeat($seat->id);
        }

        $seat->refresh();
    }

    public function releaseSeat(Seat $seat): void
    {
        DB::table('seats')
            ->where('id', $seat->id)
            ->where('status', 'held')
            ->update(['status' => 'available', 'updated_at' => now()]);
    }

    public function consumeSeat(Seat $seat): void
    {
        DB::table('seats')
            ->where('id', $seat->id)
            ->where('status', 'held')
            ->update(['status' => 'sold', 'updated_at' => now()]);
    }

    public function blockSeat(Seat $seat): void
    {
        DB::table('seats')->where('id', $seat->id)->update(['status' => 'blocked', 'updated_at' => now()]);
    }
}
