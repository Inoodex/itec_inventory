<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class _PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create([
            'name' => 'Administration',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Booking',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Service Management',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Sales Management',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Settings',
            'guard_name' => 'web',
        ]);
    }
}
