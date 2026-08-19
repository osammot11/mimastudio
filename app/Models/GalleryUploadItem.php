<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'gallery_upload_session_id',
    'fingerprint',
    'original_name',
    'byte_size',
    'position',
    'status',
    'staged_path',
    'staged_thumbnail_path',
    'width',
    'height',
])]
class GalleryUploadItem extends Model
{
    protected function casts(): array
    {
        return [
            'byte_size' => 'integer',
            'position' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function uploadSession(): BelongsTo
    {
        return $this->belongsTo(GalleryUploadSession::class, 'gallery_upload_session_id');
    }
}
