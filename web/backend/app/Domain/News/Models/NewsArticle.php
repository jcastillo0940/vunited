<?php

namespace App\Domain\News\Models;

use App\Domain\Media\Models\Media;
use Database\Factories\NewsArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'news_category_id',
    'title',
    'slug',
    'summary',
    'body',
    'featured_image_path',
    'status',
    'published_at',
    'is_featured',
    'seo_title',
    'seo_description',
])]
class NewsArticle extends Model
{
    use HasFactory;

    protected static function newFactory(): NewsArticleFactory
    {
        return NewsArticleFactory::new();
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
