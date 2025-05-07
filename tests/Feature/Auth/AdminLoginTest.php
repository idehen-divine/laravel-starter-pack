<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleEnum;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;
    
    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        // Create an admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->admin->setRole(RoleEnum::ADMIN->name); 

        // Create a non-admin user
        $this->user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_admin_can_login_successfully()
    {
        $response = $this->postJson(route('auth.admin.login'), [
            'email' => $this->admin->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user',
                    'role',
                    'permissions',
                    'token',
                ],
            ]);
    }

    public function test_non_admin_user_is_forbidden()
    {
        $response = $this->postJson(route('auth.admin.login'), [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Forbidden']);
    }

    public function test_invalid_credentials_return_401()
    {
        $response = $this->postJson(route('auth.admin.login'), [
            'email' => $this->admin->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }
}
