<?php

namespace App\Services\Style;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class RecommendationService
{
    public function __construct(private readonly AiFashionService $ai, private readonly WeatherService $weather) {}

    public function create(User $user, array $context, string $kind = 'general', ?int $conversationId = null): array
    {
        $products = Product::query()->with(['category', 'images', 'matchedProduct.images'])
            ->where('status', 'published')->where('stock_quantity', '>', 0)
            ->where(fn ($query) => $query->where('gender', 'men')->orWhereNull('gender'))
            ->when(isset($context['budget']), fn ($query) => $query->whereRaw('COALESCE(sale_price, price) <= ?', [(float) $context['budget']]))
            ->orderByDesc('is_featured')->orderByDesc('is_trending')->limit(40)->get();
        $catalog = $products->map(fn (Product $product) => [
            'id' => $product->id, 'name' => $product->name, 'category' => $product->category?->name,
            'price' => (float) ($product->sale_price ?? $product->price),
        ])->all();
        $profile = DB::table('ai_profiles')->where('user_id', $user->id)->first();
        $context['profile'] = $profile ? json_decode($profile->style_preferences ?? '[]', true) : [];
        $weather = $this->weather->current($context['city'] ?? null);
        $cacheKey = hash('sha256', json_encode([$user->id, $kind, $context, $catalog, $weather], JSON_THROW_ON_ERROR));
        $cached = DB::table('ai_response_caches')->where('cache_key', $cacheKey)->where('expires_at', '>', now())->value('payload');
        $result = $cached ? json_decode($cached, true) : $this->ai->recommend($catalog, $context + ['weather' => $weather]);
        if (! $cached) {
            DB::table('ai_response_caches')->updateOrInsert(['cache_key' => $cacheKey], ['payload' => json_encode($result), 'expires_at' => now()->addMinutes(20), 'updated_at' => now(), 'created_at' => now()]);
        }
        $allowed = $products->keyBy('id');
        $items = array_values(array_filter($result['items'], fn ($item) => $allowed->has($item['product_id'])));

        return DB::transaction(function () use ($user, $conversationId, $kind, $context, $weather, $result, $items, $allowed) {
            $id = DB::table('style_recommendations')->insertGetId([
                'user_id' => $user->id, 'style_conversation_id' => $conversationId, 'kind' => $kind, 'status' => 'completed',
                'request_context' => json_encode($context), 'weather' => json_encode($weather), 'response' => json_encode(['reply' => $result['reply']]),
                'provider' => $result['provider'], 'generated_at' => now(), 'created_at' => now(), 'updated_at' => now(),
            ]);
            $inserted = [];
            foreach ($items as $rank => $item) {
                $product = $allowed[$item['product_id']];
                DB::table('style_recommendation_items')->insert([
                    'style_recommendation_id' => $id, 'product_id' => $product->id, 'rank' => $rank + 1,
                    'reason' => $item['reason'], 'score' => $item['score'], 'product_snapshot' => json_encode(['name' => $product->name, 'price' => $product->sale_price ?? $product->price]),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $inserted[$product->id] = true;
                if ($product->matchedProduct && ! isset($inserted[$product->matchedProduct->id])) {
                    $shoe = $product->matchedProduct;
                    DB::table('style_recommendation_items')->insert([
                        'style_recommendation_id' => $id, 'product_id' => $shoe->id, 'rank' => $rank + 1,
                        'reason' => 'Matching kicks for your sport fit', 'score' => $item['score'], 'product_snapshot' => json_encode(['name' => $shoe->name, 'price' => $shoe->sale_price ?? $shoe->price]),
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                    $inserted[$shoe->id] = true;
                }
            }

            return $this->find($user, $id);
        });
    }

    public function find(User $user, int $id): array
    {
        $recommendation = DB::table('style_recommendations')->where('user_id', $user->id)->find($id);
        abort_unless($recommendation, 404);
        $recommendation->items = DB::table('style_recommendation_items')->where('style_recommendation_id', $id)
            ->join('products', 'products.id', '=', 'style_recommendation_items.product_id')
            ->leftJoin('product_images', function ($join) {
                $join->on('product_images.product_id', '=', 'products.id')->where('product_images.is_primary', true);
            })
            ->where('products.status', 'published')->where('products.stock_quantity', '>', 0)
            ->orderBy('rank')->get([
                'style_recommendation_items.*',
                'products.name as product_name',
                'products.slug as product_slug',
                'products.price as product_price',
                'products.sale_price',
                'products.catalog_line',
                'product_images.url as image_url',
            ]);

        return (array) $recommendation;
    }
}
