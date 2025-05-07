<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\OtpCode;
use App\Enums\OTPTypeEnum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test sending password reset otp.
     *
     * @return void
     */
    public function test_user_can_request_password_reset_otp()
    {
        $user = User::factory()->create(['email' => 'testuser@example.com']);

        $response = $this->postJson(route('auth.forgot-password'), [
            'email' => $user->email
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password reset OTP sent.']);
        $this->assertDatabaseHas(OtpCode::class, [
            'email' => 'testuser@example.com',
            'type' => OTPTypeEnum::RESET_PASSWORD_OTP->name,
            'code' => '111111'
        ]);
    }

    /**
     * Test verifying password reset otp.
     *
     * @return void
     */
    public function test_user_can_verify_password_reset_otp()
    {
        $user = User::factory()->create(['email' => 'testuser@example.com']);
        $otpCode = OtpCode::factory()->create([
            'email' => $user->email,
            'type' => OTPTypeEnum::RESET_PASSWORD_OTP->name,
            'code' => '111111'
        ]);
        $response = $this->postJson(route('auth.verify-otp'), [
            'email' => $user->email,
            'otp_code' => '111111',
            'type' => OTPTypeEnum::RESET_PASSWORD_OTP->name
        ]);
        $response->assertStatus(200)
            ->assertJson(['message' => 'OTP Code verified successfully.']);
        $this->assertDatabaseHas(OtpCode::class, [
            'email' => $user->email,
            'type' => OTPTypeEnum::RESET_PASSWORD_OTP->name,
            'code' => '0',
            'is_verified' => true,
        ]);
    }

    /**
     * Test resetting password.
     *
     * @return void
     */
    public function test_user_can_reset_password()
    {
        $user = User::factory()->create(['email' => 'testuser@example.com']);
        $otpCode = OtpCode::factory()->create([
            'email' => $user->email,
            'type' => OTPTypeEnum::RESET_PASSWORD_OTP->name,
            'code' => '0',
            'is_verified' => true,
        ]);

        $response = $this->postJson(route('auth.reset-password'), [
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);
        $response->assertJson(['message' => 'Password reset successfully.']);
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
        $this->assertDatabaseMissing(OtpCode::class, [
            'email' => $user->email,
            'type' => OTPTypeEnum::RESET_PASSWORD_OTP->name,
            'code' => '0',
            'is_verified' => true,
        ]);
    }
}
