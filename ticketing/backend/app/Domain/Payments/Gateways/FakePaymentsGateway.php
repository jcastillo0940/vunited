<?php

namespace App\Domain\Payments\Gateways;

use App\Domain\Payments\Contracts\PaymentsGateway;
use App\Domain\Payments\Data\PaymentIntentResult;
use App\Domain\Payments\Data\RefundResult;
use App\Domain\Ticketing\Models\Order;

/**
 * Doble de prueba: nunca hace red. Usado en tests y, si se desea, en
 * entornos locales via config('services.payments.fake').
 */
class FakePaymentsGateway implements PaymentsGateway
{
    public bool $nextIntentSucceeds = true;

    public bool $nextRefundSucceeds = true;

    public function createIntent(Order $order): PaymentIntentResult
    {
        if (! $this->nextIntentSucceeds) {
            return new PaymentIntentResult(success: false, errorMessage: 'Fake: intent rechazado.');
        }

        return new PaymentIntentResult(
            success: true,
            intentId: 'fake-intent-'.$order->public_id,
            redirectUrl: 'https://pay.example.test/fake/'.$order->public_id,
        );
    }

    public function refund(Order $order, ?string $reason = null): RefundResult
    {
        if (! $this->nextRefundSucceeds) {
            return new RefundResult(success: false, errorMessage: 'Fake: refund rechazado.');
        }

        return new RefundResult(success: true, refundId: 'fake-refund-'.$order->public_id);
    }
}
