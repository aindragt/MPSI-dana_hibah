<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->upsert([
            ['id' => 1, 'name' => 'Pengaju', 'slug' => 'pengaju'],
            ['id' => 2, 'name' => 'Admin Kesra', 'slug' => 'admin-kesra'],
            ['id' => 3, 'name' => 'Super Admin', 'slug' => 'super_admin'],
        ], ['id'], ['name', 'slug']);
    }
}
