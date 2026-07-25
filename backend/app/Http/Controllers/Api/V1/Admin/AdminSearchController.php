<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CreatorProfile;
use App\Models\Order;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Models\SupplierProfile;
use App\Models\User;
use App\Traits\RespondsWithApi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminSearchController extends Controller
{
    use RespondsWithApi;

    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q'));
        abort_if($query === '', 422, 'Search query is required.');

        $limit = min(max($request->integer('limit', 5), 1), 20);
        $like = '%'.$query.'%';

        $results = [
            'products' => $this->mapResults(
                Product::query()->where(fn (Builder $q) => $q->where('name', 'like', $like)->orWhere('sku', 'like', $like))->limit($limit)->get(['id', 'name', 'sku', 'status']),
                'product',
                fn (Product $product) => "/admin/products",
                fn (Product $product) => "{$product->name} · {$product->sku}",
                fn (Product $product) => $product->status
            ),
            'orders' => $this->mapResults(
                Order::query()->with('user:id,name,email')->where('number', 'like', $like)->limit($limit)->get(['id', 'number', 'status', 'payment_status', 'grand_total', 'user_id']),
                'order',
                fn (Order $order) => '/admin/orders',
                fn (Order $order) => "{$order->number} · {$order->user?->name}",
                fn (Order $order) => "{$order->status} / {$order->payment_status}"
            ),
            'customers' => $this->mapResults(
                User::query()->where(fn (Builder $q) => $q->where('name', 'like', $like)->orWhere('email', 'like', $like))->limit($limit)->get(['id', 'name', 'email']),
                'customer',
                fn (User $user) => '/admin/customers',
                fn (User $user) => $user->name,
                fn (User $user) => $user->email
            ),
            'creators' => $this->mapResults(
                CreatorProfile::query()->where(fn (Builder $q) => $q->where('handle', 'like', $like)->orWhere('display_name', 'like', $like))->limit($limit)->get(['id', 'handle', 'display_name']),
                'creator',
                fn (CreatorProfile $profile) => '/admin/creators',
                fn (CreatorProfile $profile) => $profile->display_name,
                fn (CreatorProfile $profile) => '@'.$profile->handle
            ),
            'suppliers' => $this->mapResults(
                SupplierProfile::query()->where(fn (Builder $q) => $q->where('company_name', 'like', $like)->orWhere('slug', 'like', $like))->limit($limit)->get(['id', 'company_name', 'slug', 'status']),
                'supplier',
                fn (SupplierProfile $profile) => '/admin/suppliers',
                fn (SupplierProfile $profile) => $profile->company_name,
                fn (SupplierProfile $profile) => $profile->status
            ),
            'community' => $this->mapResults(
                CommunityPost::query()->with('user:id,name')->where('body', 'like', $like)->limit($limit)->get(['id', 'body', 'status', 'user_id']),
                'community',
                fn (CommunityPost $post) => '/community',
                fn (CommunityPost $post) => str($post->body)->limit(72)->value(),
                fn (CommunityPost $post) => $post->user?->name
            ),
            'support' => $this->mapResults(
                SupportTicket::query()->where(fn (Builder $q) => $q->where('number', 'like', $like)->orWhere('subject', 'like', $like))->limit($limit)->get(['id', 'number', 'subject', 'status']),
                'support',
                fn (SupportTicket $ticket) => '/admin/support',
                fn (SupportTicket $ticket) => "{$ticket->number} · {$ticket->subject}",
                fn (SupportTicket $ticket) => $ticket->status
            ),
        ];

        $flat = collect($results)->flatten(1)->take(30)->values();

        return $this->success([
            'query' => $query,
            'groups' => $results,
            'results' => $flat,
            'total' => $flat->count(),
        ]);
    }

    private function mapResults($items, string $type, callable $href, callable $title, callable $subtitle): array
    {
        return $items->map(fn ($item) => [
            'id' => $item->id,
            'type' => $type,
            'title' => $title($item),
            'subtitle' => $subtitle($item),
            'href' => $href($item),
        ])->all();
    }
}
