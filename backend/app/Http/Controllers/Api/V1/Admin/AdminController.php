<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdminNotification;
use App\Models\ContentEntry;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReportExport;
use App\Models\Setting;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\Admin\ActivityLogger;
use App\Traits\RespondsWithApi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AdminController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly ActivityLogger $activity) {}

    public function dashboard(Request $request): JsonResponse
    {
        $from = now()->subDays(29)->startOfDay();
        $revenue = Order::query()->where('payment_status', 'paid')->sum('grand_total');

        return $this->success([
            'totals' => [
                'revenue' => (float) $revenue,
                'orders' => Order::query()->count(),
                'customers' => User::query()->whereHas('roles', fn (Builder $q) => $q->where('slug', 'customer'))->count(),
                'products' => Product::query()->count(),
                'open_tickets' => SupportTicket::query()->whereNotIn('status', ['resolved', 'closed'])->count(),
            ],
            'period' => [
                'from' => $from->toDateString(),
                'to' => now()->toDateString(),
                'revenue' => (float) Order::query()->where('payment_status', 'paid')->where('created_at', '>=', $from)->sum('grand_total'),
                'orders' => Order::query()->where('created_at', '>=', $from)->count(),
            ],
            'recent_orders' => Order::query()->with('user:id,name,email')->latest()->limit(5)->get(),
            'low_stock_products' => Product::query()->whereColumn('stock_quantity', '<=', 'minimum_stock')->orderBy('stock_quantity')->limit(5)->get(['id', 'name', 'sku', 'stock_quantity', 'minimum_stock']),
            'release' => [
                'version' => config('velora.release.version'),
                'demo_mode' => config('velora.demo_mode'),
            ],
        ]);
    }

    public function workspace(Request $request): JsonResponse
    {
        return $this->success([
            'quick_actions' => [
                ['id' => 'add-product', 'label' => 'Add product', 'href' => '/admin/products', 'shortcut' => 'P'],
                ['id' => 'view-orders', 'label' => 'Review orders', 'href' => '/admin/orders', 'shortcut' => 'O'],
                ['id' => 'export-reports', 'label' => 'Export reports', 'href' => '/admin/reports', 'shortcut' => 'R'],
                ['id' => 'system-health', 'label' => 'System health', 'href' => '/admin/system', 'shortcut' => 'H'],
                ['id' => 'operations', 'label' => 'Operations center', 'href' => '/admin/operations', 'shortcut' => 'M'],
                ['id' => 'clear-cache', 'label' => 'Clear application cache', 'action' => 'clear-cache', 'shortcut' => 'C'],
            ],
            'shortcuts' => [
                ['keys' => ['Meta', 'K'], 'label' => 'Open command palette'],
                ['keys' => ['G', 'D'], 'label' => 'Go to dashboard'],
                ['keys' => ['G', 'O'], 'label' => 'Go to orders'],
                ['keys' => ['G', 'P'], 'label' => 'Go to products'],
            ],
            'pinned_dashboards' => [
                ['id' => 'commerce', 'label' => 'Commerce overview', 'href' => '/admin'],
                ['id' => 'analytics', 'label' => 'Analytics', 'href' => '/admin/analytics'],
                ['id' => 'operations', 'label' => 'Operations', 'href' => '/admin/operations'],
            ],
            'recent_activity' => ActivityLog::query()->with('actor:id,name,email')->latest()->limit(8)->get(),
        ]);
    }

    public function analytics(Request $request): JsonResponse
    {
        $range = $this->range($request);
        $orders = Order::query()->whereBetween('created_at', [$range['from'], $range['to']]);
        $paid = (clone $orders)->where('payment_status', 'paid');

        return $this->success([
            'range' => ['from' => $range['from']->toDateString(), 'to' => $range['to']->toDateString()],
            'summary' => [
                'orders' => $orders->count(),
                'paid_revenue' => (float) $paid->sum('grand_total'),
                'average_order_value' => (float) $paid->avg('grand_total'),
                'new_customers' => User::query()->whereBetween('created_at', [$range['from'], $range['to']])->count(),
            ],
            'daily' => Order::query()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, COALESCE(SUM(CASE WHEN payment_status = ? THEN grand_total ELSE 0 END), 0) as revenue', ['paid'])
                ->whereBetween('created_at', [$range['from'], $range['to']])
                ->groupBy('date')->orderBy('date')->get(),
            'orders_by_status' => Order::query()->selectRaw('status, COUNT(*) as total')->whereBetween('created_at', [$range['from'], $range['to']])->groupBy('status')->get(),
        ]);
    }

    public function exportReport(Request $request, string $report): StreamedResponse
    {
        abort_unless(in_array($report, ['orders', 'customers', 'products'], true), 404);
        $range = $this->range($request);
        [$headers, $rows] = match ($report) {
            'orders' => [['number', 'customer_email', 'status', 'payment_status', 'grand_total', 'created_at'], Order::query()->with('user:id,email')->whereBetween('created_at', [$range['from'], $range['to']])->latest()->get()->map(fn (Order $order) => [$order->number, $order->user?->email, $order->status, $order->payment_status, $order->grand_total, $order->created_at])],
            'customers' => [['id', 'name', 'email', 'active', 'created_at'], User::query()->whereBetween('created_at', [$range['from'], $range['to']])->get()->map(fn (User $user) => [$user->id, $user->name, $user->email, $user->is_active ? 'yes' : 'no', $user->created_at])],
            'products' => [['id', 'name', 'sku', 'status', 'stock_quantity', 'price'], Product::query()->get()->map(fn (Product $product) => [$product->id, $product->name, $product->sku, $product->status, $product->stock_quantity, $product->price])],
        };

        ReportExport::query()->create(['requested_by' => $request->user()->id, 'report_type' => $report, 'filters' => $request->only(['from', 'to']), 'row_count' => $rows->count(), 'generated_at' => now()]);
        $this->activity->log($request, "reports.{$report}.exported", null, ['rows' => $rows->count()]);

        return response()->streamDownload(function () use ($headers, $rows): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, "{$report}-".now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function settings(Request $request): JsonResponse
    {
        return $this->success(Setting::query()->when($request->filled('group'), fn (Builder $q) => $q->where('group', $request->string('group')))->orderBy('group')->orderBy('key')->get());
    }

    public function storeSetting(Request $request): JsonResponse
    {
        $data = $request->validate(['group' => ['required', 'string', 'max:100'], 'key' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9_.-]+$/', 'unique:settings,key'], 'value' => ['nullable'], 'is_public' => ['sometimes', 'boolean']]);
        $setting = Setting::query()->create([...$data, 'updated_by' => $request->user()->id]);
        $this->activity->log($request, 'settings.created', $setting);

        return $this->success($setting, 'Setting created.', 201);
    }

    public function updateSetting(Request $request, Setting $setting): JsonResponse
    {
        $data = $request->validate(['group' => ['sometimes', 'string', 'max:100'], 'value' => ['nullable'], 'is_public' => ['sometimes', 'boolean']]);
        $setting->update([...$data, 'updated_by' => $request->user()->id]);
        $this->activity->log($request, 'settings.updated', $setting);

        return $this->success($setting->fresh(), 'Setting updated.');
    }

    public function contents(Request $request): JsonResponse
    {
        return $this->success($this->paginate(ContentEntry::query()->with('author:id,name')->when($request->filled('type'), fn (Builder $q) => $q->where('type', $request->string('type')))->latest(), $request));
    }

    public function storeContent(Request $request): JsonResponse
    {
        $data = $this->contentData($request);
        $content = ContentEntry::query()->create([...$data, 'author_id' => $request->user()->id, 'published_at' => ($data['status'] ?? null) === 'published' ? now() : null]);
        $this->activity->log($request, 'content.created', $content);

        return $this->success($content, 'Content created.', 201);
    }

    public function showContent(ContentEntry $content): JsonResponse
    {
        return $this->success($content->load('author:id,name'));
    }

    public function updateContent(Request $request, ContentEntry $content): JsonResponse
    {
        $data = $this->contentData($request, $content);
        if (($data['status'] ?? $content->status) === 'published' && ! $content->published_at) {
            $data['published_at'] = now();
        }
        $content->update($data);
        $this->activity->log($request, 'content.updated', $content);

        return $this->success($content->fresh(), 'Content updated.');
    }

    public function destroyContent(Request $request, ContentEntry $content): JsonResponse
    {
        $this->activity->log($request, 'content.deleted', $content);
        $content->delete();

        return $this->success(null, 'Content deleted.');
    }

    public function customers(Request $request): JsonResponse
    {
        $query = User::query()->with('roles')->when($request->filled('search'), fn (Builder $q) => $q->where(fn (Builder $q) => $q->where('name', 'like', '%'.$request->string('search').'%')->orWhere('email', 'like', '%'.$request->string('search').'%')));

        return $this->success($this->paginate($query->latest(), $request));
    }

    public function showCustomer(User $customer): JsonResponse
    {
        return $this->success($customer->load('roles'));
    }

    public function updateCustomer(Request $request, User $customer): JsonResponse
    {
        $data = $request->validate(['is_active' => ['sometimes', 'boolean'], 'locale' => ['sometimes', 'string', 'max:10'], 'timezone' => ['sometimes', 'string', 'max:100']]);
        $customer->update($data);
        $this->activity->log($request, 'customers.updated', $customer, $data);

        return $this->success($customer->fresh()->load('roles'), 'Customer updated.');
    }

    public function tickets(Request $request): JsonResponse
    {
        $query = SupportTicket::query()->with(['user:id,name,email', 'assignee:id,name'])->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')))->when($request->filled('priority'), fn (Builder $q) => $q->where('priority', $request->string('priority')));

        return $this->success($this->paginate($query->latest(), $request));
    }

    public function showTicket(SupportTicket $ticket): JsonResponse
    {
        return $this->success($ticket->load(['user:id,name,email', 'assignee:id,name', 'messages.user:id,name,email']));
    }

    public function updateTicket(Request $request, SupportTicket $ticket): JsonResponse
    {
        $data = $request->validate(['status' => ['sometimes', Rule::in(['open', 'pending', 'resolved', 'closed'])], 'priority' => ['sometimes', Rule::in(['low', 'normal', 'high', 'urgent'])], 'assigned_to' => ['nullable', 'exists:users,id']]);
        if (in_array($data['status'] ?? $ticket->status, ['resolved', 'closed'], true)) {
            $data['resolved_at'] = now();
        }
        $ticket->update($data);
        $this->activity->log($request, 'support.ticket.updated', $ticket, $data);

        return $this->success($ticket->fresh(), 'Ticket updated.');
    }

    public function replyTicket(Request $request, SupportTicket $ticket): JsonResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:10000'], 'is_internal' => ['sometimes', 'boolean']]);
        $message = $ticket->messages()->create([...$data, 'user_id' => $request->user()->id]);
        $ticket->update(['status' => 'pending']);
        $this->activity->log($request, 'support.message.created', $message);

        return $this->success($message->load('user:id,name,email'), 'Reply added.', 201);
    }

    public function notifications(Request $request): JsonResponse
    {
        return $this->success($this->paginate(AdminNotification::query()->with('user:id,name,email')->latest(), $request));
    }

    public function storeNotification(Request $request): JsonResponse
    {
        $data = $request->validate(['user_id' => ['nullable', 'exists:users,id'], 'type' => ['sometimes', 'string', 'max:100'], 'title' => ['required', 'string', 'max:255'], 'body' => ['nullable', 'string', 'max:2000'], 'data' => ['nullable', 'array']]);
        $notification = AdminNotification::query()->create($data);
        $this->activity->log($request, 'notifications.created', $notification);

        return $this->success($notification, 'Notification created.', 201);
    }

    public function activity(Request $request): JsonResponse
    {
        return $this->success($this->paginate(ActivityLog::query()->with('actor:id,name,email')->when($request->filled('event'), fn (Builder $q) => $q->where('event', $request->string('event')))->latest(), $request));
    }

    private function contentData(Request $request, ?ContentEntry $content = null): array
    {
        return $request->validate(['type' => [$content ? 'sometimes' : 'required', 'string', 'max:100'], 'title' => [$content ? 'sometimes' : 'required', 'string', 'max:255'], 'slug' => [$content ? 'sometimes' : 'required', 'string', 'max:255', Rule::unique('content_entries', 'slug')->ignore($content)], 'body' => ['nullable', 'string'], 'metadata' => ['nullable', 'array'], 'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])]]);
    }

    private function paginate(Builder $query, Request $request): array
    {
        $page = $query->paginate(min(max($request->integer('per_page', 20), 1), 100));

        return ['data' => $page->items(), 'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'per_page' => $page->perPage(), 'total' => $page->total()]];
    }

    /** @return array{from: Carbon, to: Carbon} */
    private function range(Request $request): array
    {
        $validated = $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from']]);

        return ['from' => isset($validated['from']) ? Carbon::parse($validated['from'])->startOfDay() : now()->subDays(29)->startOfDay(), 'to' => isset($validated['to']) ? Carbon::parse($validated['to'])->endOfDay() : now()->endOfDay()];
    }
}
