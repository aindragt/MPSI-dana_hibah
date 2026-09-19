<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ehibah-kesra.test'],
            [
                'name' => 'Super Admin',
                'role_id' => 3,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
    }
}
