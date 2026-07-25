<?php

namespace App\Http\Controllers\Api\V1\Community;

use App\Http\Controllers\Controller;
use App\Http\Requests\Community\PostRequest;
use App\Http\Resources\Api\V1\CommunityPostResource;
use App\Models\CommunityComment;
use App\Models\CommunityPost;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class CommunityController extends Controller
{
    use RespondsWithApi;

    public function index(): JsonResponse
    {
        return $this->success(CommunityPost::query()->where('status', 'published')->with('user')->latest()->paginate(20));
    }

    public function store(PostRequest $request): JsonResponse
    {
        return $this->success(new CommunityPostResource($request->user()->communityPosts()->create($request->validated())), 'Post created.', 201);
    }

    public function show(CommunityPost $post): JsonResponse
    {
        abort_unless($post->status === 'published' || auth()->id() === $post->user_id, 404);

        return $this->success(new CommunityPostResource($post->load('user')));
    }

    public function update(PostRequest $request, CommunityPost $post): JsonResponse
    {
        $this->authorize('update', $post);
        $post->update($request->validated());

        return $this->success(new CommunityPostResource($post));
    }

    public function destroy(CommunityPost $post): JsonResponse
    {
        $this->authorize('delete', $post);
        $post->delete();

        return $this->success(null, 'Post deleted.');
    }

    public function comments(CommunityPost $post): JsonResponse
    {
        return $this->success($post->comments()->where('status', 'published')->with('user')->latest()->paginate(20));
    }

    public function comment(Request $request, CommunityPost $post): JsonResponse
    {
        $data = $request->validate(['body' => 'required|string|max:1000', 'parent_id' => 'nullable|integer|exists:community_comments,id']);
        if (isset($data['parent_id'])) {
            abort_unless(CommunityComment::query()->whereKey($data['parent_id'])->where('community_post_id', $post->id)->exists(), 422);
        } $comment = $post->comments()->create($data + ['user_id' => $request->user()->id]);
        $post->increment('comments_count');
        $this->recordActivity($post->user_id, $request->user()->id, 'community.comment', ['post_id' => $post->id]);

        return $this->success($comment->load('user'), 'Comment created.', 201);
    }

    public function like(Request $request, CommunityPost $post): JsonResponse
    {
        $inserted = DB::table('community_likes')->insertOrIgnore(['community_post_id' => $post->id, 'user_id' => $request->user()->id, 'created_at' => now(), 'updated_at' => now()]);
        if ($inserted) {
            $post->increment('likes_count');
            $this->recordActivity($post->user_id, $request->user()->id, 'community.like', ['post_id' => $post->id]);
        }

return $this->success(['liked' => true]);
    }

    public function unlike(Request $request, CommunityPost $post): JsonResponse
    {
        if (DB::table('community_likes')->where(['community_post_id' => $post->id, 'user_id' => $request->user()->id])->delete()) {
            $post->decrement('likes_count');
        }

return $this->success(['liked' => false]);
    }

    public function bookmark(Request $request, CommunityPost $post): JsonResponse
    {
        DB::table('community_bookmarks')->insertOrIgnore(['community_post_id' => $post->id, 'user_id' => $request->user()->id, 'created_at' => now(), 'updated_at' => now()]);

        return $this->success(['bookmarked' => true]);
    }

    public function unbookmark(Request $request, CommunityPost $post): JsonResponse
    {
        DB::table('community_bookmarks')->where(['community_post_id' => $post->id, 'user_id' => $request->user()->id])->delete();

        return $this->success(['bookmarked' => false]);
    }

    public function report(Request $request, CommunityPost $post): JsonResponse
    {
        $data = $request->validate(['reason' => 'required|string|max:500']);
        DB::table('community_reports')->insert($data + ['community_post_id' => $post->id, 'user_id' => $request->user()->id, 'created_at' => now(), 'updated_at' => now()]);

        return $this->success(null, 'Report submitted.', 201);
    }

    public function moderate(Request $request, CommunityPost $post): JsonResponse
    {
        $this->authorize('moderate', CommunityPost::class);
        $data = $request->validate(['action' => 'required|in:hide,publish,remove', 'note' => 'nullable|string|max:500']);
        $post->update(['status' => match ($data['action']) {
            'hide' => 'hidden','remove' => 'removed',default => 'published'
        }]);
        DB::table('community_moderations')->insert($data + ['community_post_id' => $post->id, 'moderator_id' => $request->user()->id, 'created_at' => now(), 'updated_at' => now()]);

        return $this->success(new CommunityPostResource($post));
    }

    public function activity(Request $request): JsonResponse
    {
        return $this->success(DB::table('community_activities')->where('user_id', $request->user()->id)->latest()->paginate(20));
    }

    private function recordActivity(int $userId, int $actorId, string $type, array $data): void
    {
        if ($userId !== $actorId) {
            DB::table('community_activities')->insert(['user_id' => $userId, 'actor_id' => $actorId, 'type' => $type, 'data' => json_encode($data), 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
