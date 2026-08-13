<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'customer_id',
    'slug',
    'description',
    'category',
    'client_date',
    'photo_image',
    'cover_image',
    'video_url',
    'high_resolution_url',
    'sort_order',
    'is_published',
    'is_portal_visible',
])]
class Client extends Model
{
    protected function casts(): array
    {
        return [
            'client_date' => 'date',
            'is_published' => 'boolean',
            'is_portal_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images(): HasMany
    {
        return $this->hasMany(ClientImage::class)->orderBy('sort_order');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopePortalVisible(Builder $query): void
    {
        $query->where('is_portal_visible', true);
    }

    public function photoImageUrl(): string
    {
        return $this->imageUrl($this->photo_image);
    }

    public function coverImageUrl(): string
    {
        return $this->imageUrl($this->cover_image);
    }

    private function imageUrl(string $path): string
    {
        return str_starts_with($path, 'images/')
            ? asset($path)
            : '/storage/'.ltrim($path, '/');
    }
}
