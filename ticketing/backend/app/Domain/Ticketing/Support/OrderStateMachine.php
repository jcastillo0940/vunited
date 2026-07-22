<?php

namespace App\Domain\Ticketing\Support;

use App\Domain\Ticketing\Exceptions\OrderException;

/**
 * Unica fuente de verdad de que transiciones de estado de orden son
 * validas. Ningun servicio debe hacer `$order->status = 'x'` directo -
 * todos pasan por OrderStateMachine::assertTransitionAllowed() primero.
 */
class OrderStateMachine
{
    /** @var array<string, string[]> */
    private const TRANSITIONS = [
        'draft' => ['hold_active', 'failed', 'cancelled'],
        'hold_active' => ['pending_payment', 'expired', 'cancelled', 'failed'],
        'pending_payment' => ['payment_processing', 'paid', 'expired', 'failed', 'cancelled'],
        'payment_processing' => ['paid', 'failed', 'cancelled'],
        'paid' => ['tickets_issued', 'refund_pending'],
        'tickets_issued' => ['refund_pending'],
        'expired' => [],
        'cancelled' => [],
        'refund_pending' => ['refunded', 'failed'],
        'refunded' => [],
        'failed' => [],
    ];

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    public static function assertTransitionAllowed(string $from, string $to): void
    {
        if (! self::canTransition($from, $to)) {
            // OrderException (no DomainException): los controllers atrapan
            // OrderException para responder 409/422; una excepcion sin
            // atrapar aqui se convertia en 500 en vez de un error de negocio.
            throw new OrderException("Transicion de orden invalida: {$from} -> {$to}.");
        }
    }

    public static function isTerminal(string $status): bool
    {
        return in_array($status, ['expired', 'cancelled', 'refunded', 'failed'], true);
    }
}
