<?php

namespace Tests\Feature\Api\V1\Style;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class StyleEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_body_and_catalog_fallback_recommendations_are_authenticated_and_only_available_products_are_returned(): void
    {
        config(['services.openai.key' => '', 'services.weather.key' => '']);
        $user = User::factory()->create();
        $category = Category::query()->create(['name' => 'Hoodie', 'slug' => 'mens-sport-hoodie', 'status' => 'active']);
        $available = Product::factory()->create(['status' => 'published', 'stock_quantity' => 3, 'price' => 75, 'gender' => 'men', 'category_id' => $category->id, 'catalog_line' => 'apparel']);
        Product::factory()->create(['status' => 'draft', 'stock_quantity' => 9, 'price' => 20]);
        Product::factory()->create(['status' => 'published', 'stock_quantity' => 0, 'price' => 20]);

        $this->postJson('/api/v1/style/recommendations', [])->assertUnauthorized();
        Sanctum::actingAs($user);
        $this->getJson('/api/v1/style/quiz')->assertOk()->assertJsonPath('data.questions.0.key', 'style_preferences');
        $this->putJson('/api/v1/style/profile', ['style_preferences' => ['minimal'], 'quiz_answers' => ['budget' => 100]])->assertOk();
        $this->putJson('/api/v1/style/body', ['height_unit' => 'cm', 'height' => 175, 'size_preferences' => ['tops' => 'M']])->assertOk();
        $response = $this->postJson('/api/v1/style/recommendations', ['budget' => 100])->assertCreated()
            ->assertJsonPath('data.provider', 'catalog_fallback')
            ->assertJsonCount(1, 'data.items');
        $this->assertSame($available->id, $response->json('data.items.0.product_id'));
        $this->assertDatabaseHas('style_recommendation_items', ['product_id' => $available->id]);
    }

    public function test_chat_saved_outfit_feedback_and_conversation_ownership_are_enforced(): void
    {
        config(['services.openai.key' => '']);
        $user = User::factory()->create();
        $other = User::factory()->create();
        $category = Category::query()->create(['name' => 'Training Shoes', 'slug' => 'mens-sport-training-shoes', 'status' => 'active']);
        $product = Product::factory()->create(['status' => 'published', 'stock_quantity' => 2, 'price' => 50, 'gender' => 'men', 'category_id' => $category->id, 'catalog_line' => 'footwear']);
        Sanctum::actingAs($user);

        $chat = $this->postJson('/api/v1/style/chat', ['message' => 'I need a dinner outfit', 'budget' => 80])->assertCreated();
        $conversationId = $chat->json('data.conversation_id');
        $recommendationId = $chat->json('data.recommendation.id');
        $this->getJson("/api/v1/style/conversations/{$conversationId}/messages")->assertOk()->assertJsonCount(2, 'data');
        $this->postJson('/api/v1/style/saved-outfits', ['name' => 'Dinner', 'recommendation_id' => $recommendationId, 'product_ids' => [$product->id]])->assertCreated();
        $this->postJson('/api/v1/style/feedback', ['recommendation_id' => $recommendationId, 'product_id' => $product->id, 'type' => 'liked', 'rating' => 5])->assertCreated();

        Sanctum::actingAs($other);
        $this->getJson("/api/v1/style/conversations/{$conversationId}/messages")->assertNotFound();
        $this->postJson('/api/v1/style/feedback', ['recommendation_id' => $recommendationId, 'type' => 'liked'])->assertNotFound();
        $this->assertDatabaseCount('saved_outfits', 1);
        $this->assertDatabaseCount('style_feedback', 1);
    }

    public function test_seeded_trends_are_readable_by_authenticated_users(): void
    {
        DB::table('fashion_trends')->insert(['slug' => 'test-trend', 'title' => 'Test trend', 'description' => 'Non-sensitive editorial trend.', 'confidence' => .8, 'source' => 'editorial', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/v1/style/trends')->assertOk()->assertJsonPath('data.0.slug', 'test-trend');
    }
}
