<?php

namespace App\Domain\Pages\Models;

use App\Domain\Media\Models\Media;
use Database\Factories\PageSectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'page_id',
    'section_key',
    'type',
    'title',
    'body',
    'payload',
    'sort_order',
    'is_active',
    'image_path',
])]
class PageSection extends Model
{
    use HasFactory;

    protected static function newFactory(): PageSectionFactory
    {
        return PageSectionFactory::new();
    }

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
