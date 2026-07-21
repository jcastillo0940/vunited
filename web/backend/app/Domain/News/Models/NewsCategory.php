<?php

namespace App\Domain\News\Models;

use Database\Factories\NewsCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'is_active'])]
class NewsCategory extends Model
{
    use HasFactory;

    protected static function newFactory(): NewsCategoryFactory
    {
        return NewsCategoryFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function articles(): HasMany
    {
        return $this->hasMany(NewsArticle::class);
    }
}
