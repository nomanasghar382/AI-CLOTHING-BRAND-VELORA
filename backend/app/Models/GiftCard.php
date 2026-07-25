<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['initial_balance' => 'decimal:2', 'balance' => 'decimal:2', 'expires_at' => 'datetime', 'redeemed_at' => 'datetime', 'is_active' => 'boolean'];
    }
}
