<?php

namespace App\Domain\Memberships\Models;

use Database\Factories\MembershipPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'name',
    'headline',
    'description',
    'price',
    'currency',
    'duration_months',
    'benefits',
    'kit_items',
    'partner_discounts',
    'is_active',
    'sort_order',
    'metadata',
])]
class MembershipPlan extends Model
{
    /** @use HasFactory<MembershipPlanFactory> */
    use HasFactory;

    protected static function newFactory(): MembershipPlanFactory
    {
        return MembershipPlanFactory::new();
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_months' => 'integer',
            'benefits' => 'array',
            'kit_items' => 'array',
            'partner_discounts' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function membershipOrders(): HasMany
    {
        return $this->hasMany(MembershipOrder::class, 'membership_plan', 'code');
    }

    public function deactivateSiblings(): void
    {
        if (! $this->is_active) {
            return;
        }

        static::query()
            ->where('code', $this->code)
            ->whereKeyNot($this->getKey())
            ->update(['is_active' => false]);
    }

    public static function activeForCode(string $code): ?self
    {
        return static::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->first();
    }
}
