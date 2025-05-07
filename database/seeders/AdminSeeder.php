<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'user_name' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'development@damodi.io',
            'password'  => bcrypt('password'),
        ]);
        $user->setRole(RoleEnum::ADMIN->name);
    }
}
