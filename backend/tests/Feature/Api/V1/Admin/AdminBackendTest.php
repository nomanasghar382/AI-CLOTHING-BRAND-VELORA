<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\Order;
use App\Models\Role;
use App\Models\SupportTicket;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_permissioned_admin_can_manage_admin_domains_and_export_reports(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = $this->userWithRole('admin');
        $customer = $this->userWithRole('customer');
        $order = Order::query()->create([
            'number' => 'VEL-1001', 'user_id' => $customer->id, 'status' => 'pending',
            'payment_status' => 'paid', 'currency' => 'USD', 'subtotal' => 100,
            'discount_total' => 0, 'shipping_total' => 0, 'tax_total' => 0, 'grand_total' => 100,
        ]);
        $ticket = SupportTicket::query()->create(['number' => 'SUP-1001', 'user_id' => $customer->id, 'subject' => 'Delivery question']);
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/dashboard')->assertOk()
            ->assertJsonPath('data.totals.orders', 1)
            ->assertJsonPath('data.totals.revenue', 100);
        $this->getJson('/api/v1/admin/analytics')->assertOk()->assertJsonPath('data.summary.orders', 1);

        $setting = $this->postJson('/api/v1/admin/settings', ['group' => 'store', 'key' => 'store.theme', 'value' => 'midnight', 'is_public' => true])
            ->assertCreated()->json('data.id');
        $this->patchJson("/api/v1/admin/settings/{$setting}", ['value' => 'sand'])->assertOk()->assertJsonPath('data.value', 'sand');

        $content = $this->postJson('/api/v1/admin/content', ['type' => 'page', 'title' => 'Returns', 'slug' => 'returns', 'body' => 'Policy', 'status' => 'published'])
            ->assertCreated()->json('data.id');
        $this->putJson("/api/v1/admin/content/{$content}", ['title' => 'Returns policy'])->assertOk()->assertJsonPath('data.title', 'Returns policy');

        $this->getJson('/api/v1/admin/customers?search='.$customer->email)->assertOk()->assertJsonPath('data.data.0.id', $customer->id);
        $this->patchJson("/api/v1/admin/customers/{$customer->id}", ['is_active' => false])->assertOk()->assertJsonPath('data.is_active', false);

        $this->postJson("/api/v1/admin/support/tickets/{$ticket->id}/messages", ['body' => 'We are checking this for you.'])->assertCreated()
            ->assertJsonPath('data.body', 'We are checking this for you.');
        $this->patchJson("/api/v1/admin/support/tickets/{$ticket->id}", ['status' => 'resolved', 'priority' => 'high'])->assertOk()
            ->assertJsonPath('data.status', 'resolved');

        $this->postJson('/api/v1/admin/notifications', ['title' => 'Maintenance', 'body' => 'Tonight'])->assertCreated();
        $this->getJson('/api/v1/admin/activity-logs')->assertOk()->assertJsonPath('data.meta.total', 8);
        $report = $this->get('/api/v1/admin/reports/orders/csv');
        $report->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('VEL-1001', $report->streamedContent());

        $this->assertDatabaseHas('report_exports', ['report_type' => 'orders', 'requested_by' => $admin->id, 'row_count' => 1]);
    }

    public function test_customer_is_denied_admin_domain_access(): void
    {
        $this->seed(RoleSeeder::class);
        Sanctum::actingAs($this->userWithRole('customer'));

        $this->getJson('/api/v1/admin/dashboard')->assertForbidden()->assertJsonPath('success', false);
        $this->postJson('/api/v1/admin/content', ['type' => 'page', 'title' => 'Nope', 'slug' => 'nope'])->assertForbidden();
    }

    private function userWithRole(string $slug): User
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::query()->where('slug', $slug)->sole());

        return $user;
    }
}
