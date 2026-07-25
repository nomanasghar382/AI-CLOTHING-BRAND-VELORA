<?php

namespace Database\Seeders;

use App\Models\EthicalPassport;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoyaltyPassportSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->each(function (User $user): void {
            $user->loyaltyWallet()->firstOrCreate([]);
            $user->notificationPreference()->firstOrCreate([]);
        });
        Product::query()->where('status', 'published')->limit(10)->each(function (Product $product): void {
            EthicalPassport::query()->firstOrCreate(['product_id' => $product->id], [
                'status' => 'verified', 'verification_reference' => "VELORA-{$product->id}",
                'verified_by' => 'Velora Verification Registry', 'verified_at' => now()->subDay(),
                'claims' => ['materials' => 'Supplier documentation reviewed'],
            ]);
        });
    }
}
