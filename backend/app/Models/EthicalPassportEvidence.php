<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EthicalPassportEvidence extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    public function passport(): BelongsTo
    {
        return $this->belongsTo(EthicalPassport::class, 'ethical_passport_id');
    }
}
