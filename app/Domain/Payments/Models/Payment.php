<?php

namespace App\Domain\Payments\Models;

use App\Domain\Payments\Enums\PaymentStatus;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'payable_type',
    'payable_id',
    'provider',
    'provider_order_id',
    'provider_capture_id',
    'status',
    'currency',
    'amount',
    'description',
    'customer_email',
    'customer_name',
    'metadata',
    'provider_payload',
    'approved_at',
    'captured_at',
    'failed_at',
    'cancelled_at',
    'refunded_at',
])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected static function newFactory(): PaymentFactory
    {
        return PaymentFactory::new();
    }

    protected function casts(): array
    {
        return [
            'status'           => PaymentStatus::class,
            'amount'           => 'decimal:2',
            'metadata'         => 'array',
            'provider_payload' => 'array',
            'approved_at'      => 'datetime',
            'captured_at'      => 'datetime',
            'failed_at'        => 'datetime',
            'cancelled_at'     => 'datetime',
            'refunded_at'      => 'datetime',
        ];
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function paymentEvents(): HasMany
    {
        return $this->hasMany(PaymentEvent::class);
    }
}
