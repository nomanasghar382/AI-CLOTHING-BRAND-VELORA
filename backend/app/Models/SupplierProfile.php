<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierProfile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'company_name', 'slug', 'status', 'tax_id', 'contact_email', 'contact_phone', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(SupplierAddress::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SupplierDocument::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(SupplierCertification::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(SupplierInventory::class);
    }
}
