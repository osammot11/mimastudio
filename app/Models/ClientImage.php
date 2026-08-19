<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'image_path',
    'thumbnail_path',
    'alt_text',
    'original_name',
    'byte_size',
    'width',
    'height',
    'sort_order',
])]
class ClientImage extends Model
{
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'byte_size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function imageUrl(): string
    {
        return str_starts_with($this->image_path, 'images/')
            ? asset($this->image_path)
            : '/storage/'.ltrim($this->image_path, '/');
    }

    public function thumbnailUrl(): string
    {
        if (! $this->thumbnail_path) {
            return $this->imageUrl();
        }

        return str_starts_with($this->thumbnail_path, 'images/')
            ? asset($this->thumbnail_path)
            : '/storage/'.ltrim($this->thumbnail_path, '/');
    }
}
