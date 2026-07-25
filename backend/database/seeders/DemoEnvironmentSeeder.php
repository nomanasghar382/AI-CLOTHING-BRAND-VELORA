<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\AdminNotification;
use App\Models\ApplicationMetric;
use App\Models\BillingAddress;
use App\Models\CommunityComment;
use App\Models\CommunityPost;
use App\Models\CreatorCollection;
use App\Models\CreatorProfile;
use App\Models\Lookboard;
use App\Models\LookboardItem;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\ShippingMethod;
use App\Models\SupplierAddress;
use App\Models\SupplierInventory;
use App\Models\SupplierProfile;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\VisualSearch;
use App\Models\WardrobeItem;
use App\Notifications\OrderStatusNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class DemoEnvironmentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@velora.test')->firstOrFail();
        $customer = User::query()->where('email', 'customer@velora.test')->firstOrFail();
        $creator = User::query()->where('email', 'creator@velora.test')->firstOrFail();
        $supplier = User::query()->where('email', 'supplier@velora.test')->firstOrFail();
        $products = Product::query()
            ->with(['category:id,name', 'colors:id,name', 'images:id,product_id,url,thumbnail_url,is_primary,sort_order'])
            ->where('status', 'published')
            ->orderBy('id')
            ->limit(40)
            ->get();

        $this->seedProfiles($creator, $supplier);
        $this->seedShipping($customer);
        $this->seedOrders($customer, $products);
        $this->seedCreatorContent($creator, $customer, $products);
        $this->seedCommunity($creator, $customer);
        $this->seedWardrobeAndVisualSearch($customer, $products);
        $this->seedLoyalty($customer);
        $this->seedSupport($customer, $admin);
        $this->seedSupplierInventory($supplier, $products);
        $this->seedAnalyticsAndForecasts();
        $this->seedNotifications($customer);
        $this->seedActivity($admin);
        $this->seedAdminNotifications($admin);
    }

    private function seedProfiles(User $creator, User $supplier): void
    {
        CreatorProfile::query()->updateOrCreate(
            ['user_id' => $creator->id],
            [
                'handle' => 'velora-creator',
                'display_name' => 'Velora Creator',
                'bio' => 'Curating modest fashion edits and seasonal lookboards.',
                'avatar_url' => 'https://images.unsplash.com/photo-1561442748-c50715dc32f6?auto=format&fit=crop&w=400&q=80',
                'social_links' => ['instagram' => '@veloracreator', 'tiktok' => '@veloracreator'],
                'is_accepting_commissions' => true,
                'commission_rate' => 12.5,
            ]
        );

        SupplierProfile::query()->updateOrCreate(
            ['user_id' => $supplier->id],
            [
                'company_name' => 'Velora Supply Co.',
                'slug' => 'velora-supply',
                'status' => 'approved',
                'tax_id' => 'VEL-SUP-001',
                'contact_email' => 'supplier@velora.test',
                'contact_phone' => '+1-555-0100',
                'metadata' => ['fulfillment_regions' => ['US', 'GB', 'AE']],
            ]
        );

        $profile = SupplierProfile::query()->where('user_id', $supplier->id)->first();
        SupplierAddress::query()->updateOrCreate(
            ['supplier_profile_id' => $profile->id, 'type' => 'warehouse'],
            [
                'line1' => '1200 Commerce Way',
                'city' => 'Dallas',
                'region' => 'TX',
                'postal_code' => '75201',
                'country' => 'US',
            ]
        );
    }

    private function seedShipping(User $customer): void
    {
        ShippingMethod::query()->updateOrCreate(
            ['code' => 'standard'],
            ['name' => 'Standard Shipping', 'description' => 'Delivered in 5–7 business days.', 'is_active' => true]
        );

        ShippingAddress::query()->updateOrCreate(
            ['user_id' => $customer->id, 'line1' => '742 Evergreen Terrace'],
            [
                'first_name' => 'Demo',
                'last_name' => 'Customer',
                'phone' => '+1-555-0199',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62704',
                'country' => 'US',
                'is_default' => true,
            ]
        );

        BillingAddress::query()->updateOrCreate(
            ['user_id' => $customer->id, 'line1' => '742 Evergreen Terrace'],
            [
                'first_name' => 'Demo',
                'last_name' => 'Customer',
                'phone' => '+1-555-0199',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62704',
                'country' => 'US',
                'is_default' => true,
            ]
        );
    }

    private function seedOrders(User $customer, $products): void
    {
        $shippingMethod = ShippingMethod::query()->where('code', 'standard')->first();
        $address = ShippingAddress::query()->where('user_id', $customer->id)->first();
        $billing = BillingAddress::query()->where('user_id', $customer->id)->first();
        $statuses = [
            ['status' => 'delivered', 'payment_status' => 'paid', 'days' => 28],
            ['status' => 'delivered', 'payment_status' => 'paid', 'days' => 21],
            ['status' => 'shipped', 'payment_status' => 'paid', 'days' => 14],
            ['status' => 'shipped', 'payment_status' => 'paid', 'days' => 10],
            ['status' => 'processing', 'payment_status' => 'paid', 'days' => 7],
            ['status' => 'processing', 'payment_status' => 'paid', 'days' => 5],
            ['status' => 'pending', 'payment_status' => 'paid', 'days' => 3],
            ['status' => 'pending', 'payment_status' => 'pending', 'days' => 1],
        ];

        foreach ($statuses as $index => $config) {
            $items = $products->slice($index * 2, 2);
            $subtotal = $items->sum('price');
            $shipping = 9.99;
            $tax = round($subtotal * 0.08, 2);
            $grand = $subtotal + $shipping + $tax;
            $created = now()->subDays($config['days']);

            $order = Order::query()->updateOrCreate(
                ['number' => 'VEL-DEMO-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $customer->id,
                    'shipping_address_id' => $address?->id,
                    'billing_address_id' => $billing?->id,
                    'shipping_method_id' => $shippingMethod?->id,
                    'status' => $config['status'],
                    'payment_status' => $config['payment_status'],
                    'currency' => 'USD',
                    'subtotal' => $subtotal,
                    'discount_total' => 0,
                    'shipping_total' => $shipping,
                    'tax_total' => $tax,
                    'grand_total' => $grand,
                    'created_at' => $created,
                    'updated_at' => $created,
                ]
            );

            foreach ($items as $product) {
                OrderItem::query()->updateOrCreate(
                    ['order_id' => $order->id, 'sku' => $product->sku],
                    [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => 1,
                        'unit_price' => $product->price,
                        'line_total' => $product->price,
                    ]
                );
            }

            OrderStatusHistory::query()->updateOrCreate(
                ['order_id' => $order->id, 'to_status' => $config['status']],
                ['from_status' => 'pending', 'note' => 'Demo order seeded for release candidate.', 'created_at' => $created]
            );

            if ($config['payment_status'] === 'paid') {
                Payment::query()->updateOrCreate(
                    ['order_id' => $order->id, 'provider' => 'stripe'],
                    [
                        'status' => 'paid',
                        'amount' => $grand,
                        'currency' => 'USD',
                        'provider_reference' => 'pi_demo_'.Str::lower(Str::random(12)),
                        'payload' => ['demo' => true],
                        'created_at' => $created,
                    ]
                );
            }
        }
    }

    private function seedCreatorContent(User $creator, User $customer, $products): void
    {
        $profile = CreatorProfile::query()->where('user_id', $creator->id)->firstOrFail();
        $collection = CreatorCollection::query()->updateOrCreate(
            ['creator_profile_id' => $profile->id, 'slug' => 'spring-edit'],
            ['title' => 'Spring Edit', 'description' => 'Layered neutrals for transitional weather.', 'is_public' => true]
        );
        $collection->products()->sync($products->take(6)->pluck('id'));
        $profile->followers()->syncWithoutDetaching([$customer->id]);

        $lookboard = Lookboard::query()->updateOrCreate(
            ['user_id' => $creator->id, 'title' => 'Weekend Layers'],
            [
                'description' => 'Relaxed weekend styling with elevated basics.',
                'cover_url' => 'https://images.unsplash.com/photo-1770964211782-013475eacc3f?auto=format&fit=crop&w=900&q=80',
                'is_public' => true,
            ]
        );

        foreach ($products->take(4) as $sort => $product) {
            LookboardItem::query()->updateOrCreate(
                ['lookboard_id' => $lookboard->id, 'product_id' => $product->id],
                ['sort_order' => $sort, 'position' => ['x' => $sort * 20, 'y' => $sort * 10]]
            );
        }
    }

    private function seedCommunity(User $creator, User $customer): void
    {
        $posts = [
            ['user_id' => $creator->id, 'body' => 'Soft neutrals and layered textures for a calm weekend edit.'],
            ['user_id' => $customer->id, 'body' => 'Just received my VELORA order — the drape on this abaya is perfect.'],
            ['user_id' => $creator->id, 'body' => 'Three ways to style the same wide-leg pant across seasons.'],
        ];

        $mediaUrls = [
            'https://images.unsplash.com/photo-1770964211782-013475eacc3f?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1750190321796-c749877df841?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1630735988694-12186aedd73d?auto=format&fit=crop&w=800&q=80',
        ];

        foreach ($posts as $index => $post) {
            $record = CommunityPost::query()->updateOrCreate(
                ['user_id' => $post['user_id'], 'body' => $post['body']],
                [
                    'media' => [['url' => $mediaUrls[$index], 'type' => 'image']],
                    'status' => 'published',
                    'likes_count' => 12 + $index * 4,
                    'comments_count' => 2 + $index,
                ]
            );

            CommunityComment::query()->updateOrCreate(
                ['community_post_id' => $record->id, 'user_id' => $customer->id, 'body' => 'Beautiful composition — saving this look.'],
                ['status' => 'published']
            );
        }
    }

    private function seedWardrobeAndVisualSearch(User $customer, $products): void
    {
        foreach ($products->take(5) as $product) {
            WardrobeItem::query()->updateOrCreate(
                ['user_id' => $customer->id, 'product_id' => $product->id],
                [
                    'name' => $product->name,
                    'category' => $product->category?->name,
                    'color' => $product->colors->first()?->name,
                    'image_url' => $product->images->first()?->url,
                ]
            );
        }

        VisualSearch::query()->updateOrCreate(
            ['user_id' => $customer->id, 'image_hash' => hash('sha256', 'velora-demo-visual-search')],
            [
                'image_url' => 'https://images.unsplash.com/photo-1585728748176-455ac5eed962?auto=format&fit=crop&w=600&q=80',
                'provider' => 'catalog_fallback',
                'results' => $products->take(5)->map(fn (Product $product) => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'score' => round(0.95 - ($product->id % 5) * 0.05, 2),
                ])->values()->all(),
            ]
        );
    }

    private function seedLoyalty(User $customer): void
    {
        $wallet = $customer->loyaltyWallet()->firstOrCreate([]);
        LoyaltyTransaction::query()->updateOrCreate(
            ['loyalty_wallet_id' => $wallet->id, 'idempotency_key' => 'demo-welcome-bonus'],
            [
                'type' => 'earn',
                'points_delta' => 500,
                'credit_delta' => 0,
                'reference_type' => 'demo_seed',
                'metadata' => ['reason' => 'Welcome to VELORA Circle'],
            ]
        );
        LoyaltyTransaction::query()->updateOrCreate(
            ['loyalty_wallet_id' => $wallet->id, 'idempotency_key' => 'demo-order-reward'],
            [
                'type' => 'earn',
                'points_delta' => 240,
                'credit_delta' => 0,
                'reference_type' => Order::class,
                'reference_id' => Order::query()->where('number', 'VEL-DEMO-0001')->value('id'),
                'metadata' => ['reason' => 'Order purchase reward'],
            ]
        );
        $wallet->update(['points_balance' => 740, 'store_credit_balance' => 25]);
    }

    private function seedSupport(User $customer, User $admin): void
    {
        $ticket = SupportTicket::query()->updateOrCreate(
            ['number' => 'SUP-DEMO-001'],
            [
                'user_id' => $customer->id,
                'assigned_to' => $admin->id,
                'subject' => 'Sizing guidance for layered abaya',
                'status' => 'open',
                'priority' => 'normal',
            ]
        );

        SupportMessage::query()->updateOrCreate(
            ['support_ticket_id' => $ticket->id, 'body' => 'Could you advise on sizing if I plan to layer underneath?'],
            ['user_id' => $customer->id, 'is_internal' => false]
        );

        SupportMessage::query()->updateOrCreate(
            ['support_ticket_id' => $ticket->id, 'body' => 'We recommend sizing up one size for comfortable layering.'],
            ['user_id' => $admin->id, 'is_internal' => false]
        );

        $resolved = SupportTicket::query()->updateOrCreate(
            ['number' => 'SUP-DEMO-002'],
            [
                'user_id' => $customer->id,
                'assigned_to' => $admin->id,
                'subject' => 'Delivery timing for international order',
                'status' => 'resolved',
                'priority' => 'low',
                'resolved_at' => now()->subDays(2),
            ]
        );

        SupportMessage::query()->updateOrCreate(
            ['support_ticket_id' => $resolved->id, 'body' => 'Your parcel cleared customs and is on schedule.'],
            ['user_id' => $admin->id, 'is_internal' => false]
        );
    }

    private function seedSupplierInventory(User $supplier, $products): void
    {
        $profile = SupplierProfile::query()->where('user_id', $supplier->id)->firstOrFail();
        foreach ($products->take(15) as $product) {
            SupplierInventory::query()->updateOrCreate(
                ['supplier_profile_id' => $profile->id, 'product_id' => $product->id],
                [
                    'sku' => $product->sku,
                    'on_hand' => 50 + ($product->id % 20),
                    'reserved' => $product->id % 5,
                    'available' => 45 + ($product->id % 20),
                    'synced_at' => now()->subHours($product->id % 12),
                ]
            );
        }
    }

    private function seedAnalyticsAndForecasts(): void
    {
        $productId = Product::query()->value('id');
        $events = [
            ['type' => 'search', 'query' => 'abaya', 'created_at' => now()->subDays(2)],
            ['type' => 'search', 'query' => 'modest dress', 'created_at' => now()->subDay()],
            ['type' => 'recommendation_click', 'product_id' => $productId, 'created_at' => now()->subHours(6)],
            ['type' => 'page_view', 'properties' => ['page' => '/catalog'], 'created_at' => now()->subHours(3)],
        ];

        foreach ($events as $event) {
            DB::table('bi_analytics_events')->updateOrInsert(
                ['type' => $event['type'], 'query' => $event['query'] ?? null],
                [
                    'product_id' => $event['product_id'] ?? null,
                    'properties' => isset($event['properties']) ? json_encode($event['properties']) : null,
                    'created_at' => $event['created_at'],
                    'updated_at' => now(),
                ]
            );
        }

        foreach (range(1, 14) as $day) {
            DB::table('bi_analytics_events')->updateOrInsert(
                ['type' => 'search', 'query' => 'demo-day-'.$day, 'created_at' => now()->subDays($day)->startOfDay()],
                ['updated_at' => now()]
            );
        }

        DB::table('bi_forecasts')->updateOrInsert(
            ['type' => 'sales', 'product_id' => null, 'forecast_date' => now()->addDay()->toDateString()],
            ['value' => 1250.50, 'context' => json_encode(['method' => 'historical_daily_average']), 'updated_at' => now(), 'created_at' => now()]
        );

        foreach (['requests', 'orders', 'revenue'] as $index => $metric) {
            ApplicationMetric::query()->updateOrCreate(
                ['metric' => $metric, 'group' => 'demo', 'recorded_at' => now()->subDays($index)->startOfDay()],
                ['value' => [1200, 48, 3840.25][$index], 'tags' => ['environment' => 'demo']]
            );
        }
    }

    private function seedNotifications(User $customer): void
    {
        $order = Order::query()->where('number', 'VEL-DEMO-0001')->first();
        if ($order) {
            $customer->notify(new OrderStatusNotification($order, 'order'));
            $customer->notify(new OrderStatusNotification($order, 'shipped'));
        }
    }

    private function seedActivity(User $admin): void
    {
        $events = [
            ['event' => 'demo.orders.seeded', 'properties' => ['count' => 4]],
            ['event' => 'demo.community.seeded', 'properties' => ['posts' => 3]],
            ['event' => 'demo.analytics.seeded', 'properties' => ['events' => 4]],
        ];

        foreach ($events as $entry) {
            ActivityLog::query()->updateOrCreate(
                ['actor_id' => $admin->id, 'event' => $entry['event']],
                [
                    'properties' => $entry['properties'],
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'VELORA Demo Seeder',
                ]
            );
        }
    }

    private function seedAdminNotifications(User $admin): void
    {
        AdminNotification::query()->updateOrCreate(
            ['title' => 'Demo environment ready'],
            [
                'user_id' => $admin->id,
                'type' => 'system',
                'body' => 'Release candidate demo data has been populated across commerce, creator, community, loyalty, and analytics modules.',
                'data' => ['version' => '1.0.0-rc.1'],
            ]
        );
    }
}
