<?php

namespace App\Http\Controllers\Api\V1\Loyalty;

use App\Http\Controllers\Controller;
use App\Http\Requests\Loyalty\PassportRequest;
use App\Http\Requests\Loyalty\ProductAlertRequest;
use App\Http\Resources\Api\V1\EthicalPassportResource;
use App\Http\Resources\Api\V1\LoyaltyWalletResource;
use App\Models\EthicalPassport;
use App\Models\GiftCard;
use App\Models\NotificationPreference;
use App\Models\Product;
use App\Models\ProductAlert;
use App\Models\User;
use App\Services\Loyalty\LoyaltyService;
use App\Services\Loyalty\ProductAlertService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class LoyaltyController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly LoyaltyService $loyalty, private readonly ProductAlertService $alerts) {}

    public function wallet(Request $request): JsonResponse
    {
        return $this->success(new LoyaltyWalletResource($this->loyalty->wallet($request->user())->load(['transactions' => fn ($q) => $q->latest()->limit(50)])));
    }

    public function referralCode(Request $request): JsonResponse
    {
        return $this->success(['code' => $this->loyalty->referralCode($request->user())->code]);
    }

    public function applyReferral(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:32']]);

        return $this->success($this->loyalty->applyReferral($request->user(), $data['code'], $request->ip()), 'Referral applied.', 201);
    }

    public function preferences(Request $request): JsonResponse
    {
        $preference = NotificationPreference::query()->firstOrCreate(['user_id' => $request->user()->id]);
        if ($request->isMethod('put')) {
            $preference->update($request->validate(['email_marketing' => ['sometimes', 'boolean'], 'price_alerts' => ['sometimes', 'boolean'], 'restock_alerts' => ['sometimes', 'boolean'], 'loyalty_updates' => ['sometimes', 'boolean'], 'referral_updates' => ['sometimes', 'boolean']]));
        }

        return $this->success($preference->fresh());
    }

    public function alerts(Request $request): JsonResponse
    {
        return $this->success(ProductAlert::query()->where('user_id', $request->user()->id)->with('product')->latest()->get());
    }

    public function storeAlert(ProductAlertRequest $request): JsonResponse
    {
        $data = $request->validated() + ['user_id' => $request->user()->id];
        $alert = ProductAlert::query()->updateOrCreate(['user_id' => $data['user_id'], 'product_id' => $data['product_id'], 'product_variant_id' => $data['product_variant_id'] ?? null, 'type' => $data['type']], $data + ['is_active' => true, 'notified_at' => null]);

        return $this->success($alert, 'Alert saved.', 201);
    }

    public function destroyAlert(Request $request, ProductAlert $alert): JsonResponse
    {
        abort_unless($alert->user_id === $request->user()->id, 404);
        $alert->delete();

        return $this->success(null, 'Alert deleted.');
    }

    public function passport(Product $product): JsonResponse
    {
        $passport = EthicalPassport::query()->with('evidence')->where('product_id', $product->id)->where('status', 'verified')->where('verified_at', '<=', now())->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->firstOrFail();

        return $this->success(new EthicalPassportResource($passport));
    }

    public function storePassport(PassportRequest $request): JsonResponse
    {
        $passport = $this->savePassport($request->validated());

        return $this->success(new EthicalPassportResource($passport), 'Ethical passport saved.', 201);
    }

    public function updatePassport(PassportRequest $request, EthicalPassport $passport): JsonResponse
    {
        $passport = $this->savePassport($request->validated(), $passport);

        return $this->success(new EthicalPassportResource($passport), 'Ethical passport updated.');
    }

    public function giftCard(Request $request): JsonResponse
    {
        $data = $request->validate(['amount' => ['required', 'numeric', 'min:1', 'max:10000'], 'recipient_id' => ['nullable', 'exists:users,id'], 'expires_at' => ['nullable', 'date', 'after:now']]);
        $gift = GiftCard::query()->create(['code' => strtoupper(Str::random(20)), 'initial_balance' => $data['amount'], 'balance' => $data['amount'], 'purchaser_id' => $request->user()->id, 'recipient_id' => $data['recipient_id'] ?? null, 'expires_at' => $data['expires_at'] ?? null]);

        return $this->success($gift, 'Gift card created.', 201);
    }

    public function redeemGiftCard(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => ['required', 'string'], 'amount' => ['required', 'numeric', 'min:0.01']]);
        $wallet = DB::transaction(function () use ($request, $data) {
            $gift = GiftCard::query()->where('code', strtoupper($data['code']))->lockForUpdate()->firstOrFail();
            if (! $gift->is_active || ($gift->expires_at && $gift->expires_at->isPast()) || $gift->balance < $data['amount']) {
                throw ValidationException::withMessages(['code' => ['Gift card is unavailable or has insufficient balance.']]);
            }
            $gift->decrement('balance', $data['amount']);
            if ((float) $gift->fresh()->balance === 0.0) {
                $gift->update(['redeemed_at' => now()]);
            }

            return $this->loyalty->transact($request->user(), 'gift_card_redemption', 0, (float) $data['amount'], "gift-card:{$gift->id}:{$request->user()->id}", GiftCard::class, $gift->id);
        });

        return $this->success(new LoyaltyWalletResource($wallet), 'Gift card redeemed.');
    }

    public function administerWallet(Request $request, int $userId): JsonResponse
    {
        $data = $request->validate(['points_delta' => ['required', 'integer', 'between:-1000000,1000000'], 'credit_delta' => ['required', 'numeric', 'between:-10000,10000'], 'idempotency_key' => ['required', 'string', 'max:100']]);
        $user = User::query()->findOrFail($userId);

        return $this->success(new LoyaltyWalletResource($this->loyalty->transact($user, 'admin_adjustment', $data['points_delta'], (float) $data['credit_delta'], $data['idempotency_key'])), 'Wallet adjusted.');
    }

    public function dispatchAlerts(Product $product): JsonResponse
    {
        return $this->success(['notified' => $this->alerts->dispatchEligible($product)], 'Eligible alerts processed.');
    }

    private function savePassport(array $data, ?EthicalPassport $passport = null): EthicalPassport
    {
        return DB::transaction(function () use ($data, $passport): EthicalPassport {
            $evidence = $data['evidence'] ?? [];
            unset($data['evidence']);
            $passport ??= new EthicalPassport;
            $passport->fill($data)->save();
            $passport->evidence()->delete();
            $passport->evidence()->createMany($evidence);

            return $passport->fresh()->load('evidence');
        });
    }
}
