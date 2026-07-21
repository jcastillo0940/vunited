<?php

namespace App\Domain\Payments\Data;

readonly class RefundResult
{
    public function __construct(
        public bool $success,
        public ?string $refundId = null,
        public ?string $errorMessage = null,
    ) {}
}
