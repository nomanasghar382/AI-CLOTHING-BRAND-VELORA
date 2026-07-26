<?php

namespace App\Http\Controllers\Api\V1\Style;

use App\Http\Controllers\Controller;
use App\Http\Requests\Style\ChatRequest;
use App\Http\Requests\Style\FeedbackRequest;
use App\Http\Requests\Style\RecommendationRequest;
use App\Http\Requests\Style\SavedOutfitRequest;
use App\Http\Requests\Style\UpdateAiProfileRequest;
use App\Http\Requests\Style\UpdateBodyProfileRequest;
use App\Http\Resources\Api\V1\StyleRecommendationResource;
use App\Models\Product;
use App\Services\Style\RecommendationService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class StyleController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly RecommendationService $recommendations) {}

    public function quiz(): JsonResponse
    {
        return $this->success(['questions' => [
            ['key' => 'style_preferences', 'type' => 'multi_select', 'label' => 'What styles feel most like you?'],
            ['key' => 'color_preferences', 'type' => 'multi_select', 'label' => 'Which colours do you enjoy wearing?'],
            ['key' => 'avoidances', 'type' => 'multi_select', 'label' => 'What would you prefer to avoid?'],
            ['key' => 'budget', 'type' => 'number', 'label' => 'What is your usual outfit budget?'],
        ]]);
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success(DB::table('ai_profiles')->where('user_id', $request->user()->id)->first());
    }

    public function updateProfile(UpdateAiProfileRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        foreach (['style_preferences', 'color_preferences', 'avoidances', 'quiz_answers'] as $key) {
            if (array_key_exists($key, $attributes)) {
                $attributes[$key] = json_encode($attributes[$key]);
            }
        }
        $attributes += ['updated_at' => now()];
        DB::table('ai_profiles')->updateOrInsert(['user_id' => $request->user()->id], $attributes + ['created_at' => now()]);

        return $this->profile($request);
    }

    public function updateBody(UpdateBodyProfileRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        foreach (['measurements', 'size_preferences'] as $key) {
            if (array_key_exists($key, $attributes)) {
                $attributes[$key] = json_encode($attributes[$key]);
            }
        }
        DB::table('body_profiles')->updateOrInsert(['user_id' => $request->user()->id], $attributes + ['created_at' => now(), 'updated_at' => now()]);

        return $this->bodyProfile($request);
    }

    public function bodyProfile(Request $request): JsonResponse
    {
        $profile = DB::table('body_profiles')->where('user_id', $request->user()->id)->first();
        if ($profile) {
            foreach (['measurements', 'size_preferences'] as $key) {
                if ($profile->{$key}) {
                    $profile->{$key} = json_decode($profile->{$key}, true);
                }
            }
        }

        return $this->success($profile);
    }

    public function recommend(RecommendationRequest $request): JsonResponse
    {
        $context = $request->validated();
        if (isset($context['conversation_id'])) {
            $this->ownedConversation($request->user()->id, $context['conversation_id']);
        }
        $recommendation = $this->recommendations->create($request->user(), $context, 'general', $context['conversation_id'] ?? null);

        return $this->success(new StyleRecommendationResource($recommendation), 'Recommendation generated.', 201);
    }

    public function occasion(RecommendationRequest $request): JsonResponse
    {
        $context = $request->validated();
        if (isset($context['conversation_id'])) {
            $this->ownedConversation($request->user()->id, $context['conversation_id']);
        }
        $recommendation = $this->recommendations->create($request->user(), $context, 'occasion', $context['conversation_id'] ?? null);

        return $this->success(new StyleRecommendationResource($recommendation), 'Occasion outfit generated.', 201);
    }

    public function chat(ChatRequest $request): JsonResponse
    {
        $input = $request->validated();
        $conversationId = $input['conversation_id'] ?? DB::table('style_conversations')->insertGetId([
            'user_id' => $request->user()->id, 'title' => mb_strimwidth($input['message'], 0, 80, '…'), 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->ownedConversation($request->user()->id, $conversationId);
        DB::table('style_messages')->insert(['style_conversation_id' => $conversationId, 'role' => 'user', 'content' => $input['message'], 'created_at' => now(), 'updated_at' => now()]);
        $recommendation = $this->recommendations->create($request->user(), $input + ['notes' => $input['message']], 'chat', $conversationId);
        $reply = $recommendation['response'] ? json_decode($recommendation['response'], true)['reply'] : '';
        DB::table('style_messages')->insert(['style_conversation_id' => $conversationId, 'role' => 'assistant', 'content' => $reply, 'metadata' => json_encode(['recommendation_id' => $recommendation['id']]), 'created_at' => now(), 'updated_at' => now()]);
        DB::table('style_conversations')->where('id', $conversationId)->update(['last_message_at' => now(), 'updated_at' => now()]);

        return $this->success(['conversation_id' => $conversationId, 'recommendation' => new StyleRecommendationResource($recommendation)], 'Message processed.', 201);
    }

    public function conversations(Request $request): JsonResponse
    {
        return $this->success(DB::table('style_conversations')->where('user_id', $request->user()->id)->latest('last_message_at')->get());
    }

    public function messages(Request $request, int $conversation): JsonResponse
    {
        $this->ownedConversation($request->user()->id, $conversation);

        return $this->success(DB::table('style_messages')->where('style_conversation_id', $conversation)->oldest()->get());
    }

    public function trends(): JsonResponse
    {
        return $this->success(DB::table('fashion_trends')->where('is_active', true)->latest()->get());
    }

    public function savedOutfits(Request $request): JsonResponse
    {
        return $this->success(DB::table('saved_outfits')->where('user_id', $request->user()->id)->latest()->get());
    }

    public function saveOutfit(SavedOutfitRequest $request): JsonResponse
    {
        $input = $request->validated();
        $products = Product::query()->whereIn('id', $input['product_ids'])->where('status', 'published')->where('stock_quantity', '>', 0)->get();
        abort_if($products->count() !== count(array_unique($input['product_ids'])), 422, 'Outfits can only contain active, available products.');
        if (isset($input['recommendation_id'])) {
            abort_unless(DB::table('style_recommendations')->where('id', $input['recommendation_id'])->where('user_id', $request->user()->id)->exists(), 404);
        }
        $id = DB::table('saved_outfits')->insertGetId([
            'user_id' => $request->user()->id, 'style_recommendation_id' => $input['recommendation_id'] ?? null, 'name' => $input['name'],
            'occasion' => $input['occasion'] ?? null, 'notes' => $input['notes'] ?? null,
            'items' => $products->map(fn ($product) => ['product_id' => $product->id, 'name' => $product->name])->toJson(),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return $this->success(DB::table('saved_outfits')->find($id), 'Outfit saved.', 201);
    }

    public function feedback(FeedbackRequest $request): JsonResponse
    {
        $input = $request->validated();
        if (isset($input['recommendation_id'])) {
            abort_unless(DB::table('style_recommendations')->where('id', $input['recommendation_id'])->where('user_id', $request->user()->id)->exists(), 404);
        }
        $id = DB::table('style_feedback')->insertGetId(['user_id' => $request->user()->id, 'style_recommendation_id' => $input['recommendation_id'] ?? null, 'product_id' => $input['product_id'] ?? null, 'type' => $input['type'], 'rating' => $input['rating'] ?? null, 'comment' => $input['comment'] ?? null, 'metadata' => isset($input['metadata']) ? json_encode($input['metadata']) : null, 'created_at' => now(), 'updated_at' => now()]);

        return $this->success(DB::table('style_feedback')->find($id), 'Feedback recorded.', 201);
    }

    private function ownedConversation(int $userId, int $conversationId): void
    {
        abort_unless(DB::table('style_conversations')->where('id', $conversationId)->where('user_id', $userId)->exists(), 404);
    }
}
