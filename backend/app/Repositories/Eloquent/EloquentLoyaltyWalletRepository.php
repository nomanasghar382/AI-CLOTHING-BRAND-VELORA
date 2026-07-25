<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\LoyaltyWalletRepositoryInterface;
use App\Models\LoyaltyWallet;
use App\Models\User;

final class EloquentLoyaltyWalletRepository implements LoyaltyWalletRepositoryInterface
{
    public function lockedFor(User $user): LoyaltyWallet
    {
        LoyaltyWallet::query()->firstOrCreate(['user_id' => $user->id]);

        return LoyaltyWallet::query()->where('user_id', $user->id)->lockForUpdate()->firstOrFail();
    }
}
