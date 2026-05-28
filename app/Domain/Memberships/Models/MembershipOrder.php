<?php

namespace App\Domain\Memberships\Models;

use App\Domain\Memberships\Enums\MembershipOrderStatus;
use App\Domain\Payments\Models\Payment;
use Database\Factories\MembershipOrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[Fillable([
    'order_number',
    'status',
    'full_name',
    'identification_number',
    'birth_date',
    'age',
    'address',
    'phone',
    'email',
    'membership_plan',
    'membership_price',
    'currency',
    'starts_at',
    'expires_at',
    'paid_at',
    'cancelled_at',
    'metadata',
])]
class MembershipOrder extends Model
{
    /** @use HasFactory<MembershipOrderFactory> */
    use HasFactory;

    protected static function newFactory(): MembershipOrderFactory
    {
        return MembershipOrderFactory::new();
    }

    protected function casts(): array
    {
        return [
            'status'           => MembershipOrderStatus::class,
            'membership_price' => 'decimal:2',
            'birth_date'       => 'date',
            'starts_at'        => 'datetime',
            'expires_at'       => 'datetime',
            'paid_at'          => 'datetime',
            'cancelled_at'     => 'datetime',
            'metadata'         => 'array',
        ];
    }

    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable');
    }
}
