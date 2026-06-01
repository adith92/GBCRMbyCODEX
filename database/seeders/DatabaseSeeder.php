<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            'super-admin',
            'gm',
            'sales',
            'sales-manager',
            'finance',
            'operation',
            'head-pool',
            'pool-staff',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@blueerp.local'],
            ['name' => 'Super Admin', 'password' => 'password']
        );

        $superAdmin->assignRole('super-admin');

        User::factory()->count(5)->create();
    }
}
