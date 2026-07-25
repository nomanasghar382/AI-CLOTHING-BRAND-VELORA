<?php

namespace Database\Seeders;

use App\Models\AdminNotification;
use App\Models\ContentEntry;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminDomainSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()->where('email', 'admin@velora.test')->value('id');

        foreach ([
            ['group' => 'store', 'key' => 'store.name', 'value' => 'VELORA', 'is_public' => true],
            ['group' => 'store', 'key' => 'store.currency', 'value' => 'USD', 'is_public' => true],
            ['group' => 'support', 'key' => 'support.response_hours', 'value' => 24, 'is_public' => false],
        ] as $setting) {
            Setting::query()->updateOrCreate(['key' => $setting['key']], [...$setting, 'updated_by' => $adminId]);
        }

        ContentEntry::query()->updateOrCreate(
            ['slug' => 'about-velora'],
            ['type' => 'page', 'title' => 'About VELORA', 'body' => 'Thoughtful fashion for every day.', 'status' => 'published', 'published_at' => now(), 'author_id' => $adminId]
        );

        AdminNotification::query()->updateOrCreate(
            ['title' => 'Admin backend ready'],
            ['type' => 'system', 'body' => 'Dashboard, reporting, support, and content tools are available.', 'user_id' => $adminId]
        );
    }
}
