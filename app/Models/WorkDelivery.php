<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'client_name',
    'work_description',
    'work_date',
    'identifier_code',
    'email',
    'gallery_url',
    'sent_at',
    'last_send_error',
])]
class WorkDelivery extends Model
{
    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'sent_at' => 'datetime',
        ];
    }
}
