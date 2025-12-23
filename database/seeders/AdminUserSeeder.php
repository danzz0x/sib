<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! User::where('email', 'basiliojosedaniel@gmail.com')->exists()) {
            User::create([
                'name' => 'Admin Daniel',
                'email' => 'basiliojosedaniel@gmail.com',
                'password' => Hash::make('adminSibDan#2026'),
                'role' => User::ROLE_ADMIN, // 'administrador'
            ]);
        }
    }
}
