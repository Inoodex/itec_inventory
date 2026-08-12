<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create ALL permissions that your menu checks for
        $permissions = [
            ['name' => 'Administration', 'guard_name' => 'web'],
            ['name' => 'Booking', 'guard_name' => 'web'],
            ['name' => 'Service Management', 'guard_name' => 'web'],
            ['name' => 'Sales Management', 'guard_name' => 'web'],
            ['name' => 'Settings', 'guard_name' => 'web'],

            // ADD THESE NEW PERMISSIONS:
            ['name' => 'Product Management', 'guard_name' => 'web'],
            ['name' => 'Customer Management', 'guard_name' => 'web'],
            ['name' => 'Vendor Management', 'guard_name' => 'web'],
            ['name' => 'Purchase Management', 'guard_name' => 'web'],
            ['name' => 'Inventory Management', 'guard_name' => 'web'],
            ['name' => 'Expense Management', 'guard_name' => 'web'],
            ['name' => 'Report Management', 'guard_name' => 'web'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name']
            ]);
        }

        // Create Super Admin role
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);

        // Assign ALL permissions to Super Admin
        $superAdminRole->syncPermissions(Permission::all());

        // Assign Super Admin role to your user
        // $user = User::where('email', 'superadmin@example.com')->first();
        // if ($user) {
        //     $user->assignRole('Super Admin');
        // }

        // // Optional: Create other roles with specific permissions
        // $adminRole = Role::firstOrCreate([
        //     'name' => 'Admin',
        //     'guard_name' => 'web'
        // ]);

        // $adminRole->syncPermissions([
        //     'Product Management',
        //     'Sales Management',
        //     'Customer Management',
        //     'Vendor Management',
        //     'Purchase Management',
        //     'Inventory Management',
        //     'Expense Management',
        //     'Report Management'
        // ]);

        $firstUser = User::first();

        if ($firstUser && !$firstUser->hasRole('Super Admin')) {
            $firstUser->assignRole($superAdminRole);
        }
    }
}
