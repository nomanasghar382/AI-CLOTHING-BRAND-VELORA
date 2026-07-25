<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view', 'reports.view', 'reports.export',
            'settings.view', 'settings.manage', 'content.view', 'content.manage',
            'customers.view', 'customers.manage', 'support.view', 'support.manage',
            'notifications.view', 'notifications.manage', 'activity.view',
            'catalog.manage', 'orders.manage', 'coupons.manage', 'shipping.manage',
            'taxes.manage', 'payments.manage', 'returns.manage',
        ];

        foreach ($permissions as $slug) {
            Permission::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => str($slug)->replace('.', ' ')->headline(), 'description' => "Allows {$slug} actions."]
            );
        }

        $allIds = Permission::query()->pluck('id');
        Role::query()->whereIn('slug', ['super-admin', 'admin'])->each(
            fn (Role $role) => $role->permissions()->syncWithoutDetaching($allIds)
        );

        Role::query()->where('slug', 'supplier')->each(fn (Role $role) => $role->permissions()->syncWithoutDetaching(
            Permission::query()->whereIn('slug', ['catalog.manage', 'orders.manage'])->pluck('id')
        ));
    }
}
