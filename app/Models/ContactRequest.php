<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'full_name',
    'email',
    'phone',
    'project_type',
    'message',
    'wedding_date',
    'wedding_time',
    'ceremony_type',
    'reception_location',
    'guest_count',
    'request_type',
    'additional_services',
    'premium_services',
    'wedding_story',
    'referral_source',
    'privacy_accepted_at',
    'viewed_at',
    'ip_address',
    'user_agent',
])]
class ContactRequest extends Model
{
    protected function casts(): array
    {
        return [
            'wedding_date' => 'date',
            'additional_services' => 'array',
            'premium_services' => 'array',
            'privacy_accepted_at' => 'datetime',
            'viewed_at' => 'datetime',
        ];
    }
}
