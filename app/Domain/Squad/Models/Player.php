<?php

namespace App\Domain\Squad\Models;

use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'slug',
    'number',
    'position',
    'position_key',
    'category',
    'birth_date',
    'nationality',
    'height',
    'weight',
    'dominant_foot',
    'photo_path',
    'gallery',
    'stats',
    'attributes',
    'biography',
    'is_active',
    'sort_order',
])]
class Player extends Model
{
    /** @use HasFactory<PlayerFactory> */
    use HasFactory;

    protected static function newFactory(): PlayerFactory
    {
        return PlayerFactory::new();
    }

    protected function casts(): array
    {
        return [
            'birth_date'  => 'date',
            'gallery'     => 'array',
            'stats'       => 'array',
            'attributes'  => 'array',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
        ];
    }
}
