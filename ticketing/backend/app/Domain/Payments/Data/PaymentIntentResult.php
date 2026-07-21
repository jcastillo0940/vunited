<?php

namespace App\Domain\Payments\Data;

readonly class PaymentIntentResult
{
    public function __construct(
        public bool $success,
        public ?string $intentId = null,
        public ?string $redirectUrl = null,
        public ?string $errorMessage = null,
    ) {}
}
