<?php

namespace App\Services\Loyalty;

use App\Contracts\Repositories\LoyaltyWalletRepositoryInterface;
use App\Models\LoyaltyWallet;
use App\Models\Referral;
use App\Models\ReferralCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class LoyaltyService
{
    public function __construct(private readonly LoyaltyWalletRepositoryInterface $wallets) {}

    public function wallet(User $user): LoyaltyWallet
    {
        return LoyaltyWallet::query()->firstOrCreate(['user_id' => $user->id])->fresh();
    }

    public function transact(User $user, string $type, int $points, float $credit, string $idempotencyKey, ?string $referenceType = null, ?int $referenceId = null): LoyaltyWallet
    {
        return DB::transaction(function () use ($user, $type, $points, $credit, $idempotencyKey, $referenceType, $referenceId): LoyaltyWallet {
            $wallet = $this->wallets->lockedFor($user);
            $existing = $wallet->transactions()->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $wallet;
            }
            if ($wallet->points_balance + $points < 0 || (float) $wallet->store_credit_balance + $credit < 0) {
                throw ValidationException::withMessages(['wallet' => ['Insufficient points or store credit.']]);
            }
            $wallet->increment('points_balance', $points);
            $wallet->increment('store_credit_balance', $credit);
            $wallet->transactions()->create(compact('type') + ['points_delta' => $points, 'credit_delta' => $credit, 'idempotency_key' => $idempotencyKey, 'reference_type' => $referenceType, 'reference_id' => $referenceId]);

            return $wallet->fresh();
        }, 3);
    }

    public function referralCode(User $user): ReferralCode
    {
        return ReferralCode::query()->firstOrCreate(['user_id' => $user->id], ['code' => strtoupper('VEL'.str_pad((string) $user->id, 8, '0', STR_PAD_LEFT))]);
    }

    public function applyReferral(User $referee, string $code, ?string $ip): Referral
    {
        return DB::transaction(function () use ($referee, $code, $ip): Referral {
            $referralCode = ReferralCode::query()->where('code', strtoupper($code))->where('is_active', true)->lockForUpdate()->first();
            if (! $referralCode || $referralCode->user_id === $referee->id) {
                throw ValidationException::withMessages(['code' => ['Referral code is invalid.']]);
            }
            $hash = $ip ? hash('sha256', $ip.config('app.key')) : null;
            if ($hash && Referral::query()->where('referrer_id', $referralCode->user_id)->where('signup_ip_hash', $hash)->exists()) {
                throw ValidationException::withMessages(['code' => ['Referral cannot be applied from this signup context.']]);
            }

            return Referral::query()->create(['referrer_id' => $referralCode->user_id, 'referee_id' => $referee->id, 'signup_ip_hash' => $hash]);
        });
    }

    public function rewardReferral(Referral $referral): void
    {
        DB::transaction(function () use ($referral): void {
            $referral = Referral::query()->lockForUpdate()->findOrFail($referral->id);
            if ($referral->rewarded_at) {
                return;
            }
            $this->transact($referral->referrer, 'referral_reward', 500, 0, "referral:referrer:{$referral->id}", Referral::class, $referral->id);
            $this->transact($referral->referee, 'referral_welcome', 250, 0, "referral:referee:{$referral->id}", Referral::class, $referral->id);
            $referral->update(['status' => 'rewarded', 'qualified_at' => now(), 'rewarded_at' => now()]);
        });
    }
}
