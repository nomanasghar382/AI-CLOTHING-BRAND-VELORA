<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Velora Super Admin', 'email' => 'admin@velora.test', 'role' => 'super-admin'],
            ['name' => 'Demo Customer', 'email' => 'customer@velora.test', 'role' => 'customer'],
            ['name' => 'Demo Creator', 'email' => 'creator@velora.test', 'role' => 'creator'],
            ['name' => 'Demo Supplier', 'email' => 'supplier@velora.test', 'role' => 'supplier'],
        ])->each(function (array $account): void {
            $user = User::query()->updateOrCreate(
                ['email' => $account['email']],
                ['name' => $account['name'], 'password' => 'VeloraDemo!2026', 'email_verified_at' => now()]
            );

            $user->roles()->syncWithoutDetaching([
                Role::query()->where('slug', $account['role'])->valueOrFail('id'),
            ]);
        });
    }
}
