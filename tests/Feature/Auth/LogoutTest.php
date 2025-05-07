<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $login = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $token = $login->json('data.token');
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson(route('auth.logout'));

        $response->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'message' => 'Logout Successfull',
            ]);
    }
}
