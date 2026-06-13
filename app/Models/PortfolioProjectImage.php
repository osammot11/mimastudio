<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'image_path',
    'alt_text',
    'sort_order',
])]
class PortfolioProjectImage extends Model
{
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(PortfolioProject::class, 'portfolio_project_id');
    }

    public function imageUrl(): string
    {
        return str_starts_with($this->image_path, 'images/')
            ? asset($this->image_path)
            : '/storage/'.ltrim($this->image_path, '/');
    }
}
