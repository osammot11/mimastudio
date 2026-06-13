<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

#[Fillable([
    'title',
    'slug',
    'description',
    'body',
    'cover_image',
    'category',
    'sort_order',
    'is_published',
])]
class PortfolioProject extends Model
{
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images(): HasMany
    {
        return $this->hasMany(PortfolioProjectImage::class)->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function coverImageUrl(): string
    {
        return str_starts_with($this->cover_image, 'images/')
            ? asset($this->cover_image)
            : '/storage/'.ltrim($this->cover_image, '/');
    }
}
