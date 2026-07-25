<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['credit_delta' => 'decimal:2', 'metadata' => 'array'];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(LoyaltyWallet::class, 'loyalty_wallet_id');
    }
}
