<?php

namespace App\Domain\Payments\Contracts;

use App\Domain\Payments\Data\PaymentProviderResult;
use App\Domain\Payments\Models\Payment;

interface PaymentProvider
{
    public function createOrder(Payment $payment): PaymentProviderResult;

    public function captureOrder(Payment $payment, array $payload = []): PaymentProviderResult;

    public function refund(Payment $payment, array $payload = []): PaymentProviderResult;
}
