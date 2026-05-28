<?php

namespace App\Domain\Pages\Models;

use App\Domain\Media\Models\Media;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'title',
    'slug',
    'excerpt',
    'status',
    'published_at',
    'seo_title',
    'seo_description',
    'is_home',
])]
class Page extends Model
{
    use HasFactory;

    protected static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_home' => 'boolean',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
