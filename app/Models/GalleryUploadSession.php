<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'uuid',
    'client_id',
    'user_id',
    'manifest_hash',
    'expected_files',
    'uploaded_files',
    'expected_bytes',
    'uploaded_bytes',
    'status',
    'notification_requested',
    'notification_sent_at',
    'completed_at',
    'expires_at',
])]
class GalleryUploadSession extends Model
{
    protected function casts(): array
    {
        return [
            'expected_files' => 'integer',
            'uploaded_files' => 'integer',
            'expected_bytes' => 'integer',
            'uploaded_bytes' => 'integer',
            'notification_requested' => 'boolean',
            'notification_sent_at' => 'datetime',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GalleryUploadItem::class)->orderBy('position');
    }
}
