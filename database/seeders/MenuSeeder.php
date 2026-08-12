<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('menus')->insert([
            'name' => 'Subject',
            'created_by' => 1,
        ]);
        DB::table('menus')->insert([
            'name' => 'Writer',
            'created_by' => 1,
        ]);
        DB::table('menus')->insert([
            'name' => 'Publisher',
            'created_by' => 1,
        ]);
        DB::table('menus')->insert([
            'name' => 'Package',
            'created_by' => 1,
        ]);
        DB::table('menus')->insert([
            'name' => 'Brand',
            'created_by' => 1,
        ]);

    }
}
