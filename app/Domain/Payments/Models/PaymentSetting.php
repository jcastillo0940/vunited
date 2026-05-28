<?php

namespace App\Domain\Payments\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'provider',
    'mode',
    'client_id',
    'client_secret',
    'webhook_id',
    'currency',
    'is_enabled',
    'metadata',
])]
class PaymentSetting extends Model
{
    protected function casts(): array
    {
        return [
            'client_secret' => 'encrypted',
            'is_enabled' => 'boolean',
            'metadata' => 'array',
        ];
    }
}
