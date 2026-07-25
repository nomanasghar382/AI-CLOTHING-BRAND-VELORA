<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierAddress extends Model
{
    protected $fillable = ['supplier_profile_id', 'type', 'line1', 'line2', 'city', 'region', 'postal_code', 'country', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }
}
