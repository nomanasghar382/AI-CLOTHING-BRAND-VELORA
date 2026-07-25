<?php

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class CreatorCommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_profile_collections_follows_earnings_and_withdrawal_are_scoped(): void
    {
        $creator = User::factory()->create(['name' => 'Creator']);
        $follower = User::factory()->create();
        $product = Product::factory()->create(['status' => 'published', 'stock_quantity' => 2]);
        Sanctum::actingAs($creator);
        $this->putJson('/api/v1/creator/profile', ['handle' => 'creator-one', 'display_name' => 'Creator One', 'is_accepting_commissions' => true])->assertOk();
        $this->postJson('/api/v1/creator/collections', ['title' => 'Summer', 'product_ids' => [$product->id]])->assertCreated()->assertJsonPath('data.products.0.id', $product->id);
        $this->getJson('/api/v1/creator/earnings')->assertOk()->assertJsonPath('data.balance', 0);
        $this->postJson('/api/v1/creator/withdrawals', ['amount' => 1, 'destination' => 'bank'])->assertUnprocessable();
        Sanctum::actingAs($follower);
        $this->postJson('/api/v1/creators/1/follow')->assertOk()->assertJsonPath('data.following', true);
        $this->getJson('/api/v1/creators/creator-one')->assertOk()->assertJsonPath('data.handle', 'creator-one');
        $this->getJson('/api/v1/creators/1/collections')->assertOk()->assertJsonCount(1, 'data.data');
    }

    public function test_wardrobe_only_recommends_active_products_and_cloudinary_is_graceful_when_unconfigured(): void
    {
        config(['services.cloudinary.cloud_name' => null]);
        $user = User::factory()->create();
        $owned = Product::factory()->create(['status' => 'published', 'stock_quantity' => 1]);
        $available = Product::factory()->create(['status' => 'published', 'stock_quantity' => 1]);
        Product::factory()->create(['status' => 'draft', 'stock_quantity' => 1]);
        Sanctum::actingAs($user);
        $this->postJson('/api/v1/wardrobe', ['product_id' => $owned->id, 'name' => 'Owned item'])->assertCreated();
        $this->getJson('/api/v1/wardrobe/recommendations')->assertOk()->assertJsonPath('data.0.id', $available->id);
        $image = UploadedFile::fake()->createWithContent('look.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScL4WQAAAABJRU5ErkJggg=='));
        $this->postJson('/api/v1/media/images', ['image' => $image])->assertStatus(503)->assertJsonPath('success', false);
    }

    public function test_community_posts_have_pagination_interactions_reports_notifications_and_admin_moderation(): void
    {
        $author = User::factory()->create();
        $viewer = User::factory()->create();
        Sanctum::actingAs($author);
        $post = $this->postJson('/api/v1/community/posts', ['body' => 'My favourite outfit'])->assertCreated();
        $postId = $post->json('data.id');
        Sanctum::actingAs($viewer);
        $this->getJson('/api/v1/community/posts')->assertOk()->assertJsonPath('data.data.0.id', $postId);
        $this->postJson("/api/v1/community/posts/{$postId}/like")->assertOk();
        $this->postJson("/api/v1/community/posts/{$postId}/comments", ['body' => 'Love it'])->assertCreated();
        $this->postJson("/api/v1/community/posts/{$postId}/bookmark")->assertOk();
        $this->postJson("/api/v1/community/posts/{$postId}/reports", ['reason' => 'test report'])->assertCreated();
        Sanctum::actingAs($author);
        $this->getJson('/api/v1/community/activity')->assertOk()->assertJsonCount(2, 'data.data');
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::query()->create(['name' => 'Admin', 'slug' => 'admin']));
        Sanctum::actingAs($admin);
        $this->postJson("/api/v1/community/posts/{$postId}/moderate", ['action' => 'hide'])->assertOk()->assertJsonPath('data.status', 'hidden');
        $this->assertDatabaseHas('community_posts', ['id' => $postId, 'status' => 'hidden']);
    }
}
