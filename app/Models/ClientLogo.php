<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'image_path',
    'original_name',
    'sort_order',
])]
class ClientLogo extends Model
{
    public function imageUrl(): string
    {
        return '/storage/'.ltrim($this->image_path, '/');
    }
}
