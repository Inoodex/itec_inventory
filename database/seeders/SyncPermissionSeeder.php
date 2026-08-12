<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SyncPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role_has_permissions')->insert([
            'role_id' => '1',
            'permission_id' => '1',
        ]);

        DB::table('role_has_permissions')->insert([
            'role_id' => '1',
            'permission_id' => '2',
        ]);

        DB::table('role_has_permissions')->insert([
            'role_id' => '1',
            'permission_id' => '3',
        ]);

        DB::table('role_has_permissions')->insert([
            'role_id' => '1',
            'permission_id' => '4',
        ]);
        
        DB::table('role_has_permissions')->insert([
            'role_id' => '1',
            'permission_id' => '5',
        ]);

    }
}
