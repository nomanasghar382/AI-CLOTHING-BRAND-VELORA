<?php

namespace Tests\Feature\Api\V1;

use App\Models\GiftCard;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class GiftCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_redeem_active_gift_card(): void
    {
        $this->seed(RoleSeeder::class);
        $customer = User::factory()->create();
        $customer->roles()->attach(Role::query()->where('slug', 'customer')->firstOrFail());
        $gift = GiftCard::query()->create([
            'code' => 'VELORA-GIFT-TEST',
            'initial_balance' => 50,
            'balance' => 50,
            'purchaser_id' => $customer->id,
            'is_active' => true,
        ]);

        Sanctum::actingAs($customer);
        $this->postJson('/api/v1/loyalty/gift-cards/redeem', ['code' => $gift->code, 'amount' => 25])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('gift_cards', ['id' => $gift->id, 'balance' => 25]);
    }

    public function test_invalid_gift_card_code_returns_validation_error(): void
    {
        $this->seed(RoleSeeder::class);
        $customer = User::factory()->create();
        $customer->roles()->attach(Role::query()->where('slug', 'customer')->firstOrFail());
        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/loyalty/gift-cards/redeem', ['code' => 'MISSING-CODE', 'amount' => 10])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['code']);
    }
}
