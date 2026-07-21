<?php

namespace App\Domain\Sponsors\Models;

use Database\Factories\SponsorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'slug',
    'tier',
    'logo_path',
    'website_url',
    'description',
    'sort_order',
    'is_active',
    'metadata',
])]
class Sponsor extends Model
{
    /** @use HasFactory<SponsorFactory> */
    use HasFactory;

    protected static function newFactory(): SponsorFactory
    {
        return SponsorFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
            'metadata'   => 'array',
        ];
    }
}
