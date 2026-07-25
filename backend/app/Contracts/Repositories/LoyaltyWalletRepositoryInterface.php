<?php

namespace App\Contracts\Repositories;

use App\Models\LoyaltyWallet;
use App\Models\User;

interface LoyaltyWalletRepositoryInterface
{
    public function lockedFor(User $user): LoyaltyWallet;
}
