<?php

namespace App\Domain\Payments\Contracts;

use App\Domain\Payments\Data\PaymentIntentResult;
use App\Domain\Payments\Data\RefundResult;
use App\Domain\Ticketing\Models\Order;

/**
 * Contrato interno con el servicio de Payments (aun no implementado como
 * servicio independiente - ver docs/architecture/target-monorepo.md). Todo
 * el dominio Ticketing habla EXCLUSIVAMENTE contra esta interfaz: nunca un
 * SDK de TiloPay, nunca la base de datos de Payments. Eso es lo que exige
 * Fase 7 §5 ("Ticketing no puede: Procesar TiloPay, Leer la base Payments").
 */
interface PaymentsGateway
{
    public function createIntent(Order $order): PaymentIntentResult;

    public function refund(Order $order, ?string $reason = null): RefundResult;
}
