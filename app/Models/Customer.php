<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'email',
    'logo_path',
])]
class Customer extends Model
{
    public function works(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path
            ? '/storage/'.ltrim($this->logo_path, '/')
            : null;
    }
}
