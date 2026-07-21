<?php

namespace App\Domain\Stadium\Models;

use Database\Factories\StadiumFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'subtitle',
    'location',
    'address',
    'capacity',
    'venue_type',
    'hero_image_path',
    'map_embed_url',
    'zones',
    'matchday',
    'rules',
    'is_active',
    'metadata',
])]
class Stadium extends Model
{
    /** @use HasFactory<StadiumFactory> */
    use HasFactory;

    protected static function newFactory(): StadiumFactory
    {
        return StadiumFactory::new();
    }

    protected function casts(): array
    {
        return [
            'zones'     => 'array',
            'matchday'  => 'array',
            'rules'     => 'array',
            'is_active' => 'boolean',
            'metadata'  => 'array',
        ];
    }
}
