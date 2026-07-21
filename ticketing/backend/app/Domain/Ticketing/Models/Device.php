<?php

namespace App\Domain\Ticketing\Models;

use App\Domain\Ticketing\Support\HasPublicUlid;
use App\Models\Operator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'operator_id', 'is_active', 'last_seen_at', 'revoked_at'])]
class Device extends Model
{
    use HasPublicUlid;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}
