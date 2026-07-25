<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingWebhookEvent extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'verified' => 'boolean',
            'processed_at' => 'datetime',
        ];
    }
}
