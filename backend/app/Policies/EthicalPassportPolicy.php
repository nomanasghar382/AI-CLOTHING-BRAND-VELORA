<?php

namespace App\Policies;

use App\Models\EthicalPassport;
use App\Models\User;

final class EthicalPassportPolicy
{
    public function manage(User $user, EthicalPassport $passport): bool
    {
        return $user->hasRole('super-admin', 'admin') && $user->hasPermission('passports.manage');
    }
}
