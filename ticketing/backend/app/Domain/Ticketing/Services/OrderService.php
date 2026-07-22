<?php

namespace App\Domain\Ticketing\Services;

use App\Domain\Payments\Contracts\PaymentsGateway;
use App\Domain\Ticketing\Exceptions\InsufficientCapacityException;
use App\Domain\Ticketing\Exceptions\OrderException;
use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Hold;
use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Models\OrderItem;
use App\Domain\Ticketing\Models\Seat;
use App\Domain\Ticketing\Models\Zone;
use App\Domain\Ticketing\Support\OrderStateMachine;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly CapacityService $capacity,
        private readonly PaymentsGateway $payments,
    ) {}

    /**
     * Crea una orden en draft, reclama capacidad (holds) para cada linea y
     * la transiciona a hold_active. Idempotente vía idempotency_key: un
     * reintento del mismo checkout nunca reclama cupo dos veces.
     *
     * @param  array<int, array{zone_public_id: string, quantity: int, seat_public_ids?: string[]}>  $items
     *
     * @throws InsufficientCapacityException|OrderException
     */
    public function createOrder(
        Event $event,
        array $items,
        int $customerId,
        string $customerEmail,
        ?string $customerName,
        ?string $customerPhone,
        ?string $idempotencyKey,
        int $holdMinutes = 10,
    ): Order {
        if ($idempotencyKey) {
            $existing = Order::query()->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        if (! $event->isOnSale()) {
            throw new OrderException('El evento no esta en venta en este momento.');
        }

        if (empty($items)) {
            throw new OrderException('La orden debe tener al menos una linea.');
        }

        return DB::transaction(function () use ($event, $items, $customerId, $customerEmail, $customerName, $customerPhone, $idempotencyKey, $holdMinutes) {
            $order = Order::create([
                'event_id' => $event->id,
                'customer_id' => $customerId,
                'status' => 'draft',
                'customer_email' => $customerEmail,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'currency' => 'USD',
                'idempotency_key' => $idempotencyKey,
            ]);
            $order->assignOrderNumber();

            $subtotal = '0';
            $requestedQuantity = 0;

            foreach ($items as $item) {
                $zone = Zone::query()
                    ->where('event_id', $event->id)
                    ->where('public_id', $item['zone_public_id'])
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (! $zone) {
                    throw new OrderException("Zona {$item['zone_public_id']} no disponible para este evento.");
                }

                $limit = $zone->purchase_limit_per_buyer ?? $event->purchase_limit_per_buyer;

                if ($zone->isGeneralAdmission()) {
                    $quantity = (int) $item['quantity'];
                    $requestedQuantity += $quantity;

                    if ($limit && $requestedQuantity > $limit) {
                        throw new OrderException("Limite de {$limit} boletos por comprador excedido.");
                    }

                    $this->capacity->claimGeneralCapacity($zone, $quantity);

                    Hold::create([
                        'order_id' => $order->id,
                        'zone_id' => $zone->id,
                        'quantity' => $quantity,
                        'status' => 'active',
                        'expires_at' => now()->addMinutes($holdMinutes),
                    ]);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'zone_id' => $zone->id,
                        'quantity' => $quantity,
                        'unit_price' => $zone->price,
                        'line_total' => bcmul((string) $zone->price, (string) $quantity, 2),
                    ]);

                    $subtotal = bcadd($subtotal, bcmul((string) $zone->price, (string) $quantity, 2), 2);
                } else {
                    $seatPublicIds = $item['seat_public_ids'] ?? [];
                    if (empty($seatPublicIds)) {
                        throw new OrderException('Esta zona requiere seleccionar asientos.');
                    }
                    $requestedQuantity += count($seatPublicIds);
                    if ($limit && $requestedQuantity > $limit) {
                        throw new OrderException("Limite de {$limit} boletos por comprador excedido.");
                    }

                    foreach ($seatPublicIds as $seatPublicId) {
                        $seat = Seat::query()
                            ->where('zone_id', $zone->id)
                            ->where('public_id', $seatPublicId)
                            ->lockForUpdate()
                            ->first();

                        if (! $seat) {
                            throw new OrderException("Asiento {$seatPublicId} no existe en esta zona.");
                        }

                        $this->capacity->claimSeat($seat);

                        Hold::create([
                            'order_id' => $order->id,
                            'zone_id' => $zone->id,
                            'seat_id' => $seat->id,
                            'quantity' => 1,
                            'status' => 'active',
                            'expires_at' => now()->addMinutes($holdMinutes),
                        ]);

                        OrderItem::create([
                            'order_id' => $order->id,
                            'zone_id' => $zone->id,
                            'seat_id' => $seat->id,
                            'quantity' => 1,
                            'unit_price' => $zone->price,
                            'line_total' => $zone->price,
                        ]);

                        $subtotal = bcadd($subtotal, (string) $zone->price, 2);
                    }
                }
            }

            OrderStateMachine::assertTransitionAllowed('draft', 'hold_active');
            $order->update([
                'status' => 'hold_active',
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'hold_expires_at' => now()->addMinutes($holdMinutes),
            ]);

            return $order->fresh(['items', 'holds']);
        });
    }

    /**
     * Solicita el payment intent a Payments y mueve la orden a
     * pending_payment. Si Payments rechaza o no responde, la orden pasa a
     * failed y se liberan los holds (nunca se deja capacidad retenida
     * indefinidamente por un fallo externo).
     */
    public function requestPayment(Order $order, string $method = 'tilopay'): Order
    {
        OrderStateMachine::assertTransitionAllowed($order->status, 'pending_payment');

        if ($method === 'cash') {
            $this->extendHoldsForCash($order);
        }

        $result = $this->payments->createIntent($order, $method);

        if (! $result->success) {
            $this->releaseHolds($order);
            OrderStateMachine::assertTransitionAllowed($order->status, 'failed');
            $order->update(['status' => 'failed']);

            throw new OrderException($result->errorMessage ?? 'No se pudo iniciar el pago.');
        }

        $order->update([
            'status' => 'pending_payment',
            'payment_intent_id' => $result->intentId,
            'payment_method' => $method,
            'metadata' => array_merge($order->metadata ?? [], ['payment_redirect_url' => $result->redirectUrl]),
        ]);

        return $order->fresh();
    }

    /**
     * Efectivo no se paga al instante: el cliente necesita tiempo para
     * acercarse a pagar. Se extiende el hold normal (~10 min) a 24h en vez
     * de dejarlo expirar mientras va a pagar.
     */
    private function extendHoldsForCash(Order $order): void
    {
        $newExpiry = now()->addHours(24);
        $order->holds()->where('status', 'active')->update(['expires_at' => $newExpiry]);
        $order->update(['hold_expires_at' => $newExpiry]);
    }

    /**
     * Llamado por el webhook interno de Payments (o el consumidor de
     * eventos) cuando el pago se confirma. Consume los holds (capacidad
     * pasa de "held" a vendida) y deja la orden en `paid`; la emision de
     * tickets es un paso separado e idempotente (TicketIssuingService).
     */
    public function markPaid(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->status === 'paid' || $order->status === 'tickets_issued') {
                return $order; // idempotente: el webhook puede reintentar
            }

            OrderStateMachine::assertTransitionAllowed($order->status, 'paid');

            foreach ($order->holds()->where('status', 'active')->get() as $hold) {
                if ($hold->seat_id) {
                    $this->capacity->consumeSeat($hold->seat()->first());
                } else {
                    $this->capacity->consumeGeneralCapacity($hold->zone, $hold->quantity);
                }
                $hold->update(['status' => 'consumed']);
            }

            $order->update(['status' => 'paid', 'paid_at' => now()]);

            return $order;
        });
    }

    public function markFailed(Order $order, ?string $reason = null): Order
    {
        OrderStateMachine::assertTransitionAllowed($order->status, 'failed');
        $this->releaseHolds($order);
        $order->update(['status' => 'failed', 'metadata' => array_merge($order->metadata ?? [], ['failure_reason' => $reason])]);

        return $order->fresh();
    }

    public function cancel(Order $order): Order
    {
        OrderStateMachine::assertTransitionAllowed($order->status, 'cancelled');
        $this->releaseHolds($order);
        $order->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return $order->fresh();
    }

    /**
     * Libera todos los holds activos de una orden (capacidad vuelve a
     * disponible). Se usa en cancelacion, fallo de pago y expiracion.
     */
    public function releaseHolds(Order $order): void
    {
        foreach ($order->holds()->where('status', 'active')->get() as $hold) {
            if ($hold->seat_id) {
                $this->capacity->releaseSeat($hold->seat()->first());
            } else {
                $this->capacity->releaseGeneralCapacity($hold->zone, $hold->quantity);
            }
            $hold->update(['status' => 'released', 'released_at' => now()]);
        }
    }
}
