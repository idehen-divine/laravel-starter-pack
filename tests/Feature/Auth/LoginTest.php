<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Enums\RoleEnum;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * USER CAN LOGIN WITH RIGHT CREDENTIALS
     */
    public function test_user_can_login_with_right_cred(): void
    {
        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);
        
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $user->setRole(RoleEnum::USER->name);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'user_name',
                        'email',
                        'first_name',
                        'last_name',
                        'other_name',
                        'phone_no',
                        'avatar',
                        'status',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                    'permissions'
                ]
            ])
            ->assertJson([
                'code' => 200,
                'message' => 'Login Success',
            ]);
            
        $this->assertIsString($response->json('data.token'));
        $this->assertNotEmpty($response->json('data.token'));
    }

    /**
     * USER CANNOT LOGIN WITH WRONG CREDENTIALS
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt to login with wrong credentials
        $response = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // Ensure response is correct
        $response->assertStatus(401)
            ->assertJson([
                'code' => 401,
                'message' => 'Invalid credentials',
            ]);
    }

    /**
     * USER CANNOT LOGIN WITH NON-EXISTENT EMAIL
     */
    public function test_user_cannot_login_with_non_existent_email(): void
    {
        // Attempt to login with a non-existent email
        $response = $this->postJson(route('auth.login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        // Ensure response is correct
        $response->assertStatus(401)
            ->assertJson([
                'code' => 401,
                'message' => 'Invalid credentials',
            ]);
    }

    /**
     * USER CANNOT LOGIN WITH MISSING CREDENTIALS
     */
    public function test_user_cannot_login_with_missing_credentials()
    {
        // Attempt to login with missing credentials
        $response = $this->postJson(route('auth.login'), []);

        // Ensure response is correct
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email',
                    'password',
                ],
            ]);
    }
}
