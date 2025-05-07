<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\OtpCode;
use App\Enums\OTPTypeEnum;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TEST USER CAN REGISTER WITH VALID CREDENTIALS
     */
    public function test_user_can_register_with_valid_credentials(): void
    {

        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);
        
        // Attempt to register a user with valid credentials
        $response = $this->postJson(route('auth.register'), [
            'user_name' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_name' => 'Smith',
            'phone_no' => '1234567890',
        ]);

        $response->assertJson([
            'code' => 201,
            'message' => 'OTP verification sent to email.',
            'data' => [
                'email' => 'testuser@example.com'
            ]
        ]);
        $this->assertDatabaseHas(User::class, [
            'email' => 'testuser@example.com'
        ]);
        $this->assertTrue(User::where('email', 'testuser@example.com')->first()->hasRole(RoleEnum::USER->name));
        $this->assertDatabaseHas(OtpCode::class, [
            'email' => 'testuser@example.com',
            'type' => OTPTypeEnum::VERIFY_EMAIL_OTP->name,
            'code' => '111111'
        ]);
    }

    /**
     * TEST USER CANNOT REGISTER WITH INVALID CREDENTIALS
     */
    public function test_user_cannot_register_with_invalid_credentials(): void
    {
        // Attempt to register a user with invalid credentials
        $response = $this->postJson(route('auth.register'), []);

        // Assertions
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'user_name',
                    'first_name',
                    'last_name',
                    'email',
                    'password',
                ],
            ]);
    }

    /**
     * TEST USER CAN REGISTER WITH SAME EMAIL CREDENTIALS TWICE
     */
    public function test_user_can_register_with_same_email_credentials_twice(): void
    {
        // Register a user with valid credentials
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt to register a user with the same email
        $response = $this->postJson(route('auth.register'), [
            'user_name' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_name' => 'Smith',
            'phone_no' => '1234567890',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email',
                ],
            ]);
    }
}
