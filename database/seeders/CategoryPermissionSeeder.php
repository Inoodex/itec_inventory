<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CategoryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Category Management permission
        $categoryPermission = Permission::firstOrCreate(['name' => 'Category Management']);

        // Assign to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($categoryPermission);
        }

        $this->command->info('Category Management permission created and assigned to Super Admin.');
    }
}
