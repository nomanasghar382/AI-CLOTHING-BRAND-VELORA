<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingEstimate extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'payload' => 'array'];
    }
}
