<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierCertification extends Model
{
    protected $fillable = ['supplier_profile_id', 'name', 'issuer', 'reference', 'status', 'issued_at', 'expires_at'];

    protected function casts(): array
    {
        return ['issued_at' => 'date', 'expires_at' => 'date'];
    }
}
