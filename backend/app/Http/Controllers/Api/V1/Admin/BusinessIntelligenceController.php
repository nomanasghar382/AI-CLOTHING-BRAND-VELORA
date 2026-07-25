<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class BusinessIntelligenceController extends Controller
{
    use RespondsWithApi;

    public function dashboard(): JsonResponse
    {
        $today = now()->toDateString();
        $metrics = ['revenue' => (float) Order::query()->where('payment_status', 'paid')->sum('grand_total'), 'orders' => Order::query()->count(), 'inventory_units' => (int) DB::table('supplier_inventory')->sum('available'), 'suppliers' => DB::table('supplier_profiles')->where('status', 'approved')->count()];
        DB::table('bi_dashboard_snapshots')->updateOrInsert(['dashboard' => 'operations', 'snapshot_date' => $today], ['metrics' => json_encode($metrics), 'updated_at' => now(), 'created_at' => now()]);

        return $this->success(['snapshot_date' => $today, 'metrics' => $metrics]);
    }

    public function analytics(Request $request, string $type): JsonResponse
    {
        abort_unless(in_array($type, ['customers', 'recommendations', 'search', 'trends', 'sales', 'inventory'], true), 404);
        $from = now()->subDays(min(365, max(1, $request->integer('days', 30))))->startOfDay();
        $data = match ($type) {
            'customers' => DB::table('users')->leftJoin('orders', 'orders.user_id', '=', 'users.id')->selectRaw('users.id, users.email, COUNT(orders.id) as orders, COALESCE(SUM(CASE WHEN orders.payment_status = "paid" THEN orders.grand_total ELSE 0 END), 0) as lifetime_value')->groupBy('users.id', 'users.email')->orderByDesc('lifetime_value')->limit(100)->get(),
            'recommendations' => DB::table('bi_analytics_events')->where('type', 'recommendation_click')->where('created_at', '>=', $from)->selectRaw('product_id, COUNT(*) as clicks')->groupBy('product_id')->orderByDesc('clicks')->get(),
            'search' => DB::table('bi_analytics_events')->where('type', 'search')->where('created_at', '>=', $from)->whereNotNull('query')->selectRaw('query, COUNT(*) as searches')->groupBy('query')->orderByDesc('searches')->limit(100)->get(),
            'trends' => DB::table('bi_analytics_events')->where('created_at', '>=', $from)->selectRaw('DATE(created_at) as date, type, COUNT(*) as total')->groupBy('date', 'type')->orderBy('date')->get(),
            'sales' => Order::query()->where('created_at', '>=', $from)->selectRaw('DATE(created_at) as date, COUNT(*) as orders, COALESCE(SUM(CASE WHEN payment_status = "paid" THEN grand_total ELSE 0 END),0) as revenue')->groupBy('date')->orderBy('date')->get(),
            'inventory' => DB::table('supplier_inventory')->selectRaw('product_id, SUM(on_hand) as on_hand, SUM(reserved) as reserved, SUM(available) as available')->groupBy('product_id')->get(),
        };

        return $this->success(['type' => $type, 'from' => $from->toDateString(), 'data' => $data]);
    }

    public function forecast(Request $request, string $type): JsonResponse
    {
        abort_unless(in_array($type, ['sales', 'inventory'], true), 404);
        $days = min(90, max(1, $request->integer('days', 30)));
        $history = $type === 'sales'
            ? Order::query()->where('payment_status', 'paid')->where('created_at', '>=', now()->subDays($days))->selectRaw('COALESCE(SUM(grand_total),0) as total')->value('total')
            : DB::table('supplier_inventory')->sum('available');
        $value = round((float) $history / $days, 2);
        $date = now()->addDay()->toDateString();
        DB::table('bi_forecasts')->updateOrInsert(['type' => $type, 'product_id' => null, 'forecast_date' => $date], ['value' => $value, 'context' => json_encode(['method' => 'historical_daily_average', 'days' => $days]), 'updated_at' => now(), 'created_at' => now()]);

        return $this->success(['type' => $type, 'forecast_date' => $date, 'value' => $value, 'method' => 'historical_daily_average']);
    }

    public function event(Request $request): JsonResponse
    {
        $data = $request->validate(['type' => ['required', 'string', 'max:100'], 'product_id' => ['nullable', 'exists:products,id'], 'query' => ['nullable', 'string', 'max:500'], 'properties' => ['nullable', 'array']]);
        $data += ['user_id' => $request->user()->id, 'created_at' => now(), 'updated_at' => now()];
        DB::table('bi_analytics_events')->insert($data);

        return $this->success(null, 'Analytics event recorded.', 201);
    }
}
