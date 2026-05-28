<?php

namespace App\Domain\Payments\Models;

use App\Domain\Payments\Enums\PaymentEventProcessingStatus;
use App\Domain\Payments\Enums\PaymentEventVerificationStatus;
use Database\Factories\PaymentEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'payment_id',
    'provider',
    'provider_event_id',
    'event_type',
    'resource_type',
    'provider_order_id',
    'provider_capture_id',
    'verification_status',
    'processing_status',
    'payload',
    'headers',
    'received_at',
    'verified_at',
    'processed_at',
    'error_message',
])]
class PaymentEvent extends Model
{
    /** @use HasFactory<PaymentEventFactory> */
    use HasFactory;

    protected static function newFactory(): PaymentEventFactory
    {
        return PaymentEventFactory::new();
    }

    protected function casts(): array
    {
        return [
            'verification_status' => PaymentEventVerificationStatus::class,
            'processing_status'   => PaymentEventProcessingStatus::class,
            'payload'             => 'array',
            'headers'             => 'array',
            'received_at'         => 'datetime',
            'verified_at'         => 'datetime',
            'processed_at'        => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
