<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRule extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['rate' => 'decimal:2', 'is_active' => 'boolean'];
    }
}
