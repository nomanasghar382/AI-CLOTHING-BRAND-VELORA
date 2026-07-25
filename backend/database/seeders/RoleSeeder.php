<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Full platform access.'],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Operational platform access.'],
            ['name' => 'Customer', 'slug' => 'customer', 'description' => 'Marketplace customer account.'],
            ['name' => 'Creator', 'slug' => 'creator', 'description' => 'Creator commerce account.'],
            ['name' => 'Supplier', 'slug' => 'supplier', 'description' => 'Supplier account.'],
        ])->each(fn (array $role) => Role::query()->updateOrCreate(['slug' => $role['slug']], $role));
    }
}
