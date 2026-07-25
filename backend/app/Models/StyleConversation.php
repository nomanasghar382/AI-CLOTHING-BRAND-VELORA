<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class StyleConversation extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['context' => 'array', 'last_message_at' => 'datetime'];
    }
}
