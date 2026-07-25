<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationMetric extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['tags' => 'array', 'recorded_at' => 'datetime'];
    }
}
