<?php

namespace App\Http\Controllers\Api\V1\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\CreatorProfileRequest;
use App\Http\Requests\Creator\WardrobeItemRequest;
use App\Http\Resources\Api\V1\CreatorProfileResource;
use App\Models\CreatorCollection;
use App\Models\CreatorProfile;
use App\Models\Lookboard;
use App\Models\Product;
use App\Models\WardrobeItem;
use App\Services\Media\CloudinaryUploadService;
use App\Services\Search\VisualSearchService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

final class CreatorController extends Controller
{
    use RespondsWithApi;

    public function profile(Request $request): JsonResponse
    {
        return $this->success(new CreatorProfileResource(CreatorProfile::query()->where('user_id', $request->user()->id)->withCount('followers')->firstOrFail()));
    }

    public function saveProfile(CreatorProfileRequest $request): JsonResponse
    {
        $profile = CreatorProfile::query()->updateOrCreate(['user_id' => $request->user()->id], $request->validated());

        return $this->success(new CreatorProfileResource($profile), 'Creator profile saved.');
    }

    public function show(string $handle): JsonResponse
    {
        return $this->success(new CreatorProfileResource(CreatorProfile::query()->where('handle', $handle)->firstOrFail()));
    }

    public function follow(Request $request, CreatorProfile $creator): JsonResponse
    {
        abort_if($creator->user_id === $request->user()->id, 422, 'You cannot follow yourself.');
        DB::table('creator_follows')->updateOrInsert(['creator_profile_id' => $creator->id, 'user_id' => $request->user()->id], ['created_at' => now(), 'updated_at' => now()]);

        return $this->success(['following' => true]);
    }

    public function unfollow(Request $request, CreatorProfile $creator): JsonResponse
    {
        DB::table('creator_follows')->where(['creator_profile_id' => $creator->id, 'user_id' => $request->user()->id])->delete();

        return $this->success(['following' => false]);
    }

    public function collections(CreatorProfile $creator): JsonResponse
    {
        return $this->success($creator->collections()->where('is_public', true)->with('products.images')->latest()->paginate(20));
    }

