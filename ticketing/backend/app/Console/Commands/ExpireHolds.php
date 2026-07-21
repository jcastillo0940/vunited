<?php

namespace App\Console\Commands;

use App\Domain\Ticketing\Models\Hold;
use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Services\CapacityService;
use App\Domain\Ticketing\Support\OrderStateMachine;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('tickets:expire-holds')]
#[Description('Libera holds vencidos y expira las ordenes que dependian de ellos')]
class ExpireHolds extends Command
{
    public function handle(CapacityService $capacity): int
    {
        $expiredHolds = Hold::query()
            ->where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        $affectedOrders = [];

        foreach ($expiredHolds as $hold) {
            DB::transaction(function () use ($hold, $capacity, &$affectedOrders) {
                // Re-verificar dentro de la transaccion: otro proceso pudo
                // haber consumido/liberado este hold entre el SELECT de
                // arriba y este punto.
                $fresh = Hold::query()->lockForUpdate()->find($hold->id);
                if (! $fresh || $fresh->status !== 'active' || $fresh->expires_at->isFuture()) {
                    return;
                }

                if ($fresh->seat_id) {
                    $capacity->releaseSeat($fresh->seat()->first());
                } else {
                    $capacity->releaseGeneralCapacity($fresh->zone, $fresh->quantity);
                }

                $fresh->update(['status' => 'expired', 'released_at' => now()]);
                $affectedOrders[$fresh->order_id] = true;
            });
        }

        $expiredOrders = 0;
        foreach (array_keys($affectedOrders) as $orderId) {
            DB::transaction(function () use ($orderId, &$expiredOrders) {
                $order = Order::query()->lockForUpdate()->find($orderId);
                if (! $order || OrderStateMachine::isTerminal($order->status)) {
                    return;
                }
                // Solo expirar la orden si TODOS sus holds ya no estan activos.
                if ($order->holds()->where('status', 'active')->exists()) {
                    return;
                }
                if (! OrderStateMachine::canTransition($order->status, 'expired')) {
                    return;
                }
                $order->update(['status' => 'expired']);
                $expiredOrders++;
            });
        }

        $this->info(sprintf('Holds liberados: %d. Ordenes expiradas: %d.', $expiredHolds->count(), $expiredOrders));

        return self::SUCCESS;
    }
}
