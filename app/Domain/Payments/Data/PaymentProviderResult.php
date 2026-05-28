<?php

namespace App\Domain\Payments\Data;

readonly class PaymentProviderResult
{
    public function __construct(
        public bool $success,
        public ?string $status = null,
        public ?string $providerOrderId = null,
        public ?string $providerCaptureId = null,
        public ?string $redirectUrl = null,
        public array $rawPayload = [],
        public ?string $message = null,
    ) {}

    public static function success(
        ?string $providerOrderId = null,
        ?string $providerCaptureId = null,
        ?string $redirectUrl = null,
        array $rawPayload = [],
        ?string $status = null,
    ): self {
        return new self(
            success: true,
            status: $status,
            providerOrderId: $providerOrderId,
            providerCaptureId: $providerCaptureId,
            redirectUrl: $redirectUrl,
            rawPayload: $rawPayload,
        );
    }

    public static function failure(string $message, array $rawPayload = []): self
    {
        return new self(
            success: false,
            message: $message,
            rawPayload: $rawPayload,
        );
    }
}