    public function storeCollection(Request $request): JsonResponse
    {
        $profile = $this->creator($request);
        $data = $request->validate(['title' => 'required|string|max:150', 'description' => 'nullable|string|max:2000', 'cover_url' => 'nullable|url', 'is_public' => 'boolean', 'product_ids' => 'array', 'product_ids.*' => 'integer|exists:products,id']);
        $productIds = $data['product_ids'] ?? [];
        unset($data['product_ids']);
        $collection = $profile->collections()->create($data + ['slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(6))]);
        $collection->products()->sync($this->activeProducts($productIds));

        return $this->success($collection->load('products.images'), 'Collection created.', 201);
    }

    public function updateCollection(Request $request, CreatorCollection $collection): JsonResponse
    {
        $this->authorize('update', $collection);
        $data = $request->validate(['title' => 'sometimes|required|string|max:150', 'description' => 'nullable|string|max:2000', 'cover_url' => 'nullable|url', 'is_public' => 'boolean', 'product_ids' => 'array', 'product_ids.*' => 'integer|exists:products,id']);
        $collection->update(collect($data)->except('product_ids')->all());
        if (array_key_exists('product_ids', $data)) {
            $collection->products()->sync($this->activeProducts($data['product_ids']));
        }

return $this->success($collection->load('products.images'));
    }

    public function destroyCollection(CreatorCollection $collection): JsonResponse
    {
        $this->authorize('delete', $collection);
        $collection->delete();

        return $this->success(null, 'Collection deleted.');
    }

    public function earnings(Request $request): JsonResponse
    {
        $profile = $this->creator($request);

        return $this->success(['balance' => $profile->commissions()->where('status', 'available')->sum('amount'), 'commissions' => $profile->commissions()->latest()->paginate(20)]);
    }

    public function withdrawal(Request $request): JsonResponse
    {
        $profile = $this->creator($request);
        $data = $request->validate(['amount' => 'required|numeric|min:1', 'destination' => 'required|string|max:64']);
        abort_if((float) $data['amount'] > (float) $profile->commissions()->where('status', 'available')->sum('amount'), 422, 'Insufficient available earnings.');

        return $this->success($profile->withdrawals()->create($data), 'Withdrawal requested.', 201);
    }

    public function lookboards(Request $request): JsonResponse
    {
        return $this->success(Lookboard::query()->where('user_id', $request->user()->id)->with('items.product.images')->latest()->paginate(20));
    }

    public function storeLookboard(Request $request): JsonResponse
    {
        $data = $request->validate(['title' => 'required|string|max:150', 'description' => 'nullable|string|max:2000', 'cover_url' => 'nullable|url', 'is_public' => 'boolean', 'items' => 'array', 'items.*.product_id' => 'nullable|integer|exists:products,id', 'items.*.image_url' => 'nullable|url', 'items.*.position' => 'nullable|array']);
        $board = $request->user()->lookboards()->create(collect($data)->except('items')->all());
        foreach ($data['items'] ?? [] as $i => $item) {
            if (isset($item['product_id'])) {
                abort_unless(Product::query()->whereKey($item['product_id'])->where('status', 'published')->where('stock_quantity', '>', 0)->exists(), 422);
            } $board->items()->create($item + ['sort_order' => $i]);
        }

return $this->success($board->load('items.product.images'), 'Lookboard created.', 201);
    }

    public function updateLookboard(Request $request, Lookboard $lookboard): JsonResponse
    {
        $this->authorize('update', $lookboard);
        $data = $request->validate(['title' => 'sometimes|required|string|max:150', 'description' => 'nullable|string|max:2000', 'cover_url' => 'nullable|url', 'is_public' => 'boolean']);
        $lookboard->update($data);

        return $this->success($lookboard->load('items.product.images'));
    }

    public function destroyLookboard(Lookboard $lookboard): JsonResponse
    {
        $this->authorize('delete', $lookboard);
        $lookboard->delete();

        return $this->success(null, 'Lookboard deleted.');
    }

    public function wardrobe(Request $request): JsonResponse
    {
        return $this->success(WardrobeItem::query()->where('user_id', $request->user()->id)->with('product.images')->latest()->paginate(20));
    }

    public function storeWardrobe(WardrobeItemRequest $request): JsonResponse
    {
        return $this->success($request->user()->wardrobeItems()->create($request->validated()), 'Wardrobe item created.', 201);
    }

    public function updateWardrobe(WardrobeItemRequest $request, WardrobeItem $item): JsonResponse
    {
        abort_unless($item->user_id === $request->user()->id, 404);
        $item->update($request->validated());

        return $this->success($item);
    }

    public function destroyWardrobe(Request $request, WardrobeItem $item): JsonResponse
    {
        abort_unless($item->user_id === $request->user()->id, 404);
        $item->delete();

        return $this->success(null, 'Wardrobe item deleted.');
    }

    public function wardrobeRecommendations(Request $request): JsonResponse
    {
        $ids = WardrobeItem::query()->where('user_id', $request->user()->id)->pluck('product_id')->filter();

        return $this->success(Product::query()->where('status', 'published')->where('stock_quantity', '>', 0)->whereNotIn('id', $ids)->with('images')->limit(12)->get());
    }

    public function upload(Request $request, CloudinaryUploadService $uploads): JsonResponse
    {
        $request->validate(['image' => 'required|image|max:10240', 'folder' => 'nullable|string|max:100']);
        try {
            return $this->success($uploads->upload($request->file('image'), $request->input('folder', 'creator-commerce')), 'Image uploaded.', 201);
        } catch (RuntimeException $e) {
            return $this->failure($e->getMessage(), [], 503);
        }
    }

    public function visualSearch(Request $request, CloudinaryUploadService $uploads, VisualSearchService $searches): JsonResponse
    {
        $request->validate(['image' => 'required|image|max:10240']);
        try {
            $upload = $uploads->upload($request->file('image'), 'visual-search');

            return $this->success($searches->search($request->user()->id, $request->file('image'), $upload['url']), 'Visual search complete.', 201);
        } catch (RuntimeException $e) {
            return $this->failure($e->getMessage(), [], 503);
        }
    }

    public function visualHistory(Request $request): JsonResponse
    {
        return $this->success($request->user()->visualSearches()->latest()->paginate(20));
    }

    private function creator(Request $request): CreatorProfile
    {
        return CreatorProfile::query()->firstOrCreate(['user_id' => $request->user()->id], ['handle' => 'creator-'.$request->user()->id, 'display_name' => $request->user()->name]);
    }

    private function activeProducts(array $ids): array
    {
        $active = Product::query()->whereIn('id', $ids)->where('status', 'published')->where('stock_quantity', '>', 0)->pluck('id')->all();
        abort_if(count($active) !== count(array_unique($ids)),422,'Collections can only contain active products.');

        return $active;
    }
}
